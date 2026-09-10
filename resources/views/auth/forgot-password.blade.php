<x-layouts.guest title="Reset password">
    <div class="mb-8">
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Forgot your password?</h2>
        <p class="mt-1 text-sm text-slate-500">Enter your email and we'll send you a reset link.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700 ring-1 ring-inset ring-green-600/20">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <x-ui.field label="Email" name="email" required>
            <x-ui.input type="email" name="email" :value="old('email')" required autofocus placeholder="you@lagunatech.com" />
        </x-ui.field>

        <x-ui.button type="submit" size="lg" class="w-full">Email reset link</x-ui.button>

        <p class="text-center text-sm text-slate-500">
            <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-700">Back to sign in</a>
        </p>
    </form>
</x-layouts.guest>
