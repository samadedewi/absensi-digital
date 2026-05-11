<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Cukup tambahkan jadwal_id karena kolom lain sudah ada di migrasi asli
            if (!Schema::hasColumn('attendances', 'jadwal_id')) {
                $table->foreignId('jadwal_id')->after('student_id')->nullable()->constrained('jadwals')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'jadwal_id')) {
                $table->dropForeign(['jadwal_id']);
                $table->dropColumn('jadwal_id');
            }
        });
    }
};
