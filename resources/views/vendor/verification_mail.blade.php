<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
</head>

<body>
    <h2>Halo, {{ $name }}!</h2>
    <p>Terima kasih telah mendaftar. Klik tautan berikut untuk memverifikasi email kamu:</p>
    <p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>
    <p>Jika kamu tidak merasa mendaftar, abaikan email ini.</p>
</body>

</html>