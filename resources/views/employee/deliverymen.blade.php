@extends('layouts.employee')

@section('title', 'Deliveryman Management')

@section('employee_styles')
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

        #addDeliveryManCollapse {
            transition: all 0.3s ease-out;
        }
    </style>
@endsection

@section('employee_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-white mb-0">🚴 Deliveryman Management</h3>
        <button class="btn btn-success fw-bold px-4 shadow-sm" type="button" data-bs-toggle="collapse"
            data-bs-target="#addDeliveryManCollapse" aria-expanded="false">
            <i class="bi bi-plus-circle me-2"></i> Add New Deliveryman
        </button>
    </div>

    <div class="collapse mb-4" id="addDeliveryManCollapse">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-success text-white py-3">
                <h5 class="mb-0 fw-bold">➕ Create New Deliveryman Profile</h5>
            </div>
            <div class="card-body p-4">
                <div id="alert-messages-top"></div>
                <form id="addDeliveryManForm">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-success">Deliveryman Name</label>
                            <input type="text" class="form-control" id="del_name" placeholder="e.g. Rahim Uddin" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-success">Phone</label>
                            <input type="text" class="form-control" id="del_phone" placeholder="e.g. 01XXXXXXXXX" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-success">Email</label>
                            <input type="email" class="form-control" id="del_email" placeholder="rider@example.com" required>
                            <div class="form-text mt-2 text-muted">Default password is "password".</div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-success">Address</label>
                            <input type="text" class="form-control" id="del_address" placeholder="Full address" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-success">Status</label>
                            <select id="del_status" class="form-select">
                                <option value="Available" selected>Available</option>
                                <option value="Busy">Busy</option>
                            </select>
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

    <div class="card border-0 bg-transparent shadow-none">
        <div class="card-body p-0">
            <div id="alert-messages"></div>
            <div class="table-responsive">
                <table class="table table-professional align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="deliveryman-list">
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="spinner-border text-success"></div>
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
            loadDeliveryMen();
        });

        function loadDeliveryMen() {
            const tbody = document.getElementById('deliveryman-list');
            const alertDiv = document.getElementById('alert-messages');

            fetch(`${API_URL}/employee/deliverymen`, {
                headers: getHeaders()
            })
                .then(res => {
                    if (res.status === 401 || res.status === 403) logout();
                    return res.json();
                })
                .then(data => {
                    const deliveryMen = data.deliverymen || data;
                    if (deliveryMen && deliveryMen.length > 0) {
                        tbody.innerHTML = deliveryMen.map(del => `
                            <tr>
                                <td class="ps-4">${del.DelManID}</td>
                                <td>${del.DelManName}</td>
                                <td>${del.Phone}</td>
                                <td>${del.Email}</td>
                                <td>${del.Address || ''}</td>
                                <td>
                                    <span class="badge ${del.Status === 'Available' ? 'bg-success' : 'bg-warning text-dark'}">${del.Status || 'Available'}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-danger" onclick='deleteDeliveryMan(${del.DelManID}, ${JSON.stringify(del.DelManName)})'>🗑️ Delete</button>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No deliverymen found. Add one above!</td></tr>';
                    }
                })
                .catch(() => {
                    alertDiv.innerHTML = '<div class="alert alert-danger">Error loading deliverymen.</div>';
                });
        }

        document.getElementById('addDeliveryManForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const alertDiv = document.getElementById('alert-messages-top');

            const payload = {
                DelManName: document.getElementById('del_name').value,
                Phone: document.getElementById('del_phone').value,
                Email: document.getElementById('del_email').value,
                Address: document.getElementById('del_address').value,
                Status: document.getElementById('del_status').value,
            };

            fetch(`${API_URL}/employee/deliverymen`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(payload)
            })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    if (res.status === 201) {
                        alertDiv.innerHTML = `<div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>✅ Deliveryman <strong>${payload.DelManName}</strong> added successfully! Default password is 'password'.</div>`;
                        document.getElementById('addDeliveryManForm').reset();
                        document.getElementById('del_status').value = 'Available';
                        loadDeliveryMen();
                    } else {
                        const errors = res.body.errors ? Object.values(res.body.errors).flat().join('<br>') : (res.body.message || 'Failed to create deliveryman.');
                        alertDiv.innerHTML = `<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>${errors}</div>`;
                    }
                })
                .catch(() => {
                    alertDiv.innerHTML = '<div class="alert alert-danger">Network error.</div>';
                });
        });

        function deleteDeliveryMan(id, name) {
            if (!confirm(`Are you sure you want to delete deliveryman '${name}'?`)) return;
            const alertDiv = document.getElementById('alert-messages');

            fetch(`${API_URL}/employee/deliverymen/${id}`, {
                method: 'DELETE',
                headers: getHeaders()
            })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    if (res.status === 200) {
                        alertDiv.innerHTML = `<div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>✅ Deliveryman <strong>${name}</strong> has been successfully removed.</div>`;
                        loadDeliveryMen();
                    } else {
                        alertDiv.innerHTML = `<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>${res.body.message || 'Error deleting deliveryman.'}</div>`;
                    }
                })
                .catch(() => {
                    alertDiv.innerHTML = '<div class="alert alert-danger">Error deleting deliveryman.</div>';
                });
        }
    </script>
@endsection
