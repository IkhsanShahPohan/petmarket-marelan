<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use App\Filament\Pages\Kasir;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/invoice/{id}/pdf', [PDFController::class, 'testpdf'])->name('invoice.pdf');

Route::get('/invoice/report/{type}/{period}', [PDFController::class, 'generateReport'])->name('invoice.report');

// Add this route for checkout

Route::post('/checkout', [Kasir::class, 'checkout'])->name('kasir.checkout');
