@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('styles')
<style>
    body {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: #fff;
        min-height: 100vh;
    }
    .navbar-employee {
        background: rgba(15, 52, 96, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 2px solid #28a745;
    }
    .card {
        background: rgba(22, 33, 62, 0.8) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
    }
    .table {
        color: #fff !important;
    }
    .table-light {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
    }
    .text-success-light {
        color: #2ecc71;
    }
    .btn-success {
        background-color: #28a745;
        border: none;
    }
    .btn-success:hover {
        background-color: #218838;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }
    .btn-danger {
        background-color: #e74c3c;
        border: none;
    }
    .btn-danger:hover {
        background-color: #c0392b;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    }
</style>
@yield('employee_styles')
@endsection

@section('navbar')
<nav class="navbar navbar-expand-lg navbar-dark navbar-employee sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/employee/dashboard">
            <span class="text-success">K</span>enakata <span class="badge bg-success ms-2">Staff</span>
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="/employee/dashboard">Order & Product Management</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <button class="btn btn-outline-light btn-sm" onclick="logout()">Logout</button>
                </li>
            </ul>
        </div>
    </div>
</nav>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-white">Employee <span class="text-success">Control Panel</span></h2>
    </div>
    @yield('employee_content')
</div>
@endsection

@push('scripts')
    @yield('employee_scripts')
@endpush
