<?php

namespace App\Console\Commands;

use App\Actions\Invoices\SyncInvoiceState;
use App\Enums\Role;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use App\Notifications\InvoiceOverdue;
use App\Notifications\ProjectDeadlineApproaching;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

/**
 * Daily digest of things that need attention. Each notification is de-duplicated
 * for 20 hours so re-running the command never spams anyone.
 */
class SendReminders extends Command
{
    protected $signature = 'reminders:dispatch';

    protected $description = 'Notify staff about overdue invoices and approaching project deadlines';

    public function handle(SyncInvoiceState $syncInvoiceState): int
    {
        $this->refreshOverdueInvoices($syncInvoiceState);
        $this->remindProjectDeadlines();

        $this->info('Reminders dispatched.');

        return self::SUCCESS;
    }

    private function refreshOverdueInvoices(SyncInvoiceState $syncInvoiceState): void
    {
        $finance = User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', [Role::Finance->value, Role::Admin->value]))
            ->get();

        Invoice::query()->open()->with('customer', 'project.lead')->each(function (Invoice $invoice) use ($syncInvoiceState, $finance): void {
            $syncInvoiceState->handle($invoice);

            if (! $invoice->is_overdue || ! $this->firstTimeToday("invoice-overdue:{$invoice->id}")) {
                return;
            }

            $recipients = $finance
                ->when($invoice->project?->lead, fn ($c) => $c->push($invoice->project->lead))
                ->unique('id');

            Notification::send($recipients, new InvoiceOverdue($invoice));
        });
    }

    private function remindProjectDeadlines(): void
    {
        Project::query()->active()->whereNotNull('target_end_date')->with('lead', 'members')
            ->where('target_end_date', '<=', now()->addDays(7)->toDateString())
            ->each(function (Project $project): void {
                if (! $this->firstTimeToday("project-deadline:{$project->id}")) {
                    return;
                }

                $recipients = collect([$project->lead])->merge($project->members)->filter()->unique('id');

                if ($recipients->isEmpty()) {
                    return;
                }

                Notification::send($recipients, new ProjectDeadlineApproaching(
                    $project,
                    $project->target_end_date->isPast(),
                ));
            });
    }

    private function firstTimeToday(string $key): bool
    {
        return Cache::add("reminder:{$key}", true, now()->addHours(20));
    }
}
