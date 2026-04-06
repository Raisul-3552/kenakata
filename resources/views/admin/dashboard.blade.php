@extends('layouts.admin')
@section('admin_styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            border: 1px solid #c4cfd9ff !important;
            height: 42px !important;
            border-radius: 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            padding-left: 12px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        .table-professional {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table-professional thead th {
            background-color: #001f3f !important;
            color: #fff !important;
            border: none !important;
            padding: 15px !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table-professional tbody tr {
            background-color: #797fc0ff !important;
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
        }

        .table-professional tr td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .table-professional tr td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        #addEmployeeCollapse {
            transition: all 0.3s ease-out;
        }
    </style>
@endsection

@section('admin_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-navy mb-0">👔 Employee Management</h3>
        <button class="btn btn-navy fw-bold px-4 shadow-sm" type="button" data-bs-toggle="collapse"
            data-bs-target="#addEmployeeCollapse" aria-expanded="false">
            <i class="bi bi-plus-circle me-2"></i> Add New Employee
        </button>
    </div>

    <!-- Add Employee Form (Toggleable) -->
    <div class="collapse mb-4" id="addEmployeeCollapse">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-navy text-white py-3">
                <h5 class="mb-0 fw-bold">➕ Create New Employee Profile</h5>
            </div>
            <div class="card-body p-4">
                <div id="alert-messages-top"></div>
                <form id="addEmployeeForm">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-navy">Select Registered Customer</label>
                            <select class="form-control" id="emp_name" required>
                                <option value="">Search customer name...</option>
                            </select>
                            <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Start typing to search for
                                existing customers.</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-navy">Employee Email</label>
                            <input type="email" class="form-control" id="emp_email" placeholder="email@example.com" readonly
                                required title="Auto-filled from selection">
                            <div class="form-text mt-2 text-muted">Auto-filled based on selection.</div>
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

    <!-- Employee Table -->
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
                            <th>Phone</th>
                            <th>Address</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employee-list">
                        <tr>
                            <td colspan="6" class="text-center py-5">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let employeesData = [];

        document.addEventListener('DOMContentLoaded', () => {
            loadEmployees();
            initializeSelect2();
        });

        function initializeSelect2() {
            $('#emp_name').select2({
                placeholder: 'Search customer name...',
                minimumInputLength: 1,
                ajax: {
                    url: `${API_URL}/admin/customers/search`,
                    dataType: 'json',
                    headers: getHeaders(),
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(customer => ({
                                id: customer.CustomerName,
                                text: customer.CustomerName,
                                email: customer.Email
                            }))
                        };
                    },
                    cache: true
                },
                language: {
                    noResults: function () {
                        return "no match name";
                    }
                }
            }).on('select2:select', function (e) {
                const data = e.params.data;
                document.getElementById('emp_email').value = data.email;
            });
        }

        function loadEmployees() {
            const tbody = document.getElementById('employee-list');
            const alertDiv = document.getElementById('alert-messages');

            fetch(`${API_URL}/admin/employees`, {
                headers: getHeaders()
            })
                .then(res => {
                    if (res.status === 401 || res.status === 403) logout();
                    return res.json();
                })
                .then(data => {
                    const employees = data.employees || data;
                    employeesData = employees; // Store for lookup
                    if (employees && employees.length > 0) {
                        tbody.innerHTML = employees.map(emp => `
                            <tr>
                                <td>${emp.EmployeeID}</td>
                                <td>${emp.EmployeeName}</td>
                                <td>${emp.Email}</td>
                                <td>${emp.Phone}</td>
                                <td>${emp.Address || ''}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="deleteEmployee(${emp.EmployeeID}, '${emp.EmployeeName}')">🗑️ Delete</button>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No employees found. Add one above!</td></tr>';
                    }
                })
                .catch(err => {
                    alertDiv.innerHTML = `<div class="alert alert-danger">Error loading employees.</div>`;
                });
        }

        // Add Employee
        document.getElementById('addEmployeeForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const alertDiv = document.getElementById('alert-messages');

            const payload = {
                EmployeeName: document.getElementById('emp_name').value,
                Email: document.getElementById('emp_email').value,
            };

            fetch(`${API_URL}/admin/employees`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(payload)
            })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    if (res.status === 201) {
                        alertDiv.innerHTML = `<div class="alert alert-success">✅ Employee <strong>${payload.EmployeeName}</strong> added successfully!</div>`;
                        $('#emp_name').val(null).trigger('change');
                        document.getElementById('addEmployeeForm').reset();
                        loadEmployees();
                    } else {
                        const errors = res.body.errors ? Object.values(res.body.errors).flat().join('<br>') : res.body.message;
                        alertDiv.innerHTML = `<div class="alert alert-danger">${errors}</div>`;
                    }
                })
                .catch(err => {
                    alertDiv.innerHTML = `<div class="alert alert-danger">Network error.</div>`;
                });
        });

        // Delete Employee
        function deleteEmployee(id, name) {
            if (!confirm(`Are you sure you want to delete employee '${name}'?`)) return;
            const alertDiv = document.getElementById('alert-messages');

            fetch(`${API_URL}/admin/employees/${id}`, {
                method: 'DELETE',
                headers: getHeaders()
            })
                .then(res => res.json())
                .then(data => {
                    alertDiv.innerHTML = `<div class="alert alert-success">✅ Employee <strong>${name}</strong> has been successfully removed.</div>`;
                    loadEmployees();
                })
                .catch(err => {
                    alertDiv.innerHTML = `<div class="alert alert-danger">Error deleting employee.</div>`;
                });
        }
    </script>
@endsection