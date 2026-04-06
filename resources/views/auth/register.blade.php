<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Kenakata</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top left, rgba(2,132,199,0.22), transparent 30%),
                        radial-gradient(circle at bottom right, rgba(245,158,11,0.18), transparent 25%),
                        linear-gradient(135deg, #060614, #111827);
            padding: 24px 16px;
        }
        .bg-orb {
            position: fixed; border-radius: 50%; filter: blur(90px); opacity: 0.28;
            pointer-events: none; z-index: 0; animation: float 9s ease-in-out infinite;
        }
        .bg-orb-1 { width: 420px; height: 420px; background: #f59e0b; top: -80px; right: -90px; }
        .bg-orb-2 { width: 360px; height: 360px; background: #0284c7; bottom: -70px; left: -80px; animation-delay: 3s; }
        @keyframes float { 0%,100% { transform: translateY(0) scale(1); } 50% { transform: translateY(-24px) scale(1.04); } }
        .card {
            position: relative; z-index: 1;
            width: 100%; max-width: 560px;
            padding: 40px 36px; border-radius: 24px;
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
            background: rgba(34,197,94,0.12); border: 1px solid rgba(74,222,128,0.18);
            color: #bbf7d0; font-size: 13px; line-height: 1.45;
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
        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .btn-register {
            width: 100%; padding: 13px; border: none; border-radius: 10px;
            font-size: 15px; font-weight: 800; cursor: pointer; color: #fff;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            box-shadow: 0 4px 15px rgba(2,132,199,0.35);
        }
        .btn-register:hover { transform: translateY(-2px); }
        .alert-error {
            display: none; margin-bottom: 18px; padding: 12px 14px; border-radius: 10px;
            background: rgba(185,28,28,0.15); border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5; font-size: 14px;
        }
        .footer-link { text-align: center; margin-top: 18px; font-size: 14px; color: #64748b; }
        .footer-link a { color: #f59e0b; font-weight: 700; text-decoration: none; }
        .footer-link a:hover { text-decoration: underline; }
        @media (max-width: 560px) { .card { padding: 30px 22px; } .field-row { grid-template-columns: 1fr; } }
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
                <div class="logo-tag">Customer Registration</div>
            </div>
        </div>

        <h2>Create customer account</h2>
        <p class="subtitle">Register to start shopping and managing your orders</p>

        <div class="alert-error" id="error-message"></div>

        <form id="registerForm">
            <div class="field">
                <label>Full Name</label>
                <input type="text" id="reg_name" placeholder="Enter your full name" required>
            </div>
            <div class="field-row">
                <div class="field">
                    <label>Phone</label>
                    <input type="text" id="reg_phone" placeholder="e.g. 01712345678" required>
                </div>
                <div class="field">
                    <label>Email Address</label>
                    <input type="email" id="reg_email" placeholder="Enter your email" required>
                </div>
            </div>
            <div class="field">
                <label>Address</label>
                <input type="text" id="reg_address" placeholder="Your address" required>
            </div>
            <div class="field-row">
                <div class="field">
                    <label>Password</label>
                    <input type="password" id="reg_password" placeholder="Min. 6 characters" required minlength="6">
                </div>
                <div class="field">
                    <label>Confirm Password</label>
                    <input type="password" id="reg_password_confirm" placeholder="Repeat password" required minlength="6">
                </div>
            </div>

            <button type="submit" class="btn-register" id="btnRegister">Create</button>
        </form>

        <div class="footer-link">Already have an account? <a href="/login">Login here</a></div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const errorDiv = document.getElementById('error-message');
            const btn = document.getElementById('btnRegister');
            errorDiv.style.display = 'none';

            const password = document.getElementById('reg_password').value;
            const confirm = document.getElementById('reg_password_confirm').value;

            if (password !== confirm) {
                errorDiv.textContent = 'Passwords do not match.';
                errorDiv.style.display = 'block';
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating account...';

            fetch('/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    CustomerName: document.getElementById('reg_name').value,
                    Phone: document.getElementById('reg_phone').value,
                    Email: document.getElementById('reg_email').value,
                    Address: document.getElementById('reg_address').value,
                    Password: password,
                })
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                if (res.status === 201) {
                    localStorage.setItem('kenakata_token', res.body.token);
                    localStorage.setItem('kenakata_role', res.body.role);
                    localStorage.setItem('kenakata_user', JSON.stringify(res.body.user || {}));
                    window.location.href = '/customer/dashboard';
                } else {
                    const errors = res.body.errors ? Object.values(res.body.errors).flat().join('<br>') : (res.body.message || 'Registration failed.');
                    errorDiv.innerHTML = errors;
                    errorDiv.style.display = 'block';
                }
            })
            .catch(() => {
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.style.display = 'block';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Create';
            });
        });
    </script>
</body>
</html>
