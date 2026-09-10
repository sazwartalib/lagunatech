<?php

namespace App\Livewire\Forms;

use App\Models\Invoice;
use App\Support\Settings;
use Illuminate\Validation\Rule;
use Livewire\Form;

class InvoiceForm extends Form
{
    public ?int $invoiceId = null;

    public ?int $customer_id = null;

    public ?int $project_id = null;

    public ?int $quotation_id = null;

    public string $title = '';

    public ?string $issue_date = null;

    public ?string $due_date = null;

    public string $discount_type = 'amount';

    public float $discount_value = 0;

    public float $tax_rate = 0;

    public string $terms = '';

    public string $notes = '';

    /** @var list<array{description: string, quantity: float|string, unit_price: float|string}> */
    public array $items = [];

    public function mountDefaults(): void
    {
        $settings = app(Settings::class);

        $this->issue_date = now()->toDateString();
        $this->due_date = now()->addDays((int) $settings->get('finance.invoice_due_days'))->toDateString();
        $this->terms = (string) $settings->get('finance.payment_terms');
        $this->items = [$this->blankItem()];
    }

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoiceId = $invoice->id;
        $this->customer_id = $invoice->customer_id;
        $this->project_id = $invoice->project_id;
        $this->quotation_id = $invoice->quotation_id;
        $this->title = (string) $invoice->title;
        $this->issue_date = $invoice->issue_date->toDateString();
        $this->due_date = $invoice->due_date->toDateString();
        $this->discount_type = $invoice->discount_type;
        $this->discount_value = (float) $invoice->discount_value;
        $this->tax_rate = (float) $invoice->tax_rate;
        $this->terms = (string) $invoice->terms;
        $this->notes = (string) $invoice->notes;
        $this->items = $invoice->items
            ->map(fn ($i) => [
                'description' => $i->description,
                'quantity' => (float) $i->quantity,
                'unit_price' => (float) $i->unit_price,
            ])->all() ?: [$this->blankItem()];
    }

    /**
     * @return array{description: string, quantity: float, unit_price: float}
     */
    public function blankItem(): array
    {
        return ['description' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function addItem(): void
    {
        $this->items[] = $this->blankItem();
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);

        if ($this->items === []) {
            $this->items = [$this->blankItem()];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', Rule::exists('customers', 'id')],
            'project_id' => ['nullable', Rule::exists('projects', 'id')],
            'quotation_id' => ['nullable', Rule::exists('quotations', 'id')],
            'title' => ['nullable', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'discount_type' => ['required', 'in:amount,percent'],
            'discount_value' => ['numeric', 'min:0'],
            'tax_rate' => ['numeric', 'min:0', 'max:100'],
            'terms' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array{data: array<string, mixed>, items: list<array<string, mixed>>}
     */
    public function validatedData(): array
    {
        $validated = $this->validate();
        $items = $validated['items'];
        unset($validated['items'], $validated['invoiceId']);

        return ['data' => $validated, 'items' => $items];
    }

    /**
     * @return array{subtotal: float, discount: float, tax: float, total: float}
     */
    public function livePreview(): array
    {
        $subtotal = collect($this->items)->sum(
            fn ($i) => round((float) ($i['quantity'] ?: 0) * (float) ($i['unit_price'] ?: 0), 2)
        );

        $discount = $this->discount_type === 'percent'
            ? $subtotal * ((float) $this->discount_value / 100)
            : min((float) $this->discount_value, $subtotal);
        $discount = round(max(0, $discount), 2);

        $taxable = max(0, $subtotal - $discount);
        $tax = round($taxable * ((float) $this->tax_rate / 100), 2);

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => $discount,
            'tax' => $tax,
            'total' => round($taxable + $tax, 2),
        ];
    }
}
