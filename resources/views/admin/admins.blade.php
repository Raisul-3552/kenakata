@extends('layouts.admin')

@section('admin_styles')
    <style>
        .table-professional {
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .table-professional thead th {
            background: rgba(15, 52, 96, 0.95) !important;
            color: #fff !important;
            border: none !important;
            padding: 15px !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .table-professional tbody tr {
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            transition: all 0.3s;
        }
        .table-professional tbody tr:hover {
            transform: scale(1.005);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        .table-professional td {
            padding: 15px !important;
            border: none !important;
            vertical-align: middle;
            color: #1f2937 !important;
        }
        .table-professional tr td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        .table-professional tr td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        #addAdminCollapse {
            transition: all 0.3s ease-out;
        }
    </style>
@endsection

@section('admin_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-navy mb-0">🛡️ Admin Management</h3>
        <button class="btn btn-navy fw-bold px-4 shadow-sm" type="button" data-bs-toggle="collapse"
            data-bs-target="#addAdminCollapse" aria-expanded="false">
            <i class="bi bi-plus-circle me-2"></i> Add New Admin
        </button>
    </div>

    <!-- Add Admin Form (Toggleable) -->
    <div class="collapse mb-4" id="addAdminCollapse">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-navy text-white py-3">
                <h5 class="mb-0 fw-bold">➕ Create New Admin Profile</h5>
            </div>
            <div class="card-body p-4">
                <div id="alert-messages-top"></div>
                <form id="addAdminForm">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-navy">Admin Name</label>
                            <input type="text" class="form-control" id="adm_name" placeholder="e.g. John Doe" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-navy">Admin Email</label>
                            <input type="email" class="form-control" id="adm_email" placeholder="admin@example.com" required>
                            <div class="form-text mt-2 text-muted">Default password will be "password".</div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Register
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Admin Table -->
    <div class="card border-0 bg-transparent shadow-none">
        <div class="card-body p-0">
            <div id="alert-messages"></div>
            <div class="table-responsive">
                <table class="table table-professional align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="admin-list">
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="spinner-border text-navy"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadAdmins();
        });

        function loadAdmins() {
            const tbody = document.getElementById('admin-list');
            const alertDiv = document.getElementById('alert-messages');

            fetch(`${API_URL}/admin/all-admins`, {
                headers: getHeaders()
            })
                .then(res => {
                    if (res.status === 401 || res.status === 403) logout();
                    return res.json();
                })
                .then(data => {
                    const admins = data;
                    if (admins && admins.length > 0) {
                        tbody.innerHTML = admins.map(adm => `
                            <tr>
                                <td class="ps-4">${adm.AdminID}</td>
                                <td>${adm.AdminName}</td>
                                <td>${adm.Email}</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-danger" onclick="deleteAdmin(${adm.AdminID}, '${adm.AdminName}')">🗑️ Delete</button>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No admins found. Add one above!</td></tr>';
                    }
                })
                .catch(err => {
                    alertDiv.innerHTML = `<div class="alert alert-danger">Error loading admins.</div>`;
                });
        }

        // Add Admin
        document.getElementById('addAdminForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const alertDiv = document.getElementById('alert-messages-top');

            const payload = {
                AdminName: document.getElementById('adm_name').value,
                Email: document.getElementById('adm_email').value,
            };

            fetch(`${API_URL}/admin/all-admins`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(payload)
            })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    if (res.status === 201) {
                        alertDiv.innerHTML = `<div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>✅ Admin <strong>${payload.AdminName}</strong> added successfully! Default password is 'password'.</div>`;
                        document.getElementById('addAdminForm').reset();
                        loadAdmins();
                    } else {
                        const errors = res.body.errors ? Object.values(res.body.errors).flat().join('<br>') : res.body.message;
                        alertDiv.innerHTML = `<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>${errors}</div>`;
                    }
                })
                .catch(err => {
                    alertDiv.innerHTML = `<div class="alert alert-danger">Network error.</div>`;
                });
        });

        // Delete Admin
        function deleteAdmin(id, name) {
            if (!confirm(`Are you sure you want to delete admin '${name}'?`)) return;
            const alertDiv = document.getElementById('alert-messages');

            fetch(`${API_URL}/admin/all-admins/${id}`, {
                method: 'DELETE',
                headers: getHeaders()
            })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    if(res.status === 200) {
                        alertDiv.innerHTML = `<div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>✅ Admin <strong>${name}</strong> has been successfully removed.</div>`;
                        loadAdmins();
                    } else {
                        alertDiv.innerHTML = `<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>${res.body.message || 'Error deleting admin.'}</div>`;
                    }
                })
                .catch(err => {
                    alertDiv.innerHTML = `<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>Error deleting admin.</div>`;
                });
        }
    </script>
@endsection
