@include('pdf.partials.document', [
    'docType' => 'Invoice',
    'document' => $invoice,
    'showBalance' => true,
    'metaRows' => [
        'Issue date' => $invoice->issue_date->format('d M Y'),
        'Due date' => $invoice->due_date->format('d M Y'),
        'Reference' => $invoice->reference,
    ],
])
