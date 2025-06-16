@extends('layout.adminLayout')

<head>
    <link rel="icon" href="{{ asset('images/ITK_1.png') }}" type="image/png" />
    @section('title', 'Profile')
</head>

@section('content')
<div class="p-8">
    <div class="bg-white shadow-lg rounded-lg w-full p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-primary font-bold text-2xl">Profil Pengguna</h1>
        </div>

        @if(session('success'))
        <div id="success-alert" class="bg-green-100 w-1/2 text-green-800 flex justify-center p-2 mb-4 rounded mx-auto">
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
        <hr class="border-gray-300 mb-6">

        {{-- Tabel Profil Pengguna --}}
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center">
                <span class="text-sm font-medium text-gray-700 w-full sm:w-48">Nama</span>
                <span class="text-sm text-gray-900">{{ $user->name }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center">
                <span class="text-sm font-medium text-gray-700 w-full sm:w-48">Email</span>
                <span class="text-sm text-gray-900">{{ $user->email }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center">
                <span class="text-sm font-medium text-gray-700 w-full sm:w-48">Role</span>
                <span class="text-sm text-gray-900">{{ $user->role }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center">
                <span class="text-sm font-medium text-gray-700 w-full sm:w-48">Tanggal Dibuat</span>
                <span class="text-sm text-gray-900">{{ $user->created_at }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center">
                <span class="text-sm font-medium text-gray-700 w-full sm:w-48">Terakhir Diperbarui</span>
                <span class="text-sm text-gray-900">{{ $user->updated_at }}</span>
            </div>
        </div>


        {{-- Tombol Edit Profil di bawah terakhir diperbarui --}}
        <div class="flex justify-end mt-4">
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full sm:w-auto">
                <button id="ubahPwBtn"
                    class="bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-blue-700 text-sm sm:text-base w-full sm:w-auto">
                    Ubah Password
                </button>
                <button id="editProfileBtn"
                    class="bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-blue-700 text-sm sm:text-base w-full sm:w-auto">
                    Edit Profil
                </button>
            </div>
        </div>

    </div>

    {{-- Modal Edit Profil --}}
    <div id="editProfileModal"
        class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl mx-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-lg font-semibold text-primary">Edit Profil</h5>
                <button id="closeModalEdit"
                    class="text-gray-600 hover:text-red-600 text-2xl leading-none">&times;</button>
            </div>
            <form id="editProfileForm" action="{{ route('update-profile') }}" method="post">
                @csrf
                @method('PATCH')
                <div class="mb-3">
                    <label for="fullName" class="form-label text-primary">Nama Lengkap</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded bg-white text-gray-700"
                        id="fullName" value="{{ $user->name }}" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label text-primary">Email</label>
                    <input type="email" class="w-full p-2 border border-gray-300 rounded bg-gray-200 text-gray-700"
                        id="email" value="{{$user->email}}" disabled>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                        id="saveProfileBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="changePassword" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl mx-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-lg font-semibold text-primary">Ubah Password</h5>
                <button id="closeModalUbah"
                    class="text-gray-600 hover:text-red-600 text-2xl leading-none">&times;</button>
            </div>
            <div id="passwordAlert" class="mb-4 hidden"></div>
            <form id="changePasswordForm" action="{{ route('change-password') }}" method="POST">
                @csrf
                <div class="mb-3 relative">
                    <label for="old_password" class="form-label text-primary">Password Lama</label>
                    <input type="password"
                        class="w-full p-2 pr-10 border border-gray-300 rounded bg-white text-gray-700" id="old_password"
                        name="old_password" required>
                    <div class="absolute right-3 top-9 cursor-pointer" onclick="togglePassword('old_password', this)">
                        <!-- Eye Open -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24"
                            stroke="#000" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 
                         4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye Closed -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-closed hidden" fill="none"
                            viewBox="0 0 24 24" stroke="#000" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 
                         0-8.268-2.943-9.542-7a10.05 10.05 0 012.223-3.592m2.572-1.962A9.969 
                         9.969 0 0112 5c4.478 0 8.27 2.944 9.544 
                         7a9.958 9.958 0 01-4.618 5.383M15 
                         12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                        </svg>
                    </div>
                </div>

                <div class="mb-3 relative">
                    <label for="new_password" class="form-label text-primary">Password Baru</label>
                    <input type="password" class="w-full p-2 border border-gray-300 rounded bg-white text-gray-700"
                        id="new_password" name="new_password" required>
                    <div class="absolute right-3 top-9 cursor-pointer" onclick="togglePassword('new_password', this)">
                        <!-- Eye Open -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24"
                            stroke="#000" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 
                         4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye Closed -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-closed hidden" fill="none"
                            viewBox="0 0 24 24" stroke="#000" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 
                         0-8.268-2.943-9.542-7a10.05 10.05 0 012.223-3.592m2.572-1.962A9.969 
                         9.969 0 0112 5c4.478 0 8.27 2.944 9.544 
                         7a9.958 9.958 0 01-4.618 5.383M15 
                         12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                        </svg>
                    </div>
                </div>

                <div class="mb-3 relative">
                    <label for="new_password_confirmation" class="form-label text-primary">Konfirmasi Password</label>
                    <input type="password" class="w-full p-2 border border-gray-300 rounded bg-white text-gray-700"
                        id="new_password_confirmation" name="new_password_confirmation" required>
                    <div class="absolute right-3 top-9 cursor-pointer"
                        onclick="togglePassword('new_password_confirmation', this)">
                        <!-- Eye Open -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24"
                            stroke="#000" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 
                         4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye Closed -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-closed hidden" fill="none"
                            viewBox="0 0 24 24" stroke="#000" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 
                         0-8.268-2.943-9.542-7a10.05 10.05 0 012.223-3.592m2.572-1.962A9.969 
                         9.969 0 0112 5c4.478 0 8.27 2.944 9.544 
                         7a9.958 9.958 0 01-4.618 5.383M15 
                         12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                        </svg>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                        id="savePasswordBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endsection

    @section('scripts')
    <script>
        function togglePassword(inputId, toggleIconWrapper) {
            const input = document.getElementById(inputId);
            const eyeOpen = toggleIconWrapper.querySelector('.eye-open');
            const eyeClosed = toggleIconWrapper.querySelector('.eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
        // Menangani tombol edit profil untuk menampilkan modal
        document.getElementById('editProfileBtn').addEventListener('click', function() {
            document.getElementById('editProfileModal').classList.remove('hidden');
        });
        document.getElementById('ubahPwBtn').addEventListener('click', function() {
            document.getElementById('changePassword').classList.remove('hidden');
        });

        // Menangani tombol close modal
        document.getElementById('closeModalEdit').addEventListener('click', function() {
            document.getElementById('editProfileModal').classList.add('hidden');
        });
        document.getElementById('closeModalUbah').addEventListener('click', function() {
            document.getElementById('changePassword').classList.add('hidden');
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('changePasswordForm');
            const alertBox = document.getElementById('passwordAlert');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(async response => {
                        if (response.ok) {
                            const data = await response.json();
                            showMessage('success', data.message || 'Password berhasil diubah.');
                            form.reset();
                        } else {
                            const errorData = await response.json();
                            if (errorData.errors) {
                                const messages = Object.values(errorData.errors).flat().join(
                                    '<br>');
                                showMessage('error', messages);
                            } else {
                                showMessage('error', errorData.message || 'Terjadi kesalahan.');
                            }
                        }
                    })
                    .catch(() => {
                        showMessage('error', 'Terjadi kesalahan pada server.');
                    });
            });

            function showMessage(type, message) {
                alertBox.className = 'mb-4 px-4 py-2 rounded ' + (type === 'success' ?
                    'bg-green-100 text-green-800 border border-green-300' :
                    'bg-red-100 text-red-800 border border-red-300');
                alertBox.innerHTML = message;
                alertBox.classList.remove('hidden');
            }
        });
    </script>
    @endsection