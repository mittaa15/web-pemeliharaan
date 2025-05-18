dahsboard.blade.php

@extends('layout.userLayout')

@section('content')
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    </div>
    <p class="text-gray-500 mt-1">Pantau status laporan pemeliharaan dengan mudah dan cepat.</p>
</div>

<!-- Modal 1: Pilih Gedung dan Tipe -->
<div id="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-primary">Form Laporan</h3>
            <button id="closeModal" class="text-gray-600 hover:text-red-600 text-2xl leading-none">&times;</button>
        </div>
        <form id="formLaporan">
            <div class="mb-4">
                <label class="block mb-1 bg-white text-primary px-2 rounded w-fit">Gedung</label>
                <select id="gedungSelect"
                    class="w-full border border-gray-300 p-2 rounded text-sm text-gray-600 bg-white">
                    <option selected disabled value="">Pilih Gedung</option>
                    <option>Gedung A</option>
                    <option>Gedung B</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-1 bg-white text-primary px-2 rounded w-fit">Indoor/Outdoor</label>
                <select id="tipeSelect"
                    class="w-full border border-gray-300 p-2 rounded text-sm text-gray-600 bg-white">
                    <option selected disabled value="">Pilih Tipe</option>
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

<!-- Modal 2: Daftar Fasilitas -->
<div id="modalFasilitas" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-primary">Pilih Fasilitas</h3>
            <button id="closeModalFasilitas"
                class="text-gray-600 hover:text-red-600 text-2xl leading-none">&times;</button>
        </div>
        <div id="fasilitasList" class="space-y-2 max-h-[40vh] overflow-y-auto pr-1">
            <!-- Fasilitas dari JS -->
        </div>
        <button id="lanjutFormBtn" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
            Lanjut ke Form Detail
        </button>
    </div>
</div>

<!-- Tombol Buka Modal -->
<button id="openModalButton" class="mt-6 bg-primary text-white px-4 py-2 rounded hover:bg-blue-700">
    Buat Laporan Baru
</button>
@endsection

@section('scripts')
<script>
// Daftar fasilitas dummy
const fasilitasIndoor = [
    'Ruang Rapat A1',
    'Toilet Pria Lt.2',
    'Laboratorium Komputer',
    'Ruang Dosen',
    'Ruang Multimedia',
    'Ruang Arsip'
];
const fasilitasOutdoor = [
    'Area Parkir',
    'Taman Belakang',
    'Lapangan Utama',
    'Halaman Depan',
    'Pintu Gerbang',
    'Tempat Sampah Eksternal'
];

let selectedTipe = '';

document.getElementById('openModalButton').addEventListener('click', () => {
    document.getElementById('modal').classList.remove('hidden');
});

document.getElementById('closeModal').addEventListener('click', () => {
    document.getElementById('modal').classList.add('hidden');
});

document.getElementById('closeModalFasilitas').addEventListener('click', () => {
    document.getElementById('modalFasilitas').classList.add('hidden');
});

document.getElementById('formLaporan').addEventListener('submit', function(e) {
    e.preventDefault();
    const gedung = document.getElementById('gedungSelect').value;
    const tipe = document.getElementById('tipeSelect').value;

    if (!gedung || !tipe) {
        alert('Silakan pilih gedung dan tipe terlebih dahulu.');
        return;
    }

    selectedTipe = tipe;

    const list = tipe === 'Indoor' ? fasilitasIndoor : fasilitasOutdoor;
    const fasilitasList = document.getElementById('fasilitasList');
    fasilitasList.innerHTML = '';

    list.forEach(fasilitas => {
        const el = document.createElement('div');
        el.className = 'p-3 border rounded cursor-pointer hover:bg-gray-100 text-gray-600';
        el.textContent = fasilitas;
        fasilitasList.appendChild(el);
    });

    document.getElementById('modal').classList.add('hidden');
    document.getElementById('modalFasilitas').classList.remove('hidden');
});

document.getElementById('lanjutFormBtn').addEventListener('click', () => {
    if (!selectedTipe) return;

    const url = selectedTipe === 'Indoor' ? '/form-pelaporan-indoor' : '/form-pelaporan-outdoor';
    window.location.href = url;
});
</script>
@endsection