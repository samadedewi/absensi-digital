@extends('layouts.app')

@section('title', 'Riwayat Presensi')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Riwayat Presensi</h1>
            <p class="text-slate-400">Log kehadiran mahasiswa berdasarkan jadwal</p>
        </div>

    </div>

    <!-- Filter Card -->
    <div class="glass p-6 rounded-3xl border border-white/5 shadow-2xl">
        <form action="{{ route('attendance.history') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-4 py-2 rounded-xl bg-slate-900/50 border border-white/10 text-white text-sm outline-none focus:border-cyan-500">
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Dosen</label>
                <select name="dosen_id" class="w-full px-4 py-2 rounded-xl bg-slate-900/50 border border-white/10 text-white text-sm outline-none focus:border-cyan-500">
                    <option value="" class="bg-slate-900">Semua Dosen</option>
                    @foreach($dosens as $d)
                        <option value="{{ $d->id }}" {{ request('dosen_id') == $d->id ? 'selected' : '' }} class="bg-slate-900">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Mata Kuliah</label>
                <select name="matakuliah_id" class="w-full px-4 py-2 rounded-xl bg-slate-900/50 border border-white/10 text-white text-sm outline-none focus:border-cyan-500">
                    <option value="" class="bg-slate-900">Semua Mata Kuliah</option>
                    @foreach($matakuliahs as $m)
                        <option value="{{ $m->id }}" {{ request('matakuliah_id') == $m->id ? 'selected' : '' }} class="bg-slate-900">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3 flex items-end gap-2">
                <button type="submit" class="flex-1 px-3 py-2.5 bg-cyan-600 text-white font-bold rounded-xl hover:bg-cyan-500 transition-all text-[10px] uppercase tracking-widest text-center">Filter</button>
                <a href="{{ route('attendance.history') }}" class="flex-1 px-3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl hover:bg-slate-700 transition-all text-[10px] uppercase tracking-widest text-center">Reset</a>
                <a href="{{ route('attendance.print', request()->all()) }}" target="_blank" class="flex-1 px-3 py-2.5 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-500 transition-all text-[10px] uppercase tracking-widest flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </a>
            </div>



        </form>
    </div>


    <!-- Table -->
    <div class="glass rounded-3xl shadow-2xl border border-white/5 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-white/5 border-b border-white/10">
                <tr>
                    <th class="px-6 py-4 text-sm font-bold text-slate-300">Mahasiswa</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-300">Mata Kuliah</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-300">Waktu</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-300">Status</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-300">Verifikasi Wajah</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-300">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">

                @forelse($attendances as $a)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-cyan-500/10 rounded-full flex items-center justify-center text-cyan-400 text-xs font-bold">
                                {{ substr($a->student->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-white text-sm">{{ $a->student->name }}</span>
                                <span class="text-[10px] font-mono text-slate-500">{{ $a->student->nim }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-slate-300">{{ $a->jadwal->matakuliah->name }}</span>
                            <span class="text-[10px] text-slate-500">Dosen: {{ $a->jadwal->dosen->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col text-xs">
                            <span class="font-bold text-slate-300">{{ $a->date }}</span>
                            <span class="text-slate-500">{{ $a->time }}</span>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        @if($a->status == 'hadir')
                            <span class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-bold uppercase rounded-md border border-green-100">Hadir</span>
                        @else
                            <div class="flex flex-col gap-1">
                                <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold uppercase rounded-md border border-amber-100 w-fit">Terlambat</span>
                                @php
                                    $checkIn = \Carbon\Carbon::parse($a->time);
                                    $start = \Carbon\Carbon::parse($a->jadwal->jam_mulai);
                                    $diff = $start->diffInMinutes($checkIn);
                                @endphp
                                <span class="text-[9px] text-amber-500 font-medium">Lama: {{ $diff }} menit</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($a->face_verified)
                            <div class="flex items-center gap-2 group cursor-help">
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase rounded-md border border-emerald-100 flex items-center gap-1">
                                    (v) Terverifikasi
                                </span>

                                <span class="hidden group-hover:block text-[9px] text-slate-400 font-mono">dist: {{ $a->face_distance }}</span>
                            </div>
                        @else
                            <span class="px-2 py-1 bg-slate-50 text-slate-400 text-[10px] font-bold uppercase rounded-md border border-slate-100">Tanpa Wajah</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="confirmDelete('{{ $a->id }}')" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Hapus Riwayat">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                        <form id="delete-form-{{ $a->id }}" action="{{ route('attendance.destroy', $a->id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">Belum ada data riwayat absensi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-6 border-t border-slate-50">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data absensi ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            border: 'none',
            borderRadius: '1.5rem',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush
@endsection

