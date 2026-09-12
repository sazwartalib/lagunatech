<?php

namespace App\Models;

use App\Mail\Auth\ResetPasswordMail;
use Database\Factories\CustomerUserFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

/**
 * A customer-side login for the portal. Deliberately separate from the staff
 * `User` model so the `web` and `customer` guards never overlap.
 */
class CustomerUser extends Authenticatable implements CanResetPasswordContract
{
    /** @use HasFactory<CustomerUserFactory> */
    use CanResetPassword, HasFactory, Notifiable;

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Send the branded password reset email, linking to the portal's reset page.
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token)
    {
        $url = url(route('portal.password.reset', ['token' => $token], false).'?email='.rawurlencode($this->getEmailForPasswordReset()));

        Mail::to($this)->queue(new ResetPasswordMail($this, $url, config('auth.passwords.customer_users.expire')));
    }
}
