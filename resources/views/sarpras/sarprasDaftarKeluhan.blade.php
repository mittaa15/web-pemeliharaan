@extends('layout.sarprasLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Daftar Keluhan')
</head>

@section('content')

<div class="px-4 sm:px-6 md:px-8">
    <div class="bg-white rounded-md w-full py-6 px-4 md:px-10">
        <h1 class="text-primary font-bold text-lg sm:text-xl mb-4">Daftar Keluhan</h1>
        <hr class="border-black mb-6">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-4">
            <div class="flex items-center space-x-2 text-gray-600">
                <span>Show</span>
                <input id="entries" type="number" value="10"
                    class="w-16 text-center border border-gray-300 rounded-md px-2 py-1 bg-white text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary"
                    min="1" />
                <span>entries</span>
            </div>
            <div>
                <input id="search" type="text" placeholder="Cari keluhan..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full md:w-64 text-sm" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="keluhanTable" class="table w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-white uppercase bg-primary">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nomor Pengajuan</th>
                        <th class="px-4 py-3">Email Pengirim</th>
                        <th class="px-4 py-3">Deskripsi Keluhan</th>
                        <th class="px-4 py-3">Lokasi</th>
                    </tr>
                </thead>
                <tbody id="keluhanBody">
                    @foreach($Complaints as $index => $complaint)
                    <tr class="bg-white border-b hover:bg-gray-100 cursor-pointer detailRow"
                        data-id="{{ $complaint->id }}" data-email="{{ $complaint->user->email ?? 'Anonim' }}"
                        data-deskripsi="{{ $complaint->complaint_description }}"
                        data-report="{{ $complaint->id_report }}"
                        data-building="{{ $complaint->repairReport->building->building_name }}">

                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ str_pad($complaint->id_report, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3">{{ $complaint->user->email ?? '' }}</td>
                        <td class="px-4 py-3">{{ $complaint->complaint_description ?? '' }}</td>
                        <td class="px-4 py-3">{{ $complaint->repairReport->building->building_name ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="detailModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4 sm:px-0 hidden">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg relative">
        <h2 class="text-lg font-bold text-primary mb-4">Detail Keluhan</h2>

        <div class="mb-3 flex flex-col sm:flex-row">
            <label class="w-full sm:w-48 font-semibold text-primary">Nomor Pengajuan</label>
            <p id="modalReportId" class="text-gray-700 mt-1 sm:mt-0"></p>
        </div>

        <div class="mb-3 flex flex-col sm:flex-row">
            <label class="w-full sm:w-48 font-semibold text-primary">Email Pengirim</label>
            <p id="modalEmail" class="text-gray-700 mt-1 sm:mt-0"></p>
        </div>

        <div class="mb-3 flex flex-col sm:flex-row">
            <label class="w-full sm:w-48 font-semibold text-primary">Deskripsi Keluhan</label>
            <p id="modalDeskripsi" class="text-gray-700 mt-1 sm:mt-0"></p>
        </div>

        <div class="mb-6 flex flex-col sm:flex-row">
            <label class="w-full sm:w-48 font-semibold text-primary">Lokasi</label>
            <p id="modalGedung" class="text-gray-700 mt-1 sm:mt-0"></p>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-2">
            <a id="shortcutLink" href="#"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-opacity-90 text-sm text-center">Lihat
                Laporan Perbaikan</a>
            <button onclick="closeModal()"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 text-sm">Tutup</button>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('detailModal');
        const rows = document.querySelectorAll('.detailRow');

        rows.forEach(row => {
            row.addEventListener('click', () => {
                const email = row.getAttribute('data-email');
                const deskripsi = row.getAttribute('data-deskripsi');
                const report = row.getAttribute('data-report');
                const gedung = row.getAttribute('data-building');

                document.getElementById('modalEmail').textContent = email;
                document.getElementById('modalDeskripsi').textContent = deskripsi;
                document.getElementById('modalReportId').textContent = report;
                document.getElementById('modalGedung').textContent = gedung;

                document.getElementById('shortcutLink').setAttribute('href',
                    `/daftar-permintaan-perbaikan?id=${report}`);

                modal.classList.remove('hidden');
            });
        });
    });

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Ambil elemen input dan tbody
        const searchInput = document.getElementById('search');
        const entriesInput = document.getElementById('entries');
        const tableBody = document.getElementById('keluhanBody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const entriesCount = parseInt(entriesInput.value) || 10;

            // Filter rows sesuai keyword pencarian
            const filteredRows = rows.filter(row => {
                return row.textContent.toLowerCase().includes(searchTerm);
            });

            // Kosongkan tbody dulu
            tableBody.innerHTML = '';

            // Tampilkan sesuai jumlah entries yang dipilih
            filteredRows.slice(0, entriesCount).forEach(row => {
                tableBody.appendChild(row);
            });
        }

        // Event ketika user ketik di search
        searchInput.addEventListener('input', () => {
            filterTable();
        });

        // Event ketika user ubah jumlah entries
        entriesInput.addEventListener('input', () => {
            // Minimal 1 entry
            if (entriesInput.value < 1) {
                entriesInput.value = 1;
            }
            filterTable();
        });

        // Panggil pertama kali agar sesuai default input dan tampil
        filterTable();
    });
</script>
@endpush

<script>
    const complaint = @json($Complaints);
    console.log(complaint)
</script>