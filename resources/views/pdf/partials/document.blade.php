{{-- Shared print layout for quotation & invoice PDFs (dompdf-safe: tables only). --}}
@php use Illuminate\Support\Number; @endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1e293b; font-size: 12px; margin: 0; }
        .wrap { padding: 40px; }
        h1 { font-size: 22px; margin: 0 0 2px; color: #0f172a; }
        .muted { color: #64748b; }
        .right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 2px 0; }
        .items th { border-bottom: 2px solid #e2e8f0; padding: 8px 6px; text-align: left; font-size: 10px; text-transform: uppercase; color: #64748b; }
        .items td { border-bottom: 1px solid #f1f5f9; padding: 8px 6px; }
        .totals td { padding: 3px 0; }
        .totals .grand td { border-top: 2px solid #e2e8f0; padding-top: 6px; font-size: 14px; font-weight: bold; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; background: #eef4ff; color: #1836e1; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .terms { margin-top: 26px; border-top: 1px solid #f1f5f9; padding-top: 12px; }
        .brand-bar { height: 4px; background: #1f47f5; }
    </style>
</head>
<body>
<div class="brand-bar"></div>
<div class="wrap">
    <table>
        <tr>
            <td style="vertical-align: top;">
                <h1>{{ config('app.name') }}</h1>
                <div class="muted">Custom software · Web · Mobile · Technical services</div>
            </td>
            <td class="right" style="vertical-align: top;">
                <div style="font-size: 16px; font-weight: bold; text-transform: uppercase;">{{ $docType }}</div>
                <div class="muted">{{ $document->reference }}</div>
                <div style="margin-top: 4px;"><span class="badge">{{ $document->status->label() }}</span></div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 26px;">
        <tr>
            <td style="vertical-align: top; width: 55%;">
                <div class="muted" style="text-transform: uppercase; font-size: 10px;">Bill to</div>
                <div style="font-weight: bold; margin-top: 3px;">{{ $document->customer->company_name }}</div>
                <div>{{ $document->customer->contact_person }}</div>
                <div class="muted">{{ $document->customer->email }}</div>
                <div class="muted">{{ $document->customer->phone }}</div>
                @if ($document->customer->address)
                    <div class="muted">{{ $document->customer->address }}</div>
                @endif
            </td>
            <td style="vertical-align: top;">
                <table class="meta">
                    @foreach ($metaRows as $label => $value)
                        <tr><td class="muted">{{ $label }}</td><td class="right">{{ $value }}</td></tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    @if ($document->title)
        <p style="margin-top: 20px; font-weight: bold;">{{ $document->title }}</p>
    @endif

    <table class="items" style="margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 55%;">Description</th>
                <th class="right">Qty</th>
                <th class="right">Unit price</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($document->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="right">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                    <td class="right">{{ Number::format((float) $item->unit_price, 2) }}</td>
                    <td class="right">{{ Number::format((float) $item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top: 14px;">
        <tr>
            <td style="width: 60%;"></td>
            <td>
                <table class="totals">
                    <tr><td class="muted">Subtotal</td><td class="right">RM {{ Number::format((float) $document->subtotal, 2) }}</td></tr>
                    @if ((float) $document->discount_amount > 0)
                        <tr><td class="muted">Discount</td><td class="right">− RM {{ Number::format((float) $document->discount_amount, 2) }}</td></tr>
                    @endif
                    @if ((float) $document->tax_amount > 0)
                        <tr><td class="muted">Tax</td><td class="right">RM {{ Number::format((float) $document->tax_amount, 2) }}</td></tr>
                    @endif
                    <tr class="grand"><td>Total</td><td class="right">RM {{ Number::format((float) $document->total, 2) }}</td></tr>
                    @if ($showBalance)
                        <tr><td class="muted">Paid</td><td class="right">− RM {{ Number::format((float) $document->amount_paid, 2) }}</td></tr>
                        <tr class="grand"><td>Balance due</td><td class="right">RM {{ Number::format($document->outstanding, 2) }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    @if ($document->terms)
        <div class="terms">
            <div class="muted" style="text-transform: uppercase; font-size: 10px;">Terms &amp; conditions</div>
            <div style="margin-top: 4px; white-space: pre-line;">{{ $document->terms }}</div>
        </div>
    @endif

    <div class="muted" style="margin-top: 40px; font-size: 10px;">
        Generated by {{ config('app.name') }} on {{ now()->format('d M Y') }}.
    </div>
</div>
</body>
</html>
