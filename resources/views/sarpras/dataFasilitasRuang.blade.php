@extends('layout.sarprasLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Fasilitas Ruang')
</head>

@section('content')
<div class="p-4 sm:p-8 mt-20">
    <div class="bg-white rounded-md w-full py-6 sm:py-10 px-4 sm:px-10 shadow">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-4 sm:space-y-0">
            <h1 class="text-primary font-bold text-xl">Daftar Fasilitas Ruang</h1>
            <input id="search" type="text" placeholder="Cari fasilitas..."
                class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full sm:w-64 text-sm" />
        </div>
        <hr class="border-black mb-6">

        <div class="space-y-6">
            <!-- Table wrapper for horizontal scroll on mobile -->
            <div class="overflow-x-auto">
                <table class="table w-full min-w-[600px] text-sm text-left text-gray-600 border" id="facilitiesTable">
                    <thead class="bg-primary text-xs uppercase text-white">
                        <tr>
                            <th class="px-4 py-3">Gedung</th>
                            <th class="px-4 py-3">Ruang</th>
                            <th class="px-4 py-3">Nama Fasilitas</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($facilities as $facility)
                        <tr class="facility-row hover:bg-gray-100 cursor-pointer" data-id="{{ $facility->id }}"
                            data-name="{{ $facility->facility_name }}"
                            data-building="{{ $facility->room->building->building_name }}"
                            data-room="{{ $facility->room->room_name }}" data-description="{{ $facility->description }}"
                            data-number="{{ $facility->number_units }}" onclick="showFacilityDetails(this)">
                            <td class="px-4 py-2">{{ $facility->room->building->building_name ?? '-' }}</td>
                            <td class="px-4 py-2 capitalize">{{ $facility->room->room_name }}</td>
                            <td class="px-4 py-2">{{ $facility->facility_name }}</td>
                            <td class="px-4 py-2">{{ $facility->number_units }}</td>
                            <td class="px-4 py-2">{{ $facility->description }}</td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Fasilitas Ruang --}}
<div id="facilityDetailModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg max-h-[80vh] overflow-auto shadow-lg">
        <h2 class="text-lg text-primary font-bold mb-4">Detail Fasilitas Ruang</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-y-2 text-sm text-gray-700">
            <div class="font-semibold">Gedung</div>
            <div class="sm:col-span-2" id="buildingName">:</div>
            <div class="font-semibold">Nama Fasilitas</div>
            <div class="sm:col-span-2" id="facilityName">:</div>
            <div class="font-semibold">Jumlah</div>
            <div class="sm:col-span-2" id="numberUnits">:</div>
            <div class="font-semibold">Deskripsi</div>
            <div class="sm:col-span-2" id="facilityDescription">:</div>
        </div>
        <div class="text-right mt-4 space-x-2">
            <button id="btnRiwayat" onclick="openRiwayatModal()" class="px-4 py-2 bg-primary text-white rounded">Riwayat
                Laporan</button>
            <button onclick="closeModal('facilityDetailModal')"
                class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Tutup</button>
        </div>
    </div>
</div>

{{-- Modal Riwayat Status --}}
<div id="riwayatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white p-6 rounded-lg w-full max-w-xl max-h-[80vh] overflow-y-auto shadow-lg">
        <h2 class="text-lg font-bold text-primary mb-4">Riwayat Laporan</h2>
        <div class="overflow-x-auto mb-4">
            <table class="table w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Kode Laporan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3">Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody id="historyContent">
                    {{-- Isi lewat JS --}}
                </tbody>
            </table>
        </div>
        <div class="text-right">
            <button onclick="closeRiwayatModal()" class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
        </div>
    </div>
</div>



<script>
    const facilites = @json($facilities);

    console.log('facilites', facilites);
    // Fungsi untuk mencari fasilitas berdasarkan nama atau deskripsi
    document.getElementById('search').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase();
        const facilityRows = document.querySelectorAll('.facility-row');

        facilityRows.forEach(row => {
            const name = row.getAttribute('data-name').toLowerCase();
            const description = row.getAttribute('data-description').toLowerCase();
            const building = row.getAttribute('data-building').toLowerCase();
            const room = row.getAttribute('data-room').toLowerCase();

            if (
                name.includes(searchQuery) ||
                description.includes(searchQuery) ||
                building.includes(searchQuery) ||
                room.includes(searchQuery)
            ) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });


    function closeRiwayatModal() {
        document.getElementById('riwayatModal').classList.add('hidden');
    }


    let selectedFacility = null;

    function showFacilityDetails(button) {
        const row = button.closest('tr');
        const facilityId = row.dataset.id;
        const name = row.dataset.name;
        const building = row.dataset.building;
        const number = row.dataset.number;
        const description = row.dataset.description;
        selectedFacility = facilites.find(f => f.id == facilityId);

        if (!selectedFacility) {
            alert('Fasilitas tidak ditemukan!');
            return;
        }

        document.getElementById('facilityName').textContent = name;
        document.getElementById('buildingName').textContent = building;
        document.getElementById('numberUnits').textContent = number;
        document.getElementById('facilityDescription').textContent = description;


        openModal('facilityDetailModal');
    }

    function openRiwayatModal() {
        if (!selectedFacility) {
            alert('Pilih fasilitas terlebih dahulu!');
            return;
        }

        const tbody = document.getElementById('historyContent');
        tbody.innerHTML = ''; // Kosongkan dulu isi tbody

        if (selectedFacility.repair_reports && selectedFacility.repair_reports.length > 0) {
            selectedFacility.repair_reports.forEach(report => {
                const kodeLaporan = report.id ? String(report.id).padStart(4, '0') : '-';
                const status = report.status ?? '-';
                const deskripsi = report.damage_description ?? '-';
                const tanggalSelesai = report.updated_at ? new Date(report.updated_at).toLocaleDateString() : '-';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td class="px-6 py-3">${kodeLaporan}</td>
                <td class="px-6 py-3">${status}</td>
                <td class="px-6 py-3">${deskripsi}</td>
                <td class="px-6 py-3">${tanggalSelesai}</td>
            `;
                tbody.appendChild(tr);
            });
        } else {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td class="px-6 py-3 text-center" colspan="4">Tidak ada riwayat laporan</td>`;
            tbody.appendChild(tr);
        }

        openModal('riwayatModal');
    }



    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Fungsi filter tabel berdasarkan input search
    document.getElementById('search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const table = document.getElementById('facilitiesTable');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            // Ambil semua teks di setiap kolom dalam satu baris
            const rowText = row.textContent.toLowerCase();

            // Jika rowText mengandung searchTerm, tampilkan baris, jika tidak sembunyikan
            if (rowText.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }
</script>

@endsection