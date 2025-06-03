<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script>
        const updateStatusUrlAdmin = "{{ route('update-status-admin') }}";
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
        $submenuOpen = request()->is('admin-data-gedung')
        || request()->is('admin-data-ruang')
        || request()->is('admin-data-fasilitas-gedung')
        || request()->is('admin-data-fasilitas-ruang');
        @endphp

        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-72 transition-all duration-300 bg-base-200 text-base-content p-6 bg-white overflow-hidden shrink-0"
            style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"
            x-data="{ open: {{ $submenuOpen ? 'true' : 'false' }} }">

            <div class="mb-4 text-center">
                <a href="/admin-dashboard">
                    <img src="{{ asset('images/ITK_1.png') }}" alt="Logo" class="w-20 mx-auto mb-2" />
                </a>
                <h1 class="text-xl font-bold text-primary">SIM -<span class="font-normal text-black">
                        Pemeliharaan</span></h1>
            </div>

            <hr class="border-black" />

            <nav class="space-y-4 mt-4">
                <a href="/sarpras-daftar-keluhan"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ request()->is('sarpras-daftar-keluhan') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12l2 2l4 -4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5 13a9 9 0 1 1 14 0a9 9 0 1 1 -14 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Daftar Keluhan</span>
                </a>

                <a href="/sarpras-daftar-permintaan"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ request()->is('sarpras-daftar-permintaan') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6M9 16h6M9 8h6M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z" />
                    </svg>
                    <span>Daftar Permintaan</span>
                </a>

                <a href="/sarpras-riwayat-perbaikan"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ request()->is('sarpras-riwayat-perbaikan') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h1l3 8 4-16 3 8h1" />
                    </svg>
                    <span>Riwayat Perbaikan</span>
                </a>

                <div class="space-y-2">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-gray-600 hover:text-blue-700 focus:outline-none">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                            <span>Manajemen Gedung</span>
                        </div>
                        <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform duration-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition class="pl-6 space-y-2" style="display: none;">
                        <a href="/admin-data-gedung"
                            class="block px-3 py-2 rounded-lg {{ request()->is('admin-data-gedung') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                            Data Gedung
                        </a>
                        <a href="/admin-data-ruang"
                            class="block px-3 py-2 rounded-lg {{ request()->is('admin-data-ruang') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                            Data Ruang
                        </a>
                        <a href="/admin-data-fasilitas-gedung"
                            class="block px-3 py-2 rounded-lg {{ request()->is('admin-data-fasilitas-gedung') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                            Fasilitas Gedung
                        </a>
                        <a href="/admin-data-fasilitas-ruang"
                            class="block px-3 py-2 rounded-lg {{ request()->is('admin-data-fasilitas-ruang') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                            Fasilitas Ruang
                        </a>
                    </div>
                </div>

                <a href="/admin-data-teknisi"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ request()->is('admin-data-teknisi') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5a12.083 12.083 0 01-6.16-10.922L12 14z" />
                    </svg>
                    <span>Data Teknisi</span>
                </a>

                <div class="mt-6 text-sm text-gray-500 font-bold">ACCOUNT PAGES</div>

                <a href="/admin-profile"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg {{ request()->is('admin-profile') ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }} hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A9 9 0 1118.878 6.196 9 9 0 015.12 17.805z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Profil</span>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center space-x-2 text-red-600 hover:text-red-800 px-3 py-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m0-8v-1a3 3 0 016 0v1" />
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

</body>
@stack('scripts')

</html>