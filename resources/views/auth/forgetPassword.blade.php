<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-primary flex items-center justify-center min-h-screen px-4">

    <div class="bg-white rounded-lg shadow-xl px-6 py-7 w-full max-w-sm">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('images/ITK_1.png') }}" alt="Logo" class="w-20">
        </div>
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold font-sans text-black">
                <span class="text-primary">SIM -</span> Pemeliharaan
            </h2>
        </div>

        <form method="POST" action="{{ url('/forgot-password') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold mb-2 text-primary">Email</label>
                <input type="email" name="email" id="email"
                    class="border border-gray-300 rounded-lg w-full p-2 text-gray-600 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>
            <button type="submit"
                class="w-full bg-[#4aff25] text-white font-semibold text-base py-2 rounded-lg hover:bg-green-500 transition">
                Send Password Reset Link
            </button>
        </form>
    </div>

</body>

</html>