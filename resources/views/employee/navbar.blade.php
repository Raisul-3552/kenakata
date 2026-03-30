<style>
    /* ═══ Employee Navbar ═══ */
    .kk-nav {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 28px; height: 64px;
        background: #0d0b1e;
        border-bottom: 1px solid rgba(124,58,237,0.25);
        position: sticky; top: 0; z-index: 100;
        font-family: 'Inter', sans-serif;
    }
    .kk-nav .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .kk-nav .brand-icon {
        width: 36px; height: 36px; border-radius: 9px;
        background: linear-gradient(135deg, #7c3aed, #a78bfa);
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; font-weight: 800; color: #fff;
    }
    .kk-nav .brand-name  { font-size: 18px; font-weight: 800; color: #f8fafc; }
    .kk-nav .brand-badge {
        font-size: 9px; font-weight: 700; letter-spacing: 1px;
        text-transform: uppercase; padding: 2px 8px; border-radius: 20px;
        background: rgba(124,58,237,0.15); color: #a78bfa;
        border: 1px solid rgba(124,58,237,0.3);
    }
    .kk-nav .nav-links { display: flex; align-items: center; gap: 2px; }
    .kk-nav .nav-link {
        padding: 7px 14px; border-radius: 8px;
        text-decoration: none; color: #64748b;
        font-size: 14px; font-weight: 500; transition: all 0.2s ease;
    }
    .kk-nav .nav-link:hover  { color: #f8fafc; background: rgba(255,255,255,0.06); }
    .kk-nav .nav-link.active { color: #a78bfa; background: rgba(124,58,237,0.12); }
    .kk-nav .nav-right { display: flex; align-items: center; gap: 14px; }
    .kk-nav .user-chip {
        display: flex; align-items: center; gap: 10px;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
        padding: 6px 12px; border-radius: 50px;
    }
    .kk-nav .user-avatar {
        width: 30px; height: 30px; border-radius: 50%;
        background: linear-gradient(135deg, #7c3aed, #a78bfa);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #fff;
    }
    .kk-nav .user-name  { font-size: 14px; font-weight: 600; color: #f8fafc; }
    .kk-nav .user-role  { font-size: 11px; color: #64748b; }
    .kk-nav .btn-logout {
        padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(239,68,68,0.3);
        background: rgba(239,68,68,0.08); color: #fca5a5;
        font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif;
        cursor: pointer; transition: all 0.2s ease;
    }
    .kk-nav .btn-logout:hover { background: rgba(239,68,68,0.18); border-color: rgba(239,68,68,0.5); color: #fff; }
    .kk-nav .hamburger { display: none; background: none; border: none; color: #94a3b8; font-size: 22px; cursor: pointer; }

    @media (max-width: 768px) {
        .kk-nav .nav-links { display: none; flex-direction: column; position: absolute; top: 64px; left: 0; right: 0; background: #0d0b1e; padding: 16px; border-bottom: 1px solid rgba(124,58,237,0.2); gap: 4px; }
        .kk-nav .nav-links.open { display: flex; }
        .kk-nav .user-chip { display: none; }
        .kk-nav .hamburger { display: block; }
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<nav class="kk-nav">
    <a href="{{ route('employee.profile') }}" class="brand">
        <div class="brand-icon">K</div>
        <span class="brand-name">Kenakata</span>
        <span class="brand-badge">Employee</span>
    </a>

    <div class="nav-links" id="empNavLinks">
        <a href="{{ route('employee.dashboard') }}" class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('employee.profile') }}" class="nav-link {{ request()->routeIs('employee.profile') ? 'active' : '' }}">Profile</a>
    </div>

    <div class="nav-right">
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(session('kenakata_user')['name'] ?? 'E', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ session('kenakata_user')['name'] ?? '' }}</div>
                <div class="user-role">Employee</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
    <button class="hamburger" onclick="document.getElementById('empNavLinks').classList.toggle('open')">☰</button>
</nav>
