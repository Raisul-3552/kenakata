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

        #addEmployeeCollapse {
            transition: all 0.3s ease-out;
        }
        .search-results-container {
            display: none;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            border-left: 5px solid #1f3bb3;
            overflow: hidden;
        }
        .search-result-item {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }
        .search-result-item:last-child {
            border-bottom: none;
        }
        .search-result-item:hover {
            background-color: #f8fbff;
        }
        .search-result-name {
            font-weight: 600;
            color: #1f3bb3;
        }
        .search-result-email {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .search-result-badge {
            font-size: 0.7rem;
            background: #eef2ff;
            color: #1f3bb3;
            padding: 2px 8px;
            border-radius: 5px;
            font-weight: 700;
            text-transform: uppercase;
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
    <div class="collapse mb-5" id="addEmployeeCollapse">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-navy text-white py-3">
                <h5 class="mb-0 fw-bold">➕ Create New Employee Profile</h5>
            </div>
            <div class="card-body p-4">
                <div id="alert-messages-top"></div>
                <form id="addEmployeeForm">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-navy">Employee Name</label>
                            <input type="text" class="form-control" id="emp_name" placeholder="Type customer name to search..." autocomplete="off" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-navy">Employee Email</label>
                            <input type="email" class="form-control" id="emp_email" placeholder="employee@example.com" required>
                            <div class="form-text mt-2 text-muted">Default password for new employees is "password".</div>
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

    <!-- Search Results Section (Shown only when searching) -->
    <div id="search-results-section" class="search-results-container">
        <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
            <span class="text-navy fw-bold small"><i class="bi bi-person-search me-1"></i> Matching Customers</span>
            <button type="button" class="btn-close small" style="font-size: 0.7rem;" onclick="document.getElementById('search-results-section').style.display='none'"></button>
        </div>
        <div id="search-results-list"></div>
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
    <script>
        let employeesData = [];
        let selectedCustomerData = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadEmployees();
            setupNameSearch();
        });

        function setupNameSearch() {
            const nameInput = document.getElementById('emp_name');
            const resultsSection = document.getElementById('search-results-section');
            const resultsList = document.getElementById('search-results-list');

            nameInput.addEventListener('input', function() {
                const query = this.value.trim();
                if (query.length < 2) {
                    resultsSection.style.display = 'none';
                    return;
                }

                fetch(`${API_URL}/admin/customers/search?q=${encodeURIComponent(query)}`, {
                    headers: getHeaders()
                })
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        resultsList.innerHTML = data.map(customer => `
                            <div class="search-result-item" onclick="selectCustomer(${JSON.stringify(customer).replace(/"/g, '&quot;')})">
                                <div>
                                    <div class="search-result-name">${customer.CustomerName}</div>
                                    <div class="search-result-email">${customer.Email}</div>
                                </div>
                                <span class="search-result-badge">Select Customer</span>
                            </div>
                        `).join('');
                        resultsSection.style.display = 'block';
                    } else {
                        resultsSection.style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error('Search error:', err);
                });
            });
        }

        function selectCustomer(customer) {
            document.getElementById('emp_name').value = customer.CustomerName;
            document.getElementById('emp_email').value = customer.Email;
            
            // Store hidden data
            selectedCustomerData = {
                Phone: customer.Phone,
                Address: customer.Address
            };
            
            document.getElementById('search-results-section').style.display = 'none';
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
            const alertDiv = document.getElementById('alert-messages-top');

            const payload = {
                EmployeeName: document.getElementById('emp_name').value,
                Email: document.getElementById('emp_email').value,
            };

            // Add background data if a customer was selected
            if (selectedCustomerData) {
                payload.Phone = selectedCustomerData.Phone;
                payload.Address = selectedCustomerData.Address;
            }

            fetch(`${API_URL}/admin/employees`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(payload)
            })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    if (res.status === 201) {
                        alertDiv.innerHTML = `<div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>✅ Employee <strong>${payload.EmployeeName}</strong> added successfully!</div>`;
                        document.getElementById('addEmployeeForm').reset();
                        selectedCustomerData = null; // Clear selection
                        loadEmployees();
                    } else {
                        const errors = res.body.errors ? Object.values(res.body.errors).flat().join('<br>') : res.body.message;
                        alertDiv.innerHTML = `<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>${errors}</div>`;
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
                    alertDiv.innerHTML = `<div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>✅ Employee <strong>${name}</strong> has been successfully removed.</div>`;
                    loadEmployees();
                })
                .catch(err => {
                    alertDiv.innerHTML = `<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>Error deleting employee.</div>`;
                });
        }
    </script>
@endsection