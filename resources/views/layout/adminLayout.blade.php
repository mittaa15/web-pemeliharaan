<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Dashboard</title>
</head>

<body class="bg-white">
    <div class="flex min-h-screen bg-white">
        <!-- Sidebar -->
        <aside class="w-64 bg-base-200 text-base-content p-6 bg-white"
            style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div class="mb-4 text-center">
                <a href="/admin-dashboard">
                    <img src="{{ asset('images/ITK_1.png') }}" alt="Logo" class="w-20 mx-auto mb-2">
                </a>
                <h1 class="text-xl font-bold text-primary">SIM -<span class="font-normal text-black">
                        Pemeliharaan</span></h1>
            </div>

            <hr class="border-black">

            <nav class="space-y-4 mt-4">
                <a href="/admin-daftar-permintaan-perbaikan"
                    class="flex items-center text-gray-600 space-x-2 hover:text-blue-700">
                    <span><img src="{{ asset('images/checklist.png') }}" class="w-5 h-5" alt="Daftar Permintaan"></span>
                    <span>Daftar Laporan</span>
                </a>
                <a href="/admin-riwayat-perbaikan"
                    class="flex items-center space-x-2 text-gray-600 hover:text-blue-700">
                    <span><img src="{{ asset('images/office-building.png') }}" class="w-5 h-5"
                            alt="Riwayat Perbaikan"></span>
                    <span>Riwayat Perbaikan</span>
                </a>
                <a href="/admin-data-gedung" class="flex items-center space-x-2 text-gray-600 hover:text-blue-700">
                    <span><img src="{{ asset('images/office-building.png') }}" class="w-5 h-5" alt="Data Gedung"></span>
                    <span>Data Gedung</span>
                </a>
                <a href="/admin-data-fasilitas-gedung"
                    class="flex items-center space-x-2 text-gray-600 hover:text-blue-700">
                    <span><img src="{{ asset('images/office-building.png') }}" class="w-5 h-5"
                            alt="Fasilitas Gedung"></span>
                    <span>Data Fasilitas Gedung</span>
                </a>
                <a href="/admin-data-ruang" class="flex items-center space-x-2 text-gray-600 hover:text-blue-700">
                    <span><img src="{{ asset('images/office-building.png') }}" class="w-5 h-5" alt="Data Ruang"></span>
                    <span>Data Ruang</span>
                </a>
                <a href="/admin-data-fasilitas-ruang"
                    class="flex items-center space-x-2 text-gray-600 hover:text-blue-700">
                    <span><img src="{{ asset('images/office-building.png') }}" class="w-5 h-5"
                            alt="Fasilitas Ruang"></span>
                    <span>Data Fasilitas Ruang</span>
                </a>

                <div class="mt-6 text-sm text-gray-500 font-bold">ACCOUNT PAGES</div>
                <a href="/admin-profile" class="flex items-center space-x-2 text-gray-500">
                    <span><img src="{{ asset('images/Vector.png') }}" class="w-5 h-5" alt="Profil"></span>
                    <span>Profil</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center space-x-2 text-red-600 hover:text-red-800">
                        <span><img src="{{ asset('images/Vector.png') }}"></span>
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
</body>

</html>