@extends('layout.adminLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Riwayat Perbaikan')
</head>

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Riwayat Perbaikan</h1>
        <hr class="border-black mb-6">

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
            <div class="flex items-center space-x-2 text-gray-600">
                <span class="text-sm">Show</span>
                <input id="entries" type="number" value="10"
                    class="w-16 text-center border border-gray-300 rounded-md px-2 py-1 bg-white text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    min="1" />
                <span class="text-sm">entries</span>
            </div>
            <div class="w-full sm:w-auto">
                <input id="search" type="text" placeholder="Cari laporan..."
                    class="input input-bordered w-full sm:w-64 bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary text-sm" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-white uppercase bg-primary">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Gedung</th>
                        <th class="px-6 py-3">Lokasi</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    @foreach($RepairReports as $index => $report)
                    <tr class="cursor-pointer hover:bg-gray-100" onclick="showDetail('{{ $report->id }}')">
                        <td class="px-6 py-3">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">{{ $report->building->building_name ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $report->location_type}}</td>
                        <td class="px-6 py-3">
                            <span class="status-cell text-xs font-semibold px-2.5 py-0.5 rounded"
                                data-status="{{ $report->status }}">{{ $report->status }}</span>
                        </td>
                        <td class="px-6 py-3">{{ $report->created_at->format('d M Y') }}</td>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
            <div class="flex justify-end mt-2">
                <div class="join grid grid-cols-2 gap-2">
                    <button id="prevPageBtn" class="join-item btn btn-outline bg-primary" disabled>Previous</button>
                    <button id="nextPageBtn" class="join-item btn btn-outline bg-primary">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-2/3 max-h-[80vh] overflow-y-auto">
        <h2 id="detailTitle" class="text-lg font-bold text-primary mb-4">Detail Laporan</h2>
        <div class="overflow-x-auto mb-4">
            <table class="table w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Field</th>
                        <th class="px-6 py-3">Informasi</th>
                    </tr>
                </thead>
                <tbody id="detailContent">
                    {{-- Isi akan diisi lewat JS --}}
                </tbody>
            </table>
        </div>
        <div class="text-right space-x-2">
            <button id="btnRiwayat" onclick="" class="px-4 py-2 bg-primary text-white rounded">Riwayat Status</button>
            <button onclick="closeModal()" class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
        </div>
    </div>
</div>

{{-- Modal Riwayat Status --}}
<div id="riwayatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-1/2 max-h-[80vh] overflow-y-auto">
        <h2 class="text-lg font-bold text-primary mb-4">Riwayat Status</h2>
        <div class="overflow-x-auto mb-4">
            <table class="table w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Catatan</th>
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

<!-- Modal Preview Gambar -->
<div id="imagePreviewModal"
    class="fixed inset-0 bg-black bg-opacity-80 hidden z-[9999] flex items-center justify-center cursor-pointer"
    onclick="closeImagePreview()">
    <img id="previewImage" src="" alt="Preview Gambar" class="max-w-full max-h-full rounded" />
</div>



<script>
const repairReports = @json($RepairReports);
console.log(repairReports);

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

function closeRiwayatModal() {
    document.getElementById('riwayatModal').classList.add('hidden');
}

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
            return 'bg-green-100 text-green-800'
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

function showDetail(reportId) {
    const riwayatBtn = document.getElementById('btnRiwayat');
    riwayatBtn.setAttribute('onclick', `showRiwayat(${reportId})`);
    const laporan = repairReports.find(r => r.id === parseInt(reportId));
    if (!laporan) return;


    const createdAt = new Date(laporan.created_at);
    let lastStatusDate = createdAt; // default ke tanggal pengajuan

    if (laporan.histories && laporan.histories.length > 0) {
        const lastHistory = laporan.histories[laporan.histories.length - 1];
        if (lastHistory.complete_date) {
            lastStatusDate = new Date(lastHistory.complete_date);
        }
    }

    const now = new Date();
    const diffTime = now - lastStatusDate;
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays > 3 && laporan.status.toLowerCase() !== 'selesai') {
        keluhanBtn.classList.remove('hidden');
        keluhanBtn.onclick = () => {
            window.location.href = `/user/form-keluhan/${laporan.id}`; // arahkan ke form keluhan
        };
    }


    // Fungsi format tanggal (bisa kamu pindahkan keluar supaya reusable)
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

    // Ambil status laporan terbaru
    const status = laporan.status.toLowerCase();

    // Buat isi detail seperti sebelumnya
    let riwayatHtml = '';
    if (laporan.histories && laporan.histories.length > 0) {
        const lastHistory = laporan.histories[laporan.histories.length - 1];
        const statusClass = getStatusLabelClass(lastHistory.status);
        riwayatHtml = `
            <div>
                <strong>Status:</strong> 
                <span class="px-2 mb-2 text-sm font-semibold rounded ${statusClass}">
                    ${lastHistory.status}
                </span><br>
                <strong>Tanggal Selesai:</strong> ${lastHistory.complete_date}<br>
                <strong>Catatan:</strong> ${lastHistory.repair_notes ?? '-'}<br>
            </div>
        `;
    } else {
        riwayatHtml = 'Tidak ada riwayat perbaikan.';
    }

    // Isi tabel detail
    const detailContent = document.getElementById('detailContent');

    // Cari data history dengan status "Pengecekan akhir"
    const pengecekanAkhirHistory = laporan.histories?.find(
        (item) => item.status === "Pengecekan akhir"
    );

    // Ambil foto perbaikan jika ada
    const repairPhoto = pengecekanAkhirHistory?.damage_photo ?
        `<img src="/storage/${pengecekanAkhirHistory.damage_photo}" alt="Foto Perbaikan" class="max-w-xs max-h-48 rounded border border-gray-300 bukti-preview cursor-pointer" onclick="openImagePreview('/storage/${pengecekanAkhirHistory.damage_photo}')" />` :
        '-';

    detailContent.innerHTML = `
        <tr><td class="px-6 py-3 font-semibold">Nomor Pengajuan</td><td class="px-6 py-3">${String(laporan.id).padStart(4, '0')}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Status Laporan Terkini</td><td class="px-6 py-3"><span class="text-xs font-semibold inline-block px-2 py-1 rounded ${getStatusLabelClass(laporan.status)}">${laporan.status}</span></td></tr>
        <tr><td class="px-6 py-3 font-semibold">Tanggal Diajukan</td><td class="px-6 py-3">${formatDateUTC(laporan.created_at)}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Gedung</td><td class="px-6 py-3">${laporan.building?.building_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Ruangan</td><td class="px-6 py-3">${laporan.room?.room_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Fasilitas Gedung</td><td class="px-6 py-3">${laporan.building_facility?.facility_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Fasilitas Ruangan</td><td class="px-6 py-3">${laporan.room_facility?.facility_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Deskripsi Kerusakan</td><td class="px-6 py-3">${laporan.damage_description}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Dampak Kerusakan</td><td class="px-6 py-3">${laporan.damage_impact}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Bukti Kerusakan</td><td class="px-6 py-3">${laporan.damage_photo? `<img src="/storage/${laporan.damage_photo}" alt="Bukti Kerusakan" class="max-w-xs max-h-48 rounded border border-gray-300 bukti-preview cursor-pointer" onclick="openImagePreview('/storage/${laporan.damage_photo}')" />`: '-'}</td></tr>   
        <tr><td class="px-6 py-3 font-semibold">Foto Perbaikan</td><td class="px-6 py-3">${repairPhoto}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Riwayat Perbaikan Terakhir</td><td class="px-6 py-3">${riwayatHtml}</td></tr><tr>
        <td class="px-6 py-3 font-semibold">Nama Teknisi</td><td class="px-6 py-3">${laporan.repair_technicians?.length? laporan.repair_technicians.map(rt => rt.technician?.name).filter(Boolean).join(', '): '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Tanggal Perbaikan</td><td class="px-6 py-3">${laporan.schedules?.repair_date ?? '-'}</td></tr>
    `;


    document.getElementById('detailModal').classList.remove('hidden');
}


function showRiwayat(reportId) {
    const laporan = repairReports.find(r => r.id === parseInt(reportId));
    if (!laporan) return alert('Data laporan tidak ditemukan');

    console.log(reportId)

    const histories = laporan.histories || [];

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
                    <td class="px-6 py-3">${history.complete_date}</td>
                    <td class="px-6 py-3">${history.repair_notes || '-'}</td>
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

function openImagePreview(imageUrl) {
    const modal = document.getElementById('imagePreviewModal');
    const img = document.getElementById('previewImage');
    img.src = imageUrl;
    modal.classList.remove('hidden');
}

function closeImagePreview() {
    const modal = document.getElementById('imagePreviewModal');
    modal.classList.add('hidden');
    const img = document.getElementById('previewImage');
    img.src = ''; // clear src supaya tidak berat
}

// Show entries dan Cari Laporan
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const entriesInput = document.getElementById('entries');
    const tableBody = document.getElementById('historyTableBody');
    const rows = Array.from(tableBody.querySelectorAll('tr'));
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');

    let currentPage = 1;
    let entriesPerPage = parseInt(entriesInput.value) || 10;
    let filteredRows = rows;

    function renderTable() {
        const startIndex = (currentPage - 1) * entriesPerPage;
        const endIndex = startIndex + entriesPerPage;

        tableBody.innerHTML = '';
        filteredRows.slice(startIndex, endIndex).forEach(row => {
            tableBody.appendChild(row);
        });

        // Handle button disable state
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = endIndex >= filteredRows.length;
    }

    function updateFilter() {
        const searchTerm = searchInput.value.toLowerCase();

        filteredRows = rows.filter(row => {
            const text = row.textContent.toLowerCase();
            return text.includes(searchTerm);
        });

        currentPage = 1; // reset to page 1 when searching
        renderTable();
    }

    // Event listeners
    searchInput.addEventListener('input', updateFilter);

    entriesInput.addEventListener('input', function() {
        let value = parseInt(entriesInput.value);
        if (value < 1 || isNaN(value)) {
            entriesInput.value = 1;
            entriesPerPage = 1;
        } else {
            entriesPerPage = value;
        }
        currentPage = 1;
        renderTable();
    });

    nextBtn.addEventListener('click', () => {
        const maxPage = Math.ceil(filteredRows.length / entriesPerPage);
        if (currentPage < maxPage) {
            currentPage++;
            renderTable();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    });

    // Inisialisasi awal
    updateFilter();
});
</script>
@endsection