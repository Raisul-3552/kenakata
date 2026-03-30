<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Man Profile | Kenakata</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #050f08;
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
        .page-breadcrumb span { color: #4ade80; }

        /* ── Profile Card ── */
        .profile-card {
            background: linear-gradient(145deg, #08140a, #0d1f10);
            border: 1px solid rgba(22,163,74,0.2);
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
            background: radial-gradient(circle, rgba(74,222,128,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .avatar {
            width: 96px; height: 96px; border-radius: 24px;
            background: linear-gradient(135deg, #16a34a, #4ade80);
            display: flex; align-items: center; justify-content: center;
            font-size: 38px; font-weight: 800; color: #052e16;
            box-shadow: 0 0 0 4px rgba(22,163,74,0.2), 0 8px 24px rgba(22,163,74,0.3);
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
            background: rgba(22,163,74,0.15); color: #4ade80;
            border: 1px solid rgba(22,163,74,0.3);
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
            background: rgba(22,163,74,0.12);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .info-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 15px; font-weight: 500; color: #e2e8f0; }

        /* ── Delivery Status Banner ── */
        .status-banner {
            background: linear-gradient(135deg, #0d2517, #0a1f10);
            border: 1px solid rgba(74,222,128,0.2);
            border-radius: 16px;
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 16px;
        }
        .status-left { display: flex; align-items: center; gap: 16px; }
        .status-dot {
            width: 14px; height: 14px; border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 0 4px rgba(74,222,128,0.2);
            animation: pulse 2s infinite;
            flex-shrink: 0;
        }
        .status-text h4 { font-size: 15px; font-weight: 700; color: #86efac; }
        .status-text p  { font-size: 13px; color: #4b7a5a; margin-top: 2px; }
        .status-badge {
            padding: 7px 16px; border-radius: 20px;
            background: rgba(74,222,128,0.12); color: #4ade80;
            font-size: 13px; font-weight: 700;
            border: 1px solid rgba(74,222,128,0.25);
        }

        /* ── Stats ── */
        .stats-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: linear-gradient(145deg, #08140a, #0d1f10);
            border: 1px solid rgba(22,163,74,0.15);
            border-radius: 14px; padding: 22px 16px;
            text-align: center;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .stat-card:hover { transform: translateY(-3px); border-color: rgba(74,222,128,0.4); }
        .stat-icon  { font-size: 26px; margin-bottom: 8px; }
        .stat-num   { font-size: 28px; font-weight: 800; color: #4ade80; letter-spacing: -0.5px; }
        .stat-label { font-size: 12px; font-weight: 500; color: #4b7a5a; margin-top: 4px; }

        /* ── ID Card ── */
        .id-card {
            background: linear-gradient(135deg, #071209, #0a1c0b);
            border: 1px solid rgba(22,163,74,0.25);
            border-radius: 16px;
            padding: 24px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .id-card-left h4 { font-size: 13px; color: #4ade80; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .id-card-left p  { font-size: 28px; font-weight: 800; color: #86efac; letter-spacing: 2px; }
        .id-card-right   { font-size: 36px; opacity: 0.3; }

        @media (max-width: 640px) {
            .profile-card { flex-direction: column; align-items: center; text-align: center; }
            .status-banner { flex-direction: column; text-align: center; }
            .stats-grid { grid-template-columns: 1fr; }
            .id-card { flex-direction: column; gap: 12px; text-align: center; }
        }
    </style>
</head>
<body>

@include('deliveryman.navbar')

<div class="page">

    <div class="page-header">
        <div class="page-title">My Profile</div>
        <div class="page-breadcrumb">Kenakata › <span>Delivery Man Profile</span></div>
    </div>

    <!-- ── Profile Card ── -->
    <div class="profile-card">
        <div>
            <div class="avatar">{{ strtoupper(substr($deliveryman->DelManName, 0, 1)) }}</div>
            <div class="avatar-status">On Duty</div>
        </div>

        <div class="profile-info">
            <div class="profile-name">{{ $deliveryman->DelManName }}</div>
            <div class="role-badge">🚴 Delivery Man</div>

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-icon">🪪</div>
                    <div>
                        <div class="info-label">Delivery Man ID</div>
                        <div class="info-value">#{{ $deliveryman->DelManID }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📧</div>
                    <div>
                        <div class="info-label">Email Address</div>
                        <div class="info-value">{{ $deliveryman->Email }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📞</div>
                    <div>
                        <div class="info-label">Phone Number</div>
                        <div class="info-value">{{ $deliveryman->Phone }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">🏠</div>
                    <div>
                        <div class="info-label">Home Address</div>
                        <div class="info-value">{{ $deliveryman->Address }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Status Banner ── -->
    <div class="status-banner">
        <div class="status-left">
            <div class="status-dot"></div>
            <div class="status-text">
                <h4>Currently Available</h4>
                <p>Ready to accept new delivery assignments</p>
            </div>
        </div>
        <div class="status-badge">✅ On Duty</div>
    </div>

    <!-- ── Quick Stats ── -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-num">0</div>
            <div class="stat-label">Active Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-num">0</div>
            <div class="stat-label">Delivered Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-num">—</div>
            <div class="stat-label">Rating</div>
        </div>
    </div>

    <!-- ── ID Card ── -->
    <div class="id-card">
        <div class="id-card-left">
            <h4>Delivery Man Credential ID</h4>
            <p>DEL-{{ str_pad($deliveryman->DelManID, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="id-card-right">🚴</div>
    </div>

</div>
</body>
</html>
