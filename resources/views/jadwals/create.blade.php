@extends('layouts.app')

@section('title', 'Tambah Jadwal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-white">Buat Jadwal Perkuliahan</h1>
        <p class="text-slate-400">Atur relasi antara dosen, mata kuliah, dan waktu</p>
    </div>


    <div class="glass rounded-3xl shadow-2xl border border-white/5 p-8">

        <form action="{{ route('jadwals.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Dosen Pengajar</label>
                <select name="dosen_id" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('dosen_id') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all">
                    <option value="" disabled selected class="bg-slate-900">Pilih Dosen</option>
                    @foreach($dosens as $d) <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }} class="bg-slate-900">{{ $d->name }}</option> @endforeach
                </select>
                @error('dosen_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Mata Kuliah</label>
                <select name="matakuliah_id" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('matakuliah_id') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all">
                    <option value="" disabled selected class="bg-slate-900">Pilih Mata Kuliah</option>
                    @foreach($matakuliahs as $m) <option value="{{ $m->id }}" {{ old('matakuliah_id') == $m->id ? 'selected' : '' }} class="bg-slate-900">{{ $m->code }} - {{ $m->name }}</option> @endforeach
                </select>
                @error('matakuliah_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Kelas</label>
                <select name="kelas_id" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('kelas_id') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all">
                    <option value="" disabled selected class="bg-slate-900">Pilih Kelas</option>
                    @foreach($kelas as $k) <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }} class="bg-slate-900">{{ $k->name }}</option> @endforeach
                </select>
                @error('kelas_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Semester</label>
                <select name="semester_id" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('semester_id') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all">
                    <option value="" disabled selected class="bg-slate-900">Pilih Semester</option>
                    @foreach($semesters as $s) <option value="{{ $s->id }}" {{ old('semester_id') == $s->id ? 'selected' : '' }} class="bg-slate-900">{{ $s->name }}</option> @endforeach
                </select>
                @error('semester_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Hari</label>
                <select name="hari" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('hari') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                        <option value="{{ $h }}" {{ old('hari') == $h ? 'selected' : '' }} class="bg-slate-900">{{ $h }}</option>
                    @endforeach
                </select>
                @error('hari') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Jam Mulai</label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('jam_mulai') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all" required>
                    @error('jam_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Jam Selesai</label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}" class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border @error('jam_selesai') border-red-500 @else border-white/10 @enderror text-white outline-none focus:border-cyan-500 transition-all" required>
                    @error('jam_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>


            <div class="md:col-span-2 pt-4">
                <button type="submit" class="w-full py-4 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-2xl shadow-2xl shadow-cyan-900/30 transition-all uppercase tracking-widest text-sm">
                    Simpan Jadwal Kuliah
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
