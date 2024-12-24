<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Grooming extends Model
{
    use HasFactory;
    public $timestamps = true;

    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'grooming';

    protected $fillable = [
        'customer_id',
        'pet_name',
        'category_id',
        'service_price',
        'grooming_date',
        'status',
        'notes',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function grooming_detail() 
    {
        return $this->belongsTo(GroomingDetail::class);
    }
    public static function getStatusOptions(): array
    {
        $type = DB::selectOne("SHOW COLUMNS FROM grooming WHERE Field = 'status'")->Type;
        preg_match('/enum\((.*)\)/', $type, $matches);
        $values = str_getcsv($matches[1], ',', "'");
        return array_combine($values, $values); // Format [value => label]
    }
}
