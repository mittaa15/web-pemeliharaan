@extends('layout.sarprasLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-primary font-bold text-xl">Daftar Fasilitas Gedung</h1>
            <div>
                <input id="search" type="text" placeholder="Cari fasilitas..."
                    class="input input-bordered bg-white text-gray-600 placeholder-gray-600 border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary w-64 text-sm" />
            </div>
        </div>
        <hr class="border-black mb-6">

        <div class="space-y-6">
            <div class="flex justify-between space-x-8">
                <!-- Indoor Facilities -->
                <div class="w-full">
                    <h2 class="text-lg font-bold text-primary mb-2">Indoor</h2>
                    <div>
                        <table class="table w-full text-sm text-left text-gray-600 border" id="indoorFacilitiesTable">
                            <thead class="bg-primary text-xs uppercase text-white">
                                <tr>
                                    <th class="px-6 py-3">Gedung</th>
                                    <th class="px-3 py-3">Nama Fasilitas</th>
                                    <th class="px-3 py-3">Deskripsi</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($indoorFacilities as $facility)
                                <tr class="facility-row" data-id="{{ $facility->id }}"
                                    data-name="{{ strtolower($facility->facility_name) }}"
                                    data-description="{{ strtolower($facility->description) }}">
                                    <td class="px-6 py-3">{{ $facility->building->building_name }}</td>
                                    <td class="px-3 py-3">{{ $facility->facility_name }}</td>
                                    <td class="px-3 py-3">{{ $facility->description }}</td>
                                    <td class="px-6 py-3">
                                        <button onclick="showFacilityDetails(this)"
                                            class="text-primary hover:underline">Lihat Detail</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Outdoor Facilities -->
                <div class="w-full">
                    <h2 class="text-lg font-bold text-primary mb-2">Outdoor</h2>
                    <div>
                        <table class="table w-full text-sm text-left text-gray-600 border" id="outdoorFacilitiesTable">
                            <thead class="bg-primary text-xs uppercase text-white">
                                <tr>
                                    <th class="px-6 py-3">Gedung</th>
                                    <th class="px-3 py-3">Nama Fasilitas</th>
                                    <th class="px-3 py-3">Deskripsi</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($outdoorFacilities as $facility)
                                <tr class="facility-row" data-id="{{ $facility->id }}"
                                    data-name="{{ strtolower($facility->facility_name) }}"
                                    data-description="{{ strtolower($facility->description) }}">
                                    <td class="px-6 py-3">{{ $facility->building->building_name }}</td>
                                    <td class="px-3 py-3">{{ $facility->facility_name }}</td>
                                    <td class="px-3 py-3">{{ $facility->description }}</td>
                                    <td class="px-6 py-3">
                                        <button onclick="showFacilityDetails(this)"
                                            class="text-primary hover:underline">Lihat Detail</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Laporan --}}
<div id="facilityDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-[90%] md:w-2/3 relative max-h-[80vh] overflow-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-primary">Detail Laporan</h2>
            <button onclick="closeModal('facilityDetailModal')"
                class="text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
        </div>

        <div class="overflow-x-auto mb-4">
            <table class="table w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Nomor Laporan</th>
                        <th class="px-6 py-3">Pelapor</th>
                        <th class="px-6 py-3">Teknisi</th>
                        <th class="px-6 py-3">Foto</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody id="facilityDetailContent">
                    {{-- Konten laporan akan dimasukkan melalui JavaScript --}}
                </tbody>
            </table>
        </div>

        <div class="text-right">
            <button onclick="closeModal('facilityDetailModal')"
                class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Tutup</button>
        </div>
    </div>
</div>

<script>
// Filter pencarian fasilitas
document.getElementById('search').addEventListener('input', function() {
    const searchQuery = this.value.toLowerCase();
    const facilityRows = document.querySelectorAll('.facility-row');

    facilityRows.forEach(row => {
        const name = row.getAttribute('data-name');
        const description = row.getAttribute('data-description');
        if (name.includes(searchQuery) || description.includes(searchQuery)) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
});

// Fungsi menampilkan detail laporan fasilitas dari backend
function showFacilityDetails(facilityId) {
    const modal = document.getElementById('facilityDetailModal');
    const facilityDetailContent = document.getElementById('facilityDetailContent');

    // Loading placeholder
    facilityDetailContent.innerHTML = `<tr><td colspan="6" class="text-center py-4">Memuat data...</td></tr>`;

    modal.classList.remove('hidden');

    fetch(`/sarpras/facility-report/${facilityId}`)
        .then(response => {
            if (!response.ok) throw new Error('Gagal memuat data dari server');
            return response.json();
        })
        .then(data => {
            if (!data || data.length === 0) {
                facilityDetailContent.innerHTML =
                    `<tr><td colspan="6" class="text-center py-4">Tidak ada laporan untuk fasilitas ini.</td></tr>`;
                return;
            }

            const rowsHtml = data.map(laporan => {
                const teknisi = laporan.technician_name ?? '-';
                const foto = laporan.damage_photo ?
                    `<img src="/storage/${laporan.damage_photo}" alt="Foto Laporan" class="w-20 h-20 object-cover rounded">` :
                    '-';
                const tanggalFormatted = new Date(laporan.created_at).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                return `
                <tr>
                    <td class="px-6 py-3">${laporan.id}</td>
                    <td class="px-6 py-3">${laporan.user?.name ?? '-'}</td>
                    <td class="px-6 py-3">${teknisi}</td>
                    <td class="px-6 py-3">${foto}</td>
                    <td class="px-6 py-3">${laporan.status}</td>
                    <td class="px-6 py-3">${tanggalFormatted}</td>
                </tr>`;
            }).join('');

            facilityDetailContent.innerHTML = rowsHtml;
        })
        .catch(error => {
            facilityDetailContent.innerHTML =
                `<tr><td colspan="6" class="text-center py-4 text-red-600">Gagal memuat data: ${error.message}</td></tr>`;
        });
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>
@endsection