<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];
    // protected $table = 'users';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function employee()
    {
    return $this->hasOne(Employee::class, 'user_id');
    }
    public function isAdmin()
    {
        // Sesuaikan dengan kolom atau logika role di tabel users Anda
        // Misalnya jika Anda menggunakan kolom 'role' untuk menandai user
        return $this->role === 'admin';
    }
    public function isEmployee()
    {
        // Sesuaikan dengan kolom atau logika role di tabel users Anda
        // Misalnya jika Anda menggunakan kolom 'role' untuk menandai user
        return $this->role === 'pegawai';
    }
}
