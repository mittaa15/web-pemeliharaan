<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tambahkan Bootstrap di bagian <head> layout/userLayout.blade.php -->
    @vite('resources/css/app.css')
    <title>Dashboard</title>
</head>
<body class="bg-white">
    <div class="flex min-h-screen bg-white">
        <!-- Sidebar -->
        <aside class="w-64 bg-base-200 text-base-content p-6 bg-white" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div class="mb-4 text-center">
                <a href="/dashboard">
                    <img src="{{ asset('images/ITK_1.png') }}" alt="Logo" class="w-20 mx-auto mb-2">
                </a>
                <h1 class="text-xl font-bold text-primary">SIM -<span class="font-normal text-black"> Pemeliharaan</span></h1>
            </div>

            <hr class="border-black">

            <nav class="space-y-4 mt-4">
                <a href="/daftar-permintaan" class="flex items-center text-gray-600 space-x-2 hover:text-blue-700">
                    <span><img src="{{ asset('images/Vector.png') }}"></span><span>Daftar Permintaan</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-gray-600 hover:text-blue-700">
                    <span><img src="{{ asset('images/Vector.png') }}"></span><span>Status Laporan</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-gray-600 hover:text-blue-700">
                    <span><img src="{{ asset('images/Vector.png') }}"></span><span>Riwayat Laporan</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-gray-600 hover:text-blue-700">
                    <span><img src="{{ asset('images/Vector.png') }}"></span><span>Data Gedung</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-gray-600 hover:text-blue-700">
                    <span><img src="{{ asset('images/Vector.png') }}"></span><span>Data Fasilitas Gedung</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-gray-600 hover:text-blue-700">
                    <span><img src="{{ asset('images/Vector.png') }}"></span><span>Data Fasilitas Ruang</span>
                </a>

                <div class="mt-6 text-sm text-gray-500 font-bold">ACCOUNT PAGES</div>
                <a href="#" class="flex items-center space-x-2 text-gray-500">
                    <span><img src="{{ asset('images/Vector.png') }}"></span><span>Profil</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-red-600 hover:text-red-800">
                    <span><img src="{{ asset('images/Vector.png') }}"></span><span>Keluar</span>
                </a>
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
        // Menampilkan Modal saat tombol "Membuat Laporan" di klik
        document.getElementById('openModalButton').addEventListener('click', function () {
            document.getElementById('modal').classList.remove('hidden');
        });

        // Menutup Modal saat tombol close di klik
        document.getElementById('closeModal').addEventListener('click', function () {
            document.getElementById('modal').classList.add('hidden');
        });
    </script>
</body>
</html>
