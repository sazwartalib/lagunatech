<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\ProjectHealth;
use App\Enums\ProjectStatus;
use App\Support\ProjectHealthCalculator;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'reference',
        'name',
        'customer_id',
        'lead_id',
        'customer_pic_name',
        'customer_pic_phone',
        'type',
        'technology',
        'description',
        'status',
        'priority',
        'progress',
        'start_date',
        'target_end_date',
        'actual_end_date',
        'budget',
        'value',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'priority' => Priority::class,
            'progress' => 'integer',
            'start_date' => 'date',
            'target_end_date' => 'date',
            'actual_end_date' => 'date',
            'budget' => 'decimal:2',
            'value' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status', 'priority', 'progress', 'lead_id', 'target_end_date', 'value'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role_on_project')
            ->withTimestamps();
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('position');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ProjectNote::class)->latest();
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class)->latest();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->latest();
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class)->latest();
    }

    public function bugs(): HasMany
    {
        return $this->hasMany(Bug::class)->latest();
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class)->latest('scheduled_at');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class)->latest();
    }

    public function drawings(): MorphMany
    {
        return $this->morphMany(Drawing::class, 'drawable')->latest();
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class)->latest();
    }

    public function maintenancePlans(): HasMany
    {
        return $this->hasMany(MaintenancePlan::class)->latest();
    }

    /**
     * Derived delivery health. Never persisted — always reflects "now".
     */
    protected function health(): Attribute
    {
        return Attribute::get(fn (): ProjectHealth => app(ProjectHealthCalculator::class)->for($this))
            ->shouldCache();
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::get(fn (): bool => $this->health === ProjectHealth::Overdue);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->whereNotIn('status', [ProjectStatus::Completed->value, ProjectStatus::Cancelled->value]);
    }

    #[Scope]
    protected function dueWithin(Builder $query, int $days): Builder
    {
        return $query->active()
            ->whereNotNull('target_end_date')
            ->whereBetween('target_end_date', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    #[Scope]
    protected function overdue(Builder $query): Builder
    {
        return $query->active()
            ->whereNotNull('target_end_date')
            ->whereDate('target_end_date', '<', now()->toDateString());
    }

    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('reference', 'like', "%{$term}%")
                ->orWhereHas('customer', fn (Builder $c) => $c->where('company_name', 'like', "%{$term}%"));
        });
    }
}
