<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'suppliers';
    public function buying_invoice()
    {
        return $this->hasMany(BuyingInvoiceDetail::class);
    }
}
