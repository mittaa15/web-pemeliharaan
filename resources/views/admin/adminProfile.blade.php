@extends('layout.adminLayout')

@section('content')
<div class="p-8">
    <div class="bg-white shadow-lg rounded-lg w-full p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-primary font-bold text-2xl">Profil Pengguna</h1>
        </div>
        <hr class="border-gray-300 mb-6">

        {{-- Tabel Profil Pengguna --}}
        <div class="space-y-4">
            <div class="flex justify-start">
                <span class="text-sm font-medium text-gray-700 w-48">Nama Lengkap</span>
                <span class="text-sm text-gray-900">Miftahul Jannah Zahratunnisa</span>
            </div>

            <div class="flex justify-start">
                <span class="text-sm font-medium text-gray-700 w-48">Email</span>
                <span class="text-sm text-gray-900">miftahul.jannah@example.com</span>
            </div>

            <div class="flex justify-start">
                <span class="text-sm font-medium text-gray-700 w-48">Role</span>
                <span class="text-sm text-gray-900">Admin</span>
            </div>

            <div class="flex justify-start">
                <span class="text-sm font-medium text-gray-700 w-48">Tanggal Dibuat</span>
                <span class="text-sm text-gray-900">2023-05-15</span>
            </div>

            <div class="flex justify-start">
                <span class="text-sm font-medium text-gray-700 w-48">Terakhir Diperbarui</span>
                <span class="text-sm text-gray-900">2023-10-01</span>
            </div>
        </div>

        {{-- Tombol Edit Profil di bawah terakhir diperbarui --}}
        <div class="flex justify-end mt-4">
            <button id="editProfileBtn" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Edit
                Profil</button>
        </div>
    </div>
</div>

{{-- Modal Edit Profil --}}
<div id="editProfileModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-semibold text-primary">Edit Profil</h5>
            <button id="closeModal" class="text-gray-600 hover:text-red-600 text-2xl leading-none">&times;</button>
        </div>
        <form id="editProfileForm">
            <div class="mb-3">
                <label for="fullName" class="form-label text-primary">Nama Lengkap</label>
                <input type="text" class="w-full p-2 border border-gray-300 rounded bg-white text-black" id="fullName"
                    value="Miftahul Jannah Zahratunnisa" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label text-primary">Email</label>
                <input type="email" class="w-full p-2 border border-gray-300 rounded bg-white text-black" id="email"
                    value="miftahul.jannah@example.com" disabled>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label text-primary">Password</label>
                <input type="password" class="w-full p-2 border border-gray-300 rounded bg-white text-black"
                    id="password" required>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label text-primary">Konfirmasi Password</label>
                <input type="password" class="w-full p-2 border border-gray-300 rounded bg-white text-black"
                    id="confirmPassword" required>
            </div>
        </form>
        <div class="flex justify-end">
            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                id="saveProfileBtn">Simpan</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Menangani tombol edit profil untuk menampilkan modal
document.getElementById('editProfileBtn').addEventListener('click', function() {
    document.getElementById('editProfileModal').classList.remove('hidden');
});

// Menangani tombol close modal
document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('editProfileModal').classList.add('hidden');
});

// Menangani tombol simpan pada modal
document.getElementById('saveProfileBtn').addEventListener('click', function() {
    const fullName = document.getElementById('fullName').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
        alert("Password dan konfirmasi password tidak cocok!");
        return;
    }

    // Contoh alert, seharusnya bisa diganti dengan AJAX ke server
    alert(`Profil berhasil disimpan: Nama: ${fullName}`);

    // Menutup modal
    document.getElementById('editProfileModal').classList.add('hidden');
});
</script>
@endsection