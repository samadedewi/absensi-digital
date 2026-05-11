<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner Presensi Akademik</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .scanner-box { position: relative; border: 4px solid rgba(99,102,241,0.3); border-radius: 2rem; overflow: hidden; }
        .scanner-line { position: absolute; width: 100%; height: 2px; background: #6366f1; top: 0; animation: scan 2s linear infinite; box-shadow: 0 0 15px #6366f1; z-index: 10; }
        @keyframes scan { 0%{top:0} 100%{top:100%} }
        #reader video { object-fit: cover !important; }
        .face-ring { animation: pulse-ring 1.5s ease-in-out infinite; }
        @keyframes pulse-ring { 0%,100%{box-shadow:0 0 0 0 rgba(99,102,241,0.4)} 50%{box-shadow:0 0 0 12px rgba(99,102,241,0)} }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-slate-950 bg-cover bg-center bg-no-repeat relative" style="background-image: url('{{ asset('img/bg.jpg') }}');">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-[2px]"></div>


<div class="w-full max-w-5xl relative">
    {{-- Tombol Kembali --}}
    <div class="absolute -top-12 left-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-slate-400 hover:text-white transition-all font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dashboard
        </a>
    </div>


    {{-- ───────────── STEP INDICATOR ───────────── --}}
    <div class="flex items-center justify-center gap-3 mb-8">
        <div id="step-ind-1" class="flex items-center gap-2 px-4 py-2 rounded-full bg-cyan-600 text-white text-sm font-bold transition-all">
            <span class="w-6 h-6 bg-white text-cyan-600 rounded-full flex items-center justify-center text-xs font-black">1</span>
            Scan QR
        </div>

        <div class="w-8 h-0.5 bg-slate-700"></div>
        <div id="step-ind-2" class="flex items-center gap-2 px-4 py-2 rounded-full bg-slate-800 text-slate-400 text-sm font-bold transition-all">
            <span class="w-6 h-6 bg-slate-700 text-slate-400 rounded-full flex items-center justify-center text-xs font-black">2</span>
            Verifikasi Wajah
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

        {{-- ───────────── PANEL KIRI ───────────── --}}
        <div class="space-y-6">

            {{-- STEP 1: QR Scanner --}}
            <div id="panel-qr">
                <div class="text-center lg:text-left mb-4">
                    <h1 class="text-3xl font-bold text-white mb-1">Presensi Mahasiswa</h1>
                <div class="flex items-center justify-center lg:justify-start gap-2 text-indigo-400 font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="live-clock">00:00:00</span>
                    <span class="text-xs text-slate-500 ml-1">WIB</span>
                </div>
                <p class="text-slate-400 mt-2">Pilih jadwal kuliah dan scan QR Code Anda</p>
                </div>
                <div class="bg-slate-900 p-6 rounded-[2.5rem] border border-slate-800 shadow-2xl">
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Jadwal Kuliah Hari Ini</label>
                        <select id="jadwal_id" class="w-full bg-slate-800 border border-slate-700 text-white rounded-2xl px-4 py-4 outline-none focus:ring-2 focus:ring-cyan-500 transition-all">

                            @forelse($jadwals as $j)
                                <option value="{{ $j->id }}">{{ $j->matakuliah->name }} ({{ $j->kelas->name }})</option>
                            @empty
                                <option disabled>Tidak ada jadwal untuk hari ini</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="scanner-box aspect-square bg-black">
                        <div id="reader" class="w-full h-full"></div>
                        <div class="scanner-line"></div>
                    </div>
                    <div id="result-status" class="mt-4 text-center text-sm font-semibold text-slate-500">
                        Menunggu scan...
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-800">
                        <a href="{{ route('dashboard') }}" class="w-full py-4 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white font-bold rounded-2xl transition-all flex items-center justify-center gap-2 border border-slate-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Batal & Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Face Selfie --}}
            <div id="panel-face" class="hidden">
                <div class="text-center lg:text-left mb-4">
                    <h1 class="text-3xl font-bold text-white mb-1">Verifikasi Wajah</h1>
                    <p class="text-slate-400">Posisikan wajah Anda di tengah lingkaran</p>
                </div>
                <div class="bg-slate-900 p-6 rounded-[2.5rem] border border-slate-800 shadow-2xl space-y-5">

                    {{-- Info mahasiswa yang ditemukan --}}
                    <div id="found-student-info" class="flex items-center gap-3 bg-indigo-900/40 border border-indigo-700/50 rounded-2xl p-4">
                        <img id="found-photo" src="" class="w-12 h-12 rounded-xl object-cover border-2 border-indigo-500" onerror="this.src='https://ui-avatars.com/api/?background=6366f1&color=fff&name=?'">
                        <div>
                            <p class="text-white font-bold text-sm" id="found-name">-</p>
                            <p class="text-indigo-400 font-mono text-xs" id="found-nim">-</p>
                            <p class="text-slate-400 text-xs mt-0.5" id="found-mk">-</p>
                        </div>
                    </div>

                    {{-- Webcam area --}}
                    <div class="relative aspect-square bg-black rounded-3xl overflow-hidden face-ring border-4 border-indigo-600">
                        <video id="selfie-video" class="w-full h-full object-cover" style="transform: scaleX(-1);" autoplay playsinline></video>
                        <canvas id="selfie-canvas" class="hidden"></canvas>
                        {{-- Overlay lingkaran panduan --}}
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-48 h-48 rounded-full border-4 border-white/40 border-dashed"></div>
                        </div>
                        {{-- Preview foto yang sudah diambil --}}
                        <img id="selfie-preview" src="" class="hidden absolute inset-0 w-full h-full object-cover">
                    </div>

                    <div id="face-status" class="text-center text-sm font-semibold text-slate-400">
                        Kamera aktif — siap mengambil foto
                    </div>

                    <div class="flex gap-3">
                        <button id="btn-capture" onclick="captureSelfie()"
                            class="flex-1 py-4 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-2xl transition-all flex items-center justify-center gap-2">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                            Ambil Foto
                        </button>
                        <button id="btn-verify" onclick="submitVerification()" class="hidden flex-1 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Verifikasi
                        </button>
                        <button onclick="retakeSelfie()" id="btn-retake" class="hidden px-4 py-4 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-2xl transition-all">
                            Ulangi
                        </button>
                    </div>

                    <button onclick="cancelFaceStep()" class="w-full py-3 text-slate-500 hover:text-slate-300 text-sm font-medium transition-all">
                        ← Scan QR ulang
                    </button>
                </div>
            </div>

        </div>

        {{-- ───────────── PANEL KANAN: HASIL ───────────── --}}
        <div id="result-card" class="hidden">
            <div class="bg-white rounded-[3rem] p-10 shadow-2xl text-center">
                <div class="relative inline-block mb-6">
                    <img id="res-photo" src="" class="w-32 h-32 rounded-3xl object-cover border-4 border-indigo-500 shadow-lg" onerror="this.src='https://ui-avatars.com/api/?background=6366f1&color=fff&size=128&name=?'">
                    <div id="res-status-badge" class="absolute -bottom-2 -right-2 px-3 py-1 bg-green-500 text-white text-[10px] font-bold rounded-lg uppercase shadow-lg">Hadir</div>
                </div>

                <h2 id="res-name" class="text-2xl font-bold text-slate-800 mb-1">-</h2>
                <p id="res-nim" class="text-indigo-600 font-mono font-bold mb-2">-</p>

                {{-- Badge verifikasi wajah --}}
                <div id="res-face-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold mb-6 bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Wajah Terverifikasi
                </div>

                <div class="bg-slate-50 rounded-2xl p-4 text-left space-y-3 mb-8">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400 font-bold uppercase">Mata Kuliah</span>
                        <span id="res-mk" class="text-sm font-bold text-slate-700 text-right max-w-[60%]">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400 font-bold uppercase">Dosen</span>
                        <span id="res-dosen" class="text-sm font-bold text-slate-700 text-right">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400 font-bold uppercase">Waktu</span>
                        <span id="res-time" class="text-sm font-bold text-slate-700">-</span>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button onclick="resetAll()" class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl transition-all">
                        Selesai — Mahasiswa Berikutnya
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-full py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all block">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>


    </div>
</div>

<script>
// ─────────────────────────────────────────────────────────
// State
// ─────────────────────────────────────────────────────────
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const ROUTE_PROCESS = '{{ route("scan.process") }}';
const ROUTE_VERIFY  = '{{ route("scan.verify") }}';

let isProcessing  = false;
let selfieBase64  = null;
let currentStudent = null; // { id, nim, name, photo }
let currentJadwal  = null; // jadwal_id

// ─────────────────────────────────────────────────────────
// STEP 1 — QR Scanner
// ─────────────────────────────────────────────────────────
const h5q = new Html5Qrcode("reader");

function startScanner() {
    const width = window.innerWidth;
    const qrBoxSize = width < 600 ? 250 : 300; // Lebih besar untuk HP agar mudah membidik

    h5q.start(
        { facingMode: "environment" },
        { 
            fps: 20, // Lebih cepat
            qrbox: { width: qrBoxSize, height: qrBoxSize },
            aspectRatio: 1.0,
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true
            }
        },
        onScanSuccess
    );
}

async function onScanSuccess(nim) {
    if (isProcessing) return;

    const jadwal_id = document.getElementById('jadwal_id').value;
    if (!jadwal_id) {
        Swal.fire('Error', 'Pilih jadwal kuliah terlebih dahulu', 'error');
        return;
    }

    isProcessing = true;
    document.getElementById('result-status').innerText = 'Mencari mahasiswa...';

    try {
        const res  = await postJSON(ROUTE_PROCESS, { nim, jadwal_id });
        const data = await res.json();

        if (!data.success) {
            Swal.fire('Gagal', data.message, 'warning');
            document.getElementById('result-status').innerText = 'Scan ulang...';
            isProcessing = false;
            return;
        }

        // Mahasiswa ditemukan → cek apakah butuh verifikasi wajah
        currentStudent = data.student;
        currentJadwal  = data.jadwal_id;

        if (data.has_face_reference) {
            showFaceStep(data);
        } else {
            // Langsung catat absensi (tanpa wajah)
            submitVerification(true);
        }


    } catch (e) {
        Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
        document.getElementById('result-status').innerText = 'Scan ulang...';
        isProcessing = false;
    }
}

// ─────────────────────────────────────────────────────────
// STEP 2 — Face Selfie
// ─────────────────────────────────────────────────────────
let selfieStream = null;

async function showFaceStep(data) {
    // Hentikan QR scanner
    try { await h5q.stop(); } catch(e) {}

    // Update step indicator
    document.getElementById('step-ind-1').className = 'flex items-center gap-2 px-4 py-2 rounded-full bg-slate-700 text-slate-300 text-sm font-bold transition-all';
    document.getElementById('step-ind-2').className = 'flex items-center gap-2 px-4 py-2 rounded-full bg-cyan-600 text-white text-sm font-bold transition-all';
    document.getElementById('step-ind-2').querySelector('span').className = 'w-6 h-6 bg-white text-cyan-600 rounded-full flex items-center justify-center text-xs font-black';


    // Isi info mahasiswa
    document.getElementById('found-name').innerText = data.student.name;
    document.getElementById('found-nim').innerText  = 'NIM ' + data.student.nim;
    document.getElementById('found-mk').innerText   = data.matakuliah + ' • ' + data.dosen;
    if (data.student.photo) {
        document.getElementById('found-photo').src = data.student.photo;
    }

    // Tampilkan panel wajah, sembunyikan panel QR
    document.getElementById('panel-qr').classList.add('hidden');
    document.getElementById('panel-face').classList.remove('hidden');

    // Aktifkan kamera depan
    try {
        selfieStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
        document.getElementById('selfie-video').srcObject = selfieStream;
        document.getElementById('face-status').innerText = 'Kamera aktif — posisikan wajah Anda';
    } catch (e) {
        document.getElementById('face-status').innerText = 'Kamera tidak dapat diakses: ' + e.message;
    }

    isProcessing = false;
}

function captureSelfie() {
    const video   = document.getElementById('selfie-video');
    const canvas  = document.getElementById('selfie-canvas');
    const preview = document.getElementById('selfie-preview');

    canvas.width  = video.videoWidth  || 640;
    canvas.height = video.videoHeight || 480;

    const ctx = canvas.getContext('2d');
    // Mirror-flip karena kamera depan
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transform

    selfieBase64 = canvas.toDataURL('image/jpeg', 0.85);

    // Tampilkan preview
    preview.src = selfieBase64;
    preview.classList.remove('hidden');
    video.classList.add('hidden');

    document.getElementById('btn-capture').classList.add('hidden');
    document.getElementById('btn-verify').classList.remove('hidden');
    document.getElementById('btn-retake').classList.remove('hidden');
    document.getElementById('face-status').innerText = 'Foto berhasil diambil — klik Verifikasi';
}

function retakeSelfie() {
    selfieBase64 = null;
    document.getElementById('selfie-preview').classList.add('hidden');
    document.getElementById('selfie-video').classList.remove('hidden');

    document.getElementById('btn-capture').classList.remove('hidden');
    document.getElementById('btn-verify').classList.add('hidden');
    document.getElementById('btn-retake').classList.add('hidden');
    document.getElementById('face-status').innerText = 'Kamera aktif — posisikan wajah Anda';
}

async function submitVerification(isDirect = false) {
    if (!currentStudent || !currentJadwal) return;
    if (!isDirect && !selfieBase64) return;

    if (!isDirect) {
        document.getElementById('btn-verify').disabled = true;
        document.getElementById('face-status').innerText = '⏳ Memverifikasi wajah...';
    } else {
        document.getElementById('result-status').innerText = '⏳ Mencatat absensi...';
    }


    try {
        const res  = await postJSON(ROUTE_VERIFY, {
            student_id    : currentStudent.id,
            jadwal_id     : currentJadwal,
            selfie_base64 : selfieBase64,
        });
        const data = await res.json();

        if (data.success) {
            if (!isDirect) stopSelfieCamera();
            showResult(data);

            Swal.fire({
                icon : 'success',
                title: 'Absensi Berhasil!',
                html : `Status: <b>${data.attendance.status.toUpperCase()}</b><br>` +
                       (data.face_verified ? '✅ Wajah terverifikasi' : '⚠️ Tanpa verifikasi wajah'),
                timer: 3000,
                showConfirmButton: false,
            });
        } else {
            document.getElementById('face-status').innerText = '❌ ' + data.message;
            document.getElementById('btn-verify').disabled = false;

            if (data.error === 'face_mismatch') {
                Swal.fire({
                    icon : 'error',
                    title: 'Wajah Tidak Cocok',
                    html : `${data.message}<br><small class="text-gray-400">Jarak: ${data.distance ?? '-'} (threshold: 0.4)</small>`,
                    confirmButtonText: 'Coba Lagi',
                }).then(() => retakeSelfie());
            } else {
                Swal.fire('Gagal', data.message, 'warning');
            }
        }
    } catch(e) {
        document.getElementById('face-status').innerText = 'Terjadi kesalahan jaringan';
        document.getElementById('btn-verify').disabled = false;
        Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
    }
}

function cancelFaceStep() {
    stopSelfieCamera();
    selfieBase64   = null;
    currentStudent = null;
    currentJadwal  = null;
    selfieBase64   = null;

    document.getElementById('panel-face').classList.add('hidden');
    document.getElementById('panel-qr').classList.remove('hidden');
    document.getElementById('result-status').innerText = 'Menunggu scan...';

    // Reset step indicator
    document.getElementById('step-ind-1').className = 'flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-600 text-white text-sm font-bold transition-all';
    document.getElementById('step-ind-1').querySelector('span').className = 'w-6 h-6 bg-white text-indigo-600 rounded-full flex items-center justify-center text-xs font-black';
    document.getElementById('step-ind-2').className = 'flex items-center gap-2 px-4 py-2 rounded-full bg-slate-800 text-slate-400 text-sm font-bold transition-all';

    startScanner();
}

function stopSelfieCamera() {
    if (selfieStream) {
        selfieStream.getTracks().forEach(t => t.stop());
        selfieStream = null;
    }
}

// ─────────────────────────────────────────────────────────
// Tampilkan hasil akhir
// ─────────────────────────────────────────────────────────
function showResult(data) {
    document.getElementById('panel-face').classList.add('hidden');
    document.getElementById('result-card').classList.remove('hidden');

    document.getElementById('res-name').innerText = data.student.name;
    document.getElementById('res-nim').innerText  = 'NIM ' + data.student.nim;
    document.getElementById('res-mk').innerText   = data.course;
    document.getElementById('res-dosen').innerText= data.dosen;
    document.getElementById('res-time').innerText = data.attendance.time;

    const statusEl = document.getElementById('res-status-badge');
    statusEl.innerText = data.attendance.status.toUpperCase();
    statusEl.className = data.attendance.status === 'terlambat'
        ? 'absolute -bottom-2 -right-2 px-3 py-1 bg-amber-500 text-white text-[10px] font-bold rounded-lg uppercase shadow-lg'
        : 'absolute -bottom-2 -right-2 px-3 py-1 bg-green-500 text-white text-[10px] font-bold rounded-lg uppercase shadow-lg';

    const faceBadge = document.getElementById('res-face-badge');
    if (data.face_verified) {
        faceBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold mb-6 bg-emerald-50 text-emerald-700 border border-emerald-200';
        faceBadge.innerHTML = '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Wajah Terverifikasi';
    } else {
        faceBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold mb-6 bg-amber-50 text-amber-700 border border-amber-200';
        faceBadge.innerHTML = '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg> Tanpa Verifikasi Wajah';
    }

    const photoUrl = data.student.photo
        ? `/storage/${data.student.photo}`
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(data.student.name)}&background=6366f1&color=fff&size=128`;
    document.getElementById('res-photo').src = photoUrl;
}

function resetAll() {
    document.getElementById('result-card').classList.add('hidden');
    document.getElementById('panel-qr').classList.remove('hidden');
    document.getElementById('result-status').innerText = 'Menunggu scan...';

    selfieBase64   = null;
    currentStudent = null;
    currentJadwal  = null;
    isProcessing   = false;

    retakeSelfie();

    document.getElementById('step-ind-1').className = 'flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-600 text-white text-sm font-bold transition-all';
    document.getElementById('step-ind-1').querySelector('span').className = 'w-6 h-6 bg-white text-indigo-600 rounded-full flex items-center justify-center text-xs font-black';
    document.getElementById('step-ind-2').className = 'flex items-center gap-2 px-4 py-2 rounded-full bg-slate-800 text-slate-400 text-sm font-bold transition-all';
    document.getElementById('step-ind-2').querySelector('span').className = 'w-6 h-6 bg-slate-700 text-slate-400 rounded-full flex items-center justify-center text-xs font-black';

    startScanner();
}

// ─────────────────────────────────────────────────────────
// Utility
// ─────────────────────────────────────────────────────────
function postJSON(url, body) {
    return fetch(url, {
        method  : 'POST',
        headers : { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body    : JSON.stringify(body),
    });
}

// ─────────────────────────────────────────────────────────
// Live Clock
// ─────────────────────────────────────────────────────────
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('live-clock').innerText = timeString;
}
setInterval(updateClock, 1000);
updateClock();

// ─────────────────────────────────────────────────────────
// Init
// ─────────────────────────────────────────────────────────
startScanner();
</script>

</body>
</html>
