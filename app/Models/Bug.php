<?php

namespace App\Models;

use App\Enums\BugStatus;
use App\Enums\Priority;
use Database\Factories\BugFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bug extends Model
{
    /** @use HasFactory<BugFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'project_id',
        'title',
        'description',
        'reported_by',
        'assigned_to',
        'priority',
        'status',
        'due_date',
        'resolved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'status' => BugStatus::class,
            'due_date' => 'date',
            'resolved_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BugComment::class)->latest();
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::get(fn (): bool => $this->due_date !== null
            && $this->status->isOpen()
            && $this->due_date->isPast());
    }

    #[Scope]
    protected function open(Builder $query): Builder
    {
        return $query->where('status', '!=', BugStatus::Closed->value);
    }

    #[Scope]
    protected function critical(Builder $query): Builder
    {
        return $query->open()->whereIn('priority', [Priority::High->value, Priority::Urgent->value]);
    }
}
