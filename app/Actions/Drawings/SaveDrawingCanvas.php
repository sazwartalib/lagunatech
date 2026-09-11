<?php

namespace App\Actions\Drawings;

use App\Models\Drawing;
use Illuminate\Support\Facades\Storage;

class SaveDrawingCanvas
{
    /**
     * @param  array<string, mixed>  $canvasData
     */
    public function handle(Drawing $drawing, array $canvasData, ?string $thumbnailDataUrl = null): Drawing
    {
        $attributes = [
            'canvas_data' => $canvasData,
            'updated_by' => auth()->id(),
        ];

        if ($thumbnailDataUrl !== null) {
            $attributes['thumbnail_path'] = $this->storeThumbnail($drawing, $thumbnailDataUrl);
        }

        $drawing->update($attributes);

        return $drawing;
    }

    private function storeThumbnail(Drawing $drawing, string $dataUrl): ?string
    {
        if (! preg_match('/^data:image\/png;base64,(?<data>.+)$/', $dataUrl, $matches)) {
            return $drawing->thumbnail_path;
        }

        $contents = base64_decode($matches['data'], true);

        if ($contents === false || strlen($contents) > 2 * 1024 * 1024) {
            return $drawing->thumbnail_path;
        }

        $path = "drawings/{$drawing->id}/thumbnail.png";
        Storage::disk('public')->put($path, $contents);

        return $path;
    }
}
