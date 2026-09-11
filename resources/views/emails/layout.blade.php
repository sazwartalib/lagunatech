<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('subject', config('app.name'))</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    {{-- Preview text shown in inbox lists, hidden in the rendered email --}}
    @hasSection('preview')
        <div style="display:none; max-height:0; overflow:hidden; opacity:0;">@yield('preview')</div>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%; max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#1f47f5; padding:28px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        <img src="{{ asset('images/logo-icon.png') }}" alt="{{ config('app.name') }}" width="32" height="32" style="display:inline-block; vertical-align:middle; border-radius:6px;">
                                        <span style="display:inline-block; vertical-align:middle; margin-left:10px; font-size:17px; font-weight:600; color:#ffffff;">{{ config('app.name') }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="padding:36px 32px 8px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Security footnote --}}
                    @hasSection('security')
                        <tr>
                            <td style="padding:0 32px 28px;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc; border-radius:8px;">
                                    <tr>
                                        <td style="padding:14px 16px; font-size:13px; line-height:1.6; color:#64748b;">
                                            @yield('security')
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 32px; background-color:#f8fafc; border-top:1px solid #eef2f6;">
                            <p style="margin:0; font-size:12px; line-height:1.6; color:#94a3b8;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                            <p style="margin:4px 0 0; font-size:12px; line-height:1.6; color:#94a3b8;">
                                This is an automated message — please don't reply directly to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
