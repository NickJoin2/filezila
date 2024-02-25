<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLES = [
        'admin' => 1,
        'waiter' => 2,
        'cook' => 3
    ];

    protected $fillable = [
        'name',
        'surname',
        'patronymic',
        'login',
        'password',
        'photo_file',
        'role_id'
    ];

    public $timestamps = null;

//    protected $hidden = [
//        'password',
//    ];

    public function role() {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function isAdmin() {
        return $this->role_id === self::ROLES['admin'];
    }

    public function isWaiter() {
        return $this->role_id === self::ROLES['waiter'];
    }

    public function isCook() {
        return $this->role_id === self::ROLES['cook'];
    }
}
