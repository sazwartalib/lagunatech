<?php

namespace App\Models;

use App\Enums\DrawingStatus;
use Database\Factories\DrawingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Drawing extends Model
{
    /** @use HasFactory<DrawingFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'drawable_type',
        'drawable_id',
        'title',
        'description',
        'canvas_data',
        'thumbnail_path',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'canvas_data' => 'array',
            'status' => DrawingStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (Drawing $drawing): void {
            Storage::disk('public')->deleteDirectory('drawings/'.$drawing->id);
        });
    }

    public function drawable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
