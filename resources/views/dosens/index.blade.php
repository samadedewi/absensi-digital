@extends('layouts.app')

@section('title', 'Manajemen Dosen')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Daftar Dosen</h1>
            <p class="text-slate-400">Kelola data tenaga pengajar akademik</p>
        </div>
        <a href="{{ route('dosens.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl shadow-lg shadow-cyan-900/30 transition-all uppercase tracking-widest text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Dosen
        </a>

    </div>

    <div class="glass rounded-3xl shadow-2xl border border-white/5 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-white/5 border-b border-white/10">
                <tr>
                    <th class="px-6 py-4 text-sm font-bold text-slate-400 uppercase tracking-widest">NIDN</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-400 uppercase tracking-widest">Nama Lengkap</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-400 uppercase tracking-widest">Email</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-400 text-right uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">

                @forelse($dosens as $dosen)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs font-bold px-3 py-1 bg-slate-800 text-cyan-400 rounded-md border border-white/5">{{ $dosen->nidn }}</span>
                    </td>
                    <td class="px-6 py-4 font-bold text-white">{{ $dosen->name }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $dosen->email }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('dosens.edit', $dosen) }}" class="p-2 text-cyan-400 hover:bg-cyan-500/10 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>

                            <form action="{{ route('dosens.destroy', $dosen) }}" method="POST" onsubmit="return confirm('Hapus dosen ini?')">
                                @csrf @method('DELETE')
                                <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                        Belum ada data dosen.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-6 border-t border-slate-50">
            {{ $dosens->links() }}
        </div>
    </div>
</div>
@endsection
