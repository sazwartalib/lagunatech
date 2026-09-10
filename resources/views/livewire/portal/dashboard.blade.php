@php use Illuminate\Support\Number; @endphp

<div>
    <h1 class="text-xl font-semibold tracking-tight text-slate-900">Welcome, {{ \Illuminate\Support\Str::of(auth('customer')->user()->name)->explode(' ')->first() }}</h1>
    <p class="mt-1 text-sm text-slate-500">Here's where your work with {{ config('app.name') }} stands.</p>

    <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <x-ui.stat-card label="Projects" :value="$customer->projects()->count()" icon="📁" :href="route('portal.projects')" />
        <x-ui.stat-card label="Quotations to review" :value="$pendingQuotations" icon="📝" tone="brand" :href="route('portal.quotations')" />
        <x-ui.stat-card label="Amount due" :value="'RM ' . Number::format($outstanding)" icon="⏳" tone="amber" :href="route('portal.invoices')" />
        <x-ui.stat-card label="Open tickets" :value="$openTickets" icon="🎫" :href="route('portal.tickets')" />
    </div>

    <x-ui.card title="Your projects" class="mt-6">
        <x-slot:actions><x-ui.button variant="ghost" size="sm" href="{{ route('portal.projects') }}">View all</x-ui.button></x-slot:actions>
        @forelse ($projects as $project)
            <a href="{{ route('portal.projects.show', $project) }}" wire:navigate wire:key="pp-{{ $project->id }}"
               class="-mx-2 flex items-center gap-4 rounded-lg px-2 py-2.5 hover:bg-slate-50">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-slate-800">{{ $project->name }}</p>
                    <p class="text-xs text-slate-400">Due {{ $project->target_end_date?->format('d M Y') ?? 'TBC' }}</p>
                </div>
                <x-ui.badge :color="$project->status->color()" size="xs">{{ $project->status->label() }}</x-ui.badge>
                <div class="hidden w-32 sm:block"><x-ui.progress :value="$project->progress" show-label size="sm" /></div>
            </a>
        @empty
            <x-ui.empty-state icon="📁" title="No projects yet" description="Your projects will appear here once work begins." />
        @endforelse
    </x-ui.card>
</div>
