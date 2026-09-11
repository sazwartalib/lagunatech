<?php

namespace App\Http\Controllers;

use App\Actions\Drawings\StoreDrawingImage;
use App\Models\Drawing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DrawingImageController extends Controller
{
    public function store(Request $request, Drawing $drawing, StoreDrawingImage $store): JsonResponse
    {
        $this->authorize('update', $drawing);

        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        return response()->json([
            'url' => $store->handle($drawing, $validated['image']),
        ]);
    }
}
