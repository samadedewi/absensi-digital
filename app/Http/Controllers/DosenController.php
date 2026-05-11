<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index()
    {
        $dosens = Dosen::latest()->paginate(10);
        return view('dosens.index', compact('dosens'));
    }

    public function create()
    {
        return view('dosens.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nidn'  => 'required|unique:dosens,nidn',
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:dosens,email',
        ]);

        Dosen::create($validated);
        return redirect()->route('dosens.index')->with('success', 'Dosen berhasil ditambahkan');
    }

    public function show(Dosen $dosen)
    {
        return redirect()->route('dosens.index');
    }

    public function edit(Dosen $dosen)
    {
        return view('dosens.edit', compact('dosen'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $validated = $request->validate([
            'nidn'  => 'required|unique:dosens,nidn,' . $dosen->id,
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:dosens,email,' . $dosen->id,
        ]);

        $dosen->update($validated);
        return redirect()->route('dosens.index')->with('success', 'Data dosen berhasil diperbarui');
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->delete();
        return redirect()->route('dosens.index')->with('success', 'Dosen berhasil dihapus');
    }
}
