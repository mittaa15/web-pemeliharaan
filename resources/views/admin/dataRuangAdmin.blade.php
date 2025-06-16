@extends('layout.adminLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Data Ruang')
</head>

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Ruangan</h1>
        <hr class="border-black mb-6">

        <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-2 md:space-y-0">
            <button onclick="openModal('addRoomModal')"
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
            <div class="flex flex-wrap justify-between space-x-0 sm:space-x-8">
                <div class="w-full">
                    <h2 class="text-lg font-bold text-primary mb-2">Facilities</h2>
                    <div>
                        <div class="overflow-x-auto">
                            <table class="table w-full text-sm text-left text-gray-600 border" id="roomTable">
                                <thead class="bg-primary text-xs uppercase text-white">
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
                            <div class="flex justify-end mt-2">
                                <div class="join grid grid-cols-2 gap-2">
                                    <button id="prevPageBtn" class="join-item btn btn-outline bg-primary"
                                        disabled>Previous</button>
                                    <button id="nextPageBtn" class="join-item btn btn-outline bg-primary">Next</button>
                                </div>
                            </div>
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

    <!-- Modal Tambah Ruangan -->
    <div id="addRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
            <h2 class="text-lg font-bold mb-4 text-primary">Tambah Ruangan</h2>

            <!-- Notifikasi Sukses / Gagal -->
            <div id="addRoomAlert" class="hidden mb-4 text-sm px-4 py-2 rounded"></div>

            <form id="addRoomForm" action="{{ route('create_room') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-primary">Nama Ruangan</label>
                    <input type="text" name="room_name"
                        class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-primary">Tipe Ruang</label>
                    <input type="text" name="room_type"
                        class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-primary">Kapasitas</label>
                    <input type="number" name="capacity"
                        class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-primary">Deskripsi</label>
                    <textarea name="description"
                        class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 text-primary">Gedung</label>
                    <select name="id_building"
                        class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                        <option value="">-- Pilih Gedung --</option>
                        @foreach ($buildings as $building)
                        <option value="{{ $building->id }}">{{ $building->building_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" onclick="closeModalAdd()"
                        class="mr-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Tutup</button>
                    <button type="submit"
                        class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
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
        <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
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
                        class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="capacity"
                        required>
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
    <div id="confirmDeleteModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
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

    function closeModalAdd() {
        document.getElementById('addRoomModal').classList.add('hidden');
        location.reload();
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

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const table = document.getElementById('roomTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');

        let currentPage = 1;
        const itemsPerPage = 10;
        let filteredRows = [...rows];

        function renderTable() {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            tbody.innerHTML = '';
            filteredRows.slice(start, end).forEach(row => {
                tbody.appendChild(row);
                row.style.display = '';
            });

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = end >= filteredRows.length;
        }

        function updateFilter() {
            const filter = searchInput.value.toLowerCase();

            filteredRows = rows.filter(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                const type = row.getAttribute('data-type').toLowerCase();
                const capacity = row.getAttribute('data-capacity').toLowerCase();
                const description = row.getAttribute('data-description').toLowerCase();
                const building = row.getAttribute('data-building').toLowerCase();

                return (
                    name.includes(filter) ||
                    type.includes(filter) ||
                    capacity.includes(filter) ||
                    description.includes(filter) ||
                    building.includes(filter)
                );
            });

            currentPage = 1;
            renderTable();
        }

        // Event listener
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

    document.getElementById('addRoomForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const alertBox = document.getElementById('addRoomAlert');
        const submitBtn = form.querySelector('button[type="submit"]');

        // Reset alert
        alertBox.classList.add('hidden');
        alertBox.classList.remove('bg-red-100', 'bg-green-100', 'text-red-700', 'text-green-700');

        submitBtn.disabled = true;

        fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw data;
                }
                // Sukses
                alertBox.textContent = data.message;
                alertBox.classList.remove('hidden');
                alertBox.classList.add('bg-green-100', 'text-green-700');
                form.reset();

                // Tutup modal dan reload halaman setelah 1.5 detik
                setTimeout(() => {
                    closeModal('addRoomModal');
                }, 3000);
            })
            .catch(error => {
                alertBox.textContent = error.message || 'Terjadi kesalahan.';
                alertBox.classList.remove('hidden');
                alertBox.classList.add('bg-red-100', 'text-red-700');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan';
            });
    });
    </script>
    @endsection