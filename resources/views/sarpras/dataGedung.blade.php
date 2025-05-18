@extends('layout.sarprasLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-primary font-bold text-xl">Daftar Gedung</h1>
            <div>
                <input id="search" type="text" placeholder="Cari fasilitas..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-64 text-sm" />
            </div>
        </div>
        <hr class="border-black mb-6">


        <div class="space-y-6">
            <!-- Indoor and Outdoor Facilities with Flexbox -->
            <div class="flex justify-between space-x-8">
                <!-- Indoor Facilities -->
                <div class="w-full">

                    <div class="overflow-x-auto">
                        <table class="table w-full text-sm text-left text-gray-600 border" id="indoorFacilitiesTable">
                            <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                                <tr>
                                    <th class="px-6 py-3">Nama Fasilitas</th>
                                    <th class="px-6 py-3">Deskripsi</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($facilities as $facility)
                                <tr class="facility-row" data-name="{{ $facility->building_name }}"
                                    data-id="{{ $facility->id }}" data-description="{{ $facility->description }}">
                                    <td class="px-6 py-3">{{ $facility->building_name }}</td>
                                    <td class="px-6 py-3">{{ $facility->description }}</td>
                                    <td class="px-6 py-3 relative">
                                        <div class="relative inline-block text-left">
                                            <button onclick="showFacilityDetails('{{ $facility->id }}')"
                                                class="text-primary hover:underline">Lihat Laporan</button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Laporan --}}
<div id="facilityDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-2/3 relative">
        <div class="flex justify-between items-center mb-4">
            <h2 id="facilityDetailTitle" class="text-lg font-bold text-primary">Detail Laporan</h2>
            <button onclick="closeModal('facilityDetailModal')"
                class="text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
        </div>

        <div class="overflow-x-auto mb-4">
            <table class="table w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Nomor Laporan</th>
                        <th class="px-6 py-3">Pelapor</th>
                        <th class="px-6 py-3">Teknisi</th>
                        <th class="px-6 py-3">Foto</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody id="facilityDetailContent">
                    {{-- Konten laporan akan dimasukkan melalui JavaScript --}}
                </tbody>
            </table>
        </div>

        <div class="text-right">
            <button onclick="closeModal('facilityDetailModal')"
                class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Tutup</button>
        </div>
    </div>
</div>

<script>
// Dummy data laporan contoh
const laporanData = {
    "1": [ // ID gedung atau fasilitas
        {
            no: "L001",
            pelapor: "Budi",
            teknisi: "Andi",
            foto: "https://via.placeholder.com/100", // Ganti URL jika perlu
            status: "Selesai",
            tanggal: "2025-05-10"
        }
    ],
    "2": [{
        no: "L002",
        pelapor: "Siti",
        teknisi: "Dika",
        foto: "https://via.placeholder.com/100",
        status: "Dalam Proses",
        tanggal: "2025-05-11"
    }]
};

// Fungsi untuk mencari fasilitas berdasarkan nama atau deskripsi
document.getElementById('search').addEventListener('input', function() {
    const searchQuery = this.value.toLowerCase();
    const facilityRows = document.querySelectorAll('.facility-row');

    facilityRows.forEach(row => {
        const name = row.getAttribute('data-name').toLowerCase();
        const description = row.getAttribute('data-description').toLowerCase();
        if (name.includes(searchQuery) || description.includes(searchQuery)) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
});

// Render laporan detail ke dalam modal
function showFacilityDetails(facilityId) {
    const laporanForFacility = laporanData[facilityId] || [];
    const modal = document.getElementById('facilityDetailModal');
    const facilityDetailContent = document.getElementById('facilityDetailContent');

    let detailContent = '';
    laporanForFacility.forEach(laporan => {
        detailContent += `
            <tr>
                <td class="px-6 py-3">${laporan.no}</td>
                <td class="px-6 py-3">${laporan.pelapor}</td>
                <td class="px-6 py-3">${laporan.teknisi}</td>
                <td class="px-6 py-3"><img src="${laporan.foto}" alt="Foto Laporan" class="w-20 h-20 object-cover"></td>
                <td class="px-6 py-3">${laporan.status}</td>
                <td class="px-6 py-3">${laporan.tanggal}</td>
            </tr>
        `;
    });

    facilityDetailContent.innerHTML = detailContent;
    modal.classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>

@endsection