@extends('layout.adminLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Data Gedung')
</head>

@section('content')
<div class="p-8 mt-20">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Gedung</h1>
        <hr class="border-black mb-6">

        <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-2 md:space-y-0">
            <button onclick="openModal('addFacilityModal')"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm w-full md:w-auto text-center">
                + Tambah Data
            </button>
            <div class="w-full md:w-64">
                <input id="search" type="text" placeholder="Cari fasilitas..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full text-sm" />
            </div>
        </div>

        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                closeModal('addFacilityModal');
                setTimeout(function() {
                    alert("{{ session('success') }}");
                }, 300);
            });
        </script>
        @endif

        <div class="overflow-x-auto">
            <table class="table w-full text-sm text-left text-gray-600 border" id="indoorFacilitiesTable">
                <thead class="bg-primary text-xs uppercase text-white">
                    <tr>
                        <th class="px-6 py-3">Nama Fasilitas</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($facilities as $facility)
                    <tr class="facility-row" data-id="{{ $facility->id }}" data-name="{{ $facility->building_name }}"
                        data-description="{{ $facility->description }}">
                        <td class="px-6 py-3">{{ $facility->building_name }}</td>
                        <td class="px-6 py-3">{{ $facility->description }}</td>
                        <td class="px-6 py-3">
                            <div class="relative inline-block text-left">
                                <button onclick="toggleDropdown(this)" class="text-primary hover:underline">Aksi
                                    ▼</button>
                                <div
                                    class="dropdown-menu hidden absolute right-0 mt-2 w-36 bg-white border rounded shadow-lg z-10">
                                    <button onclick="showFacilityDetails(this)"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">Detail</button>
                                    <button onclick="editFacility(this)"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">Edit</button>
                                    <button onclick="deleteFacility(this)"
                                        class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 w-full text-left">Hapus</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex justify-end mt-2">
                <div class="join grid grid-cols-2 gap-2">
                    <button id="prevPageBtn" class="join-item btn btn-outline bg-primary" disabled>Previous</button>
                    <button id="nextPageBtn" class="join-item btn btn-outline bg-primary">Next</button>
                </div>
            </div>
            <form id="deleteFacilityForm" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="addFacilityModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <<div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
            <h2 class="text-lg font-bold mb-4 text-primary">Tambah Fasilitas</h2>
            <form id="addFacilityForm" action="{{ route('create_building') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-primary">Nama Fasilitas</label>
                    <input type="text" id="addFacilityName" name="building_name"
                        class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-primary">Deskripsi</label>
                    <textarea id="addFacilityDescription" name="description"
                        class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required></textarea>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" onclick="closeModal('addFacilityModal')"
                        class="mr-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Batal</button>
                    <button type="submit"
                        class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Simpan</button>
                </div>
            </form>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailFacilityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
        <h2 class="text-lg text-primary font-bold mb-4">Detail Fasilitas</h2>
        <div class="grid grid-cols-3 gap-y-2 text-sm text-gray-700">
            <div class="font-semibold">Nama</div>
            <div class="col-span-2" id="facilityName">:</div>
            <div class="font-semibold">Deskripsi</div>
            <div class="col-span-2" id="facilityDescription">:</div>
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="closeModal('detailFacilityModal')"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editFacilityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
        <h2 class="text-lg text-primary font-bold mb-4">Edit Fasilitas</h2>
        <form id="editFacilityForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-primary text-sm font-medium mb-1">Nama Fasilitas</label>
                <input type="text" id="editFacilityName" name="building_name"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-primary font-medium mb-1">Deskripsi</label>
                <textarea id="editFacilityDescription" name="description"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required></textarea>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeModal('editFacilityModal')"
                    class="mr-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Batal</button>
                <button type="submit"
                    class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus -->
<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
        <h2 class="text-lg font-bold text-primary mb-4">Konfirmasi Hapus</h2>
        <p class="text-gray-700 mb-6 text-sm">Apakah Anda yakin ingin menghapus <span id="facilityToDeleteName"
                class="text-primary font-semibold"></span>? Data ini akan dihapus permanen.</p>
        <div class="flex justify-end">
            <button onclick="closeModal('confirmDeleteModal')"
                class="bg-gray-500 text-white px-4 py-2 rounded mr-2 hover:bg-gray-600 text-sm">Batal</button>
            <button id="confirmDeleteButton"
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">Hapus</button>
        </div>
    </div>
</div>

<script>
    function toggleDropdown(button) {
        const dropdown = button.nextElementSibling;
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== dropdown) menu.classList.add('hidden');
        });
        dropdown.classList.toggle('hidden');
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function showFacilityDetails(button) {
        const row = button.closest('tr');
        document.getElementById('facilityName').textContent = row.dataset.name;
        document.getElementById('facilityDescription').textContent = row.dataset.description;
        openModal('detailFacilityModal');
    }

    function editFacility(button) {
        const row = button.closest('tr');
        document.getElementById('editFacilityName').value = row.dataset.name;
        document.getElementById('editFacilityDescription').value = row.dataset.description;
        document.getElementById('editFacilityForm').action = `/update-gedung/${row.dataset.id}`;
        openModal('editFacilityModal');
    }

    let deleteFacilityId = null;

    function deleteFacility(button) {
        const row = button.closest('tr');
        deleteFacilityId = row.dataset.id;
        document.getElementById('facilityToDeleteName').textContent = `"${row.dataset.name}"`;
        openModal('confirmDeleteModal');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (deleteFacilityId) {
                const form = document.getElementById('deleteFacilityForm');
                form.action = `/delete-gedung/${deleteFacilityId}`;
                form.submit();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const table = document.getElementById('indoorFacilitiesTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr.facility-row'));
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');

        let currentPage = 1;
        const itemsPerPage = 10;
        let filteredRows = [...rows];

        function renderTable() {
            // Hitung indeks
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            // Bersihkan tampilan
            tbody.innerHTML = '';

            // Tampilkan baris yang sesuai halaman
            filteredRows.slice(start, end).forEach(row => {
                tbody.appendChild(row);
                row.style.display = ''; // pastikan terlihat
            });

            // Disable tombol jika di batas
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = end >= filteredRows.length;
        }

        function updateFilter() {
            const query = searchInput.value.toLowerCase();

            // Filter berdasarkan nama/deskripsi
            filteredRows = rows.filter(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                const desc = row.getAttribute('data-description').toLowerCase();
                return name.includes(query) || desc.includes(query);
            });

            currentPage = 1; // Reset ke halaman pertama
            renderTable();
        }

        // Event listeners
        searchInput.addEventListener('input', updateFilter);

        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });

        nextBtn.addEventListener('click', () => {
            const maxPage = Math.ceil(filteredRows.length / itemsPerPage);
            if (currentPage < maxPage) {
                currentPage++;
                renderTable();
            }
        });

        // Tampilkan awal
        updateFilter();
    });
</script>
@endsection