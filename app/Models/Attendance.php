<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

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
    protected static function booted()
    {
        static::creating(function ($attendance) {
            $currentTime = Carbon::now();

            // Ambil data shift dari employee yang terkait
            $employee = Employee::find($attendance->employee_id);

            if (!$employee) {
                throw ValidationException::withMessages([
                    'employee' => 'Data pegawai tidak ditemukan.'
                ]);
            }

            // Tetapkan rentang waktu berdasarkan shift
            $startLimit = null;
            $endLimit = null;

            if ($employee->shift === 'pagi') {
                $startLimit = $currentTime->copy()->setHour(8)->setMinute(0)->setSecond(0);
                $endLimit = $currentTime->copy()->setHour(18)->setMinute(0)->setSecond(0);
            } elseif ($employee->shift === 'malam') {
                $startLimit = $currentTime->copy()->setHour(12)->setMinute(0)->setSecond(0);
                $endLimit = $currentTime->copy()->setHour(22)->setMinute(0)->setSecond(0);
            } else {
                throw ValidationException::withMessages([
                    'shift' => 'Shift pegawai tidak valid.'
                ]);
            }

            // Validasi waktu absensi
            if ($currentTime->lt($startLimit) || $currentTime->gt($endLimit)) {
                // Tampilkan notifikasi error
                Notification::make()
                    ->title('Absensi Ditolak')
                    ->body("Absensi hanya diperbolehkan antara jam {$startLimit->format('H:i')} hingga {$endLimit->format('H:i')} untuk shift {$employee->shift}.")
                    ->danger()
                    ->send();

                // Batalkan penyimpanan dengan ValidationException
                throw ValidationException::withMessages([
                    'time' => "Absensi hanya diperbolehkan antara jam {$startLimit->format('H:i')} hingga {$endLimit->format('H:i')} untuk shift {$employee->shift}."
                ]);
            }
        });
    }
    public static function getStatusOptions(): array
    {
        $type = DB::selectOne("SHOW COLUMNS FROM attendances WHERE Field = 'request_status'")->Type;
        preg_match('/enum\((.*)\)/', $type, $matches);
        $values = str_getcsv($matches[1], ',', "'");
        return array_combine($values, $values); // Format [value => label]
    }
}
