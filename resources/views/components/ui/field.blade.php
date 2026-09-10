@props([
    'label' => null,
    'name' => null,
    'hint' => null,
    'required' => false,
])

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    @if ($label)
        <label @if ($name) for="{{ $name }}" @endif class="block text-sm font-medium text-slate-700">
            {{ $label }}
            @if ($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    {{ $slot }}

    @if ($hint)
        <p class="text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @if ($name)
        @error($name)
            <p class="text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    @endif
</div>
