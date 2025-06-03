<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite('resources/css/app.css')
    <title>Dashboard</title>
</head>

<body class="bg-white">
    <div class="flex min-h-screen min-w-0">
        <!-- Tombol Toggle Mobile -->
        <button id="toggleSidebar" class="fixed top-4 left-4 z-50 bg-white p-2 rounded-md shadow-lg md:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Overlay Mobile -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden md:hidden"></div>

        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-64 md:static fixed top-10 left-0 h-screen bg-white text-gray-800 p-6 z-50 shadow-md transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:block overflow-y-auto">

            <!-- Logo & Judul -->
            <div class="mb-6 text-center">
                <img src="{{ asset('images/ITK_1.png') }}" alt="Logo" class="w-20 mx-auto mb-2">
                <h1 class="text-xl font-bold text-blue-600">SIM <span class="text-black font-normal">Pemeliharaan</span>
                </h1>
            </div>

            <hr class="border-gray-300" />

            <!-- Navigasi -->
            <nav class="space-y-4 mt-4">
                <a href="/dashboard"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition {{ Request::is('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2l4 -4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5 13a9 9 0 1 1 14 0a9 9 0 1 1 -14 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Buat Laporan</span>
                </a>
                <a href="/daftar-permintaan"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition {{ Request::is('daftar-permintaan') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 7h18M3 12h18M3 17h18" />
                    </svg>
                    <span>Daftar Permintaan</span>
                </a>
                <a href="/riwayat-laporan-perbaikan"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition {{ Request::is('riwayat-laporan-perbaikan') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M8 17l4-4-4-4m8 8l-4-4 4-4" />
                    </svg>
                    <span>Riwayat Perbaikan</span>
                </a>

                <p class="text-gray-500 font-semibold mb-1">ACCOUNT</p>
                <a href="/profile"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition {{ Request::is('profile') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                    <span>Profil</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 bg-gray-200">
            @include('layout.usertopLayout')
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    @yield('scripts')

    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
</body>

</html>