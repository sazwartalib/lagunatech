@props([
    'name' => null,
    'options' => [],
    'placeholder' => null,
])

@php
    $hasError = $name && $errors->has($name);
@endphp

<select
    @if ($name) name="{{ $name }}" id="{{ $attributes->get('id', $name) }}" @endif
    {{ $attributes->merge(['class' => 'block w-full rounded-lg border-0 py-2 pl-3 pr-9 text-sm text-slate-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-brand-600 ' . ($hasError ? 'ring-red-400' : 'ring-slate-300')]) }}
>
    @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif

    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        @foreach ($options as $optValue => $optLabel)
            <option value="{{ $optValue }}">{{ $optLabel }}</option>
        @endforeach
    @endif
</select>
