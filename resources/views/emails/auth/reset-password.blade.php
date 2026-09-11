@extends('emails.layout')

@section('subject', 'Reset your password')
@section('preview', 'Reset your ' . config('app.name') . ' password')

@section('content')
    <h1 style="margin:0 0 12px; font-size:20px; font-weight:600; color:#0f172a;">Reset your password</h1>
    <p style="margin:0 0 20px; font-size:14px; line-height:1.6; color:#475569;">
        Hi {{ $user->name }}, we received a request to reset the password for your account
        ({{ $user->email }}). Click the button below to choose a new one.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
        <tr>
            <td style="border-radius:8px; background-color:#1f47f5;">
                <a href="{{ $url }}" style="display:inline-block; padding:12px 24px; font-size:14px; font-weight:600; color:#ffffff; text-decoration:none;">
                    Reset password
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 20px; font-size:13px; line-height:1.6; color:#94a3b8;">
        This link expires in {{ $expiresMinutes }} minutes. If the button doesn't work, copy and paste this
        URL into your browser:<br>
        <a href="{{ $url }}" style="color:#1f47f5; word-break:break-all;">{{ $url }}</a>
    </p>
@endsection

@section('security')
    If you didn't request a password reset, no action is needed — your password will stay unchanged.
    Contact your administrator if you think someone else is trying to access your account.
@endsection
