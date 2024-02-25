<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['start', 'end', 'active'];

    public $timestamps = null;

    public function user() {
        return $this->hasOne(User::class, 'id', 'shift_workers');
    }
}
