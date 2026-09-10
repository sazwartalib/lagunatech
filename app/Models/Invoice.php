<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Models\Concerns\CalculatesDocumentTotals;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use CalculatesDocumentTotals, HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'reference',
        'customer_id',
        'project_id',
        'quotation_id',
        'created_by',
        'title',
        'status',
        'issue_date',
        'due_date',
        'discount_type',
        'discount_value',
        'tax_rate',
        'terms',
        'notes',
        'sent_at',
        'paid_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'issue_date' => 'date',
            'due_date' => 'date',
            'discount_value' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'sent_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total', 'amount_paid', 'due_date'])
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

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('position');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('paid_on');
    }

    protected function outstanding(): Attribute
    {
        return Attribute::get(fn (): float => round(
            max(0, (float) $this->total - (float) $this->amount_paid),
            2
        ));
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::get(fn (): bool => $this->status->isOpen()
            && $this->due_date !== null
            && $this->due_date->isPast()
            && $this->outstanding > 0);
    }

    #[Scope]
    protected function open(Builder $query): Builder
    {
        return $query->whereNotIn('status', [InvoiceStatus::Paid->value, InvoiceStatus::Cancelled->value]);
    }

    #[Scope]
    protected function overdue(Builder $query): Builder
    {
        return $query->open()
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereColumn('amount_paid', '<', 'total');
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
