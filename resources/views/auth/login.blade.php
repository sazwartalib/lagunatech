<x-layouts.guest title="Sign in">
    <div class="mb-8">
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Welcome back</h2>
        <p class="mt-1 text-sm text-slate-500">Sign in to your {{ config('app.name') }} account.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700 ring-1 ring-inset ring-green-600/20">
            {{ session('status') }}
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('login.store') }}"
        x-data="{ submitting: false }"
        @submit="submitting = true"
        class="space-y-4"
    >
        @csrf

        <x-ui.field label="Email" name="email" required>
            <x-ui.input type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@lagunatech.com" />
        </x-ui.field>

        <x-ui.field label="Password" name="password" required>
            <x-ui.input type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
        </x-ui.field>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600">
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                    Forgot password?
                </a>
            @endif
        </div>

        <x-ui.button type="submit" size="lg" class="w-full" x-bind:disabled="submitting">
            <span x-show="!submitting">Sign in</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                <x-ui.spinner />
                Signing in…
            </span>
        </x-ui.button>
    </form>
</x-layouts.guest>
