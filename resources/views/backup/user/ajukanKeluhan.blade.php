@extends('layout.userLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Ajukan Keluhan</h1>
        <hr class="border-black mb-6">

        {{-- Pesan kesalahan atau sukses --}}
        @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded-md mb-4">
            {{ session('error') }}
        </div>
        @endif
        @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
            {{ session('success') }}
        </div>
        @endif

        {{-- Form Keluhan --}}
        <form action="#" method="POST">
            @csrf
            <div class="mb-4">
                <textarea id="keluhan" name="keluhan" rows="4" class="w-full bg-white border border-gray-300 rounded-md p-2 text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary focus:border-black 
                                 placeholder-gray-600" placeholder="Masukkan penjelasan keluhan"></textarea>
                @error('keluhan')
                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 bg-primary text-white rounded-md focus:outline-none hover:bg-primary-dark">
                    Kirim Keluhan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection