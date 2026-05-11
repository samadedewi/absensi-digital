<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_dosens' => Dosen::count(),
            'total_jadwals' => Jadwal::count(),
            'total_attendances' => Attendance::count(),
        ];

        // Stats per Status
        $attendanceStats = Attendance::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        // Stats per Mata Kuliah (Top 5)
        $courseStats = Attendance::join('jadwals', 'attendances.jadwal_id', '=', 'jadwals.id')
            ->join('mata_kuliahs', 'jadwals.matakuliah_id', '=', 'mata_kuliahs.id')
            ->select('mata_kuliahs.name', DB::raw('count(*) as total'))
            ->groupBy('mata_kuliahs.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'attendanceStats', 'courseStats'));
    }
}
