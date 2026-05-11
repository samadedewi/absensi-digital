<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Apakah absensi ini sudah diverifikasi dengan wajah
            $table->boolean('face_verified')->default(false)->after('status');
            // Jarak kosinus hasil DeepFace (semakin kecil = semakin mirip)
            $table->float('face_distance')->nullable()->after('face_verified');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['face_verified', 'face_distance']);
        });
    }
};
