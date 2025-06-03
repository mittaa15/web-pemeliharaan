<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            if (Schema::hasColumn('notification', 'notification_status')) {
                $table->dropColumn('notification_status');
            }
            if (Schema::hasColumn('notification', 'data_sent')) {
                $table->dropColumn('data_sent');
            }
            if (Schema::hasColumn('notification', 'date_sent')) {
                $table->dropColumn('date_sent');
            }

            // Tambahkan kolom baru
            $table->boolean('isRead')->default(false)->after('id');
            $table->string('title')->nullable()->after('isRead');
            $table->text('description')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->string('notification_status')->nullable()->after('id');
            $table->timestamp('date_sent')->nullable()->after('notification_status');

            // Hapus kolom baru
            $table->dropColumn('isRead');
            $table->dropColumn('title');
            $table->dropColumn('description');
        });
    }
};