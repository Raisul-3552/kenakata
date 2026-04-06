@extends('layouts.employee')

@section('title', 'My Profile')

@section('employee_styles')
<style>
    .profile-avatar {
        width: 96px; height: 96px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745, #20c997);
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; font-weight: 700; color: #fff;
        flex-shrink: 0;
        box-shadow: 0 0 0 4px rgba(40,167,69,0.25);
    }
    .section-card {
        background: rgba(22, 33, 62, 0.85) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        border-radius: 16px;
    }
    .section-title {
        font-size: 1rem; font-weight: 700; letter-spacing: 0.04em;
        color: #2ecc71; text-transform: uppercase;
        border-bottom: 1px solid rgba(40,167,69,0.25);
        padding-bottom: 0.5rem; margin-bottom: 1.25rem;
    }
    .info-row { display: flex; gap: 0.5rem; align-items: start; margin-bottom: 0.9rem; }
    .info-label { min-width: 110px; color: rgba(255,255,255,0.5); font-size: 0.82rem; padding-top: 0.35rem; }
    .info-value { color: #fff; font-weight: 500; flex: 1; }
    .badge-role {
        background: linear-gradient(135deg,#0f3460,#28a745);
        font-size: 0.75rem; padding: 0.3em 0.75em; border-radius: 20px;
    }
</style>
@endsection

@section('employee_content')
<div id="profile-alert"></div>

<div class="row g-4">
    {{-- Left: Info card --}}
    <div class="col-lg-4">
        <div class="card section-card p-4 h-100">
            <div class="d-flex gap-3 align-items-center mb-4">
                <div class="profile-avatar" id="avatar-initials">?</div>
                <div>
                    <h5 class="mb-1 fw-bold" id="profile-name">Loading...</h5>
                    <span class="badge badge-role text-white">👔 Employee</span>
                </div>
            </div>
            <div class="section-title">Account Info</div>
            <div class="info-row">
                <span class="info-label">📧 Email</span>
                <span class="info-value" id="profile-email">—</span>
            </div>
            <div class="info-row">
                <span class="info-label">📱 Phone</span>
                <span class="info-value" id="profile-phone">—</span>
            </div>
            <div class="info-row">
                <span class="info-label">🏠 Address</span>
                <span class="info-value" id="profile-address">—</span>
            </div>
            <div class="info-row">
                <span class="info-label">🆔 Employee ID</span>
                <span class="info-value" id="profile-id">—</span>
            </div>
        </div>
    </div>

    {{-- Right: Edit form + Password --}}
    <div class="col-lg-8">
        {{-- Edit Profile --}}
        <div class="card section-card p-4 mb-4">
            <div class="section-title">✏️ Edit Profile</div>
            <form id="edit-profile-form">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Full Name</label>
                        <input type="text" class="form-control" id="edit-name" placeholder="Your full name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Phone Number</label>
                        <input type="text" class="form-control" id="edit-phone" placeholder="e.g. +8801XXXXXXXXX">
                    </div>
                    <div class="col-12">
                        <label class="form-label text-white-50 small">Address</label>
                        <textarea class="form-control" id="edit-address" rows="2" placeholder="Your address"></textarea>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">💾 Save Changes</button>
                    <button type="button" class="btn btn-outline-light px-4" onclick="loadProfile()">↺ Reset</button>
                </div>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="card section-card p-4">
            <div class="section-title">🔒 Change Password</div>
            <form id="change-password-form">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-white-50 small">Current Password</label>
                        <input type="password" class="form-control" id="current-password" placeholder="Enter your current password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">New Password</label>
                        <input type="password" class="form-control" id="new-password" placeholder="Min. 6 characters">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm-password" placeholder="Repeat new password">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-warning text-dark fw-bold px-4">🔑 Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    loadProfile();
    document.getElementById('edit-profile-form').addEventListener('submit', saveProfile);
    document.getElementById('change-password-form').addEventListener('submit', changePassword);
});

function loadProfile() {
    fetch(`${API_URL}/employee/profile`, { headers: getHeaders() })
    .then(r => { if (r.status === 401) { logout(); } return r.json(); })
    .then(data => {
        // Store user info
        localStorage.setItem('kenakata_user', JSON.stringify(data));

        document.getElementById('profile-name').textContent   = data.EmployeeName || '—';
        document.getElementById('profile-email').textContent  = data.Email || '—';
        document.getElementById('profile-phone').textContent  = data.Phone || '—';
        document.getElementById('profile-address').textContent = data.Address || '—';
        document.getElementById('profile-id').textContent     = '#EMP-' + (data.EmployeeID || '?');

        // Edit form pre-fill
        document.getElementById('edit-name').value    = data.EmployeeName || '';
        document.getElementById('edit-phone').value   = data.Phone || '';
        document.getElementById('edit-address').value = data.Address || '';

        // Avatar initials
        const initials = (data.EmployeeName || 'E').split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 2);
        document.getElementById('avatar-initials').textContent = initials;

        // Navbar name
        const navEl = document.getElementById('nav-employee-name');
        if (navEl) navEl.textContent = '👋 ' + data.EmployeeName;
    })
    .catch(() => showAlert('Failed to load profile', 'danger'));
}

function saveProfile(e) {
    e.preventDefault();
    const btn = e.submitter || e.target.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    fetch(`${API_URL}/employee/profile`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify({
            EmployeeName: document.getElementById('edit-name').value,
            Phone: document.getElementById('edit-phone').value,
            Address: document.getElementById('edit-address').value,
        })
    })
    .then(r => r.json())
    .then(data => {
        showAlert(data.message || 'Profile updated!', 'success');
        loadProfile();
    })
    .catch(() => showAlert('Failed to update profile', 'danger'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '💾 Save Changes'; });
}

function changePassword(e) {
    e.preventDefault();
    const np  = document.getElementById('new-password').value;
    const cnp = document.getElementById('confirm-password').value;

    if (np !== cnp) { showAlert('New passwords do not match!', 'warning'); return; }
    if (np.length < 6) { showAlert('New password must be at least 6 characters', 'warning'); return; }

    const btn = e.submitter || e.target.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Changing...';

    fetch(`${API_URL}/employee/profile/change-password`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({
            current_password: document.getElementById('current-password').value,
            new_password: np,
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.message === 'Current password is incorrect') {
            showAlert('⚠️ Current password is incorrect', 'danger');
        } else {
            showAlert('✅ ' + (data.message || 'Password changed!'), 'success');
            e.target.reset();
        }
    })
    .catch(() => showAlert('Failed to change password', 'danger'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '🔑 Change Password'; });
}

function showAlert(msg, type) {
    const el = document.getElementById('profile-alert');
    el.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show shadow-sm" role="alert">
        ${msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    setTimeout(() => { el.innerHTML = ''; }, 5000);
}
</script>
@endsection
