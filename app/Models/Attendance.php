<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'attendances';
    protected $fillable = [
        'employee_id',
        'status',
        'notes',
        'image',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
