@extends('layouts.employee')

@section('title', 'Coupons & Offers')

@section('employee_styles')
<style>
    .section-card {
        background: rgba(22, 33, 62, 0.85) !important;
        border: 1px solid rgba(255,255,255,0.08) !important;
        border-radius: 16px;
    }
    .section-title {
        font-size: 0.85rem; font-weight: 700; letter-spacing: 0.06em;
        color: #2ecc71; text-transform: uppercase;
        border-bottom: 1px solid rgba(40,167,69,0.2);
        padding-bottom: 0.4rem; margin-bottom: 1rem;
    }
    .coupon-card {
        background: linear-gradient(135deg, rgba(15,52,96,0.9), rgba(26,26,46,0.9));
        border: 1px dashed rgba(40,167,69,0.5) !important;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .coupon-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3) !important; }
    .coupon-card::before {
        content: ''; position: absolute; left: -16px; top:50%; transform:translateY(-50%);
        width: 32px; height: 32px; border-radius: 50%;
        background: rgba(26,26,46,0.95);
        border: 1px dashed rgba(40,167,69,0.5);
    }
    .coupon-card::after {
        content: ''; position: absolute; right: -16px; top:50%; transform:translateY(-50%);
        width: 32px; height: 32px; border-radius: 50%;
        background: rgba(26,26,46,0.95);
        border: 1px dashed rgba(40,167,69,0.5);
    }
    .coupon-code {
        font-family: 'Courier New', monospace;
        font-size: 1.3rem; font-weight: 700; color: #2ecc71;
        letter-spacing: 0.15em; background: rgba(40,167,69,0.1);
        border: 1px solid rgba(40,167,69,0.3); border-radius: 8px;
        padding: 0.4rem 1rem; display: inline-block;
    }
    .offer-card {
        background: linear-gradient(135deg, rgba(96,15,15,0.6), rgba(46,26,26,0.9));
        border: 1px solid rgba(231,76,60,0.3) !important;
        border-radius: 12px;
        transition: transform 0.15s;
    }
    .offer-card:hover { transform: translateY(-2px); }
    .discount-badge {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: #fff; font-size: 1.1rem; font-weight: 700;
        border-radius: 8px; padding: 0.4rem 0.8rem;
    }
    .modal-dark { background: rgba(15, 27, 52, 0.98) !important; color: #fff; }
    .modal-dark .modal-header { background: #0f3460; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .date-chip {
        display: inline-flex; align-items: center; gap: 4px;
        background: rgba(255,255,255,0.07); border-radius: 6px;
        padding: 2px 8px; font-size: 0.78rem; color: rgba(255,255,255,0.6);
    }
    .status-active  { color: #2ecc71; }
    .status-expired { color: #e74c3c; }
    .status-upcoming { color: #f1c40f; }
</style>
@endsection

@section('employee_content')
<div id="coupon-alert"></div>

{{-- Header --}}
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-white">🎟️ Coupons &amp; Offers</h4>
        <small class="text-white-50">Issue discount coupons and manage product offers</small>
    </div>
    <button class="btn btn-success px-4 fw-semibold" onclick="openCouponModal()">+ New Coupon</button>
</div>

<div class="row g-4">
    {{-- LEFT: Coupons --}}
    <div class="col-lg-7">
        <div class="card section-card p-4">
            <div class="section-title">🎟️ Active Coupon Codes</div>
            <div id="coupons-list">
                <div class="text-center py-4">
                    <div class="spinner-border text-success spinner-border-sm"></div>
                    <span class="ms-2 text-white-50">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Offers --}}
    <div class="col-lg-5">
        <div class="card section-card p-4">
            <div class="section-title">🏷️ Product Offers</div>
            <div id="offers-list">
                <div class="text-center py-4">
                    <div class="spinner-border text-success spinner-border-sm"></div>
                    <span class="ms-2 text-white-50">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- == ADD COUPON MODAL == --}}
<div class="modal fade" id="couponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modal-dark border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">🎟️ Issue New Coupon</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-2">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-white-50 small">Coupon Code *</label>
                        <div class="input-group">
                            <input type="text" class="form-control text-uppercase" id="coupon-code" placeholder="e.g. SAVE200, EID50">
                            <button class="btn btn-outline-secondary" type="button" onclick="generateCode()" title="Auto generate">🎲</button>
                        </div>
                        <div class="form-text text-white-50 small">Must be unique. Customers will enter this at checkout.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-white-50 small">Discount Amount (৳) *</label>
                        <input type="number" class="form-control" id="coupon-amount" placeholder="e.g. 150" step="0.01" min="1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Valid From *</label>
                        <input type="date" class="form-control" id="coupon-start">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Valid Until *</label>
                        <input type="date" class="form-control" id="coupon-end">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-2">
                <button class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success px-4 fw-bold" onclick="submitCoupon()">✅ Issue Coupon</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    loadCoupons();
    loadOffers();
    // Set today as default start date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('coupon-start').value = today;
    document.getElementById('coupon-end').value   = today;
});

// --- Coupons ---
function loadCoupons() {
    fetch(`${API_URL}/employee/coupons`, { headers: getHeaders() })
    .then(r => { if (r.status===401) logout(); return r.json(); })
    .then(data => {
        const list = document.getElementById('coupons-list');
        const coupons = Array.isArray(data) ? data : [];
        if (!coupons.length) {
            list.innerHTML = '<p class="text-white-50 text-center py-3">No coupons issued yet. Click "+ New Coupon".</p>';
            return;
        }
        const today = new Date().toISOString().split('T')[0];
        list.innerHTML = coupons.map(c => {
            const statusText = c.EndDate < today ? 'Expired' : c.StartDate > today ? 'Upcoming' : 'Active';
            const statusCls  = c.EndDate < today ? 'status-expired' : c.StartDate > today ? 'status-upcoming' : 'status-active';
            const statusIcon = c.EndDate < today ? '🔴' : c.StartDate > today ? '🟡' : '🟢';
            return `
            <div class="card coupon-card p-3 mb-3 border-0">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <span class="coupon-code">${c.CouponCode}</span>
                        <p class="mt-2 mb-1 fw-bold text-white">৳${parseFloat(c.DiscountAmount).toLocaleString()} Off</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="date-chip">📅 ${c.StartDate} → ${c.EndDate}</span>
                            <span class="${statusCls} small fw-semibold">${statusIcon} ${statusText}</span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCoupon(${c.CouponID}, this)" title="Delete Coupon">🗑️</button>
                </div>
            </div>`;
        }).join('');
    })
    .catch(() => {
        document.getElementById('coupons-list').innerHTML = '<p class="text-danger text-center">Failed to load coupons</p>';
    });
}

function openCouponModal() {
    document.getElementById('coupon-code').value   = '';
    document.getElementById('coupon-amount').value = '';
    new bootstrap.Modal(document.getElementById('couponModal')).show();
}

function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 8; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('coupon-code').value = code;
}

function submitCoupon() {
    const body = {
        CouponCode:     document.getElementById('coupon-code').value.trim().toUpperCase(),
        DiscountAmount: document.getElementById('coupon-amount').value,
        StartDate:      document.getElementById('coupon-start').value,
        EndDate:        document.getElementById('coupon-end').value,
    };

    if (!body.CouponCode || !body.DiscountAmount || !body.StartDate || !body.EndDate) {
        showAlert('Please fill all fields', 'warning'); return;
    }
    if (body.EndDate < body.StartDate) {
        showAlert('End date must be after start date', 'warning'); return;
    }

    const btn = document.querySelector('#couponModal .btn-success');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Issuing...';

    fetch(`${API_URL}/employee/coupons`, {
        method: 'POST', headers: getHeaders(), body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        if (data.CouponID || data.message === undefined) {
            bootstrap.Modal.getInstance(document.getElementById('couponModal')).hide();
            showAlert('✅ Coupon issued successfully!', 'success');
            loadCoupons();
        } else {
            showAlert('⚠️ ' + (data.message || 'Failed to issue coupon'), 'danger');
        }
    })
    .catch(() => showAlert('Failed to issue coupon', 'danger'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '✅ Issue Coupon'; });
}

function deleteCoupon(id, btn) {
    if (!confirm('Delete this coupon? Customers can no longer use it.')) return;
    btn.disabled = true;
    fetch(`${API_URL}/employee/coupons/${id}`, { method: 'DELETE', headers: getHeaders() })
    .then(r => r.json())
    .then(() => { showAlert('🗑️ Coupon deleted', 'success'); loadCoupons(); })
    .catch(() => { showAlert('Failed to delete coupon', 'danger'); btn.disabled = false; });
}

// --- Offers ---
function loadOffers() {
    fetch(`${API_URL}/employee/offers`, { headers: getHeaders() })
    .then(r => r.json())
    .then(data => {
        const list = document.getElementById('offers-list');
        const offers = Array.isArray(data) ? data : [];
        if (!offers.length) {
            list.innerHTML = '<p class="text-white-50 text-center py-3">No offers. Add discounts from the Products page.</p>';
            return;
        }
        const today = new Date().toISOString().split('T')[0];
        list.innerHTML = offers.map(o => {
            const prodName = o.product ? o.product.ProductName : 'Unknown Product';
            const statusText = o.EndDate < today ? 'Expired' : o.StartDate > today ? 'Upcoming' : 'Active';
            const statusCls  = o.EndDate < today ? 'status-expired' : o.StartDate > today ? 'status-upcoming' : 'status-active';
            const statusIcon = o.EndDate < today ? '🔴' : o.StartDate > today ? '🟡' : '🟢';
            return `
            <div class="card offer-card p-3 mb-3 border-0">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="mb-1 fw-semibold text-white text-truncate" title="${prodName}">${prodName}</p>
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            <span class="discount-badge">৳${parseFloat(o.DiscountAmount).toLocaleString()} Off</span>
                            <span class="${statusCls} small fw-semibold">${statusIcon} ${statusText}</span>
                        </div>
                        <span class="date-chip mt-2 d-inline-block">📅 ${o.StartDate} → ${o.EndDate}</span>
                    </div>
                    <button class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="deleteOffer(${o.OfferID}, this)" title="Remove Offer">🗑️</button>
                </div>
            </div>`;
        }).join('');
    })
    .catch(() => {
        document.getElementById('offers-list').innerHTML = '<p class="text-danger text-center">Failed to load offers</p>';
    });
}

function deleteOffer(id, btn) {
    if (!confirm('Remove this offer?')) return;
    btn.disabled = true;
    fetch(`${API_URL}/employee/offers/${id}`, { method: 'DELETE', headers: getHeaders() })
    .then(r => r.json())
    .then(() => { showAlert('🗑️ Offer removed', 'success'); loadOffers(); })
    .catch(() => { showAlert('Failed to remove offer', 'danger'); btn.disabled = false; });
}

// --- Utility ---
function showAlert(msg, type) {
    const el = document.getElementById('coupon-alert');
    el.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show shadow-sm" role="alert">
        ${msg} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    setTimeout(() => { el.innerHTML = ''; }, 5000);
}
</script>
@endsection
