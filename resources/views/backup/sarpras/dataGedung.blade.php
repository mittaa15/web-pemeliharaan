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
                            <thead class="bg-primary text-xs uppercase text-white">
                                <tr>
                                    <th class="px-6 py-3">Nama Fasilitas</th>
                                    <th class="px-6 py-3">Deskripsi</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($facilities as $facility)
                                <tr class="facility-row" data-id="{{ $facility->id }}"
                                    data-name="{{ $facility->building_name }}"
                                    data-description="{{ $facility->description }}">
                                    <td class="px-6 py-3">{{ $facility->building_name }}</td>
                                    <td class="px-6 py-3">{{ $facility->description }}</td>
                                    <td class="px-6 py-3">
                                        <button onclick="showFacilityDetails(this)"
                                            class="text-primary hover:underline">Lihat Detail</button>
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

<!-- Modal Detail -->
<div id="detailFacilityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-1/2">
        <h2 class="text-lg text-primary font-bold mb-4">Detail Fasilitas</h2>
        <div class="grid grid-cols-3 gap-y-2 text-sm text-gray-700">
            <div class="font-semibold">Nama</div>
            <div class="col-span-2" id="facilityName">:</div>
            <div class="font-semibold">Deskripsi</div>
            <div class="col-span-2" id="facilityDescription">:</div>
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="closeModal('detailFacilityModal')"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Tutup</button>
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
function showFacilityDetails(button) {
    const row = button.closest('tr');
    document.getElementById('facilityName').textContent = row.dataset.name;
    document.getElementById('facilityDescription').textContent = row.dataset.description;
    openModal('detailFacilityModal');
}

function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>

@endsection