@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('admin_content')
<div class="row g-4">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="position-relative d-inline-block mb-4">
                    <div class="rounded-circle overflow-hidden border border-4 border-light shadow-sm" style="width: 150px; height: 150px; margin: 0 auto;">
                        <img id="profile-preview" src="https://via.placeholder.com/150" class="w-100 h-100" alt="Profile" style="object-fit: cover;">
                    </div>
                </div>
                <h3 id="display-name" class="fw-bold text-navy mb-1">Admin Name</h3>
                <p id="display-email" class="text-muted mb-3">admin@email.com</p>
                <div class="d-inline-block bg-navy bg-opacity-10 text-navy px-4 py-2 rounded-pill fw-bold small">
                    <i class="bi bi-shield-check me-2"></i>Administrator
                </div>
            </div>
        </div>

        <!-- Assigned Employees Stats -->
        <div class="card border-0 shadow-sm bg-navy text-white text-center py-4">
            <div class="card-body">
                <p class="text-uppercase small fw-bold mb-2 opacity-75">Employees Assigned</p>
                <h1 id="employee-count" class="display-4 fw-bold mb-0">0</h1>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div id="alert-messages"></div>
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom border-light py-3">
                <h5 class="mb-0 fw-bold text-navy"><i class="bi bi-person-gear me-2"></i>Account Management</h5>
            </div>
            <div class="card-body p-4">
                <form id="updateProfileForm">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-navy small fw-bold">Display Name</label>
                            <input type="text" class="form-control" id="profile_name" name="AdminName" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-navy small fw-bold">Email Address</label>
                            <input type="email" class="form-control" id="profile_email" name="Email" placeholder="admin@example.com" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-navy small fw-bold">Update Profile Photo</label>
                        <input type="file" class="form-control" id="profile_photo" name="Photo" accept="image/*">
                        <div class="form-text mt-2">Select a clean, professional image (JPG/PNG).</div>
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label text-navy small fw-bold text-uppercase border-bottom pb-2 d-block mb-3">Login Security</label>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-muted">New Password</label>
                                <input type="password" class="form-control" id="profile_password" name="Password" placeholder="Enter new password (min. 6 characters)">
                                <div class="form-text">Leave blank to keep your current password.</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid shadow-sm">
                        <button type="submit" class="btn btn-navy py-3 fw-bold text-uppercase">
                            <i class="bi bi-cloud-arrow-up me-2"></i> Update Admin Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Assigned Employees Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom border-light py-3">
                <h5 class="mb-0 fw-bold text-navy">Recently Assigned Personnel</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="pe-4">Contact</th>
                            </tr>
                        </thead>
                        <tbody id="assigned-employees-list" class="small">
                            <!-- JS content -->
                        </tbody>
                    </table>
                </div>
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

        fetch(`${API_URL}/admin/profile`, {
            headers: getHeaders()
        })
        .then(res => res.json())
        .then(data => {
            const admin = data.admin;
            const employees = data.employees;

            // Fill form
            document.getElementById('profile_name').value = admin.AdminName;
            document.getElementById('profile_email').value = admin.Email;
            
            // Fill display
            document.getElementById('display-name').textContent = admin.AdminName;
            document.getElementById('display-email').textContent = admin.Email;
            
            if(admin.Photo) {
                document.getElementById('profile-preview').src = admin.Photo;
            }

            // Fill employees
            document.getElementById('employee-count').textContent = employees.length;
            const tbody = document.getElementById('assigned-employees-list');
            
            if(employees.length > 0) {
                tbody.innerHTML = employees.map(emp => `
                    <tr>
                        <td><strong>#${emp.EmployeeID}</strong></td>
                        <td>${emp.EmployeeName}</td>
                        <td>${emp.Email}</td>
                        <td>${emp.Phone || 'N/A'}</td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No employees assigned yet.</td></tr>';
            }
        })
        .catch(err => {
            alertDiv.innerHTML = `<div class="alert alert-danger">Failed to load profile data.</div>`;
        });
    }

    document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const alertDiv = document.getElementById('alert-messages');
        const formData = new FormData(this);

        // API call
        fetch(`${API_URL}/admin/profile/update`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('kenakata_token')}`,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if(res.status === 200) {
                alertDiv.innerHTML = `<div class="alert alert-success">✅ ${res.body.message}</div>`;
                loadProfileData(); // Reload to update photo and text
                this.reset();
            } else {
                const errors = res.body.errors ? Object.values(res.body.errors).flat().join('<br>') : res.body.message;
                alertDiv.innerHTML = `<div class="alert alert-danger">${errors}</div>`;
            }
        })
        .catch(err => {
            alertDiv.innerHTML = `<div class="alert alert-danger">Network error.</div>`;
        });
    });
</script>
@endsection
