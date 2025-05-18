<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-bold mb-4">Jadwalkan Perbaikan</h2>
        <div class="mb-4">
            <label class="block mb-1 text-sm font-medium text-gray-700">Nama Teknisi</label>
            <input id="technicianName" type="text" class="w-full border border-gray-300 rounded p-2"
                placeholder="Masukkan nama teknisi">
        </div>
        <div class="mb-4">
            <label class="block mb-1 text-sm font-medium text-gray-700">Tanggal Perbaikan</label>
            <input id="repairDate" type="date" class="w-full border border-gray-300 rounded p-2">
        </div>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('scheduleModal')" class="px-4 py-2 rounded bg-gray-300">Batal</button>
            <button onclick="submitSchedule()" class="px-4 py-2 rounded bg-green-500 text-white">Jadwalkan</button>
        </div>
    </div>
</div>