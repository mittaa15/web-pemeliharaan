@extends('layout.userLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl">Laporan Perbaikan</h1>
        <hr class="border-black mt-5">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <div class="py-2 mt-4">
                    <label class="block text-gray-400 mb-2">Nama Pelapor<span class="text-red-600">*</span></label>
                    <input type="text"
                        class="input block w-full bg-white input-bordered border-gray-300 focus:text-black" />
                </div>

                <div class="py-2">
                    <label class=" block text-gray-400 mb-2">Gedung<span class="text-red-600">*</span></label>
                    <select class="input block w-full bg-white input-bordered border-gray-300 focus:text-black">
                        <option value="">Pilih Gedung</option>
                        <option value="gedung1">Gedung 1</option>
                        <option value="gedung2">Gedung 2</option>
                        <option value="gedung3">Gedung 3</option>
                    </select>
                </div>
            </div>
            <div>
                <div class="py-2 mt-4">
                    <label class=" block text-gray-400 mb-2">Kategori<span class="text-red-600">*</span></label>
                    <select class="input block w-full bg-white input-bordered border-gray-300 focus:text-black">
                        <option value="" class="text-gray-900">Pilih Kategori</option>
                        <option value="kategori1">Kategori 1</option>
                        <option value="kategori2">Kategori 2</option>
                        <option value="kategori3">Kategori 3</option>
                    </select>
                </div>
                <div class="py-2">
                    <label class=" block text-gray-400 mb-2">Ruang<span class="text-red-600">*</span></label>
                    <select class="input block w-full bg-white input-bordered border-gray-300 focus:text-black">
                        <option value="">Pilih Ruang</option>
                        <option value="ruang1">Ruang 1</option>
                        <option value="ruang2">Ruang 2</option>
                        <option value="ruang3">Ruang 3</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="py-2">
            <label class=" block text-gray-400 mb-2">Bukti Kerusakan<span class="text-red-600">*</span></label>
            <input type="file" class="input blok bg-white text-gray-300 w-full" />
            <div class="py-2">
                <label class="block text-gray-400 mb-2">Deskripsi Kerusakan<span class="text-red-600">*</span></label>
                <input type="text"
                    class="input input-xl h-20 block bg-white input-bordered text-gray-300 border-gray-300 w-full text-sm" />
            </div>


            <div class="flex justify-center mt-4">
                <button class="btn w-1/6 bg-primary border-none">Simpan</button>
            </div>
        </div>
    </div>
    @endsection