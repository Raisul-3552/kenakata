@extends('layouts.customer')

@section('title', 'My Orders')

@section('customer_styles')
<style>
    .order-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 16px;
        transition: border-color 0.2s ease;
    }
    .order-card:hover {
        border-color: var(--accent-cyan);
    }
    .status-pill {
        padding: 4px 14px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .status-pending    { background-color: rgba(245,159,11,0.12); color: #fbbf24; border: 1px solid rgba(245,159,11,0.25); }
    .status-confirmed  { background-color: rgba(124,58,237,0.12); color: #a78bfa; border: 1px solid rgba(124,58,237,0.25); }
    .status-shipped    { background-color: rgba(14,165,233,0.12);  color: #38bdf8; border: 1px solid rgba(14,165,233,0.25); }
    .status-delivered  { background-color: rgba(34,197,94,0.12);   color: #4ade80; border: 1px solid rgba(34,197,94,0.25); }
    .status-cancelled  { background-color: rgba(239,68,68,0.12);   color: #fca5a5; border: 1px solid rgba(239,68,68,0.25); }

    .order-item-line {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        font-size: 0.88rem;
    }
    .order-item-line:last-child { border-bottom: none; }
</style>
@endsection

@section('customer_content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center gap-3">
            <div>
                <h1 class="mb-1">My Orders</h1>
                <p class="text-muted mb-0">Track all your orders and their status</p>
            </div>
        </div>
    </div>

    <div id="orders-container">
        <div class="text-center py-5">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customer_scripts')
<script>
document.addEventListener('DOMContentLoaded', loadOrders);

function loadOrders() {
    const container = document.getElementById('orders-container');

    fetch(`${API_URL}/customer/orders`, { headers: getHeaders() })
    .then(res => {
        if (res.status === 401 || res.status === 403) logout();
        return res.json();
    })
    .then(data => {
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <div style="font-size: 48px; margin-bottom: 12px;">📦</div>
                    <h5 class="text-white">No orders yet</h5>
                    <p class="text-muted">You haven't placed any orders. Start shopping!</p>
                    <a href="/customer/dashboard" class="btn btn-cyan mt-2 px-4">Browse Products</a>
                </div>`;
            return;
        }

        container.innerHTML = data.map((order, index) => {
            let statusClass = 'status-pending';
            if (order.OrderStatus === 'Confirmed')  statusClass = 'status-confirmed';
            if (order.OrderStatus === 'Shipped')    statusClass = 'status-shipped';
            if (order.OrderStatus === 'Delivered')  statusClass = 'status-delivered';
            if (order.OrderStatus === 'Cancelled')  statusClass = 'status-cancelled';

            const orderDate = order.OrderDate
                ? new Date(order.OrderDate).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                : '—';
            
            const serialNumber = data.length - index;

            const itemsHtml = (order.items && order.items.length > 0)
                ? order.items.map(item => {
                    const imgUrl = item.product && item.product.detail && item.product.detail.Image
                        ? (item.product.detail.Image.startsWith('http') ? item.product.detail.Image : '/' + item.product.detail.Image)
                        : null;
                    const imgHtml = imgUrl
                        ? `<img src="${imgUrl}" alt="${item.product ? item.product.ProductName : ''}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px;">`
                        : '<span class="text-muted" style="font-size: 24px;">📦</span>';

                    return `
                    <div class="order-item-line">
                        ${imgHtml}
                        <span class="text-white flex-grow-1 ms-2">${item.product ? item.product.ProductName : 'Product #' + item.ProductID}</span>
                        <span class="text-muted">×${item.Quantity}</span>
                        <span style="color: var(--accent-cyan); min-width: 80px; text-align: right;">Tk ${parseFloat(item.UnitPrice * item.Quantity).toFixed(0)}</span>
                    </div>`;
                }).join('')
                : '<div class="text-muted small py-2">No item details available.</div>';

            return `
            <div class="order-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-muted small mb-1">ORDER ID</div>
                        <h6 class="mb-0 text-white fw-bold">${serialNumber}. #${order.OrderID}</h6>
                    </div>
                    <span class="status-pill ${statusClass}">${order.OrderStatus}</span>
                </div>

                <div class="mb-3">
                    ${itemsHtml}
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-10">
                    <div>
                        <div class="text-muted small">Placed on ${orderDate}</div>
                        ${order.Address ? `<div class="text-muted small mt-1">📍 ${order.Address}</div>` : ''}
                    </div>
                        <div class="text-end">
                        <div class="text-muted small">${order.items ? order.items.length : 0} item(s)</div>
                        <div class="fw-bold mt-1" style="color: var(--accent-cyan); font-size: 1.1rem;">Tk ${parseFloat(order.TotalAmount).toFixed(0)}</div>
                        ${order.OrderStatus === 'Pending' ? `
                            <button id="cancel-btn-${order.OrderID}" class="btn btn-sm btn-outline-danger mt-2" onclick="confirmCancel(${order.OrderID})">Cancel Order</button>
                        ` : ''}
                    </div>
                </div>
            </div>`;
        }).join('');
    })
    .catch(err => {
        console.error(err);
        container.innerHTML = '<div class="text-center py-5 text-danger">Error loading orders. Please try again.</div>';
    });
}

function confirmCancel(id) {
    const btn = document.getElementById(`cancel-btn-${id}`);
    if (!btn) return;

    if (btn.dataset.confirming === 'true') {
        // Second click -> actually cancel
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Cancelling...';
        btn.disabled = true;
        cancelOrder(id);
    } else {
        // First click -> ask for confirmation
        btn.dataset.confirming = 'true';
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Are you sure? Click to confirm';
        btn.classList.add('btn-danger');
        btn.classList.remove('btn-outline-danger');
        btn.style.color = '#fff';

        // Reset if they wait too long
        setTimeout(() => {
            if (!btn.disabled) {
                btn.dataset.confirming = 'false';
                btn.innerHTML = originalText;
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-outline-danger');
                btn.style.color = '';
            }
        }, 3500);
    }
}

function cancelOrder(id) {
    fetch(`${API_URL}/customer/orders/${id}/cancel`, { 
        method: 'POST',
        headers: getHeaders() 
    })
    .then(res => {
        if (res.status === 401 || res.status === 403) logout();
        return res.json();
    })
    .then(data => {
        if (data.message) showToast(data.message, 'info');
        loadOrders();
    })
    .catch(err => {
        console.error(err);
        showToast('Failed to cancel order.', 'error');
    });
}
</script>
@endsection
