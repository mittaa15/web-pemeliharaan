@extends('layout.adminLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Fasilitas Gedung</h1>
        <hr class="border-black mb-6">
        @if(session('success'))
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tutup modal terlebih dahulu
            closeModal('addFacilityModal');

            // Tampilkan alert setelah modal ditutup
            setTimeout(function() {
                const alertBox = document.createElement('div');
                alertBox.id = 'success-alert';
                alertBox.className =
                    'bg-green-100 text-green-800 flex justify-center p-2 mb-4 rounded';
                alertBox.textContent = "{{ session('success') }}";
                document.body.appendChild(alertBox);

                // Setelah 3 detik, sembunyikan alert
                setTimeout(() => {
                    alertBox.style.opacity = '0';
                    setTimeout(() => {
                        alertBox.style.display = 'none';
                    }, 500); // Delay untuk transisi opacity
                }, 3000); // 3000ms = 3 detik
            }, 300); // Tunggu 300ms setelah modal ditutup
        });
        </script>
        @endif
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




        <div class="space-y-6">
            <div class="flex justify-between space-x-8">
                <!-- Indoor Facilities -->
                <div class="w-full">
                    <h2 class="text-lg font-bold text-primary mb-2">Indoor Facilities</h2>
                    <div class="overflow-x-auto">
                        <table class="table w-full text-sm text-left text-gray-600 border" id="indoorFacilitiesTable">
                            <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                                <tr>
                                    <th class="px-6 py-3">Gedung</th>
                                    <th class="px-6 py-3">Nama Fasilitas</th>
                                    <th class="px-6 py-3">Deskripsi</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($indoorFacilities as $facility)
                                <tr class="facility-row" data-name="{{ $facility->facility_name }}"
                                    data-description="{{ $facility->description }}">
                                    <td class="px-6 py-3">{{ $facility->building->building_name }}</td>
                                    <td class="px-6 py-3">{{ $facility->facility_name }}</td>
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
                                <!-- Tambah baris lain jika perlu -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Outdoor Facilities -->
                <div class="w-full">
                    <h2 class="text-lg font-bold text-primary mb-2">Outdoor Facilities</h2>
                    <div class="overflow-x-auto">
                        <table class="table w-full text-sm text-left text-gray-600 border" id="outdoorFacilitiesTable">
                            <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                                <tr>
                                    <th class="px-6 py-3">Gedung</th>
                                    <th class="px-6 py-3">Nama Fasilitas</th>
                                    <th class="px-6 py-3">Deskripsi</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($outdoorFacilities as $facility)
                                <tr class="facility-row" data-name="{{ $facility->facility_name }}"
                                    data-description="{{ $facility->description }}">
                                    <td class="px-6 py-3">{{ $facility->building->building_name }}</td>
                                    <td class="px-6 py-3">{{ $facility->facility_name }}</td>
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
                                <!-- Tambah baris lain jika perlu -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addFacilityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-1/2">
        <h2 class="text-lg font-bold mb-4 text-primary">Tambah Fasilitas</h2>
        <form id="addFacilityForm" method="POST" action="{{ route('create_building_facility') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 text-primary">Gedung</label>
                <select id="addFacilityBuilding" name="id_building"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                    <option value="">Pilih Gedung</option>
                    @foreach ($buildings as $building)
                    <option value="{{ $building->id }}">{{ $building->building_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 text-primary">Nama Fasilitas</label>
                <input type="text" id="addFacilityName"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="facility_name"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 text-primary">Deskripsi</label>
                <textarea id="addFacilityDescription"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="description"
                    required></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 text-primary">Jenis Fasilitas</label>
                <select id="addFacilityType" name="location"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                    <option value="">Pilih jenis</option>
                    <option value="indoor">Indoor</option>
                    <option value="outdoor">Outdoor</option>
                </select>
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
        <h2 class="text-lg font-bold mb-4">Detail Fasilitas</h2>
        <p id="facilityName" class="text-md font-semibold"></p>
        <p id="facilityDescription" class="text-gray-600 mt-2"></p>

        <div class="mt-6 flex justify-end">
            <button onclick="closeModal('detailFacilityModal')"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editFacilityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-1/2">
        <h2 class="text-lg font-bold mb-4">Edit Fasilitas</h2>
        <form id="editFacilityForm">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama Fasilitas</label>
                <input type="text" id="editFacilityName"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Deskripsi</label>
                <textarea id="editFacilityDescription"
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

    document.getElementById('editFacilityName').value = name;
    document.getElementById('editFacilityDescription').value = description;

    openModal('editFacilityModal');
}

function deleteFacility(button) {
    const row = button.closest('tr');
    const name = row.dataset.name;

    if (confirm('Yakin ingin menghapus fasilitas "' + name + '"?')) {
        alert('Fasilitas "' + name + '" berhasil dihapus!');
    }
}

document.getElementById('editFacilityForm').addEventListener('submit', function(event) {
    event.preventDefault();
    alert('Data fasilitas berhasil diperbarui!');
    closeModal('editFacilityModal');
});

document.getElementById('search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase(); // Ambil input pencarian
    const rows = document.querySelectorAll('.facility-row'); // Ambil semua baris fasilitas

    rows.forEach(function(row) {
        const name = row.querySelector('td:nth-child(2)').textContent
    .toLowerCase(); // Ambil teks Nama Fasilitas (kolom kedua)
        const building = row.querySelector('td:nth-child(1)').textContent
    .toLowerCase(); // Ambil teks Gedung (kolom pertama)

        // Jika nama fasilitas atau gedung cocok dengan kata kunci pencarian, tampilkan baris tersebut, jika tidak sembunyikan
        if (name.includes(searchTerm) || building.includes(searchTerm)) {
            row.style.display = ''; // Tampilkan baris
        } else {
            row.style.display = 'none'; // Sembunyikan baris
        }
    });
});
</script>
@endsection