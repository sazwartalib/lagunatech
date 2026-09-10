<div>
    <h1 class="mb-6 text-xl font-semibold tracking-tight text-slate-900">Projects</h1>

    <div class="space-y-3">
        @forelse ($projects as $project)
            <a href="{{ route('portal.projects.show', $project) }}" wire:navigate wire:key="p-{{ $project->id }}"
               class="block rounded-xl border border-slate-200 bg-white p-4 hover:border-slate-300">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $project->name }}</p>
                        <p class="text-xs text-slate-400">{{ $project->type }} · Due {{ $project->target_end_date?->format('d M Y') ?? 'TBC' }}</p>
                    </div>
                    <x-ui.badge :color="$project->status->color()" size="xs">{{ $project->status->label() }}</x-ui.badge>
                </div>
                <x-ui.progress :value="$project->progress" show-label size="md" class="mt-3" />
            </a>
        @empty
            <x-ui.empty-state icon="📁" title="No projects" description="Your projects will appear here." />
        @endforelse
    </div>
</div>
