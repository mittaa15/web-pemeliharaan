@extends('layout.userLayout')

@section('content')
<div class="p-8">
    <div class="bg-white rounded-md w-full py-10 px-10">
        <h1 class="text-primary font-bold text-xl mb-4">Riwayat Perbaikan</h1>
        <hr class="border-black mb-6">

        <div class="overflow-x-auto">
            <table class="table w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-white uppercase bg-primary">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nomor Pengajuan</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Alasan / Jadwal</th>
                        <th class="px-6 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    {{-- Dynamic content will be injected via JS --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function renderHistory() {
    const historyTableBody = document.getElementById('historyTableBody');
    historyTableBody.innerHTML = '';
    historyData.forEach((report, index) => {
        const row = `
            <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-6 py-4">${index + 1}</td>
                <td class="px-6 py-4">${report.pengajuan}</td>
                <td class="px-6 py-4">${report.status}</td>
                <td class="px-6 py-4">${report.status === 'Ditolak' ? report.reason : `${report.schedule.date} at ${report.schedule.time}`}</td>
                <td class="px-6 py-4">${report.timestamp}</td>
            </tr>
        `;
        historyTableBody.innerHTML += row;
    });
}

// Call the function to render the history data when the page loads
document.addEventListener('DOMContentLoaded', function() {
    renderHistory();
});
</script>
@endsection