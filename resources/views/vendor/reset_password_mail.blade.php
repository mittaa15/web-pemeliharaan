<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="font-size: 24px; margin-bottom: 20px;">Halo, {{ $userName }}</h1>
        <p style="margin-bottom: 20px;">Anda menerima email ini karena kami menerima permintaan reset password untuk
            akun Anda.</p>
        <p style="margin-bottom: 20px;"><a href="{{ $resetUrl }}"
                style="display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none;">Reset
                Password</a></p>
        <p style="margin-bottom: 20px;">Tautan reset password ini akan kedaluwarsa dalam 60 menit.</p>
        <p>Jika Anda tidak melakukan permintaan reset password, tidak perlu tindakan lebih lanjut.</p>
        <p>Terima kasih,<br>{{ config('app.name') }}</p>
    </div>
</body>

</html>