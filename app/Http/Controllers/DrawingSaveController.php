<?php

namespace App\Http\Controllers;

use App\Actions\Drawings\SaveDrawingCanvas;
use App\Models\Drawing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DrawingSaveController extends Controller
{
    public function update(Request $request, Drawing $drawing, SaveDrawingCanvas $save): JsonResponse
    {
        $this->authorize('update', $drawing);

        $validated = $request->validate([
            'canvas_data' => ['required', 'array'],
            'thumbnail' => ['nullable', 'string', 'starts_with:data:image/png;base64,'],
        ]);

        abort_if(strlen(json_encode($validated['canvas_data'])) > 5_000_000, 422, 'Drawing is too large to save.');

        $save->handle($drawing, $validated['canvas_data'], $validated['thumbnail'] ?? null);

        return response()->json([
            'saved_at' => $drawing->updated_at->toIso8601String(),
        ]);
    }
}
