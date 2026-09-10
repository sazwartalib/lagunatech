@props([
    'column',
    'sort' => null,
    'direction' => 'asc',
    'align' => 'left',
])

@php $isActive = $sort === $column; @endphp

<th scope="col" class="px-4 py-2.5 text-{{ $align }} text-xs font-semibold text-slate-500">
    <button type="button" wire:click="sortBy('{{ $column }}')"
            class="inline-flex items-center gap-1 hover:text-slate-800 {{ $align === 'right' ? 'flex-row-reverse' : '' }}">
        {{ $slot }}
        <span class="text-[10px] {{ $isActive ? 'text-brand-600' : 'text-slate-300' }}">
            {{ $isActive ? ($direction === 'asc' ? '▲' : '▼') : '↕' }}
        </span>
    </button>
</th>
