@props([
    'name' => '?',
    'src' => null,
    'size' => 'md',
])

@php
    $sizes = [
        'xs' => 'size-6 text-[10px]',
        'sm' => 'size-7 text-xs',
        'md' => 'size-9 text-sm',
        'lg' => 'size-12 text-base',
    ];

    $initials = collect(explode(' ', trim((string) $name)))
        ->filter()
        ->take(2)
        ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
        ->implode('') ?: '?';

    // Deterministic colour from the name.
    $tones = ['bg-brand-600', 'bg-indigo-600', 'bg-teal-600', 'bg-amber-600', 'bg-rose-600', 'bg-cyan-600', 'bg-violet-600'];
    $tone = $tones[crc32((string) $name) % count($tones)];
@endphp

<span {{ $attributes->merge(['class' => 'inline-grid shrink-0 place-items-center overflow-hidden rounded-full font-semibold text-white ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($src ? '' : $tone)]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $name }}" class="size-full object-cover">
    @else
        {{ $initials }}
    @endif
</span>
