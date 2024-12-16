<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

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

