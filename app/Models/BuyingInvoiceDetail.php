<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyingInvoiceDetail extends Model
{
    use HasFactory;

    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'buying_invoice_detail';
    protected $fillable = [
        'invoice_id',
        'name_product',
        'quantity',
        'price',
    ];
    public function invoice()
    {
        return $this->belongsTo(BuyingInvoiceDetail::class);
    }
}
