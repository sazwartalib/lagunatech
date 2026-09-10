{{--
    Global toast host. Livewire components fire:  $this->dispatch('toast', message: '...', type: 'success')
    Server flashes ('status' / 'error') are surfaced on first paint too.
--}}
<div
    x-data="{
        toasts: [],
        push(detail) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type: detail.type || 'success', message: detail.message });
            setTimeout(() => this.dismiss(id), detail.timeout || 4000);
        },
        dismiss(id) { this.toasts = this.toasts.filter(t => t.id !== id); },
    }"
    x-on:toast.window="push($event.detail)"
    @if (session('status')) x-init="push({ type: 'success', message: @js(session('status')) })"
    @elseif (session('error')) x-init="push({ type: 'error', message: @js(session('error')) })" @endif
    class="pointer-events-none fixed inset-x-0 top-4 z-[60] flex flex-col items-center gap-2 px-4"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition
            class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border bg-white px-4 py-3 shadow-lg"
            :class="{
                'border-green-200': toast.type === 'success',
                'border-red-200': toast.type === 'error',
                'border-slate-200': toast.type === 'info',
            }"
        >
            <span class="mt-0.5 text-sm"
                  x-text="toast.type === 'success' ? '✓' : (toast.type === 'error' ? '✕' : 'ℹ')"
                  :class="{
                      'text-green-600': toast.type === 'success',
                      'text-red-600': toast.type === 'error',
                      'text-slate-500': toast.type === 'info',
                  }"></span>
            <p class="flex-1 text-sm text-slate-700" x-text="toast.message"></p>
            <button type="button" class="text-slate-300 hover:text-slate-500" @click="dismiss(toast.id)">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
    </template>
</div>
