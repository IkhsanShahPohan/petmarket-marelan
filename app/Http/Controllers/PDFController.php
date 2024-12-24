<?php
namespace App\Http\Controllers;

use App\Models\InvoiceView;
use App\Models\SellingInvoiceDetail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan package DomPDF sudah terinstal
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;

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

public function attendanceReport($employee, $period, $status = 'all')
{
    $query = Attendance::query()
        ->with('employee') // Eager load employee relation
        ->whereYear('created_at', substr($period, 0, 4))
        ->whereMonth('created_at', substr($period, 5, 2));

    // Jika employee tidak 'all', tambahkan filter berdasarkan employee_id
    if ($employee !== 'all') {
        $query->where('employee_id', $employee);
        $employeeData = Employee::find($employee);
        $title = "Laporan Absensi " . $employeeData->user->name;
    } else {
        $title = "Laporan Absensi Semua Karyawan";
    }

    // Jika status tidak 'all', tambahkan filter berdasarkan status
    if ($status !== 'all') {
        $query->where('request_status', $status);
    }

    // Ambil data absensi berdasarkan query
    $attendances = $query->orderBy('created_at')->get();

    // Hitung statistik
    $stats = [
        'total_present' => $attendances->where('status', 'present')->count(),
        'total_absent' => $attendances->where('status', 'absent')->count(),
        'total_late' => $attendances->where('status', 'late')->count(),
        'attendance_rate' => $attendances->count() > 0
            ? round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100, 2)
            : 0,
        'total_employees' => $employee === 'all'
            ? $attendances->pluck('employee_id')->unique()->count()
            : 1
    ];

    // Kelompokkan absensi berdasarkan employee_id jika employee adalah 'all'
    $groupedAttendances = $employee === 'all'
        ? $attendances->groupBy('employee_id')
        : collect([$employee => $attendances]);

    // Persiapkan data untuk view PDF
    $attendanceDetails = $attendances->map(function ($attendance) {
        return [
            'employee_id' => $attendance->employee_id,
            'status' => $attendance->status,
            'type' => $attendance->type, // Menambahkan type di sini
            'request_status' => $attendance->request_status, // Menambahkan request_status di sini
            'notes' => $attendance->notes,
            'image' => $attendance->image,
            'created_at' => $attendance->created_at->format('d-m-Y H:i:s'),
        ];
    });

    // Generate PDF
    $pdf = Pdf::loadView('attendance.report-pdf', [
        'title' => $title,
        'period' => Carbon::parse($period . '-01')->format('F Y'),
        'stats' => $stats,
        'groupedAttendances' => $groupedAttendances,
        'attendanceDetails' => $attendanceDetails, // Mengirimkan detail absensi, termasuk type dan request_status
        'isAllEmployees' => $employee === 'all'
    ]);

    return $pdf->download("laporan-absensi-{$period}.pdf");
}


}
