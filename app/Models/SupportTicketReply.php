<?php

namespace App\Models;

use Database\Factories\SupportTicketReplyFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketReply extends Model
{
    /** @use HasFactory<SupportTicketReplyFactory> */
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'staff_id',
        'customer_user_id',
        'body',
        'is_internal',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function customerUser(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class);
    }

    protected function authorName(): Attribute
    {
        return Attribute::get(fn (): string => $this->staff?->name
            ?? $this->customerUser?->name
            ?? 'System');
    }

    protected function fromCustomer(): Attribute
    {
        return Attribute::get(fn (): bool => $this->customer_user_id !== null);
    }
}
