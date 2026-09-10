<?php

namespace App\Actions\Quotations;

use App\Enums\QuotationStatus;
use App\Enums\Role;
use App\Models\Quotation;
use App\Models\User;
use App\Notifications\QuotationDecision;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

/**
 * Moves a quotation through its lifecycle and records the side effects
 * (timestamps, notifications). Illegal transitions are rejected.
 */
class TransitionQuotationStatus
{
    /** @var array<string, list<string>> */
    private const ALLOWED = [
        'draft' => ['sent'],
        'sent' => ['viewed', 'approved', 'rejected', 'expired', 'draft'],
        'viewed' => ['approved', 'rejected', 'expired'],
        'approved' => [],
        'rejected' => ['draft'],
        'expired' => ['draft', 'sent'],
    ];

    public function handle(Quotation $quotation, QuotationStatus $to): Quotation
    {
        $from = $quotation->status;

        if ($from === $to) {
            return $quotation;
        }

        if (! in_array($to->value, self::ALLOWED[$from->value] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => "A {$from->label()} quotation cannot become {$to->label()}.",
            ]);
        }

        $quotation->status = $to;

        match ($to) {
            QuotationStatus::Sent => $quotation->sent_at ??= now(),
            QuotationStatus::Viewed => $quotation->viewed_at ??= now(),
            QuotationStatus::Approved, QuotationStatus::Rejected => $quotation->decided_at = now(),
            default => null,
        };

        $quotation->save();

        if (in_array($to, [QuotationStatus::Approved, QuotationStatus::Rejected], true)) {
            $this->notifyDeciders($quotation, $to === QuotationStatus::Approved);
        }

        return $quotation;
    }

    private function notifyDeciders(Quotation $quotation, bool $approved): void
    {
        $recipients = User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', [
                Role::Admin->value, Role::ProjectManager->value, Role::Finance->value,
            ]))
            ->get()
            ->when(
                $quotation->creator !== null,
                fn ($c) => $c->push($quotation->creator)
            )
            ->unique('id')
            ->values();

        Notification::send($recipients, new QuotationDecision($quotation->loadMissing('customer'), $approved));
    }
}
