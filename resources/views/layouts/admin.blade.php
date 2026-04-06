@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
    body {
        background-color: #f0f8ff;
        color: #333;
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .navbar-admin {
        background-color: #1a1a1a !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        border: none !important;
    }
    .navbar-admin .navbar-brand,
    .navbar-admin .navbar-brand:hover {
        color: #fff !important;
        font-size: 1.3rem;
    }
    .navbar-admin .nav-link,
    .navbar-admin .nav-link:hover,
    .navbar-admin .nav-link.active {
        color: #fff !important;
        background: transparent !important;
    }
    .navbar-admin .btn-outline-light:hover {
        background-color: transparent !important;
        color: #fff !important;
        border-color: #fff !important;
    }
    .navbar-admin .btn-info,
    .navbar-admin .btn-info:hover {
        background-color: #0dcaf0 !important;
        border-color: #0dcaf0 !important;
        color: #000 !important;
    }
    .card {
        background-color: #fff !important;
        border: none !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important;
        border-radius: 12px !important;
        color: #333 !important;
    }
    .table {
        background-color: #fff !important;
    }
    .text-navy {
        color: #000080;
    }
    .btn-navy {
        background-color: #000080;
        color: #fff;
        border: none;
        transition: all 0.3s;
    }
    .btn-navy:hover {
        background-color: #000066;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 128, 0.2);
    }
    .form-control, .form-select {
        border: 1px solid #ced4da !important;
        border-radius: 8px !important;
        padding: 0.6rem 1rem !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #000080 !important;
        box-shadow: 0 0 0 0.25rem rgba(0, 0, 128, 0.1) !important;
    }
</style>
@yield('admin_styles')
@endsection

@section('navbar')
<nav class="navbar navbar-expand-lg navbar-dark navbar-admin sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="/admin/dashboard">
            Kenakata Admin
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto ms-3 gap-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="/admin/dashboard">
                        Employees
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/products') ? 'active' : '' }}" href="/admin/products">
                        Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/profile') ? 'active' : '' }}" href="/admin/profile">
                        Profile
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav align-items-center gap-3">
                @if(!request()->is('admin/dashboard'))
                <li class="nav-item">
                    <a href="/admin/dashboard" class="btn btn-info btn-sm fw-bold">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <span class="text-light small fw-semibold" id="nav-admin-name"></span>
                </li>
                <li class="nav-item">
                    <button class="btn btn-outline-light btn-sm px-3" onclick="logout()">Logout</button>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        try {
            const user = JSON.parse(localStorage.getItem('kenakata_user') || '{}');
            const el = document.getElementById('nav-admin-name');
            if (el && user.AdminName) el.textContent = '👋 ' + user.AdminName;
        } catch(e) {}
    });
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Admin Dashboard</h2>
        @yield('admin_content')
    </div>
</div>
@endsection
