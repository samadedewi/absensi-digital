<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Jadwal;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Services\FaceVerificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function scan()
    {
        $days = [
            'Sunday'    => 'Minggu', 'Monday'  => 'Senin',  'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',   'Thursday' => 'Kamis', 'Friday'  => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $today = $days[Carbon::now()->format('l')];

        $jadwals = Jadwal::with(['matakuliah', 'kelas', 'dosen'])
            ->where('hari', $today)
            ->get();

        return view('attendance.scan', compact('jadwals'));
    }

    /**
     * STEP 1: QR Code di-scan → temukan mahasiswa & cek validasi awal.
     * Jika semua OK, kembalikan data mahasiswa agar frontend bisa tampilkan step selfie.
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'nim'       => 'required',
            'jadwal_id' => 'required|exists:jadwals,id',
        ]);

        $student = Student::where('nim', $request->nim)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan']);
        }

        $jadwal = Jadwal::with(['matakuliah', 'dosen'])->findOrFail($request->jadwal_id);

        // Verifikasi hari
        $days = [
            'Sunday'    => 'Minggu', 'Monday'  => 'Senin',  'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',   'Thursday' => 'Kamis', 'Friday'  => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        if ($jadwal->hari !== $days[Carbon::now()->format('l')]) {
            return response()->json(['success' => false, 'message' => 'Jadwal ini bukan untuk hari ini']);
        }

        // --- VALIDASI WAKTU (Time Window) ---
        $now = Carbon::now();
        $startTime = Carbon::parse($jadwal->jam_mulai);
        $endTime = Carbon::parse($jadwal->jam_selesai);
        
        // Set tanggal ke hari ini agar perbandingannya akurat
        $startTime->setDate($now->year, $now->month, $now->day);
        $endTime->setDate($now->year, $now->month, $now->day);

        // Batas awal absen: 30 menit sebelum mulai
        $earliestTime = $startTime->copy()->subMinutes(30);

        if ($now->lt($earliestTime)) {
            return response()->json([
                'success' => false, 
                'message' => 'Absensi belum dibuka. Silakan kembali 30 menit sebelum jam mulai (' . $startTime->format('H:i') . ')'
            ]);
        }

        if ($now->gt($endTime)) {
            return response()->json([
                'success' => false, 
                'message' => 'Absensi sudah ditutup karena jam matakuliah telah berakhir (' . $endTime->format('H:i') . ')'
            ]);
        }
        // ------------------------------------

        // Cek double absen
        $exists = Attendance::where('student_id', $student->id)
            ->where('jadwal_id', $jadwal->id)
            ->where('date', Carbon::today()->toDateString())
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absensi untuk mata kuliah ini hari ini']);
        }

        // Cek apakah mahasiswa punya foto referensi untuk face verification
        $hasFaceReference = !empty($student->photo);

        return response()->json([
            'success'            => true,
            'step'               => 'face_verification',  // sinyal ke frontend untuk tampilkan step selfie
            'has_face_reference' => $hasFaceReference,
            'student'            => [
                'id'    => $student->id,
                'nim'   => $student->nim,
                'name'  => $student->name,
                'photo' => $student->photo ? asset('storage/' . $student->photo) : null,
            ],
            'jadwal_id'          => $jadwal->id,
            'matakuliah'         => $jadwal->matakuliah->name,
            'dosen'              => $jadwal->dosen->name,
            'message'            => $hasFaceReference
                ? 'Mahasiswa ditemukan. Silakan ambil foto selfie untuk verifikasi wajah.'
                : 'Mahasiswa ditemukan. Foto referensi belum ada — absensi langsung dicatat.',
        ]);
    }

    /**
     * STEP 2: Terima foto selfie → verifikasi wajah → simpan absensi.
     */
    public function verifyAndRecord(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'jadwal_id'     => 'required|exists:jadwals,id',
            'selfie_base64' => 'nullable|string',
        ]);

        $student = Student::findOrFail($request->student_id);
        $jadwal  = Jadwal::with(['matakuliah', 'dosen'])->findOrFail($request->jadwal_id);

        // Cek double absen lagi (perlindungan kedua)
        $todayDate = Carbon::today()->toDateString();
        $exists = Attendance::where('student_id', $student->id)
            ->where('jadwal_id', $jadwal->id)
            ->where('date', $todayDate)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Absensi sudah tercatat sebelumnya']);
        }

        // ── Verifikasi Wajah ─────────────────────────────────────────
        $faceVerified  = false;
        $faceDistance  = null;
        $faceMessage   = '';

        if ($student->photo && $request->filled('selfie_base64')) {
            $faceService = new FaceVerificationService();
            $result      = $faceService->verify($student->photo, $request->selfie_base64);

            if (!$result['success']) {
                // API tidak aktif atau error teknis
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'error'   => $result['error'] ?? 'unknown',
                ]);
            }

            $faceVerified = $result['verified'];
            $faceDistance = $result['distance'];
            $faceMessage  = $result['message'];

            if (!$faceVerified) {
                return response()->json([
                    'success'    => false,
                    'message'    => $faceMessage,
                    'distance'   => $faceDistance,
                    'confidence' => $result['confidence'],
                    'error'      => 'face_mismatch',
                ]);
            }
        } elseif (!$student->photo) {
            // Tidak ada foto referensi → skip verifikasi wajah
            $faceVerified = false;
            $faceMessage  = 'Tidak ada foto referensi';
        }

        // ── Hitung status hadir/terlambat ────────────────────────────
        $now       = Carbon::now();
        $startTime = Carbon::parse($jadwal->jam_mulai);
        $startTime->setDate($now->year, $now->month, $now->day);
        $status = $now->gt($startTime->copy()->addMinutes(15)) ? 'terlambat' : 'hadir';

        // ── Simpan absensi ────────────────────────────────────────────
        $attendance = Attendance::create([
            'student_id'    => $student->id,
            'jadwal_id'     => $jadwal->id,
            'date'          => $todayDate,
            'time'          => $now->toTimeString(),
            'status'        => $status,
            'face_verified' => $faceVerified,
            'face_distance' => $faceDistance,
        ]);

        return response()->json([
            'success'       => true,
            'message'       => 'Absensi berhasil! Status: ' . strtoupper($status),
            'face_verified' => $faceVerified,
            'face_message'  => $faceMessage,
            'student'       => $student,
            'attendance'    => $attendance,
            'course'        => $jadwal->matakuliah->name,
            'dosen'         => $jadwal->dosen->name,
        ]);
    }

    public function findStudent(Request $request)
    {
        $request->validate(['nim' => 'required']);
        $student = Student::where('nim', $request->nim)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Mahasiswa tidak ditemukan']);
        }

        return response()->json(['success' => true, 'student' => $student]);
    }

    public function history(Request $request)
    {
        $query = Attendance::with(['student', 'jadwal.matakuliah', 'jadwal.dosen', 'jadwal.kelas']);

        if ($request->filled('dosen_id')) {
            $query->whereHas('jadwal', fn($q) => $q->where('dosen_id', $request->dosen_id));
        }
        if ($request->filled('matakuliah_id')) {
            $query->whereHas('jadwal', fn($q) => $q->where('matakuliah_id', $request->matakuliah_id));
        }
        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        $attendances = $query->latest()->paginate(15);
        $dosens      = Dosen::orderBy('name')->get();
        $matakuliahs = MataKuliah::orderBy('name')->get();

        return view('attendance.history', compact('attendances', 'dosens', 'matakuliahs'));
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus');
    }

    public function print(Request $request)
    {
        $query = Attendance::with(['student', 'jadwal.matakuliah', 'jadwal.dosen', 'jadwal.kelas']);

        if ($request->filled('dosen_id')) {
            $query->whereHas('jadwal', fn($q) => $q->where('dosen_id', $request->dosen_id));
        }
        if ($request->filled('matakuliah_id')) {
            $query->whereHas('jadwal', fn($q) => $q->where('matakuliah_id', $request->matakuliah_id));
        }
        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        $attendances = $query->latest()->get();
        
        return view('attendance.print', compact('attendances'));
    }
}

