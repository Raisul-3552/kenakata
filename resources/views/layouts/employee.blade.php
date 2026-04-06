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
        background: rgba(15, 52, 96, 0.97);
        backdrop-filter: blur(10px);
        border-bottom: 2px solid #28a745;
    }
    .navbar-employee .nav-link {
        color: rgba(255,255,255,0.8) !important;
        font-size: 0.9rem;
        padding: 0.5rem 0.9rem;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .navbar-employee .nav-link:hover,
    .navbar-employee .nav-link.active {
        color: #2ecc71 !important;
        background: rgba(40,167,69,0.12);
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
    .form-control, .form-select {
        background: rgba(255,255,255,0.07) !important;
        border: 1px solid rgba(255,255,255,0.15) !important;
        color: #fff !important;
    }
    .form-control::placeholder { color: rgba(255,255,255,0.4); }
    .form-control:focus, .form-select:focus {
        background: rgba(255,255,255,0.12) !important;
        box-shadow: 0 0 0 0.2rem rgba(40,167,69,0.25) !important;
        border-color: #28a745 !important;
    }
    .form-select option { background: #16213e; color: #fff; }
</style>
@yield('employee_styles')
@endsection

@section('navbar')
<nav class="navbar navbar-expand-lg navbar-dark navbar-employee sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="/employee/dashboard">
            <span class="text-success">K</span>enakata <span class="badge bg-success ms-2">Staff</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#empNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="empNavbar">
            <ul class="navbar-nav me-auto ms-3 gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard') ? 'active' : '' }}" href="/employee/dashboard">📋 Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/products') ? 'active' : '' }}" href="/employee/products">📦 Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/coupons') ? 'active' : '' }}" href="/employee/coupons">🎟️ Coupons</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/deliverymen') ? 'active' : '' }}" href="/employee/deliverymen">🚴 Deliverymen</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/profile') ? 'active' : '' }}" href="/employee/profile">👤 Profile</a>
                </li>
            </ul>
            <ul class="navbar-nav align-items-center gap-2">
                <li class="nav-item">
                    <span class="text-success-light small fw-semibold" id="nav-employee-name"></span>
                </li>
                <li class="nav-item">
                    <button class="btn btn-outline-danger btn-sm px-3" onclick="logout()">Logout</button>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        try {
            const user = JSON.parse(localStorage.getItem('kenakata_user') || '{}');
            const el = document.getElementById('nav-employee-name');
            if (el && user.EmployeeName) el.textContent = '👋 ' + user.EmployeeName;
        } catch(e) {}
    });
</script>
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
