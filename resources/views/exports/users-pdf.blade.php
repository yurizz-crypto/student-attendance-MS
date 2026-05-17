<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Export</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1e293b; background: #fff; }
        .header { padding: 20px 24px 16px; border-bottom: 2px solid #084924; margin-bottom: 16px; }
        .header h1 { font-size: 18px; font-weight: 700; color: #084924; }
        .header p { font-size: 9px; color: #64748b; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #084924; color: #fff; }
        thead th { padding: 8px 10px; text-align: left; font-weight: 600; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:nth-child(odd) { background: #ffffff; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 8px; font-weight: 600; }
        .badge-admin { background: #fee2e2; color: #dc2626; }
        .badge-faculty { background: #dbeafe; color: #1d4ed8; }
        .badge-student { background: #dcfce7; color: #084924; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #f1f5f9; color: #475569; }
        .badge-suspended { background: #fef9c3; color: #854d0e; }
        .footer { margin-top: 20px; padding: 10px 24px; border-top: 1px solid #e2e8f0; font-size: 8px; color: #94a3b8; display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="header">
        <h1>User Management Report</h1>
        <p>Generated: {{ now()->format('F j, Y \a\t g:i A') }} &nbsp;|&nbsp; Total records: {{ $users->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Identity ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $i => $user)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>{{ $user->identity_id }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $user->status ?? 'inactive' }}">{{ ucfirst($user->status ?? 'N/A') }}</span>
                    </td>
                    <td>{{ $user->created_at?->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:20px;color:#94a3b8;">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>Student Attendance Management System</span>
        <span>Confidential — For Internal Use Only</span>
    </div>
</body>
</html>
