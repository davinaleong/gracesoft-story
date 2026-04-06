<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#2f62d4;font-family:Arial,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#2f62d4;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
                    <tr>
                        <td style="padding-bottom:18px;text-align:center;color:#ffffff;font-size:32px;font-weight:700;">
                            GraceSoft Story
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f4f4f5;border-radius:6px;padding:28px 32px;">
                            <p style="margin:0 0 12px;font-size:30px;line-height:1.15;font-weight:700;color:#111827;">{{ $title }}</p>
                            <p style="margin:0 0 18px;font-size:20px;line-height:1.4;color:#111827;">{{ $message }}</p>

                            @if (!empty($context))
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;">
                                    @foreach ($context as $key => $value)
                                        <tr>
                                            <td style="padding:4px 0;font-size:16px;color:#111827;">
                                                <strong>{{ ucwords(str_replace('_', ' ', (string) $key)) }}:</strong>
                                                {{ is_scalar($value) ? (string) $value : json_encode($value) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif

                            @if ($actionUrl && $actionLabel)
                                <p style="margin:0 0 22px;">
                                    <a href="{{ $actionUrl }}" style="display:inline-block;background:#2f62d4;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:4px;font-weight:600;font-size:15px;">{{ $actionLabel }}</a>
                                </p>
                            @endif

                            <p style="margin:0;font-size:18px;line-height:1.5;color:#111827;">
                                Thanks,<br>
                                GraceSoft Story Team
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:18px;text-align:center;color:#ffffff;font-size:14px;">
                            &copy; {{ date('Y') }} GraceSoft Story. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
