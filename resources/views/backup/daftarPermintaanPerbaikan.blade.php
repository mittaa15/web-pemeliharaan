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
                <tbody id="laporanBody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Detail -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 relative">
        <h2 class="text-xl font-bold mb-4">Detail Laporan</h2>
        <table class="w-full text-sm text-gray-600" id="detailContent"></table>
        <div class="mt-6 flex justify-end gap-3">
            <button id="rejectButton" onclick="openModal('rejectModal')"
                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Tolak</button>
            <button id="approveButton" onclick="openModal('scheduleModal')"
                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Setujui</button>
            <button onclick="closeModal('detailModal')" class="bg-gray-300 text-black px-4 py-2 rounded">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal: Tolak -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-bold mb-4">Tolak Laporan</h2>
        <textarea id="rejectReason" class="w-full border border-gray-300 rounded p-2"
            placeholder="Alasan penolakan..."></textarea>
        <div class="mt-4 flex justify-end gap-3">
            <button onclick="closeModal('rejectModal')" class="px-4 py-2 rounded bg-gray-300">Batal</button>
            <button onclick="submitRejection()" class="px-4 py-2 rounded bg-red-500 text-white">Kirim</button>
        </div>
    </div>
</div>

<!-- Modal: Jadwal Perbaikan -->
<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-bold mb-4">Jadwalkan Perbaikan</h2>
        <div class="mb-4">
            <label class="block mb-1 text-sm font-medium text-gray-700">Nama Teknisi</label>
            <input id="technicianName" type="text" class="w-full border border-gray-300 rounded p-2"
                placeholder="Masukkan nama teknisi">
        </div>
        <div class="mb-4">
            <label class="block mb-1 text-sm font-medium text-gray-700">Tanggal Perbaikan</label>
            <input id="repairDate" type="date" class="w-full border border-gray-300 rounded p-2">
        </div>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('scheduleModal')" class="px-4 py-2 rounded bg-gray-300">Batal</button>
            <button onclick="submitSchedule()" class="px-4 py-2 rounded bg-green-500 text-white">Jadwalkan</button>
        </div>
    </div>
</div>

<script>
let laporanData = [{
        no: 1,
        pengajuan: 'KD001',
        gedung: 'Gedung A',
        status: 'Diproses',
        waktu: '2025-04-21',
        riwayatStatus: [],
        teknisi: null,
        jadwal: null
    },
    {
        no: 2,
        pengajuan: 'KD002',
        gedung: 'Gedung B',
        status: 'Diproses',
        waktu: '2025-04-23',
        riwayatStatus: [],
        teknisi: null,
        jadwal: null
    },
];

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

function renderTable(data) {
    const tableBody = document.getElementById('laporanBody');
    tableBody.innerHTML = '';
    data.forEach((laporan) => {
        const statusClass = getStatusLabelClass(laporan.status);
        tableBody.innerHTML += `
            <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-6 py-4">${laporan.no}</td>
                <td class="px-6 py-4">${laporan.pengajuan}</td>
                <td class="px-6 py-4">${laporan.gedung}</td>
                <td class="px-6 py-4">
                    <span class="${statusClass} text-xs font-semibold px-2.5 py-0.5 rounded">${laporan.status}</span>
                </td>
                <td class="px-6 py-4">${laporan.waktu}</td>
                <td class="px-6 py-4">
                    <button class="text-primary hover:underline" onclick="showDetail('${laporan.pengajuan}')">Lihat Detail</button>
                </td>
            </tr>
        `;
    });
}

document.getElementById('entries').addEventListener('input', function() {
    const entries = parseInt(this.value) || 10;
    renderTable(laporanData.slice(0, entries));
});

document.getElementById('search').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const filtered = laporanData.filter(l => l.pengajuan.toLowerCase().includes(q) || l.gedung.toLowerCase()
        .includes(q));
    renderTable(filtered);
});

function showDetail(id) {
    currentReportId = id;
    const laporan = laporanData.find(l => l.pengajuan === id);
    if (!laporan) return;

    let ekstra = '';
    if (laporan.teknisi && laporan.jadwal) {
        ekstra = ` 
        <tr><td class="px-6 py-3 font-medium">Teknisi</td><td class="px-6 py-3">${laporan.teknisi}</td></tr>
        <tr><td class="px-6 py-3 font-medium">Jadwal</td><td class="px-6 py-3">${laporan.jadwal}</td></tr>`;
    }

    let opsiStatus = '';
    if (laporan.status === 'Dijadwalkan') {
        opsiStatus = ` 
        <tr><td colspan="2" class="px-6 py-3 text-right">
            <select id="statusSelect" onchange="ubahStatus(this.value)" class="border rounded px-2 py-1">
                <option value="">Ubah Status</option>
                <option value="Dalam proses pengerjaan" ${laporan.status === 'Dalam proses pengerjaan' ? 'selected' : ''}>Dalam proses pengerjaan</option>
                <option value="Pengecekan akhir" ${laporan.status === 'Pengecekan akhir' ? 'selected' : ''}>Pengecekan akhir</option>
                <option value="Selesai" ${laporan.status === 'Selesai' ? 'selected' : ''}>Selesai</option>
            </select>
        </td></tr>`;
    }

    document.getElementById('detailContent').innerHTML = ` 
        <tr><td class="px-6 py-3 font-medium">Nomor</td><td class="px-6 py-3">${laporan.pengajuan}</td></tr>
        <tr><td class="px-6 py-3 font-medium">Gedung</td><td class="px-6 py-3">${laporan.gedung}</td></tr>
        <tr><td class="px-6 py-3 font-medium">Status</td><td class="px-6 py-3">${laporan.status}</td></tr>
        <tr><td class="px-6 py-3 font-medium">Tanggal</td><td class="px-6 py-3">${laporan.waktu}</td></tr>
        ${ekstra}
        ${opsiStatus}
    `;

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

        // Jika status berubah menjadi 'Selesai', pindahkan laporan ke riwayat
        if (statusBaru === 'Selesai') {
            riwayatLaporan.push(laporan);
            laporanData.splice(laporanIndex, 1); // Hapus dari laporanData
        }

        // Tidak menutup modal agar status bisa terus diubah
        renderTable(laporanData);
    }
}

function submitRejection() {
    const reason = document.getElementById('rejectReason').value;
    if (!reason.trim()) return alert('Isi alasan!');
    const laporan = laporanData.find(l => l.pengajuan === currentReportId);
    if (laporan) {
        laporan.status = 'Ditolak';
        laporan.riwayatStatus.push({
            status: 'Ditolak',
            tanggal: new Date().toISOString().split('T')[0]
        });
        closeModal('rejectModal');
        closeModal('detailModal');
        renderTable(laporanData);

        // Sembunyikan tombol setelah penolakan
        const approveButton = document.querySelector('#detailModal .bg-green-500');
        const rejectButton = document.querySelector('#detailModal .bg-red-500');
        if (approveButton && rejectButton) {
            approveButton.classList.add('hidden');
            rejectButton.classList.add('hidden');
        }
    }
}

function submitSchedule() {
    const date = document.getElementById('repairDate').value;
    const tech = document.getElementById('technicianName').value;
    if (!date || !tech) return alert('Isi semua data!');
    const laporan = laporanData.find(l => l.pengajuan === currentReportId);
    if (laporan) {
        laporan.status = 'Dijadwalkan';
        laporan.teknisi = tech;
        laporan.jadwal = date;
        laporan.riwayatStatus.push({
            status: 'Dijadwalkan',
            tanggal: new Date().toISOString().split('T')[0]
        });
        closeModal('scheduleModal');
        closeModal('detailModal');
        renderTable(laporanData);

        // Sembunyikan tombol setelah persetujuan
        const approveButton = document.querySelector('#detailModal .bg-green-500');
        const rejectButton = document.querySelector('#detailModal .bg-red-500');
        if (approveButton && rejectButton) {
            approveButton.classList.add('hidden');
            rejectButton.classList.add('hidden');
        }
    }
}

function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

window.onload = function() {
    renderTable(laporanData);
};
</script>
@endsection