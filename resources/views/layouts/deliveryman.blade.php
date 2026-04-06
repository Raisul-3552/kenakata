@extends('layouts.app')

@section('title', 'Delivery Dashboard')

@section('styles')
<style>
    body {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: #fff;
        min-height: 100vh;
    }
    .navbar-delivery {
        background: rgba(15, 52, 96, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .card {
        background: rgba(22, 33, 62, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    .text-gold {
        color: #ffd700;
    }
    .btn-gold {
        background: #ffd700;
        color: #000;
        border: none;
        font-weight: bold;
    }
    .btn-gold:hover {
        background: #ffc400;
        color: #000;
    }
</style>
@endsection

@section('navbar')
<nav class="navbar navbar-expand-lg navbar-dark navbar-delivery sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/deliveryman/dashboard">
            <span class="text-gold">K</span>enakata <span class="badge bg-gold text-dark ms-2">Rider</span>
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('deliveryman/dashboard') ? 'active' : '' }}" href="/deliveryman/dashboard">My Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('deliveryman/profile') ? 'active' : '' }}" href="/deliveryman/profile">Profile</a>
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
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Delivery <span class="text-gold">Dashboard</span></h2>
        <div id="rider-status-badge">
            <!-- Status loaded via JS -->
        </div>
    </div>
    @yield('delivery_content')
</div>
@endsection
