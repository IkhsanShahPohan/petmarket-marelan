<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceView extends Model
{
    // VIEW
    protected $table = 'invoice_view';
    protected $guarded = [];
    public $timestamps = false;
    public function details()
    {
        return $this->hasMany(SellingInvoiceDetail::class, 'invoice_id', 'id');
    }
}