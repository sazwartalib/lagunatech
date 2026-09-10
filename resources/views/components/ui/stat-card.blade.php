@props([
    'label' => '',
    'value' => '',
    'icon' => null,
    'hint' => null,
    'tone' => 'slate',
    'href' => null,
])

@php
    $tones = [
        'slate' => 'text-slate-400',
        'brand' => 'text-brand-500',
        'green' => 'text-green-500',
        'amber' => 'text-amber-500',
        'red' => 'text-red-500',
    ];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" wire:navigate @endif
    {{ $attributes->merge(['class' => 'block rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition ' . ($href ? 'hover:border-slate-300 hover:shadow' : '')]) }}
>
    <div class="flex items-start justify-between">
        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</p>
        @if ($icon)<span class="text-lg {{ $tones[$tone] ?? $tones['slate'] }}">{{ $icon }}</span>@endif
    </div>
    <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $value }}</p>
    @if ($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
</{{ $tag }}>
