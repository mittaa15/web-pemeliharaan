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
        <button id="toggleSidebar" class="fixed top-4 left-4 z-50 bg-white p-2 rounded-md shadow-lg"> <svg
                xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24"
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
            class="fixed top-0 left-0 h-screen w-72 transition-all duration-300 bg-white text-base-content p-6 overflow-y-auto z-40"
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
                <a href="/admin-daftar-keluhan" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
          {{ request()->is('admin-daftar-keluhan') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12l2 2l4 -4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5 13a9 9 0 1 1 14 0a9 9 0 1 1 -14 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Daftar Keluhan</span>
                </a>

                <a href="/admin-daftar-permintaan-perbaikan" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
          {{ request()->is('admin-daftar-permintaan-perbaikan') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Daftar Laporan</span>
                </a>


                <a href="/admin-riwayat-perbaikan" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
          {{ request()->is('admin-riwayat-perbaikan') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 3v6h6M21 21v-6h-6M3 21h6v-6H3v6zm12-6h6v6h-6v-6zM3 3h6v6H3V3zm12 0h6v6h-6V3z" />
                    </svg>
                    <span>Riwayat Perbaikan</span>
                </a>


                <div class="space-y-2">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 rounded text-gray-700 hover:text-blue-700 hover:bg-blue-50 transition focus:outline-none">
                        <div class="flex items-center gap-2">
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
                        <a href="/admin-data-gedung" class="block px-3 py-2 rounded hover:bg-blue-50 transition 
                   {{ request()->is('admin-data-gedung') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                            Data Gedung
                        </a>
                        <a href="/admin-data-ruang" class="block px-3 py-2 rounded hover:bg-blue-50 transition 
                   {{ request()->is('admin-data-ruang') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                            Data Ruang
                        </a>
                        <a href="/admin-data-fasilitas-gedung" class="block px-3 py-2 rounded hover:bg-blue-50 transition 
                   {{ request()->is('admin-data-fasilitas-gedung') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                            Fasilitas Outdoor
                        </a>
                        <a href="/admin-data-fasilitas-ruang" class="block px-3 py-2 rounded hover:bg-blue-50 transition 
                   {{ request()->is('admin-data-fasilitas-ruang') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                            Fasilitas Indoor
                        </a>
                    </div>
                </div>


                <a href="/admin-data-teknisi" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
          {{ request()->is('admin-data-teknisi') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12c2.21 0 4 -1.79 4 -4s-1.79 -4 -4 -4 -4 1.79 -4 4 1.79 4 4 4zM4 20c0 -2.21 3.58 -4 8 -4s8 1.79 8 4v1H4v-1z" />
                    </svg>
                    <span>Data Teknisi</span>
                </a>


                <div class="mt-6 text-sm text-gray-500 font-bold">ACCOUNT PAGES</div>

                <a href="/admin-profile" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
          {{ request()->is('admin-profile') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12c2.21 0 4 -1.79 4 -4s-1.79 -4 -4 -4 -4 1.79 -4 4 1.79 4 4 4zM4 20c0 -2.21 3.58 -4 8 -4s8 1.79 8 4v1H4v-1z" />
                    </svg>
                    <span>Profil</span>
                </a>


                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50 transition">
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
        <main class="flex-1 bg-gray-200 ml-72 transition-all duration-300" id="mainContent">
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

        const mainContent = document.getElementById('mainContent');

        toggleBtn.addEventListener('click', () => {
            if (isCollapsed) {
                sidebar.classList.remove('w-0', 'p-0');
                sidebar.classList.add('w-72', 'p-6');
                mainContent.classList.remove('ml-0');
                mainContent.classList.add('ml-72');
            } else {
                sidebar.classList.remove('w-72', 'p-6');
                sidebar.classList.add('w-0', 'p-0');
                mainContent.classList.remove('ml-72');
                mainContent.classList.add('ml-0');
            }
            isCollapsed = !isCollapsed;
        });
    </script>

</body>
@stack('scripts')

</html>