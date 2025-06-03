@extends('layout.userLayout')

@section('content')
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    </div>
    <p class="text-gray-500 mt-1">Pantau status laporan pemeliharaan dengan mudah dan cepat.</p>
</div>

<!-- Modal 1: Pilih Gedung dan Tipe -->
<div id="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-primary">Form Laporan Gedung</h3>
            <button id="closeModal" class="text-gray-600 hover:text-red-600 text-2xl leading-none">&times;</button>
        </div>
        <form id="formLaporanGedung">
            <div class="mb-4">
                <label class="block mb-1 bg-white text-primary px-2 rounded w-fit">Gedung</label>
                <select id="gedungSelect"
                    class="w-full border border-gray-300 p-2 rounded text-sm text-gray-600 bg-white">
                    <option selected disabled value="">Pilih Gedung</option>
                    @foreach ($buildings->sortBy('building_name') as $building)
                    <option value="{{ $building->id }}">{{ $building->building_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-1 bg-white text-primary px-2 rounded w-fit">Indoor/Outdoor</label>
                <select id="tipeSelect"
                    class="w-full border border-gray-300 p-2 rounded text-sm text-gray-600 bg-white">
                    <option selected disabled value="">Pilih Tipe</option>
                    <option>Indoor</option>
                    <option>Outdoor</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                Selanjutnya
            </button>
        </form>
    </div>
</div>

<!-- Modal 2: Daftar Fasilitas -->
<div id="modalFasilitas" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-primary">Pilih Fasilitas</h3>
            <button id="closeModalFasilitas"
                class="text-gray-600 hover:text-red-600 text-2xl leading-none">&times;</button>
        </div>
        <div id="fasilitasList" class="space-y-2 max-h-[60vh] overflow-y-auto pr-1">
            <!-- List fasilitas akan dimasukkan lewat JS -->
        </div>
    </div>
</div>

<!-- Tombol Buka Modal -->
<button id="openModalButton" class="mt-6 bg-primary text-white px-4 py-2 rounded hover:bg-blue-700">
    Buat Laporan Baru
</button>
@endsection

@section('scripts')
<script>
    // Helper Modal
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    document.getElementById('openModalButton').addEventListener('click', () => openModal('modal'));
    document.getElementById('closeModal').addEventListener('click', () => closeModal('modal'));
    document.getElementById('closeModalFasilitas').addEventListener('click', () => closeModal('modalFasilitas'));

    // Ambil data dari controller
    const fasilitasIndoor = @json($indoorFacilities);
    const fasilitasOutdoor = @json($outdoorFacilities);
    const rooms = @json($rooms);
    const buildings = @json($buildings);


    let selectedTipe = '';

    document.getElementById('formLaporanGedung').addEventListener('submit', function(e) {
        e.preventDefault();

        const gedung = document.getElementById('gedungSelect').value;
        const tipe = document.getElementById('tipeSelect').value;

        if (!gedung || !tipe) {
            alert('Silakan pilih gedung dan tipe terlebih dahulu.');
            return;
        }

        selectedTipe = tipe.toLowerCase();

        const building = buildings.find(b => b.id == gedung);
        const buildingName = building ? building.building_name : '';

        console.log(buildingName);

        fasilitasIndoor.forEach(f => f.id_room = null);
        fasilitasOutdoor.forEach(f => f.id_room = null);

        let fasilitasListData = selectedTipe === 'indoor' ? fasilitasIndoor : fasilitasOutdoor;

        // Tambahkan room jika indoor
        if (selectedTipe === 'indoor') {
            const roomFasilitas = rooms.map(room => ({
                facility_name: room.room_name,
                id_building: room.id_building,
                id_room: room.id
            }));
            fasilitasListData = fasilitasListData.concat(roomFasilitas);
        }

        // Filter berdasarkan id gedung
        const filteredFasilitas = fasilitasListData.filter(item => parseInt(item.id_building) === parseInt(gedung));

        console.log(filteredFasilitas);

        // Tampilkan di modal kedua
        const fasilitasListEl = document.getElementById('fasilitasList');
        fasilitasListEl.innerHTML = '';

        filteredFasilitas.forEach(fasilitas => {
            const div = document.createElement('div');
            div.className = 'p-3 border rounded cursor-pointer hover:bg-gray-100 text-gray-600';
            div.textContent = fasilitas.facility_name;

            div.addEventListener('click', () => {
                const encodedFasilitas = encodeURIComponent(fasilitas.facility_name);
                const encodedGedung = encodeURIComponent(gedung);
                const encodedIdRoom = encodeURIComponent(fasilitas.id_room ?? '');
                const encodedBuildingName = encodeURIComponent(buildingName);

                if (!fasilitas.id_room && fasilitas.id) {
                    const encodedFacilityId = encodeURIComponent(fasilitas.id);
                    window.location.href =
                        `/form-pelaporan?fasilitas=${encodedFasilitas}&tipe=${selectedTipe}&gedung=${encodedGedung}&building_name=${encodedBuildingName}&room=${encodedIdRoom}&id_facility=${encodedFacilityId}`;
                } else {
                    // Untuk room atau outdoor
                    window.location.href =
                        `/form-pelaporan?fasilitas=${encodedFasilitas}&tipe=${selectedTipe}&gedung=${encodedGedung}&building_name=${encodedBuildingName}&room=${encodedIdRoom}`;
                }

            });


            fasilitasListEl.appendChild(div);
        });

        closeModal('modal');
        openModal('modalFasilitas');
    });
</script>
@endsection