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
                <p style="color: #333; font-size: 15px; margin-top: 0;">Hi {{ $name }},</p>
                <p style="color: #333; font-size: 15px;">Your account is verified and ready to go. Here are your login details for reference:</p>
                <table style="width: 100%; font-size: 14px; color: #333; margin: 16px 0; background: #f6f7fb; border-radius: 8px;">
                    <tr>
                        <td style="padding: 12px 16px; color: #888; width: 130px;">Username (Email)</td>
                        <td style="padding: 12px 16px;">{{ $email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 16px; color: #888;">Password</td>
                        <td style="padding: 12px 16px;">{{ $password }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 16px; color: #888;">Referral Code</td>
                        <td style="padding: 12px 16px;">{{ $referralCode }}</td>
                    </tr>
                </table>
                <p style="color: #888; font-size: 13px; margin-bottom: 0;">Keep this email safe, or log in and change your password from your dashboard. Share your referral code with others to grow your team.</p>
            </td>
        </tr>
    </table>
</body>
</html>
