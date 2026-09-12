<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Thin, cached accessor for the `settings` table. Values are JSON-cast, so
 * scalars, arrays and nested structures all round-trip.
 */
class Settings
{
    private const CACHE_KEY = 'app.settings';

    /**
     * @var array<string, mixed>
     */
    public const DEFAULTS = [
        'company.name' => 'Laguna Tech',
        'company.registration_no' => '202603101118 (TR0340857-X)',
        'company.address' => '',
        'company.email' => 'hello@lagunatech.my',
        'company.phone' => '',
        'company.bank_details' => "Bank: Maybank\nAccount Name: Laguna Tech\nAccount No.: 552189625040",
        'finance.default_tax_rate' => 0,
        'finance.quotation_validity_days' => 30,
        'finance.invoice_due_days' => 30,
        'finance.currency' => 'RM',
        'finance.payment_terms' => self::DEFAULT_PAYMENT_TERMS,
    ];

    /**
     * Standard payment terms printed on quotations and invoices. Editable in
     * System → Settings; this constant is only the fallback.
     */
    public const DEFAULT_PAYMENT_TERMS = <<<'TERMS'
        PAYMENT TERMS

        1. 50% deposit is required upon confirmation of the project before development work commences.
        2. The remaining 50% balance is payable upon project completion and prior to final deployment/handover.
        3. All payments shall be made to:
           Company: Laguna Tech
           Registration No.: 202603101118 (TR0340857-X)
           Bank: Maybank
           Account No.: 552189625040
        4. Any additional features, modifications, or requirements outside the agreed project scope will be quoted separately and are subject to approval.
        5. Project delivery timelines commence upon receipt of the required deposit and all necessary information/materials from the client.
        6. All payments made are non-refundable once development work has commenced.
        TERMS;

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? self::DEFAULTS[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): array => Setting::query()->pluck('value', 'key')->all());
    }

    public function set(string $key, mixed $value): void
    {
        Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        $this->flush();
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
