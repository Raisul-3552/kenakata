<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Kenakata</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --role-color: #f59e0b;
            --role-bg: rgba(245,159,11,0.12);
            --role-border: rgba(245,159,11,0.4);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #060614;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ── Animated background orbs ── */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: float 8s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        .bg-orb-1 { width: 500px; height: 500px; background: #7c3aed; top: -100px; left: -100px; animation-delay: 0s; }
        .bg-orb-2 { width: 400px; height: 400px; background: #0284c7; bottom: -80px; right: -80px; animation-delay: 3s; }
        .bg-orb-3 { width: 300px; height: 300px; background: #f59e0b; top: 40%; left: 50%; animation-delay: 6s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        /* ── Card ── */
        .card {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 44px 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.5);
            transition: border-color 0.4s ease;
        }

        /* ── Logo ── */
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }
        .logo-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800;
            color: #0f0a00;
            background: linear-gradient(135deg, #f59e0b, #fcd34d);
            flex-shrink: 0;
        }
        .logo-text { display: flex; flex-direction: column; }
        .logo-name  { font-size: 20px; font-weight: 800; color: #f8fafc; letter-spacing: -0.3px; }
        .logo-tag   { font-size: 11px; color: #64748b; font-weight: 400; }

        h2 {
            font-size: 26px;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 28px;
        }

        /* ── Role Tabs ── */
        .role-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 6px;
            margin-bottom: 26px;
            background: rgba(255,255,255,0.04);
            border-radius: 12px;
            padding: 4px;
            border: 1px solid rgba(255,255,255,0.07);
        }
        .role-tab {
            padding: 8px 4px;
            border-radius: 8px;
            border: none;
            background: transparent;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: all 0.25s ease;
            font-family: 'Inter', sans-serif;
        }
        .role-tab .tab-icon { font-size: 18px; }
        .role-tab .tab-label { font-size: 10px; font-weight: 600; color: #475569; letter-spacing: 0.3px; text-transform: uppercase; transition: color 0.25s ease; }
        .role-tab:hover .tab-label { color: #94a3b8; }

        .role-tab.active[data-role="Admin"]       { background: rgba(245,159,11,0.15); }
        .role-tab.active[data-role="Employee"]    { background: rgba(124,58,237,0.15); }
        .role-tab.active[data-role="Customer"]    { background: rgba(2,132,199,0.15);  }
        .role-tab.active[data-role="DeliveryMan"] { background: rgba(22,163,74,0.15);  }

        .role-tab.active[data-role="Admin"]       .tab-label { color: #f59e0b; }
        .role-tab.active[data-role="Employee"]    .tab-label { color: #a78bfa; }
        .role-tab.active[data-role="Customer"]    .tab-label { color: #38bdf8; }
        .role-tab.active[data-role="DeliveryMan"] .tab-label { color: #4ade80; }

        /* ── Input Fields ── */
        .field { margin-bottom: 16px; position: relative; }
        .field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .field input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: #f8fafc;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.25s ease, background 0.25s ease;
        }
        .field input::placeholder { color: #334155; }
        .field input:focus {
            border-color: var(--role-color);
            background: rgba(255,255,255,0.09);
        }

        /* ── Login Button ── */
        .btn-login {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            color: #fff;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            transition: all 0.3s ease;
            margin-top: 4px;
            box-shadow: 0 4px 15px rgba(245,159,11,0.35);
            letter-spacing: 0.2px;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245,159,11,0.45); }
        .btn-login:active { transform: translateY(0); }

        /* Role-based button colors */
        body.role-Admin       .btn-login { background: linear-gradient(135deg, #f59e0b, #fbbf24); box-shadow: 0 4px 15px rgba(245,159,11,0.35); }
        body.role-Employee    .btn-login { background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 4px 15px rgba(124,58,237,0.35); }
        body.role-Customer    .btn-login { background: linear-gradient(135deg, #0284c7, #38bdf8); box-shadow: 0 4px 15px rgba(2,132,199,0.35); }
        body.role-DeliveryMan .btn-login { background: linear-gradient(135deg, #16a34a, #4ade80); box-shadow: 0 4px 15px rgba(22,163,74,0.35); color: #052e16; }

        /* ── Error ── */
        .alert-error {
            background: rgba(185,28,28,0.15);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
        }
        .alert-error ul { margin: 0; padding-left: 16px; }

        .alert-success {
            background: rgba(22,163,74,0.12);
            border: 1px solid rgba(74,222,128,0.3);
            color: #86efac;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        /* ── Footer link ── */
        .footer-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #475569;
        }
        .footer-link a { color: #f59e0b; text-decoration: none; font-weight: 600; }
        .footer-link a:hover { text-decoration: underline; }

        /* Role indicator bar */
        .role-bar {
            height: 3px;
            border-radius: 2px;
            margin-bottom: 24px;
            background: #f59e0b;
            transition: background 0.4s ease;
        }

        @media (max-width: 480px) {
            .card { padding: 32px 24px; margin: 16px; }
        }
    </style>
</head>
<body class="role-Admin">

    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-orb bg-orb-3"></div>

    <div class="card">
        <!-- Logo -->
        <div class="logo">
            <div class="logo-icon">K</div>
            <div class="logo-text">
                <span class="logo-name">Kenakata</span>
                <span class="logo-tag">Online Shopping Platform</span>
            </div>
        </div>

        <!-- Role active bar -->
        <div class="role-bar" id="roleBar"></div>

        <h2>Welcome back</h2>
        <p class="subtitle">Sign in to your account to continue</p>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Role Tabs -->
        <div class="role-tabs">
            <button type="button" class="role-tab active" data-role="Admin" onclick="selectRole('Admin')">
                <span class="tab-icon">🛡️</span>
                <span class="tab-label">Admin</span>
            </button>
            <button type="button" class="role-tab" data-role="Employee" onclick="selectRole('Employee')">
                <span class="tab-icon">👔</span>
                <span class="tab-label">Employee</span>
            </button>
            <button type="button" class="role-tab" data-role="Customer" onclick="selectRole('Customer')">
                <span class="tab-icon">🛍️</span>
                <span class="tab-label">Customer</span>
            </button>
            <button type="button" class="role-tab" data-role="DeliveryMan" onclick="selectRole('DeliveryMan')">
                <span class="tab-icon">🚴</span>
                <span class="tab-label">Delivery</span>
            </button>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="user_type" id="userTypeInput" value="{{ old('user_type', 'Admin') }}">

            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required value="{{ old('email') }}" autocomplete="email">
            </div>

            <div class="field">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login" id="loginBtn">Sign In as Admin</button>
        </form>

        <div class="footer-link">
            Don't have an account? <a href="{{ route('register.form') }}">Register here</a>
        </div>
    </div>

    <script>
        const roleColors = {
            Admin:       { color: '#f59e0b', bar: '#f59e0b' },
            Employee:    { color: '#a78bfa', bar: '#7c3aed' },
            Customer:    { color: '#38bdf8', bar: '#0284c7' },
            DeliveryMan: { color: '#4ade80', bar: '#16a34a' },
        };

        const roleNames = {
            Admin: 'Admin',
            Employee: 'Employee',
            Customer: 'Customer',
            DeliveryMan: 'Delivery Man',
        };

        function selectRole(role) {
            // Update hidden input
            document.getElementById('userTypeInput').value = role;

            // Update tabs active state
            document.querySelectorAll('.role-tab').forEach(tab => {
                tab.classList.toggle('active', tab.dataset.role === role);
            });

            // Update body class for button colors
            document.body.className = 'role-' + role;

            // Update CSS variable for input focus
            document.documentElement.style.setProperty('--role-color', roleColors[role].color);

            // Update role bar color
            document.getElementById('roleBar').style.background = roleColors[role].bar;

            // Update button text
            document.getElementById('loginBtn').textContent = 'Sign In as ' + roleNames[role];
        }

        // Init with previously selected role (after form error)
        const prevRole = '{{ old("user_type", "Admin") }}';
        selectRole(prevRole);
    </script>
</body>
</html>