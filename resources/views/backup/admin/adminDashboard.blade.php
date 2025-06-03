@extends('layout.adminLayout')

@section('content')
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    </div>
    <p class="text-gray-500 mt-1">Pantau status laporan pemeliharaan dengan mudah dan cepat.</p>
</div>

<style>
    .status-cell[data-status="Diproses"] {
        background-color: #DBEAFE;
        /* bg-blue-100 */
        color: #1E40AF;
        /* text-blue-800 */
    }

    .status-cell[data-status="Ditolak"] {
        background-color: #FECACA;
        /* bg-red-100 */
        color: #991B1B;
        /* text-red-800 */
    }

    .status-cell[data-status="Dijadwalkan"] {
        background-color: #E0E7FF;
        /* bg-indigo-100 */
        color: #3730A3;
        /* text-indigo-800 */
    }

    .status-cell[data-status="Dalam proses pengerjaan"] {
        background-color: #FEF3C7;
        /* bg-yellow-100 */
        color: #92400E;
        /* text-yellow-800 */
    }

    .status-cell[data-status="Pengecekan akhir"] {
        background-color: #EDE9FE;
        /* bg-purple-100 */
        color: #6B21A8;
        /* text-purple-800 */
    }

    .status-cell[data-status="Selesai"] {
        background-color: #D1FAE5;
        /* bg-green-100 */
        color: #065F46;
        /* text-green-800 */
    }
</style>

<!-- Card Section -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @php
    $cards = [
    ['title' => 'Permintaan Perbaikan', 'value' => 10, 'icon' => '🛠️', 'bg' => 'bg-primary', 'text' => 'text-white'],
    ['title' => 'Sedang Diproses', 'value' => 10, 'icon' => '⏳', 'bg' => 'bg-primary', 'text' => 'text-white'],
    ['title' => 'Perbaikan Selesai', 'value' => 10, 'icon' => '✅', 'bg' => 'bg-primary', 'text' => 'text-white'],
    ['title' => 'Laporan Keluhan', 'value' => 10, 'icon' => '📢', 'bg' => 'bg-primary', 'text' => 'text-white'],
    ];
    @endphp

    @foreach ($cards as $card)
    <div class="rounded-xl shadow-sm p-5 {{ $card['bg'] }} {{ $card['text'] }}">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-semibold">{{ $card['title'] }}</div>
                <div class="text-3xl font-bold mt-2">{{ $card['value'] }}</div>
            </div>
            <div class="text-4xl">
                {{ $card['icon'] }}
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- 5 Laporan Terakhir -->
<div class="mt-10">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">5 Laporan Terakhir</h3>
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-primary text-white uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Nomor Pengajuan</th>
                    <th class="px-6 py-3">Pelapor</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporanTerakhir as $index => $laporan)
                <tr class="border-b">
                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4">{{ $laporan->user->email ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="status-cell text-xs font-semibold px-2.5 py-0.5 rounded"
                            data-status="{{ $laporan->status }}">
                            {{ ucfirst($laporan->status) }}
                        </span>

                    </td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($laporan->created_at)->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada laporan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@endsection