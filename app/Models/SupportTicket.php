<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\SupportTicketStatus;
use Database\Factories\SupportTicketFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    /** @use HasFactory<SupportTicketFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'customer_id',
        'project_id',
        'opened_by_user_id',
        'assigned_to',
        'subject',
        'description',
        'priority',
        'status',
        'resolved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'status' => SupportTicketStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class, 'opened_by_user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class)->oldest();
    }

    #[Scope]
    protected function open(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            SupportTicketStatus::Resolved->value,
            SupportTicketStatus::Closed->value,
        ]);
    }
}
