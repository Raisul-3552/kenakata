<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile | Kenakata</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0f;
            color: #f8fafc;
            min-height: 100vh;
        }

        /* ── Page Content ── */
        .page {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 24px 60px;
        }

        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 32px;
        }
        .page-title       { font-size: 22px; font-weight: 700; color: #f8fafc; }
        .page-breadcrumb  { font-size: 13px; color: #475569; margin-top: 3px; }
        .page-breadcrumb span { color: #f59e0b; }

        /* ── Profile Card ── */
        .profile-card {
            background: linear-gradient(145deg, #111118, #16161f);
            border: 1px solid rgba(245,159,11,0.15);
            border-radius: 20px;
            padding: 40px;
            display: flex;
            align-items: flex-start;
            gap: 36px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .profile-card::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245,159,11,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Avatar */
        .avatar-wrap { flex-shrink: 0; }
        .avatar {
            width: 96px; height: 96px; border-radius: 24px;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            display: flex; align-items: center; justify-content: center;
            font-size: 38px; font-weight: 800; color: #0a0a0f;
            box-shadow: 0 0 0 4px rgba(245,159,11,0.2), 0 8px 24px rgba(245,159,11,0.25);
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

        /* Profile info */
        .profile-info { flex: 1; }
        .profile-name {
            font-size: 28px; font-weight: 800; color: #f8fafc;
            letter-spacing: -0.5px; margin-bottom: 6px;
        }
        .role-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 14px; border-radius: 20px; margin-bottom: 20px;
            font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;
            background: rgba(245,159,11,0.12); color: #f59e0b;
            border: 1px solid rgba(245,159,11,0.3);
        }

        /* Info rows */
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
            background: rgba(245,159,11,0.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .info-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 15px; font-weight: 500; color: #e2e8f0; }

        /* ── Stats Grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: linear-gradient(145deg, #111118, #16161f);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            padding: 24px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-card.gold  { border-color: rgba(245,159,11,0.25); }
        .stat-card.gold:hover  { border-color: rgba(245,159,11,0.5); }
        .stat-card.blue  { border-color: rgba(56,189,248,0.2); }
        .stat-card.blue:hover  { border-color: rgba(56,189,248,0.45); }
        .stat-card.green { border-color: rgba(74,222,128,0.2); }
        .stat-card.green:hover { border-color: rgba(74,222,128,0.45); }

        .stat-icon { font-size: 28px; margin-bottom: 10px; }
        .stat-num  { font-size: 36px; font-weight: 800; color: #f8fafc; letter-spacing: -1px; margin-bottom: 4px; }
        .stat-num.gold  { color: #f59e0b; }
        .stat-num.blue  { color: #38bdf8; }
        .stat-num.green { color: #4ade80; }
        .stat-label { font-size: 13px; font-weight: 500; color: #64748b; }

        /* ── ID Card ── */
        .id-card {
            background: linear-gradient(135deg, #1a1208, #2d1f00);
            border: 1px solid rgba(245,159,11,0.25);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .id-card-left h4 { font-size: 13px; color: #f59e0b; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .id-card-left p  { font-size: 28px; font-weight: 800; color: #fbbf24; letter-spacing: 2px; }
        .id-card-right   { font-size: 36px; opacity: 0.3; }

        @media (max-width: 640px) {
            .profile-card { flex-direction: column; align-items: center; text-align: center; }
            .stats-grid   { grid-template-columns: 1fr; }
            .id-card      { flex-direction: column; gap: 12px; text-align: center; }
        }
    </style>
</head>
<body>

@include('admin.navbar')

<div class="page">

    <div class="page-header">
        <div>
            <div class="page-title">My Profile</div>
            <div class="page-breadcrumb">Kenakata › <span>Admin Profile</span></div>
        </div>
    </div>

    <!-- ── Profile Card ── -->
    <div class="profile-card">
        <div class="avatar-wrap">
            <div class="avatar">{{ strtoupper(substr($admin->AdminName, 0, 1)) }}</div>
            <div class="avatar-status">Active</div>
        </div>

        <div class="profile-info">
            <div class="profile-name">{{ $admin->AdminName }}</div>
            <div class="role-badge">🛡️ Administrator</div>

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-icon">🪪</div>
                    <div>
                        <div class="info-label">Admin ID</div>
                        <div class="info-value">#{{ $admin->AdminID }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📧</div>
                    <div>
                        <div class="info-label">Email Address</div>
                        <div class="info-value">{{ $admin->Email }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">🔐</div>
                    <div>
                        <div class="info-label">Access Level</div>
                        <div class="info-value">Full System Access</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Stats ── -->
    <div class="stats-grid">
        <div class="stat-card gold">
            <div class="stat-icon">👔</div>
            <div class="stat-num gold">{{ $employeeCount }}</div>
            <div class="stat-label">My Employees</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-icon">🛍️</div>
            <div class="stat-num blue">{{ $customerCount }}</div>
            <div class="stat-label">Total Customers</div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon">🚴</div>
            <div class="stat-num green">{{ $deliveryManCount }}</div>
            <div class="stat-label">Delivery Men</div>
        </div>
    </div>

    <!-- ── Admin ID Card ── -->
    <div class="id-card">
        <div class="id-card-left">
            <h4>Admin Credential ID</h4>
            <p>ADM-{{ str_pad($admin->AdminID, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="id-card-right">🛡️</div>
    </div>

</div>
</body>
</html>
