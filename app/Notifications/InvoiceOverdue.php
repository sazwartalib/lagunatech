<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Number;

class InvoiceOverdue extends Notification
{
    use Queueable;

    public function __construct(public readonly Invoice $invoice) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'icon' => '⚠️',
            'title' => 'Invoice overdue',
            'body' => sprintf(
                '%s (%s) is overdue — RM %s outstanding',
                $this->invoice->reference,
                $this->invoice->customer->company_name,
                Number::format($this->invoice->outstanding, 2),
            ),
            'url' => route('invoices.show', $this->invoice),
        ];
    }
}
