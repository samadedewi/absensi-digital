# 📋 Dokumentasi Fitur: Portal Data Karyawan
**Project:** `food-store` (Laravel + Filament Admin Panel)
**Tanggal Pembuatan:** 25 April 2026
**Status:** ✅ Selesai diimplementasi

---

## 1. Ringkasan Fitur

Fitur **Portal Data Karyawan** adalah modul manajemen data pegawai yang diintegrasikan ke dalam admin panel project `food-store`. Fitur ini dibangun di atas **Laravel Filament** dan menyediakan antarmuka CRUD (Create, Read, Update, Delete) yang lengkap untuk mengelola data seluruh karyawan toko.

Modul ini muncul di menu navigasi admin pada grup **"Data Master"** dengan ikon 👥 dan urutan menu ke-2.

---

## 2. Teknologi yang Digunakan

| Komponen       | Teknologi                    |
|----------------|------------------------------|
| Framework      | Laravel (PHP)                |
| Admin Panel    | Filament PHP v3              |
| Database ORM   | Eloquent Model               |
| Skema Database | Laravel Migration            |
| UI Form        | Filament Form Components     |
| UI Tabel       | Filament Table Columns       |

---

## 3. Arsitektur & Struktur File

Fitur ini terdiri dari **5 file** baru yang dibuat di dalam project:

```
food-store/
├── app/
│   ├── Models/
│   │   └── Karyawan.php                          ← Model Eloquent
│   └── Filament/
│       └── Resources/
│           ├── KaryawanResource.php              ← Resource utama (form + tabel)
│           └── KaryawanResource/
│               └── Pages/
│                   ├── ListKaryawans.php         ← Halaman daftar karyawan
│                   ├── CreateKaryawan.php        ← Halaman tambah karyawan
│                   └── EditKaryawan.php          ← Halaman edit karyawan
└── database/
    └── migrations/
        └── 2026_04_25_000000_create_karyawans_table.php  ← Skema database
```

---

## 4. Skema Database

### Tabel: `karyawans`

Dibuat melalui file migration: `2026_04_25_000000_create_karyawans_table.php`

| Kolom              | Tipe Data       | Nullable | Keterangan                  |
|--------------------|-----------------|----------|-----------------------------|
| `id`               | `bigint` (PK)   | ❌        | Primary key auto-increment  |
| `nama`             | `varchar(255)`  | ❌        | Nama lengkap karyawan       |
| `posisi`           | `varchar(255)`  | ❌        | Posisi atau jabatan          |
| `telepon`          | `varchar(255)`  | ✅        | Nomor telepon               |
| `alamat`           | `text`          | ✅        | Alamat lengkap              |
| `tanggal_bergabung`| `date`          | ✅        | Tanggal mulai bekerja       |
| `created_at`       | `timestamp`     | ✅        | Dibuat otomatis Laravel     |
| `updated_at`       | `timestamp`     | ✅        | Diupdate otomatis Laravel   |

```php
// database/migrations/2026_04_25_000000_create_karyawans_table.php
Schema::create('karyawans', function (Blueprint $table) {
    $table->id();
    $table->string('nama');
    $table->string('posisi');
    $table->string('telepon')->nullable();
    $table->text('alamat')->nullable();
    $table->date('tanggal_bergabung')->nullable();
    $table->timestamps();
});
```

---

## 5. Model Eloquent

**File:** `app/Models/Karyawan.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'posisi',
        'telepon',
        'alamat',
        'tanggal_bergabung',
    ];
}
```

**Penjelasan:**
- `HasFactory` — memungkinkan pembuatan data dummy via Factory/Seeder.
- `$fillable` — mendaftarkan kolom yang boleh diisi secara mass-assignment (keamanan dari mass-assignment vulnerability).

---

## 6. Filament Resource

**File:** `app/Filament/Resources/KaryawanResource.php`

Resource ini adalah "jantung" dari fitur portal karyawan. Di sinilah tampilan form dan tabel dikonfigurasi.

### 6.1 Konfigurasi Navigasi

```php
protected static ?string $navigationIcon  = 'heroicon-o-users';   // Ikon pengguna
protected static ?string $navigationGroup = 'Data Master';         // Grup menu
protected static ?string $navigationLabel = 'Karyawan';            // Label menu
protected static ?string $pluralModelLabel = 'Data Karyawan';      // Judul halaman
```

### 6.2 Form Input (Tambah / Edit Data)

Form menggunakan 2-kolom layout dengan komponen:

| Field              | Komponen Filament          | Validasi       |
|--------------------|----------------------------|----------------|
| Nama Lengkap       | `TextInput`                | Required, max 255 |
| Posisi / Jabatan   | `TextInput`                | Required, max 255 |
| Nomor Telepon      | `TextInput` (type: tel)    | Optional, max 255 |
| Tanggal Bergabung  | `DatePicker`               | Optional       |
| Alamat Lengkap     | `Textarea`                 | Optional, full-width |

### 6.3 Tampilan Tabel (Daftar Karyawan)

| Kolom              | Fitur Tambahan             |
|--------------------|----------------------------|
| Nama               | Searchable ✅, Sortable ✅  |
| Posisi             | Searchable ✅               |
| Telepon            | —                          |
| Tanggal Bergabung  | Format tanggal, Sortable ✅ |

**Aksi yang tersedia per baris:**
- ✏️ **Edit** — membuka halaman edit data karyawan
- 🗑️ **Delete** — menghapus data karyawan (single)

**Aksi massal (Bulk Action):**
- 🗑️ **Delete Selected** — menghapus beberapa data sekaligus

### 6.4 Routing Halaman

```php
public static function getPages(): array
{
    return [
        'index'  => Pages\ListKaryawans::route('/'),
        'create' => Pages\CreateKaryawan::route('/create'),
        'edit'   => Pages\EditKaryawan::route('/{record}/edit'),
    ];
}
```

| URL Relatif                         | Halaman            | Fungsi                  |
|-------------------------------------|--------------------|-------------------------|
| `/admin/karyawans`                  | `ListKaryawans`    | Daftar semua karyawan   |
| `/admin/karyawans/create`           | `CreateKaryawan`   | Form tambah karyawan    |
| `/admin/karyawans/{id}/edit`        | `EditKaryawan`     | Form edit karyawan      |

---

## 7. Halaman-Halaman (Pages)

### `ListKaryawans.php`
Menampilkan tabel seluruh data karyawan beserta tombol **"Tambah Karyawan"** di pojok kanan atas.

### `CreateKaryawan.php`
Menampilkan form kosong untuk menginput data karyawan baru. Setelah disimpan, otomatis redirect ke halaman daftar.

### `EditKaryawan.php`
Menampilkan form yang sudah terisi data karyawan yang dipilih. Tersedia tombol **"Simpan"** dan **"Hapus"** di pojok kanan.

---

## 8. Cara Menjalankan Fitur

### Langkah Wajib: Jalankan Migrasi Database

Sebelum fitur dapat digunakan, tabel `karyawans` harus dibuat di database dengan menjalankan perintah berikut di terminal (dalam folder `food-store`):

```bash
php artisan migrate
```

> [!IMPORTANT]
> Pastikan konfigurasi database di file `.env` sudah benar (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) dan server MySQL (XAMPP) sudah berjalan sebelum menjalankan migrasi.

### Mengakses Portal Karyawan

1. Jalankan server lokal: `php artisan serve`
2. Buka browser dan akses: `http://localhost:8000/admin`
3. Login ke admin panel
4. Klik menu **"Data Master" → "Karyawan"** di sidebar kiri

---

## 9. Alur Kerja (User Flow)

```mermaid
flowchart TD
    A[Admin Login ke Panel] --> B[Klik Menu 'Karyawan']
    B --> C{Pilih Aksi}
    C --> D[Tambah Karyawan\n/admin/karyawans/create]
    C --> E[Lihat Daftar\n/admin/karyawans]
    D --> F[Isi Form & Simpan]
    F --> E
    E --> G{Aksi per Data}
    G --> H[Edit\n/admin/karyawans/{id}/edit]
    G --> I[Hapus Data]
    H --> J[Ubah Data & Simpan]
    J --> E
    I --> E
```

---

## 10. Catatan & Potensi Pengembangan

> [!NOTE]
> Fitur ini saat ini sudah fungsional sebagai CRUD dasar. Berikut ide pengembangan lebih lanjut:

| Fitur Lanjutan                | Deskripsi                                                |
|-------------------------------|----------------------------------------------------------|
| **Upload Foto Profil**        | Tambah kolom `foto` + komponen `FileUpload` di form     |
| **Filter Berdasarkan Posisi** | Tambah `SelectFilter` di tabel untuk filter jabatan     |
| **Export ke Excel/PDF**       | Integrasi dengan package `maatwebsite/excel` atau `dompdf` |
| **Relasi ke Transaksi**       | Menghubungkan karyawan dengan data transaksi (kasir)    |
| **Soft Delete**               | Gunakan `SoftDeletes` trait agar data tidak terhapus permanen |
| **Riwayat Jabatan**           | Tabel terpisah untuk histori posisi karyawan            |
