@props([
    'name' => null,
    'rows' => 3,
])

@php
    $hasError = $name && $errors->has($name);
@endphp

<textarea
    @if ($name) name="{{ $name }}" id="{{ $attributes->get('id', $name) }}" @endif
    rows="{{ $rows }}"
    {{ $attributes->merge(['class' => 'block w-full rounded-lg border-0 py-2 px-3 text-sm text-slate-900 shadow-sm ring-1 ring-inset placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 ' . ($hasError ? 'ring-red-400' : 'ring-slate-300')]) }}
>{{ $slot }}</textarea>
