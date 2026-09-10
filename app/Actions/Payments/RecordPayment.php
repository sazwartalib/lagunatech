<?php

namespace App\Actions\Payments;

use App\Actions\Invoices\SyncInvoiceState;
use App\Enums\Role;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentReceived;
use App\Support\ReferenceGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class RecordPayment
{
    public function __construct(
        private readonly ReferenceGenerator $references,
        private readonly SyncInvoiceState $syncInvoiceState,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Invoice $invoice, array $data): Payment
    {
        [$payment, $becamePaid] = DB::transaction(function () use ($invoice, $data): array {
            $payment = $invoice->payments()->create([
                'reference' => $this->references->next('PAY'),
                'recorded_by' => auth()->id(),
                'amount' => $data['amount'],
                'paid_on' => $data['paid_on'],
                'method' => $data['method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $wasPaid = $invoice->status->value === 'paid';
            $this->syncInvoiceState->handle($invoice->fresh('payments'));

            return [$payment, ! $wasPaid && $invoice->fresh()->status->value === 'paid'];
        });

        Notification::send(
            $this->recipientsFor($invoice),
            new PaymentReceived($payment->fresh('invoice'), $becamePaid),
        );

        return $payment;
    }

    /**
     * Finance team plus the project's internal PIC.
     *
     * @return Collection<int, User>
     */
    private function recipientsFor(Invoice $invoice): Collection
    {
        $finance = User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', Role::Finance->value))
            ->get();

        $lead = $invoice->project?->lead;

        return $finance
            ->when($lead !== null, fn ($c) => $c->push($lead))
            ->unique('id')
            ->values();
    }
}
