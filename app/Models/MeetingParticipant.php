<?php

namespace App\Models;

use Database\Factories\MeetingParticipantFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingParticipant extends Model
{
    /** @use HasFactory<MeetingParticipantFactory> */
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'user_id',
        'name',
        'is_organizer',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_organizer' => 'boolean',
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function displayName(): Attribute
    {
        return Attribute::get(fn (): string => $this->user?->name ?? $this->name ?? 'Guest');
    }
}
