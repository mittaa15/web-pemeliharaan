@extends('layout.adminLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Gedung</h1>
        <hr class="border-black mb-6">

        <div class="flex justify-between items-center mb-4">
            <button onclick="openModal('addFacilityModal')"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                + Tambah Data
            </button>
            <div>
                <input id="search" type="text" placeholder="Cari fasilitas..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-64 text-sm" />
            </div>
        </div>

        @if(session('success'))
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tutup modal dulu
            closeModal('addFacilityModal');

            // Tunggu 300ms baru tampilkan alert
            setTimeout(function() {
                alert("{{ session('success') }}");
            }, 300);
        });
        </script>
        @endif



        <div class="space-y-6">
            <div class="flex justify-between space-x-8">
                <div class="w-full">
                    <h2 class="text-lg font-bold text-primary mb-2">Facilities</h2>
                    <div>
                        <table class="table w-full text-sm text-left text-gray-600 border" id="indoorFacilitiesTable">
                            <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                                <tr>
                                    <th class="px-6 py-3">Nama Fasilitas</th>
                                    <th class="px-6 py-3">Deskripsi</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($facilities as $facility)
                                <tr class="facility-row" data-name="{{ $facility->building_name }}"
                                    data-id="{{ $facility->id }}" data-description="{{ $facility->description }}">
                                    <td class="px-6 py-3">{{ $facility->building_name }}</td>
                                    <td class="px-6 py-3">{{ $facility->description }}</td>
                                    <td class="px-6 py-3 relative">
                                        <div class="relative inline-block text-left">
                                            <button onclick="toggleDropdown(this)"
                                                class="text-primary hover:underline">Aksi ▼</button>
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
                    </div>
                </div>
                <form id="deleteFacilityForm" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addFacilityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-1/2">
        <h2 class="text-lg font-bold mb-4 text-primary">Tambah Fasilitas</h2>
        <form id="addFacilityForm" action="{{ route('create_building') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 text-primary">Nama Fasilitas</label>
                <input type="text" id="addFacilityName"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="building_name"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 text-primary">Deskripsi</label>
                <textarea id="addFacilityDescription"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="description"
                    required></textarea>
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
    <div class="bg-white p-6 rounded-lg w-1/2">
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
    <div class="bg-white p-6 rounded-lg w-1/2">
        <h2 class="text-lg text-primary font-bold mb-4">Edit Fasilitas</h2>
        <form id="editFacilityForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-primary text-sm font-medium mb-1">Nama Fasilitas</label>
                <input type="text" id="editFacilityName"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="building_name"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-primary font-medium mb-1">Deskripsi</label>
                <textarea id="editFacilityDescription"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="description"
                    required></textarea>
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

<!-- Modal Konfirmasi Hapus -->
<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-1/3">
        <h2 class="text-lg font-bold text-primary mb-4">Konfirmasi Hapus</h2>
        <p class="text-gray-700 mb-6 text-sm">Apakah Anda yakin ingin menghapus <span id="facilityToDeleteName"
                class=" text-primary font-semibold"></span>. ini akan menghapus semua data
            fasilitas Gedung?</p>
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
        if (menu !== dropdown) {
            menu.classList.add('hidden');
        }
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
    const name = row.dataset.name;
    const description = row.dataset.description;

    document.getElementById('facilityName').textContent = name;
    document.getElementById('facilityDescription').textContent = description;

    openModal('detailFacilityModal');
}

function editFacility(button) {
    const row = button.closest('tr');
    const name = row.dataset.name;
    const description = row.dataset.description;
    const id = row.dataset.id;

    document.getElementById('editFacilityName').value = name;
    document.getElementById('editFacilityDescription').value = description;

    document.getElementById('editFacilityForm').action = `/update-gedung/${id}`;

    openModal('editFacilityModal');
}


let deleteFacilityId = null;

function deleteFacility(button) {
    const row = button.closest('tr');
    deleteFacilityId = row.dataset.id;
    const name = row.dataset.name;

    // Set nama fasilitas di modal
    document.getElementById('facilityToDeleteName').textContent = `"${name}"`;

    // Tampilkan modal
    openModal('confirmDeleteModal');
}

// Ketika tombol "Hapus" di modal ditekan
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('confirmDeleteButton').addEventListener('click', function() {
        if (deleteFacilityId) {
            const form = document.getElementById('deleteFacilityForm');
            form.action = `/delete-gedung/${deleteFacilityId}`;
            form.submit();
        }
    });
});
</script>
@endsection