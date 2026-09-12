<x-layouts.guest title="Customer Portal">
    <div class="mb-8">
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Customer Portal</h2>
        <p class="mt-1 text-sm text-slate-500">Sign in to track your projects with {{ config('app.name') }}.</p>
    </div>

    <form method="POST" action="{{ route('portal.login.store') }}" class="space-y-4">
        @csrf
        <x-ui.field label="Email" name="email" required>
            <x-ui.input type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        </x-ui.field>
        <x-ui.field label="Password" name="password" required>
            <x-ui.input type="password" name="password" required autocomplete="current-password" />
        </x-ui.field>
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600"> Remember me
            </label>
            <a href="{{ route('portal.password.request') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Forgot password?</a>
        </div>
        <x-ui.button type="submit" size="lg" class="w-full">Sign in</x-ui.button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-400">
        Staff member? <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-700">Sign in here</a>.
    </p>
</x-layouts.guest>
