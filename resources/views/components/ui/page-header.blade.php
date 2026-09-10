@props([
    'title' => '',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div class="min-w-0">
        @isset($breadcrumbs)
            <nav class="mb-1 flex items-center gap-1.5 text-xs text-slate-400">{{ $breadcrumbs }}</nav>
        @endisset
        <h1 class="text-xl font-semibold tracking-tight text-slate-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
