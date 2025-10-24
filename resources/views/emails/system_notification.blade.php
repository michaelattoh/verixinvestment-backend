<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Notification' }}</title>
</head>
<body>
    <p>Hello {{ $recipientName ?? 'Admin' }},</p>

    <p>{{ $message ?? 'You have a new notification.' }}</p>

    @if(isset($details))
        <ul>
            @foreach($details as $key => $value)
                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
            @endforeach
        </ul>
    @endif

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
