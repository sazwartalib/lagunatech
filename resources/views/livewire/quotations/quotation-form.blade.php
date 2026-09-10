<div class="mx-auto max-w-3xl">
    <x-ui.page-header :title="$quotation ? 'Edit quotation' : 'New quotation'" :subtitle="$quotation?->reference">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="$quotation ? route('quotations.show', $quotation) : route('quotations.index')">Cancel</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form wire:submit="save" class="space-y-5">
        <x-ui.card title="Details">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ui.field label="Customer" name="form.customer_id" required>
                    <x-ui.select wire:model="form.customer_id" placeholder="Select customer">
                        @foreach ($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->company_name }}</option>@endforeach
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Title" name="form.title">
                    <x-ui.input wire:model="form.title" placeholder="ABC POS System — Proposal" />
                </x-ui.field>
                <x-ui.field label="Issue date" name="form.issue_date" required>
                    <x-ui.input type="date" wire:model="form.issue_date" />
                </x-ui.field>
                <x-ui.field label="Valid until" name="form.valid_until">
                    <x-ui.input type="date" wire:model="form.valid_until" />
                </x-ui.field>
            </div>
        </x-ui.card>

        <x-ui.card title="Line items">
            @include('partials.document-line-items', ['prefix' => 'form', 'items' => $form->items, 'preview' => $preview])
        </x-ui.card>

        <x-ui.card title="Terms & notes">
            <div class="space-y-4">
                <x-ui.field label="Terms & conditions" name="form.terms">
                    <x-ui.textarea wire:model="form.terms" rows="3" />
                </x-ui.field>
                <x-ui.field label="Internal notes" name="form.notes" hint="Not shown on the PDF.">
                    <x-ui.textarea wire:model="form.notes" rows="2" />
                </x-ui.field>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-2">
                    <x-ui.button variant="secondary" :href="$quotation ? route('quotations.show', $quotation) : route('quotations.index')">Cancel</x-ui.button>
                    <x-ui.button type="submit">
                        <span wire:loading.remove wire:target="save">{{ $quotation ? 'Save changes' : 'Create quotation' }}</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </form>
</div>
