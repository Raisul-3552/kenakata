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
            background: #060614;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 32px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background */
        .bg-orb {
            position: fixed; border-radius: 50%; filter: blur(90px);
            opacity: 0.3; animation: float 9s ease-in-out infinite; pointer-events: none; z-index: 0;
        }
        .bg-orb-1 { width: 450px; height: 450px; background: #7c3aed; top: -80px; right: -80px; animation-delay: 0s; }
        .bg-orb-2 { width: 380px; height: 380px; background: #0284c7; bottom: 0; left: -60px; animation-delay: 4s; }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-25px) scale(1.04); }
        }

        /* Wrapper */
        .wrapper {
            position: relative; z-index: 1;
            width: 100%; max-width: 560px;
        }

        /* ── Logo ── */
        .logo {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 32px; justify-content: center;
        }
        .logo-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800; color: #0f0a00;
            background: linear-gradient(135deg, #f59e0b, #fcd34d);
        }
        .logo-name { font-size: 22px; font-weight: 800; color: #f8fafc; letter-spacing: -0.3px; }

        /* ── Step indicator ── */
        .steps {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 28px; justify-content: center;
        }
        .step {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; font-weight: 600;
        }
        .step-num {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
            border: 2px solid #1e293b; color: #475569;
            background: transparent; transition: all 0.3s ease;
        }
        .step.active .step-num { background: #f59e0b; border-color: #f59e0b; color: #0f0a00; }
        .step.done   .step-num { background: #1e293b; border-color: #334155; color: #94a3b8; }
        .step-label { color: #475569; transition: color 0.3s ease; }
        .step.active .step-label { color: #f8fafc; }
        .step-sep { width: 40px; height: 2px; background: #1e293b; border-radius: 1px; }

        /* ── Card ── */
        .card {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 24px; padding: 36px 36px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.5);
            transition: border-color 0.4s ease;
        }

        h2 { font-size: 24px; font-weight: 700; color: #f8fafc; margin-bottom: 6px; letter-spacing: -0.4px; }
        .subtitle { font-size: 14px; color: #64748b; margin-bottom: 28px; }

        /* ── Role Selection Cards (Step 1) ── */
        #step1 {}
        .role-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 12px; margin-bottom: 8px;
        }
        .role-card {
            padding: 20px 16px;
            border-radius: 14px;
            border: 2px solid rgba(255,255,255,0.07);
            background: rgba(255,255,255,0.04);
            cursor: pointer;
            transition: all 0.25s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .role-card::before {
            content: ''; position: absolute; inset: 0;
            opacity: 0; transition: opacity 0.25s ease;
            border-radius: 12px;
        }
        .role-card[data-role="Admin"]::before       { background: rgba(245,159,11,0.08); }
        .role-card[data-role="Employee"]::before    { background: rgba(124,58,237,0.08); }
        .role-card[data-role="Customer"]::before    { background: rgba(2,132,199,0.08);  }
        .role-card[data-role="DeliveryMan"]::before { background: rgba(22,163,74,0.08);  }
        .role-card:hover::before { opacity: 1; }
        .role-card:hover { transform: translateY(-3px); }

        .role-card.selected[data-role="Admin"]       { border-color: #f59e0b; background: rgba(245,159,11,0.1); }
        .role-card.selected[data-role="Employee"]    { border-color: #7c3aed; background: rgba(124,58,237,0.1); }
        .role-card.selected[data-role="Customer"]    { border-color: #0284c7; background: rgba(2,132,199,0.1); }
        .role-card.selected[data-role="DeliveryMan"] { border-color: #16a34a; background: rgba(22,163,74,0.1); }

        .role-emoji { font-size: 32px; margin-bottom: 8px; display: block; }
        .role-card-name  { font-size: 15px; font-weight: 700; color: #f8fafc; margin-bottom: 4px; }
        .role-card-desc  { font-size: 11px; color: #64748b; line-height: 1.4; }

        .btn-continue {
            width: 100%; padding: 13px; margin-top: 20px;
            border: none; border-radius: 10px;
            font-size: 15px; font-weight: 700; font-family: 'Inter', sans-serif;
            cursor: pointer; color: #0f0a00;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245,159,11,0.3);
            letter-spacing: 0.2px;
            opacity: 0.5; pointer-events: none;
        }
        .btn-continue.ready { opacity: 1; pointer-events: all; }
        .btn-continue.ready:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245,159,11,0.4); }

        /* ── Registration Form (Step 2) ── */
        #step2 { display: none; }

        .role-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 14px; border-radius: 20px; margin-bottom: 20px;
            font-size: 13px; font-weight: 600;
        }
        .badge-admin       { background: rgba(245,159,11,0.15); color: #f59e0b; border: 1px solid rgba(245,159,11,0.3); }
        .badge-employee    { background: rgba(124,58,237,0.15); color: #a78bfa; border: 1px solid rgba(124,58,237,0.3); }
        .badge-customer    { background: rgba(2,132,199,0.15);  color: #38bdf8; border: 1px solid rgba(2,132,199,0.3);  }
        .badge-deliveryman { background: rgba(22,163,74,0.15);  color: #4ade80; border: 1px solid rgba(22,163,74,0.3);  }

        /* Form fields */
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .field input {
            width: 100%; padding: 12px 16px;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; color: #f8fafc; font-size: 15px;
            font-family: 'Inter', sans-serif; outline: none;
            transition: border-color 0.25s ease, background 0.25s ease;
        }
        .field input::placeholder { color: #334155; }
        .field input:focus { border-color: #f59e0b; background: rgba(255,255,255,0.09); }

        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .field-hint { font-size: 11px; color: #475569; margin-top: 5px; }

        .btn-register {
            width: 100%; padding: 13px; margin-top: 4px;
            border: none; border-radius: 10px;
            font-size: 15px; font-weight: 700; font-family: 'Inter', sans-serif;
            cursor: pointer; color: #fff;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(245,159,11,0.3);
        }
        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245,159,11,0.4); }

        .back-link {
            text-align: center; margin-top: 14px; font-size: 13px; color: #475569; cursor: pointer;
        }
        .back-link span { color: #94a3b8; text-decoration: underline; cursor: pointer; }
        .back-link span:hover { color: #f8fafc; }

        /* Error/Success alerts */
        .alert-error {
            background: rgba(185,28,28,0.15); border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5; padding: 12px 14px; border-radius: 10px;
            margin-bottom: 18px; font-size: 14px;
        }
        .alert-error ul { margin: 0; padding-left: 16px; }
        .alert-success {
            background: rgba(22,163,74,0.12); border: 1px solid rgba(74,222,128,0.3);
            color: #86efac; padding: 12px 14px; border-radius: 10px;
            margin-bottom: 18px; font-size: 14px;
        }

        /* Footer link */
        .footer-link { text-align: center; margin-top: 20px; font-size: 14px; color: #475569; }
        .footer-link a { color: #f59e0b; text-decoration: none; font-weight: 600; }
        .footer-link a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .card { padding: 28px 22px; }
            .role-grid { grid-template-columns: 1fr 1fr; }
            .field-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>

    <div class="wrapper">
        <!-- Logo -->
        <div class="logo">
            <div class="logo-icon">K</div>
            <span class="logo-name">Kenakata</span>
        </div>

        <!-- Step indicator -->
        <div class="steps">
            <div class="step active" id="stepIndicator1">
                <div class="step-num">1</div>
                <span class="step-label">Choose Role</span>
            </div>
            <div class="step-sep"></div>
            <div class="step" id="stepIndicator2">
                <div class="step-num">2</div>
                <span class="step-label">Your Details</span>
            </div>
        </div>

        <div class="card">

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

            <!-- ════════════ STEP 1: Choose Role ════════════ -->
            <div id="step1">
                <h2>Create an account</h2>
                <p class="subtitle">First, select what type of user you are</p>

                <div class="role-grid">
                    <div class="role-card" data-role="Admin" onclick="selectRoleCard('Admin')">
                        <span class="role-emoji">🛡️</span>
                        <div class="role-card-name">Admin</div>
                        <div class="role-card-desc">Manage the platform and its users</div>
                    </div>
                    <div class="role-card" data-role="Employee" onclick="selectRoleCard('Employee')">
                        <span class="role-emoji">👔</span>
                        <div class="role-card-name">Employee</div>
                        <div class="role-card-desc">Staff member assigned to an admin</div>
                    </div>
                    <div class="role-card" data-role="Customer" onclick="selectRoleCard('Customer')">
                        <span class="role-emoji">🛍️</span>
                        <div class="role-card-name">Customer</div>
                        <div class="role-card-desc">Browse and purchase products</div>
                    </div>
                    <div class="role-card" data-role="DeliveryMan" onclick="selectRoleCard('DeliveryMan')">
                        <span class="role-emoji">🚴</span>
                        <div class="role-card-name">Delivery Man</div>
                        <div class="role-card-desc">Manage and complete deliveries</div>
                    </div>
                </div>

                <button class="btn-continue" id="btnContinue" onclick="goToStep2()">
                    Continue →
                </button>
            </div>

            <!-- ════════════ STEP 2: Fill Details ════════════ -->
            <div id="step2">
                <div id="roleBadge" class="role-badge badge-admin">🛡️ Registering as Admin</div>
                <h2>Your details</h2>
                <p class="subtitle">Fill in your information to create an account</p>

                <form method="POST" action="{{ route('register') }}" id="regForm">
                    @csrf
                    <input type="hidden" name="user_type" id="finalUserType" value="">

                    <!-- ── ADMIN FIELDS ── -->
                    <div id="fields-Admin" class="role-fields" style="display:none">
                        <div class="field">
                            <label>Full Name</label>
                            <input type="text" name="admin_name" placeholder="Enter your full name" value="{{ old('admin_name') }}">
                        </div>
                        <div class="field">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}">
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Min. 6 characters">
                            </div>
                            <div class="field">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>

                    <!-- ── EMPLOYEE FIELDS ── -->
                    <div id="fields-Employee" class="role-fields" style="display:none">
                        <div class="field">
                            <label>Full Name</label>
                            <input type="text" name="employee_name" placeholder="Enter your full name" value="{{ old('employee_name') }}">
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label>Phone</label>
                                <input type="text" name="phone" placeholder="e.g. 01712345678" value="{{ old('phone') }}">
                            </div>
                            <div class="field">
                                <label>Employment Code</label>
                                <input type="text" name="reg_code" placeholder="e.g. EMP-XXXX-XXXX" value="{{ old('reg_code') }}">
                                <div class="field-hint">Code provided by your Administrator</div>
                            </div>
                        </div>
                        <div class="field">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}">
                        </div>
                        <div class="field">
                            <label>Address</label>
                            <input type="text" name="address" placeholder="Your home address" value="{{ old('address') }}">
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Min. 6 characters">
                            </div>
                            <div class="field">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>

                    <!-- ── CUSTOMER FIELDS ── -->
                    <div id="fields-Customer" class="role-fields" style="display:none">
                        <div class="field">
                            <label>Full Name</label>
                            <input type="text" name="customer_name" placeholder="Enter your full name" value="{{ old('customer_name') }}">
                        </div>
                        <div class="field">
                            <label>Phone</label>
                            <input type="text" name="phone" placeholder="e.g. 01712345678" value="{{ old('phone') }}">
                        </div>
                        <div class="field">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}">
                        </div>
                        <div class="field">
                            <label>Delivery Address</label>
                            <input type="text" name="address" placeholder="Your default delivery address" value="{{ old('address') }}">
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Min. 6 characters">
                            </div>
                            <div class="field">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>

                    <!-- ── DELIVERY MAN FIELDS ── -->
                    <div id="fields-DeliveryMan" class="role-fields" style="display:none">
                        <div class="field">
                            <label>Full Name</label>
                            <input type="text" name="delman_name" placeholder="Enter your full name" value="{{ old('delman_name') }}">
                        </div>
                        <div class="field">
                            <label>Phone</label>
                            <input type="text" name="phone" placeholder="e.g. 01712345678" value="{{ old('phone') }}">
                        </div>
                        <div class="field">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}">
                        </div>
                        <div class="field">
                            <label>Home Address</label>
                            <input type="text" name="address" placeholder="Your home address" value="{{ old('address') }}">
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Min. 6 characters">
                            </div>
                            <div class="field">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-register" id="btnRegister">Create Account</button>
                </form>

                <div class="back-link">
                    <span onclick="goToStep1()">← Change role selection</span>
                </div>
            </div>

        </div><!-- .card -->

        <div class="footer-link" style="margin-top:20px; text-align:center; font-size:14px; color:#475569;">
            Already have an account? <a href="{{ route('login.form') }}" style="color:#f59e0b; font-weight:600; text-decoration:none;">Login here</a>
        </div>
    </div><!-- .wrapper -->

    <script>
        let selectedRole = null;

        const roleMeta = {
            Admin:       { emoji: '🛡️', label: 'Admin',        badge: 'badge-admin',       btnColor: '#f59e0b', textColor: '#0f0a00' },
            Employee:    { emoji: '👔', label: 'Employee',      badge: 'badge-employee',    btnColor: '#7c3aed', textColor: '#fff'    },
            Customer:    { emoji: '🛍️', label: 'Customer',      badge: 'badge-customer',    btnColor: '#0284c7', textColor: '#fff'    },
            DeliveryMan: { emoji: '🚴', label: 'Delivery Man',  badge: 'badge-deliveryman', btnColor: '#16a34a', textColor: '#052e16' },
        };

        function selectRoleCard(role) {
            selectedRole = role;
            document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
            document.querySelector(`.role-card[data-role="${role}"]`).classList.add('selected');

            const btn = document.getElementById('btnContinue');
            btn.classList.add('ready');
            btn.textContent = `Continue as ${roleMeta[role].label} →`;

            // Color the continue button
            const m = roleMeta[role];
            btn.style.background = m.btnColor;
            btn.style.color      = m.textColor;
        }

        function goToStep2() {
            if (!selectedRole) return;

            // Update step indicators
            document.getElementById('stepIndicator1').classList.remove('active');
            document.getElementById('stepIndicator1').classList.add('done');
            document.getElementById('stepIndicator2').classList.add('active');

            // Show step 2
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';

            // Show correct fields, enable only active inputs
            document.querySelectorAll('.role-fields').forEach(f => {
                f.style.display = 'none';
                f.querySelectorAll('input').forEach(i => i.disabled = true);
            });
            const activeFields = document.getElementById('fields-' + selectedRole);
            activeFields.style.display = 'block';
            activeFields.querySelectorAll('input').forEach(i => i.disabled = false);

            // Update badge
            const m = roleMeta[selectedRole];
            const badge = document.getElementById('roleBadge');
            badge.textContent = `${m.emoji} Registering as ${m.label}`;
            badge.className   = 'role-badge ' + m.badge;

            // Set hidden input
            document.getElementById('finalUserType').value = selectedRole;

            // Color register button
            const btn = document.getElementById('btnRegister');
            btn.style.background = m.btnColor;
            btn.style.color      = m.textColor;
        }

        function goToStep1() {
            document.getElementById('stepIndicator1').classList.add('active');
            document.getElementById('stepIndicator1').classList.remove('done');
            document.getElementById('stepIndicator2').classList.remove('active');
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step1').style.display = 'block';
        }

        // If there are validation errors, re-open step 2 with the old role
        @if($errors->any())
            const oldRole = '{{ old("user_type") }}';
            if (oldRole) {
                selectRoleCard(oldRole);
                goToStep2();
            }
        @endif
    </script>
</body>
</html>