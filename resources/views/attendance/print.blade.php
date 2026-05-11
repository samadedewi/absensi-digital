<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - {{ date('d M Y') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: A4; margin: 0; }
            .no-print { display: none; }
            body { background: white !important; padding: 0 !important; }
            .print-container { 
                width: 210mm; 
                height: 297mm; 
                padding: 20mm !important; 
                margin: 0 !important; 
                box-shadow: none !important;
                border: none !important;
            }
        }
        body { background: #f0f2f5; padding: 40px 0; }
        .print-container { 
            background: white; 
            width: 210mm; 
            min-height: 297mm; 
            margin: 0 auto; 
            padding: 20mm; 
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="antialiased">

    <div class="print-container">

        
        <!-- Header Laporan -->
        <div class="flex items-center justify-between border-b-4 border-double border-black pb-6 mb-8">
            <div class="flex items-center gap-6">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-24 h-24 object-contain">
                <div>
                    <h1 class="text-2xl font-bold uppercase">Kementerian Pendidikan dan Kebudayaan</h1>
                    <h2 class="text-xl font-bold uppercase">Politeknik Negeri Manado</h2>
                    <p class="text-sm">Jl. Kampus Bahu, Manado, Sulawesi Utara</p>
                    <p class="text-sm">Telepon: (0431) 123456 | Website: www.polimdo.ac.id</p>
                </div>
            </div>
        </div>

        <div class="text-center mb-8">
            <h3 class="text-xl font-bold uppercase underline">Laporan Riwayat Presensi Mahasiswa</h3>
            <p class="text-sm mt-1">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
        </div>

        <!-- Tabel Data -->
        <table class="w-full border-collapse border border-black text-sm">
            <thead>
                <tr class="bg-gray-100 uppercase font-bold">
                    <th class="border border-black p-3 text-center w-10">No</th>
                    <th class="border border-black p-3 text-left">Mahasiswa</th>
                    <th class="border border-black p-3 text-left">Mata Kuliah</th>
                    <th class="border border-black p-3 text-center">Tanggal</th>
                    <th class="border border-black p-3 text-center">Waktu</th>
                    <th class="border border-black p-3 text-center">Status</th>
                    <th class="border border-black p-3 text-center">Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $index => $a)
                    <tr>
                        <td class="border border-black p-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-black p-2 font-bold">{{ $a->student->name }}<br><span class="font-normal text-xs text-gray-600">{{ $a->student->nim }}</span></td>
                        <td class="border border-black p-2">{{ $a->jadwal->matakuliah->name }}<br><span class="text-xs text-gray-600">Dosen: {{ $a->jadwal->dosen->name }}</span></td>
                        <td class="border border-black p-2 text-center">{{ \Carbon\Carbon::parse($a->date)->format('d/m/Y') }}</td>
                        <td class="border border-black p-2 text-center">{{ $a->time }}</td>
                        <td class="border border-black p-2 text-center">
                            <span class="font-bold {{ $a->status == 'hadir' ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ strtoupper($a->status) }}
                            </span>
                            @if($a->status == 'terlambat')
                                @php
                                    $checkIn = \Carbon\Carbon::parse($a->time);
                                    $start = \Carbon\Carbon::parse($a->jadwal->jam_mulai);
                                    $diff = $start->diffInMinutes($checkIn);
                                @endphp
                                <div class="text-[10px] text-amber-600">({{ $diff }} menit)</div>
                            @endif
                        </td>
                        <td class="border border-black p-2 text-center">
                            {{ $a->face_verified ? 'Terverifikasi (v)' : 'QR Code' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border border-black p-8 text-center text-gray-500 italic">Tidak ada data presensi yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="mt-12 flex justify-end">
            <div class="text-center w-64">
                <p>Manado, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold mt-1 mb-20">Kepala Bagian Akademik,</p>
                <p class="font-bold underline">( ........................................ )</p>
                <p class="text-sm">NIP. ........................................</p>
            </div>
        </div>

    </div>

    <!-- Floating Print Button (No Print) -->
    <div class="fixed bottom-8 right-8 no-print">
        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-full shadow-2xl font-bold flex items-center gap-3 transition-all active:scale-95">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Sekarang
        </button>
    </div>

    <script>
        // Auto trigger print
        window.onload = function() {
            // setTimeout(() => window.print(), 500);
        }
    </script>

</body>
</html>
