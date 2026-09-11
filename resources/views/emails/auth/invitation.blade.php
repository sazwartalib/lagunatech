@extends('emails.layout')

@section('subject', 'You’ve been added to ' . config('app.name'))
@section('preview', 'Set your password to get started on ' . config('app.name'))

@section('content')
    <h1 style="margin:0 0 12px; font-size:20px; font-weight:600; color:#0f172a;">Welcome to {{ config('app.name') }}</h1>
    <p style="margin:0 0 20px; font-size:14px; line-height:1.6; color:#475569;">
        Hi {{ $user->name }}, an account has been created for you at {{ config('app.name') }}. Set a
        password below to get started.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
        <tr>
            <td style="border-radius:8px; background-color:#1f47f5;">
                <a href="{{ $url }}" style="display:inline-block; padding:12px 24px; font-size:14px; font-weight:600; color:#ffffff; text-decoration:none;">
                    Set your password
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
    If you weren't expecting this invitation, you can ignore this email — no account access is granted
    until a password is set.
@endsection
