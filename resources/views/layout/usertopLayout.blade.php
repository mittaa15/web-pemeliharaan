<!-- HEADER FIXED POSISI ATAS -->
<div class="p-2 w-full bg-white">
    <div class="flex items-center gap-4 py-2 sm:py-4 w-full justify-end">

        @php $user = Auth::user(); @endphp

        <span class="text-primary text-sm sm:text-md font-semibold">
            Hi,
            @if ($user->role === 'user')
            {{ $user->name }}
            @elseif ($user->role === 'admin')
            Admin
            @elseif ($user->role === 'sarpras')
            Sarpras
            @else
            {{ $user->name }}
            @endif
            !
        </span>

        <!-- WRAP RELATIVE UNTUK POSISI POPUP -->
        <div class="relative">
            <button id="notificationButton" class="relative focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 text-gray-600 hover:text-blue-700 transition duration-150 ease-in-out" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C8.67 6.165 8 7.388 8 9v5.159c0 .538-.214 1.055-.595 1.436L6 17h5m0 0v1a3 3 0 006 0v-1m-6 0h6" />
                </svg>
                <span
                    class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full ring-2 ring-white"></span>
            </button>

            <!-- POPUP NOTIFIKASI -->
            <div id="notificationPopup"
                class="hidden absolute top-10 right-0 w-64 bg-white border border-gray-300 rounded-lg shadow-lg z-50">
                <div class="p-4">
                    <h3 class="text-md font-semibold text-primary mb-2">Notifikasi</h3>
                    <ul class="space-y-2 max-h-48 overflow-y-auto">
                        @forelse($notifications as $notif)
                        <li class="text-sm text-gray-700 border-b pb-2">
                            <span class="font-bold">{{ $notif->title }}</span><br>
                            <span>{{ $notif->description }}</span>
                        </li>
                        @empty
                        <li class="text-sm text-gray-500">Belum ada notifikasi.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Taruh ini di paling bawah halaman sebelum </body> -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const notificationButton = document.getElementById('notificationButton');
    const notificationPopup = document.getElementById('notificationPopup');

    notificationButton.addEventListener('click', function(event) {
        event.stopPropagation();
        notificationPopup.classList.toggle('hidden');
    });

    document.addEventListener('click', function(event) {
        const isClickInside = notificationButton.contains(event.target) || notificationPopup.contains(
            event.target);
        if (!isClickInside) {
            notificationPopup.classList.add('hidden');
        }
    });
});
</script>