<?php

namespace App\Providers;

use App\Enums\Role;
use App\Listeners\Auth\SendPasswordChangedEmail;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Settings::class);
    }

    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::unguard(false);

        // Super admins bypass every permission check.
        Gate::before(function (User $user, string $ability): ?bool {
            return $user->hasRole(Role::SuperAdmin->value) ? true : null;
        });

        Password::defaults(fn () => $this->app->isProduction()
            ? Password::min(10)->mixedCase()->numbers()->uncompromised()
            : Password::min(8));

        Event::listen(PasswordReset::class, SendPasswordChangedEmail::class);
    }
}
