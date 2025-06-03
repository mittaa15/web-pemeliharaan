<div id="rejectModal" class="fixed inset-0 bg-white bg-opacity-50 flex items-center justify-center hidden z-[9999]">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4 text-primary">Alasan Penolakan</h2>
        <textarea id="rejectReason" rows="4"
            class="w-full border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white"
            placeholder="Tulis alasan penolakan di sini..." style="color: black;"></textarea>
        <div class="mt-4 flex justify-end space-x-2">
            <button onclick="closeModal('rejectModal')"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
            <button onclick="submitRejection()"
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Kirim</button>
        </div>
    </div>
</div>