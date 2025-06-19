@extends('layout.userLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Riwayat Perbaikan')
</head>

@section('content')
<div class="p-4 md:p-8">
    <div class="bg-white rounded-md w-full py-6 px-4 md:py-10 md:px-10">
        <h1 class="text-primary font-bold text-xl md:text-2xl mb-4">Riwayat Perbaikan</h1>
        <hr class="border-black mb-6">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-2 text-gray-600">
                <label for="entries" class="hidden sm:inline">Show</label>
                <input id="entries" type="number" value="8" min="1"
                    class="w-16 text-center border border-gray-300 rounded-md px-2 py-1 bg-white text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary" />
                <span>entries</span>
            </div>
            <div class="w-full sm:w-auto">
                <input id="search" type="text" placeholder="Cari laporan..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full sm:w-64 text-sm" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-xs md:text-sm text-left text-gray-600 min-w-[600px]">
                <thead class="text-xs text-white uppercase bg-primary">
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Nomor Pengajuan</th>
                        <th class="px-4 py-2">Gedung</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Waktu</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    @forelse($RepairReports as $index => $report)
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-2">{{ $report->building->building_name ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <span class="status-cell text-xs font-semibold px-2.5 py-0.5 rounded"
                                data-status="{{ $report->status }}">{{ $report->status }}</span>
                        </td>
                        <td class="px-4 py-2">{{ $report->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2">
                            <button class="text-blue-600 hover:underline detailBtn"
                                onclick="showDetail('{{ $report->id }}')">
                                Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="px-4 py-3 text-center text-gray-500" colspan="6">Belum ada riwayat laporan yang
                            diajukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex justify-end mt-4 space-x-2">
                <button id="prevPageBtn" class="btn btn-outline bg-primary px-4 py-1 text-sm" disabled>Previous</button>
                <button id="nextPageBtn" class="btn btn-outline bg-primary px-4 py-1 text-sm">Next</button>
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
            <button id="btnRiwayat" onclick="" class="px-4 py-2 bg-primary text-white rounded">Riwayat
                Status</button>
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


        // Fungsi format tanggal (keluar supaya reusable)
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
        <tr><td class="px-6 py-3 font-semibold">Riwayat Perbaikan Terakhir</td><td class="px-6 py-3">${riwayatHtml}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Nama Teknisi</td><td class="px-6 py-3">${laporan.technicians?.length? laporan.technicians.map(t => t.name).join(', '): '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Tanggal Perbaikan</td><td class="px-6 py-3">${laporan.schedules?.repair_date?.slice(0, 10) ?? '-'}</td></tr>
    `;


        document.getElementById('detailModal').classList.remove('hidden');
    }

    function showRiwayat(reportId) {
        const laporan = repairReports.find(r => r.id === parseInt(reportId));
        if (!laporan || !laporan.histories || laporan.histories.length === 0) return;

        const sortedHistories = laporan.histories.slice().sort((a, b) => {
            return new Date(a.complete_date) - new Date(b.complete_date);
        });

        const tbody = document.getElementById('historyContent');
        tbody.innerHTML = ''; // Bersihkan isi sebelumnya

        sortedHistories.forEach(history => {
            const statusClass = getStatusLabelClass(history.status);
            const row = `
            <tr>
                <td class="px-6 py-3">
                    <span class="text-xs font-semibold inline-block px-2 py-1 rounded ${statusClass}">
                        ${history.status}
                    </span>
                </td>
                <td class="px-6 py-3">${formatDateUTC(history.complete_date)}</td>
                <td class="px-6 py-3">${history.repair_notes ?? '-'}</td>
            </tr>
        `;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        document.getElementById('riwayatModal').classList.remove('hidden');
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

    const entriesInput = document.getElementById('entries');
    const searchInput = document.getElementById('search');
    const tableBody = document.getElementById('historyTableBody');
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');

    let filteredReports = [...repairReports]; // salinan awal data
    let currentEntries = parseInt(entriesInput.value) || 10;
    let currentPage = 1;

    function renderTable() {
        tableBody.innerHTML = '';

        const totalPages = Math.ceil(filteredReports.length / currentEntries) || 1;

        // Pastikan currentPage valid
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * currentEntries;
        const endIndex = startIndex + currentEntries;

        const slicedReports = filteredReports.slice(startIndex, endIndex);

        slicedReports.forEach((report, index) => {
            const statusClass = getStatusLabelClass(report.status);
            const buildingName = report.building?.building_name ?? '-';
            const createdAt = new Date(report.created_at).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });

            const row = `
        <tr>
            <td class="px-6 py-3">${startIndex + index + 1}</td>
            <td class="px-6 py-3">${String(report.id).padStart(4, '0')}</td>
            <td class="px-6 py-3">${buildingName}</td>
            <td class="px-6 py-3">
                <span class="status-cell text-xs font-semibold px-2.5 py-0.5 rounded ${statusClass}">
                    ${report.status}
                </span>
            </td>
            <td class="px-6 py-3">${createdAt}</td>
            <td class="px-6 py-3">
                <button class="text-blue-600 hover:underline detailBtn" onclick="showDetail('${report.id}')">Detail</button>
            </td>
        </tr>
        `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });

        // Update state tombol pagination
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages || totalPages === 0;
    }

    // Event listener untuk input jumlah entri
    entriesInput.addEventListener('input', () => {
        currentEntries = parseInt(entriesInput.value) || 1;
        currentPage = 1; // reset ke halaman 1
        renderTable();
    });

    // Event listener untuk input pencarian
    searchInput.addEventListener('input', () => {
        const keyword = searchInput.value.toLowerCase();

        filteredReports = repairReports.filter(report =>
            String(report.id).padStart(4, '0').includes(keyword) ||
            (report.building?.building_name ?? '').toLowerCase().includes(keyword) ||
            report.status.toLowerCase().includes(keyword)
        );

        currentPage = 1; // reset ke halaman 1 saat cari
        renderTable();
    });

    // Event listener tombol Previous
    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    });

    // Event listener tombol Next
    nextBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredReports.length / currentEntries) || 1;
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
        }
    });

    // Render pertama kali saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        renderTable();
    });
</script>
@endsection