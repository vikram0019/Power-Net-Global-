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
            <td style="padding: 32px;">
                <p style="color: #333; font-size: 15px; margin-top: 0;">New message from the Contact Us form:</p>
                <table style="width: 100%; font-size: 14px; color: #333; margin-bottom: 16px;">
                    <tr>
                        <td style="padding: 6px 0; color: #888; width: 80px;">Name</td>
                        <td style="padding: 6px 0;">{{ $senderName }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #888;">Email</td>
                        <td style="padding: 6px 0;"><a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a></td>
                    </tr>
                </table>
                <div style="background: #f6f7fb; border-radius: 8px; padding: 16px; color: #333; font-size: 14px; white-space: pre-line;">{{ $messageBody }}</div>
                <p style="color: #aaa; font-size: 11px; margin-top: 24px; margin-bottom: 0;">Reply directly to this email to respond to {{ $senderName }}.</p>
            </td>
        </tr>
    </table>
</body>
</html>
