@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('navbar')
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="/customer/dashboard">Kenakata</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/customer/dashboard">Products</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/customer/cart" id="cart-btn">Cart (0)</a>
                </li>
                <li class="nav-item">
                    <button class="btn btn-outline-light btn-sm mt-1" onclick="logout()">Logout</button>
                </li>
            </ul>
        </div>
    </div>
</nav>
@endsection

@section('content')
@yield('customer_content')
@endsection

@section('scripts')
<script>
    // Simple localstorage cart system
    function getCart() {
        return JSON.parse(localStorage.getItem('kenakata_cart')) || [];
    }
    
    function saveCart(cart) {
        localStorage.setItem('kenakata_cart', JSON.stringify(cart));
        updateCartCount();
    }
    
    function updateCartCount() {
        const cart = getCart();
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const cartBtn = document.getElementById('cart-btn');
        if(cartBtn) cartBtn.innerText = `Cart (${totalItems})`;
    }
    
    function addToCart(product) {
        let cart = getCart();
        let existing = cart.find(i => i.id === product.id);
        if(existing) {
            existing.quantity += 1;
        } else {
            cart.push({...product, quantity: 1});
        }
        saveCart(cart);
        alert(`${product.name} added to cart!`);
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCartCount();
    });
</script>
@yield('customer_scripts')
@endsection
