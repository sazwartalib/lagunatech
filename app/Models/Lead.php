<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'reference',
        'name',
        'company',
        'email',
        'phone',
        'project_type',
        'budget_range',
        'message',
        'source',
        'status',
        'owner_id',
        'converted_customer_id',
        'ip_address',
        'contacted_at',
        'closed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'source' => LeadSource::class,
            'status' => LeadStatus::class,
            'contacted_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'owner_id', 'converted_customer_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function convertedCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'converted_customer_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    #[Scope]
    protected function open(Builder $query): Builder
    {
        return $query->whereNotIn('status', [LeadStatus::Won->value, LeadStatus::Lost->value]);
    }

    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('company', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('reference', 'like', "%{$term}%");
        });
    }
}
