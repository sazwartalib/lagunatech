<?php

namespace App\Models;

use App\Enums\BillingCycle;
use App\Enums\MaintenanceStatus;
use Database\Factories\MaintenancePlanFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePlan extends Model
{
    /** @use HasFactory<MaintenancePlanFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'project_id',
        'name',
        'description',
        'status',
        'billing_cycle',
        'fee',
        'starts_on',
        'ends_on',
        'next_renewal_on',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MaintenanceStatus::class,
            'billing_cycle' => BillingCycle::class,
            'fee' => 'decimal:2',
            'starts_on' => 'date',
            'ends_on' => 'date',
            'next_renewal_on' => 'date',
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

    protected function annualValue(): Attribute
    {
        return Attribute::get(fn (): float => $this->billing_cycle->annualise((float) $this->fee));
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', MaintenanceStatus::Active->value);
    }

    #[Scope]
    protected function renewingWithin(Builder $query, int $days): Builder
    {
        return $query->active()
            ->whereNotNull('next_renewal_on')
            ->whereBetween('next_renewal_on', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }
}
