<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = ['student_id', 'jadwal_id', 'date', 'time', 'status', 'face_verified', 'face_distance'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }
}
