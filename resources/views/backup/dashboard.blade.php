@extends('layout.userLayout')

@section('content')
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    </div>
    <p class="text-gray-500 mt-1">Pantau status laporan pemeliharaan dengan mudah dan cepat.</p>
</div>

<!-- Card Section -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Card Template -->
    @php
        $cards = [
            ['title' => 'Permintaan Perbaikan', 'value' => 10, 'icon' => '🛠️', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
            ['title' => 'Sedang Diproses', 'value' => 10, 'icon' => '⏳', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
            ['title' => 'Perbaikan Selesai', 'value' => 10, 'icon' => '✅', 'bg' => 'bg-green-100', 'text' => 'text-green-800'],
            ['title' => 'Laporan Keluhan', 'value' => 10, 'icon' => '📢', 'bg' => 'bg-red-100', 'text' => 'text-red-800'],
        ];
    @endphp

    @foreach ($cards as $card)
        <div class="rounded-xl shadow-sm p-5 {{ $card['bg'] }}">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-600">{{ $card['title'] }}</div>
                    <div class="text-3xl font-bold mt-2 {{ $card['text'] }}">{{ $card['value'] }}</div>
                </div>
                <div class="text-4xl">{{ $card['icon'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<!-- Modal Pop-up -->
<div id="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-primary">Form Laporan</h3>
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
                Selanjutnya
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
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
@endsection
