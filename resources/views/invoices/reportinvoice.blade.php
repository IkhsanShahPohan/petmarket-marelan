<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Market Sales Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3A76E2;
            --secondary-color: #38C172;
            --text-color: #333;
            --background-color: #F8FAFC;
            --card-background: #FFFFFF;
            --header-gradient-start: #4A90E2;
            --header-gradient-end: #7B68EE;
            --border-color: #E0E6ED;
            --shadow-light: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-color);
            padding: 20px;
        }

        .report-container {
            max-width: 1100px;
            margin: 0 auto;
            background-color: var(--card-background);
            box-shadow: var(--shadow-light);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .report-header {
            background: linear-gradient(135deg, var(--header-gradient-start) 0%, var(--header-gradient-end) 100%);
            color: rgb(64, 64, 64);
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .report-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .report-header p {
            font-size: 1.2rem;
        }

        .report-header::after {
            content: '\1F43E'; /* Paw print emoji */
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 3rem;
            opacity: 0.1;
        }

        .summary-grid {
            display: grid;
            margin-bottom: 5rem;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 25px;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            background-color: var(--background-color);
        }

        .summary-card {
            background-color: var(--card-background);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow-light);
            transition: transform 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .summary-card p {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .sales-table-container {
            padding: 20px;
            background-color: var(--card-background);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin: 20px;
            box-shadow: var(--shadow-light);
        }

        .sales-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .sales-table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .sales-table th, .sales-table td {
            padding: 15px;
            text-align: left;
            border: 1px solid var(--border-color);
        }

        .sales-table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .sales-table tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .sales-table tbody tr:hover {
            background-color: rgba(58, 118, 226, 0.1);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-paid {
            background-color: rgba(56, 193, 114, 0.2);
            color: var(--secondary-color);
        }

        .status-cancelled {
            background-color: rgba(255, 99, 132, 0.2);
            color: #FF6384;
        }

        .additional-insights {
            background-color: var(--card-background);
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .additional-insights h2 {
            color: rgb(53, 53, 53);
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .additional-insights p {
            color: #555;
            font-size: 1rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1>Marelan PetMarket Income's Report</h1>
        </div>

        <div class="additional-insights">
            <h2>{{ $title }}</h2>
            <p><strong>Tipe Laporan:</strong> {{ ucfirst($type) }}</p>
            <p><strong>Periode:</strong> {{ $period }}</p>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Invoices</h3>
                <p>{{ $totalInvoices }}</p>
            </div>
            <div class="summary-card">
                <h3>Paid Invoices</h3>
                <p class="status-paid">{{ $paidInvoices }}</p>
            </div>
            <div class="summary-card">
                <h3>Cancelled Invoices</h3>
                <p class="status-cancelled">{{ $cancelledInvoices }}</p>
            </div>
            <div class="summary-card">
                <h3>Total Revenue</h3>
                <p>Rp {{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>

        <div class="sales-table-container">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>INV-ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $index => $invoice)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $invoice->id }}</td>
                        <td>{{ Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y H:i') }}</td>
                        <td>
                            <span class="status-badge {{ $invoice->status == 'Paid' ? 'status-paid' : 'status-cancelled' }}">
                                {{ $invoice->status }}
                            </span>
                        </td>
                        <td>Rp {{ number_format($invoice->total, 2) }}</td>
                        <td>{{ $invoice->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
