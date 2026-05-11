@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('jadwals.index') }}" class="text-cyan-400 hover:text-cyan-300 text-sm font-bold flex items-center gap-1 mb-6 uppercase tracking-widest transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-3xl font-bold text-white text-center">Edit Jadwal Perkuliahan</h1>
        <p class="text-slate-400 text-center mt-1">Sesuaikan relasi antara dosen, mata kuliah, dan waktu</p>
    </div>


    <div class="glass rounded-3xl shadow-2xl border border-white/5 p-8">

        <form action="{{ route('jadwals.update', $jadwal->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Dosen Pengajar</label>
                <select name="dosen_id" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('dosen_id') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all">
                    @foreach($dosens as $d) <option value="{{ $d->id }}" {{ old('dosen_id', $jadwal->dosen_id) == $d->id ? 'selected' : '' }} class="bg-slate-900">{{ $d->name }}</option> @endforeach
                </select>
                @error('dosen_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Mata Kuliah</label>
                <select name="matakuliah_id" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('matakuliah_id') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all">
                    @foreach($matakuliahs as $m) <option value="{{ $m->id }}" {{ old('matakuliah_id', $jadwal->matakuliah_id) == $m->id ? 'selected' : '' }} class="bg-slate-900">{{ $m->code }} - {{ $m->name }}</option> @endforeach
                </select>
                @error('matakuliah_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Kelas</label>
                <select name="kelas_id" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('kelas_id') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all">
                    @foreach($kelas as $k) <option value="{{ $k->id }}" {{ old('kelas_id', $jadwal->kelas_id) == $k->id ? 'selected' : '' }} class="bg-slate-900">{{ $k->name }}</option> @endforeach
                </select>
                @error('kelas_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Semester</label>
                <select name="semester_id" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('semester_id') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all">
                    @foreach($semesters as $s) <option value="{{ $s->id }}" {{ old('semester_id', $jadwal->semester_id) == $s->id ? 'selected' : '' }} class="bg-slate-900">{{ $s->name }}</option> @endforeach
                </select>
                @error('semester_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Hari</label>
                <select name="hari" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('hari') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                        <option value="{{ $h }}" {{ old('hari', $jadwal->hari) == $h ? 'selected' : '' }} class="bg-slate-900">{{ $h }}</option>
                    @endforeach
                </select>
                @error('hari') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Jam Mulai</label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $jadwal->jam_mulai) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('jam_mulai') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all" required>
                    @error('jam_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Jam Selesai</label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $jadwal->jam_selesai) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('jam_selesai') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all" required>
                    @error('jam_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>


            <div class="md:col-span-2 pt-4">
                <button type="submit" class="w-full py-4 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-2xl shadow-2xl shadow-cyan-900/30 transition-all uppercase tracking-widest text-sm">
                    Perbarui Jadwal Kuliah
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
