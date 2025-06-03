@extends('layout.userLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Detail Laporan')
</head>

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Detail Laporan</h1>
        <hr class="border-black mb-6">

        <div class="space-y-4">
            <!-- Status dan Tanggal -->
            <div class="flex justify-between">
                <p><strong>Status:</strong>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                        {{ $report['status'] }}
                    </span>
                </p>
                <p><strong>Tanggal Pelaporan:</strong> {{ $report['tanggal_pelaporan'] }}</p>
            </div>

            <!-- Daftar Kerusakan -->
            <div class="space-y-2">
                <p><strong>Daftar Kerusakan:</strong></p>
                <ul class="list-disc ml-6">
                    @foreach ($report['kerusakan'] as $item)
                    <li>{{ $item['jenis'] }} - {{ $item['lokasi'] }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Komentar -->
            <div>
                <p><strong>Komentar:</strong></p>
                <p>{{ $report['komentar'] }}</p>
            </div>
        </div>

        <!-- Button Kembali -->
        <div class="mt-6">
            <a href="{{ url('/daftar-permintaan') }}"
                class="bg-primary text-white py-2 px-4 rounded hover:bg-primary-dark">
                Kembali ke Daftar Permintaan
            </a>
        </div>
    </div>
</div>
@endsection