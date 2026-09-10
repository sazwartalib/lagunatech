@php
    use App\Enums\ProjectStatus;
    use App\Enums\Priority;
@endphp

<div class="mx-auto max-w-3xl">
    <x-ui.page-header :title="$project ? 'Edit project' : 'New project'" :subtitle="$project?->reference">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="$project ? route('projects.show', $project) : route('projects.index')">Cancel</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form wire:submit="save" class="space-y-5">
        <x-ui.card title="Basics">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-ui.field label="Project name" name="form.name" required>
                        <x-ui.input wire:model="form.name" placeholder="ABC POS System" />
                    </x-ui.field>
                </div>

                <x-ui.field label="Customer" name="form.customer_id" required>
                    <x-ui.select wire:model="form.customer_id" placeholder="Select customer">
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Internal PIC" name="form.lead_id">
                    <x-ui.select wire:model="form.lead_id" placeholder="Unassigned">
                        @foreach ($staff as $person)
                            <option value="{{ $person->id }}">{{ $person->name }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Customer PIC name" name="form.customer_pic_name">
                    <x-ui.input wire:model="form.customer_pic_name" />
                </x-ui.field>

                <x-ui.field label="Customer PIC phone" name="form.customer_pic_phone">
                    <x-ui.input wire:model="form.customer_pic_phone" />
                </x-ui.field>

                <x-ui.field label="Project type" name="form.type">
                    <x-ui.input wire:model="form.type" placeholder="Web Application" list="project-types" />
                    <datalist id="project-types">
                        <option>Web Application</option><option>Mobile Application</option>
                        <option>Custom System</option><option>Website</option><option>Technical Service</option>
                    </datalist>
                </x-ui.field>

                <x-ui.field label="Technology" name="form.technology">
                    <x-ui.input wire:model="form.technology" placeholder="Laravel + Livewire" />
                </x-ui.field>

                <div class="sm:col-span-2">
                    <x-ui.field label="Description" name="form.description">
                        <x-ui.textarea wire:model="form.description" rows="3" />
                    </x-ui.field>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Delivery">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ui.field label="Status" name="form.status" required>
                    <x-ui.select wire:model="form.status" :options="ProjectStatus::options()" />
                </x-ui.field>
                <x-ui.field label="Priority" name="form.priority" required>
                    <x-ui.select wire:model="form.priority" :options="Priority::options()" />
                </x-ui.field>
                <x-ui.field label="Start date" name="form.start_date">
                    <x-ui.input type="date" wire:model="form.start_date" />
                </x-ui.field>
                <x-ui.field label="Target completion" name="form.target_end_date">
                    <x-ui.input type="date" wire:model="form.target_end_date" />
                </x-ui.field>
                <div class="sm:col-span-2">
                    <x-ui.field label="Progress ({{ $form->progress }}%)" name="form.progress">
                        <input type="range" min="0" max="100" step="5" wire:model.live="form.progress" class="w-full accent-brand-600">
                    </x-ui.field>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Commercials & team">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ui.field label="Budget (RM)" name="form.budget" hint="Internal cost estimate.">
                    <x-ui.input type="number" step="0.01" wire:model="form.budget" />
                </x-ui.field>
                <x-ui.field label="Project value (RM)" name="form.value" hint="What the customer is billed.">
                    <x-ui.input type="number" step="0.01" wire:model="form.value" />
                </x-ui.field>
                <div class="sm:col-span-2">
                    <x-ui.field label="Team members" name="form.member_ids">
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach ($staff as $person)
                                <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm">
                                    <input type="checkbox" value="{{ $person->id }}" wire:model="form.member_ids"
                                           class="rounded border-slate-300 text-brand-600 focus:ring-brand-600">
                                    <span class="truncate">{{ $person->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </x-ui.field>
                </div>
                <div class="sm:col-span-2">
                    <x-ui.field label="Notes" name="form.notes" hint="Internal — never shown to the customer.">
                        <x-ui.textarea wire:model="form.notes" rows="3" />
                    </x-ui.field>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-2">
                    <x-ui.button variant="secondary" :href="$project ? route('projects.show', $project) : route('projects.index')">Cancel</x-ui.button>
                    <x-ui.button type="submit">
                        <span wire:loading.remove wire:target="save">{{ $project ? 'Save changes' : 'Create project' }}</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </form>
</div>
