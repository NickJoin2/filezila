<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftWorker extends Model
{
    use HasFactory;

    protected $table = 'worker_shifts';
    public $timestamps = null;

    protected $fillable = ['shift_id', 'shift_workers_id'];
}
