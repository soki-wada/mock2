<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'status',
        'clock_in',
        'clock_out',
        'notes'
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function requestBreaks()
    {
        return $this->hasMany(RequestBreak::class);
    }
}
