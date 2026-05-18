<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Completed</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #10b981, #059669); padding: 36px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 14px; }
        .icon { font-size: 48px; margin-bottom: 12px; display: block; }
        .body { padding: 36px 40px; }
        .body p { color: #374151; line-height: 1.6; margin: 0 0 16px; }
        .details { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .details table { width: 100%; border-collapse: collapse; }
        .details td { padding: 6px 0; font-size: 14px; }
        .details td:first-child { color: #6b7280; font-weight: 500; width: 40%; }
        .details td:last-child { color: #111827; font-weight: 600; }
        .badge { display: inline-block; background: #d1fae5; color: #065f46; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .note { font-size: 13px; color: #6b7280; background: #f9fafb; border-left: 3px solid #d1d5db; padding: 12px 16px; border-radius: 0 6px 6px 0; margin-top: 20px; }
        .footer { padding: 20px 40px; text-align: center; background: #f9fafb; border-top: 1px solid #f0f0f0; }
        .footer p { color: #9ca3af; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="icon">✅</span>
            <h1>Backup Completed Successfully</h1>
            <p>{{ now()->format('l, F d, Y \a\t h:i A') }}</p>
        </div>
        <div class="body">
            <p>Hello Administrator,</p>
            <p>Your system backup has been completed successfully. Here are the details:</p>

            <div class="details">
                <table>
                    <tr>
                        <td>Backup Type</td>
                        <td><span class="badge">{{ ucfirst($backupLog->type) }}</span></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>✅ Success</td>
                    </tr>
                    <tr>
                        <td>File Size</td>
                        <td>{{ $backupLog->formatted_size }}</td>
                    </tr>
                    <tr>
                        <td>Created At</td>
                        <td>{{ $backupLog->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td>Expires At</td>
                        <td>{{ $backupLog->expires_at?->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            @if($backupLog->file_size && $backupLog->file_size >= 20 * 1024 * 1024)
            <div class="note">
                📎 The backup file exceeds 20 MB and was not attached. Please retrieve it from the server's <code>storage/backups/</code> directory.
            </div>
            @else
            <div class="note">
                📎 The backup file is attached to this email. Please save it to a secure location.
            </div>
            @endif
        </div>
        <div class="footer">
            <p>This is an automated message from <strong>{{ config('app.name') }}</strong>. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
