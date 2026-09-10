@props([
    'color' => 'slate',
    'size' => 'sm',
    'dot' => false,
])

@php
    $palette = [
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-600/10',
        'brand' => 'bg-brand-50 text-brand-700 ring-brand-600/20',
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        'purple' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
        'green' => 'bg-green-50 text-green-700 ring-green-600/20',
        'teal' => 'bg-teal-50 text-teal-700 ring-teal-600/20',
        'cyan' => 'bg-cyan-50 text-cyan-700 ring-cyan-600/20',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
        'red' => 'bg-red-50 text-red-700 ring-red-600/20',
    ];

    $sizes = [
        'xs' => 'px-1.5 py-0.5 text-[10px]',
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
    ];

    $dotColor = [
        'slate' => 'bg-slate-400', 'brand' => 'bg-brand-500', 'blue' => 'bg-blue-500',
        'indigo' => 'bg-indigo-500', 'purple' => 'bg-purple-500', 'green' => 'bg-green-500',
        'teal' => 'bg-teal-500', 'cyan' => 'bg-cyan-500', 'amber' => 'bg-amber-500', 'red' => 'bg-red-500',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full font-medium ring-1 ring-inset ' . ($palette[$color] ?? $palette['slate']) . ' ' . ($sizes[$size] ?? $sizes['sm'])]) }}>
    @if ($dot)
        <span class="size-1.5 rounded-full {{ $dotColor[$color] ?? $dotColor['slate'] }}"></span>
    @endif
    {{ $slot }}
</span>
