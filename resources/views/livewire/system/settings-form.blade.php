<div class="mx-auto max-w-2xl">
    <x-ui.page-header title="Settings" subtitle="Company details and finance defaults used across documents." />

    <form wire:submit="save" class="space-y-5">
        <x-ui.card title="Company">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ui.field label="Company name" name="companyName" required>
                    <x-ui.input wire:model="companyName" />
                </x-ui.field>
                <x-ui.field label="Registration no." name="companyRegistrationNo">
                    <x-ui.input wire:model="companyRegistrationNo" />
                </x-ui.field>
                <x-ui.field label="Email" name="companyEmail">
                    <x-ui.input type="email" wire:model="companyEmail" />
                </x-ui.field>
                <x-ui.field label="Phone" name="companyPhone">
                    <x-ui.input wire:model="companyPhone" />
                </x-ui.field>
                <div class="sm:col-span-2">
                    <x-ui.field label="Address" name="companyAddress">
                        <x-ui.textarea wire:model="companyAddress" rows="2" />
                    </x-ui.field>
                </div>
                <div class="sm:col-span-2">
                    <x-ui.field label="Bank details" name="companyBankDetails" hint="Shown on invoices.">
                        <x-ui.textarea wire:model="companyBankDetails" rows="2" />
                    </x-ui.field>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Finance defaults">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ui.field label="Currency" name="currency" required>
                    <x-ui.input wire:model="currency" />
                </x-ui.field>
                <x-ui.field label="Default tax rate (%)" name="defaultTaxRate">
                    <x-ui.input type="number" step="0.01" wire:model="defaultTaxRate" />
                </x-ui.field>
                <x-ui.field label="Quotation validity (days)" name="quotationValidityDays">
                    <x-ui.input type="number" wire:model="quotationValidityDays" />
                </x-ui.field>
                <x-ui.field label="Invoice due (days)" name="invoiceDueDays">
                    <x-ui.input type="number" wire:model="invoiceDueDays" />
                </x-ui.field>
                <div class="sm:col-span-2">
                    <x-ui.field label="Payment terms" name="paymentTerms"
                                hint="Pre-filled into the Terms field of every new quotation and invoice.">
                        <x-ui.textarea wire:model="paymentTerms" rows="12" class="font-mono text-xs" />
                    </x-ui.field>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex justify-end">
                    <x-ui.button type="submit">Save settings</x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </form>
</div>
