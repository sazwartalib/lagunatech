@extends('emails.layout')

@section('subject', 'Your password was changed')
@section('preview', 'Your ' . config('app.name') . ' password was just changed')

@section('content')
    <h1 style="margin:0 0 12px; font-size:20px; font-weight:600; color:#0f172a;">Password changed</h1>
    <p style="margin:0 0 20px; font-size:14px; line-height:1.6; color:#475569;">
        Hi {{ $user->name }}, this confirms the password for your account ({{ $user->email }}) was
        changed on {{ $changedAt->format('d M Y, g:i A') }}.
    </p>
@endsection

@section('security')
    If you didn't make this change, contact an administrator immediately — someone else may have
    access to your account.
@endsection
