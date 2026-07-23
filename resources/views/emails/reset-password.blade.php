@php
    if (!empty($locale)) {
        app()->setLocale($locale);
    }
    $siteName = config('app.name');
    $buttonText = __('ui.reset_email_button');
    $greeting = __('ui.reset_email_greeting', ['name' => $user->name ?? '']);
    $reason = __('ui.reset_email_reason');
    $expire = __('ui.reset_email_expire', ['count' => $expireMinutes]);
    $ignore = __('ui.reset_email_ignore');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ __('ui.reset_email_subject') }}</title>
    <!-- Prevent Gmail from collapsing content -->
    <style type="text/css">.email-content { display: block !important; visibility: visible !important; }</style>
</head>
<body style="margin:0; padding:0; background-color:#1a1a1a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div class="email-content" style="display: block !important; visibility: visible !important;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#1a1a1a; padding: 24px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 480px; margin: 0 auto;">
                    <tr>
                        <td style="background-color:#111111; border: 2px solid #333333; border-radius: 24px; padding: 28px 24px; text-align: center;">
                            <p style="margin: 0 0 16px 0; font-size: 20px; font-weight: 800; color: #F97316;">{{ $siteName }}</p>
                            <p style="margin: 0 0 12px 0; font-size: 16px; line-height: 1.5; color: #CCCCCC;">{{ $greeting }}</p>
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.5; color: #CCCCCC;">{{ $reason }}</p>
                            <table role="presentation" align="center" cellspacing="0" cellpadding="0" style="margin: 0 auto 16px auto;">
                                <tr>
                                    <td style="border-radius: 12px; background-color: #F97316;">
                                        <a href="{{ $url }}" target="_blank" rel="noopener" style="display: inline-block; padding: 14px 28px; font-size: 16px; font-weight: 700; color: #ffffff; text-decoration: none;">{{ $buttonText }}</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 0 0 6px 0; font-size: 12px; color: #999999;">{{ __('ui.reset_email_fallback_link') }}</p>
                            <input type="text" value="{{ $url }}" readonly style="width: 100%; max-width: 100%; box-sizing: border-box; padding: 10px 12px; font-size: 12px; color: #CCCCCC; background-color: #000000; border: 1px solid #333333; border-radius: 8px;margin: 0 0 6px 0;" />
                            <p style="margin: 0 0 12px 0; font-size: 14px; color: #999999;">{{ $expire }}</p>
                            <p style="margin: 0 0 16px 0; font-size: 14px; color: #999999;">{{ $ignore }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </div>
</body>
</html>
