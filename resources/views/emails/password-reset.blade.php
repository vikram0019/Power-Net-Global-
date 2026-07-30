<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background: #f6f7fb; padding: 32px;">
    <table style="max-width: 480px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden;">
        <tr>
            <td style="background: #0a1428; padding: 24px; text-align: center;">
                <span style="color: #e6c26b; font-size: 22px; font-weight: 800;">PowerNetGlobal</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 32px; text-align: center;">
                <p style="color: #333; font-size: 15px;">We received a request to reset your password.</p>
                <p style="margin: 24px 0;">
                    <a href="{{ $resetUrl }}" style="background: #d4a94a; color: #0a1428; text-decoration: none; font-weight: 800; padding: 12px 28px; border-radius: 8px; display: inline-block;">Reset Password</a>
                </p>
                <p style="color: #888; font-size: 13px;">This link expires in 60 minutes. If you did not request a password reset, no action is needed — your password will stay the same.</p>
                <p style="color: #aaa; font-size: 11px; word-break: break-all; margin-top: 24px;">{{ $resetUrl }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
