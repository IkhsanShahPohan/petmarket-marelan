<!-- resources/views/attendance/report-pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        :root {
            --primary: #0EA5E9;
            --secondary: #1F2937;
            --success: #22C55E;
            --danger: #EF4444;
            --warning: #F59E0B;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: var(--secondary);
        }

        .header {
            background: var(--primary);
            color: white;
            padding: 25px;
            border: 4px solid var(--secondary);
            border-radius: 12px;
            box-shadow: 8px 8px 0 rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .period {
            font-size: 18px;
            margin-top: 10px;
            opacity: 0.9;
        }

        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            flex: 1;
            min-width: 120px;
            background: white;
            border: 3px solid var(--secondary);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 4px 4px 0 rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary);
        }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--secondary);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
        }

        th {
            background: var(--secondary);
            color: white;
            padding: 12px;
            text-align: left;
            border: 2px solid var(--secondary);
        }

        td {
            padding: 12px;
            border: 2px solid #e5e7eb;
            background: white;
        }

        tr:nth-child(even) td {
            background: #f9fafb;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-present {
            background: var(--success);
            color: white;
        }

        .status-absent {
            background: var(--danger);
            color: white;
        }

        .status-late {
            background: var(--warning);
            color: white;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #6b7280;
        }

        .employee-section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }


        //==================================

        table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 15px;
        text-align: left;
        font-size: 14px;
        color: #333;
    }

    th {
        background-color: var(--primary);
        color: white;
        font-weight: bold;
        text-transform: uppercase;
        border-bottom: 2px solid #ddd;
    }

    td {
        background-color: #f9fafb;
        border-bottom: 1px solid #ddd;
        transition: background-color 0.3s ease;
    }

    tr:nth-child(even) td {
        background-color: #f1f5f9;
    }

    tr:hover td {
        background-color: #e2e8f0;
    }

    td:first-child {
        font-weight: 600;
        color: #4B5563;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-present {
        background-color: var(--success);
        color: white;
    }

    .status-absent {
        background-color: var(--danger);
        color: white;
    }

    .status-late {
        background-color: var(--warning);
        color: white;
    }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ $title }}</h1>
        <div class="period">Periode: {{ $period }}</div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_present'] }}</div>
            <div class="stat-label">Hadir</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_absent'] }}</div>
            <div class="stat-label">Tidak Hadir</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_late'] }}</div>
            <div class="stat-label">Terlambat</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['attendance_rate'] }}%</div>
            <div class="stat-label">Tingkat Kehadiran</div>
        </div>
    </div>

    @foreach($groupedAttendances as $employeeId => $attendances)
        <div class="employee-section">
            @if($isAllEmployees)
                <h2 class="section-title">{{ $attendances->first()->employee->name }}</h2>
            @endif

            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Status Attandance</th>
                        <th>Type</th>
                        <th>Status Penerimaan</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $attendance)
                        <tr>
                            <td>{{ Carbon\Carbon::parse($attendance->created_at)->format('d M Y H:i') }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($attendance->status) }}">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                            <td>{{ $attendance->request_status }}</td>
                            <td>{{ $attendance->type }}</td>
                            <td>{{ $attendance->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer">
        Generated on {{ now()->format('d F Y H:i:s') }}
    </div>
</body>
</html>
