<?php

namespace App\Models;

use App\Enums\LeadAttachmentKind;
use Database\Factories\LeadAttachmentFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

class LeadAttachment extends Model
{
    /** @use HasFactory<LeadAttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'kind',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => LeadAttachmentKind::class,
            'size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Remove the stored file when its record is deleted.
        static::deleted(function (LeadAttachment $attachment): void {
            Storage::disk($attachment->disk)->delete($attachment->path);
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    protected function humanSize(): Attribute
    {
        return Attribute::get(fn (): string => Number::fileSize($this->size));
    }
}
