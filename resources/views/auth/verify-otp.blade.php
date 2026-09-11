<x-layouts.guest title="Enter your code">
    <div class="mb-8">
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Check your email</h2>
        <p class="mt-1 text-sm text-slate-500">
            Enter the 6-digit code we sent to your email address. It expires in {{ $ttlMinutes }} minutes.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700 ring-1 ring-inset ring-green-600/20">
            {{ session('status') }}
        </div>
    @endif

    @error('code')
        <div class="mb-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700 ring-1 ring-inset ring-red-600/20">
            {{ $message }}
        </div>
    @enderror

    <form
        method="POST"
        action="{{ route('login.otp.verify') }}"
        x-data="{ submitting: false }"
        @submit="submitting = true"
        class="space-y-6"
    >
        @csrf

        <x-ui.otp-input name="code" :length="6" />

        <label class="flex items-center justify-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="trust_device" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600">
            Trust this device for {{ config('otp.trust_device_days') }} days
        </label>

        <x-ui.button type="submit" size="lg" class="w-full" x-bind:disabled="submitting">
            <span x-show="!submitting">Verify and sign in</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                <x-ui.spinner />
                Verifying…
            </span>
        </x-ui.button>
    </form>

    <div
        x-data="{ seconds: 60 }"
        x-init="const tick = setInterval(() => { if (seconds > 0) seconds--; else clearInterval(tick); }, 1000)"
        class="mt-6 text-center text-sm text-slate-500"
    >
        <span x-show="seconds > 0">Resend available in <span x-text="seconds"></span>s</span>

        <form x-show="seconds === 0" x-cloak method="POST" action="{{ route('login.otp.resend') }}">
            @csrf
            <button type="submit" class="font-medium text-brand-600 hover:text-brand-700">Resend code</button>
        </form>
    </div>
</x-layouts.guest>
