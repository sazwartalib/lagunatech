@extends('emails.layout')

@section('subject', 'Your sign-in code')
@section('preview', 'Your verification code is ' . $code)

@section('content')
    <h1 style="margin:0 0 12px; font-size:20px; font-weight:600; color:#0f172a;">Your sign-in code</h1>
    <p style="margin:0 0 24px; font-size:14px; line-height:1.6; color:#475569;">
        Hi {{ $user->name }}, use the code below to finish signing in to {{ config('app.name') }}.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
        <tr>
            <td style="border-radius:8px; background-color:#f8fafc; border:1px solid #e2e8f0; padding:18px 28px;">
                <span style="font-size:32px; font-weight:700; letter-spacing:8px; color:#0f172a; font-family:'Courier New',monospace;">{{ $code }}</span>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 20px; font-size:13px; line-height:1.6; color:#94a3b8;">
        This code expires in {{ $expiresMinutes }} minutes and can only be used once.
    </p>
@endsection

@section('security')
    If you didn't try to sign in, someone may be using your email address — you can safely ignore this
    message, but consider letting an administrator know.
@endsection
