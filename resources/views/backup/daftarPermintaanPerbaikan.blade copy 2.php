@extends('layout.sarprasLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Permintaan Laporan</h1>
        <hr class="border-black mb-6">

        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-2 text-gray-600">
                <span>Show</span>
                <input id="entries" type="number" value="10"
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
                <tbody id="laporanBody">
                    @foreach($RepairReports as $index => $report)
                    <tr>
                        <td class="px-6 py-3">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-3">{{ $report->building->building_name ?? '-' }}</td>
                        <td class="px-6 py-3">
                            <span class="status-cell text-xs font-semibold px-2.5 py-0.5 rounded"
                                data-status="{{ $report->status }}">{{ $report->status }}</span>
                        </td>
                        <td class="px-6 py-3">{{ $report->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            <button class="text-primary hover:underline detailBtn"
                                onclick="showDetail('{{ $report->id }}')">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

{{-- MODALS --}}
@include('sarpras.modals.reject')
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 relative">
        <h2 class="text-xl font-bold mb-4 text-primary">Detail Laporan</h2>
        <table class="w-full text-sm text-gray-600" id="detailContent"></table>
        <div class="mt-6 flex justify-end gap-3">
            <button id="rejectButton" onclick="openModal('rejectModal')"
                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Tolak</button>
            <button id="approveButton" data-id="" onclick="openScheduleModal(this)"
                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Setujui
            </button>
            <button onclick="closeModal('detailModal')" class="bg-gray-300 text-black px-4 py-2 rounded">Tutup</button>
        </div>
    </div>
</div>
<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <form action="{{ route('update-schedule') }}" method="POST">
            @csrf
            <input type="hidden" name="id_report" id="reportIdInput">
            <h2 class="text-lg font-bold mb-4 text-primary">Jadwalkan Perbaikan</h2>
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">Nama Teknisi</label>
                <input id="technicianName" type="text" name="technician_name"
                    class="w-full border border-gray-300 rounded p-2 bg-white text-gray-700"
                    placeholder="Masukkan nama teknisi">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700">Tanggal Perbaikan</label>
                <input id="repairDate" type="date" name="repair_date"
                    class="w-full border border-gray-300 rounded p-2 bg-white text-gray-700">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('scheduleModal')"
                    class="px-4 py-2 rounded bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-green-500 text-white">Jadwalkan</button>
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
document.addEventListener('DOMContentLoaded', () => {
    const statusCells = document.querySelectorAll('.status-cell');

    statusCells.forEach(cell => {
        const status = cell.getAttribute('data-status');
        const classes = getStatusLabelClass(status);
        cell.classList.add(...classes.split(' '));
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
    console.log(id);
    if (!laporan) return;

    const approveButton = document.getElementById('approveButton');
    const rejectButton = document.getElementById('rejectButton');

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

    // Sembunyikan tombol jika status bukan 'Diproses'
    if (laporan.status.toLowerCase() !== 'diproses') {
        approveButton.classList.add('hidden');
        rejectButton.classList.add('hidden');
    } else {
        approveButton.classList.remove('hidden');
        rejectButton.classList.remove('hidden');
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
                    <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 bg-white">
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


    document.getElementById('detailContent').innerHTML = `
        <tr><td class="px-6 py-3 font-semibold">Nomor Pengajuan</td><td class="px-6 py-3">${String(laporan.id).padStart(4, '0')}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Status Laporan Terkini</td><td class="px-6 py-3">${laporan.status}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Tanggal Diajukan</td><td class="px-6 py-3">${formatDateUTC(laporan.created_at)}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Gedung</td><td class="px-6 py-3">${laporan.building?.building_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Ruangan</td><td class="px-6 py-3">${laporan.room?.room_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Fasilitas Gedung</td><td class="px-6 py-3">${laporan.building_facility?.facility_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Fasilitas Ruangan</td><td class="px-6 py-3">${laporan.room_facility?.facility_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Dampak Kerusakan</td><td class="px-6 py-3">${laporan.damage_impact}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Deskripsi Kerusakan</td><td class="px-6 py-3">${laporan.damage_description}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Nama Teknisi</td><td class="px-6 py-3">${laporan.schedules?.technician_name ?? '-'}</td></tr>
        <tr><td class="px-6 py-3 font-semibold">Tanggal Perbaikan</td><td class="px-6 py-3">${laporan.schedules?.repair_date ?? '-'}</td></tr>
        ${ekstra}
        ${opsiStatus}
    `;
    document.getElementById('approveButton').setAttribute('data-id', laporan.id);

    openModal('detailModal');
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
    const reportId = button.getAttribute('data-id');
    console.log(reportId);
    document.getElementById('reportIdInput').value = reportId;
    openModal('scheduleModal');
}

function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>
@endsection