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

                <div id="discount-row" class="d-flex justify-content-between mb-3" style="display: none !important;">
                    <span class="text-muted">Coupon Discount</span>
                    <span id="summary-discount" class="fw-bold text-success">-Tk 0</span>
                </div>

                <div class="mb-4">
                    <label class="info-label mb-2 d-block" style="font-size:0.72rem;font-weight:800;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.1em;">PROMO CODE</label>
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
                    <label class="info-label mb-2 d-block" style="font-size:0.72rem;font-weight:800;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.1em;">DELIVERY ADDRESS</label>
                    <textarea class="form-control" id="delivery-address" rows="3" placeholder="Enter your full street address"></textarea>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Wallet Balance</span>
                        <span id="summary-wallet-balance" class="fw-bold text-warning">Tk 0</span>
                    </div>
                    <div id="wallet-warning" class="small text-danger mt-1" style="display:none;">Insufficient wallet balance for this order.</div>
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
        window.addEventListener('wallet:updated', () => updateSummary());
    });

    // ── Rendering ─────────────────────────────────────────────────────────────

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

        container.innerHTML = cart.map(item => {
            const imgUrl = item.image ? (item.image.startsWith('http') ? item.image : '/' + item.image) : null;
            const imgHtml = imgUrl
                ? `<img src="${imgUrl}" alt="${item.name}" style="width: 100%; height: 100%; object-fit: cover;">`
                : '<span style="font-size: 24px">📦</span>';

            return `
            <div class="cart-item-row p-3 mb-3">
                <div class="row align-items-center g-3">
                    <div class="col-auto">
                        <div class="dark-card d-flex align-items-center justify-content-center overflow-hidden" style="width: 60px; height: 60px; background-color: #020617; border-radius: 8px;">
                            ${imgHtml}
                        </div>
                    </div>
                    <div class="col">
                        <h6 class="text-white mb-1 fw-bold">${item.name}</h6>
                        <div class="text-muted small">Unit Price: Tk ${item.price.toFixed(0)}</div>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-2">
                            <button class="qty-btn" onclick="changeQty(${item.id}, -1)">-</button>
                            <span class="text-white fw-bold px-2">${item.quantity}</span>
                            <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
                        </div>
                    </div>
                    <div class="col-auto text-end" style="min-width: 100px;">
                        <div class="fw-bold mb-1" style="color: var(--accent-cyan);">Tk ${(item.price * item.quantity).toFixed(0)}</div>
                        <button class="btn btn-link text-danger p-0 small text-decoration-none" onclick="removeItem(${item.id})">Remove</button>
                    </div>
                </div>
            </div>
            `;
        }).join('');

        updateSummary();
    }

    function changeQty(id, delta) {
        let cart = getCart();
        let item = cart.find(i => i.id == id);
        if (!item) return;

        const currentQty = Number(item.quantity) || 1;
        let newQty = currentQty + delta;
        if (newQty < 1) {
            // Cannot drop below 1 via minus button. Must use 'Remove'
            return; 
        }
        
        item.quantity = newQty;
        dbUpdateItem(id, item.quantity);
        saveCart(cart);
        renderCart();
    }

    function removeItem(id) {
        let cart = getCart().filter(i => i.id != id);
        saveCart(cart);
        dbRemoveItem(id);
        renderCart();
    }

    function clearCartUI() {
        if (!confirm('Are you sure you want to clear your entire cart?')) return;
        saveCart([]);
        dbClearCart();
        renderCart();
        showToast('Cart cleared entirely', 'info');
    }

    // ── DB Sync Helpers ───────────────────────────────────────────────────────

    function dbUpdateItem(productId, quantity) {
        fetch(`${API_URL}/customer/cart/items/${productId}`, {
            method: 'PUT',
            headers: getHeaders(),
            body: JSON.stringify({ quantity })
        }).catch(console.error);
    }

    function dbRemoveItem(productId) {
        fetch(`${API_URL}/customer/cart/items/${productId}`, {
            method: 'DELETE',
            headers: getHeaders(),
        }).catch(console.error);
    }

    function dbClearCart() {
        fetch(`${API_URL}/customer/cart`, {
            method: 'DELETE',
            headers: getHeaders(),
        }).catch(console.error);
    }

    // ── Summary ───────────────────────────────────────────────────────────────

    function updateSummary() {
        const cart = getCart();
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let discount = 0;

        if (currentCoupon) {
            discount = subtotal * (currentCoupon.DiscountAmount / 100);
            document.getElementById('discount-row').style.display = 'flex';
            document.getElementById('summary-discount').innerText = `-Tk ${discount.toFixed(0)}`;
        } else {
            document.getElementById('discount-row').style.display = 'none';
        }

        const total = Math.max(0, subtotal - discount);
        document.getElementById('summary-subtotal').innerText = `Tk ${subtotal.toFixed(0)}`;
        document.getElementById('summary-total').innerText    = `Tk ${total.toFixed(0)}`;

        const walletBalance = Number(window.customerWalletBalance || 0);
        document.getElementById('summary-wallet-balance').innerText = `Tk ${walletBalance.toFixed(0)}`;

        const insufficient = total > walletBalance;
        const warn = document.getElementById('wallet-warning');
        const btn = document.getElementById('btn-place-order');
        if (insufficient) {
            warn.style.display = 'block';
            btn.disabled = true;
            btn.style.opacity = 0.7;
        } else {
            warn.style.display = 'none';
            btn.disabled = false;
            btn.style.opacity = 1;
        }
    }

    // ── Coupon ────────────────────────────────────────────────────────────────

    function applyCoupon() {
        const code = document.getElementById('coupon-code').value.trim();
        const msg  = document.getElementById('coupon-message');
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
                msg.className = 'small mt-2 text-info';
                msg.innerText = `✅ Coupon Applied: ${data.coupon.DiscountAmount}% off`;
                updateSummary();
            } else {
                currentCoupon = null;
                msg.className = 'small mt-2 text-danger';
                msg.innerText = data.message || 'Invalid coupon code';
                updateSummary();
            }
        })
        .catch(() => {
            msg.className = 'small mt-2 text-danger';
            msg.innerText = 'Validation error. Please try again.';
        });
    }

    // ── Place Order ───────────────────────────────────────────────────────────

    function placeOrder() {
        const cart    = getCart();
        const address = document.getElementById('delivery-address').value.trim();
        const btn     = document.getElementById('btn-place-order');
        const msg     = document.getElementById('order-message');

        if (cart.length === 0) { showToast('Your cart is empty!', 'error'); return; }
        if (!address)          { showToast('Please enter a delivery address', 'error'); return; }

        btn.disabled  = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Processing...`;

        const subtotal   = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        const discount   = currentCoupon ? subtotal * (currentCoupon.DiscountAmount / 100) : 0;
        const finalTotal = Math.max(0, subtotal - discount);
        const walletBalance = Number(window.customerWalletBalance || 0);

        if (finalTotal > walletBalance) {
            btn.disabled  = false;
            btn.innerHTML = 'Place Order Now';
            msg.className = 'mt-3 text-center small text-danger';
            msg.innerText = 'Insufficient wallet balance. Please add funds in Wallet page.';
            return;
        }

        fetch(`${API_URL}/customer/orders`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                CouponID:    currentCoupon ? currentCoupon.CouponID : null,
                TotalAmount: finalTotal,
                Address:     address,
                items: cart.map(i => ({
                    ProductID: i.id,
                    Quantity:  i.quantity,
                    UnitPrice: i.price,
                }))
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status === 201) {
                msg.className = 'mt-3 text-center small text-info';
                msg.innerText = '✅ Order placed! Redirecting...';
                saveCart([]);
                if (typeof loadWalletBalance === 'function') {
                    loadWalletBalance();
                }
                setTimeout(() => { window.location.href = '/customer/orders'; }, 1500);
            } else {
                btn.disabled  = false;
                btn.innerHTML = 'Place Order Now';
                msg.className = 'mt-3 text-center small text-danger';
                msg.innerText = res.body.message || 'Order failed. Please try again.';
            }
        })
        .catch(() => {
            btn.disabled  = false;
            btn.innerHTML = 'Place Order Now';
            msg.className = 'mt-3 text-center small text-danger';
            msg.innerText = 'Connection error. Please try again.';
        });
    }
</script>
@endsection
