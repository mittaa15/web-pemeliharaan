@extends('layout.adminLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Dashboard')
</head>

@section('content')
<div class="container mx-auto px-4 mb-8">

    @php
    $cards = [
    ['title' => 'Sedang Diproses', 'value' => $sedangDiproses, 'icon' => '⏳', 'bg' => 'bg-primary', 'text' =>
    'text-white'],
    ['title' => 'Perbaikan Selesai', 'value' => $perbaikanSelesai, 'icon' => '✅', 'bg' => 'bg-primary', 'text' =>
    'text-white'],
    ['title' => 'Ditolak', 'value' => $perbaikanDitolak, 'icon' => '❌', 'bg' => 'bg-primary', 'text' => 'text-white'],
    ['title' => 'Laporan Keluhan', 'value' => 1, 'icon' => '📢', 'bg' => 'bg-primary', 'text' => 'text-white'],
    ];
    @endphp

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

        /* Membuat tabel responsif: 
       Scroll horizontal hanya di layar >= md (768px) */
        @media (min-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }

        /* Di layar kecil, padding dan font lebih kecil supaya muat */
        @media (max-width: 767px) {
            table {
                font-size: 0.75rem;
                /* 12px */
            }

            th,
            td {
                padding: 0.5rem 0.75rem;
                /* padding lebih kecil */
            }
        }
    </style>

    <!-- Wrapper dengan background putih -->
    <div class="bg-white rounded-xl shadow-md p-6">

        <!-- Judul -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
        </div>
        <p class="text-gray-500 mt-1 text-sm">Pantau status laporan pemeliharaan dengan mudah dan cepat.</p>

        <!-- Card Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
            @foreach ($cards as $card)
            <div
                class="rounded-xl shadow-sm p-5 {{ $card['bg'] }} {{ $card['text'] }} transition duration-200 hover:scale-[1.02]">
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
            <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">5 Laporan Terakhir</h3>
            <div class="table-responsive bg-white shadow rounded-lg">
                <table class="min-w-full text-sm text-left text-gray-700">
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
                            <td class="px-4 py-4">{{ $index + 1 }}</td>
                            <td class="px-4 py-4">{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-4">{{ $laporan->user->email ?? '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="status-cell text-xs font-semibold px-2.5 py-0.5 rounded"
                                    data-status="{{ $laporan->status }}">
                                    {{ ucfirst($laporan->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                {{ \Carbon\Carbon::parse($laporan->created_at)->format('d M Y') }}
                            </td>
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

    </div> <!-- Penutup bg putih -->
</div>
@endsection

@section('scripts')
@endsection