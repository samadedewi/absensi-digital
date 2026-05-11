<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwals = Jadwal::with(['dosen', 'matakuliah', 'kelas', 'semester'])->latest()->paginate(10);
        return view('jadwals.index', compact('jadwals'));
    }

    public function create()
    {
        $dosens      = Dosen::orderBy('name')->get();
        $matakuliahs = MataKuliah::orderBy('name')->get();
        $kelas       = Kelas::orderBy('name')->get();
        $semesters   = Semester::orderBy('name')->get();
        return view('jadwals.create', compact('dosens', 'matakuliahs', 'kelas', 'semesters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dosen_id'      => 'required|exists:dosens,id',
            'matakuliah_id' => 'required|exists:mata_kuliahs,id',
            'kelas_id'      => 'required|exists:kelas,id',
            'semester_id'   => 'required|exists:semesters,id',
            'hari'          => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai'     => 'required',
            'jam_selesai'   => 'required|after:jam_mulai',
        ], [
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'hari.in'           => 'Hari tidak valid.',
        ]);

        Jadwal::create($validated);
        return redirect()->route('jadwals.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function show(Jadwal $jadwal)
    {
        return redirect()->route('jadwals.index');
    }

    public function edit(Jadwal $jadwal)
    {
        $dosens      = Dosen::orderBy('name')->get();
        $matakuliahs = MataKuliah::orderBy('name')->get();
        $kelas       = Kelas::orderBy('name')->get();
        $semesters   = Semester::orderBy('name')->get();
        return view('jadwals.edit', compact('jadwal', 'dosens', 'matakuliahs', 'kelas', 'semesters'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'dosen_id'      => 'required|exists:dosens,id',
            'matakuliah_id' => 'required|exists:mata_kuliahs,id',
            'kelas_id'      => 'required|exists:kelas,id',
            'semester_id'   => 'required|exists:semesters,id',
            'hari'          => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai'     => 'required',
            'jam_selesai'   => 'required|after:jam_mulai',
        ], [
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'hari.in'           => 'Hari tidak valid.',
        ]);

        $jadwal->update($validated);
        return redirect()->route('jadwals.index')->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('jadwals.index')->with('success', 'Jadwal berhasil dihapus');
    }
}
