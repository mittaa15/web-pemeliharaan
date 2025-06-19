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
                    class="h-8 w-8 text-gray-600 hover:text-blue-700 transition duration-150 ease-in-out" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.31317 12.463C6.20006 9.29213 8.60976 6.6252 11.701 6.5C14.7923 6.6252 17.202 9.29213 17.0889 12.463C17.0889 13.78 18.4841 15.063 18.525 16.383C18.525 16.4017 18.525 16.4203 18.525 16.439C18.5552 17.2847 17.9124 17.9959 17.0879 18.029H13.9757C13.9786 18.677 13.7404 19.3018 13.3098 19.776C12.8957 20.2372 12.3123 20.4996 11.701 20.4996C11.0897 20.4996 10.5064 20.2372 10.0923 19.776C9.66161 19.3018 9.42346 18.677 9.42635 18.029H6.31317C5.48869 17.9959 4.84583 17.2847 4.87602 16.439C4.87602 16.4203 4.87602 16.4017 4.87602 16.383C4.91795 15.067 6.31317 13.781 6.31317 12.463Z"
                        stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M9.42633 17.279C9.01212 17.279 8.67633 17.6148 8.67633 18.029C8.67633 18.4432 9.01212 18.779 9.42633 18.779V17.279ZM13.9757 18.779C14.3899 18.779 14.7257 18.4432 14.7257 18.029C14.7257 17.6148 14.3899 17.279 13.9757 17.279V18.779ZM12.676 5.25C13.0902 5.25 13.426 4.91421 13.426 4.5C13.426 4.08579 13.0902 3.75 12.676 3.75V5.25ZM10.726 3.75C10.3118 3.75 9.97601 4.08579 9.97601 4.5C9.97601 4.91421 10.3118 5.25 10.726 5.25V3.75ZM9.42633 18.779H13.9757V17.279H9.42633V18.779ZM12.676 3.75H10.726V5.25H12.676V3.75Z"
                        fill="#000000" />
                </svg>

                @if($notifications->count() > 0)
                <span
                    class="absolute top-2 right-1 inline-block w-2 h-2 bg-red-600 rounded-full ring-2 ring-white"></span>
                @endif

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