<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MahasiswaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|unique:mahasiswa',
            'nama' => 'required',
            'foto' => 'required|image'
        ]);

        // upload foto
        $path = $request->file('foto')->store('mahasiswa', 'public');

        // generate QR (pakai SVG biar aman tanpa imagick)
        $qr = QrCode::format('svg')
            ->size(200)
            ->generate($request->nim);

        // tentukan path file QR
        $qrPath = 'qrcode/' . $request->nim . '.svg';

        // simpan file QR ke storage
        Storage::disk('public')->put($qrPath, $qr);

        // simpan ke database
        Mahasiswa::create([
            'nim' => $request->nim,
            'nama' => $request->nama,
            'foto' => $path,
            'qr_code' => $qrPath
        ]);

        return redirect('/mahasiswa')->with('success', 'Data berhasil disimpan');
    }
}