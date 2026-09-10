<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'reference',
        'invoice_id',
        'recorded_by',
        'amount',
        'paid_on',
        'method',
        'reference_number',
        'proof_path',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_on' => 'date',
            'method' => PaymentMethod::class,
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    #[Scope]
    protected function inMonth(Builder $query, ?\DateTimeInterface $month = null): Builder
    {
        $month ??= now();

        return $query->whereBetween('paid_on', [
            $month->format('Y-m-01'),
            (clone $month)->modify('last day of this month')->format('Y-m-d'),
        ]);
    }
}
