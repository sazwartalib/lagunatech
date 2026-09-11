<?php

namespace App\Actions\Drawings;

use App\Models\Drawing;
use Illuminate\Database\Eloquent\Model;

class CreateDrawing
{
    public function handle(Model $drawable, string $title): Drawing
    {
        return Drawing::create([
            'drawable_type' => $drawable::class,
            'drawable_id' => $drawable->getKey(),
            'title' => $title,
            'canvas_data' => ['version' => '6.0.0', 'objects' => []],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
    }
}
