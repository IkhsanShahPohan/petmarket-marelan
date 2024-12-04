<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public $timestamps = true;

    // Field yang diizinkan untuk diisi (mass assignment)
    protected $fillable = [
        'product_name',
        'category_id',
        'sell_price',
        'buy_price',
        'stock',
        'product_photo',
        'product_side_effect',
        'product_description',
    ];
    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'products';
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}


