@extends('layout.userLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Daftar Permintaan Perbaikan')
</head>

@php
function getStatusClass($status) {
switch (strtolower($status)) {
case 'diproses': return 'bg-blue-100 text-blue-800';
case 'ditolak': return 'bg-red-100 text-red-800';
case 'dijadwalkan': return 'bg-indigo-100 text-indigo-800';
case 'dalam proses pengerjaan': return 'bg-yellow-100 text-yellow-800';
case 'pengecekan akhir': return 'bg-purple-100 text-purple-800';
default: return 'bg-gray-100 text-gray-800';
}
}
@endphp


@section('content')
<div class="mt-20 px-4 sm:px-8 py-6 w-full overflow-x-auto"
    style="touch-action: pan-x pan-y; -webkit-overflow-scrolling: touch;">
    @if(session('success'))
    <div id="success-alert" class="bg-green-100 text-green-800 flex justify-center p-2 mb-4 rounded">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            const alertBox = document.getElementById('success-alert');
            if (alertBox) {
                alertBox.style.display = 'none';
            }
        }, 3000);
    </script>
    @endif

    <div class="bg-white rounded-md w-full py-10 px-10 min-w-[700px]">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Permintaan Laporan</h1>
        <hr class="border-black mb-6">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
            <div class="flex items-center space-x-2 text-gray-600">
                <span>Show</span>
                <input id="entries" type="number" value="8"
                    class="w-16 text-center border border-gray-300 rounded-md px-2 py-1 bg-white text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary"
                    min="1" />
                <span>entries</span>
            </div>
            <div class="w-full md:w-auto">
                <input id="search" type="text" placeholder="Cari laporan..."
                    class="input input-bordered w-full md:w-64 bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary text-sm" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-xs md:text-sm text-left text-gray-600 min-w-[600px]">
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
                        <td class="px-6 py-3">
                            <span
                                class="status-cell text-xs font-semibold px-2.5 py-0.5 rounded {{ getStatusClass($report->status) }}">
                                {{ $report->status }}
                            </span>
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
        </div>

        <div class="flex justify-end mt-2">
            <div class="join grid grid-cols-2 gap-2">
                <button id="prevPageBtn" class="join-item btn btn-outline bg-primary" disabled>Previous</button>
                <button id="nextPageBtn" class="join-item btn btn-outline bg-primary">Next</button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Modal Detail --}}
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[95%] sm:w-[90%] md:w-1/2 max-h-[80vh] overflow-y-auto">
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
        <div class="flex flex-col sm:flex-row justify-end sm:space-x-2 space-y-2 sm:space-y-0">
            <button id="btnEditLaporan" class="hidden px-4 py-2 bg-yellow-500 text-white rounded">Edit</button>
            <button id="btnBatalkan" class="hidden px-4 py-2 bg-red-500 text-white rounded">Batalkan</button>
            <button id="btnRiwayat" onclick="openModal()" class="px-4 py-2 bg-primary text-white rounded">Riwayat
                Status</button>
            <button id="btnKeluhan" class="hidden px-4 py-2 bg-red-600 text-white rounded">Ajukan Keluhan</button>
            <button onclick="closeModalDetail()" class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
        </div>

    </div>
</div>

{{-- Modal Riwayat Status --}}
<div id="riwayatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[95%] sm:w-[90%] md:w-1/2 max-h-[80vh] overflow-y-auto">
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

<!-- Modal Gambar -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
    <img id="modalImage" class="max-w-full max-h-full rounded shadow-lg" />
</div>


<div id="batalkanModal" class="fixed inset-0 bg-white bg-opacity-50 flex items-center justify-center hidden z-[9999]">
    <div class="bg-white p-6 rounded-lg w-[95%] sm:w-[90%] md:w-1/2 max-h-[80vh] overflow-y-auto">
        <form action="{{ route('cancel-report') }}" method="POST">
            @csrf
            <input type="hidden" name="id_report" id="BatalkanReportIdInput">
            <h2 class="text-lg font-bold mb-4 text-primary">Konfirmasi</h2>
            <p class="mb-4 text-sm text-gray-700">Apakah Anda yakin ingin membatalkan laporan ini?</p>
            <div class="mt-4 flex justify-end space-x-2">
                <button onclick="closeModalBatalkan()" type="button"
                    class=" px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Tidak</button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Ya,
                    Batalkan</button>
            </div>
        </form>
    </div>
</div>

<div id="keluhanModal" class="fixed inset-0 bg-white bg-opacity-50 flex items-center justify-center hidden z-[9999]">
    <div class="bg-white p-6 rounded-lg w-[95%] sm:w-[90%] md:w-1/2 max-h-[80vh] overflow-y-auto">
        <h2 class="text-lg font-bold mb-4 text-primary">Deskripsi Keluhan</h2>
        <form action="{{ route('create-keluhan') }}" method="POST">
            @csrf
            <input type="hidden" name="report_id" id="KeluhanReportIdInput">
            <textarea id="rejectReason" rows="4" name="keluhan"
                class="w-full border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white"
                placeholder="Tulis kelihan anda di sini..." style="color: black;"></textarea>
            <div class="mt-4 flex justify-end space-x-2">
                <button onclick="closeModalKeluhan()" type="button"
                    class=" px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600" type="submit">Kirim</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Laporan --}}
<div id="editLaporan" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[95%] sm:w-[90%] md:w-1/2 max-h-[80vh] overflow-y-auto">
        <h2 class="text-lg font-bold text-primary mb-4">Edit Laporan</h2>
        <div class="overflow-x-auto mb-4">
            <form id="formPelaporan" method="POST">
                @csrf
                @method('PUT')
                <div>
                    <div class=" grid grid-cols-2 gap-4 mt-4">
                        {{-- Fasilitas --}}
                        <div class=" py-2 col-span-1" id="gedungRuangWrapper">
                            <label class="block text-gray-400 mb-2">Ruang/Fasilitas Gedung<span class="text-red-600">
                                    *</span></label>
                            <button type="button"
                                class="input block w-full bg-white input-bordered border-gray-300 text-black text-left cursor-not-allowed"
                                id="fasilitasBtn">Pilih Fasilitas</button>
                            <input type="text" id="fasilitas" name="fasilitas" class="hidden" value="" readonly />
                        </div>

                        <div id="wrapperFasilitas" class="col-span-1">
                            {{-- Fasilitas --}}
                            <div class="py-2">
                                <label class="block text-gray-400 mb-2">Fasilitas<span class="text-red-600">
                                        *</span></label>
                                <input type="text" name="facility_name" id="fasilitas"
                                    class="input block w-full bg-white input-bordered border-gray-300 text-black text-left cursor-not-allowed"
                                    readonly />
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan --}}
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        {{-- Dampak Kerusakan --}}
                        <div class="py-2 ">
                            <label class="block text-gray-400 mb-2">Dampak Kerusakan<span class="text-red-600">
                                    *</span></label>
                            <select name="damage_impact" id="" required
                                class="input block w-full bg-white input-bordered border-gray-300 text-black">
                                <option value="" disabled selected>-- Pilih Dampak --</option>
                                <option value="Keselamatan pengguna">Keselamatan pengguna</option>
                                <option value="Menghambat pekerjaan">Menghambat pekerjaan</option>
                                <option value="Penghentian operasional">Penghentian operasional</option>
                            </select>
                        </div>
                        {{-- Bukti Kerusakan --}}
                        <div class="py-2">
                            <label class="block text-gray-400 mb-2">Bukti Kerusakan<span class="text-red-600">
                                    *</span></label>
                            <input type="text" class="input block bg-gray-200 text-gray-600 w-full cursor-not-allowed"
                                value="Bukti kerusakan tidak bisa diubah." readonly />
                        </div>
                    </div>

                    {{-- Deskripsi Kerusakan --}}
                    <div class="py-1">
                        <label class="block text-gray-400 mb-2">Deskripsi Kerusakan<span
                                class="text-red-600">*</span></label>
                        <input type="text" name="damage_description" id="deskripsiKerusakan"
                            class="input input-xl h-20 block bg-white input-bordered text-black border-gray-300 w-full text-sm"
                            required />
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="flex justify-center mt-4 gap-3">
                        <button id="btnSimpan" class="btn w-1/6 bg-primary border-none text-white">Simpan</button>
                    </div>
            </form>
        </div>
    </div>
    <div class="text-right">
        <button onclick="closeModalEdit()" class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
    </div>
</div>
</div>



@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded and parsed');
        const tableBody = document.getElementById('laporanTableBody');
        const entriesInput = document.getElementById('entries');
        const searchInput = document.getElementById('search');
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        console.log('laporanTableBody element:', tableBody);

        console.log('entriesInput element:', entriesInput);

        let currentPage = 1;
        let entriesPerPage = parseInt(entriesInput.value) || 8;

        const allRows = Array.from(tableBody.querySelectorAll('tr')).filter(
            row => !row.classList.contains('empty-row')
        );

        function getFilteredRows() {
            const keyword = searchInput.value.toLowerCase();
            return allRows.filter(row => row.textContent.toLowerCase().includes(keyword));
        }

        function renderTable() {
            const filteredRows = getFilteredRows();
            const totalPages = Math.ceil(filteredRows.length / entriesPerPage);

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * entriesPerPage;
            const end = start + entriesPerPage;

            tableBody.innerHTML = '';

            if (filteredRows.length === 0) {
                tableBody.innerHTML = `
                    <tr class="empty-row">
                        <td colspan="6" class="px-6 py-3 text-center text-gray-500">Tidak ada data yang cocok.</td>
                    </tr>`;
            } else {
                filteredRows.slice(start, end).forEach(row => tableBody.appendChild(row));
            }

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage >= totalPages;
        }

        entriesInput.addEventListener('change', () => {
            entriesPerPage = parseInt(entriesInput.value) || 1;
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
            currentPage++;
            renderTable();
        });

        renderTable();
    });

    const repairReports = @json($RepairReports);
    const latestHistory = @json($report ?? null);

    if (latestHistory) {
        console.log('Status terakhir:', latestHistory.status);
        // Misalnya: tampilkan ke elemen di halaman
        document.getElementById('status-info').innerText = latestHistory.status;
    } else {
        console.log('User belum memiliki laporan');
        document.getElementById('status-info').innerText = 'Belum ada laporan yang dibuat.';
        document.getElementById('cancel-button').classList.add('hidden'); // kalau kamu punya tombol
    }

    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        // Event listener saat gambar bukti diklik
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('bukti-preview')) {
                console.log('Gambar diklik:', e.target.getAttribute('src')); // Debug log
                const src = e.target.getAttribute('src');
                modalImage.setAttribute('src', src);
                imageModal.classList.remove('hidden');
            }
        });

        // Klik di luar gambar untuk menutup modal
        imageModal.addEventListener('click', function(e) {
            if (e.target === imageModal) {
                imageModal.classList.add('hidden');
                modalImage.setAttribute('src', '');
            }
        });
    });

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
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    // Setelah DOM siap, cari semua elemen dengan class 'status-cell' dan tambahkan class warna sesuai statusnya
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.status-cell').forEach(el => {
            const status = el.getAttribute('data-status');
            const classes = getStatusLabelClass(status);
            el.classList.add(...classes.split(' '));
        });
    });

    function closeAllModals() {
        const modals = document.querySelectorAll('.modal'); // pastikan semua modal pakai class 'modal'
        modals.forEach(modal => modal.classList.add('hidden'));
    }

    function openModalEdit() {
        closeAllModals();
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeModalEdit() {
        document.getElementById('editLaporan').classList.add('hidden');
    }

    function closeModalDetail() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    function openModalKeluhan() {
        const modal = document.getElementById('keluhanModal');
        if (!modal) {
            console.error("Modal keluhanModal tidak ditemukan!");
            return;
        }
        closeAllModals();
        modal.classList.remove('hidden');
        console.log('Modal status classes:', modal.classList.toString());
    }

    function closeModalKeluhan() {
        document.getElementById('keluhanModal').classList.add('hidden');
    }

    function openModalBatalkan() {
        document.getElementById('batalkanModal').classList.remove('hidden');
    }

    function closeModalBatalkan() {
        document.getElementById('batalkanModal').classList.add('hidden');
    }


    function closeRiwayatModal() {
        document.getElementById('riwayatModal').classList.add('hidden');
    }

    function submitPembatalan() {
        // Lakukan aksi pembatalan di sini
        // Contoh redirect atau fetch API
        alert("Laporan berhasil dibatalkan."); // Ganti dengan aksi real
        closeModalBatalkan();
    }

    function showRiwayat(reportId) {
        const laporan = repairReports.find(r => r.id === parseInt(reportId));
        if (!laporan) return alert('Data laporan tidak ditemukan');

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

    function showDetail(reportId) {
        const riwayatBtn = document.getElementById('btnRiwayat');
        riwayatBtn.setAttribute('onclick', `showRiwayat(${reportId})`);

        const laporan = repairReports.find(r => r.id === parseInt(reportId));
        if (!laporan) return;
        console.log(laporan)

        const batalkanBtn = document.getElementById('btnBatalkan');
        batalkanBtn.classList.add('hidden');


        const keluhanBtn = document.getElementById('btnKeluhan');
        if (!keluhanBtn) {
            console.error("Tombol btnKeluhan tidak ditemukan di DOM!");
            return;
        }
        keluhanBtn.classList.add('hidden');

        const createdAt = new Date(laporan.created_at);
        const now = new Date();

        const diffMs = now - createdAt;
        const diffDays = diffMs / (1000 * 60 * 60 * 24); // hasil float

        console.log('Created At:', createdAt);
        console.log('Now:', now);
        console.log('Selisih Hari:', diffDays);


        // Tampilkan tombol Ajukan Keluhan jika laporan statusnya "Diproses" dan sudah lebih dari 2 hari
        if (diffDays >= 1 && laporan.status.trim().toLowerCase() === 'diproses') {
            keluhanBtn.classList.remove('hidden');
            keluhanBtn.onclick = () => {
                console.log("Tombol Ajukan Keluhan diklik!");
                document.getElementById('KeluhanReportIdInput').value = laporan.id;
                openModalKeluhan();
            };
        }


        function openEditModal(reportId) {
            const modal = document.getElementById('editLaporan');
            const form = document.getElementById('formPelaporan');

            // Set action form ke URL update
            form.action = `/laporan/${reportId}`;

            // Tampilkan modal
            modal.classList.remove('hidden');
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
        detailContent.innerHTML = `
        <tr><td class="px-6 py-3 font-semibold">Nomor Pengajuan</td><td class="px-6 py-3">${String(laporan.id).padStart(4, '0')}</td></tr>
        <tr>
            <td class="px-6 py-3 font-semibold">Status Laporan Terkini</td>
            <td class="px-6 py-3">
                <span class="text-xs font-semibold inline-block px-2 py-1 rounded ${getStatusLabelClass(laporan.status)}">
                    ${laporan.status}
                </span>
            </td>
        </tr>
        <tr><td class="px-6 py-3 font-semibold">Tanggal Diajukan</td><td class="px-6 py-3">${formatDateUTC(laporan.created_at)}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Gedung</td><td class="px-6 py-3">${laporan.building?.building_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Ruangan</td><td class="px-6 py-3">${laporan.room?.room_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Fasilitas Gedung</td><td class="px-6 py-3">${laporan.building_facility?.facility_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Fasilitas Ruangan</td><td class="px-6 py-3">${laporan.room_facility?.facility_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Deskripsi Kerusakan</td><td class="px-6 py-3">${laporan.damage_description}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Dampak Kerusakan</td><td class="px-6 py-3">${laporan.damage_impact}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Bukti Kerusakan</td><td class="px-6 py-3">${laporan.damage_photo? `<img src="/storage/${laporan.damage_photo}" alt="Bukti Kerusakan" class="max-w-xs max-h-48 rounded border border-gray-300 bukti-preview cursor-pointer" />` : '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Riwayat Perbaikan Terakhir</td><td class="px-6 py-3">${riwayatHtml}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Nama Teknisi</td><td class="px-6 py-3">${laporan.schedules?.technician_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Tanggal Perbaikan</td><td class="px-6 py-3">${laporan.schedules?.repair_date ?? '-'}</td></tr>
    `;

        if (laporan.status.toLowerCase() === 'diproses') {
            batalkanBtn.classList.remove('hidden');
            batalkanBtn.onclick = () => {
                document.getElementById('BatalkanReportIdInput').value = laporan.id;
                openModalBatalkan();
            };
        }

        // Tampilkan tombol Edit hanya jika status 'Diproses'
        const btnEdit = document.getElementById('btnEditLaporan');
        if (laporan.status.toLowerCase() === 'diproses') {
            btnEdit.classList.remove('hidden');
            btnEdit.onclick = function() {
                closeAllModals();
                const modalEdit = document.getElementById('editLaporan');
                modalEdit.classList.remove('hidden');

                // Isi field input di form modal edit
                const fasilitasValue = laporan.room?.room_name || laporan.building?.building_name || '-';
                document.querySelector('input[name="fasilitas"]').value = fasilitasValue;
                document.getElementById('fasilitasBtn').innerText = fasilitasValue;

                document.querySelector('input[name="facility_name"]').value =
                    laporan.room_facility?.facility_name ?? '';

                document.querySelector('select[name="damage_impact"]').value =
                    laporan.damage_impact ?? '';

                document.querySelector('input[name="damage_description"]').value =
                    laporan.damage_description ?? '';

                const form = document.getElementById('formPelaporan');
                form.action = `/laporan-user/${laporan.id}`; // Sesuaikan jika prefix route berbeda
                form.method = 'POST'; // Form HTML hanya mendukung GET dan POST

                // Tambahkan input hidden _method=PUT jika belum ada
                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    form.appendChild(methodInput);
                } else {
                    methodInput.value = 'PUT'; // Pastikan valuenya benar
                }

                // Catatan: input file tidak bisa diisi otomatis karena alasan keamanan browser
            };

        } else {
            btnEdit.classList.add('hidden');
            btnEdit.onclick = null;
        }

        document.getElementById('detailModal').classList.remove('hidden');
    }
</script>
@endsection