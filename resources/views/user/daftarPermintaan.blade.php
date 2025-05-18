@extends('layout.userLayout')

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
                    {{-- Dynamic content will be injected via JS --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-2/3">
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
                    {{-- Konten detail laporan ditampilkan di sini --}}
                </tbody>
            </table>
        </div>
        <div class="text-right space-x-2" id="modalButtons">
            <button onclick="closeModal()" class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
let laporanData = [{
        no: 1,
        pengajuan: 'KD001',
        gedung: 'Gedung A',
        status: 'Diproses',
        waktu: '2025-04-21',
        riwayatStatus: [{
                status: 'Diajukan',
                tanggal: '2025-04-15'
            },
            {
                status: 'Diproses',
                tanggal: '2025-04-21'
            }
        ]
    },
    {
        no: 2,
        pengajuan: 'KD002',
        gedung: 'Gedung B',
        status: 'Ditolak',
        waktu: '2025-04-23',
        riwayatStatus: [{
                status: 'Diajukan',
                tanggal: '2025-04-10'
            },
            {
                status: 'Ditolak',
                tanggal: '2025-04-23'
            }
        ]
    },
    {
        no: 3,
        pengajuan: 'KD003',
        gedung: 'Gedung C',
        status: 'Dijadwalkan',
        waktu: '2025-04-22',
        riwayatStatus: [{
                status: 'Diajukan',
                tanggal: '2025-04-18'
            },
            {
                status: 'Dijadwalkan',
                tanggal: '2025-04-22'
            }
        ]
    },
    {
        no: 4,
        pengajuan: 'KD004',
        gedung: 'Gedung D',
        status: 'Dalam proses pengerjaan',
        waktu: '2025-04-21',
        riwayatStatus: [{
                status: 'Diajukan',
                tanggal: '2025-04-10'
            },
            {
                status: 'Dalam proses pengerjaan',
                tanggal: '2025-04-21'
            }
        ]
    },
    {
        no: 5,
        pengajuan: 'KD005',
        gedung: 'Gedung E',
        status: 'Pengecekan akhir',
        waktu: '2025-04-20',
        riwayatStatus: [{
                status: 'Diajukan',
                tanggal: '2025-04-12'
            },
            {
                status: 'Pengecekan akhir',
                tanggal: '2025-04-20'
            }
        ]
    },
    {
        no: 6,
        pengajuan: 'KD006',
        gedung: 'Gedung F',
        status: 'Selesai',
        waktu: '2025-04-19',
        riwayatStatus: [{
                status: 'Diajukan',
                tanggal: '2025-04-05'
            },
            {
                status: 'Selesai',
                tanggal: '2025-04-19'
            }
        ]
    }
];

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
        const row = `
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
        tableBody.innerHTML += row;
    });
}

document.getElementById('entries').addEventListener('input', function() {
    const entries = parseInt(this.value) || 10;
    renderTable(laporanData.slice(0, entries));
});

document.getElementById('search').addEventListener('input', function() {
    const searchQuery = this.value.toLowerCase();
    const filteredData = laporanData.filter(laporan =>
        laporan.pengajuan.toLowerCase().includes(searchQuery) ||
        laporan.gedung.toLowerCase().includes(searchQuery)
    );
    renderTable(filteredData);
});

function showDetail(reportId) {
    const modal = document.getElementById('detailModal');
    const detailTitle = document.getElementById('detailTitle');
    const detailContent = document.getElementById('detailContent');
    const modalButtons = document.getElementById('modalButtons');

    const laporan = laporanData.find(l => l.pengajuan === reportId);

    if (laporan) {
        detailTitle.textContent = 'Detail Laporan ' + laporan.pengajuan;

        const dibuat = new Date(laporan.waktu);
        const sekarang = new Date();
        const selisihHari = Math.floor((sekarang - dibuat) / (1000 * 60 * 60 * 24));

        const statusLower = laporan.status.toLowerCase();
        const bolehAjukanKeluhan = selisihHari > 3 && (statusLower === 'diproses' || statusLower === '');

        detailContent.innerHTML = `
            <tr><td class="px-6 py-3 font-medium">Nomor Pengajuan</td><td class="px-6 py-3">${laporan.pengajuan}</td></tr>
            <tr><td class="px-6 py-3 font-medium">Gedung</td><td class="px-6 py-3">${laporan.gedung}</td></tr>
            <tr><td class="px-6 py-3 font-medium">Status</td><td class="px-6 py-3">${laporan.status}</td></tr>
            <tr><td class="px-6 py-3 font-medium">Waktu Pembuatan</td><td class="px-6 py-3">${laporan.waktu}</td></tr>
        `;

        // Reset tombol
        modalButtons.innerHTML = `
            <button onclick="closeModal()" class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
        `;

        if (bolehAjukanKeluhan) {
            modalButtons.innerHTML += `
                <a href="/keluhan/${laporan.pengajuan}" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Ajukan Keluhan
                </a>
            `;
        }

        // Tombol Riwayat Status hanya muncul jika ada riwayat
        if (laporan.riwayatStatus && laporan.riwayatStatus.length > 0) {
            modalButtons.innerHTML += `
                <button onclick="showHistory('${laporan.pengajuan}')"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Riwayat Status
                </button>
            `;
        }

        modal.classList.remove('hidden');
    }
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

function showHistory(reportId) {
    const laporan = laporanData.find(l => l.pengajuan === reportId);
    const modal = document.getElementById('detailModal');
    const modalButtons = document.getElementById('modalButtons');
    const detailContent = document.getElementById('detailContent');

    if (laporan && laporan.riwayatStatus) {
        detailContent.innerHTML = '<tr><th class="px-6 py-3">Tanggal</th><th class="px-6 py-3">Status</th></tr>';
        laporan.riwayatStatus.forEach(item => {
            detailContent.innerHTML += `
                <tr>
                    <td class="px-6 py-3">${item.tanggal}</td>
                    <td class="px-6 py-3">${item.status}</td>
                </tr>
            `;
        });

        modalButtons.innerHTML = `
            <button onclick="closeModal()" class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
        `;

        modal.classList.remove('hidden');
    }
}

renderTable(laporanData);
</script>
@endsection