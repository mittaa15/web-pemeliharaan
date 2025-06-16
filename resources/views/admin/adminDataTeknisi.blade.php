@extends('layout.adminLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Data Teknisi')
</head>

@section('content')
<div class="p-8">
    @if(session('success'))
    <div id="success-alert" class="bg-green-100 text-green-800 flex justify-center p-2 mb-4 rounded">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const alertBox = document.getElementById('success-alert');
            if (alertBox) {
                alertBox.style.display = 'none';
            }
        }, 3000); // 3000ms = 3 detik
    </script>
    @endif

    @if($errors->has('message'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-2">
        {{ $errors->first('message') }}
    </div>
    @endif
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Data Teknisi</h1>
        <hr class="border-black mb-6">

        <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-2 md:space-y-0">
            <button onclick="openModal('addTechnicianModal')"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm w-full md:w-auto text-center">
                + Tambah Data
            </button>
            <div class="w-full md:w-64">
                <input id="search" type="text" placeholder="Cari fasilitas..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full text-sm" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-sm text-left text-gray-600 border min-w-[600px]">

                <thead class="bg-primary text-xs uppercase text-white">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nama Teknisi</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Nomor HP</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Technicians as $technician)
                    <tr class="technician-row" data-id="{{ $technician->id }}" data-name="{{ $technician->name }}"
                        data-email="{{ $technician->email }}" data-phone_number="{{ $technician->phone_number }}">
                        <td class="px-6 py-3">{{ $loop->iteration  }}</td>
                        <td class="px-6 py-3">{{ $technician->name }}</td>
                        <td class="px-6 py-3">{{ $technician->email }}</td>
                        <td class="px-6 py-3">{{ $technician->phone_number }}</td>
                        <td class="px-6 py-3">
                            <div class="relative inline-block text-left">
                                <button onclick="toggleDropdown(this)" class="text-primary hover:underline">Aksi
                                    ▼</button>
                                <div
                                    class="dropdown-menu hidden absolute right-0 mt-2 w-36 bg-white border rounded shadow-lg z-10">
                                    <button onclick="showTechnicianDetails(this)"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">Detail</button>
                                    <button onclick="editTechnician(this)"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">Edit</button>
                                    <button onclick="deleteTechnician(this)"
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
                <form id="deleteTechnicianForm" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        <div id="addTechnicianModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
                <h2 class="text-lg font-bold mb-4 text-primary">Tambah Teknisi</h2>
                <form action="{{ route('create-technician') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1 text-primary">Nama Teknisi</label>
                        <input type="text" id="addFacilityName" name="name"
                            class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1 text-primary">Email</label>
                        <input id="addTechnicianEmail" name="email"
                            class="input input-bordered w-full bg-white text-gray-600 border-gray-300" type="email"
                            required></input>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1 text-primary">Nomor Handphone</label>
                        <input id="addTechnicianPhone" name="phone_number"
                            class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required></input>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="button" onclick="closeModal('addTechnicianModal')"
                            class="mr-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Batal</button>
                        <button type="submit"
                            class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Detail -->
        <div id="detailTechnicianModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
                <h2 class="text-lg text-primary font-bold mb-4">Detail Teknisi</h2>
                <div class="grid grid-cols-3 gap-y-2 text-sm text-gray-700">
                    <div class="font-semibold">Nama Teknisi</div>
                    <div class="col-span-2" id="name">:</div>
                    <div class="font-semibold">Email</div>
                    <div class="col-span-2" id="email">:</div>
                    <div class="font-semibold">Nomor HP</div>
                    <div class="col-span-2" id="nomorHandphone">:</div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button onclick="closeModal('detailTechnicianModal')"
                        class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Tutup</button>
                </div>
            </div>
        </div>

        <!-- Modal Edit Teknisi -->
        <div id="editTechnicianModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
                <h2 class="text-lg text-primary font-bold mb-4">Edit Teknisi</h2>
                <form id="editTechnicianForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1 text-primary">Nama Teknisi</label>
                        <input type="text" id="editTechnicianName" name="name"
                            class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1 text-primary">Email</label>
                        <input type="email" id="editTechnicianEmail" name="email"
                            class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1 text-primary">Nomor Handphone</label>
                        <input type="text" id="editTechnicianHandphone" name="phone_number"
                            class="input input-bordered w-full bg-white text-gray-600 border-gray-300" required>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="button" onclick="closeModal('editTechnicianModal')"
                            class="mr-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Batal</button>
                        <button type="submit"
                            class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>



        <!-- Modal Hapus -->
        <div id="confirmDeleteModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md mx-4 sm:mx-auto">
                <h2 class="text-lg font-bold text-primary mb-4">Konfirmasi Hapus</h2>
                <p class="text-gray-700 mb-6 text-sm">Apakah Anda yakin ingin menghapus <span
                        id="technicianToDeleteName" class="text-primary font-semibold"></span>? Data ini akan dihapus
                    permanen.</p>
                <div class="flex justify-end">
                    <button onclick="closeModal('confirmDeleteModal')"
                        class="bg-gray-500 text-white px-4 py-2 rounded mr-2 hover:bg-gray-600 text-sm">Batal</button>
                    <button id="confirmDeleteButton"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">Hapus</button>
                </div>
            </div>
        </div>

        <script>
            const complaint = @json($Technicians);
            console.log(complaint)

            function toggleDropdown(button) {
                const dropdown = button.nextElementSibling;
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu !== dropdown) {
                        menu.classList.add('hidden');
                    }
                });
                dropdown.classList.toggle('hidden');
            }

            function showTechnicianDetails(button) {
                const tr = button.closest('tr');

                const name = tr.getAttribute('data-name');
                const email = tr.getAttribute('data-email');
                const phone = tr.getAttribute('data-phone_number');

                document.getElementById('name').textContent = `: ${name}`;
                document.getElementById('email').textContent = `: ${email}`;
                document.getElementById('nomorHandphone').textContent = `: ${phone}`;

                openModal('detailTechnicianModal');
            }


            function editTechnician(button) {
                const row = button.closest('tr');
                document.getElementById('editTechnicianName').value = row.dataset.name;
                document.getElementById('editTechnicianEmail').value = row.dataset.email;
                document.getElementById('editTechnicianHandphone').value = row.dataset.phone_number;

                document.getElementById('editTechnicianForm').action = `/update-data-teknisi/${row.dataset.id}`;
                openModal('editTechnicianModal');
            }


            let deleteTechnicianId = null;

            function deleteTechnician(button) {
                const row = button.closest('tr');
                deleteTechnicianId = row.dataset.id;
                document.getElementById('technicianToDeleteName').textContent = `"${row.dataset.name}"`;
                openModal('confirmDeleteModal');
            }

            document.getElementById('confirmDeleteButton').addEventListener('click', function() {
                if (deleteTechnicianId) {
                    const form = document.getElementById('deleteTechnicianForm');
                    form.action = `/delete-data-teknisi/${deleteTechnicianId}`;
                    form.submit();
                }
            });

            function openModal(id) {
                document.getElementById(id).classList.remove('hidden');
            }

            function closeModal(id) {
                document.getElementById(id).classList.add('hidden');
            }

            document.getElementById('search').addEventListener('input', function() {
                const keyword = this.value.toLowerCase();
                const rows = document.querySelectorAll('.technician-row');

                rows.forEach(row => {
                    const name = row.dataset.name.toLowerCase();
                    const email = row.dataset.email.toLowerCase();
                    const phone = row.dataset.phone_number.toLowerCase();

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