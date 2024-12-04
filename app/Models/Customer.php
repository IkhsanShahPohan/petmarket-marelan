<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'customers';
    public function groomings()
    {
        return $this->hasMany(Grooming::class);
    }
    protected $fillable = [
        'name',
        'phone',
    ];
}
