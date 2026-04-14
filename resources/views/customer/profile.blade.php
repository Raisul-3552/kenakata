@extends('layouts.customer')

@section('title', 'My Profile')

@section('customer_styles')
<style>
    .profile-avatar {
        width: 96px; height: 96px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; font-weight: 700; color: #fff;
        flex-shrink: 0;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.25);
    }
    .section-card {
        background: var(--bg-surface) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 16px;
    }
    .section-title {
        font-size: 0.85rem; font-weight: 700; letter-spacing: 0.06em;
        color: var(--accent-cyan); text-transform: uppercase;
        border-bottom: 1px solid rgba(14, 165, 233, 0.2);
        padding-bottom: 0.5rem; margin-bottom: 1.25rem;
    }
    .info-row { display: flex; gap: 0.5rem; align-items: start; margin-bottom: 0.9rem; }
    .info-label { min-width: 110px; color: rgba(255,255,255,0.45); font-size: 0.82rem; padding-top: 0.35rem; }
    .info-value { color: #fff; font-weight: 500; flex: 1; }
    .badge-role {
        background: linear-gradient(135deg, #0f3460, #0ea5e9);
        font-size: 0.75rem; padding: 0.3em 0.75em; border-radius: 20px;
    }
    .form-control {
        background-color: #020617 !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-primary) !important;
    }
    .form-control:focus {
        border-color: var(--accent-cyan) !important;
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2) !important;
    }
    .form-control[readonly] {
        opacity: 0.55;
        cursor: not-allowed;
    }
</style>
@endsection

@section('customer_content')
<div class="container">
    <div class="row g-4">
        {{-- Left: Info card --}}
        <div class="col-lg-4">
            <div class="section-card p-4 h-100">
                <div class="d-flex gap-3 align-items-center mb-4">
                    <div class="profile-avatar" id="avatar-initials">?</div>
                    <div>
                        <h5 class="mb-1 fw-bold text-white" id="profile-name">Loading...</h5>
                        <span class="badge badge-role text-white">🛒 Customer</span>
                    </div>
                </div>
                <div class="section-title">Account Info</div>
                <div class="info-row">
                    <span class="info-label">📧 Email</span>
                    <span class="info-value" id="profile-email">—</span>
                </div>
                <div class="info-row">
                    <span class="info-label">📱 Phone</span>
                    <span class="info-value" id="profile-phone">—</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🏠 Address</span>
                    <span class="info-value" id="profile-address">—</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🆔 Customer ID</span>
                    <span class="info-value" id="profile-id">—</span>
                </div>
                <div class="mt-3">
                    <a href="/customer/orders" class="btn btn-outline-info btn-sm w-100">
                        📦 View My Orders
                    </a>
                </div>
            </div>
        </div>

        {{-- Right: Edit form + Password --}}
        <div class="col-lg-8">
            {{-- Edit Profile --}}
            <div class="section-card p-4 mb-4">
                <div class="section-title">✏️ Edit Profile</div>
                <form id="edit-profile-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Full Name</label>
                            <input type="text" class="form-control" id="edit-name" placeholder="Your full name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Email Address <span class="text-danger small">(not editable)</span></label>
                            <input type="email" class="form-control" id="edit-email" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Phone Number</label>
                            <input type="text" class="form-control" id="edit-phone" placeholder="e.g. +8801XXXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Address</label>
                            <input type="text" class="form-control" id="edit-address" placeholder="Your address">
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-cyan px-4">💾 Save Changes</button>
                        <button type="button" class="btn btn-outline-secondary px-4" onclick="loadProfile()">↺ Reset</button>
                    </div>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="section-card p-4">
                <div class="section-title">🔒 Change Password</div>
                <form id="change-password-form">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label text-white-50 small">Current Password</label>
                            <input type="password" class="form-control" id="current-password" placeholder="Enter your current password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">New Password</label>
                            <input type="password" class="form-control" id="new-password" placeholder="Min. 6 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm-password" placeholder="Repeat new password">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning text-dark fw-bold px-4">🔑 Change Password</button>
                    </div>
                </form>
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
    document.getElementById('edit-profile-form').addEventListener('submit', saveProfile);
    document.getElementById('change-password-form').addEventListener('submit', changePassword);
});

function loadProfile() {
    fetch(`${API_URL}/customer/profile`, { headers: getHeaders() })
    .then(r => { if (r.status === 401) { logout(); } return r.json(); })
    .then(data => {
        document.getElementById('profile-name').textContent    = data.CustomerName || '—';
        document.getElementById('profile-email').textContent   = data.Email || '—';
        document.getElementById('profile-phone').textContent   = data.Phone || '—';
        document.getElementById('profile-address').textContent = data.Address || '—';
        document.getElementById('profile-id').textContent      = '#CUST-' + (data.CustomerID || '?');

        // Edit form pre-fill
        document.getElementById('edit-name').value    = data.CustomerName || '';
        document.getElementById('edit-email').value   = data.Email || '';
        document.getElementById('edit-phone').value   = data.Phone || '';
        document.getElementById('edit-address').value = data.Address || '';

        // Avatar initials
        const initials = (data.CustomerName || 'C').split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 2);
        document.getElementById('avatar-initials').textContent = initials;
    })
    .catch(() => showToast('Failed to load profile', 'error'));
}

function saveProfile(e) {
    e.preventDefault();
    const btn = e.submitter || e.target.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    fetch(`${API_URL}/customer/profile`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify({
            CustomerName: document.getElementById('edit-name').value,
            Phone:        document.getElementById('edit-phone').value,
            Address:      document.getElementById('edit-address').value,
        })
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message || 'Profile updated!', 'success');
        loadProfile();
    })
    .catch(() => showToast('Failed to update profile', 'error'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '💾 Save Changes'; });
}

function changePassword(e) {
    e.preventDefault();
    const np  = document.getElementById('new-password').value;
    const cnp = document.getElementById('confirm-password').value;

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
