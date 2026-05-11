@extends('layouts.app')

@section('title', 'Jadwal Perkuliahan')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Jadwal Kuliah</h1>
            <p class="text-slate-400">Atur waktu perkuliahan untuk setiap kelas</p>
        </div>
        <a href="{{ route('jadwals.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl shadow-lg shadow-cyan-900/30 transition-all uppercase tracking-widest text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Jadwal
        </a>

    </div>

    <div class="glass rounded-3xl shadow-2xl border border-white/5 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-white/5 border-b border-white/10">
                <tr>
                    <th class="px-6 py-4 text-sm font-bold text-slate-400 uppercase tracking-widest">Waktu</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-400 uppercase tracking-widest">Mata Kuliah</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-400 uppercase tracking-widest">Dosen</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-400 uppercase tracking-widest">Kelas / Sem</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-400 text-right uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">

                @forelse($jadwals as $j)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-cyan-400 uppercase tracking-wider">{{ $j->hari }}</span>
                            <span class="text-xs text-slate-400 font-mono">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-white">{{ $j->matakuliah->name }}</span>
                            <span class="text-xs text-slate-500 font-mono tracking-widest">{{ $j->matakuliah->code }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-300">{{ $j->dosen->name }}</td>

                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <span class="px-2 py-1 bg-cyan-500/10 text-cyan-400 text-[10px] font-bold uppercase rounded-md border border-cyan-500/20">{{ $j->kelas->name }}</span>
                            <span class="px-2 py-1 bg-amber-500/10 text-amber-400 text-[10px] font-bold uppercase rounded-md border border-amber-500/20">{{ $j->semester->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        <a href="{{ route('jadwals.edit', $j) }}" class="p-2 text-cyan-400 hover:bg-cyan-500/10 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>

                        <form action="{{ route('jadwals.destroy', $j) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">Belum ada jadwal kuliah.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-6 border-t border-slate-50">
            {{ $jadwals->links() }}
        </div>
    </div>
</div>
@endsection
