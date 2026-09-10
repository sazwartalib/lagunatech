@props([
    'value' => 0,
    'color' => 'brand',
    'showLabel' => false,
    'size' => 'md',
])

@php
    $pct = max(0, min(100, (int) $value));
    $bar = [
        'brand' => 'bg-brand-500', 'green' => 'bg-green-500', 'amber' => 'bg-amber-500',
        'red' => 'bg-red-500', 'slate' => 'bg-slate-400', 'blue' => 'bg-blue-500',
    ];
    $h = ['sm' => 'h-1', 'md' => 'h-1.5', 'lg' => 'h-2.5'][$size] ?? 'h-1.5';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <div class="w-full overflow-hidden rounded-full bg-slate-100 {{ $h }}">
        <div class="{{ $h }} rounded-full {{ $bar[$color] ?? $bar['brand'] }} transition-all" style="width: {{ $pct }}%"></div>
    </div>
    @if ($showLabel)
        <span class="w-9 shrink-0 text-right text-xs font-medium tabular-nums text-slate-500">{{ $pct }}%</span>
    @endif
</div>
