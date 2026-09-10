<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Fired when a quotation is approved or rejected.
 */
class QuotationDecision extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Quotation $quotation,
        public readonly bool $approved,
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
        return [
            'icon' => $this->approved ? '✅' : '🚫',
            'title' => $this->approved ? 'Quotation approved' : 'Quotation rejected',
            'body' => sprintf(
                '%s for %s was %s',
                $this->quotation->reference,
                $this->quotation->customer->company_name,
                $this->approved ? 'approved' : 'rejected',
            ),
            'url' => route('quotations.show', $this->quotation),
        ];
    }
}
