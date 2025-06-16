@extends('layout.sarprasLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Fasilitas Gedung')
</head>

@section('content')
<div class="p-4 md:p-8">
    <div class="bg-white rounded-md w-full py-6 md:py-10 px-4 md:px-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 space-y-4 md:space-y-0">
            <h1 class="text-primary font-bold text-xl">Daftar Fasilitas Gedung</h1>
            <div class="w-full md:w-auto">
                <input id="search" type="text" placeholder="Cari fasilitas..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full md:w-64 text-sm" />
            </div>
        </div>
        <hr class="border-black mb-6">

        <div class="space-y-6">
            <div class="flex flex-col md:flex-row md:space-x-8 space-y-6 md:space-y-0">
                <!-- Indoor Facilities -->
                <div class="w-full overflow-x-auto">
                    <h2 class="text-lg font-bold text-primary mb-2">Daftar Fasilitas Gedung</h2>
                    <table class="table min-w-[500px] w-full text-sm text-left text-gray-600 border"
                        id="indoorFacilitiesTable">
                        <thead class="bg-primary text-xs uppercase text-white">
                            <tr>
                                <th class="px-6 py-3">Gedung</th>
                                <th class="px-6 py-3">Lokasi</th>
                                <th class="px-3 py-3">Nama Fasilitas</th>
                                <th class="px-3 py-3">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($facilities as $facility)
                            <tr class="facility-row hover:bg-gray-100 cursor-pointer"
                                onclick="showFacilityDetails({{ $facility->id }})" data-id="{{ $facility->id }}"
                                data-name="{{ strtolower($facility->facility_name) }}"
                                data-description="{{ strtolower($facility->description) }}">
                                <td class="px-6 py-3 whitespace-nowrap">{{ $facility->building->building_name }}</td>
                                <td class="px-6 py-3">{{ $facility->location}}</td>
                                <td class="px-3 py-3 whitespace-nowrap">{{ $facility->facility_name }}</td>
                                <td class="px-3 py-3">{{ $facility->description }}</td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Fasilitas (Grid Layout) -->
<div id="facilityDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-4 md:p-6 rounded-lg w-[90%] md:w-[40%] max-h-[80vh] overflow-auto shadow-lg relative">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-primary">Detail Fasilitas</h2>
        </div>

        <div id="facilityDetailContent" class="grid grid-cols-3 gap-y-2 text-sm text-gray-700">
            <!-- Data isi akan diisi lewat JS -->
            <div class="col-span-3 text-center py-4" id="loadingText">Memuat data...</div>
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
<div id="riwayatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-4 md:p-6 rounded-lg w-[90%] md:w-1/2 max-h-[80vh] overflow-y-auto">
        <h2 class="text-lg font-bold text-primary mb-4">Riwayat Laporan</h2>
        <div class="overflow-x-auto mb-4">
            <table class="table min-w-[500px] w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Kode Laporan</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3">Tanggal Selesai</th>
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
    // Filter pencarian fasilitas 
    document.getElementById('search').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase();
        const facilityRows = document.querySelectorAll('.facility-row');

        facilityRows.forEach(row => {
            const name = row.getAttribute('data-name');
            const description = row.getAttribute('data-description');
            if (name.includes(searchQuery) || description.includes(searchQuery)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openRiwayatModal() {
        const historyContent = document.getElementById('historyContent');

        if (currentRepairReports.length === 0) {
            historyContent.innerHTML = `
            <tr><td colspan="4" class="text-center py-2">Tidak ada riwayat laporan.</td></tr>
        `;
        } else {
            const rows = currentRepairReports.map(report => {
                const kode = report.id ? String(report.id).padStart(4, '0') : '-';
                const status = report.status ?? '-';
                const deskripsi = report.damage_description ?? '-';
                const tanggalSelesai = report.updated_at ?
                    new Date(report.updated_at).toLocaleDateString('id-ID') :
                    '-';

                return `
                <tr>
                    <td class="px-6 py-2">${kode}</td>
                    <td class="px-6 py-2">${status}</td>
                    <td class="px-6 py-2">${deskripsi}</td>
                    <td class="px-6 py-2">${tanggalSelesai}</td>
                </tr>
            `;
            }).join('');

            historyContent.innerHTML = rows;
        }

        document.getElementById('riwayatModal').classList.remove('hidden');
    }


    function closeRiwayatModal() {
        document.getElementById('riwayatModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');

        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();

            // Fungsi filter baris pada tabel tertentu
            function filterTable(tableId) {
                const table = document.getElementById(tableId);
                const rows = table.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    // Ambil text dari kolom nama fasilitas dan deskripsi dengan pengecekan null
                    const nameCell = row.querySelector('td:nth-child(2)');
                    const descCell = row.querySelector('td:nth-child(3)');

                    const name = nameCell ? nameCell.textContent.toLowerCase() : '';
                    const desc = descCell ? descCell.textContent.toLowerCase() : '';

                    // Tampilkan baris jika mengandung filter, sembunyikan jika tidak
                    if (name.includes(filter) || desc.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Terapkan filter ke kedua tabel
            filterTable('indoorFacilitiesTable');
            filterTable('outdoorFacilitiesTable');
        });
    });

    function showFacilityDetails(facilityId) {
        const modal = document.getElementById('facilityDetailModal');
        const facilityDetailContent = document.getElementById('facilityDetailContent');

        facilityDetailContent.innerHTML = `<div class="col-span-3 text-center py-4" id="loadingText">Memuat data...</div>`;
        modal.classList.remove('hidden');

        fetch(`/building-facility/${facilityId}`)
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat data dari server');
                return response.json();
            })
            .then(response => {
                const data = response.data;
                currentRepairReports = data.repair_reports ?? [];
                console.log(data)

                if (!data) {
                    facilityDetailContent.innerHTML = `
                    <div class="col-span-3 text-center py-4">Tidak ada data fasilitas.</div>`;
                    return;
                }

                const gedung = data.building?.building_name ?? '-';
                const namaFasilitas = data.facility_name ?? '-';
                const jumlah = data.number_units ?? '-';
                const deskripsi = data.description ?? '-';

                // Mapping riwayat laporan kerusakan
                const riwayat = (data.repair_reports && data.repair_reports.length > 0) ?
                    data.repair_reports.map(report => {
                        const status = report.status ?? '-';
                        const tanggal = new Date(report.created_at).toLocaleDateString('id-ID');
                        return `<li>Status: <b>${status}</b> - Tanggal: ${tanggal}</li>`;
                    }).join('') :
                    '<li>Tidak ada riwayat laporan.</li>';

                facilityDetailContent.innerHTML = `
                <div class="font-semibold">Gedung</div>
                <div>:</div>
                <div>${gedung}</div>

                <div class="font-semibold">Nama Fasilitas</div>
                <div>:</div>
                <div>${namaFasilitas}</div>

                <div class="font-semibold">Deskripsi</div>
                <div>:</div>
                <div>${deskripsi}</div>
            `;
            })
            .catch(error => {
                facilityDetailContent.innerHTML = `
                <div class="col-span-3 text-center py-4 text-red-600">Gagal memuat data: ${error.message}</div>`;
            });
    }
</script>
@endsection