@extends('layout.sarprasLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Data Gedung')
</head>

@section('content')
<style>
    .sort-arrow {
        font-size: 0.6rem;
        user-select: none;
        color: rgba(255, 255, 255, 0.6);
        /* warna abu2 terang */
        transition: color 0.3s ease;
    }

    .sort-arrow.active {
        font-size: 0.8rem;
        color: white;
        /* warna utama saat aktif */
    }
</style>


<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-6 sm:px-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-4 sm:space-y-0">
            <h1 class="text-primary font-bold text-xl">Daftar Gedung</h1>
            <div>
                <input id="search" type="text" placeholder="Cari fasilitas..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-full sm:w-64 text-sm" />
            </div>
        </div>
        <hr class="border-black mb-6" />

        <div class="space-y-6">
            <!-- Indoor and Outdoor Facilities with Flexbox -->
            <div class="flex flex-col sm:flex-row sm:space-x-8">
                <!-- Indoor Facilities -->
                <div class="w-full overflow-x-auto">
                    <table class="table w-full min-w-[600px] text-sm text-left text-gray-600 border"
                        id="indoorFacilitiesTable">
                        <thead class="bg-primary text-xs uppercase text-white">
                            <tr>
                                <th class="px-4 py-3 cursor-pointer" data-sortable="true" data-column="name">
                                    Nama Fasilitas <span class="sort-arrow ml-1">▲▼</span>
                                </th>
                                <th class="px-4 py-3 cursor-pointer" data-sortable="true" data-column="description">
                                    Deskripsi <span class="sort-arrow ml-1">▲▼</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($facilities as $facility)
                            <tr class="facility-row hover:bg-gray-100 cursor-pointer" data-id="{{ $facility->id }}"
                                data-name="{{ $facility->building_name }}"
                                data-description="{{ $facility->description }}">
                                <td class="px-4 py-3">{{ $facility->building_name }}</td>
                                <td class="px-4 py-3">{{ $facility->description }}</td>
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
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailFacilityModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg sm:w-1/2">
        <h2 class="text-lg text-primary font-bold mb-4">Detail Fasilitas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-y-2 text-sm text-gray-700">
            <div class="font-semibold">Nama</div>
            <div class="sm:col-span-2" id="facilityName">:</div>
            <div class="font-semibold">Deskripsi</div>
            <div class="sm:col-span-2" id="facilityDescription">:</div>
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="closeModal('detailFacilityModal')"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Tutup</button>
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

    // Render laporan detail ke dalam modal
    function showFacilityDetails(button) {
        const row = button.closest('tr');
        document.getElementById('facilityName').textContent = row.dataset.name;
        document.getElementById('facilityDescription').textContent = row.dataset.description;
        openModal('detailFacilityModal');
    }

    document.querySelectorAll('.facility-row').forEach(row => {
        row.addEventListener('click', function() {
            const name = this.dataset.name;
            const description = this.dataset.description;

            document.getElementById('facilityName').textContent = name;
            document.getElementById('facilityDescription').textContent = description;

            openModal('detailFacilityModal');
        });
    });

    // Event klik baris untuk lihat detail fasilitas
    document.querySelectorAll('.facility-row').forEach(row => {
        row.addEventListener('click', function() {
            const name = this.dataset.name;
            const description = this.dataset.description;

            document.getElementById('facilityName').textContent = name;
            document.getElementById('facilityDescription').textContent = description;

            openModal('detailFacilityModal');
        });
    });

    // Fungsi sorting tabel berdasarkan kolom dan update tanda panah
    function sortTable(tableId, column, asc = true) {
        const table = document.getElementById(tableId);
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const aText = a.querySelector(`td:nth-child(${column + 1})`).textContent.trim().toLowerCase();
            const bText = b.querySelector(`td:nth-child(${column + 1})`).textContent.trim().toLowerCase();

            if (aText < bText) return asc ? -1 : 1;
            if (aText > bText) return asc ? 1 : -1;
            return 0;
        });

        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));

        const headers = table.querySelectorAll('thead th[data-sortable="true"]');
        headers.forEach((header, idx) => {
            const arrowSpan = header.querySelector('.sort-arrow');
            if (idx === column) {
                arrowSpan.textContent = asc ? '▲' : '▼';
                arrowSpan.classList.add('active');
            } else {
                arrowSpan.textContent = '▲▼';
                arrowSpan.classList.remove('active');
            }
        });
    }

    let sortDirections = {
        name: true,
        description: true
    };

    document.querySelectorAll('#indoorFacilitiesTable thead th[data-sortable="true"]').forEach((header, index) => {
        header.addEventListener('click', () => {
            const columnName = header.getAttribute('data-column');
            const asc = sortDirections[columnName];
            sortTable('indoorFacilitiesTable', index, asc);
            sortDirections[columnName] = !asc;
        });
    });



    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const table = document.getElementById('indoorFacilitiesTable');
        const tbody = table.querySelector('tbody');
        const allRows = Array.from(tbody.querySelectorAll('tr.facility-row'));

        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        const pageIndicator = document.getElementById('pageIndicator');

        let currentPage = 1;
        const rowsPerPage = 10;

        function getFilteredRows() {
            const query = searchInput.value.toLowerCase();
            return allRows.filter(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                const desc = row.getAttribute('data-description').toLowerCase();
                return name.includes(query) || desc.includes(query);
            });
        }

        function renderTable() {
            const filtered = getFilteredRows();
            const totalPages = Math.ceil(filtered.length / rowsPerPage);

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            tbody.innerHTML = '';
            filtered.slice(start, end).forEach(row => tbody.appendChild(row));

            // Pagination UI
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
            pageIndicator.textContent = `Page ${totalPages === 0 ? 0 : currentPage} of ${totalPages}`;
        }

        // Event listeners
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            renderTable();
        });

        prevBtn.addEventListener('click', () => {
            currentPage--;
            renderTable();
        });

        nextBtn.addEventListener('click', () => {
            currentPage++;
            renderTable();
        });

        renderTable(); // Initial render
    });
</script>

@endsection