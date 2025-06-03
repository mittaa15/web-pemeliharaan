@extends('layout.sarprasLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Ruangan</h1>
        <hr class="border-black mb-6">

        <div class="flex justify-end items-center mb-4">
            <div>
                <input id="search" type="text" placeholder="Cari fasilitas..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-64 text-sm" />
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex justify-between space-x-8">
                <div class="w-full">
                    <div>
                        <table class="table w-full text-sm text-left text-gray-600 border" id="roomTable">
                            <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                                <tr>
                                    <th class="px-6 py-3">Nama Ruangan</th>
                                    <th class="px-6 py-3">Tipe Ruang</th>
                                    <th class="px-6 py-3">Kapasitas</th>
                                    <th class="px-6 py-3">Deskripsi</th>
                                    <th class="px-6 py-3">Gedung</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rooms as $room)
                                <tr class="room-row" data-name="{{ $room->room_name }}" data-id="{{ $room->id }}"
                                    data-type="{{ $room->room_type }}" data-capacity="{{ $room->capacity }}"
                                    data-description="{{ $room->description }}"
                                    data-building="{{ $room->building->building_name ?? '-' }}">
                                    <td class="px-6 py-3">{{ $room->room_name }}</td>
                                    <td class="px-6 py-3">{{ $room->room_type }}</td>
                                    <td class="px-6 py-3">{{ $room->capacity }}</td>
                                    <td class="px-6 py-3">{{ $room->description }}</td>
                                    <td class="px-6 py-3">{{ $room->building->building_name ?? '-' }}</td>
                                    <td class="px-6 py-3 relative">
                                        <div class="relative inline-block text-left">
                                            <button onclick="toggleDropdown(this)"
                                                class="text-primary hover:underline">Aksi ▼</button>
                                            <div
                                                class="dropdown-menu hidden absolute right-0 mt-2 w-36 bg-white border rounded shadow-lg z-10">
                                                <button onclick="showRoomDetails(this)"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">Detail</button>
                                                <button onclick="editRoom(this)"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">Edit</button>
                                                <button onclick="deleteRoom(this)"
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

<!-- Modal Detail -->
<div id="detailRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-1/2">
        <h2 class="text-lg text-primary font-bold mb-4">Detail Ruangan</h2>
        <div class="grid grid-cols-3 gap-y-2 text-sm text-gray-600">
            <div class="font-semibold">Nama</div>
            <div class="col-span-2" id="detailName">:</div>

            <div class="font-semibold">Tipe</div>
            <div class="col-span-2" id="detailType">:</div>

            <div class="font-semibold">Kapasitas</div>
            <div class="col-span-2" id="detailCapacity">:</div>

            <div class="font-semibold">Deskripsi</div>
            <div class="col-span-2" id="detailDescription">:</div>

            <div class="font-semibold">Gedung</div>
            <div class="col-span-2" id="detailBuilding">:</div>
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="closeModal('detailRoomModal')"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Edit (Contoh AJAX/manual update) -->
<div id="editRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-1/2">
        <h2 class="text-lg text-primary font-bold mb-4">Edit Ruangan</h2>
        <form id="editRoomForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" id="editRoomId" name="id">
            <div class="mb-4">
                <label class="block text-primary text-sm font-medium mb-1">Nama Ruangan</label>
                <input type="text" id="editRoomName" name="room_name"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="room_name"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-primary text-sm font-medium mb-1">Tipe Ruang</label>
                <input type="text" id="editRoomType" name="room_type"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="room_type"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-primary text-sm font-medium mb-1">Kapasitas</label>
                <input type="number" id="editRoomCapacity" name="capacity"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="capacity" required>
            </div>
            <div class="mb-4">
                <label class="block text-primary text-sm font-medium mb-1">Deskripsi</label>
                <textarea id="editRoomDescription" name="description"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="description"
                    required></textarea>
            </div>
            <div class=" mt-6 flex justify-end">
                <button type="button" onclick="closeModal('editRoomModal')"
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
                class=" text-primary font-semibold"></span>. ini akan menghapus semua data fasilitas ruang?</p>
        <div class="flex justify-end">
            <button onclick="closeModal('confirmDeleteModal')"
                class="bg-gray-500 text-white px-4 py-2 rounded mr-2 hover:bg-gray-600 text-sm">Batal</button>
            <button id="confirmDeleteButton"
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">Hapus</button>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk mencari fasilitas berdasarkan nama atau deskripsi
    document.getElementById('search').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase();
        const facilityRows = document.querySelectorAll('.facility-row');

        facilityRows.forEach(row => {
            const name = row.getAttribute('data-name').toLowerCase();
            const description = row.getAttribute('data-description').toLowerCase();
            if (name.includes(searchQuery) || description.includes(searchQuery)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });

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

    function showRoomDetails(button) {
        const row = button.closest('tr');
        document.getElementById('detailName').textContent = row.dataset.name;
        document.getElementById('detailType').textContent = row.dataset.type;
        document.getElementById('detailCapacity').textContent = row.dataset.capacity;
        document.getElementById('detailDescription').textContent = row.dataset.description;
        document.getElementById('detailBuilding').textContent = row.dataset.building;
        openModal('detailRoomModal');
    }

    function editRoom(button) {
        const row = button.closest('tr');
        const name = row.dataset.name;
        const type = row.dataset.type;
        const capacity = row.dataset.capacity;
        const description = row.dataset.description;
        const id = row.dataset.id;

        document.getElementById('editRoomName').value = name;
        document.getElementById('editRoomType').value = type;
        document.getElementById('editRoomCapacity').value = capacity;
        document.getElementById('editRoomDescription').value = description;

        document.getElementById('editRoomForm').action = `/update-room/${id}`;
        openModal('editRoomModal');
    }

    let deleteFacilityId = null;

    function deleteRoom(button) {
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
                form.action = `/delete-room/${deleteFacilityId}`;
                form.submit();
            }
        });
    });
</script>
@endsection