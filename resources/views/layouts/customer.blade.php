@extends('layouts.app')

@section('title', 'Kenakata Customer')

@section('styles')
<style>
    /* Professional Dark Theme palette */
    :root {
        --bg-deep: #0f172a;        /* Deep Navy/Charcoal */
        --bg-surface: #1e293b;     /* Card surface */
        --text-primary: #f8fafc;   /* White contrast */
        --text-secondary: #94a3b8; /* Muted contrast */
        --accent-cyan: #0ea5e9;    /* Sky blue accent */
        --accent-orange: #f59e0b;  /* Orange accent */
        --border-color: #334155;   /* Subtle border */
    }

    body {
        background-color: var(--bg-deep) !important;
        color: var(--text-primary) !important;
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
    }

    /* Global Typography Fixes */
    h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
        color: var(--text-primary) !important;
        font-weight: 700;
    }

    p, span, label, div {
        color: inherit; /* Fallback to body color */
    }

    .text-muted {
        color: var(--text-secondary) !important;
    }

    /* Professional Navbar */
    .navbar-professional {
        background-color: rgba(15, 23, 42, 0.95) !important;
        backdrop-filter: blur(8px);
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 0;
    }

    .nav-brand-logo {
        font-weight: 900;
        letter-spacing: -0.05em;
        font-size: 1.5rem;
        color: var(--accent-cyan);
        text-decoration: none;
    }

    .nav-brand-logo span {
        color: var(--text-primary);
    }

    .nav-link {
        color: var(--text-secondary) !important;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.5rem 1rem !important;
        transition: color 0.2s ease;
    }

    .nav-link:hover, .nav-link.active {
        color: var(--accent-cyan) !important;
    }

    /* Dark Mode Form Controls (for Cart/Profile) */
    .form-control, .form-select {
        background-color: #020617 !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-primary) !important;
    }

    .form-control::placeholder {
        color: #475569 !important;
    }

    .form-control:focus {
        border-color: var(--accent-cyan) !important;
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2) !important;
    }

    /* Dark Cards */
    .dark-card {
        background-color: var(--bg-surface) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Buttons */
    .btn-cyan {
        background-color: var(--accent-cyan) !important;
        color: white !important;
        font-weight: 600;
        border-radius: 8px;
        border: none;
    }

    .btn-cyan:hover {
        background-color: #0284c7 !important;
    }
</style>
@yield('customer_styles')
@endsection

@section('navbar')
<nav class="navbar navbar-expand-lg navbar-dark navbar-professional sticky-top">
    <div class="container">
        <a class="nav-brand-logo" href="/customer/dashboard">KENA<span>KATA</span></a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('customer/dashboard*') ? 'active' : '' }}" href="/customer/dashboard">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('customer/profile*') ? 'active' : '' }}" href="/customer/profile">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('customer/cart*') ? 'active' : '' }}" href="/customer/cart">Cart</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <a href="/customer/cart" class="text-decoration-none me-3" id="cart-counter-nav">
                    <span class="badge bg-primary" style="background-color: var(--accent-cyan) !important;">
                        Cart (0)
                    </span>
                </a>
                <button class="btn btn-outline-danger btn-sm px-3" onclick="logout()">Logout</button>
            </div>
        </div>
    </div>
</nav>
@endsection

@section('content')
<main class="py-5" style="background-color: var(--bg-deep);">
    @yield('customer_content')
</main>
@endsection

@section('scripts')
<script>
    function getCart() { return JSON.parse(localStorage.getItem('kenakata_cart')) || []; }
    function saveCart(cart) { localStorage.setItem('kenakata_cart', JSON.stringify(cart)); updateCartCount(); }
    
    function updateCartCount() {
        const cart = getCart();
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const navCounter = document.getElementById('cart-counter-nav');
        if(navCounter) navCounter.querySelector('.badge').innerText = `Cart (${totalItems})`;
    }

    function addToCart(product) {
        let cart = getCart();
        let existing = cart.find(i => i.id === product.id);
        if(existing) { existing.quantity += 1; } 
        else { cart.push({...product, quantity: 1}); }
        saveCart(cart);
        alert(`🛒 Added ${product.name} to cart!`);
    }

    document.addEventListener('DOMContentLoaded', updateCartCount);
</script>
@yield('customer_scripts')
@endsection
