<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employment Codes | Kenakata Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0f;
            color: #f8fafc;
            min-height: 100vh;
        }

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

        /* Button */
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: #0a0a0f;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(245,159,11,0.25);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(245,159,11,0.4);
        }

        /* Stats Card */
        .stats-card {
            background: linear-gradient(145deg, #111118, #16161f);
            border: 1px solid rgba(245,159,11,0.15);
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 24px;
            display: flex; gap: 40px;
        }
        .stat { display: flex; flex-direction: column; }
        .stat-val { font-size: 32px; font-weight: 800; color: #f59e0b; margin-bottom: 4px; }
        .stat-lbl { font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Table Card */
        .table-card {
            background: #111118;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            overflow: hidden;
        }
        .table-header {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            font-size: 16px; font-weight: 600;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; padding: 14px 24px;
            font-size: 12px; font-weight: 600; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.5px;
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        td {
            padding: 16px 24px;
            font-size: 14px; color: #e2e8f0;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        tr:last-child td { border-bottom: none; }
        
        .code-string {
            font-family: monospace;
            font-size: 15px;
            background: rgba(245,159,11,0.1);
            color: #f59e0b;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid rgba(245,159,11,0.2);
            letter-spacing: 1px;
        }

        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .badge-avail { background: rgba(74,222,128,0.15); color: #4ade80; border: 1px solid rgba(74,222,128,0.3); }
        .badge-used  { background: rgba(100,116,139,0.15); color: #94a3b8; border: 1px solid rgba(100,116,139,0.3); }

        .empty-state { text-align: center; padding: 60px 20px; color: #64748b; }
        .empty-icon { font-size: 48px; margin-bottom: 16px; opacity: 0.5; }

        .alert-success {
            background: rgba(22,163,74,0.12);
            border: 1px solid rgba(74,222,128,0.3);
            color: #86efac;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
        }
    </style>
</head>
<body>

@include('admin.navbar')

<div class="page">
    <div class="page-header">
        <div>
            <div class="page-title">Employment Codes</div>
            <div class="page-breadcrumb">Kenakata › <span>Employment Codes</span></div>
        </div>
        <form action="{{ route('admin.generate_code') }}" method="POST" style="display: flex; gap: 12px; align-items: center;">
            @csrf
            <div>
                <input type="text" name="reg_code" placeholder="Enter custom code..." required 
                       style="padding: 11px 16px; border-radius: 10px; border: 1px solid rgba(245,159,11,0.3); background: #111118; color: #f8fafc; font-family: 'Inter', sans-serif; font-size: 14px; outline: none; transition: border-color 0.2s;"
                       onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='rgba(245,159,11,0.3)'" value="{{ old('reg_code') }}">
            </div>
            <button type="submit" class="btn-primary">
                <span>➕</span> Create Code
            </button>
        </form>
    </div>

    @if($errors->has('reg_code'))
        <div style="color: #ef4444; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); padding: 12px; border-radius: 10px; margin-bottom: 24px; font-size: 14px;">
            ⚠️ {{ $errors->first('reg_code') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="stats-card">
        <div class="stat">
            <div class="stat-val">{{ $codes->count() }}</div>
            <div class="stat-lbl">Total Generated</div>
        </div>
        <div class="stat">
            <div class="stat-val" style="color: #4ade80;">{{ $codes->where('IsUsed', 0)->count() }}</div>
            <div class="stat-lbl">Available Now</div>
        </div>
        <div class="stat">
            <div class="stat-val" style="color: #94a3b8;">{{ $codes->where('IsUsed', 1)->count() }}</div>
            <div class="stat-lbl">Already Claimed</div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">Generated Codes</div>
        
        @if($codes->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🎟️</div>
                <p>You haven't generated any employment codes yet.</p>
                <p style="font-size: 13px; margin-top: 8px;">Generate a code to allow a new Employee to register under your ID.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Registration Code</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($codes as $code)
                        <tr>
                            <td>
                                <span class="code-string">{{ $code->RegCode }}</span>
                            </td>
                            <td>
                                @if($code->IsUsed)
                                    <span class="badge badge-used">Claimed</span>
                                @else
                                    <span class="badge badge-avail">Available</span>
                                @endif
                            </td>
                            <td style="color: #94a3b8; font-size: 13px;">
                                {{ \Carbon\Carbon::parse($code->CreatedAt)->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

</body>
</html>
