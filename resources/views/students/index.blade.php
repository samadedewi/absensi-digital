@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Mahasiswa</h1>
            <p class="text-slate-400 text-sm mt-1">Kelola data dan QR Code mahasiswa</p>
        </div>

        <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl shadow-lg text-sm font-bold text-white bg-cyan-600 hover:bg-cyan-500 transition-all uppercase tracking-widest">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Tambah Mahasiswa
        </a>

    </div>

    <!-- Search Box -->
    <div class="glass p-4 rounded-t-2xl border-b border-white/5 shadow-2xl flex justify-between items-center">
        <form method="GET" action="{{ route('students.index') }}" class="flex w-full max-w-md">
            <div class="relative flex-grow focus-within:z-10">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="block w-full rounded-l-xl pl-10 sm:text-sm border-white/10 py-2.5 border bg-slate-900/50 text-white focus:border-cyan-500 outline-none transition-all placeholder:text-slate-600" placeholder="Cari NIM atau Nama...">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-r-xl border border-l-0 border-white/10 hover:bg-slate-700 transition-all text-sm uppercase tracking-widest">
                Cari
            </button>
        </form>
    </div>


    <!-- Table -->
    <div class="glass shadow-2xl rounded-b-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/5">
                <thead class="bg-white/5">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Mahasiswa</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">NIM</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">QR Code</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">

                    @forelse ($students as $student)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($student->photo)
                                        <img class="h-10 w-10 rounded-full object-cover shadow-lg border border-white/10" src="{{ asset('storage/' . $student->photo) }}" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-cyan-500/10 text-cyan-400 flex items-center justify-center font-bold shadow-inner">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-white">{{ $student->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs text-white bg-slate-800 inline-block px-3 py-1.5 rounded-lg font-mono tracking-wider border border-white/5 shadow-inner">{{ $student->nim }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($student->qr_code)
                                <button type="button" onclick="showQR('{{ asset('storage/' . $student->qr_code) }}', '{{ $student->name }}')" class="inline-flex items-center text-cyan-400 hover:text-cyan-300 bg-cyan-500/10 px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest transition-all hover:bg-cyan-500/20 shadow-sm border border-cyan-500/20">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Lihat QR
                                </button>

                            @else
                                <span class="text-xs text-gray-400">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('students.edit', $student->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form id="delete-form-{{ $student->id }}" action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->name) }}')"
                                    class="text-red-600 hover:text-red-900 font-medium cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <p>Belum ada data mahasiswa.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
        <div class="px-6 py-4 border-t border-white/5 bg-white/5">
            {{ $students->links() }}
        </div>
        @endif

    </div>
</div>

<!-- Modal QR Code -->
<div id="qrModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeQR()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom glass rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full relative z-10 border border-white/10">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-center">
                <h3 class="text-lg leading-6 font-bold text-white mb-4" id="modal-title">
                    QR Code Mahasiswa
                </h3>
                <div class="mt-2 flex justify-center bg-white p-4 rounded-xl shadow-inner">
                    <img id="qrImage" src="" alt="QR Code" class="w-48 h-48">
                </div>
                <p id="qrName" class="mt-4 text-sm text-slate-300 font-bold uppercase tracking-widest"></p>
            </div>
            <div class="bg-white/5 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-white/5">
                <button type="button" onclick="closeQR()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2.5 bg-cyan-600 text-sm font-bold text-white hover:bg-cyan-500 transition-all uppercase tracking-widest sm:ml-3 sm:w-auto">
                    Tutup
                </button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Data?',
            html: `<span class="text-slate-300">Apakah Anda yakin ingin menghapus data mahasiswa <strong>${name}</strong>?</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#1e293b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl glass border border-white/10',
                title: 'text-white font-bold',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold uppercase tracking-widest',
                cancelButton: 'rounded-xl px-6 py-2.5 font-bold uppercase tracking-widest'
            }
        })
.then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    function showQR(url, name) {
        document.getElementById('qrImage').src = url;
        document.getElementById('qrName').innerText = name;
        document.getElementById('qrModal').classList.remove('hidden');
    }

    function closeQR() {
        document.getElementById('qrModal').classList.add('hidden');
    }
</script>
@endpush
