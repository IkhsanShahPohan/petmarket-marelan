<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    public $timestamps = true;
    
    // Field yang diizinkan untuk diisi (mass assignment)
    protected $fillable = [
        'category_name',
        'category_image',
    ];
    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'categories';
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
