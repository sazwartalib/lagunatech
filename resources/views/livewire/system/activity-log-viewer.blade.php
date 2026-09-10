<div>
    <x-ui.page-header title="Activity Logs" subtitle="A record of important actions across the system." />

    <div class="mb-4 flex flex-wrap gap-2">
        <select wire:model.live="subjectType" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <option value="">All record types</option>
            @foreach ($subjectTypes as $type)<option value="{{ $type }}">{{ $type }}</option>@endforeach
        </select>
        <select wire:model.live="causerId" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <option value="">Anyone</option>
            @foreach ($causers as $causer)<option value="{{ $causer->id }}">{{ $causer->name }}</option>@endforeach
        </select>
    </div>

    <x-ui.card flush>
        <ol class="divide-y divide-slate-100">
            @forelse ($entries as $entry)
                <li class="flex gap-3 px-4 py-3" wire:key="al-{{ $entry->id }}">
                    <x-ui.avatar :name="$entry->causer?->name ?? 'System'" size="xs" class="mt-0.5" />
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-700">
                            <span class="font-medium text-slate-900">{{ $entry->causer?->name ?? 'System' }}</span>
                            {{ $entry->description }}
                            @if ($entry->subject_type)
                                <span class="text-slate-500">{{ class_basename($entry->subject_type) }} #{{ $entry->subject_id }}</span>
                            @endif
                        </p>
                        @if (! empty($entry->properties['attributes']))
                            <p class="mt-0.5 truncate text-xs text-slate-400">
                                {{ collect($entry->properties['attributes'])->map(fn ($v, $k) => "$k: ".(is_scalar($v) ? $v : json_encode($v)))->implode(' · ') }}
                            </p>
                        @endif
                        <p class="text-xs text-slate-400">{{ $entry->created_at->format('d M Y, g:ia') }} · {{ $entry->created_at->diffForHumans() }}</p>
                    </div>
                </li>
            @empty
                <li class="p-6"><x-ui.empty-state icon="📜" title="No activity" description="Nothing matches these filters." /></li>
            @endforelse
        </ol>
        @if ($entries->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $entries->links() }}</div>
        @endif
    </x-ui.card>
</div>
