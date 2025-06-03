@extends('layout.sarprasLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Data Teknisi')
</head>

@section('content')
<div class="p-4 sm:p-6 md:p-8 mt-20">
    <div class="bg-white rounded-md w-full py-6 px-4 sm:px-6 md:px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Data Teknisi</h1>
        <hr class="border-black mb-6">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 space-y-4 md:space-y-0">
            <div class="flex items-center space-x-2 text-gray-600">
                <span>Show</span>
                <input id="entries" type="number" value="10"
                    class="w-16 text-center border border-gray-300 rounded-md px-2 py-1 bg-white text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary"
                    min="1" />
                <span>entries</span>
            </div>
            <div class="w-full md:w-64">
                <input id="search" type="text" placeholder="Cari laporan..."
                    class="w-full input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary text-sm" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-sm text-left text-gray-600 border">
                <thead class="bg-primary text-xs uppercase text-white">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Teknisi</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Telepon</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($technicians as $technician)
                    <tr class="technician-row" data-id="{{ $technician->id }}" data-name="{{ $technician->name }}"
                        data-email="{{ $technician->email }}" data-phone="{{ $technician->phone_number }}">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $technician->name }}</td>
                        <td class="px-4 py-3">{{ $technician->email }}</td>
                        <td class="px-4 py-3">{{ $technician->phone_number }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <form id="deleteTechnicianForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>


<script>
    document.getElementById('search').addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('.technician-row');

        rows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const email = row.dataset.email.toLowerCase();
            const phone = row.dataset.phone.toLowerCase(); // fix di sini

            if (
                name.includes(keyword) ||
                email.includes(keyword) ||
                phone.includes(keyword)
            ) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection