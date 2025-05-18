@extends('layout.adminLayout')

@section('content')
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    </div>
    <p class="text-gray-500 mt-1">Pantau status laporan pemeliharaan dengan mudah dan cepat.</p>
</div>

<!-- Card Section -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
@endsection

@section('scripts')
@endsection