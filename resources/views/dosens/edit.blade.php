@extends('layouts.app')

@section('title', 'Edit Dosen')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('dosens.index') }}" class="text-cyan-400 hover:text-cyan-300 text-sm font-bold flex items-center gap-1 mb-6 uppercase tracking-widest transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-3xl font-bold text-white">Edit Data Dosen</h1>
        <p class="text-slate-400 text-lg mt-1">Perbarui informasi tenaga pengajar</p>
    </div>


    <div class="glass rounded-3xl shadow-2xl border border-white/5 p-8">

        <form action="{{ route('dosens.update', $dosen->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">NIDN</label>
                <input type="text" name="nidn" value="{{ old('nidn', $dosen->nidn) }}" class="w-full px-6 py-4 bg-slate-900/50 border @error('nidn') border-red-500 @else border-white/10 @enderror rounded-2xl outline-none focus:border-cyan-500 focus:bg-slate-900 text-white transition-all placeholder:text-slate-600" placeholder="Contoh: 0102030405" required>
                @error('nidn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $dosen->name) }}" class="w-full px-6 py-4 bg-slate-900/50 border @error('name') border-red-500 @else border-white/10 @enderror rounded-2xl outline-none focus:border-cyan-500 focus:bg-slate-900 text-white transition-all placeholder:text-slate-600" placeholder="Nama Lengkap & Gelar" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Email Institusi</label>
                <input type="email" name="email" value="{{ old('email', $dosen->email) }}" class="w-full px-6 py-4 bg-slate-900/50 border @error('email') border-red-500 @else border-white/10 @enderror rounded-2xl outline-none focus:border-cyan-500 focus:bg-slate-900 text-white transition-all placeholder:text-slate-600" placeholder="dosen@kampus.ac.id" required>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>


            <div class="pt-4">
                <button type="submit" class="w-full py-4 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-2xl shadow-2xl shadow-cyan-900/30 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Perbarui Data Dosen
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
