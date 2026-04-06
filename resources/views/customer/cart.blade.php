@extends('layouts.customer')

@section('title', 'Your Shopping Cart')

@section('customer_styles')
<style>
    .cart-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 16px;
    }

    .cart-item-row {
        background-color: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        transition: border-color 0.2s ease;
    }

    .cart-item-row:hover {
        border-color: var(--accent-cyan);
    }

    .qty-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background-color: #020617;
        color: white;
        cursor: pointer;
        font-weight: bold;
    }

    .qty-btn:hover {
        border-color: var(--accent-cyan);
        color: var(--accent-cyan);
    }

    .summary-box {
        background-color: #020617;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        position: sticky;
        top: 100px;
    }

    .checkout-btn {
        background: linear-gradient(135deg, var(--accent-cyan), #0284c7);
        color: white;
        border-radius: 10px;
        padding: 14px;
        font-weight: 700;
        width: 100%;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: transform 0.2s;
    }

    .checkout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
    }
</style>
@endsection

@section('customer_content')
<div class="container">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Shopping Cart</h2>
                <button class="btn btn-link text-danger text-decoration-none p-0" onclick="clearCartUI()">
                    <small>Clear All Items</small>
                </button>
            </div>

            <div id="cart-items-container">
                <div class="text-center py-5">
                    <div class="spinner-border text-info"></div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="/customer/dashboard" class="text-decoration-none fw-bold" style="color: var(--accent-cyan);">
                    ← Back to Products
                </a>
            </div>
        </div>

        <!-- Summary Column -->
        <div class="col-lg-4">
            <div class="summary-box p-4">
                <h4 class="mb-4 text-white">Order Summary</h4>
                
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Subtotal</span>
                    <span id="summary-subtotal" class="fw-bold text-white">Tk 0</span>
                </div>
                
                <div class="mb-4">
                    <label class="info-label mb-2 d-block">PROMO CODE</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="coupon-code" placeholder="Enter code">
                        <button class="btn btn-cyan btn-sm px-3" type="button" onclick="applyCoupon()">Apply</button>
                    </div>
                    <div id="coupon-message" class="small mt-2"></div>
                </div>

                <div class="border-top border-secondary border-opacity-10 pt-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 fw-bold mb-0 text-white">Total</span>
                        <span class="h4 fw-bold mb-0" style="color: var(--accent-cyan);" id="summary-total">Tk 0</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="info-label mb-2 d-block">DELIVERY ADDRESS</label>
                    <textarea class="form-control" id="delivery-address" rows="3" placeholder="Enter your full street address"></textarea>
                </div>

                <button class="checkout-btn" id="btn-place-order" onclick="placeOrder()">
                    Place Order Now
                </button>
                
                <div id="order-message" class="mt-3 text-center small"></div>
            </div>
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
                <div class="text-center py-5 dark-card">
                    <div class="mb-3" style="font-size: 40px">🛒</div>
                    <h5 class="text-white">Your cart is empty</h5>
                    <p class="text-muted">Looks like you haven't added anything yet.</p>
                    <a href="/customer/dashboard" class="btn btn-cyan btn-sm mt-2 px-4">Start Shopping</a>
                </div>`;
            updateSummary();
            return;
        }

        container.innerHTML = cart.map(item => `
            <div class="cart-item-row p-3 mb-3">
                <div class="row align-items-center g-3">
                    <div class="col-auto">
                        <div class="dark-card d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: #020617;">
                            <span style="font-size: 24px">📦</span>
                        </div>
                    </div>
                    <div class="col">
                        <h6 class="text-white mb-1 font-bold">${item.name}</h6>
                        <div class="text-muted small">Unit Price: Tk ${item.price}</div>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-2">
                            <button class="qty-btn" onclick="changeQty(${item.id}, -1)">-</button>
                            <span class="text-white fw-bold px-2">${item.quantity}</span>
                            <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
                        </div>
                    </div>
                    <div class="col-auto text-end" style="min-width: 100px;">
                        <div class="fw-bold mb-1" style="color: var(--accent-cyan);">Tk ${item.price * item.quantity}</div>
                        <button class="btn btn-link text-danger p-0 small text-decoration-none" onclick="removeItem(${item.id})">Remove</button>
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
        if (confirm('Are you sure you want to clear your entire cart?')) {
            saveCart([]);
            renderCart();
        }
    }

    function updateSummary() {
        const cart = getCart();
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let discount = 0;

        if (currentCoupon) {
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
                msg.className = "small mt-2 text-info";
                msg.innerText = `Coupon Applied: ${data.coupon.Percentage}% discount`;
                updateSummary();
            } else {
                currentCoupon = null;
                msg.className = "small mt-2 text-danger";
                msg.innerText = data.message || "Invalid coupon code";
                updateSummary();
            }
        })
        .catch(err => {
            msg.className = "small mt-2 text-danger";
            msg.innerText = "Validation error";
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
                msg.className = "mt-3 text-center small text-info";
                msg.innerText = "Success! Order placed. Redirecting...";
                saveCart([]);
                setTimeout(() => {
                    window.location.href = "/customer/dashboard";
                }, 1500);
            } else {
                btn.disabled = false;
                btn.innerHTML = "Place Order Now";
                msg.className = "mt-3 text-center small text-danger";
                msg.innerText = res.body.message || "Order failed";
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = "Place Order Now";
            msg.className = "mt-3 text-center small text-danger";
            msg.innerText = "Connection error";
        });
    }
</script>
@endsection
