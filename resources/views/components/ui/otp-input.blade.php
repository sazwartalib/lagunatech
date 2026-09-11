@props([
    'name' => 'code',
    'length' => 6,
])

<div
    x-data="{
        digits: Array({{ $length }}).fill(''),
        get code() { return this.digits.join(''); },
        inputs() { return Array.from(this.$root.querySelectorAll('[data-digit]')); },
        focusAt(i) {
            const input = this.inputs()[i];
            if (input) { input.focus(); input.select(); }
        },
        onInput(i, e) {
            const value = e.target.value.replace(/\D/g, '').slice(-1);
            this.digits[i] = value;
            e.target.value = value;
            if (value && i < {{ $length - 1 }}) this.focusAt(i + 1);
        },
        onKeydown(i, e) {
            if (e.key === 'Backspace' && !this.digits[i] && i > 0) {
                this.focusAt(i - 1);
            }
        },
        onPaste(e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, {{ $length }});
            if (!pasted) return;
            this.digits = pasted.split('').concat(Array({{ $length }}).fill('')).slice(0, {{ $length }});
            this.inputs().forEach((input, i) => { input.value = this.digits[i]; });
            this.$nextTick(() => this.focusAt(Math.min(pasted.length, {{ $length - 1 }})));
        },
    }"
    x-init="$nextTick(() => focusAt(0))"
    class="flex justify-center gap-2 sm:gap-3"
>
    @for ($i = 0; $i < $length; $i++)
        <input
            type="text"
            data-digit
            inputmode="numeric"
            autocomplete="one-time-code"
            maxlength="1"
            @input="onInput({{ $i }}, $event)"
            @keydown="onKeydown({{ $i }}, $event)"
            @paste="onPaste($event)"
            class="size-11 rounded-lg border-0 text-center text-lg font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600 sm:size-12"
        >
    @endfor

    <input type="hidden" {{ $attributes->merge(['name' => $name]) }} :value="code">
</div>
