<?php

namespace App\Actions\Drawings;

use App\Models\Drawing;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class StoreDrawingImage
{
    public function handle(Drawing $drawing, UploadedFile $file): string
    {
        $path = $file->storeAs(
            "drawings/{$drawing->id}/images",
            Str::uuid()->toString().'.'.$file->getClientOriginalExtension(),
            'public',
        );

        // asset() (not Storage::url()) so this resolves against the current
        // request's host — Storage::url() bakes in APP_URL, which drifts from
        // the real dev domain under Herd.
        return asset('storage/'.$path);
    }
}
