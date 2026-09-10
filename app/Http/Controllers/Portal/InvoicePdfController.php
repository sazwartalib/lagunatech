<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InvoicePdfController extends Controller
{
    public function __invoke(Invoice $invoice): Response
    {
        $customer = Auth::guard('customer')->user()->customer;

        abort_unless($invoice->customer_id === $customer->id, 404);
        abort_if($invoice->status->value === 'draft', 404);

        $invoice->loadMissing(['customer', 'items']);

        return Pdf::loadView('pdf.invoice', ['invoice' => $invoice])
            ->stream("{$invoice->reference}.pdf");
    }
}
