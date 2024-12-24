<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use App\Models\Employee;
use Carbon\Carbon;

use Filament\Forms\Get; // Untuk menggunakan Get di dalam closure
class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
{
    return [
        Action::make('generate_report')
    ->label('Generate Report')
    ->icon('heroicon-o-document-text')
    ->color('primary')
    ->modalWidth(MaxWidth::Medium)
    ->form([
        // Filter untuk memilih karyawan
        Select::make('employee_id')
            ->label('Pilih Karyawan')
            ->options(Employee::with('user')->get()->pluck('user.name', 'id'))
            ->placeholder('Semua Karyawan')
            ->searchable()
            ->required()
            ->live(),

        // Filter untuk memilih periode
        Select::make('period')
            ->label('Periode')
            ->options(function (Get $get) {
                $employeeId = $get('employee_id'); // Ambil nilai dari employee_id
                if ($employeeId) {
                    $periods = [];
                    // Generate 6 months back and 1 month forward
                    for ($i = -6; $i <= 1; $i++) {
                        $date = \Carbon\Carbon::now()->addMonths($i);
                        $periods[$date->format('Y-m')] = $date->format('F Y');
                    }
                    return $periods;
                }
                return []; // Tidak ada opsi jika employee_id belum dipilih
            })
            ->required()
            ->hidden(fn (Get $get) => !$get('employee_id')), // Sembunyikan jika employee_id belum dipilih

        // Filter untuk memilih type, hanya muncul setelah periode dipilih
        Select::make('status')
    ->label('Status')
    ->options([
        'all' => 'Semua Status',  // Menambahkan 'all' agar bisa memilih semua status
        'diterima' => 'Diterima',
        'menunggu' => 'Menunggu',
        'ditolak' => 'Ditolak',
    ])
    ->placeholder('Pilih Status')
    ->hidden(fn (Get $get) => !$get('employee_id')) // Sembunyikan jika employee_id belum dipilih

    ])

            ->action(function (array $data) {
                // Menambahkan parameter status ke URL jika ada
                return redirect()->route('attendance.report', [
                    'employee' => $data['employee_id'] ?? 'all',
                    'period' => $data['period'],
                    'status' => $data['status'] ?? 'all', // Kirim status jika ada, atau 'all' jika tidak ada status yang dipilih
                ]);
            }),
    ];
}



    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        // Periksa apakah pengguna sudah login dan memiliki relasi 'employee'
        if (Auth::check() && Auth::user()->employee) {
            // Filter data payroll hanya untuk pegawai yang sedang login
            $query->where('employee_id', Auth::user()->employee->id);
        }

        return $query;
    }
}
