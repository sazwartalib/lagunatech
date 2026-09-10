<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Number;

class PaymentReceived extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Payment $payment,
        public readonly bool $invoiceSettled = false,
    ) {}

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
        $invoice = $this->payment->invoice;

        return [
            'icon' => '💰',
            'title' => 'Payment received',
            'body' => sprintf(
                'RM %s recorded against %s%s',
                Number::format((float) $this->payment->amount, 2),
                $invoice->reference,
                $this->invoiceSettled ? ' — now fully paid' : '',
            ),
            'url' => route('invoices.show', $invoice),
        ];
    }
}
