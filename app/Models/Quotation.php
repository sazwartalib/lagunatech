<?php

namespace App\Models;

use App\Enums\QuotationStatus;
use App\Models\Concerns\CalculatesDocumentTotals;
use Database\Factories\QuotationFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Quotation extends Model
{
    /** @use HasFactory<QuotationFactory> */
    use CalculatesDocumentTotals, HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'reference',
        'customer_id',
        'project_id',
        'created_by',
        'title',
        'status',
        'issue_date',
        'valid_until',
        'discount_type',
        'discount_value',
        'tax_rate',
        'terms',
        'notes',
        'sent_at',
        'viewed_at',
        'decided_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => QuotationStatus::class,
            'issue_date' => 'date',
            'valid_until' => 'date',
            'discount_value' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'sent_at' => 'datetime',
            'viewed_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total', 'valid_until', 'project_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('position');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function isExpired(): bool
    {
        return $this->valid_until !== null
            && $this->valid_until->isPast()
            && ! $this->status->isDecided();
    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->whereIn('status', [
            QuotationStatus::Sent->value,
            QuotationStatus::Viewed->value,
        ]);
    }

    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('reference', 'like', "%{$term}%")
                ->orWhere('title', 'like', "%{$term}%")
                ->orWhereHas('customer', fn (Builder $c) => $c->where('company_name', 'like', "%{$term}%"));
        });
    }
}
