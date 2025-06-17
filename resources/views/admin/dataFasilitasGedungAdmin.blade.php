@extends('layout.adminLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Fasilitas Gedung')
</head>

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Daftar Fasilitas Gedung</h1>
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
            <div class="flex flex-col space-y-6 md:space-y-0 md:flex-row md:justify-between md:space-x-8">
                <!-- Indoor Facilities -->
                <div class="w-full">
                    <h2 class="text-lg font-bold text-primary mb-2">Fasilitas Gedung</h2>
                    <div class="overflow-x-auto">
                        <table class="table w-full text-sm text-left text-gray-600 border" id="indoorFacilitiesTable">
                            <thead class="bg-primary text-xs uppercase text-white">
                                <tr>
                                    <th class="px-6 py-3">Gedung</th>
                                    <th class="px-3 py-3">Lokasi</th>
                                    <th class="px-3 py-3">Nama Fasilitas</th>
                                    <th class="px-3 py-3">Deskripsi</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($facilities as $facility)
                                <tr class="facility-row" data-id="{{ $facility->id }}"
                                    data-name="{{ $facility->facility_name }}"
                                    data-description="{{ $facility->description }}">
                                    <td class="px-6 py-3">{{ $facility->building->building_name }}</td>
                                    <td class="px-6 py-3">{{ $facility->location}}</td>
                                    <td class="px-3 py-3">{{ $facility->facility_name }}</td>
                                    <td class="px-3 py-3">{{ $facility->description }}</td>
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
            </div>
        </div>

        <!-- Form Delete -->
        <form id="deleteFacilityForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>


        <!-- Modal Tambah -->
        <div id="addFacilityModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
                <h2 class="text-lg font-bold mb-4 text-primary">Tambah Fasilitas</h2>

                <div id="alertBox" class="hidden p-4 mb-4 rounded-lg text-sm" role="alert"></div>

                <form id="addFacilityForm" method="POST" action="#">
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
                            class="input input-bordered w-full bg-white text-gray-600 border-gray-300"
                            name="facility_name" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1 text-primary">Deskripsi</label>
                        <textarea id="addFacilityDescription"
                            class="input input-bordered w-full bg-white text-gray-600 border-gray-300"
                            name="description" required></textarea>
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
                        <button type="button" onclick="closeModalAdd()"
                            class="mr-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Tutup</button>
                        <button type="submit"
                            class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Detail -->
        <div id="detailFacilityModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
                <h2 class="text-lg font-bold text-primary mb-4">Detail Fasilitas</h2>

                <div class="grid grid-cols-3 gap-y-2 text-sm text-gray-700">
                    <div class="font-semibold">Nama</div>
                    <div class="col-span-2" id="facilityName">:</div>

                    <div class="font-semibold">Deskripsi</div>
                    <div class="col-span-2" id="facilityDescription">:</div>
                </div>

                <div class="text-right mt-4">
                    <button id="btnRiwayat" onclick="openRiwayatModal()"
                        class="px-4 py-2 bg-primary text-white rounded">Riwayat
                        Laporan</button>
                    <button onclick="closeModal('detailFacilityModal')"
                        class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Tutup</button>
                </div>
            </div>
        </div>

        {{-- Modal Riwayat Status --}}
        <div id="riwayatModal"
            class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
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
                    <button onclick="closeModal('riwayatModal')"
                        class="px-4 py-2 bg-primary text-white rounded">Tutup</button>
                </div>
            </div>
        </div>


        <!-- Modal Edit -->
        <div id="editFacilityModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
                <h2 class="text-lg text-primary font-bold mb-4">Edit Fasilitas</h2>
                <form id="editFacilityForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-primary text-sm font-medium mb-1">Nama Fasilitas</label>
                        <input type="text" id="editFacilityName"
                            class="input input-bordered w-full bg-white text-gray-600 border-gray-300"
                            name="facility_name" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-primary text-sm font-medium mb-1">Deskripsi</label>
                        <textarea id="editFacilityDescription"
                            class="input input-bordered w-full bg-white text-gray-600 border-gray-300"
                            name="description" required></textarea>
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
        <div id="confirmDeleteModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
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
        const indoor = @json($facilities);

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
            const description = row.dataset.description;
            selectedFacility = indoor.find(f => f.id == facilityId);

            // Kalau gak ketemu di indoor, cari di outdoor
            if (!selectedFacility) {
                selectedFacility = outdoor.find(f => f.id == facilityId);
            }

            console.log(facilityId);

            if (!selectedFacility) {
                alert('Fasilitas tidak ditemukan!');
                return;
            }

            document.getElementById('facilityName').textContent = name;
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
                    const tanggalSelesai = report.updated_at ? new Date(report.updated_at)
                        .toLocaleDateString() : '-';

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
            const description = row.dataset.description;
            const id = row.dataset.id;

            document.getElementById('editFacilityName').value = name;
            document.getElementById('editFacilityDescription').value = description;

            document.getElementById('editFacilityForm').action = `/update-facility-gedung/${id}`;
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
                    form.action = `/delete-facility-gedung/${deleteFacilityId}`;
                    form.submit();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');

            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();

                // Fungsi filter baris pada tabel tertentu
                function filterTable(tableId) {
                    const table = document.getElementById(tableId);
                    const rows = table.querySelectorAll('tbody tr');

                    rows.forEach(row => {
                        // Ambil text dari kolom nama fasilitas dan deskripsi
                        const name = row.querySelector('td:nth-child(2)').textContent
                            .toLowerCase();
                        const desc = row.querySelector('td:nth-child(3)').textContent
                            .toLowerCase();

                        // Tampilkan baris jika mengandung filter, sembunyikan jika tidak
                        if (name.includes(filter) || desc.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }

                // Terapkan filter ke kedua tabel
                filterTable('indoorFacilitiesTable');
                filterTable('outdoorFacilitiesTable');
            });
        });

        document.getElementById('addFacilityForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const alertBox = document.getElementById('alertBox');

            // Reset alert
            alertBox.classList.add('hidden');
            alertBox.innerHTML = '';
            alertBox.className = 'hidden p-4 mb-4 rounded-lg text-sm';

            const formData = {
                id_building: document.getElementById('addFacilityBuilding').value,
                facility_name: document.getElementById('addFacilityName').value,
                description: document.getElementById('addFacilityDescription').value,
                location: document.getElementById('addFacilityType').value,
            };

            fetch("{{ route('create_building_facility') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(formData),
                })
                .then(response => {
                    if (!response.ok) return response.json().then(err => Promise.reject(err));
                    return response.json();
                })
                .then(data => {
                    showAlert('success', data.message || 'Fasilitas berhasil ditambahkan.');
                    form.reset();
                })
                .catch(error => {
                    let message = 'Terjadi kesalahan.';

                    if (error.errors) {
                        message = Object.values(error.errors).join('<br>');
                    } else if (error.message) {
                        message = error.message;
                    }

                    showAlert('error', message);
                });

            function showAlert(type, message) {
                alertBox.innerHTML = message;
                alertBox.classList.remove('hidden');

                if (type === 'success') {
                    alertBox.classList.add('bg-green-100', 'text-green-700', 'border', 'border-green-400');
                } else {
                    alertBox.classList.add('bg-red-100', 'text-red-700', 'border', 'border-red-400');
                }

                setTimeout(() => {
                    alertBox.classList.add('hidden');
                }, 3000); // Hilang otomatis setelah 5 detik
            }
        });
        </script>
        @endsection