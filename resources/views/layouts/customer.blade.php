@extends('layouts.app')

@section('title', 'Kenakata Customer')

@section('styles')
<style>
    /* Professional Dark Theme palette */
    :root {
        --bg-deep: #0f172a;
        --bg-surface: #1e293b;
        --text-primary: #f8fafc;
        --text-secondary: #94a3b8;
        --accent-cyan: #0ea5e9;
        --accent-orange: #f59e0b;
        --border-color: #334155;
    }

    body {
        background-color: var(--bg-deep) !important;
        color: var(--text-primary) !important;
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
    }

    h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
        color: var(--text-primary) !important;
        font-weight: 700;
    }

    p, span, label, div { color: inherit; }

    .text-muted { color: var(--text-secondary) !important; }

    /* Navbar */
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

    .nav-brand-logo span { color: var(--text-primary); }

    .nav-link {
        color: var(--text-secondary) !important;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.5rem 1rem !important;
        transition: color 0.2s ease;
    }

    .nav-link:hover, .nav-link.active { color: var(--accent-cyan) !important; }

    /* Form Controls */
    .form-control, .form-select {
        background-color: #020617 !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-primary) !important;
    }
    .form-control::placeholder { color: #475569 !important; }
    .form-control:focus {
        border-color: var(--accent-cyan) !important;
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2) !important;
    }

    /* Dark Cards */
    .dark-card {
        background-color: var(--bg-surface) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
    }

    /* Buttons */
    .btn-cyan {
        background-color: var(--accent-cyan) !important;
        color: white !important;
        font-weight: 600;
        border-radius: 8px;
        border: none;
    }
    .btn-cyan:hover { background-color: #0284c7 !important; }
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
                    <a class="nav-link {{ Request::is('customer/orders*') ? 'active' : '' }}" href="/customer/orders">My Orders</a>
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
                    <span class="badge" style="background-color: var(--accent-cyan);">
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
    // ─── LocalStorage cart helpers ────────────────────────────────────────────
    function getCart() {
        const cart = JSON.parse(localStorage.getItem('kenakata_cart')) || [];
        return cart.map(item => ({ ...item, quantity: Number(item.quantity) || 1 }));
    }
    function saveCart(cart)   { localStorage.setItem('kenakata_cart', JSON.stringify(cart)); updateCartCount(); }

    function updateCartCount() {
        const cart       = getCart();
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const navCounter = document.getElementById('cart-counter-nav');
        if (navCounter) navCounter.querySelector('.badge').innerText = `Cart (${totalItems})`;
    }

    // ─── Add to cart (localStorage + DB sync) ────────────────────────────────
    function addToCart(product) {
        // 1. Update localStorage immediately for instant UI feedback
        let cart     = getCart();
        let existing = cart.find(i => i.id == product.id);
        if (existing) {
            existing.quantity = (Number(existing.quantity) || 0) + 1;
        } else {
            cart.push({ ...product, quantity: 1 });
        }
        saveCart(cart);

        // 2. Sync to DB in the background
        fetch(`${API_URL}/customer/cart/items`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ id: product.id, name: product.name, price: product.price, quantity: 1 })
        }).catch(console.error);

        // 3. Show toast feedback
        showCartToast(product.name);
    }

    function showCartToast(name) {
        // Remove old toast if any
        const old = document.getElementById('cart-toast');
        if (old) old.remove();

        const toast = document.createElement('div');
        toast.id = 'cart-toast';
        toast.style.cssText = `
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            background: #1e293b; border: 1px solid #0ea5e9;
            border-radius: 12px; padding: 14px 20px;
            color: #f8fafc; font-size: 0.9rem; font-weight: 600;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            animation: slideIn 0.3s ease;
        `;
        toast.innerHTML = `🛒 <strong style="margin-left: 8px;">${name}</strong> added to cart!`;
        document.body.appendChild(toast);
        setTimeout(() => { if (document.body.contains(toast)) toast.remove(); }, 2500);
    }

    function showToast(message, type = 'success') {
        const old = document.getElementById('main-toast');
        if (old) old.remove();

        const toast = document.createElement('div');
        toast.id = 'main-toast';
        const borderColor = type === 'error' ? '#ef4444' : '#0ea5e9';
        const icon = type === 'error' ? '❌' : (type === 'info' ? 'ℹ️' : '✅');

        toast.style.cssText = `
            position: fixed; bottom: 84px; right: 24px; z-index: 9999;
            background: #1e293b; border: 1px solid ${borderColor};
            border-radius: 12px; padding: 14px 20px;
            color: #f8fafc; font-size: 0.9rem; font-weight: 600;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            animation: slideIn 0.3s ease;
        `;
        toast.innerHTML = `${icon} <span style="margin-left: 8px;">${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => { if (document.body.contains(toast)) toast.remove(); }, 3000);
    }

    // ─── On load: sync DB cart back to localStorage (so cart page is accurate) ─
    document.addEventListener('DOMContentLoaded', () => {
        updateCartCount();

        // Pull latest cart from DB and sync to localStorage
        const token = localStorage.getItem('kenakata_token');
        if (token) {
            fetch(`${API_URL}/customer/cart`, { headers: getHeaders() })
            .then(r => r.ok ? r.json() : [])
            .then(dbCart => {
                if (Array.isArray(dbCart)) {
                    saveCart(dbCart);
                }
            })
            .catch(console.error);
        }
    });
</script>
<style>
    @keyframes slideIn {
        from { transform: translateX(120%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }
</style>
@yield('customer_scripts')
@endsection
