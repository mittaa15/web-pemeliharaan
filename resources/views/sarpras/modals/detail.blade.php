<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 relative">
        <h2 class="text-xl font-bold mb-4 text-primary">Detail Laporan</h2>
        <table class="w-full text-sm text-gray-600" id="detailContent"></table>
        <div class="mt-6 flex justify-end gap-3">
            <button id="rejectButton" onclick="openModal('rejectModal')"
                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Tolak</button>
            <button id="approveButton" onclick="openModal('scheduleModal')"
                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Setujui</button>
            <button onclick="closeModal('detailModal')" class="bg-gray-300 text-black px-4 py-2 rounded">Tutup</button>
        </div>
    </div>
</div>