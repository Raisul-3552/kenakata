@extends('layouts.customer')

@section('title', 'Your Shopping Cart')

@section('styles')
<style>
    .cart-item-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 12px;
    }
    .cart-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .qty-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
    }
    .qty-btn:hover {
        background: #f8f9fa;
    }
    .summary-card {
        border-radius: 16px;
        background: #fff;
        border: 1px solid #eee;
    }
    .btn-checkout {
        background: var(--kenakata-green);
        color: white;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        width: 100%;
        border: none;
    }
    .btn-checkout:hover {
        background: var(--kenakata-dark);
    }
</style>
@endsection

@section('customer_content')
<div class="row">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success fw-bold">🛒 Your Shopping Cart</h2>
            <button class="btn btn-outline-danger btn-sm" onclick="clearCartUI()">Clear All</button>
        </div>

        <div id="cart-items-container">
            <!-- Items will be loaded here via JS -->
            <div class="text-center py-5">
                <p class="text-muted">Loading your cart...</p>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="/customer/dashboard" class="text-decoration-none text-success fw-semibold">
                ← Continue Shopping
            </a>
        </div>
    </div>

    <!-- Summary Column -->
    <div class="col-lg-4">
        <div class="card summary-card p-4 shadow-sm">
            <h4 class="mb-4">Order Summary</h4>
            
            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal</span>
                <span id="summary-subtotal">Tk 0</span>
            </div>
            
            <div class="mb-4">
                <label class="form-label small text-muted">Promo Code</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="coupon-code" placeholder="Enter code">
                    <button class="btn btn-outline-success" type="button" onclick="applyCoupon()">Apply</button>
                </div>
                <div id="coupon-message" class="small mt-1"></div>
            </div>

            <div class="d-flex justify-content-between mb-4 pt-3 border-top">
                <span class="h5 fw-bold">Total</span>
                <span class="h5 fw-bold text-success" id="summary-total">Tk 0</span>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Delivery Address</label>
                <textarea class="form-control" id="delivery-address" rows="3" placeholder="Enter full address where you want to receive your order"></textarea>
            </div>

            <button class="btn-checkout" id="btn-place-order" onclick="placeOrder()">
                Place Order Now
            </button>
            
            <div id="order-message" class="mt-3 text-center small"></div>
        </div>
    </div>
</div>
@endsection

@section('customer_scripts')
<script>
    let currentCoupon = null;

    document.addEventListener('DOMContentLoaded', () => {
        renderCart();
        // Pre-fill dummy address
        document.getElementById('delivery-address').value = "Dhaka, Bangladesh";
    });

    function renderCart() {
        const container = document.getElementById('cart-items-container');
        const cart = getCart();
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 bg-white rounded shadow-sm border">
                    <div class="mb-3" style="font-size: 50px">🛍️</div>
                    <h5>Your cart is empty</h5>
                    <p class="text-muted">Looks like you haven't added anything to your cart yet.</p>
                    <a href="/customer/dashboard" class="btn btn-kenakata mt-2">Start Shopping</a>
                </div>`;
            updateSummary();
            return;
        }

        container.innerHTML = cart.map(item => `
            <div class="card cart-item-card mb-3 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-2 bg-light d-flex align-items-center justify-content-center p-3" style="min-height: 100px;">
                            <span style="font-size: 40px">📦</span>
                        </div>
                        <div class="col-md-5 p-3">
                            <h5 class="card-title mb-1">${item.name}</h5>
                            <p class="text-muted small mb-0">Unit Price: Tk ${item.price}</p>
                        </div>
                        <div class="col-md-3 p-3">
                            <div class="d-flex align-items-center gap-3">
                                <button class="qty-btn" onclick="changeQty(${item.id}, -1)">-</button>
                                <span class="fw-bold">${item.quantity}</span>
                                <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
                            </div>
                        </div>
                        <div class="col-md-2 p-3 text-md-end">
                            <p class="text-success fw-bold mb-1">Tk ${item.price * item.quantity}</p>
                            <button class="btn btn-link link-danger p-0 small" onclick="removeItem(${item.id})">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        updateSummary();
    }

    function changeQty(id, delta) {
        let cart = getCart();
        let item = cart.find(i => i.id === id);
        if (item) {
            item.quantity += delta;
            if (item.quantity <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
            saveCart(cart);
            renderCart();
        }
    }

    function removeItem(id) {
        let cart = getCart();
        cart = cart.filter(i => i.id !== id);
        saveCart(cart);
        renderCart();
    }

    function clearCartUI() {
        if (confirm('Clear entire cart?')) {
            saveCart([]);
            renderCart();
        }
    }

    function updateSummary() {
        const cart = getCart();
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let discount = 0;

        if (currentCoupon) {
            // Assume coupon reduces a percentage for now
            discount = subtotal * (currentCoupon.Percentage / 100);
        }

        const total = subtotal - discount;

        document.getElementById('summary-subtotal').innerText = `Tk ${subtotal.toLocaleString()}`;
        document.getElementById('summary-total').innerText = `Tk ${(total < 0 ? 0 : total).toLocaleString()}`;
    }

    function applyCoupon() {
        const code = document.getElementById('coupon-code').value.trim();
        const msg = document.getElementById('coupon-message');
        
        if (!code) return;

        fetch(`${API_URL}/customer/coupons/validate`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ CouponCode: code })
        })
        .then(res => res.json())
        .then(data => {
            if (data.valid) {
                currentCoupon = data.coupon;
                msg.className = "small mt-1 text-success";
                msg.innerText = `Coupon ${code} applied! (${data.coupon.Percentage}% off)`;
                updateSummary();
            } else {
                currentCoupon = null;
                msg.className = "small mt-1 text-danger";
                msg.innerText = data.message || "Invalid coupon code";
                updateSummary();
            }
        })
        .catch(err => {
            msg.className = "small mt-1 text-danger";
            msg.innerText = "Error validating coupon";
        });
    }

    function placeOrder() {
        const cart = getCart();
        const address = document.getElementById('delivery-address').value.trim();
        const btn = document.getElementById('btn-place-order');
        const msg = document.getElementById('order-message');

        if (cart.length === 0) {
            alert('Your cart is empty!');
            return;
        }

        if (!address) {
            alert('Please enter a delivery address');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Processing...`;
        
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let discount = 0;
        if (currentCoupon) discount = subtotal * (currentCoupon.Percentage / 100);
        const finalTotal = subtotal - discount;

        const payload = {
            CouponID: currentCoupon ? currentCoupon.CouponID : null,
            TotalAmount: finalTotal,
            Address: address,
            items: cart.map(i => ({
                ProductID: i.id,
                Quantity: i.quantity,
                UnitPrice: i.price
            }))
        };

        fetch(`${API_URL}/customer/orders`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(payload)
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status === 201) {
                msg.className = "mt-3 text-center small text-success";
                msg.innerText = "Order placed successfully! Redirecting...";
                saveCart([]); // Clear cart
                setTimeout(() => {
                    window.location.href = "/customer/dashboard"; // Usually would go to orders list, but we'll go home for now
                }, 2000);
            } else {
                btn.disabled = false;
                btn.innerHTML = "Place Order Now";
                msg.className = "mt-3 text-center small text-danger";
                msg.innerText = res.body.message || "Error placing order";
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = "Place Order Now";
            msg.className = "mt-3 text-center small text-danger";
            msg.innerText = "Network error. Please try again.";
        });
    }
</script>
@endsection
