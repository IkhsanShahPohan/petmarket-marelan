<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use App\Filament\Pages\Kasir;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/invoice/{id}/pdf', [PDFController::class, 'testpdf'])->name('invoice.pdf');

Route::get('/invoice/report/{type}/{period}', [PDFController::class, 'generateReport'])->name('invoice.report');

Route::get('/attendance/report/{employee}/{period}/{status?}', [PDFController::class, 'attendanceReport'])
    ->name('attendance.report')
    ->where([
        'employee' => '[0-9]+|all',  // Pastikan employee ID atau 'all'
        'period' => '[0-9]{4}-[0-9]{2}|all',  // Format periode: YYYY-MM
        'status' => 'diterima|menunggu|ditolak|all'  // Status absensi: diterima, menunggu, ditolak, atau all
    ]);


// Add this route for checkout

Route::post('/checkout', [Kasir::class, 'checkout'])->name('kasir.checkout');
