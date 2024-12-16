<?php
namespace App\Http\Controllers;

use App\Models\InvoiceView;
use App\Models\SellingInvoiceDetail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan package DomPDF sudah terinstal
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function testpdf($id)
{
    // Ambil data invoice berdasarkan ID
    $invoice = InvoiceView::findOrFail($id); // Mengambil invoice berdasarkan ID

    // Ambil detail invoice berdasarkan invoice_id dan load relasi 'product'
    $invoiceDetails = SellingInvoiceDetail::with('product') // Eager load relasi product
                        ->where('invoice_id', $id)
                        ->get();

                        // dd($invoiceDetails);
    // Jika data tidak ditemukan, Anda bisa mengarahkan ke halaman lain atau memberikan pesan error
    if (!$invoice || $invoiceDetails->isEmpty()) {
        return redirect()->route('invoice.index')->with('error', 'Invoice tidak ditemukan!');
    }

    // Debugging untuk memastikan relasi 'product' sudah dimuat

    // Pass data ke Blade view untuk template PDF
    $pdf = Pdf::loadView('invoices.template', [
        'invoice' => $invoice,
        'invoiceDetails' => $invoiceDetails,
    ]);

    // Download file PDF dengan nama invoice.pdf
    return $pdf->download("invoice-{$invoice->id}.pdf");
}


public function generateReport($type, $period)
{
    // Logika query berdasarkan tipe dan periode
    $query = InvoiceView::query();

    switch ($type) {
        case 'daily':
            $query->whereDate('invoice_date', $period);
            $title = "Laporan Harian - " . Carbon::parse($period)->format('d F Y');
            break;
        case 'monthly':
            $query->whereYear('invoice_date', substr($period, 0, 4))
                  ->whereMonth('invoice_date', substr($period, 5, 2));
            $title = "Laporan Bulanan - " . Carbon::parse($period . '-01')->format('F Y');
            break;
        case 'yearly':
            $query->whereYear('invoice_date', $period);
            $title = "Laporan Tahunan - " . $period;
            break;
    }

    $invoices = $query->get();
    $totalRevenue = $invoices->where('status', 'Paid')->sum('total');
    $totalInvoices = $invoices->count();
    $paidInvoices = $invoices->where('status', 'Paid')->count();
    $cancelledInvoices = $invoices->where('status', 'Cancelled')->count();

    // Load view untuk laporan PDF
    $pdf = Pdf::loadView('invoices.reportinvoice', [
        'invoices' => $invoices,
        'title' => $title,
        'type' => $type,
        'period' => $period,
        'totalRevenue' => $totalRevenue,
        'totalInvoices' => $totalInvoices,
        'paidInvoices' => $paidInvoices,
        'cancelledInvoices' => $cancelledInvoices
    ]);

    return $pdf->download("laporan-invoice-{$type}-{$period}.pdf");
}


}
