<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class DocumentPdfController extends Controller
{
    public function quotation(Quotation $quotation): Response
    {
        $this->authorize('view', $quotation);

        $quotation->loadMissing(['customer', 'items', 'creator']);

        return Pdf::loadView('pdf.quotation', ['quotation' => $quotation])
            ->stream("{$quotation->reference}.pdf");
    }

    public function invoice(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        $invoice->loadMissing(['customer', 'items', 'payments']);

        return Pdf::loadView('pdf.invoice', ['invoice' => $invoice])
            ->stream("{$invoice->reference}.pdf");
    }
}
