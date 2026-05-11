<h2>Tambah Mahasiswa</h2>

@if(session('success'))
<p style="color:green">{{ session('success') }}</p>
@endif

<form action="/mahasiswa" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="text" name="nim" placeholder="NIM"><br><br>
    <input type="text" name="nama" placeholder="Nama"><br><br>
    <input type="file" name="foto"><br><br>
    <button type="submit">Simpan</button>
</form>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<hr>

<h3>Data Mahasiswa</h3>

@foreach(\App\Models\Mahasiswa::all() as $m)
    <div style="margin-bottom:20px;">
        <p><b>{{ $m->nama }}</b> ({{ $m->nim }})</p>

        <img src="{{ asset('storage/'.$m->foto) }}" width="100"><br>

        <img src="{{ asset('storage/'.$m->qr_code) }}" width="150">
    </div>
@endforeach