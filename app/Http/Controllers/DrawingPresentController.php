<?php

namespace App\Http\Controllers;

use App\Models\Drawing;
use Illuminate\View\View;

class DrawingPresentController extends Controller
{
    public function show(Drawing $drawing): View
    {
        $this->authorize('view', $drawing);

        $siblings = Drawing::query()
            ->where('drawable_type', $drawing->drawable_type)
            ->where('drawable_id', $drawing->drawable_id)
            ->orderBy('created_at')
            ->get(['id', 'title']);

        $index = $siblings->search(fn (Drawing $d) => $d->id === $drawing->id);

        return view('drawings.present', [
            'drawing' => $drawing,
            'previous' => $index !== false && $index > 0 ? $siblings[$index - 1] : null,
            'next' => $index !== false && $index < $siblings->count() - 1 ? $siblings[$index + 1] : null,
        ]);
    }
}
