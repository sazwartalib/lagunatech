@include('pdf.partials.document', [
    'docType' => 'Quotation',
    'document' => $quotation,
    'showBalance' => false,
    'metaRows' => [
        'Issue date' => $quotation->issue_date->format('d M Y'),
        'Valid until' => $quotation->valid_until?->format('d M Y') ?? '—',
        'Prepared by' => $quotation->creator?->name ?? '—',
    ],
])
