@props([
    'type' => 'text',
    'name' => null,
])

@php
    $hasError = $name && $errors->has($name);
    $baseClass = 'block w-full rounded-lg border-0 py-2 text-sm text-slate-900 shadow-sm ring-1 ring-inset placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 ' . ($hasError ? 'ring-red-400' : 'ring-slate-300');
@endphp

@if ($type === 'password')
    <div x-data="{ show: false }" class="relative">
        <input
            :type="show ? 'text' : 'password'"
            @if ($name) name="{{ $name }}" id="{{ $attributes->get('id', $name) }}" @endif
            {{ $attributes->merge(['class' => $baseClass . ' px-3 pr-10']) }}
        />
        <button type="button" @click="show = !show" tabindex="-1"
                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 transition hover:text-slate-600"
                :aria-label="show ? 'Hide password' : 'Show password'">
            <svg x-show="!show" class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            </svg>
            <svg x-show="show" x-cloak class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
            </svg>
        </button>
    </div>
@else
    <input
        type="{{ $type }}"
        @if ($name) name="{{ $name }}" id="{{ $attributes->get('id', $name) }}" @endif
        {{ $attributes->merge(['class' => $baseClass . ' px-3']) }}
    />
@endif
