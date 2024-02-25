<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['table_id', 'shift_workers', 'create_at', 'status', 'price'];
    public $timestamps = null;

    public function user() {
        return $this->hasOne(User::class, 'id', 'shift_workers');
    }

}
