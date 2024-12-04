<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GroomingDetail extends Model
{
    use HasFactory;
    public $timestamps = true;

    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'grooming_detail';

    protected $fillable = [
        'grooming_type',
        'animal_age_type',
        'description',
    ];
    public function groomings()
    {
        return $this->hasMany(Grooming::class);
    }

}
