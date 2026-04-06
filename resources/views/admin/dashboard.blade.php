@extends('layouts.admin')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #dee2e6 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>
@endsection

@section('admin_content')
<!-- Add Employee Form -->
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <strong>➕ Add New Employee</strong>
    </div>
    <div class="card-body">
        <form id="addEmployeeForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Full Name</label>
                    <select class="form-control" id="emp_name" required>
                        <option value="">Search customer name...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="emp_email" placeholder="employee@email.com" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">Add Employee</button>
                </div>
                <div class="col-12">
                    <small class="text-muted">📌 Default password for new employees: <strong>password</strong></small>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Employee Table -->
<div class="card">
    <div class="card-header bg-success text-white">
        <strong>👔 Manage Employees</strong>
    </div>
    <div class="card-body">
        <div id="alert-messages"></div>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="employee-list">
            </tbody>
        </table>
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
            if(res.status === 401 || res.status === 403) logout();
            return res.json();
        })
        .then(data => {
            const employees = data.employees || data;
            employeesData = employees; // Store for lookup
            if(employees && employees.length > 0) {
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
    document.getElementById('addEmployeeForm').addEventListener('submit', function(e) {
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
            if(res.status === 201) {
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
        if(!confirm(`Are you sure you want to delete employee '${name}'?`)) return;
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
