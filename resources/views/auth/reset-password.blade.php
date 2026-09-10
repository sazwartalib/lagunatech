<x-layouts.guest title="Set a new password">
    <div class="mb-8">
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Set a new password</h2>
        <p class="mt-1 text-sm text-slate-500">Choose a strong password you don't use elsewhere.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-ui.field label="Email" name="email" required>
            <x-ui.input type="email" name="email" :value="old('email', $request->email)" required autocomplete="username" />
        </x-ui.field>

        <x-ui.field label="New password" name="password" required>
            <x-ui.input type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
        </x-ui.field>

        <x-ui.field label="Confirm password" name="password_confirmation" required>
            <x-ui.input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
        </x-ui.field>

        <x-ui.button type="submit" size="lg" class="w-full">Reset password</x-ui.button>
    </form>
</x-layouts.guest>
