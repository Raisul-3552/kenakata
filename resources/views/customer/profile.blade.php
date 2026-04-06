@extends('layouts.customer')

@section('customer_content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Customer Details Section -->
        <div class="col-md-4">
            <div class="dark-card p-4">
                <h3 class="mb-4">My Profile</h3>
                <div id="profile-container">
                    <div class="text-center py-4">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order History Section -->
        <div class="col-md-8">
            <div class="dark-card p-4">
                <h3 class="mb-4">Order History</h3>
                <div id="orders-container">
                    <div class="text-center py-4">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-group {
        margin-bottom: 24px;
        padding-bottom: 8px;
    }
    
    .info-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 6px;
    }
    
    .info-value {
        font-size: 1.1rem;
        color: #fff;
        font-weight: 500;
    }

    .order-item-row {
        background-color: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        transition: border-color 0.2s ease;
    }
    
    .order-item-row:hover {
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
    
    .status-pending { background-color: rgba(245, 159, 11, 0.1); color: #fbbf24; border: 1px solid rgba(245, 159, 11, 0.2); }
    .status-confirmed { background-color: rgba(124, 58, 237, 0.1); color: #a78bfa; border: 1px solid rgba(124, 58, 237, 0.2); }
    .status-shipped { background-color: rgba(14, 165, 233, 0.1); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.2); }
    .status-cancelled { background-color: rgba(239, 68, 68, 0.1); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.2); }
</style>
@endsection

@section('customer_scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadProfile();
        loadOrders();
    });

    function loadProfile() {
        const container = document.getElementById('profile-container');
        fetch(`/api/customer/profile`, { headers: getHeaders() })
        .then(res => res.json())
        .then(data => {
            container.innerHTML = `
                <div class="info-group">
                    <div class="info-label">Full Name</div>
                    <div class="info-value text-white">${data.CustomerName}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Email Address</div>
                    <div class="info-value text-white">${data.Email}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value text-white">${data.Phone}</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Shipping Address</div>
                    <div class="info-value text-white">${data.Address || 'Primary address not set'}</div>
                </div>
            `;
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<p class="text-danger">Error loading profile</p>';
        });
    }

    function loadOrders() {
        const container = document.getElementById('orders-container');
        fetch(`/api/customer/orders`, { headers: getHeaders() })
        .then(res => res.json())
        .then(data => {
            if (!data || data.length === 0) {
                container.innerHTML = '<div class="py-5 text-center"><p class="text-muted">No orders found.</p></div>';
                return;
            }

            container.innerHTML = data.map(order => {
                let statusClass = 'status-pending';
                if (order.OrderStatus === 'Confirmed') statusClass = 'status-confirmed';
                if (order.OrderStatus === 'Shipped') statusClass = 'status-shipped';
                if (order.OrderStatus === 'Cancelled') statusClass = 'status-cancelled';

                return `
                    <div class="order-item-row">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="text-muted small">ORDER ID</div>
                                <h6 class="mb-0 text-white fw-bold">#${order.OrderID}</h6>
                            </div>
                            <span class="status-pill ${statusClass}">${order.OrderStatus}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-end pt-2 border-top border-secondary border-opacity-10">
                            <div>
                                <div class="text-muted small">Placed on ${new Date(order.OrderDate).toLocaleDateString()}</div>
                                <div class="fw-bold mt-1" style="color: var(--accent-cyan);">Tk ${order.TotalAmount}</div>
                            </div>
                            <div class="text-muted small">${order.items ? order.items.length : 0} Items</div>
                        </div>
                    </div>
                `;
            }).join('');
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<p class="text-danger">Error loading history</p>';
        });
    }
</script>
@endsection
