<?php

namespace App\Http\Controllers;

use App\Models\Drawing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DrawingExportController extends Controller
{
    public function pdf(Request $request, Drawing $drawing): Response
    {
        $this->authorize('export', $drawing);

        $validated = $request->validate([
            'image' => ['required', 'string', 'starts_with:data:image/png;base64,'],
        ]);

        return Pdf::loadView('pdf.drawing', [
            'drawing' => $drawing,
            'image' => $validated['image'],
        ])->stream(str($drawing->title)->slug().'.pdf');
    }
}
