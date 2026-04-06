@extends('layouts.deliveryman')

@section('title', 'Rider Profile')

@section('delivery_styles')
<style>
    .profile-avatar {
        width: 96px; height: 96px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; font-weight: 700; color: #1f2937;
        flex-shrink: 0;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.25);
    }
    .section-card {
        background: rgba(22, 33, 62, 0.85) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        border-radius: 16px;
        color: #fff;
    }
    .section-title {
        font-size: 1rem; font-weight: 700; letter-spacing: 0.04em;
        color: #fbbf24; text-transform: uppercase;
        border-bottom: 1px solid rgba(245, 158, 11, 0.25);
        padding-bottom: 0.5rem; margin-bottom: 1.25rem;
    }
    .info-row { display: flex; gap: 0.5rem; align-items: start; margin-bottom: 0.9rem; }
    .info-label { min-width: 120px; color: rgba(255,255,255,0.5); font-size: 0.82rem; padding-top: 0.35rem; }
    .info-value { color: #fff; font-weight: 500; flex: 1; }
    .badge-role {
        background: linear-gradient(135deg,#0f3460,#f59e0b);
        font-size: 0.75rem; padding: 0.3em 0.75em; border-radius: 20px;
    }
</style>
@endsection

@section('delivery_content')
<div id="profile-alert"></div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card section-card p-4 h-100">
            <div class="d-flex gap-3 align-items-center mb-4">
                <div class="profile-avatar" id="avatar-initials">R</div>
                <div>
                    <h5 class="mb-1 fw-bold" id="profile-name">Loading...</h5>
                    <span class="badge badge-role text-white">🚴 Deliveryman</span>
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
                <span class="info-label">🆔 Rider ID</span>
                <span class="info-value" id="profile-id">—</span>
            </div>
            <div class="info-row mb-3">
                <span class="info-label">📊 Status</span>
                <span class="info-value" id="status-text">—</span>
            </div>
            <div class="d-grid gap-2">
                <button type="button" id="toggle-status-btn" class="btn btn-outline-warning" onclick="toggleStatus()">⟳ Toggle Status</button>
            </div>
            <hr class="border-light border-opacity-25">
            <div>
                <div class="text-white-50 small mb-1">Lifetime Deliveries</div>
                <div id="lifetime-count" class="h3 fw-bold text-warning mb-0">0</div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card section-card p-4 mb-4">
            <div class="section-title">✏️ Edit Profile</div>
            <form id="edit-profile-form">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Full Name</label>
                        <input type="text" class="form-control" id="edit-name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Phone</label>
                        <input type="text" class="form-control" id="edit-phone" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Address</label>
                        <input type="text" class="form-control" id="edit-address" required>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-warning text-dark fw-bold px-4">💾 Save Changes</button>
                    <button type="button" class="btn btn-outline-light px-4" onclick="loadProfile()">↺ Reset</button>
                </div>
            </form>
        </div>

        <div class="card section-card p-4">
            <div class="section-title">🔒 Change Password</div>
            <form id="change-password-form">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-white-50 small">Current Password</label>
                        <input type="password" class="form-control" id="current-password" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">New Password</label>
                        <input type="password" class="form-control" id="new-password" placeholder="Min. 6 characters" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm-password" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-outline-warning px-4 fw-bold">🔑 Change Password</button>
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
        fetch(`${API_URL}/deliveryman/profile`, {
            headers: getHeaders()
        })
        .then(res => {
            if (res.status === 401 || res.status === 403) logout();
            return res.json();
        })
        .then(data => {
            const rider = data.rider;
            const lifetimeCount = data.lifetime_deliveries;

            localStorage.setItem('kenakata_user', JSON.stringify(rider));

            document.getElementById('profile-name').textContent = rider.DelManName || '—';
            document.getElementById('profile-email').textContent = rider.Email || '—';
            document.getElementById('profile-phone').textContent = rider.Phone || '—';
            document.getElementById('profile-address').textContent = rider.Address || '—';
            document.getElementById('profile-id').textContent = '#DEL-' + (rider.DelManID || '?');
            document.getElementById('status-text').textContent = rider.Status || 'Available';
            document.getElementById('lifetime-count').textContent = lifetimeCount || 0;

            document.getElementById('edit-name').value = rider.DelManName || '';
            document.getElementById('edit-phone').value = rider.Phone || '';
            document.getElementById('edit-address').value = rider.Address || '';

            const initials = (rider.DelManName || 'R').split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 2);
            document.getElementById('avatar-initials').textContent = initials;

            const btn = document.getElementById('toggle-status-btn');
            if (rider.Status === 'Available') {
                btn.className = 'btn btn-outline-warning';
                btn.textContent = 'Set Busy';
            } else {
                btn.className = 'btn btn-outline-success';
                btn.textContent = 'Set Available';
            }
        })
        .catch(() => showAlert('Failed to load profile data.', 'danger'));
    }

    function saveProfile(e) {
        e.preventDefault();
        const btn = e.submitter || e.target.querySelector('[type=submit]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const payload = {
            DelManName: document.getElementById('edit-name').value,
            Phone: document.getElementById('edit-phone').value,
            Address: document.getElementById('edit-address').value,
        };

        fetch(`${API_URL}/deliveryman/profile`, {
            method: 'PUT',
            headers: getHeaders(),
            body: JSON.stringify(payload)
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status === 200) {
                showAlert('✅ ' + (res.body.message || 'Profile updated successfully'), 'success');
                loadProfile();
            } else {
                const errors = res.body.errors ? Object.values(res.body.errors).flat().join('<br>') : res.body.message;
                showAlert(errors || 'Failed to update profile', 'danger');
            }
        })
        .catch(() => showAlert('Network error.', 'danger'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '💾 Save Changes';
        });
    }

    function changePassword(e) {
        e.preventDefault();

        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        if (newPassword !== confirmPassword) {
            showAlert('New passwords do not match!', 'warning');
            return;
        }
        if (newPassword.length < 6) {
            showAlert('New password must be at least 6 characters', 'warning');
            return;
        }

        const btn = e.submitter || e.target.querySelector('[type=submit]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Changing...';

        fetch(`${API_URL}/deliveryman/profile/change-password`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                current_password: document.getElementById('current-password').value,
                new_password: newPassword,
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status === 422) {
                showAlert('⚠️ ' + (res.body.message || 'Current password is incorrect'), 'danger');
            } else if (res.status === 200) {
                showAlert('✅ ' + (res.body.message || 'Password changed successfully'), 'success');
                e.target.reset();
            } else {
                showAlert('Failed to change password', 'danger');
            }
        })
        .catch(() => showAlert('Failed to change password', 'danger'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '🔑 Change Password';
        });
    }

    function toggleStatus() {
        const btn = document.getElementById('toggle-status-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

        fetch(`${API_URL}/deliveryman/profile/toggle-status`, {
            method: 'POST',
            headers: getHeaders(),
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status === 200) {
                showAlert('✅ ' + (res.body.message || 'Status updated successfully'), 'success');
                loadProfile();
            } else {
                showAlert(res.body.message || 'Failed to update status', 'danger');
            }
        })
        .catch(() => showAlert('Failed to update status', 'danger'))
        .finally(() => {
            btn.disabled = false;
        });
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
