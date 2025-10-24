<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Password Reset</title>
</head>
<body>
    <p>Hello Admin,</p>

    <p>You requested a password reset for your account.</p>

    <p>
        Click the link below to reset your password (valid for 60 minutes):
    </p>

    <p>
        <a href="{{ $resetLink }}">{{ $resetLink }}</a>
    </p>

    <p>If you did not request a password reset, please ignore this email.</p>

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
