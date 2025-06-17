@extends('layout.adminLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Fasilitas Ruang')
</head>

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Fasilitas Ruang</h1>
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

        <div class="space-y-6">
            <div class="flex flex-col lg:flex-row justify-between gap-8">
                <div class="w-full">
                    <h2 class="text-lg font-bold text-primary mb-2">Facilities</h2>

                    <div class="overflow-x-auto bg-white rounded shadow border">
                        <table class="min-w-full text-sm text-left text-gray-600" id="facilitiesTable">
                            <thead class="bg-primary text-xs uppercase text-white">
                                <tr>
                                    <th class="px-4 py-3 whitespace-nowrap">Gedung</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Ruang</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Nama Fasilitas</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Jumlah</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Deskripsi</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($facilities as $facility)
                                <tr class="facility-row" data-id="{{ $facility->id }}"
                                    data-name="{{ $facility->facility_name }}"
                                    data-building="{{ $facility->room->building->building_name }}"
                                    data-description="{{ $facility->description }}"
                                    data-number="{{ $facility->number_units }}">

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $facility->room->building->building_name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 capitalize whitespace-nowrap">
                                        {{ $facility->room->room_name }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $facility->facility_name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $facility->number_units }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $facility->description }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap relative">
                                        <div class="relative inline-block text-left">
                                            <button onclick="toggleDropdown(this)" class="text-primary hover:underline">
                                                Aksi ▼
                                            </button>
                                            <div
                                                class="dropdown-menu hidden absolute right-0 mt-2 w-36 bg-white border rounded shadow-lg z-10">
                                                <button onclick="showFacilityDetails(this)"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    Detail
                                                </button>
                                                <button onclick="editFacility(this)"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    Edit
                                                </button>
                                                <button onclick="deleteFacility(this)"
                                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 w-full text-left">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <form id="deleteFacilityForm" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

    </div>
</div>

<!-- Modal Tambah -->
<div id="addFacilityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
        <h2 class="text-lg font-bold mb-4 text-primary">Tambah Fasilitas</h2>

        <div id="alertBox" class="hidden p-4 mb-4 rounded-lg text-sm" role="alert"></div>


        <form id="addFacilityForm" action="{{ route('create_room_facility') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 text-primary">Ruang</label>
                <select name="id_room" class="input input-bordered w-full bg-white text-gray-600 border-gray-300"
                    required>
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach ($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->room_name }}</option>
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
                <label class="block text-sm font-medium mb-1 text-primary">Jumlah</label>
                <input type="number" id="addFacilityNumberUnits"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="number_units"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 text-primary">Deskripsi</label>
                <textarea id="addFacilityDescription"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="description"
                    required></textarea>
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
<div id="detailFacilityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
        <h2 class="text-lg font-bold mb-4 text-primary">Detail Fasilitas</h2>

        <div class="grid grid-cols-3 gap-y-2 text-sm text-gray-700">
            <div class="font-semibold">Gedung</div>
            <div class="col-span-2" id="buildingName">:</div>

            <div class="font-semibold">Nama</div>
            <div class="col-span-2" id="facilityName">:</div>

            <div class="font-semibold">Jumlah</div>
            <div class="col-span-2" id="numberUnits">:</div>

            <div class="font-semibold">Deskripsi</div>
            <div class="col-span-2" id="facilityDescription">:</div>
        </div>

        <div class="text-right mt-4">
            <button id="btnRiwayat" onclick="openRiwayatModal()" class="px-4 py-2 bg-primary text-white rounded">Riwayat
                Laporan</button>
            <button onclick="closeModal('detailFacilityModal')"
                class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Tutup</button>
        </div>
    </div>
</div>

{{-- Modal Riwayat Status --}}
<div id="riwayatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-1/2 max-h-[80vh] overflow-y-auto">
        <h2 class="text-lg font-bold text-primary mb-4">Riwayat Laporan</h2>
        <div class="overflow-x-auto mb-4">
            <table class="table w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Kode Laporan</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3">Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody id="historyContent">
                    {{-- Isi lewat JS --}}
                </tbody>
            </table>
        </div>
        <div class="text-right">
            <button onclick="closeModal('riwayatModal')" class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
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
                <input type="text" id="editFacilityName"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="facility_name"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-primary text-sm font-medium mb-1">Jumlah</label>
                <input type="text" id="editNumberUnits"
                    class="input input-bordered w-full bg-white text-gray-600 border-gray-300" name="number_units"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-primary text-sm font-medium mb-1">Deskripsi</label>
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

<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
        <h2 class="text-lg font-bold text-primary mb-4">Konfirmasi Hapus</h2>
        <p class="text-gray-700 mb-6 text-sm">Apakah Anda yakin ingin menghapus fasilitas <span
                id="facilityToDeleteName" class=" text-primary font-semibold"></span>?</p>
        <div class="flex justify-end">
            <button onclick="closeModal('confirmDeleteModal')"
                class="bg-gray-500 text-white px-4 py-2 rounded mr-2 hover:bg-gray-600 text-sm">Batal</button>
            <button id="confirmDeleteButton"
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">Hapus</button>
        </div>
    </div>
</div>

<script>
const facilites = @json($facilities);
console.log(facilites);

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

function closeModalAdd() {
    document.getElementById('addFacilityModal').classList.add('hidden');
    location.reload();
}

let selectedFacility = null;

function showFacilityDetails(button) {
    const row = button.closest('tr');
    const facilityId = row.dataset.id;
    const name = row.dataset.name;
    const building = row.dataset.building;
    const number = row.dataset.number;
    const description = row.dataset.description;
    selectedFacility = facilites.find(f => f.id == facilityId);

    if (!selectedFacility) {
        alert('Fasilitas tidak ditemukan!');
        return;
    }

    document.getElementById('facilityName').textContent = name;
    document.getElementById('buildingName').textContent = building;
    document.getElementById('numberUnits').textContent = number;
    document.getElementById('facilityDescription').textContent = description;


    openModal('detailFacilityModal');
}

function openRiwayatModal() {
    if (!selectedFacility) {
        alert('Pilih fasilitas terlebih dahulu!');
        return;
    }

    const tbody = document.getElementById('historyContent');
    tbody.innerHTML = ''; // Kosongkan dulu isi tbody

    if (selectedFacility.repair_reports && selectedFacility.repair_reports.length > 0) {
        selectedFacility.repair_reports.forEach(report => {
            const kodeLaporan = report.id ? String(report.id).padStart(4, '0') : '-';
            const status = report.status ?? '-';
            const deskripsi = report.damage_description ?? '-';
            const tanggalSelesai = report.updated_at ? new Date(report.updated_at).toLocaleDateString() :
                '-';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-6 py-3">${kodeLaporan}</td>
                <td class="px-6 py-3">${status}</td>
                <td class="px-6 py-3">${deskripsi}</td>
                <td class="px-6 py-3">${tanggalSelesai}</td>
            `;
            tbody.appendChild(tr);
        });
    } else {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td class="px-6 py-3 text-center" colspan="4">Tidak ada riwayat laporan</td>`;
        tbody.appendChild(tr);
    }

    openModal('riwayatModal');
}

function editFacility(button) {
    const row = button.closest('tr');
    const name = row.dataset.name;
    const number_units = row.dataset.number;
    const description = row.dataset.description;
    const id = row.dataset.id;

    document.getElementById('editFacilityName').value = name;
    document.getElementById('editNumberUnits').value = number_units;
    document.getElementById('editFacilityDescription').value = description;

    document.getElementById('editFacilityForm').action = `/update-facility-room/${id}`;
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

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('confirmDeleteButton').addEventListener('click', function() {
        if (deleteFacilityId) {
            const form = document.getElementById('deleteFacilityForm');
            form.action = `/delete-facility-room/${deleteFacilityId}`;
            form.submit();
        }
    });
});

// Fungsi filter tabel berdasarkan input search
document.getElementById('search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('facilitiesTable');
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        // Ambil semua teks di setiap kolom dalam satu baris
        const rowText = row.textContent.toLowerCase();

        // Jika rowText mengandung searchTerm, tampilkan baris, jika tidak sembunyikan
        if (rowText.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

document.getElementById('addFacilityForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const alertBox = document.getElementById('alertBox');

    const formData = {
        id_room: form.querySelector('select[name="id_room"]').value,
        facility_name: document.getElementById('addFacilityName').value,
        number_units: document.getElementById('addFacilityNumberUnits').value,
        description: document.getElementById('addFacilityDescription').value,
    };

    fetch("{{ route('create_room_facility') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(formData),
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            showAlert(data.message || 'Fasilitas ruangan berhasil ditambahkan.', 'success');
            form.reset();
            // Optionally reload table here
        })
        .catch(error => {
            let message = 'Terjadi kesalahan.';
            if (error.errors) {
                message = Object.values(error.errors).join('<br>');
            } else if (error.message) {
                message = error.message;
            }
            showAlert(message, 'error');
        });

    function showAlert(message, type = 'success') {
        alertBox.innerHTML = message;
        alertBox.classList.remove('hidden', 'bg-red-100', 'bg-green-100', 'text-red-700', 'text-green-700',
            'border-red-400', 'border-green-400');

        if (type === 'success') {
            alertBox.classList.add('bg-green-100', 'text-green-700', 'border', 'border-green-400');
        } else {
            alertBox.classList.add('bg-red-100', 'text-red-700', 'border', 'border-red-400');
        }

        setTimeout(() => {
            alertBox.classList.add('hidden');
        }, 3000); // Sembunyikan otomatis dalam 4 detik
    }
});
</script>
@endsection