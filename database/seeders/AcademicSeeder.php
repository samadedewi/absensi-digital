<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;
use App\Models\MataKuliah;
use App\Models\Kelas;
use App\Models\Dosen;

class AcademicSeeder extends Seeder
{
    public function run(): void
    {
        // Semesters
        $semesters = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6', 'Semester 7', 'Semester 8'];
        foreach ($semesters as $s) {
            Semester::updateOrCreate(['name' => $s], ['is_active' => false]);
        }

        // Kelas
        $kelas = ['TI1', 'TI2', 'TI3', 'TI4', 'TI5', 'TI6', 'TI7'];
        foreach ($kelas as $k) {
            Kelas::updateOrCreate(['name' => $k]);
        }

        // Matakuliahs
        $matakuliahs = [
            ['code' => 'MK01', 'name' => 'Pengolahan Citra'],
            ['code' => 'MK02', 'name' => 'Sistem Pendukung Keputusan'],
            ['code' => 'MK03', 'name' => 'Machine Learning'],
            ['code' => 'MK04', 'name' => 'Pemodelan Perangkat Lunak'],
            ['code' => 'MK05', 'name' => 'Teknologi Web'],
            ['code' => 'MK06', 'name' => 'Sistem Informasi Geografis'],
            ['code' => 'MK07', 'name' => 'Metodologi Penelitian'],
            ['code' => 'MK08', 'name' => 'Data Mining & Data Warehousing'],
            ['code' => 'MK09', 'name' => 'Praktek Teknologi Web'],
            ['code' => 'MK10', 'name' => 'Praktek Pemodelan Perangkat Lunak'],
        ];
        foreach ($matakuliahs as $mk) {
            MataKuliah::updateOrCreate(['code' => $mk['code']], ['name' => $mk['name']]);
        }

        // Dosen (Default)
        Dosen::updateOrCreate(['nidn' => '12345678'], ['name' => 'Dr. Budi Santoso', 'email' => 'budi@kampus.ac.id']);
    }
}
