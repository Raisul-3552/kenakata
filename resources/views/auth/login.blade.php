<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Kenakata</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: radial-gradient(circle at top left, rgba(2,132,199,0.22), transparent 28%),
                        radial-gradient(circle at bottom right, rgba(245,158,11,0.18), transparent 24%),
                        linear-gradient(135deg, #060614, #111827);
        }
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.3;
            pointer-events: none;
            z-index: 0;
            animation: float 9s ease-in-out infinite;
        }
        .bg-orb-1 { width: 420px; height: 420px; background: #0284c7; top: -80px; left: -80px; }
        .bg-orb-2 { width: 340px; height: 340px; background: #f59e0b; bottom: -60px; right: -60px; animation-delay: 3s; }
        @keyframes float { 0%,100% { transform: translateY(0) scale(1); } 50% { transform: translateY(-24px) scale(1.04); } }
        .card {
            position: relative; z-index: 1;
            width: 100%; max-width: 460px;
            padding: 40px 36px;
            border-radius: 24px;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 24px 64px rgba(0,0,0,0.5);
        }
        .logo { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .logo-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800; color: #0f0a00;
            background: linear-gradient(135deg, #f59e0b, #fcd34d);
        }
        .logo-name { font-size: 20px; font-weight: 800; color: #f8fafc; }
        .logo-tag { font-size: 11px; color: #64748b; }
        h2 { color: #f8fafc; font-size: 26px; margin-bottom: 6px; }
        .subtitle { color: #94a3b8; font-size: 14px; margin-bottom: 26px; }
        .notice {
            margin-bottom: 18px; padding: 12px 14px; border-radius: 12px;
            background: rgba(2,132,199,0.12); border: 1px solid rgba(56,189,248,0.18);
            color: #bae6fd; font-size: 13px; line-height: 1.45;
        }
        .field { margin-bottom: 16px; }
        .field label {
            display: block; font-size: 12px; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
        }
        .field input {
            width: 100%; padding: 12px 16px; border-radius: 10px;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);
            color: #f8fafc; outline: none; font-size: 15px;
        }
        .field input:focus { border-color: #38bdf8; background: rgba(255,255,255,0.09); }
        .field input::placeholder { color: #334155; }
        .btn-login {
            width: 100%; padding: 13px; border: none; border-radius: 10px;
            font-size: 15px; font-weight: 800; cursor: pointer; color: #fff;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            box-shadow: 0 4px 15px rgba(2,132,199,0.35);
        }
        .btn-login:hover { transform: translateY(-2px); }
        .alert-error {
            display: none; margin-bottom: 18px; padding: 12px 14px; border-radius: 10px;
            background: rgba(185,28,28,0.15); border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5; font-size: 14px;
        }
        .footer-link { text-align: center; margin-top: 18px; font-size: 14px; color: #64748b; }
        .footer-link a { color: #f59e0b; font-weight: 700; text-decoration: none; }
        .footer-link a:hover { text-decoration: underline; }
        @media (max-width: 480px) { .card { padding: 30px 22px; margin: 16px; } }
    </style>
</head>
<body>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>

    <div class="card">
        <div class="logo">
            <div class="logo-icon">K</div>
            <div>
                <div class="logo-name">Kenakata</div>
                <div class="logo-tag">Online Shopping Platform</div>
            </div>
        </div>

        <h2>Welcome back</h2>
        <p class="subtitle">Sign in with your email and password</p>

        <div class="alert-error" id="error-message"></div>

        <form id="loginForm">
            <div class="field">
                <label>Email Address</label>
                <input type="email" id="email" placeholder="Enter your email" required autocomplete="email">
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" id="password" placeholder="Enter your password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-login" id="loginBtn">Sign In</button>
        </form>

        <div class="footer-link">Need a customer account? <a href="/register">Register here</a></div>
    </div>

    <script>
        const roleDashboard = {
            admin: '/admin/dashboard',
            employee: '/employee/dashboard',
            customer: '/customer/dashboard',
            deliveryman: '/deliveryman/dashboard'
        };

        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('error-message');
            const btn = document.getElementById('loginBtn');

            errorDiv.style.display = 'none';
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Signing in...';

            fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ Email: email, Password: password })
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                if (res.status === 200) {
                    localStorage.setItem('kenakata_token', res.body.token);
                    localStorage.setItem('kenakata_role', res.body.role);
                    localStorage.setItem('kenakata_user', JSON.stringify(res.body.user || {}));
                    window.location.href = roleDashboard[res.body.role] || '/customer/dashboard';
                } else {
                    errorDiv.textContent = res.body.message || 'Login failed. Check your credentials.';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(() => {
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.style.display = 'block';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Sign In';
            });
        });
    </script>
</body>
</html>
