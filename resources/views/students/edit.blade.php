@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Edit Mahasiswa</h1>
        <p class="text-slate-400 text-sm mt-1">Ubah data mahasiswa. Jika NIM diubah, QR Code baru akan dibuat secara otomatis.</p>
    </div>


    <div class="glass rounded-3xl shadow-2xl border border-white/5 p-6 sm:p-8">

        <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- NIM -->
                <div>
                    <label for="nim" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Nomor Induk Mahasiswa (NIM)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="text" name="nim" id="nim" class="block w-full rounded-xl py-3 px-4 border bg-slate-900/50 border-white/10 text-white focus:border-cyan-500 outline-none transition-all @error('nim') border-red-500 text-red-400 @enderror" value="{{ old('nim', $student->nim) }}" required>
                    </div>
                    @error('nim')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Nama -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="text" name="name" id="name" class="block w-full rounded-xl py-3 px-4 border bg-slate-900/50 border-white/10 text-white focus:border-cyan-500 outline-none transition-all @error('name') border-red-500 text-red-400 @enderror" value="{{ old('name', $student->name) }}" required>
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Photo -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Foto Profil (Biarkan kosong jika tidak ingin mengubah)</label>
                    <div class="mt-2 flex items-center">
                        <span class="inline-block h-14 w-14 rounded-full overflow-hidden bg-white/10 mr-4 border border-white/5 shadow-inner">
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="Current photo" class="h-full w-full object-cover">
                            @else
                                <svg class="h-full w-full text-slate-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            @endif
                        </span>
                        <input type="file" name="photo" id="photo" class="block w-full text-sm text-slate-400 file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white/5 file:text-cyan-400 hover:file:bg-white/10 file:transition-all file:uppercase file:tracking-widest cursor-pointer">
                    </div>
                    @error('photo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div class="mt-8 pt-8 border-t border-white/5 flex items-center justify-end">
                <a href="{{ route('students.index') }}" class="py-2.5 px-6 rounded-xl text-sm font-bold text-slate-400 hover:text-white transition-all uppercase tracking-widest mr-3">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2.5 px-8 rounded-xl text-sm font-bold text-white bg-cyan-600 hover:bg-cyan-500 shadow-2xl shadow-cyan-900/30 transition-all uppercase tracking-widest">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
