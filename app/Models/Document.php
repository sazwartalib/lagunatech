<?php

namespace App\Models;

use App\Enums\DocumentCategory;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'uploaded_by',
        'title',
        'category',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'is_internal',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => DocumentCategory::class,
            'size' => 'integer',
            'is_internal' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Remove the stored file when its record is deleted.
        static::deleted(function (Document $document): void {
            Storage::disk($document->disk)->delete($document->path);
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    protected function humanSize(): Attribute
    {
        return Attribute::get(fn (): string => Number::fileSize($this->size));
    }

    #[Scope]
    protected function customerVisible(Builder $query): Builder
    {
        return $query->where('is_internal', false);
    }
}
