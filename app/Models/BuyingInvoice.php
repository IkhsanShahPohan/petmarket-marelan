<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyingInvoice extends Model
{
    use HasFactory;

    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'buying_invoice';
    protected $fillable = [
        'invoice_code',
        'supplier_id',
        'status',
    ];
    public function details()
    {
        return $this->hasMany(BuyingInvoiceDetail::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
