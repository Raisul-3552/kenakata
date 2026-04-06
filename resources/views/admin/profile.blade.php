@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('admin_content')
<div class="row">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    <img id="profile-preview" src="https://via.placeholder.com/150" class="rounded-circle border" alt="Profile" style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <h4 id="display-name" class="fw-bold">Admin Name</h4>
                <p id="display-email" class="text-muted">admin@email.com</p>
                <span class="badge bg-primary px-3 py-2">Administrator</span>
            </div>
        </div>

        <!-- Assigned Employees Stats -->
        <div class="card shadow-sm border-0 bg-success text-white">
            <div class="card-body text-center">
                <h5 class="mb-0">Assigned Employees</h5>
                <h2 id="employee-count" class="display-4 fw-bold mb-0">0</h2>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div id="alert-messages"></div>
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Account Settings</h5>
            </div>
            <div class="card-body">
                <form id="updateProfileForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="profile_name" name="AdminName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="profile_email" name="Email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" class="form-control" id="profile_photo" name="Photo" accept="image/*">
                        <div class="form-text text-muted">Leave empty to keep current photo.</div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" id="profile_password" name="Password" placeholder="Minimum 6 characters">
                        <div class="form-text text-muted">Leave empty to keep current password.</div>
                    </div>
                    <button type="submit" class="btn btn-success px-4">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Assigned Employees Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Employees You've Assigned</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody id="assigned-employees-list">
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
