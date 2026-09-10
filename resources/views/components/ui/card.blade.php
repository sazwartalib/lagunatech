@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
    'flush' => false,
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white shadow-sm']) }}>
    @if ($title || isset($actions))
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 sm:px-5">
            <div class="min-w-0">
                @if ($title)<h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>@endif
                @if ($subtitle)<p class="mt-0.5 text-xs text-slate-500">{{ $subtitle }}</p>@endif
            </div>
            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div @class(['px-4 py-4 sm:px-5' => $padding && ! $flush, 'p-0' => $flush])>
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-slate-100 px-4 py-3 sm:px-5">{{ $footer }}</div>
    @endisset
</div>
