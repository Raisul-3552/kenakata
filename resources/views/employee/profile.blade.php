<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Profile | Kenakata</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0d0b1e;
            color: #f8fafc;
            min-height: 100vh;
        }

        .page {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 24px 60px;
        }

        .page-header { margin-bottom: 32px; }
        .page-title       { font-size: 22px; font-weight: 700; color: #f8fafc; }
        .page-breadcrumb  { font-size: 13px; color: #475569; margin-top: 3px; }
        .page-breadcrumb span { color: #a78bfa; }

        /* ── Profile Card ── */
        .profile-card {
            background: linear-gradient(145deg, #110e1e, #180d33);
            border: 1px solid rgba(124,58,237,0.2);
            border-radius: 20px;
            padding: 40px;
            display: flex;
            align-items: flex-start;
            gap: 36px;
            margin-bottom: 24px;
            position: relative; overflow: hidden;
        }
        .profile-card::before {
            content: ''; position: absolute; top: -60px; right: -60px;
            width: 200px; height: 200px; border-radius: 50%;
            background: radial-gradient(circle, rgba(124,58,237,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .avatar {
            width: 96px; height: 96px; border-radius: 24px;
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
            display: flex; align-items: center; justify-content: center;
            font-size: 38px; font-weight: 800; color: #fff;
            box-shadow: 0 0 0 4px rgba(124,58,237,0.2), 0 8px 24px rgba(124,58,237,0.3);
            flex-shrink: 0;
        }
        .avatar-status {
            display: flex; align-items: center; gap: 6px;
            margin-top: 10px; justify-content: center;
            font-size: 12px; font-weight: 600; color: #4ade80;
        }
        .avatar-status::before {
            content: ''; width: 8px; height: 8px; border-radius: 50%;
            background: #4ade80; animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.6; transform: scale(0.85); }
        }

        .profile-info { flex: 1; }
        .profile-name { font-size: 28px; font-weight: 800; color: #f8fafc; letter-spacing: -0.5px; margin-bottom: 6px; }
        .role-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 14px; border-radius: 20px; margin-bottom: 20px;
            font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;
            background: rgba(124,58,237,0.15); color: #a78bfa;
            border: 1px solid rgba(124,58,237,0.3);
        }

        .info-grid { display: flex; flex-direction: column; gap: 10px; }
        .info-row {
            display: flex; align-items: center; gap: 14px;
            padding: 12px 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
        }
        .info-icon {
            width: 36px; height: 36px; border-radius: 9px;
            background: rgba(124,58,237,0.12);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .info-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 15px; font-weight: 500; color: #e2e8f0; }

        /* ── Admin Affiliation Card ── */
        .affiliation-card {
            background: linear-gradient(135deg, #13052e, #1e0d4e);
            border: 1px solid rgba(124,58,237,0.3);
            border-radius: 16px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .aff-icon {
            width: 56px; height: 56px; border-radius: 16px;
            background: rgba(245,159,11,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; flex-shrink: 0;
        }
        .aff-info h4 { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; margin-bottom: 4px; }
        .aff-info .aff-name { font-size: 20px; font-weight: 700; color: #f59e0b; }
        .aff-info .aff-id   { font-size: 13px; color: #64748b; margin-top: 2px; }

        /* ── ID Badge ── */
        .id-card {
            background: linear-gradient(135deg, #0d0820, #1a0d40);
            border: 1px solid rgba(124,58,237,0.25);
            border-radius: 16px;
            padding: 24px;
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 20px;
        }
        .id-card-left h4 { font-size: 13px; color: #a78bfa; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .id-card-left p  { font-size: 28px; font-weight: 800; color: #c4b5fd; letter-spacing: 2px; }
        .id-card-right   { font-size: 36px; opacity: 0.3; }

        @media (max-width: 640px) {
            .profile-card { flex-direction: column; align-items: center; text-align: center; }
            .affiliation-card { flex-direction: column; text-align: center; }
            .id-card { flex-direction: column; gap: 12px; text-align: center; }
        }
    </style>
</head>
<body>

@include('employee.navbar')

<div class="page">

    <div class="page-header">
        <div class="page-title">My Profile</div>
        <div class="page-breadcrumb">Kenakata › <span>Employee Profile</span></div>
    </div>

    <!-- ── Profile Card ── -->
    <div class="profile-card">
        <div>
            <div class="avatar">{{ strtoupper(substr($employee->EmployeeName, 0, 1)) }}</div>
            <div class="avatar-status">Active</div>
        </div>

        <div class="profile-info">
            <div class="profile-name">{{ $employee->EmployeeName }}</div>
            <div class="role-badge">👔 Employee</div>

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-icon">🪪</div>
                    <div>
                        <div class="info-label">Employee ID</div>
                        <div class="info-value">#{{ $employee->EmployeeID }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📧</div>
                    <div>
                        <div class="info-label">Email Address</div>
                        <div class="info-value">{{ $employee->Email }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📞</div>
                    <div>
                        <div class="info-label">Phone Number</div>
                        <div class="info-value">{{ $employee->Phone }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📍</div>
                    <div>
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $employee->Address }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Admin Affiliation ── -->
    @if($admin)
    <div class="affiliation-card">
        <div class="aff-icon">🛡️</div>
        <div class="aff-info">
            <h4>Reporting to Administrator</h4>
            <div class="aff-name">{{ $admin->AdminName }}</div>
            <div class="aff-id">Admin ID: #{{ $admin->AdminID }} &nbsp;·&nbsp; {{ $admin->Email }}</div>
        </div>
    </div>
    @endif

    <!-- ── Employee ID Card ── -->
    <div class="id-card">
        <div class="id-card-left">
            <h4>Employee Credential ID</h4>
            <p>EMP-{{ str_pad($employee->EmployeeID, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="id-card-right">👔</div>
    </div>

</div>
</body>
</html>
