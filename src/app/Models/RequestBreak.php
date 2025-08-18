<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'break_start',
        'break_end',
    ];

    public function request(){
        return $this->belongsTo(Request::class);
    }
}
