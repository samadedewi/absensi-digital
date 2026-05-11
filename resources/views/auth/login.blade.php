<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-slate-950 bg-cover bg-center bg-no-repeat relative" style="background-image: url('{{ asset('img/bg.jpg') }}');">
    {{-- Overlay untuk keterbacaan --}}
    <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-[2px]"></div>


    <div class="relative z-10 w-full max-w-md bg-slate-950/80 backdrop-blur-2xl rounded-[2.5rem] shadow-2xl shadow-black/50 p-10 border border-white/10">


        <div class="text-center mb-10">
            <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-indigo-100 p-2 overflow-hidden">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Polimdo" class="w-full h-full object-contain">
            </div>

            <h1 class="text-3xl font-bold text-white tracking-tight">E-Absensi</h1>
            <p class="text-slate-400 mt-2">Sistem Presensi QR & Face Verification</p>
        </div>


        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Alamat Email</label>
                <input type="email" name="email" class="w-full px-6 py-4 bg-slate-900/50 border border-white/10 rounded-2xl outline-none focus:border-cyan-500 focus:bg-slate-900 text-white transition-all placeholder:text-slate-600" placeholder="admin@example.com" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Password</label>
                <input type="password" name="password" class="w-full px-6 py-4 bg-slate-900/50 border border-white/10 rounded-2xl outline-none focus:border-cyan-500 focus:bg-slate-900 text-white transition-all placeholder:text-slate-600" placeholder="••••••••" required>
            </div>

            @if($errors->any())
                <p class="text-red-400 text-sm text-center font-medium">{{ $errors->first() }}</p>
            @endif

            <button type="submit" class="w-full py-5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-2xl shadow-2xl shadow-cyan-500/20 transition-all active:scale-[0.98] uppercase tracking-widest text-sm">
                Masuk Sekarang
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-slate-500 text-xs italic tracking-widest uppercase font-bold opacity-60">"Sistem Presensi QR & Face ID Terintegrasi"</p>
        </div>

    </div>

</body>
</html>
