@extends('layout.userLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Permintaan Laporan</h1>
        <hr class="border-black mb-6">

        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-2 text-gray-600">
                <span>Show</span>
                <input id="entries" type="number" value="8"
                    class="w-12 text-center border border-gray-300 rounded-md px-2 py-1 bg-white text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary"
                    min="1" />
                <span>entries</span>
            </div>
            <div>
                <input id="search" type="text" placeholder="Cari laporan..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-64 text-sm" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="laporanTable" class="table w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-white uppercase bg-primary">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nomor Pengajuan</th>
                        <th class="px-6 py-3">Gedung</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Waktu Pembuatan</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="laporanTableBody">
                    @foreach($RepairReports as $index => $report)
                    <tr>
                        <td class="px-6 py-3">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-3">{{ $report->building->building_name ?? '-' }}</td>
                        <td class="px-6 py-3"><span class="status-cell text-xs font-semibold px-2.5 py-0.5 rounded"
                                data-status="{{ $report->status }}">{{ $report->status }}</span>
                        </td>
                        <td class="px-6 py-3">{{ $report->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            <button class="text-blue-600 hover:underline detailBtn"
                                onclick="showDetail('{{ $report->id }}')">
                                Detail
                            </button>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex justify-end mt-2">
                <div class="join grid grid-cols-2">
                    <button id="prevPageBtn" class="join-item btn btn-outline bg-primary">Previous page</button>
                    <button id="nextPageBtn" class="join-item btn btn-outline bg-primary">Next</button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-2/3">
        <h2 id="detailTitle" class="text-lg font-bold text-primary mb-4">Detail Laporan</h2>
        @php
        $latestHistory = $report->histories->sortByDesc('created_at')->first();
        @endphp
        <div class="overflow-x-auto mb-4">
            <table class="table w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Field</th>
                        <th class="px-6 py-3">Informasi</th>
                    </tr>
                </thead>
                <tbody id="detailContent">

                </tbody>

            </table>
        </div>
        <div class="text-right space-x-2" id="modalButtons">
            <button id="btnRiwayat" onclick="" class="px-4 py-2 bg-primary text-white rounded">Riwayat
                Status</button>
            <button onclick="closeModal()" class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Riwayat Status -->
<div id="riwayatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-1/2">
        <h2 class="text-lg font-bold text-primary mb-4">Riwayat Status</h2>
        <div class="overflow-x-auto mb-4">
            <table class="table w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Catatan</th>
                        <th class="px-6 py-3">Foto</th>
                    </tr>
                </thead>
                <tbody id="historyContent">
                    <!-- akan diisi lewat JS -->
                </tbody>
            </table>
        </div>
        <div class="text-right">
            <button onclick="closeRiwayatModal()" class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
    const repairReports = @json($RepairReports);
    const latestHistory = @json($report);

    console.log('Isi latestHistory:', latestHistory);
    // Lihat di console browser
    console.log(repairReports);

    function getStatusLabelClass(status) {
        switch (status.toLowerCase()) {
            case 'diproses':
                return 'bg-blue-100 text-blue-800';
            case 'ditolak':
                return 'bg-red-100 text-red-800';
            case 'dijadwalkan':
                return 'bg-indigo-100 text-indigo-800';
            case 'dalam proses pengerjaan':
                return 'bg-yellow-100 text-yellow-800';
            case 'pengecekan akhir':
                return 'bg-purple-100 text-purple-800';
            case 'selesai':
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        const statusCells = document.querySelectorAll('.status-cell');

        statusCells.forEach(cell => {
            const status = cell.getAttribute('data-status');
            const classes = getStatusLabelClass(status);
            cell.classList.add(...classes.split(' '));
        });
    });

    document.getElementById('search').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase();
        const filteredData = laporanData.filter(laporan =>
            laporan.pengajuan.toLowerCase().includes(searchQuery) ||
            laporan.gedung.toLowerCase().includes(searchQuery)
        );
        renderTable(filteredData);
    });


    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    function closeRiwayatModal() {
        document.getElementById('riwayatModal').classList.add('hidden');
    }

    function showRiwayat(reportId) {
        const laporan = repairReports.find(r => r.id === parseInt(reportId));
        if (!laporan) return alert('Data laporan tidak ditemukan');

        console.log(reportId)

        const histories = laporan.histories || [];

        function formatDateUTC(dateString) {
            if (!dateString) return '-';

            const date = new Date(dateString);
            if (isNaN(date)) return dateString;

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const day = String(date.getUTCDate()).padStart(2, '0');
            const month = months[date.getUTCMonth()];
            const year = date.getUTCFullYear();

            return `${day} ${month} ${year}`;
        }

        // Urutkan histories dari tanggal terbaru
        const sortedHistories = [...histories].sort((a, b) => {
            const dateA = new Date(a.complete_date);
            const dateB = new Date(b.complete_date);
            return dateB - dateA; // descending
        });

        // Siapkan HTML riwayat
        let riwayatHtml = '';
        if (sortedHistories.length > 0) {
            sortedHistories.forEach(history => {
                riwayatHtml += `
                <tr class="border-b">
                    <td class="px-6 py-3">
                        <span class="px-2 py-1 text-sm font-semibold rounded ${getStatusLabelClass(history.status)}">
                            ${history.status}
                        </span>
                    </td>
                    <td class="px-6 py-3">${formatDateUTC(history.complete_date)}</td>
                    <td class="px-6 py-3">${history.repair_notes || '-'}</td>
                    <td class="px-6 py-3">
                        ${history.damage_photo ? `<img src="/storage/${history.damage_photo}" alt="Foto Kerusakan" class="w-24 rounded" />` : '-'}
                    </td>
                </tr>
            `;
            });
        } else {
            riwayatHtml = `
            <tr>
                <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada riwayat perbaikan.</td>
            </tr>
        `;
        }

        const riwayatModalContent = document.getElementById('historyContent');
        riwayatModalContent.innerHTML = riwayatHtml;

        document.getElementById('riwayatModal').classList.remove('hidden');
    }

    function showDetail(reportId) {
        const riwayatBtn = document.getElementById('btnRiwayat');
        riwayatBtn.setAttribute('onclick', `showRiwayat(${reportId})`);
        const laporan = repairReports.find(r => r.id === parseInt(reportId));
        if (!laporan) return;

        const detailContent = document.getElementById('detailContent');
        let riwayatHtml = '';

        if (laporan.histories && laporan.histories.length > 0) {
            const lastHistory = laporan.histories[laporan.histories.length - 1]; // ambil data terakhir
            const statusClass = getStatusLabelClass(lastHistory.status);

            riwayatHtml = `
        <div>
            <strong>Status:</strong> 
            <span class="px-2 mb-2 text-sm font-semibold rounded ${statusClass}">
                ${lastHistory.status}
            </span><br>
            <strong>Tanggal Selesai:</strong> ${formatDateUTC(lastHistory.complete_date)}<br>
            <strong>Catatan:</strong> ${lastHistory.repair_notes ?? '-'}<br>
            ${lastHistory.damage_photo ? `<img src="/storage/${lastHistory.damage_photo}" class="w-24 mt-1 rounded" />` : ''}
        </div>
    `;
        } else {
            riwayatHtml = 'Tidak ada riwayat perbaikan.';
        }

        function formatDateUTC(dateString) {
            if (!dateString) return '-';

            const date = new Date(dateString);
            if (isNaN(date)) return dateString;

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const day = String(date.getUTCDate()).padStart(2, '0');
            const month = months[date.getUTCMonth()];
            const year = date.getUTCFullYear();

            return `${day} ${month} ${year}`;
        }

        detailContent.innerHTML = `
        <tr><td class="px-6 py-3 font-semibold">Nomor Pengajuan</td><td class="px-6 py-3">${String(laporan.id).padStart(4, '0')}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Status Laporan Terkini</td><td class="px-6 py-3">${laporan.status}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Tanggal Diajukan</td><td class="px-6 py-3">${formatDateUTC(laporan.created_at)}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Gedung</td><td class="px-6 py-3">${laporan.building?.building_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Ruangan</td><td class="px-6 py-3">${laporan.room?.room_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Fasilitas Gedung</td><td class="px-6 py-3">${laporan.building_facility?.facility_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Fasilitas Ruangan</td><td class="px-6 py-3">${laporan.room_facility?.facility_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Deskripsi Kerusakan</td><td class="px-6 py-3">${laporan.damage_description}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Riwayat Perbaikan Terakhir</td><td class="px-6 py-3">${riwayatHtml}</td></tr>
    `;

        document.getElementById('detailModal').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('laporanTableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        const entriesInput = document.getElementById('entries');
        const searchInput = document.getElementById('search');
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');

        let currentPage = 1;
        let entriesPerPage = parseInt(entriesInput.value);

        function renderTable() {
            const keyword = searchInput.value.toLowerCase();
            const filteredRows = rows.filter(row =>
                row.textContent.toLowerCase().includes(keyword)
            );

            const totalPages = Math.ceil(filteredRows.length / entriesPerPage);
            if (currentPage > totalPages) currentPage = totalPages || 1;

            tableBody.innerHTML = '';

            const start = (currentPage - 1) * entriesPerPage;
            const end = start + entriesPerPage;
            filteredRows.slice(start, end).forEach(row => {
                tableBody.appendChild(row);
            });

            // Update tombol navigasi
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
        }

        entriesInput.addEventListener('change', () => {
            entriesPerPage = parseInt(entriesInput.value);
            currentPage = 1;
            renderTable();
        });

        searchInput.addEventListener('input', () => {
            currentPage = 1;
            renderTable();
        });

        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });

        nextBtn.addEventListener('click', () => {
            const keyword = searchInput.value.toLowerCase();
            const filteredRows = rows.filter(row =>
                row.textContent.toLowerCase().includes(keyword)
            );
            const totalPages = Math.ceil(filteredRows.length / entriesPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });

        renderTable();
    });
</script>
@endsection