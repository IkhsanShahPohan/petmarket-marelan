<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellingInvoice extends Model
{
    use HasFactory;

    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'selling_invoice';
    protected $fillable = ['product_id', 'status', 'invoice_code'];

    // Event "creating" untuk men-generate invoice_code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            // Format kode otomatis: SINV001, SINV002, dst.
            $latestInvoice = self::latest('id')->first();
            $lastNumber = $latestInvoice ? intval(substr($latestInvoice->invoice_code, 4)) : 0;

            // Tambahkan prefix dan nomor
            $invoice->invoice_code = 'SINV' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        });
    }
    public function selling_invoice_detail()
    {
        return $this->hasMany(SellingInvoiceDetail::class);
    }
}
