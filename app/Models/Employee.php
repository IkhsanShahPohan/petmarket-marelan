<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Secara eksplisit mendefinisikan nama tabel
    protected $table = 'employees';
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'hire_date',
    ];
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'employee_id', 'id');
    }
    public function user()
    {
    return $this->belongsTo(User::class, 'user_id');
    }

}
