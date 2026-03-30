<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard | Kenakata</title>
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
        .page-header {
            margin-bottom: 32px;
        }
        .page-title { font-size: 22px; font-weight: 700; color: #f8fafc; }
        .page-breadcrumb { font-size: 13px; color: #475569; margin-top: 3px; }
        .page-breadcrumb span { color: #a78bfa; }
        
        .content-card {
            background: linear-gradient(145deg, #110e1e, #180d33);
            border: 1px solid rgba(124,58,237,0.2);
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
        }
        .content-card h2 { font-size: 28px; font-weight: 800; color: #a78bfa; margin-bottom: 12px; }
        .content-card p { font-size: 16px; color: #94a3b8; line-height: 1.6; }
    </style>
</head>
<body>

@include('employee.navbar')

<div class="page">
    <div class="page-header">
        <div class="page-title">Employee Dashboard</div>
        <div class="page-breadcrumb">Kenakata › <span>Dashboard</span></div>
    </div>

    <div class="content-card">
        <h2>Welcome to the Employee Dashboard</h2>
        <p>This is the dashboard of Employee ({{ $employee->EmployeeName }}).</p>
    </div>
</div>

</body>
</html>
