@extends('layout.userLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Permintaan Laporan</h1>
        <hr class="border-black mb-6">

        {{-- Pagination kiri & search kanan --}}
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-2 text-gray-600">
                <span>Show</span>
                <input type="number" value="10"
                    class="w-12 text-center border border-gray-300 rounded-md px-2 py-1 bg-white text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary"
                    min="1" />
                <span>entries</span>
            </div>

            <div>
                <input type="text" placeholder="Cari laporan..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-64 text-sm" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-white uppercase bg-primary">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nomor Pengajuan</th>
                        <th class="px-6 py-3">Gedung</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Fasilitas</th>
                        <th class="px-6 py-3">Jenis Kerusakan</th>
                        <th class="px-6 py-3">Bukti Kerusakan</th>
                        <th class="px-6 py-3">Tanggal Pelaporan</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 1st Entry --}}
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">1</td>
                        <td class="px-6 py-4">KD001</td>
                        <td class="px-6 py-4">Gedung A</td>
                        <td class="px-6 py-4">Outdoor</td>
                        <td class="px-6 py-4">Taman Belakang</td>
                        <td class="px-6 py-4">Lampu mati</td>
                        <td class="px-6 py-4">
                            <img src="{{ asset('images/lampu.jpg') }}" alt="Bukti Kerusakan"
                                class="w-20 h-20 object-cover rounded-md cursor-pointer"
                                onclick="openModal('{{ asset('images/lampu.jpg') }}')" />
                        </td>
                        <td class="px-6 py-4">2025-04-24</td>
                        <td class="px-6 py-4">
                            <span
                                class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Menunggu</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="relative inline-block text-left">
                                <button type="button"
                                    class="inline-flex justify-center w-full rounded-md border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-gray-100"
                                    onclick="toggleDropdown(event)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 9l6 6 6-6"></path>
                                    </svg>
                                </button>
                                <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                                    id="dropdown-menu">
                                    <div class="py-1" role="none">
                                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm"
                                            role="menuitem">Hapus</a>
                                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm"
                                            role="menuitem">Edit</a>
                                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem">Lihat
                                            Detail</a>
                                        <a href="{{ url('/ajukan-keluhan') }}"
                                            class="text-gray-700 block px-4 py-2 text-sm" role="menuitem">Ajukan
                                            Keluhan</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- Dummy Data --}}
                    @for ($i = 2; $i <= 15; $i++) <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $i }}</td>
                        <td class="px-6 py-4">KD{{ str_pad($i, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4">Gedung {{ chr(64 + $i) }}</td>
                        <td class="px-6 py-4">Indoor</td>
                        <td class="px-6 py-4">Ruang Rapat {{ $i }}</td>
                        <td class="px-6 py-4">AC Rusak</td>
                        <td class="px-6 py-4">
                            <img src="{{ asset('images/lampu.jpg') }}" alt="Bukti Kerusakan"
                                class="w-20 h-20 object-cover rounded-md cursor-pointer"
                                onclick="openModal('{{ asset('images/lampu.jpg') }}')" />
                        </td>
                        <td class="px-6 py-4">2025-04-24</td>
                        <td class="px-6 py-4">
                            <span
                                class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Menunggu</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="relative inline-block text-left">
                                <button type="button"
                                    class="inline-flex justify-center w-full rounded-md border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-gray-100"
                                    onclick="toggleDropdown(event)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 9l6 6 6-6"></path>
                                    </svg>
                                </button>
                                <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                                    id="dropdown-menu">
                                    <div class="py-1" role="none">
                                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm"
                                            role="menuitem">Hapus</a>
                                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm"
                                            role="menuitem">Edit</a>
                                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem">Lihat
                                            Detail</a>
                                        <a href="{{ url('/ajukan-keluhan') }}"
                                            class="text-gray-700 block px-4 py-2 text-sm" role="menuitem">Ajukan
                                            Keluhan</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        </tr>
                        @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div id="imageModal"
    class="modal hidden fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="modal-content bg-white p-4 relative">
        <img id="modalImage" src="" alt="Bukti Kerusakan" class="max-w-full max-h-full" />
        <button class="close-modal absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full"
            onclick="closeModal()">X</button>
    </div>
</div>

{{-- Script --}}
<script>
function openModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSrc;
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
}

function toggleDropdown(event) {
    const dropdownMenu = event.target.closest('div').querySelector('#dropdown-menu');
    dropdownMenu.classList.toggle('hidden');
}
</script>
@endsection