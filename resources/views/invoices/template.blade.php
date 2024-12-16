<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->id }} - Marelan Petmarket</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f4f4f4 0%, #e9e9e9 100%);
            padding: 20px;
            color: #333;
            line-height: 1.5;
        }
        .invoice-container {
            background: white;
            border: 4px solid #1a5f7a;
            box-shadow: 10px 10px 0 rgba(26, 95, 122, 0.2);
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
            border-radius: 12px;
            font-size: 0.9rem;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #1a5f7a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-info h1 {
            font-size: 2rem;
            font-weight: 900;
            color: #1a5f7a;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .company-info p {
            color: #666;
            font-weight: 300;
            font-size: 0.8rem;
        }
        .invoice-number {
            text-align: right;
        }
        .invoice-number h2 {
            color: #1a5f7a;
            font-size: 1.3rem;
        }
        .customer-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .invoice-details-section {
            background: #f0f8fa;
            border: 2px solid #1a5f7a;
            padding: 10px;
            border-radius: 8px;
            width: 100%;
            font-size: 0.8rem;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .invoice-table th, .invoice-table td {
            border: 2px solid #1a5f7a;
            padding: 8px;
            text-align: left;
            font-size: 0.8rem;
        }
        .invoice-table th {
            background: #1a5f7a;
            color: white;
            font-weight: bold;
        }
        .invoice-table tr:nth-child(even) {
            background-color: #f4f9fb;
        }
        .total-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .total-box {
            border: 3px solid #1a5f7a;
            padding: 10px;
            background: #ffeb3b;
            font-weight: bold;
            font-size: 1rem;
            border-radius: 8px;
            min-width: 200px;
            text-align: center;
        }
        .terms-section {
            margin-top: 15px;
            background: #f0f8fa;
            border: 2px solid #1a5f7a;
            border-radius: 8px;
            padding: 10px;
        }
        .terms-section h3 {
            color: #1a5f7a;
            border-bottom: 2px solid #1a5f7a;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        .terms-section ul {
            padding-left: 15px;
            font-size: 0.7rem;
        }
        .payment-info {
            margin-top: 10px;
            background: #e6f2f7;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            font-size: 0.8rem;
        }
        .contact-info {
            margin-top: 10px;
            text-align: center;
            color: #666;
            font-size: 0.7rem;
        }
        @media print {
            body {
                padding: 0;
            }
            .invoice-container {
                border: none;
                box-shadow: none;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="company-info">
                <h1>Marelan Petmarket</h1>
                <p>Jl. SM. Raja - Pusat Kebutuhan Hewan Peliharaan</p>
            </div>
            <div class="invoice-number">
                <h2>Invoice #INV-{{ $invoice->id }}</h2>
                <p>Tanggal: {{ $invoice->invoice_date }}</p>
            </div>
        </div>

        <div class="customer-details">
            <div class="invoice-details-section">
                <h3>Detail Invoice</h3>
                <p>Status: {{ $invoice->status }}</p>
                <p>Catatan: {{ $invoice->notes }}</p>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kuantitas</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                @endphp
                @foreach($invoiceDetails as $detail)
                @php
                    $totalPrice = $detail->quantity * $detail->price;
                    $grandTotal += $totalPrice;
                @endphp
                <tr>
                    <td>{{ $detail->product->name }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>Rp. {{ number_format($detail->price, 2, ',', '.') }}</td>
                    <td>Rp. {{ number_format($totalPrice, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="payment-info">
                <strong>Terima Kasih Telah Berbelanja di Marelan Petmarket!</strong>
            </div>
            <div class="total-box">
                Total: Rp. {{ number_format($grandTotal, 2, ',', '.') }}
            </div>
        </div>

        <div class="terms-section">
            <h3>Syarat dan Ketentuan Pembelian</h3>
            <ul>
                <li>Semua produk yang dibeli tidak dapat dikembalikan kecuali dalam kondisi cacat atau rusak.</li>
                <li>Penukaran atau pengembalian produk hanya dilakukan dengan menunjukkan invoice asli.</li>
                <li>Harga yang tertera sudah termasuk pajak penjualan yang berlaku.</li>
                <li>Marelan Petmarket tidak bertanggung jawab atas penggunaan produk yang tidak sesuai petunjuk.</li>
                <li>Untuk produk makanan hewan, mohon periksa tanggal kadaluarsa sebelum menggunakan.</li>
                <li>Disarankan menyimpan produk sesuai petunjuk pada kemasan.</li>
                <li>Stok produk dan harga dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya.</li>
            </ul>
        </div>

        <div class="contact-info">
            <p>Hubungi Kami: (0123) 456-7890 | Email: customer@marelanpetmarket.com</p>
            <p>www.marelanpetmarket.com</p>
        </div>
    </div>
</body>
</html>
