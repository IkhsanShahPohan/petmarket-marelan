<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellingInvoiceDetail extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_id', 'product_id', 'quantity', 'price', 'notes'];
    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'selling_invoice_detail';
    public function selling_invoice()
    {
        return $this->belongsTo(SellingInvoice::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
