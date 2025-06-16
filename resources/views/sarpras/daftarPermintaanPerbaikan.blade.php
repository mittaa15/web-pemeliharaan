@extends('layout.sarprasLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Daftar Laporan')
</head>

@section('content')
@if(session('success'))
<div id="success-message" class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center">
    {{ session('success') }}
</div>

<script>
    setTimeout(() => {
        const msg = document.getElementById('success-message');
        if (msg) {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        }
    }, 3000);
</script>
@endif

<div class="p-4 md:p-8">
    <div class="bg-white rounded-md w-full py-6 md:py-10 px-4 md:px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Permintaan Laporan</h1>
        <hr class="border-black mb-6">

        <!-- Filter & Search -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
            <div class="flex items-center space-x-2 text-gray-600">
                <span>Show</span>
                <input id="entries" type="number" value="10"
                    class="w-16 text-center border border-gray-300 rounded-md px-2 py-1 bg-white text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary"
                    min="1" />
                <span>entries</span>
            </div>
            <div class="w-full md:w-auto">
                <input id="search" type="text" placeholder="Cari laporan..."
                    class="input input-bordered w-full md:w-64 bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary text-sm" />
            </div>
        </div>

        <!-- Table Responsive -->
        <div class="overflow-x-auto">
            <table id="laporanTable" class="min-w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-white uppercase bg-primary">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Gedung</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Waktu Pembuatan</th>
                        <th class="px-4 py-3">Prioritas</th>
                    </tr>
                </thead>
                <tbody id="laporanBody">
                    @foreach($RepairReports as $index => $report)
                    <tr class="cursor-pointer hover:bg-gray-100" data-id="{{ $report->id }}">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ $report->building->building_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $report->location_type ?? '-'}}</td>
                        <td class="px-4 py-3">
                            <span class="status-cell text-xs font-semibold px-2.5 py-0.5 rounded"
                                data-status="{{ $report->status }}">{{ $report->status }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $report->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            @php
                            $dp = $report->damage_point;
                            if ($dp >= 0 && $dp <= 25) { $prioritas='Rendah' ; $color='text-green-600 font-semibold' ; }
                                elseif ($dp>= 26 && $dp <= 50) { $prioritas='Sedang' ;
                                    $color='text-yellow-600 font-semibold' ; } elseif ($dp>= 51 && $dp <= 75) {
                                        $prioritas='Tinggi' ; $color='text-orange-600 font-semibold' ; } elseif ($dp>=
                                        76) {
                                        $prioritas = 'Urgent';
                                        $color = 'text-red-600 font-semibold';
                                        } else {
                                        $prioritas = '-';
                                        $color = '';
                                        }
                                        @endphp
                                        <span class="{{ $color }}">{{ $prioritas }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-end mt-4">
            <div class="join grid grid-cols-2 gap-2">
                <button id="prevPageBtn" class="join-item btn btn-outline bg-primary text-white"
                    disabled>Previous</button>
                <button id="nextPageBtn" class="join-item btn btn-outline bg-primary text-white">Next</button>
            </div>
        </div>
    </div>
</div>
@endsection


{{-- Modal Detail--}}
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-lg md:max-w-2xl max-h-[80vh] overflow-y-auto p-4 md:p-6 shadow-lg">
        <h2 class="text-lg font-bold text-primary mb-4">Detail Laporan</h2>

        <div class="overflow-x-auto mb-4">
            <table class="table w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Field</th>
                        <th class="px-6 py-3">Informasi</th>
                    </tr>
                </thead>
                <tbody id="detailContent">
                    {{-- Diisi lewat JavaScript --}}
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap gap-2 justify-end">
            <button id="deleteButton" data-id="" onclick="openModal('deleteModal')"
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Hapus
            </button>
            <button id="rejectButton" data-id-report="" data-id-user="" onclick="openRejectModal()"
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Tolak
            </button>
            <button id="approveButton" data-id-report="" data-id-user="" onclick="openScheduleModal(this)"
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                Setujui
            </button>
            <button id="editButton" data-id="" onclick="openEditModal()"
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Edit
            </button>
            <button id="addNoteButton" data-id-history="" data-id-user="" onclick="openAddNoteModal()"
                class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 hidden">
                Tambah Catatan
            </button>
            <button onclick="closeModal('detailModal')" class="px-4 py-2 bg-primary text-white rounded">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Gambar -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
    <img id="modalImage" class="max-w-full max-h-full rounded shadow-lg" />
</div>

{{-- DeleteModal --}}
<div id="deleteModal" class="fixed inset-0 bg-white bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-1/3 max-h-[80vh] overflow-y-auto">
        <h2 class="text-lg font-bold text-primary mb-4">Konfirmasi Hapus Data</h2>

        <p class="mb-6 text-gray-700">
            Apakah Anda yakin ingin menghapus data ini?
        </p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal('deleteModal')"
                    class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                    Batal
                </button>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Hapus
                </button>
            </div>
        </form>
    </div>
</div>

{{-- EditModal --}}
<div id="editModal" class="fixed inset-0 bg-white bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-1/3 max-h-[80vh] overflow-y-auto">
        <h2 class="text-lg font-bold text-primary mb-4">Edit Dampak Kerusakan</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-gray-600 mb-1">Dampak Kerusakan<span class="text-red-600">*</span></label>
                <select id="editDamageImpact" name="damage_impact" required class="input w-full bg-gray-100 text-black">
                    <option value="" disabled selected>-- Pilih Dampak --</option>
                    <option value="Keselamatan pengguna">Keselamatan pengguna</option>
                    <option value="Menghambat pekerjaan">Menghambat pekerjaan</option>
                    <option value="Penghentian operasional">Penghentian operasional</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditModal('editModal')"
                    class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                    Batal
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- addNoteModal --}}
<div id="addNoteModal" class="fixed inset-0 bg-white bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-1/3 max-h-[80vh] overflow-y-auto">
        <h2 class="text-lg font-bold text-primary mb-4">Tambah Catatan</h2>

        <form action="{{ route('update-repair-notes-sarpras') }}" method="POST">
            @csrf
            <input name="id" id="historieId" type="hidden">
            <input name="id_user" id="userId" type="hidden">
            <div class="mb-4">
                <label class="block text-gray-600 mb-1">Catatan<span class="text-red-600">*</span></label>
                <textarea id="editNoteText" required name="repair_notes"
                    class="input w-full bg-gray-100 text-black h-32 resize-none rounded-md p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary"
                    placeholder="Tulis catatan di sini..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAddNoteModal()"
                    class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                    Batal
                </button>
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <form action="{{ route('update-schedule') }}" method="POST">
            @csrf
            <input type="hidden" name="id_report" id="reportIdInput">
            <input type="hidden" name="id_user" id="userIdInput">
            <h2 class="text-lg font-bold mb-4 text-primary">Jadwalkan Perbaikan</h2>
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">Tanggal Perbaikan</label>
                <input id="repairDate" type="date" name="repair_date"
                    class="w-full border border-gray-300 rounded p-2 bg-white text-gray-700">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeScheduleModal('scheduleModal')"
                    class="px-4 py-2 rounded bg-gray-300 text-black">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-green-500 text-white">Jadwalkan</button>
            </div>
        </form>
    </div>
</div>

<div id="repairModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <form action="{{ route('upload-perbaikan-sarpras') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_report" id="repairReportId">
            <input type="hidden" name="id_user" id="repairUserId">
            <h2 class="text-lg font-bold mb-4 text-primary">Upload Bukti Perbaikan</h2>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Bukti Perbaikan</label>
                <input type="file" name="repair_photo" required
                    class="w-full text-gray-700 border border-gray-300 rounded p-2 bg-white">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Perbaikan</label>
                <textarea name="repair_description" rows="3" required
                    class="w-full text-gray-700 border border-gray-300 rounded p-2 bg-white"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRepairModal('repairModal')"
                    class="px-4 py-2 rounded bg-gray-300 text-black">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-green-500 text-white">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="teknisiModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 mx-4">
        <form action="{{ route('create-teknisi-sarpras') }}" method="POST">
            @csrf
            <input type="hidden" name="id_report" id="teknisiReportId">
            <h2 class="text-lg font-bold mb-4 text-primary">Isi Nama Penanggung Jawab (Teknisi)</h2>
            <div id="teknisiSelectContainer">
                <div class="teknisi-select-group mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Teknisi</label>
                    <select name="nama_teknisi[]" required class="input w-full bg-gray-100 text-black">
                        <option value="" disabled selected>-- Pilih Teknisi --</option>
                        @foreach ($TeknisiLists as $teknisi)
                        <option value="{{ $teknisi->id }}">{{ $teknisi->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="remove-btn hidden text-red-500 font-bold text-lg"
                    onclick="removeTeknisiField(this)">×</button>
            </div>
            <button type="button" onclick="addTeknisiSelect()" class="mb-4 text-sm text-blue-600 hover:underline">+
                Tambah Teknisi</button>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan</label>
                <textarea name="deskripsi_pekerjaan" required
                    class="w-full border border-gray-300 rounded p-2 bg-white text-gray-700" rows="4"
                    placeholder="Masukkan deskripsi pekerjaan..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeTeknisiModal('teknisiModal')"
                    class="px-4 py-2 rounded bg-gray-300 text-black">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-green-500 text-white">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="fixed inset-0 bg-white bg-opacity-50 flex items-center justify-center hidden z-[9999]">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <form action="{{ route('reject-repair') }}" method="POST">
            @csrf
            <input type="hidden" name="id_report" id="rejectReportId">
            <input type="hidden" name="id_user" id="rejectUserId">
            <h2 class="text-lg font-bold mb-4 text-primary">Alasan Penolakan</h2>
            <textarea id="rejectReason" rows="4" name="repair_notes"
                class="w-full border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white"
                placeholder="Tulis alasan penolakan di sini..." style="color: black;"></textarea>
            <div class="mt-4 flex justify-end space-x-2">
                <button onclick="closeRejectModal('rejectModal')" type="button"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Kirim</button>
            </div>
        </form>
    </div>
</div>


@section('scripts')
<script>
    const repairReports = @json($RepairReports);
    console.log(repairReports);

    let riwayatLaporan = [];
    let currentReportId = null;

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

    function addTeknisiSelect() {
        const container = document.getElementById('teknisiSelectContainer');
        const selectHTML = `
            <div class="teknisi-select-group mb-4 flex gap-2 items-start">
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Teknisi</label>
                    <select name="nama_teknisi[]" required class="input w-full bg-gray-100 text-black">
                        <option value="" selected>-- Pilih Teknisi --</option>
                        @foreach ($TeknisiLists as $teknisi)
                            <option value="{{ $teknisi->id }}">{{ $teknisi->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button"
                    class="remove-btn text-red-500 font-bold text-lg h-10 self-end"
                    onclick="removeTeknisiField(this)">×</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', selectHTML);
    }

    function removeTeknisiField(button) {
        button.closest('.teknisi-select-group').remove();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const statusCells = document.querySelectorAll('.status-cell');

        statusCells.forEach(cell => {
            const status = cell.getAttribute('data-status');
            const classes = getStatusLabelClass(status);
            cell.classList.add(...classes.split(' '));
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Tangkap semua baris tbody
        const rows = document.querySelectorAll('#laporanBody tr');

        rows.forEach(row => {
            row.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                if (id) {
                    showDetail(id);
                }
            });
        });
    });

    document.getElementById('entries').addEventListener('input', function() {
        const entries = parseInt(this.value) || 10;
        renderTable(laporanData.slice(0, entries));
    });

    document.getElementById('search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        const filtered = laporanData.filter(l =>
            l.pengajuan.toLowerCase().includes(q) || l.gedung.toLowerCase().includes(q)
        );
        renderTable(filtered);
    });

    function showDetail(id) {
        currentReportId = id;
        const laporan = repairReports.find(r => r.id === parseInt(id));
        console.log(laporan);
        if (!laporan) return;
        console.log(laporan.latest_history.id);

        const rejectButton = document.getElementById('rejectButton');
        const approveButton = document.getElementById('approveButton');
        const addNoteButton = document.getElementById('addNoteButton');
        const status = laporan.status.toLowerCase();

        rejectButton.setAttribute('data-id-report', laporan.id);
        rejectButton.setAttribute('data-id-user', laporan.id_user);
        if (status === 'selesai') {
            rejectButton.classList.add('hidden');
            approveButton.classList.add('hidden');
            addNoteButton.classList.add('hidden');
        } else {
            rejectButton.classList.remove('hidden');

            if (status === 'diproses') {
                approveButton.classList.remove('hidden');
            } else {
                approveButton.classList.add('hidden');
            }

            // Tambah kontrol tombol "Tambah Catatan"
            if (status === 'dalam proses pengerjaan' || status === 'pengecekan akhir') {
                addNoteButton.classList.remove('hidden');
                if (laporan.latest_history && laporan.latest_history.id) {
                    addNoteButton.setAttribute('data-id-history', laporan.latest_history.id);
                    addNoteButton.setAttribute('data-id-user', laporan.id_user);
                } else {
                    addNoteButton.setAttribute('data-id', '');
                }
            } else {
                addNoteButton.classList.add('hidden');
            }
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

        // Tampilkan atau sembunyikan tombol berdasarkan status laporan
        if (laporan.status.toLowerCase() === 'selesai') {
            rejectButton.classList.add('hidden');
            approveButton.classList.add('hidden');
        } else if (laporan.status.toLowerCase() !== 'diproses') {
            rejectButton.classList.remove('hidden');
            approveButton.classList.add('hidden');
        } else {
            rejectButton.classList.remove('hidden');
            approveButton.classList.remove('hidden');
        }

        let ekstra = '';
        if (laporan.teknisi && laporan.jadwal) {
            ekstra = `
        <tr><td class="px-6 py-3 font-medium">Teknisi</td><td class="px-6 py-3">${laporan.teknisi}</td></tr>
        <tr><td class="px-6 py-3 font-medium">Jadwal</td><td class="px-6 py-3">${laporan.jadwal}</td></tr>`;
        }

        let opsiStatus = '';
        if (laporan.status !== 'Diproses') {
            opsiStatus = `
        <tr>
            <td colspan="2" class="px-6 py-3 text-right">
                <form action="${updateStatusUrl}" method="POST">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="id_report" value="${laporan.id}">
                    <input type="hidden" name="id_user" value="${laporan.id_user}">
                    <select name="status" onchange="handleStatusChange(this, ${laporan.id})" class="border rounded px-2 py-1 bg-white">
                        <option value="">Ubah Status</option>
                        <option value="Dalam proses pengerjaan" ${laporan.status === 'Dalam proses pengerjaan' ? 'selected' : ''}>Dalam proses pengerjaan</option>
                        <option value="Pengecekan akhir" ${laporan.status === 'Pengecekan akhir' ? 'selected' : ''}>Pengecekan akhir</option>
                        <option value="Selesai" ${laporan.status === 'Selesai' ? 'selected' : ''}>Selesai</option>
                    </select>
                </form>
            </td>
        </tr>
        `;
        }

        // Ambil history dengan status "Pengecekan akhir"
        const pengecekanAkhirHistory = laporan.histories?.find(
            (item) => item.status === "Pengecekan akhir"
        );

        // Ambil foto perbaikan jika ada di status "Pengecekan akhir"
        const repairPhoto = pengecekanAkhirHistory?.damage_photo ?
            `<img src="/storage/${pengecekanAkhirHistory.damage_photo}" alt="Foto Perbaikan" class="max-w-xs max-h-48 rounded border border-gray-300 bukti-preview cursor-pointer" />` :
            '-';

        document.getElementById('detailContent').innerHTML = `
        <tr><td class="px-6 py-3 font-semibold">Nomor Pengajuan</td><td class="px-6 py-3">${String(laporan.id).padStart(4, '0')}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Pengirim Laporan</td><td class="px-6 py-3">${laporan.user?.email ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Status Laporan Terkini</td><td class="px-6 py-3">${laporan.status}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Tanggal Diajukan</td><td class="px-6 py-3">${formatDateUTC(laporan.created_at)}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Gedung</td><td class="px-6 py-3">${laporan.building?.building_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Ruangan</td><td class="px-6 py-3">${laporan.room?.room_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Fasilitas Gedung</td><td class="px-6 py-3">${laporan.building_facility?.facility_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Fasilitas Ruangan</td><td class="px-6 py-3">${laporan.room_facility?.facility_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Dampak Kerusakan</td><td class="px-6 py-3">${laporan.damage_impact}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Bukti Kerusakan</td><td class="px-6 py-3">${laporan.damage_photo? `<img src="/storage/${laporan.damage_photo}" alt="Bukti Kerusakan" class="max-w-xs max-h-48 rounded border border-gray-300 bukti-preview cursor-pointer" />` : '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Foto Perbaikan</td><td class="px-6 py-3">${repairPhoto}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Deskripsi Kerusakan</td><td class="px-6 py-3">${laporan.damage_description}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Tanggal Perbaikan</td><td class="px-6 py-3">${laporan.schedules?.repair_date ?? '-'}</td></tr>
        ${ekstra}
        ${opsiStatus}
    `;

        document.getElementById('approveButton').setAttribute('data-id-report', laporan.id);
        document.getElementById('approveButton').setAttribute('data-id-user', laporan.id_user);

        document.getElementById('editForm').action = `/laporan/sarpras/${laporan.id}`;
        openModal('detailModal');

        const deleteButton = document.getElementById('deleteButton');
        deleteButton.setAttribute('data-id', laporan.id);
        const deleteForm = document.getElementById('deleteForm').action = `/delete-report/${laporan.id}`;

        if (laporan.status.toLowerCase() === 'diproses' || laporan.status.toLowerCase() === 'ditolak') {
            deleteButton.classList.remove('hidden');
            deleteInput.value = laporan.id;
        } else {
            deleteButton.classList.add('hidden');
            deleteInput.value = '';
        }

    }

    function ubahStatus(statusBaru) {
        const laporanIndex = laporanData.findIndex(l => l.pengajuan === currentReportId);
        if (laporanIndex !== -1 && statusBaru) {
            const laporan = laporanData[laporanIndex];
            laporan.status = statusBaru;
            laporan.riwayatStatus.push({
                status: statusBaru,
                tanggal: new Date().toISOString().split('T')[0]
            });

            if (statusBaru === 'Selesai') {
                riwayatLaporan.push(laporan);
                laporanData.splice(laporanIndex, 1);
            }

            closeModal('detailModal');
        }
    }

    function openScheduleModal(button) {
        const reportId = button.getAttribute('data-id-report');
        const userId = button.getAttribute('data-id-user');
        console.log(reportId);
        document.getElementById('reportIdInput').value = reportId;
        document.getElementById('userIdInput').value = userId;
        openModal('scheduleModal');
    }

    function closeScheduleModal() {
        document.getElementById('scheduleModal').classList.add('hidden');
    }

    function openEditModal() {
        // Tampilkan modal edit
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function closeRepairModal() {
        document.getElementById('repairModal').classList.add('hidden');
    }

    function closeTeknisiModal() {
        document.getElementById('teknisiModal').classList.add('hidden');
    }

    function openAddNoteModal() {
        const idHistory = document.getElementById("addNoteButton").getAttribute("data-id-history");
        const idUser = document.getElementById("addNoteButton").getAttribute("data-id-user");

        document.getElementById("historieId").value = idHistory;
        document.getElementById("userId").value = idUser;
        document.getElementById('addNoteModal').classList.remove('hidden');
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

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function openRejectModal() {
        const id = document.getElementById("rejectButton").getAttribute("data-id-report");
        const idUser = document.getElementById("rejectButton").getAttribute("data-id-user");

        if (!id) {
            alert("ID tidak tersedia.");
            return;
        }

        document.getElementById("rejectReportId").value = id;
        document.getElementById("rejectUserId").value = idUser;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    function closeAddNoteModal() {
        document.getElementById('addNoteModal').classList.add('hidden');
    }


    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function handleStatusChange(selectEl, reportId) {
        const status = selectEl.value;

        if (status === "Pengecekan akhir") {
            document.getElementById('repairReportId').value = reportId;
            openModal('repairModal');
        } else if (status === "Selesai") {
            document.getElementById('teknisiReportId').value = reportId;
            openModal('teknisiModal');
        } else {
            selectEl.form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const selectedId = @json($selectedId);

        if (selectedId) {
            // Trigger klik pada baris yang sesuai atau langsung buka modal
            const targetRow = document.querySelector(`[data-id='${selectedId}']`);
            if (targetRow) {
                targetRow.click(); // Pakai cara yang sama untuk buka modal
            } else {
                console.warn("Data ID tidak ditemukan di tabel.");
            }
        }
    });

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');

        // Ubah URL, hapus query parameter id tanpa reload halaman
        if (window.history.replaceState) {
            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({
                path: newUrl
            }, '', newUrl);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search');
        const entriesInput = document.getElementById('entries');
        const tableBody = document.getElementById('laporanBody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');

        let currentPage = 1;

        function filterRows() {
            const searchValue = searchInput.value.toLowerCase();

            return rows.filter(row => {
                return row.textContent.toLowerCase().includes(searchValue);
            });
        }

        function renderTable() {
            const entries = parseInt(entriesInput.value) || 10;
            const filtered = filterRows();
            const totalPages = Math.ceil(filtered.length / entries);

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * entries;
            const end = start + entries;

            // Clear table
            tableBody.innerHTML = '';

            // Append filtered rows
            filtered.slice(start, end).forEach(row => {
                tableBody.appendChild(row);
            });

            // Disable/enable buttons
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
        }

        // Event listeners
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            renderTable();
        });

        entriesInput.addEventListener('input', () => {
            if (entriesInput.value < 1) entriesInput.value = 1;
            currentPage = 1;
            renderTable();
        });

        prevBtn.addEventListener('click', () => {
            currentPage--;
            renderTable();
        });

        nextBtn.addEventListener('click', () => {
            currentPage++;
            renderTable();
        });

        // Initial render
        renderTable();
    });
</script>
@endsection