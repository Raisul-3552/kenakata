@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
    body {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: #fff;
        min-height: 100vh;
    }
    .navbar-admin {
        background: rgba(15, 52, 96, 0.97);
        backdrop-filter: blur(10px);
        border-bottom: 2px solid #28a745;
    }
    .navbar-admin .navbar-brand,
    .navbar-admin .navbar-brand:hover {
        color: #fff;
        font-size: 1.3rem;
    }
    .navbar-admin .nav-link {
        color: rgba(255,255,255,0.8) !important;
        font-size: 0.9rem;
        padding: 0.5rem 0.9rem;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .navbar-admin .nav-link:hover,
    .navbar-admin .nav-link.active {
        color: #2ecc71 !important;
        background: rgba(40,167,69,0.12) !important;
    }
    .navbar-admin .btn-outline-danger:hover {
        transform: translateY(-1px);
    }
    .navbar-admin .btn-success,
    .navbar-admin .btn-success:hover {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: #fff !important;
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
    .text-navy {
        color: #2ecc71 !important;
    }
    .btn-navy {
        background-color: #28a745;
        color: #fff;
        border: none;
        transition: all 0.3s;
    }
    .btn-navy:hover {
        background-color: #218838;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }
    .bg-navy {
        background: rgba(15, 52, 96, 0.95) !important;
    }
    .form-control, .form-select {
        background: rgba(255,255,255,0.07) !important;
        border: 1px solid rgba(255,255,255,0.15) !important;
        border-radius: 8px !important;
        padding: 0.6rem 1rem !important;
        color: #fff !important;
    }
    .form-control::placeholder {
        color: rgba(255,255,255,0.4);
    }
    .form-control:focus, .form-select:focus {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40,167,69,0.25) !important;
        background: rgba(255,255,255,0.12) !important;
    }
    .text-success-light {
        color: #2ecc71;
    }
    .text-muted,
    .form-text {
        color: rgba(255,255,255,0.65) !important;
    }
</style>
@yield('admin_styles')
@endsection

@section('navbar')
<nav class="navbar navbar-expand-lg navbar-dark navbar-admin sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="/admin/dashboard">
            <span class="text-success">K</span>enakata <span class="badge bg-success ms-2">Admin</span>
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
                    <a class="nav-link {{ request()->is('admin/admins') ? 'active' : '' }}" href="/admin/admins">
                        Admins
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
                    <a href="/admin/dashboard" class="btn btn-success btn-sm fw-bold">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <span class="text-success-light small fw-semibold" id="nav-admin-name"></span>
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
            const el = document.getElementById('nav-admin-name');
            if (el && user.AdminName) el.textContent = '👋 ' + user.AdminName;
        } catch(e) {}
    });
</script>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-white">Admin <span class="text-success">Control Panel</span></h2>
    </div>
    @yield('admin_content')
</div>
@endsection
