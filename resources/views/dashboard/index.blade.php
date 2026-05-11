@extends('layouts.app')

@section('title', 'Academic Overview')

@section('content')
<div class="space-y-8">
    <!-- Top Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass p-6 rounded-[2rem] border border-white/5 shadow-2xl flex items-center gap-4 transition-all hover:scale-[1.02]">
            <div class="w-12 h-12 bg-indigo-500/10 text-indigo-400 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 1 1 0 5.292M15 21H3v-1a6 6 0 0 1 12 0v1zm0 0h6v-1a6 6 0 0 0-9-5.197M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mahasiswa</p>
                <p class="text-2xl font-bold text-white">{{ $stats['total_students'] }}</p>
            </div>
        </div>
        <div class="glass p-6 rounded-[2rem] border border-white/5 shadow-2xl flex items-center gap-4 transition-all hover:scale-[1.02]">
            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dosen</p>
                <p class="text-2xl font-bold text-white">{{ $stats['total_dosens'] }}</p>
            </div>
        </div>
        <div class="glass p-6 rounded-[2rem] border border-white/5 shadow-2xl flex items-center gap-4 transition-all hover:scale-[1.02]">
            <div class="w-12 h-12 bg-amber-500/10 text-amber-400 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jadwal Aktif</p>
                <p class="text-2xl font-bold text-white">{{ $stats['total_jadwals'] }}</p>
            </div>
        </div>
        <div class="glass p-6 rounded-[2rem] border border-white/5 shadow-2xl flex items-center gap-4 transition-all hover:scale-[1.02]">
            <div class="w-12 h-12 bg-rose-500/10 text-rose-400 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Presensi</p>
                <p class="text-2xl font-bold text-white">{{ $stats['total_attendances'] }}</p>
            </div>
        </div>

    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="glass p-8 rounded-[2.5rem] border border-white/5 shadow-2xl">
            <h3 class="font-bold text-white mb-6 flex items-center gap-2">
                <div class="w-2 h-6 bg-cyan-500 rounded-full"></div>
                Status Kehadiran
            </h3>
            <canvas id="attendanceChart" height="250"></canvas>
        </div>
        <div class="glass p-8 rounded-[2.5rem] border border-white/5 shadow-2xl">
            <h3 class="font-bold text-white mb-6 flex items-center gap-2">
                <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
                Top 5 Mata Kuliah Teraktif
            </h3>
            <canvas id="courseChart" height="250"></canvas>
        </div>
    </div>

</div>

@push('scripts')
<script>
    const ctx1 = document.getElementById('attendanceChart');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($attendanceStats)) !!}.map(s => s.toUpperCase()),
            datasets: [{
                data: {!! json_encode(array_values($attendanceStats)) !!},
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#06b6d4'],
                borderWidth: 0,
                hoverOffset: 20
            }]
        },
        options: {
            cutout: '70%',
            plugins: { 
                legend: { 
                    position: 'bottom', 
                    labels: { color: '#94a3b8', usePointStyle: true, padding: 20, font: { weight: 'bold' } } 
                } 
            }
        }
    });

    const ctx2 = document.getElementById('courseChart');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: {!! json_encode($courseStats->pluck('name')) !!},
            datasets: [{
                label: 'Jumlah Presensi',
                data: {!! json_encode($courseStats->pluck('total')) !!},
                backgroundColor: '#06b6d4',
                borderRadius: 12,
            }]
        },
        options: {
            scales: { 
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#64748b' }
                }, 
                x: { 
                    grid: { display: false },
                    ticks: { color: '#64748b' }
                } 
            },
            plugins: { legend: { display: false } }
        }
    });

</script>
@endpush
@endsection
