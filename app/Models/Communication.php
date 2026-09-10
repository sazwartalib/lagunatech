<?php

namespace App\Models;

use App\Enums\CommunicationType;
use Database\Factories\CommunicationFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Communication extends Model
{
    /** @use HasFactory<CommunicationFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'project_id',
        'user_id',
        'type',
        'communicated_at',
        'summary',
        'action_required',
        'follow_up_on',
        'follow_up_done',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => CommunicationType::class,
            'communicated_at' => 'datetime',
            'follow_up_on' => 'date',
            'follow_up_done' => 'boolean',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function needsFollowUp(Builder $query): Builder
    {
        return $query->whereNotNull('follow_up_on')
            ->where('follow_up_done', false);
    }
}
