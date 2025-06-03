@extends('layout.sarprasLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Dashboard')
</head>

@section('content')
<div class="w-full max-w-screen-xl mx-auto px-3 sm:px-4 mt-20">
    <div class="bg-white p-4 sm:p-8 rounded-xl shadow-md w-full">
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Dashboard</h2>
            </div>
            <p class="text-gray-500 mt-1 text-sm sm:text-base">Pantau status laporan pemeliharaan dengan mudah dan
                cepat.</p>
        </div>

        <style>
            .status-cell[data-status="Diproses"] {
                background-color: #DBEAFE;
                color: #1E40AF;
            }

            .status-cell[data-status="Ditolak"] {
                background-color: #FECACA;
                color: #991B1B;
            }

            .status-cell[data-status="Dijadwalkan"] {
                background-color: #E0E7FF;
                color: #3730A3;
            }

            .status-cell[data-status="Dalam proses pengerjaan"] {
                background-color: #FEF3C7;
                color: #92400E;
            }

            .status-cell[data-status="Pengecekan akhir"] {
                background-color: #EDE9FE;
                color: #6B21A8;
            }

            .status-cell[data-status="Selesai"] {
                background-color: #D1FAE5;
                color: #065F46;
            }
        </style>

        <!-- Card Summary Section -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-5 mb-10 w-full">
            @php
            $cards = [
            ['title' => 'Total Permintaan Perbaikan', 'value' => $permintaanPerbaikan, 'icon' => '🛠', 'bg' =>
            'bg-primary', 'text' => 'text-white'],
            ['title' => 'Sedang Diproses', 'value' => $sedangDiproses, 'icon' => '⏳', 'bg' => 'bg-primary', 'text' =>
            'text-white'],
            ['title' => 'Ditolak', 'value' => $perbaikanDitolak, 'icon' => '❌', 'bg' => 'bg-primary', 'text' =>
            'text-white'],
            ['title' => 'Perbaikan Selesai', 'value' => $perbaikanSelesai, 'icon' => '✅', 'bg' => 'bg-primary', 'text'
            => 'text-white'],
            ];
            @endphp

            @foreach ($cards as $card)
            <div class="rounded-xl shadow-sm p-3 sm:p-5 {{ $card['bg'] }} {{ $card['text'] }} w-full min-h-[90px]">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs sm:text-sm font-semibold">{{ $card['title'] }}</div>
                        <div class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2">{{ $card['value'] }}</div>
                    </div>
                    <div class="text-2xl sm:text-4xl">
                        {{ $card['icon'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- 5 Laporan Terakhir -->
        <div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">5 Laporan Terakhir</h3>
            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="w-full text-sm text-left text-gray-700 whitespace-nowrap">
                    <thead class="bg-primary text-white uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nomor Pengajuan</th>
                            <th class="px-4 py-3">Pelapor</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanTerakhir as $index => $laporan)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3">{{ $laporan->user->email ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="status-cell text-xs font-semibold px-2.5 py-0.5 rounded"
                                    data-status="{{ $laporan->status }}">
                                    {{ ucfirst($laporan->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($laporan->created_at)->format('d M Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">Belum ada laporan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection