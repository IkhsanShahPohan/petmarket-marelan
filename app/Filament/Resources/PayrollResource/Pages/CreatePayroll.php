<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
{
    \Log::info('Employee ID:', ['id' => $data['employee_id']]);
    \Log::info('Month:', ['month' => $data['month']]);

    // Debugging untuk memastikan employee_id dan month dikirim dengan benar
    \Log::info('Payroll Data:', $data);

    // Tambahkan '-01' agar format menjadi YYYY-MM-DD
    $data['month'] = $data['month'] . '-01';

    // Hitung base_salary menggunakan stored function
    $result = DB::selectOne('SELECT calculate_base_salary(?, ?) AS base_salary', [
        $data['employee_id'],  
        $data['month'], // Format sudah menjadi YYYY-MM-DD
    ]);

    // Debug hasil dari stored function
    \Log::info('Stored Function Result:', ['base_salary' => $result]);

    // Atur base_salary
    $data['base_salary'] = $result->base_salary ?? 0;
    
    //  // Debugging: Log data untuk memastikan employee_id dan month dikirim dengan benar
    //  \Log::info('Payroll Data:', $data);

    //  // Mengambil hasil dari stored procedure
    //  $result = DB::selectOne('CALL GetPayrollSummary(?)', [$data['employee_id']]);
 
    //  // Debugging: Log hasil dari stored procedure
    //  \Log::info('Stored Procedure Result:', ['total_salary' => $result]);
 
    //  // Pastikan data total_salary diupdate jika ada hasil
    //  $data['total_salary'] = $result->total_salary ?? 0;

    return $data;
}

}
