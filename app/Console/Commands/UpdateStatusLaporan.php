<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\RepairSchedule;
use App\Models\RepairReport;
use App\Models\RepairHistory;

class UpdateStatusLaporan extends Command
{
    protected $signature = 'laporan:update-status';
    protected $description = 'Update status laporan ke "Dalam Proses Pengerjaan" jika repair_date sudah sampai.';

    public function handle()
    {
        $today = Carbon::today();

        // Ambil semua jadwal yang repair_date-nya sudah lewat atau hari ini
        $schedules = RepairSchedule::whereDate('repair_date', '<=', $today)->get();

        $updatedCount = 0;

        foreach ($schedules as $schedule) {
            // Cari laporan yang statusnya masih "Dijadwalkan"
            $report = RepairReport::where('id', $schedule->id_report)
                ->where('status', 'Dijadwalkan')
                ->first();

            if ($report) {
                // Update status laporan
                $report->status = 'Dalam Proses Pengerjaan';
                $report->save();

                // Tambahkan ke repair history
                RepairHistory::create([
                    'id_report' => $report->id,
                    'status' => 'Dalam Proses Pengerjaan',
                    'complete_date' => now(),
                ]);

                $updatedCount++;
            }
        }

        $this->info("Status berhasil diperbarui untuk {$updatedCount} laporan.");
    }
}