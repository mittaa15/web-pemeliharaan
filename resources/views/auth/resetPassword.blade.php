<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-primary flex items-center justify-center h-screen">

    <div class="bg-white rounded-lg shadow-xl px-10 py-7">
        <div class="flex justify-center mb-3">
            <img src="{{ asset('images/ITK_1.png') }}" alt="Logo" class="w-20">
        </div>
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold font-sans text-black"><span class="text-primary">SIM -</span> Pemeliharaan
            </h2>
        </div>

        <form class="w-96" method="POST" action="{{ url('/reset-password') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <div class="mb-4 relative">
                <label class="block text-sm font-semibold mb-2 text-primary" for="password">Password</label>
                <input type="password" name="password" id="password"
                    class="border border-gray-300 rounded-lg w-full p-2 pr-10 text-gray-600 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>

                <!-- Toggle Icon -->
                <div id="togglePassword" class="absolute right-3 top-[39px] cursor-pointer text-gray-500">
                    <!-- Eye (open) -->
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 
                              4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <!-- Eye (closed) -->
                    <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 
                              0-8.268-2.943-9.542-7a10.05 10.05 0 012.223-3.592m2.572-1.962A9.969 
                              9.969 0 0112 5c4.478 0 8.27 2.944 9.544 
                              7a9.958 9.958 0 01-4.618 5.383M15 
                              12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                    </svg>
                </div>
            </div>

            <div class="mb-4 relative">
                <label class="block text-sm font-semibold mb-2 text-primary" for="confirmPassword">Konfirmasi
                    Password</label>
                <input type="password" name="password_confirmation" id="confirmPassword"
                    class="border border-gray-300 rounded-lg w-full p-2 pr-10 text-gray-600 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>

                <!-- Toggle Icon -->
                <div id="toggleConfirmPassword" class="absolute right-3 top-[39px] cursor-pointer text-gray-500">
                    <!-- Eye (open) -->
                    <svg id="eyeOpenConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 
                              4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <!-- Eye (closed) -->
                    <svg id="eyeClosedConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 
                              0-8.268-2.943-9.542-7a10.05 10.05 0 012.223-3.592m2.572-1.962A9.969 
                              9.969 0 0112 5c4.478 0 8.27 2.944 9.544 
                              7a9.958 9.958 0 01-4.618 5.383M15 
                              12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                    </svg>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-500 text-white font-semibold py-2 rounded-lg hover:bg-blue-600 transition">MASUK</button>
        </form>
    </div>

    <!-- SCRIPT TOGGLE -->
    <script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    togglePassword.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle icons
        eyeOpen.classList.toggle('hidden');
        eyeClosed.classList.toggle('hidden');
    });

    // Toggle untuk konfirmasi password
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const eyeOpenConfirm = document.getElementById('eyeOpenConfirm');
    const eyeClosedConfirm = document.getElementById('eyeClosedConfirm');

    toggleConfirmPassword.addEventListener('click', () => {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        eyeOpenConfirm.classList.toggle('hidden');
        eyeClosedConfirm.classList.toggle('hidden');
    });
    </script>
</body>

</html>