<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Absensi QR') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        },
                        electric: {
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                        }
                    }
                }
            }
        }

        // Global SweetAlert2 Theme Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#0f172a',
            color: '#f8fafc'
        });

        // Set default Swal theme
        document.addEventListener('DOMContentLoaded', () => {
            const swalConfig = {
                background: '#0f172a',
                color: '#f8fafc',
                confirmButtonColor: '#0891b2',
                cancelButtonColor: '#1e293b',
                customClass: {
                    popup: 'rounded-3xl border border-white/10 shadow-2xl backdrop-blur-xl',
                    title: 'text-white font-bold',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-sm transition-all hover:scale-105',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-sm transition-all hover:scale-105'
                }
            };
            
            // Apply defaults
            const originalFire = Swal.fire;
            Swal.fire = function() {
                if (arguments[0] instanceof Object) {
                    arguments[0] = { ...swalConfig, ...arguments[0] };
                } else if (typeof arguments[0] === 'string') {
                    // Convert simple Swal.fire('Title', 'Text', 'icon') to object
                    const [title, text, icon] = arguments;
                    arguments[0] = { ...swalConfig, title, text, icon };
                }
                return originalFire.apply(Swal, arguments);
            };
        });
    </script>


    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #020617; }
        .glass { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-glass { background: rgba(2, 6, 23, 0.8); backdrop-filter: blur(20px); border-right: 1px solid rgba(255, 255, 255, 0.05); }
    </style>

</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 sidebar-glass text-white flex-shrink-0 flex flex-col shadow-2xl z-20">

        <div class="p-6 flex items-center gap-3">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center overflow-hidden p-1 shadow-inner">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-lg leading-tight tracking-tight text-white">E-Absensi</span>
                <span class="text-[10px] text-indigo-300 font-medium uppercase tracking-wider">Polimdo</span>
            </div>
        </div>

        
        <nav class="flex-1 px-4 space-y-1 mt-4 overflow-y-auto">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="m3 12 2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6">Dashboard</x-nav-link>
            
            <div class="pt-4 pb-2 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Data Master</div>
            <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')" icon="M12 4.354a4 4 0 1 1 0 5.292M15 21H3v-1a6 6 0 0 1 12 0v1zm0 0h6v-1a6 6 0 0 0-9-5.197M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z">Mahasiswa</x-nav-link>
            <x-nav-link :href="route('dosens.index')" :active="request()->routeIs('dosens.*')" icon="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4">Dosen</x-nav-link>
            
            <div class="pt-4 pb-2 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Akademik</div>
            <x-nav-link :href="route('jadwals.index')" :active="request()->routeIs('jadwals.*')" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z">Jadwal Kuliah</x-nav-link>
            <x-nav-link :href="route('attendance.history')" :active="request()->routeIs('attendance.history')" icon="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z">Riwayat Absensi</x-nav-link>
            
            <div class="pt-4 pb-2 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Lainnya</div>
            <a href="{{ route('scan') }}" target="_blank" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl hover:bg-white/10 transition-all text-slate-300">

                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Buka Scanner
            </a>
        </nav>



        <div class="p-4 border-t border-white/5">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl text-rose-400 hover:bg-rose-500/10 transition-all uppercase tracking-widest">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full bg-slate-950 bg-cover bg-center bg-no-repeat relative overflow-hidden" style="background-image: url('{{ asset('img/bg.jpg') }}');">
        {{-- Overlay subtle --}}
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-[1px]"></div>

        <!-- Header -->
        <header class="h-16 bg-white/5 backdrop-blur-xl border-b border-white/5 flex items-center justify-between px-8 relative z-10">
            <h2 class="text-white font-semibold text-lg">@yield('title', 'Dashboard')</h2>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400">Administrator</p>
                </div>

                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold border-2 border-indigo-200">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-8 relative z-10">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center gap-3 animate-slide-in">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm3.707-9.293a1 1 0 0 0-1.414-1.414L9 10.586 7.707 9.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl animate-slide-in">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM8.707 7.293a1 1 0 0 0-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 11.414l1.293 1.293a1 1 0 0 0 1.414-1.414L11.414 10l1.293-1.293a1 1 0 0 0-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span class="font-bold">Terjadi Kesalahan!</span>
                    </div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
