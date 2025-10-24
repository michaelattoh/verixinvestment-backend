<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Action Confirmation' }}</title>
</head>
<body>
    <p>Hello Admin,</p>

    <p>Your recent action has been successfully completed:</p>

    <ul>
        <li>Action: {{ $action ?? 'N/A' }}</li>
        <li>Date: {{ $date ?? now()->toDateTimeString() }}</li>
        <li>Status: {{ $status ?? 'Completed' }}</li>
    </ul>

    @if(isset($additionalInfo))
        <p>Additional Information:</p>
        <ul>
            @foreach($additionalInfo as $key => $value)
                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
            @endforeach
        </ul>
    @endif

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
