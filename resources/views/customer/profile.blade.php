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
    .status-delivered { background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .status-cancelled { background-color: rgba(239, 68, 68, 0.1); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.2); }
    
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 10px;
    }
    .star-rating input { display: none; }
    .star-rating label {
        font-size: 2rem;
        color: #4b5563;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #fbbf24;
    }
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
                if (order.OrderStatus === 'Delivered') statusClass = 'status-delivered';
                if (order.OrderStatus === 'Cancelled') statusClass = 'status-cancelled';

                let ratingAction = '';
                if (order.OrderStatus === 'Delivered' && order.delivery) {
                    if (order.delivery.Rating) {
                        ratingAction = `<div class="text-warning small mt-2">Rating: ${'★'.repeat(order.delivery.Rating)}${'☆'.repeat(5-order.delivery.Rating)}</div>`;
                    } else {
                        ratingAction = `<button class="btn btn-cyan btn-sm mt-2 py-1 px-3" onclick="openRatingModal(${order.delivery.DeliveryID})">Rate Rider</button>`;
                    }
                }

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
                                ${ratingAction}
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

    let currentDeliveryId = null;
    function openRatingModal(deliveryId) {
        currentDeliveryId = deliveryId;
        const modal = new bootstrap.Modal(document.getElementById('ratingModal'));
        modal.show();
    }

    function submitRating() {
        const rating = document.querySelector('input[name="rating"]:checked')?.value;
        const comment = document.getElementById('ratingComment').value;
        const btn = document.getElementById('btnSubmitRating');

        if (!rating) {
            alert('Please select a star rating');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Submitting...';

        fetch(`/api/customer/deliveries/${currentDeliveryId}/rate`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                Rating: rating,
                RatingComment: comment
            })
        })
        .then(res => res.json())
        .then(data => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('ratingModal'));
            modal.hide();
            loadOrders(); // Refresh to show rating
            alert('Thank you for your feedback!');
        })
        .catch(err => {
            console.error(err);
            alert('Error submitting rating');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Submit Review';
        });
    }
</script>

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content dark-card border-secondary">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title text-white">Rate Your Delivery Rider</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <p class="text-muted small mb-3">How was your delivery experience?</p>
                    <div class="star-rating">
                        <input type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
                        <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                        <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                        <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                        <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="info-label">Comments (Optional)</label>
                    <textarea class="form-control bg-dark border-secondary text-white" id="ratingComment" rows="3" placeholder="Tell us more about the service..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-secondary border-opacity-25">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Skip</button>
                <button type="button" class="btn btn-cyan" id="btnSubmitRating" onclick="submitRating()">Submit Review</button>
            </div>
        </div>
    </div>
</div>
@endsection
