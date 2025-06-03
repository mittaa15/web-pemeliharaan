<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
    <script>
        const updateStatusUrl = "{{ route('update-status-sarpras') }}";
        const csrfToken = "{{ csrf_token() }}";
    </script>
    <title>@yield('title', 'Dashboard')</title>
</head>

<body class="bg-white">
    <div class="flex min-h-screen bg-white min-w-0">
        <!-- Tombol Toggle -->
        <button id="toggleSidebar" class="absolute top-4 left-4 z-50 bg-white p-2 rounded-md shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        @php
        // Cek apakah user berada di submenu manajemen gedung agar dropdown tetap terbuka
        $submenuOpen = request()->is('data-gedung')
        || request()->is('sarpras-data-ruang')
        || request()->is('data-fasilitas-gedung')
        || request()->is('data-fasilitas-ruang');
        @endphp

        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-72 sticky top-0 self-start h-screen transition-all duration-300 bg-base-200 text-base-content p-6 bg-white shrink-0 overflow-y-auto"
            style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"
            x-data="{ open: {{ $submenuOpen ? 'true' : 'false' }} }">

            <div class="mb-4 text-center">
                <a href="/sarpras-dashboard">
                    <img src="{{ asset('images/ITK_1.png') }}" alt="Logo" class="w-20 mx-auto mb-2" />
                </a>
                <h1 class="text-xl font-bold text-primary">SIM -<span class="font-normal text-black">
                        Pemeliharaan</span></h1>
            </div>

            <hr class="border-black" />

            <nav class="space-y-4 mt-4">
                <!-- Daftar Keluhan -->
                <a href="/sarpras-daftar-keluhan"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ request()->is('sarpras-daftar-keluhan') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12l2 2l4 -4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5 13a9 9 0 1 1 14 0a9 9 0 1 1 -14 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Daftar Keluhan</span>
                </a>

                <!-- Daftar Laporan -->
                <a href="/daftar-permintaan-perbaikan"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ request()->is('daftar-permintaan-perbaikan') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Daftar Laporan</span>
                </a>

                <!-- Riwayat Perbaikan -->
                <a href="/riwayat-perbaikan"
                    class="flex items-center space-x-2 px-3 py-2 mb-4 rounded-lg {{ request()->is('riwayat-perbaikan') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 3v6h6M21 21v-6h-6M3 21h6v-6H3v6zm12-6h6v6h-6v-6zM3 3h6v6H3V3zm12 0h6v6h-6V3z" />
                    </svg>
                    <span>Riwayat Perbaikan</span>
                </a>

                <!-- Manajemen Gedung Dropdown -->
                <div class="space-y-2">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-gray-600 hover:text-blue-700 focus:outline-none">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6h16M4 10h16M4 14h10" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Manajemen Gedung</span>
                        </div>
                        <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform duration-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition class="pl-6 space-y-2" style="display: none;">
                        <a href="/data-gedung"
                            class="block px-3 py-2 rounded-lg {{ request()->is('data-gedung') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                            Data Gedung
                        </a>
                        <a href="/sarpras-data-ruang"
                            class="block px-3 py-2 rounded-lg {{ request()->is('sarpras-data-ruang') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                            Data Ruang
                        </a>
                        <a href="/data-fasilitas-gedung"
                            class="block px-3 py-2 rounded-lg {{ request()->is('data-fasilitas-gedung') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                            Fasilitas Gedung
                        </a>
                        <a href="/data-fasilitas-ruang"
                            class="block px-3 py-2 rounded-lg {{ request()->is('data-fasilitas-ruang') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                            Fasilitas Ruang
                        </a>
                    </div>
                </div>

                <!-- Data Teknisi -->
                <a href="/sarpras-data-teknisi"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ request()->is('sarpras-data-teknisi') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12c2.21 0 4 -1.79 4 -4s-1.79 -4 -4 -4 -4 1.79 -4 4 1.79 4 4 4zM4 20c0 -2.21 3.58 -4 8 -4s8 1.79 8 4v1H4v-1z" />
                    </svg>
                    <span>Data Teknisi</span>
                </a>

                <!-- ACCOUNT PAGES -->
                <div class="mt-6 text-sm text-gray-500 font-bold">ACCOUNT PAGES</div>

                <!-- Profil -->
                <a href="/sarpras-profile"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ request()->is('sarpras-profile') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12c2.21 0 4 -1.79 4 -4s-1.79 -4 -4 -4 -4 1.79 -4 4 1.79 4 4 4zM4 20c0 -2.21 3.58 -4 8 -4s8 1.79 8 4v1H4v-1z" />
                    </svg>
                    <span>Profil</span>
                </a>

                <!-- Keluar -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center space-x-2 text-red-600 hover:text-red-800 px-3 py-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M3 12H7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </nav>
        </aside>


        <!-- Main content -->
        <main class="flex-1 bg-gray-200">
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
            <form>
                <div class="mb-4">
                    <label class="block mb-1 text-gray-600">Gedung</label>
                    <select class="w-full border border-gray-300 p-2 rounded text-sm">
                        <option selected disabled>Pilih Gedung</option>
                        <option>Gedung A</option>
                        <option>Gedung B</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block mb-1 text-gray-600">Indoor/Outdoor</label>
                    <select class="w-full border border-gray-300 p-2 rounded text-sm">
                        <option selected disabled>Pilih Tipe</option>
                        <option>Indoor</option>
                        <option>Outdoor</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                    Kirim
                </button>
            </form>
        </div>
    </div>

    @yield('scripts')

    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');

        let isCollapsed = false;

        toggleBtn.addEventListener('click', () => {
            if (isCollapsed) {
                // Buka sidebar: hapus semua kelas ukuran, lalu pasang w-72 dan padding
                sidebar.classList.remove('w-0', 'w-64');
                sidebar.classList.add('w-72');

                sidebar.classList.remove('p-0');
                sidebar.classList.add('p-6');
            } else {
                // Tutup sidebar: hapus semua kelas ukuran, lalu pasang w-0 dan padding 0
                sidebar.classList.remove('w-72', 'w-64');
                sidebar.classList.add('w-0');

                sidebar.classList.remove('p-6');
                sidebar.classList.add('p-0');
            }
            isCollapsed = !isCollapsed;
        });
    </script>
    @stack('scripts')
</body>

</html>