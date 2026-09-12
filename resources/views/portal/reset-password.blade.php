<x-layouts.guest title="Set a new password">
    <div class="mb-8">
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Set a new password</h2>
        <p class="mt-1 text-sm text-slate-500">Choose a strong password you don't use elsewhere.</p>
    </div>

    <form
        method="POST"
        action="{{ route('portal.password.store') }}"
        x-data="{
            password: '',
            confirm: '',
            submitting: false,
            get hasLength() { return this.password.length >= 10; },
            get hasMixedCase() { return /[a-z]/.test(this.password) && /[A-Z]/.test(this.password); },
            get hasNumber() { return /\d/.test(this.password); },
            get score() { return [this.hasLength, this.hasMixedCase, this.hasNumber].filter(Boolean).length; },
            get matches() { return this.confirm.length > 0 && this.password === this.confirm; },
        }"
        @submit="submitting = true"
        class="space-y-4"
    >
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-ui.field label="Email" name="email" required>
            <x-ui.input type="email" name="email" :value="old('email', $request->email)" required autocomplete="username" />
        </x-ui.field>

        <div>
            <x-ui.field label="New password" name="password" required>
                <x-ui.input type="password" name="password" x-model="password" required autocomplete="new-password" placeholder="••••••••" />
            </x-ui.field>

            {{-- Strength meter --}}
            <div class="mt-2 flex gap-1" x-show="password.length > 0" x-cloak>
                <template x-for="i in 3">
                    <span
                        class="h-1.5 flex-1 rounded-full transition-colors"
                        :class="score >= i ? (score === 3 ? 'bg-green-500' : score === 2 ? 'bg-amber-500' : 'bg-red-500') : 'bg-slate-200'"
                    ></span>
                </template>
            </div>

            {{-- Requirements checklist --}}
            <ul class="mt-2 space-y-1 text-xs">
                <li class="flex items-center gap-1.5" :class="hasLength ? 'text-green-600' : 'text-slate-400'">
                    <span x-text="hasLength ? '✓' : '·'"></span> At least 10 characters
                </li>
                <li class="flex items-center gap-1.5" :class="hasMixedCase ? 'text-green-600' : 'text-slate-400'">
                    <span x-text="hasMixedCase ? '✓' : '·'"></span> Upper and lower case letters
                </li>
                <li class="flex items-center gap-1.5" :class="hasNumber ? 'text-green-600' : 'text-slate-400'">
                    <span x-text="hasNumber ? '✓' : '·'"></span> At least one number
                </li>
            </ul>
        </div>

        <div>
            <x-ui.field label="Confirm password" name="password_confirmation" required>
                <x-ui.input type="password" name="password_confirmation" x-model="confirm" required autocomplete="new-password" placeholder="••••••••" />
            </x-ui.field>

            <p class="mt-1.5 text-xs" x-show="confirm.length > 0" x-cloak :class="matches ? 'text-green-600' : 'text-red-600'">
                <span x-text="matches ? '✓ Passwords match' : '✗ Passwords don\'t match'"></span>
            </p>
        </div>

        <x-ui.button type="submit" size="lg" class="w-full" x-bind:disabled="submitting">
            <span x-show="!submitting">Reset password</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                <x-ui.spinner />
                Resetting…
            </span>
        </x-ui.button>
    </form>
</x-layouts.guest>
