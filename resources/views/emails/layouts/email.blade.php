<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>{{ $subject ?? 'eSawda' }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f6f8; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; -webkit-text-size-adjust: 100%; }
        table { border-collapse: collapse; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; }
        .card { background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e6e9ee; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 28px 32px; }
        .header a { color: #ffffff; text-decoration: none; font-size: 22px; font-weight: 800; letter-spacing: -0.4px; }
        .header span { color: #38bdf8; }
        .body { padding: 32px; color: #1e293b; font-size: 15px; line-height: 1.7; }
        .body h1 { font-size: 20px; margin: 0 0 16px; color: #0f172a; }
        .body p { margin: 0 0 16px; }
        .body strong { color: #0f172a; }
        .btn { display: inline-block; background-color: #2563eb; color: #ffffff !important; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; margin: 8px 0 16px; }
        .btn-secondary { background-color: #e2e8f0; color: #0f172a !important; }
        .panel { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin: 16px 0; }
        .panel table { width: 100%; }
        .panel td { padding: 4px 0; font-size: 14px; }
        .panel .label { color: #64748b; }
        .panel .value { text-align: right; font-weight: 600; color: #0f172a; }
        .footer { background-color: #f8fafc; padding: 20px 32px; color: #64748b; font-size: 12px; line-height: 1.6; border-top: 1px solid #e6e9ee; }
        .footer a { color: #2563eb; text-decoration: none; }
        .footer .muted { color: #94a3b8; }
        @media only screen and (max-width: 620px) {
            .body, .header, .footer { padding-left: 20px; padding-right: 20px; }
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" class="container" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <div class="card">
                                <div class="header">
                                    <a href="{{ $frontendUrl ?? '' }}">{{ config('app.name', 'eSawda') }}<span>.</span></a>
                                </div>
                                <div class="body">
                                    @yield('content')
                                </div>
                                <div class="footer">
                                    {{ config('app.name', 'eSawda') }} — buy and sell with confidence.<br>
                                    Need help? Reply to this email or visit <a href="{{ $frontendUrl ?? '' }}">our site</a>.<br>
                                    <span class="muted">© {{ date('Y') }} eSawda. All rights reserved.</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>