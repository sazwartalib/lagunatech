<div>
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $documents->flatten()->count() }} {{ Str::plural('document', $documents->flatten()->count()) }}</p>
        @can('create', App\Models\Document::class)
            <x-ui.button size="sm" wire:click="$toggle('showUpload')" icon="⬆">Upload</x-ui.button>
        @endcan
    </div>

    @if ($showUpload)
        <x-ui.card class="mb-4">
            <form wire:submit="upload" class="space-y-3">
                <x-ui.field label="File" name="file" required hint="Up to 20 MB.">
                    <input type="file" wire:model="file" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-brand-700">
                    <div wire:loading wire:target="file" class="mt-1 text-xs text-slate-400">Uploading…</div>
                </x-ui.field>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-ui.field label="Title" name="docTitle">
                        <x-ui.input wire:model="docTitle" placeholder="Defaults to the file name" />
                    </x-ui.field>
                    <x-ui.field label="Category" name="category" required>
                        <x-ui.select wire:model="category" :options="App\Enums\DocumentCategory::options()" />
                    </x-ui.field>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" wire:model="isInternal" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600">
                    Internal only — hide from the customer portal
                </label>
                <div class="flex justify-end gap-2">
                    <x-ui.button type="button" variant="secondary" wire:click="$set('showUpload', false)">Cancel</x-ui.button>
                    <x-ui.button type="submit">Upload</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    @endif

    @forelse ($categories as $cat)
        @php $items = $documents[$cat->value] ?? collect(); @endphp
        @if ($items->isNotEmpty())
            <div class="mb-4" wire:key="cat-{{ $cat->value }}">
                <p class="mb-1.5 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                    <x-ui.badge :color="$cat->color()" size="xs">{{ $cat->label() }}</x-ui.badge>
                </p>
                <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white">
                    @foreach ($items as $doc)
                        <div class="flex items-center gap-3 px-4 py-2.5" wire:key="doc-{{ $doc->id }}">
                            <span class="text-lg">📄</span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('documents.download', $doc) }}"
                                   class="truncate text-sm font-medium text-brand-700 hover:underline">{{ $doc->title }}</a>
                                <p class="text-xs text-slate-400">
                                    {{ $doc->human_size }} · {{ $doc->uploader?->name ?? 'system' }} · {{ $doc->created_at->format('d M Y') }}
                                    @unless ($doc->is_internal)<span class="text-green-600"> · shared</span>@endunless
                                </p>
                            </div>
                            @can('delete', $doc)
                                <button wire:click="deleteDocument({{ $doc->id }})" wire:confirm="Delete this document?"
                                        class="text-slate-300 hover:text-red-500" title="Delete">✕</button>
                            @endcan
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @empty
    @endforelse

    @if ($documents->flatten()->isEmpty())
        <x-ui.empty-state icon="📄" title="No documents" description="Upload requirements, designs, contracts and deployment notes here." />
    @endif
</div>
