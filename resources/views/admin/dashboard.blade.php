@extends('layouts.admin')

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
                    <input type="text" class="form-control" id="emp_name" placeholder="Employee name" required>
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadEmployees();
    });

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
            if(employees && employees.length > 0) {
                tbody.innerHTML = employees.map(emp => `
                    <tr>
                        <td>${emp.EmployeeID}</td>
                        <td>${emp.EmployeeName}</td>
                        <td>${emp.Email}</td>
                        <td>${emp.Phone}</td>
                        <td>${emp.Address || ''}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="deleteEmployee(${emp.EmployeeID})">🗑️ Delete</button>
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
                alertDiv.innerHTML = `<div class="alert alert-success">✅ Employee added successfully!</div>`;
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
    function deleteEmployee(id) {
        if(!confirm('Are you sure you want to delete this employee?')) return;
        const alertDiv = document.getElementById('alert-messages');

        fetch(`${API_URL}/admin/employees/${id}`, {
            method: 'DELETE',
            headers: getHeaders()
        })
        .then(res => res.json())
        .then(data => {
            alertDiv.innerHTML = `<div class="alert alert-success">✅ ${data.message}</div>`;
            loadEmployees();
        })
        .catch(err => {
            alertDiv.innerHTML = `<div class="alert alert-danger">Error deleting employee.</div>`;
        });
    }
</script>
@endsection
