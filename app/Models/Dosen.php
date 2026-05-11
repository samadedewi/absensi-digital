<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    protected $fillable = ['nidn', 'name', 'email'];

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}
