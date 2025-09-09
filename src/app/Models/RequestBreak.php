<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_request_id',
        'break_start',
        'break_end',
    ];

    public function workRequest(){
        return $this->belongsTo(WorkRequest::class);
    }
}
