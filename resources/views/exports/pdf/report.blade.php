<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>System Report - {{ ucwords(str_replace('_', ' ', $type)) }}</title>
    <style>
        @page {
            margin: 100px 30px 100px 30px; /* Top, Right, Bottom, Left */
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        header {
            position: fixed;
            top: -70px;
            left: 0px;
            right: 0px;
            height: 60px;
            border-bottom: 2px solid #2563eb; /* Brand color */
        }
        .header-content {
            width: 100%;
        }
        .header-content td {
            border: none;
            padding: 0;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin: 0;
        }
        .system-title {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }
        .report-title-top {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin: 0;
        }
        .report-date-top {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
            margin: 0;
        }
        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 40px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 10px;
            text-align: center;
            padding-top: 10px;
        }
        .page-number:after {
            content: counter(page);
        }
        main {
            margin-top: 20px;
        }
        .meta-info {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 11px;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
            color: #111827;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.05em;
        }
        tr:nth-child(even) {
            background-color: #fcfcfd;
        }
        .signature-section {
            margin-top: 50px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 45%;
            display: inline-block;
            text-align: center;
        }
        .signature-line {
            margin: 40px auto 10px auto;
            border-bottom: 1px solid #111827;
            width: 80%;
        }
        .signature-name {
            font-weight: bold;
            font-size: 12px;
            color: #111827;
            margin: 0;
        }
        .signature-title {
            font-size: 10px;
            color: #6b7280;
            margin: 0;
        }
        /* Simple CSS visual bars for System Usage report */
        .bar-container {
            width: 100%;
            background-color: #e5e7eb;
            border-radius: 4px;
            height: 12px;
            margin-top: 4px;
        }
        .bar-fill {
            height: 100%;
            background-color: #3b82f6;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <table class="header-content">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <h1 class="logo">SAMS</h1>
                    <p class="system-title">Student Attendance Management System</p>
                </td>
                <td style="width: 40%; vertical-align: top;">
                    <p class="report-title-top">{{ ucwords(str_replace('_', ' ', $type)) }} Report</p>
                    <p class="report-date-top">Generated: {{ $date }}</p>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table style="width: 100%; border: none; margin: 0; padding: 0;">
            <tr>
                <td style="border: none; text-align: left; padding: 0; width: 33%;">
                    Confidential Document &copy; {{ date('Y') }}
                </td>
                <td style="border: none; text-align: center; padding: 0; width: 33%;">
                    Do not distribute without authorization.
                </td>
                <td style="border: none; text-align: right; padding: 0; width: 33%;">
                    Page <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </footer>

    <main>
        <div class="meta-info">
            <table style="border: none; margin: 0; padding: 0;">
                <tr>
                    <td style="border: none; padding: 0; width: 50%;">
                        <strong>Report Type:</strong> {{ ucwords(str_replace('_', ' ', $type)) }}<br>
                        <strong>Generated By:</strong> {{ $generatedBy }}
                    </td>
                    <td style="border: none; padding: 0; width: 50%; text-align: right;">
                        <strong>Date of Generation:</strong> {{ $date }}<br>
                        <strong>Total Records:</strong> {{ count($data['rows']) }}
                    </td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr>
                    @foreach($data['headers'] as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($data['rows'] as $row)
                    <tr>
                        @foreach($row as $key => $value)
                            <td>
                                {{ $value }}
                                
                                @if($type === 'system_usage' && str_contains($value, '%'))
                                    <!-- Simple visual bar for percentage metrics -->
                                    <div class="bar-container">
                                        <div class="bar-fill" style="width: {{ (float)$value }}%"></div>
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($data['headers']) }}" style="text-align: center; color: #9ca3af; padding: 20px;">
                            No data available for this report.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Digital Signature Placeholders -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p class="signature-name">{{ $generatedBy }}</p>
                <p class="signature-title">Report Generator</p>
            </div>
            <div class="signature-box" style="float: right;">
                <div class="signature-line"></div>
                <p class="signature-name">Administrator Approval</p>
                <p class="signature-title">System Administrator / Principal</p>
            </div>
        </div>
    </main>
</body>
</html>
