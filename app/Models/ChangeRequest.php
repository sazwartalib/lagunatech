<?php

namespace App\Models;

use App\Enums\ChangeRequestStatus;
use Database\Factories\ChangeRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ChangeRequest extends Model
{
    /** @use HasFactory<ChangeRequestFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'reference',
        'project_id',
        'customer_id',
        'requested_by',
        'assigned_to',
        'title',
        'description',
        'estimated_cost',
        'additional_days',
        'status',
        'task_id',
        'decided_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ChangeRequestStatus::class,
            'estimated_cost' => 'decimal:2',
            'additional_days' => 'integer',
            'decided_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'estimated_cost', 'assigned_to', 'task_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    #[Scope]
    protected function awaitingAction(Builder $query): Builder
    {
        return $query->whereIn('status', [
            ChangeRequestStatus::Pending->value,
            ChangeRequestStatus::Quoted->value,
            ChangeRequestStatus::WaitingApproval->value,
        ]);
    }
}
