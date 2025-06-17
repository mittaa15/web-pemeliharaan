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

        @php
        $submenuOpen = request()->is('data-gedung')
        || request()->is('sarpras-data-ruang')
        || request()->is('data-fasilitas-gedung')
        || request()->is('data-fasilitas-ruang');
        @endphp

        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-72 md:static fixed top-0 h-screen md:top-10 left-0  bg-white text-base-content p-6 z-50 shadow-md transition-transform duration-300 transform -translate-x-full md:translate-x-0 sm:blockx overflow-y-auto"
            x-data="{ open: {{ $submenuOpen ? 'true' : 'false' }} }">

            <div class="mb-4 text-center">
                <a href="/sarpras-dashboard">
                    <img src="{{ asset('images/ITK_1.png') }}" alt="Logo" class="w-16 mx-auto mb-2" />
                </a>
                <h1 class="text-xl font-bold text-primary">SIM <span class="font-normal text-black">Pemeliharaan</span>
                </h1>
            </div>

            <hr class="border-black" />

            <nav class="space-y-4 mt-4">
                <!-- Daftar Keluhan -->
                <a href="/sarpras-daftar-keluhan" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
                    {{ request()->is('sarpras-daftar-keluhan') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2l4 -4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5 13a9 9 0 1 1 14 0a9 9 0 1 1 -14 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Daftar Keluhan</span>
                </a>

                <!-- Daftar Laporan -->
                <a href="/daftar-permintaan-perbaikan"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
                    {{ request()->is('daftar-permintaan-perbaikan') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Daftar Laporan</span>
                </a>

                <!-- Riwayat Perbaikan -->
                <a href="/riwayat-perbaikan" class="flex items-center gap-2 px-3 py-2 mb-4 rounded hover:bg-blue-50 transition
                    {{ request()->is('riwayat-perbaikan') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3v6h6M21 21v-6h-6M3 21h6v-6H3v6zm12-6h6v6h-6v-6zM3 3h6v6H3V3zm12 0h6v6h-6V3z" />
                    </svg>
                    <span>Riwayat Perbaikan</span>
                </a>

                <!-- Dropdown Manajemen Gedung -->
                <div class="space-y-2">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 rounded hover:bg-blue-50 transition text-gray-700 hover:text-blue-700 focus:outline-none">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                        <a href="/data-gedung" class="block px-3 py-2 rounded hover:bg-blue-50 transition
                            {{ request()->is('data-gedung') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                            Data Gedung
                        </a>
                        <a href="/sarpras-data-ruang" class="block px-3 py-2 rounded hover:bg-blue-50 transition
                            {{ request()->is('sarpras-data-ruang') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                            Data Ruang
                        </a>
                        <a href="/data-fasilitas-gedung"
                            class="block px-3 py-2 rounded hover:bg-blue-50 transition
                            {{ request()->is('data-fasilitas-gedung') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                            Fasilitas Gedung
                        </a>
                        <a href="/data-fasilitas-ruang"
                            class="block px-3 py-2 rounded hover:bg-blue-50 transition
                            {{ request()->is('data-fasilitas-ruang') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                            Fasilitas Ruang
                        </a>
                    </div>
                </div>

                <!-- Data Teknisi -->
                <a href="/sarpras-data-teknisi" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
                    {{ request()->is('sarpras-data-teknisi') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4 -1.79 4 -4s-1.79 -4 -4 -4 -4 1.79 -4 4 1.79 4 4 4zM4 20c0 -2.21 3.58 -4 8 -4s8 1.79 8 4v1H4v-1z" />
                    </svg>
                    <span>Data Teknisi</span>
                </a>

                <div class="mt-6 text-sm text-gray-500 font-bold">ACCOUNT PAGES</div>

                <!-- Profil -->
                <a href="/sarpras-profile" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 transition
                    {{ request()->is('sarpras-profile') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4 -1.79 4 -4s-1.79 -4 -4 -4 -4 1.79 -4 4 1.79 4 4 4zM4 20c0 -2.21 3.58 -4 8 -4s8 1.79 8 4v1H4v-1z" />
                    </svg>
                    <span>Profil</span>
                </a>

                <!-- Keluar -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7M3 12H7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 min-w-0 bg-gray-200">
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

    @stack('scripts')
</body>

</html>