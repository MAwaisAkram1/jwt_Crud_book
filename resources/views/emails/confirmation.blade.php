<!DOCTYPE html>
<html>
<head>
    <title>Confirm Your Email</title>
</head>
<body>
    {{-- Email body with user confirmation link --}}
    <h1>Hello, {{ $user->name }}</h1>
    <p>Thank you for registering. Please click the link below to confirm your email:</p>
    <a href="{{ $url }}">Confirm Email</a>
    <p>This link will expire in 5 Minute.</p>
</body>
</html>
