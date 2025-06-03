@extends('layout.sarprasLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-primary font-bold text-xl">Data Teknisi</h1>
            <div>
                <input id="search" type="text" placeholder="Cari teknisi..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-64 text-sm" />
            </div>
        </div>
        <hr class="border-black mb-6">

        <table class="table w-full text-sm text-left text-gray-600 border">
            <thead class="bg-primary text-xs uppercase text-white">
                <tr>
                    <th class="px-6 py-3">Nama Teknisi</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Telepon</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($technicians as $technician)
                <tr class="technician-row" data-id="{{ $technician->id }}" data-name="{{ $technician->name }}"
                    data-email="{{ $technician->email }}" data-phone="{{ $technician->phone_number }}">
                    <td class="px-6 py-3">{{ $technician->name }}</td>
                    <td class="px-6 py-3">{{ $technician->email }}</td>
                    <td class="px-6 py-3">{{ $technician->phone_number }}</td>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <form id="deleteTechnicianForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection