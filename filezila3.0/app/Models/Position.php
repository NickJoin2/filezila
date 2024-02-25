<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    protected $table = 'positions';
    protected  $fillable = ['menu_id', 'count'];

    public function menu() {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }
}
