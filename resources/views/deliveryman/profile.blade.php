@extends('layouts.deliveryman')

@section('title', 'Rider Profile')

@section('delivery_content')
<div class="row">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <div class="rounded-circle bg-gold d-inline-flex align-items-center justify-content-center shadow-lg" style="width: 120px; height: 120px;">
                        <h1 id="initial-name" class="display-3 fw-bold text-dark mb-0">R</h1>
                    </div>
                </div>
                <h3 id="display-name" class="fw-bold text-white mb-1">Rider Name</h3>
                <p id="display-email" class="text-gold opacity-75 mb-3">rider@email.com</p>
                <div id="status-container" class="badge rounded-pill bg-success px-4 py-2 mt-2">Active Rider</div>
            </div>
        </div>

        <!-- Performance Stats -->
        <div class="card shadow-sm border-0 bg-gold text-dark mb-4">
            <div class="card-body text-center py-4">
                <p class="text-uppercase small fw-bold mb-1 opacity-75">Lifetime Deliveries</p>
                <h1 id="lifetime-count" class="display-3 fw-bold mb-0">0</h1>
                <p class="small mb-0">Orders Delivered</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div id="alert-messages"></div>
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom border-light py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>Account Settings</h5>
            </div>
            <div class="card-body py-4">
                <form id="updateProfileForm">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-gold small fw-bold">Full Name</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white py-2" id="profile_name" name="DelManName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-gold small fw-bold">Email Address</label>
                            <input type="email" class="form-control bg-dark border-secondary text-white py-2" id="profile_email" name="Email" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-gold small fw-bold text-uppercase">Security</label>
                        <div class="p-3 rounded bg-dark border border-secondary">
                            <div class="mb-0">
                                <label class="form-label small">New Password</label>
                                <input type="password" class="form-control bg-dark border-secondary text-white" id="profile_password" name="Password" placeholder="Minimum 6 characters">
                                <div class="form-text text-light opacity-50 small mt-1">Leave empty if you don't want to change your password.</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid shadow-lg">
                        <button type="submit" class="btn btn-gold py-3 fw-bold text-uppercase">
                            <i class="bi bi-check-circle-fill me-2"></i>Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                <p class="mb-0 opacity-75">Registered since: <span id="join-date" class="fw-bold">N/A</span></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadProfileData();
    });

    function loadProfileData() {
        const alertDiv = document.getElementById('alert-messages');

        fetch(`${API_URL}/deliveryman/profile`, {
            headers: getHeaders()
        })
        .then(res => res.json())
        .then(data => {
            const rider = data.rider;
            const lifetimeCount = data.lifetime_deliveries;

            // Fill form
            document.getElementById('profile_name').value = rider.DelManName;
            document.getElementById('profile_email').value = rider.Email;
            
            // Fill display
            document.getElementById('display-name').textContent = rider.DelManName;
            document.getElementById('display-email').textContent = rider.Email;
            document.getElementById('initial-name').textContent = rider.DelManName.charAt(0).toUpperCase();
            document.getElementById('lifetime-count').textContent = lifetimeCount;
            
            // Status badge logic
            const statusContainer = document.getElementById('status-container');
            if(rider.Status === 'Available') {
                statusContainer.className = 'badge rounded-pill bg-success px-4 py-2 mt-2';
                statusContainer.textContent = 'Available for Task';
            } else {
                statusContainer.className = 'badge rounded-pill bg-warning text-dark px-4 py-2 mt-2';
                statusContainer.textContent = 'Currently Busy';
            }
        })
        .catch(err => {
            alertDiv.innerHTML = `<div class="alert alert-danger bg-danger border-0 text-white">Failed to load profile data.</div>`;
        });
    }

    document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const alertDiv = document.getElementById('alert-messages');
        
        const payload = {
            DelManName: document.getElementById('profile_name').value,
            Email: document.getElementById('profile_email').value,
        };
        
        const password = document.getElementById('profile_password').value;
        if(password) {
            payload.Password = password;
        }

        fetch(`${API_URL}/deliveryman/profile/update`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(payload)
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if(res.status === 200) {
                alertDiv.innerHTML = `<div class="alert alert-success bg-success border-0 text-white">✅ ${res.body.message}</div>`;
                loadProfileData(); // Reload text
                document.getElementById('profile_password').value = ''; // Clear password field
            } else {
                const errors = res.body.errors ? Object.values(res.body.errors).flat().join('<br>') : res.body.message;
                alertDiv.innerHTML = `<div class="alert alert-danger bg-danger border-0 text-white">${errors}</div>`;
            }
        })
        .catch(err => {
            alertDiv.innerHTML = `<div class="alert alert-danger bg-danger border-0 text-white">Network error.</div>`;
        });
    });
</script>
@endsection
