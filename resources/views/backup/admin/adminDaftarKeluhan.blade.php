@extends('layout.adminLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Daftar Keluhan')
</head>

@section('content')

<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Keluhan</h1>
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
                <input id="search" type="text" placeholder="Cari keluhan..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-64 text-sm" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="keluhanTable" class="table w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-white uppercase bg-primary">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nomor Pengajuan</th>
                        <th class="px-6 py-3">Email Pengirim</th>
                        <th class="px-6 py-3">Deskripsi Keluhan</th>
                        <th class="px-6 py-3">Lokasi</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="keluhanBody">
                    @foreach($Complaints as $index => $complaint)
                    <tr class="bg-white border-b hover:bg-gray-100">
                        <td class="px-6 py-3">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">{{ str_pad($complaint->id_report, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-3">{{ $complaint->user->email ?? '' }}</td>
                        <td class="px-6 py-3">{{ $complaint->complaint_description ?? '' }}</td>
                        <td class="px-6 py-3">{{ $complaint->repairReport->building->building_name ?? '' }}</td>
                        <td class="px-6 py-3">
                            <button class="text-primary hover:underline detailBtn" data-id="{{ $complaint->id }}"
                                data-email="{{ $complaint->user->email ?? 'Anonim' }}"
                                data-deskripsi="{{ $complaint->complaint_description }}"
                                data-report="{{ $complaint->id_report }}"
                                data-building="{{ $complaint->repairReport->building->building_name }}">
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

<!-- Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg w-[500px] p-6 shadow-lg relative">
        <h2 class="text-lg font-bold text-primary mb-4">Detail Keluhan</h2>

        <div class="mb-3 flex">
            <label class="w-48 font-semibold text-primary">Nomor Pengajuan</label>
            <p id="modalReportId" class="text-gray-700"></p>
        </div>

        <div class="mb-3 flex">
            <label class="w-48 font-semibold text-primary">Email Pengirim</label>
            <p id="modalEmail" class="text-gray-700"></p>
        </div>

        <div class="mb-3 flex">
            <label class="w-48 font-semibold text-primary">Deskripsi Keluhan</label>
            <p id="modalDeskripsi" class="text-gray-700"></p>
        </div>

        <div class="mb-6 flex">
            <label class="w-48 font-semibold text-primary">Lokasi</label>
            <p id="modalGedung" class="text-gray-700"></p>
        </div>

        <div class="flex justify-end gap-2">
            <a id="shortcutLink" href="#" class="bg-primary text-white px-4 py-2 rounded hover:bg-opacity-90 text-sm">
                Lihat Laporan Perbaikan
            </a>
            <button onclick="closeModal()"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 text-sm">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('detailModal');
    const buttons = document.querySelectorAll('.detailBtn');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const email = button.getAttribute('data-email');
            const deskripsi = button.getAttribute('data-deskripsi');
            const report = button.getAttribute('data-report');
            const gedung = button.getAttribute('data-building');

            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalDeskripsi').textContent = deskripsi;
            document.getElementById('modalReportId').textContent = report;
            document.getElementById('modalGedung').textContent = gedung;

            document.getElementById('shortcutLink').setAttribute('href',
                `/admin-daftar-permintaan-perbaikan?id=${report}`);

            modal.classList.remove('hidden');
        });
    });
});

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
}
</script>
@endpush

<script>
const complaint = @json($Complaints);
console.log(complaint)
</script>