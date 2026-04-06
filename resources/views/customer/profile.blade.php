@extends('layouts.customer')

@section('title', 'My Profile')

@section('customer_styles')
<style>
    .profile-avatar {
        width: 96px; height: 96px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; font-weight: 700; color: #fff;
        flex-shrink: 0;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.25);
    }
    .section-card {
        background: var(--bg-surface) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 16px;
    }
    .section-title {
        font-size: 0.85rem; font-weight: 700; letter-spacing: 0.06em;
        color: var(--accent-cyan); text-transform: uppercase;
        border-bottom: 1px solid rgba(14, 165, 233, 0.2);
        padding-bottom: 0.5rem; margin-bottom: 1.25rem;
    }
    .info-row { display: flex; gap: 0.5rem; align-items: start; margin-bottom: 0.9rem; }
    .info-label { min-width: 110px; color: rgba(255,255,255,0.45); font-size: 0.82rem; padding-top: 0.35rem; }
    .info-value { color: #fff; font-weight: 500; flex: 1; }
    .badge-role {
        background: linear-gradient(135deg, #0f3460, #0ea5e9);
        font-size: 0.75rem; padding: 0.3em 0.75em; border-radius: 20px;
    }
    .form-control {
        background-color: #020617 !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-primary) !important;
    }
    .form-control:focus {
        border-color: var(--accent-cyan) !important;
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2) !important;
    }
    .form-control[readonly] {
        opacity: 0.55;
        cursor: not-allowed;
    }
</style>
@endsection

@section('customer_content')
<div class="container">
    <div class="row g-4">
        {{-- Left: Info card --}}
        <div class="col-lg-4">
            <div class="section-card p-4 h-100">
                <div class="d-flex gap-3 align-items-center mb-4">
                    <div class="profile-avatar" id="avatar-initials">?</div>
                    <div>
                        <h5 class="mb-1 fw-bold text-white" id="profile-name">Loading...</h5>
                        <span class="badge badge-role text-white">🛒 Customer</span>
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
                    <span class="info-label">🆔 Customer ID</span>
                    <span class="info-value" id="profile-id">—</span>
                </div>
                <div class="mt-3">
                    <a href="/customer/orders" class="btn btn-outline-info btn-sm w-100">
                        📦 View My Orders
                    </a>
                </div>
            </div>
        </div>

        {{-- Right: Edit form + Password --}}
        <div class="col-lg-8">
            {{-- Edit Profile --}}
            <div class="section-card p-4 mb-4">
                <div class="section-title">✏️ Edit Profile</div>
                <form id="edit-profile-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Full Name</label>
                            <input type="text" class="form-control" id="edit-name" placeholder="Your full name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Email Address <span class="text-danger small">(not editable)</span></label>
                            <input type="email" class="form-control" id="edit-email" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Phone Number</label>
                            <input type="text" class="form-control" id="edit-phone" placeholder="e.g. +8801XXXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Address</label>
                            <input type="text" class="form-control" id="edit-address" placeholder="Your address">
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-cyan px-4">💾 Save Changes</button>
                        <button type="button" class="btn btn-outline-secondary px-4" onclick="loadProfile()">↺ Reset</button>
                    </div>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="section-card p-4">
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
</div>
@endsection

@section('customer_scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    loadProfile();
    document.getElementById('edit-profile-form').addEventListener('submit', saveProfile);
    document.getElementById('change-password-form').addEventListener('submit', changePassword);
});

function loadProfile() {
    fetch(`${API_URL}/customer/profile`, { headers: getHeaders() })
    .then(r => { if (r.status === 401) { logout(); } return r.json(); })
    .then(data => {
        document.getElementById('profile-name').textContent    = data.CustomerName || '—';
        document.getElementById('profile-email').textContent   = data.Email || '—';
        document.getElementById('profile-phone').textContent   = data.Phone || '—';
        document.getElementById('profile-address').textContent = data.Address || '—';
        document.getElementById('profile-id').textContent      = '#CUST-' + (data.CustomerID || '?');

        // Edit form pre-fill
        document.getElementById('edit-name').value    = data.CustomerName || '';
        document.getElementById('edit-email').value   = data.Email || '';
        document.getElementById('edit-phone').value   = data.Phone || '';
        document.getElementById('edit-address').value = data.Address || '';

        // Avatar initials
        const initials = (data.CustomerName || 'C').split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 2);
        document.getElementById('avatar-initials').textContent = initials;
    })
    .catch(() => showToast('Failed to load profile', 'error'));
}

function saveProfile(e) {
    e.preventDefault();
    const btn = e.submitter || e.target.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    fetch(`${API_URL}/customer/profile`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify({
            CustomerName: document.getElementById('edit-name').value,
            Phone:        document.getElementById('edit-phone').value,
            Address:      document.getElementById('edit-address').value,
        })
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message || 'Profile updated!', 'success');
        loadProfile();
    })
    .catch(() => showToast('Failed to update profile', 'error'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '💾 Save Changes'; });
}

function changePassword(e) {
    e.preventDefault();
    const np  = document.getElementById('new-password').value;
    const cnp = document.getElementById('confirm-password').value;

    if (np !== cnp) { showToast('New passwords do not match!', 'error'); return; }
    if (np.length < 6) { showToast('New password must be at least 6 characters', 'error'); return; }

    const btn = e.submitter || e.target.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Changing...';

    fetch(`${API_URL}/customer/profile/change-password`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({
            current_password: document.getElementById('current-password').value,
            new_password:     np,
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.message === 'Current password is incorrect') {
            showToast('Current password is incorrect', 'error');
        } else {
            showToast(data.message || 'Password changed!', 'success');
            e.target.reset();
            // Reload email field after reset
            loadProfile();
        }
    })
    .catch(() => showToast('Failed to change password', 'error'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '🔑 Change Password'; });
}
</script>
@endsection
