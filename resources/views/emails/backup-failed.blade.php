<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Failed</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #ef4444, #dc2626); padding: 36px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 14px; }
        .icon { font-size: 48px; margin-bottom: 12px; display: block; }
        .body { padding: 36px 40px; }
        .body p { color: #374151; line-height: 1.6; margin: 0 0 16px; }
        .details { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .details table { width: 100%; border-collapse: collapse; }
        .details td { padding: 6px 0; font-size: 14px; }
        .details td:first-child { color: #6b7280; font-weight: 500; width: 40%; }
        .details td:last-child { color: #111827; font-weight: 600; }
        .error-box { background: #1f2937; color: #f9fafb; padding: 16px; border-radius: 8px; font-family: monospace; font-size: 13px; margin: 20px 0; word-break: break-all; }
        .badge-error { display: inline-block; background: #fee2e2; color: #991b1b; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .note { font-size: 13px; color: #6b7280; background: #f9fafb; border-left: 3px solid #f87171; padding: 12px 16px; border-radius: 0 6px 6px 0; margin-top: 20px; }
        .footer { padding: 20px 40px; text-align: center; background: #f9fafb; border-top: 1px solid #f0f0f0; }
        .footer p { color: #9ca3af; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="icon">❌</span>
            <h1>Backup Failed</h1>
            <p>{{ now()->format('l, F d, Y \a\t h:i A') }}</p>
        </div>
        <div class="body">
            <p>Hello Administrator,</p>
            <p>A system backup has <strong>failed</strong>. Immediate attention may be required.</p>

            <div class="details">
                <table>
                    <tr>
                        <td>Backup Type</td>
                        <td><span class="badge-error">{{ ucfirst($backupType) }}</span></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>❌ Failed</td>
                    </tr>
                    <tr>
                        <td>Attempted At</td>
                        <td>{{ now()->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>

            <p><strong>Error Details:</strong></p>
            <div class="error-box">{{ $errorMessage }}</div>

            <div class="note">
                ⚠️ Please check the application logs at <code>storage/logs/laravel.log</code> for more details, and ensure the database server and storage directories are accessible.
            </div>
        </div>
        <div class="footer">
            <p>This is an automated message from <strong>{{ config('app.name') }}</strong>. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
