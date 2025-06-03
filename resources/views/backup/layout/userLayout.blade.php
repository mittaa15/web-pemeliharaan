<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Dashboard</title>
</head>

<body class="bg-white">
    <div class="flex min-h-screen bg-white min-w-0">
        <!-- Tombol Toggle -->
        <button id="toggleSidebar" class="fixed top-4 left-4 z-50 bg-white p-2 rounded-md shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <aside id="sidebar"
            class="fixed top-0 left-0 h-screen w-64 transition-all duration-300 bg-white text-gray-800 p-6 overflow-y-auto shadow-md z-40">


            <!-- Logo & Judul -->
            <div class="mb-6 text-center">
                <img src="{{ asset('images/ITK_1.png') }}" alt="Logo" class="w-20 mx-auto mb-2">
                <h1 class="text-xl font-bold text-blue-600">SIM <span class="text-black font-normal">Pemeliharaan</span>
                </h1>
            </div>

            <!-- Garis -->
            <hr class="border-gray-300 mb-4">

            <!-- Navigasi -->
            <nav class="space-y-1 text-[15px]">

                <a href="/dashboard"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ Request::is('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12l2 2l4 -4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5 13a9 9 0 1 1 14 0a9 9 0 1 1 -14 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Buat Laporan</span>
                </a>

                <a href="/daftar-permintaan"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ Request::is('daftar-permintaan') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 7h18M3 12h18M3 17h18" />
                    </svg>
                    <span>Daftar Permintaan</span>
                </a>

                <a href="/riwayat-laporan-perbaikan" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
            {{ Request::is('riwayat-laporan-perbaikan') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 17l4-4-4-4m8 8l-4-4 4-4" />
                    </svg>
                    <span>Riwayat Perbaikan</span>
                </a>

                <p class="text-gray-500 font-semibold mb-1">ACCOUNT</p>

                <a href="/profile" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
            {{ Request::is('profile') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                    <span>Profil</span>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m6-8V7a3 3 0 00-6 0v1" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </nav>
        </aside>



        <!-- Main content -->
        <main id="mainContent" class="flex-1 bg-gray-200 pl-64 transition-all duration-300">
            @include('layout.usertopLayout')
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Modal Pop-up -->
    <div id="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Form Buat Laporan</h3>
                <button id="closeModal" class="text-gray-600 hover:text-red-600 text-2xl leading-none">&times;</button>
            </div>

            <!-- Non-submitting form untuk sekarang -->
            <form onsubmit="event.preventDefault();">
                <div class="mb-4">
                    <label class="block mb-1 text-gray-600">Gedung</label>
                    <select name="gedung" class="w-full border border-gray-300 p-2 rounded text-sm">
                        <option selected disabled>Pilih Gedung</option>
                        <option>Gedung A</option>
                        <option>Gedung B</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block mb-1 text-gray-600">Indoor/Outdoor</label>
                    <select name="tipe" class="w-full border border-gray-300 p-2 rounded text-sm">
                        <option selected disabled>Pilih Tipe</option>
                        <option>Indoor</option>
                        <option>Outdoor</option>
                    </select>
                </div>
                <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full"
                    onclick="alert('Laporan berhasil disiapkan (simulasi)!'); document.getElementById('modal').classList.add('hidden')">
                    Kirim
                </button>
            </form>
        </div>
    </div>

    @yield('scripts')

    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        let isCollapsed = false;

        toggleBtn.addEventListener('click', () => {
            if (isCollapsed) {
                sidebar.classList.remove('w-0');
                sidebar.classList.add('w-64');
                sidebar.classList.remove('p-0');
                sidebar.classList.add('p-6');

                mainContent.classList.remove('pl-0');
                mainContent.classList.add('pl-64');
            } else {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-0');
                sidebar.classList.remove('p-6');
                sidebar.classList.add('p-0');

                mainContent.classList.remove('pl-64');
                mainContent.classList.add('pl-0');
            }
            isCollapsed = !isCollapsed;
        });
    </script>


</body>

</html>