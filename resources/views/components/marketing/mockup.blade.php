@props(['tone' => 'cyan'])

@php
    $tones = [
        'rose' => ['accent' => 'bg-rose-400/70', 'accent2' => 'bg-fuchsia-400/50', 'ring' => 'ring-rose-400/20'],
        'amber' => ['accent' => 'bg-amber-400/70', 'accent2' => 'bg-orange-400/50', 'ring' => 'ring-amber-400/20'],
        'cyan' => ['accent' => 'bg-cyan-400/70', 'accent2' => 'bg-blue-400/50', 'ring' => 'ring-cyan-400/20'],
    ];
    $t = $tones[$tone] ?? $tones['cyan'];
@endphp

{{-- Abstract UI mockup — no fake screenshots, just a stylised representation of a product interface. --}}
<div {{ $attributes->merge(['class' => "relative w-[86%] overflow-hidden rounded-xl border border-white/10 bg-white/[0.03] shadow-2xl shadow-black/40 ring-1 {$t['ring']} backdrop-blur"]) }}>
    <div class="flex items-center gap-1.5 border-b border-white/10 bg-white/[0.02] px-3 py-2">
        <span class="size-2 rounded-full bg-white/15"></span>
        <span class="size-2 rounded-full bg-white/15"></span>
        <span class="size-2 rounded-full bg-white/15"></span>
    </div>
    <div class="flex gap-3 p-4">
        <div class="flex w-8 shrink-0 flex-col gap-2">
            <div class="h-2 w-full rounded {{ $t['accent'] }}"></div>
            <div class="h-2 w-full rounded bg-white/10"></div>
            <div class="h-2 w-full rounded bg-white/10"></div>
            <div class="h-2 w-full rounded bg-white/10"></div>
        </div>
        <div class="flex-1 space-y-2">
            <div class="flex gap-2">
                <div class="h-12 flex-1 rounded-lg {{ $t['accent2'] }}"></div>
                <div class="h-12 w-12 rounded-lg bg-white/10"></div>
            </div>
            <div class="h-2 w-4/5 rounded bg-white/15"></div>
            <div class="h-2 w-3/5 rounded bg-white/10"></div>
            <div class="mt-3 flex gap-2">
                <div class="h-6 flex-1 rounded bg-white/[0.06]"></div>
                <div class="h-6 flex-1 rounded bg-white/[0.06]"></div>
                <div class="h-6 flex-1 rounded {{ $t['accent'] }}"></div>
            </div>
        </div>
    </div>
</div>
