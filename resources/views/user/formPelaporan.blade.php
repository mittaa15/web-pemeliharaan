@extends('layout.userLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Form Pelaporan')
</head>

@section('content')
<div class="p-4 md:p-8 mt-20">
    <div class="bg-white rounded-md w-full py-6 md:py-10 px-4 md:px-10">
        <h1 class="text-primary font-bold text-lg md:text-xl">Form Laporan Perbaikan</h1>
        <hr class="border-black mt-4 md:mt-5">

        <form id="formPelaporan" method="POST" action="{{ route('create-laporan') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_user" value="{{ auth()->user()->id }}">
            <input type="hidden" name="id_building" value="{{ request('gedung') }}">
            <input type="hidden" name="id_room" value="{{ request('room') }}">
            <input type="hidden" name="location_type" value="{{ request('tipe') }}">
            <input type="hidden" name="id_facility_building" value="{{ request('id_facility') }}">
            <input type="hidden" name="room_name" value="{{ request('fasilitas') }}">
            <input type="hidden" name="building_name" value="{{ request('building_name') }}">
            <input type="hidden" name="action" id="formAction" value="">

            {{-- Grid Input --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Ruang/Fasilitas Gedung --}}
                <div class="py-2" id="gedungRuangWrapper">
                    <label class="block text-gray-400 mb-2">Ruang/Fasilitas Gedung<span
                            class="text-red-600">*</span></label>
                    <button type="button"
                        class="input block w-full bg-white input-bordered border-gray-300 text-gray-600 text-left"
                        id="fasilitasBtn">Pilih Fasilitas</button>
                    <input type="text" id="fasilitas" name="fasilitas" class="hidden" value="" readonly />
                </div>

                {{-- Fasilitas --}}
                <div id="wrapperFasilitas" class="py-2">
                    <label class="block text-gray-400 mb-2">Fasilitas<span class="text-red-600">*</span></label>
                    <select name="id_facility_room" id="fasilitasSelect"
                        class="input block w-full bg-white input-bordered border-gray-300 text-black">
                        <option value="" disabled selected>-- Pilih Fasilitas --</option>
                        @foreach ($roomFacilitys as $facility)
                        <option value="{{ $facility->id }}">{{ $facility->facility_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Dampak Kerusakan --}}
                <div class="py-2">
                    <label class="block text-gray-400 mb-2">Dampak Kerusakan<span class="text-red-600">*</span></label>
                    <select name="damage_impact" id="damageImpact"
                        class="input block w-full bg-white input-bordered border-gray-300 text-black" required>
                        <option value="" disabled selected>-- Pilih Dampak --</option>
                        <option value="Keselamatan pengguna">Keselamatan pengguna</option>
                        <option value="Menghambat pekerjaan">Menghambat pekerjaan</option>
                        <option value="Penghentian operasional">Penghentian operasional</option>
                    </select>
                </div>

                {{-- Bukti Kerusakan --}}
                <div class="py-2">
                    <label class="block text-gray-400 mb-2">Bukti Kerusakan<span class="text-red-600">*</span></label>
                    <input type="file" name="damage_photo" id="damagePhoto"
                        class="input block bg-white mt-2 text-gray-600 w-full" required />
                </div>
            </div>

            {{-- Deskripsi Kerusakan --}}
            <div class="py-2">
                <label class="block text-gray-400 mb-2">Deskripsi Kerusakan<span class="text-red-600">*</span></label>
                <textarea name="damage_description" id="damageDescription" rows="4"
                    class="input block bg-white input-bordered text-gray-600 border-gray-300 w-full text-sm resize-none"
                    required></textarea>
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex justify-center mt-6 gap-3">
                <button id="btnSimpan" type="submit"
                    class="btn w-full md:w-1/6 bg-primary border-none text-white">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Pilih Fasilitas --}}
<div id="modalFasilitas" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-primary">Pilih Fasilitas</h3>
            <button id="closeModalFasilitas" class="text-gray-600 hover:text-red-600 text-2xl">&times;</button>
        </div>
        <div>
            <button class="w-full py-2 border rounded hover:bg-gray-100 text-gray-600 text-left"
                onclick="setFasilitas('Ruang Rapat A1')">Ruang Rapat A1</button>
            <button class="w-full py-2 border rounded hover:bg-gray-100 text-gray-600 text-left"
                onclick="setFasilitas('Toilet Pria Lt.2')">Toilet Pria Lt.2</button>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi --}}
<div id="modalKonfirmasi" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl mx-4">
        <h3 class="text-lg font-semibold text-primary mb-2">Konfirmasi</h3>
        <p class="text-gray-700 mb-4">Apakah data yang Anda masukkan sudah sesuai?</p>
        <div class="flex justify-end gap-2">
            <button id="batalSimpan"
                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-gray-800">Batal</button>
            <button id="konfirmasiSimpan" type="submit"
                class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark">Ya, Simpan</button>
        </div>
    </div>
</div>

{{-- Modal Tambah Lagi --}}
<div id="modalTambahLagi" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl mx-4">
        <h3 class="text-lg font-semibold text-primary mb-2">Laporan Berhasil</h3>
        <p class="text-gray-700 mb-4">Apakah Anda ingin menambahkan laporan lain?</p>
        <div class="flex justify-end gap-2">
            <button id="tidakTambah" type="button"
                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-gray-800">Tidak</button>
            <button id="iyaTambah" type="button"
                class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark">Ya</button>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const queryParams = new URLSearchParams(window.location.search);
        const idRoomParam = queryParams.get('room');
        const wrapperFasilitas = document.getElementById('wrapperFasilitas');
        const gedungRuangWrapper = document.getElementById('gedungRuangWrapper');

        if (!idRoomParam || idRoomParam.toLowerCase() === 'git') {
            if (wrapperFasilitas) {
                wrapperFasilitas.style.display = 'none';
            }
            if (gedungRuangWrapper) {
                gedungRuangWrapper.classList.remove('col-span-1');
                gedungRuangWrapper.classList.add('col-span-2'); // Full width
            }
        } else {
            if (wrapperFasilitas) {
                wrapperFasilitas.style.display = 'block';
            }
            if (gedungRuangWrapper) {
                gedungRuangWrapper.classList.remove('col-span-2');
                gedungRuangWrapper.classList.add('col-span-1'); // Normal width
            }
        }


        const formPelaporan = document.getElementById('formPelaporan');
        const fasilitasBtn = document.getElementById('fasilitasBtn');
        const fasilitas = document.getElementById('fasilitas');
        const kerusakanBtn = document.getElementById('kerusakanBtn');
        const modalFasilitas = document.getElementById('modalFasilitas');
        const modalKerusakan = document.getElementById('modalKerusakan');
        const closeModalFasilitas = document.getElementById('closeModalFasilitas');
        const closeModalKerusakan = document.getElementById('closeModalKerusakan');
        const deskripsiInput = document.getElementById('deskripsiKerusakan');
        const btnSimpan = document.getElementById('btnSimpan');
        const batalSimpan = document.getElementById('batalSimpan');
        const konfirmasiSimpan = document.getElementById('konfirmasiSimpan');
        const inputAction = document.getElementById('formAction');
        const modalKonfirmasi = document.getElementById('modalKonfirmasi');
        const modalTambahLagi = document.getElementById('modalTambahLagi');

        // Modal buka dan tutup
        fasilitasBtn?.addEventListener('click', () => modalFasilitas?.classList.remove('hidden'));
        kerusakanBtn?.addEventListener('click', () => modalKerusakan?.classList.remove('hidden'));
        closeModalFasilitas?.addEventListener('click', () => modalFasilitas?.classList.add('hidden'));
        closeModalKerusakan?.addEventListener('click', () => modalKerusakan?.classList.add('hidden'));

        // Pilih fasilitas
        window.setFasilitas = function(val) {
            fasilitas.value = val;
            fasilitasBtn.textContent = val;
            fasilitasBtn.classList.add('text-black');
            modalFasilitas.classList.add('hidden');
        }


        // Warna input aktif
        deskripsiInput?.addEventListener('input', function() {
            this.classList.remove('text-gray-300');
            this.classList.add('text-black');
        });

        // Isi fasilitas dari query param jika ada
        const fasilitasParam = queryParams.get('fasilitas');
        if (fasilitasParam && fasilitas && fasilitasBtn) {
            fasilitas.value = fasilitasParam;
            fasilitasBtn.textContent = fasilitasParam;
            fasilitasBtn.classList.add('text-black');
            fasilitasBtn.disabled = true;
            fasilitasBtn.classList.add('cursor-not-allowed');
            fasilitasBtn.style.backgroundColor = 'rgba(0, 0, 0, 0.1)';
            fasilitasBtn.style.color = 'black';
            fasilitasBtn.style.border = '1px solid #ccc';
        }

        // Simpan konfirmasi


        batalSimpan?.addEventListener('click', function() {
            modalKonfirmasi.classList.add('hidden');
        });
        konfirmasiSimpan?.addEventListener('click', function() {
            inputAction.value = 'dashboard';
            if (formPelaporan.reportValidity()) {
                formPelaporan.submit();
            } else {
                modalKonfirmasi.classList.add('hidden');
            }
        });

        btnSimpan?.addEventListener('click', function(event) {
            event.preventDefault();

            if (!formPelaporan.reportValidity()) {
                // Tampilkan pesan error atau beri tanda di input yang kosong
                formPelaporan.reportValidity(); // ini juga akan trigger pesan validasi browser
                return; // hentikan eksekusi
            }

            if (!idRoomParam || idRoomParam.toLowerCase() === 'git') {
                modalKonfirmasi?.classList.remove('hidden');
            } else {
                modalTambahLagi?.classList.remove('hidden');
            }
        });


        document.getElementById('iyaTambah').addEventListener('click', () => {
            inputAction.value = 'back';
            if (formPelaporan.reportValidity()) {
                formPelaporan.submit();
            } else {
                modalKonfirmasi.classList.add('hidden');
            }
        });

        document.getElementById('tidakTambah').addEventListener('click', () => {
            inputAction.value = 'dashboard';
            if (formPelaporan.reportValidity()) {
                formPelaporan.submit();
            } else {
                modalKonfirmasi.classList.add('hidden');
            }
        });
    });
</script>
@endsection