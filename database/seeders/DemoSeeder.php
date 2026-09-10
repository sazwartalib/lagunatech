<?php

namespace Database\Seeders;

use App\Actions\Invoices\SyncInvoiceState;
use App\Enums\MilestoneStatus;
use App\Enums\PaymentMethod;
use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\QuotationStatus;
use App\Enums\Role as RoleEnum;
use App\Enums\TaskStatus;
use App\Models\Bug;
use App\Models\ChangeRequest;
use App\Models\Communication;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\CustomerUser;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\MaintenancePlan;
use App\Models\Meeting;
use App\Models\MeetingActionItem;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Sequence;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\Task;
use App\Models\User;
use App\Notifications\InvoiceOverdue;
use App\Notifications\PaymentReceived;
use App\Notifications\ProjectDeadlineApproaching;
use App\Notifications\QuotationDecision;
use App\Support\ReferenceGenerator;
use App\Support\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Builds a realistic snapshot of Laguna Tech so every screen has meaningful
 * data on first run. Model events stay enabled so the activity log fills in.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $staff = $this->createStaff();
        $pms = $staff['project_manager'];
        $builders = array_merge($staff['developer'], $staff['designer']);

        $customers = $this->createCustomers($staff['project_manager']);

        $milestoneNames = ['Requirement', 'UI/UX', 'Development', 'Testing', 'Deployment'];

        foreach ($customers as $index => $customer) {
            $projectCount = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $projectCount; $i++) {
                $lead = fake()->randomElement($pms);
                Auth::login($lead);

                $status = $this->pickStatus();
                $start = fake()->dateTimeBetween('-5 months', '-2 weeks');
                $target = (clone $start)->modify('+'.fake()->numberBetween(45, 160).' days');

                if (in_array($status, [ProjectStatus::Completed, ProjectStatus::Maintenance], true)) {
                    $progress = 100;
                } elseif ($status === ProjectStatus::Lead || $status === ProjectStatus::Quotation) {
                    $progress = fake()->numberBetween(0, 10);
                } else {
                    $progress = fake()->numberBetween(15, 90);
                }

                // Salt a few overdue projects so the dashboard shows problems.
                if ($index % 4 === 0 && $i === 0 && $status->isActive()) {
                    $target = now()->subDays(fake()->numberBetween(4, 25));
                    $progress = fake()->numberBetween(35, 70);
                }

                $value = fake()->numberBetween(9, 140) * 1000;

                /** @var Project $project */
                $project = Project::factory()->create([
                    'customer_id' => $customer->id,
                    'lead_id' => $lead->id,
                    'customer_pic_name' => $customer->contact_person,
                    'customer_pic_phone' => $customer->phone,
                    'status' => $status,
                    'priority' => fake()->randomElement(Priority::cases()),
                    'progress' => $progress,
                    'start_date' => $start,
                    'target_end_date' => $target,
                    'actual_end_date' => $progress === 100 ? $target : null,
                    'budget' => $value * 0.65,
                    'value' => $value,
                ]);

                $members = fake()->randomElements($builders, fake()->numberBetween(2, 4));
                $project->members()->sync(collect($members)->pluck('id'));

                foreach ($milestoneNames as $position => $name) {
                    $mProgress = min(100, max(0, $progress - (4 - $position) * fake()->numberBetween(5, 25)));
                    $project->milestones()->create([
                        'name' => $name,
                        'progress' => $mProgress,
                        'status' => match (true) {
                            $mProgress >= 100 => MilestoneStatus::Completed,
                            $mProgress <= 0 => MilestoneStatus::Pending,
                            default => MilestoneStatus::InProgress,
                        },
                        'due_date' => (clone $start)->modify('+'.(($position + 1) * 20).' days'),
                        'completed_at' => $mProgress >= 100 ? (clone $start)->modify('+'.(($position + 1) * 20).' days') : null,
                        'position' => $position,
                    ]);
                }

                $this->createTasks($project, $members, $progress);

                foreach (range(1, fake()->numberBetween(1, 4)) as $ignored) {
                    $author = fake()->randomElement(array_merge([$lead], $members));
                    $project->notes()->create([
                        'user_id' => $author->id,
                        'body' => fake()->randomElement([
                            'Client confirmed the scope over WhatsApp. Proceeding with current plan.',
                            'Waiting on logo assets from the customer before finalising the header.',
                            'Staging deployed. Shared credentials with the client PIC for review.',
                            'Payment terms: 50% upfront, 50% on deployment. First invoice sent.',
                            'Blocked on third-party API access — chased the client again today.',
                        ]),
                        'is_internal' => true,
                    ]);
                }

                $this->createFinance($project, $lead, $status, $progress);
                $this->createDeliveryExtras($project, $lead, $members, $progress);

                Auth::logout();
            }
        }

        $this->createContacts($customers);
        $this->createCommunications($customers, array_merge($pms, $builders));
        $this->createStandaloneQuotations($customers, $pms);
        $this->createPortalUsers($customers);
        $this->seedSettings();
        $this->seedNotifications($staff);
        $this->syncSequences();
    }

    /**
     * Factories mint references (LT-2026-001, ...) with their own counters and
     * bypass the ReferenceGenerator, so the `sequences` table would still read
     * zero after seeding and the first record created through the UI would
     * collide. Walk the seeded data and fast-forward each counter to its real
     * high-water mark.
     */
    private function syncSequences(): void
    {
        /** @var array<string, class-string<Model>> $sources */
        $sources = [
            'CUST' => Customer::class,
            'LT' => Project::class,
            'QT' => Quotation::class,
            'INV' => Invoice::class,
            'PAY' => Payment::class,
            'CR' => ChangeRequest::class,
            'BUG' => Bug::class,
            'TKT' => SupportTicket::class,
        ];

        $highWater = [];

        foreach ($sources as $prefix => $model) {
            $query = $model::query();

            if (in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
                $query->withTrashed();
            }

            foreach ($query->pluck('reference') as $reference) {
                if (! preg_match('/^'.preg_quote($prefix, '/').'-(\d{4})-(\d+)$/', (string) $reference, $m)) {
                    continue;
                }

                $key = $prefix.'|'.$m[1];
                $highWater[$key] = max($highWater[$key] ?? 0, (int) $m[2]);
            }
        }

        foreach ($highWater as $key => $value) {
            [$prefix, $period] = explode('|', $key);

            Sequence::query()->updateOrCreate(
                ['prefix' => $prefix, 'period' => $period],
                ['current_value' => $value],
            );
        }
    }

    /**
     * One portal login per active customer. Demo password: "password".
     *
     * @param  list<Customer>  $customers
     */
    private function createPortalUsers(array $customers): void
    {
        foreach ($customers as $index => $customer) {
            CustomerUser::factory()->create([
                'customer_id' => $customer->id,
                'name' => $customer->contact_person,
                'email' => $index === 0 ? 'client@abc.test' : Str::of($customer->company_name)
                    ->lower()->replaceMatches('/[^a-z0-9]+/', '')->append('@client.test')->value(),
                'is_active' => $customer->status->value !== 'inactive',
            ]);
        }
    }

    private function seedSettings(): void
    {
        app(Settings::class)->setMany([
            'company.name' => 'Laguna Tech Sdn Bhd',
            'company.registration_no' => '202601000123 (1400123-X)',
            'company.address' => "Level 12, Menara Laguna\nJalan Tun Razak, 50400 Kuala Lumpur",
            'company.email' => 'hello@lagunatech.com',
            'company.phone' => '03-2000 1234',
            'company.bank_details' => "Maybank — Laguna Tech Sdn Bhd\nAccount: 5123 4567 8901\nSwift: MBBEMYKL",
            'finance.default_tax_rate' => 8,
            'finance.quotation_validity_days' => 30,
            'finance.invoice_due_days' => 30,
            'finance.currency' => 'RM',
        ]);
    }

    /**
     * Change requests, bugs and meetings for a project.
     *
     * @param  list<User>  $members
     */
    private function createDeliveryExtras(Project $project, User $lead, array $members, int $progress): void
    {
        if ($progress > 20 && fake()->boolean(45)) {
            ChangeRequest::factory()->count(fake()->numberBetween(1, 2))->create([
                'project_id' => $project->id,
                'customer_id' => $project->customer_id,
                'requested_by' => $lead->id,
                'assigned_to' => fake()->boolean(60) ? fake()->randomElement($members)->id : null,
            ]);
        }

        if ($progress > 30) {
            Bug::factory()->count(fake()->numberBetween(2, 8))->create([
                'project_id' => $project->id,
                'reported_by' => fake()->randomElement(array_merge([$lead], $members))->id,
                'assigned_to' => fake()->randomElement($members)->id,
            ]);
        }

        foreach (range(1, fake()->numberBetween(1, 3)) as $ignored) {
            /** @var Meeting $meeting */
            $meeting = Meeting::factory()->create([
                'project_id' => $project->id,
                'customer_id' => $project->customer_id,
                'created_by' => $lead->id,
            ]);

            $pool = array_merge([$lead], $members);
            $attendees = fake()->randomElements($pool, min(count($pool), fake()->numberBetween(2, 4)));
            foreach ($attendees as $person) {
                $meeting->participants()->create(['user_id' => $person->id, 'is_organizer' => $person->is($lead)]);
            }
            $meeting->participants()->create(['name' => $project->customer->contact_person]);

            MeetingActionItem::factory()->count(fake()->numberBetween(0, 3))->create([
                'meeting_id' => $meeting->id,
                'owner_id' => fake()->randomElement($attendees)->id,
            ]);
        }

        // Documents on most projects (placeholder rows — no real files).
        if ($progress > 15) {
            Document::factory()->count(fake()->numberBetween(2, 6))->create([
                'project_id' => $project->id,
                'uploaded_by' => fake()->randomElement(array_merge([$lead], $members))->id,
            ]);
        }

        // Support tickets on delivered/live projects.
        if ($progress > 60 && fake()->boolean(55)) {
            SupportTicket::factory()->count(fake()->numberBetween(1, 3))->create([
                'customer_id' => $project->customer_id,
                'project_id' => $project->id,
                'assigned_to' => fake()->boolean(70) ? fake()->randomElement($members)->id : null,
            ])->each(function (SupportTicket $ticket) use ($members): void {
                SupportTicketReply::factory()->count(fake()->numberBetween(0, 3))->create([
                    'support_ticket_id' => $ticket->id,
                    'staff_id' => fake()->randomElement($members)->id,
                ]);
            });
        }

        // A maintenance plan once a project is complete or in maintenance.
        if ($progress >= 100 && fake()->boolean(70)) {
            MaintenancePlan::factory()->active()->create([
                'customer_id' => $project->customer_id,
                'project_id' => $project->id,
                'starts_on' => now()->subMonths(fake()->numberBetween(1, 10)),
            ]);
        }
    }

    /**
     * @param  list<Customer>  $customers
     * @param  list<User>  $staff
     */
    private function createCommunications(array $customers, array $staff): void
    {
        foreach ($customers as $customer) {
            Communication::factory()->count(fake()->numberBetween(2, 6))->create([
                'customer_id' => $customer->id,
                'project_id' => $customer->projects()->inRandomOrder()->value('id'),
                'user_id' => fake()->randomElement($staff)->id,
            ]);

            if ($customer->status->value === 'inactive') {
                continue;
            }

            SupportTicket::factory()->count(fake()->numberBetween(1, 3))->create([
                'customer_id' => $customer->id,
                'project_id' => $customer->projects()->inRandomOrder()->value('id'),
                'assigned_to' => fake()->randomElement($staff)->id,
            ])->each(fn (SupportTicket $ticket) => SupportTicketReply::factory()
                ->count(fake()->numberBetween(0, 3))
                ->create(['support_ticket_id' => $ticket->id, 'staff_id' => fake()->randomElement($staff)->id]));

            if (fake()->boolean(55)) {
                MaintenancePlan::factory()->create([
                    'customer_id' => $customer->id,
                    'project_id' => $customer->projects()->inRandomOrder()->value('id'),
                ]);
            }
        }
    }

    /**
     * @param  array<string, list<User>>  $staff
     */
    private function seedNotifications(array $staff): void
    {
        $recipients = array_merge($staff['admin'], $staff['project_manager'], $staff['finance']);

        $paidInvoice = Invoice::query()->where('status', 'paid')->with(['customer', 'payments'])->inRandomOrder()->first();
        $overdueInvoice = Invoice::query()->where('status', 'overdue')->with('customer')->inRandomOrder()->first();
        $approvedQuote = Quotation::query()->where('status', 'approved')->with('customer')->inRandomOrder()->first();
        $nearProject = Project::query()->active()->whereNotNull('target_end_date')->orderBy('target_end_date')->first();

        $payment = $paidInvoice?->payments->first();
        $payment?->setRelation('invoice', $paidInvoice);

        foreach ($recipients as $user) {
            if ($payment) {
                $user->notify(new PaymentReceived($payment, true));
            }
            if ($overdueInvoice) {
                $user->notify(new InvoiceOverdue($overdueInvoice));
            }
            if ($approvedQuote) {
                $user->notify(new QuotationDecision($approvedQuote, true));
            }
            if ($nearProject) {
                $user->notify(new ProjectDeadlineApproaching($nearProject, false));
            }
        }

        // Leave a couple unread, mark the rest read so the badge looks realistic.
        foreach ($recipients as $user) {
            $user->notifications()->latest()->skip(2)->take(10)->get()->each->markAsRead();
        }
    }

    private function createFinance(Project $project, User $lead, ProjectStatus $status, int $progress): void
    {
        Auth::login($lead);

        // Most delivered projects were quoted first.
        if ($progress > 10 && fake()->boolean(70)) {
            Quotation::factory()
                ->for($project->customer)
                ->approved()
                ->create([
                    'project_id' => $project->id,
                    'created_by' => $lead->id,
                    'title' => $project->name.' — Proposal',
                ]);
        }

        // Invoices appear once a project is underway.
        if (in_array($status, [ProjectStatus::Lead, ProjectStatus::Quotation], true)) {
            return;
        }

        $invoiceCount = $progress >= 100 ? 2 : 1;

        for ($n = 0; $n < $invoiceCount; $n++) {
            $isFinal = $n === $invoiceCount - 1 && $progress >= 100;

            /** @var Invoice $invoice */
            $invoice = Invoice::factory()
                ->for($project->customer)
                ->create([
                    'project_id' => $project->id,
                    'created_by' => $lead->id,
                    'title' => $invoiceCount === 1 ? 'Project fee' : ($n === 0 ? 'Deposit (50%)' : 'Final payment (50%)'),
                    'issue_date' => now()->subDays(fake()->numberBetween(10, 80)),
                    'due_date' => now()->subDays(fake()->numberBetween(-20, 50)),
                ]);

            // Decide how much has been paid.
            $roll = fake()->numberBetween(1, 100);
            $payFraction = match (true) {
                $isFinal && $progress >= 100 && $roll > 40 => 1.0,
                $n === 0 && $progress > 30 => 1.0,
                $roll > 70 => 0.5,
                $roll > 45 => 0.0,
                default => fake()->randomFloat(2, 0.2, 0.8),
            };

            if ($payFraction > 0) {
                $invoice->payments()->create([
                    'reference' => app(ReferenceGenerator::class)->next('PAY'),
                    'recorded_by' => $lead->id,
                    'amount' => round((float) $invoice->total * $payFraction, 2),
                    'paid_on' => now()->subDays(fake()->numberBetween(1, 40)),
                    'method' => fake()->randomElement(PaymentMethod::cases()),
                    'reference_number' => strtoupper(fake()->bothify('TXN####??')),
                ]);
            }

            app(SyncInvoiceState::class)->handle($invoice->fresh('payments'));
        }
    }

    /**
     * @param  list<Customer>  $customers
     * @param  list<User>  $pms
     */
    private function createStandaloneQuotations(array $customers, array $pms): void
    {
        foreach (fake()->randomElements($customers, 5) as $customer) {
            Quotation::factory()
                ->for($customer)
                ->status(fake()->randomElement([
                    QuotationStatus::Draft,
                    QuotationStatus::Sent,
                    QuotationStatus::Viewed,
                ]))
                ->create([
                    'created_by' => fake()->randomElement($pms)->id,
                ]);
        }
    }

    /**
     * @return array<string, list<User>>
     */
    private function createStaff(): array
    {
        $owner = User::factory()->create([
            'name' => 'Saiful Rahman',
            'email' => 'owner@lagunatech.com',
            'position' => 'Founder & CEO',
            'department' => 'Management',
        ]);
        $owner->assignRole(RoleEnum::SuperAdmin->value);

        $admin = User::factory()->create([
            'name' => 'Nadia Ismail',
            'email' => 'admin@lagunatech.com',
            'position' => 'Operations Manager',
            'department' => 'Management',
        ]);
        $admin->assignRole(RoleEnum::Admin->value);

        $finance = User::factory()->create([
            'name' => 'Farah Lim',
            'email' => 'finance@lagunatech.com',
            'position' => 'Finance Executive',
            'department' => 'Finance',
        ]);
        $finance->assignRole(RoleEnum::Finance->value);

        $projectManagers = collect(['Fikri Hassan', 'Amirah Zulkifli'])
            ->map(function (string $name): User {
                $user = User::factory()->create([
                    'name' => $name,
                    'email' => strtolower(explode(' ', $name)[0]).'@lagunatech.com',
                    'position' => 'Project Manager',
                    'department' => 'Delivery',
                ]);
                $user->assignRole(RoleEnum::ProjectManager->value);

                return $user;
            })->all();

        $developers = collect(['Ahmad Faiz', 'Wei Jie Tan', 'Raj Kumar', 'Iman Yusof'])
            ->map(function (string $name): User {
                $user = User::factory()->create([
                    'name' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)).'@lagunatech.com',
                    'position' => 'Software Engineer',
                    'department' => 'Engineering',
                ]);
                $user->assignRole(RoleEnum::Developer->value);

                return $user;
            })->all();

        $designers = collect(['Sofia Aziz', 'Daniel Cheah'])
            ->map(function (string $name): User {
                $user = User::factory()->create([
                    'name' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)).'@lagunatech.com',
                    'position' => 'Product Designer',
                    'department' => 'Design',
                ]);
                $user->assignRole(RoleEnum::Designer->value);

                return $user;
            })->all();

        $support = User::factory()->create([
            'name' => 'Hakim Osman',
            'email' => 'support@lagunatech.com',
            'position' => 'Support Engineer',
            'department' => 'Support',
        ]);
        $support->assignRole(RoleEnum::Support->value);

        return [
            'admin' => [$owner, $admin],
            'finance' => [$finance],
            'project_manager' => $projectManagers,
            'developer' => $developers,
            'designer' => $designers,
            'support' => [$support],
        ];
    }

    /**
     * @param  list<User>  $managers
     * @return list<Customer>
     */
    private function createCustomers(array $managers): array
    {
        $seed = [
            ['ABC Sdn Bhd', 'Ahmad Kamal', 'active'],
            ['XYZ Enterprise', 'Lim Chee Keong', 'active'],
            ['Laguna Wedding', 'Nurul Aina', 'active'],
            ['Prima Logistics Sdn Bhd', 'Suresh Nair', 'active'],
            ['GreenLeaf Cafe', 'Michelle Wong', 'prospect'],
            ['Bandar Property Group', 'Zulkarnain Idris', 'active'],
            ['MediCare Clinic', 'Dr. Tan Mei Ling', 'active'],
            ['EduBright Academy', 'Faizal Roslan', 'inactive'],
        ];

        $customers = [];
        foreach ($seed as $index => [$name, $contact, $status]) {
            $customers[] = Customer::factory()->create([
                'reference' => sprintf('CUST-%s-%s', now()->year, str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)),
                'company_name' => $name,
                'contact_person' => $contact,
                'status' => $status,
                'account_manager_id' => fake()->randomElement($managers)->id,
            ]);
        }

        return $customers;
    }

    /**
     * @param  list<Customer>  $customers
     */
    private function createContacts(array $customers): void
    {
        foreach ($customers as $customer) {
            CustomerContact::factory()->primary()->create([
                'customer_id' => $customer->id,
                'name' => $customer->contact_person,
                'email' => $customer->email,
                'phone' => $customer->phone,
            ]);

            CustomerContact::factory()->count(fake()->numberBetween(0, 2))->create([
                'customer_id' => $customer->id,
            ]);
        }
    }

    /**
     * @param  list<User>  $members
     */
    private function createTasks(Project $project, array $members, int $progress): void
    {
        $count = fake()->numberBetween(6, 14);
        $doneShare = $progress / 100;

        for ($i = 0; $i < $count; $i++) {
            $isDone = fake()->boolean((int) ($doneShare * 100));
            $status = $isDone
                ? TaskStatus::Done
                : fake()->randomElement([TaskStatus::Todo, TaskStatus::InProgress, TaskStatus::Blocked, TaskStatus::Review]);

            Task::factory()->create([
                'project_id' => $project->id,
                'assignee_id' => fake()->randomElement($members)->id,
                'created_by' => $project->lead_id,
                'status' => $status,
                'completed_at' => $status === TaskStatus::Done ? fake()->dateTimeBetween('-2 months', 'now') : null,
                'due_date' => fake()->boolean(75) ? fake()->dateTimeBetween('-2 weeks', '+5 weeks') : null,
            ]);
        }
    }

    private function pickStatus(): ProjectStatus
    {
        return fake()->randomElement([
            ProjectStatus::Planning,
            ProjectStatus::Development,
            ProjectStatus::Development,
            ProjectStatus::Testing,
            ProjectStatus::CustomerReview,
            ProjectStatus::Deployment,
            ProjectStatus::Completed,
            ProjectStatus::Maintenance,
            ProjectStatus::Quotation,
        ]);
    }
}
