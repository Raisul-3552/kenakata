@extends('layouts.admin')

@section('title', 'Admin Products')

@section('admin_styles')
<style>
    .product-card {
        background-color: #fff !important;
        border: none !important;
        border-radius: 16px;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    .product-img {
        width: 100%; height: 180px; object-fit: cover;
        border-radius: 16px 16px 0 0;
    }
    .product-img-placeholder {
        width: 100%; height: 180px;
        background-color: #e9ecef;
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem; border-radius: 16px 16px 0 0;
        color: #adb5bd;
    }
    .price-tag { color: #001f3f; font-size: 1.25rem; font-weight: 700; }
    .section-title {
        font-size: 0.9rem; font-weight: 700; letter-spacing: 1px;
        color: #001f3f; text-transform: uppercase;
        border-bottom: 2px solid #001f3f; padding-bottom: 0.5rem; margin-bottom: 1.5rem;
    }
    .modal-content { border-radius: 16px; border: none; }
    .modal-header { background-color: #001f3f; color: #fff; border-radius: 16px 16px 0 0; }
    .search-box { border-radius: 10px; padding: 0.6rem 1.2rem; border: 1px solid #ced4da !important; }
    .search-box:focus { border-color: #001f3f !important; box-shadow: 0 0 0 0.25rem rgba(0,31,63,0.1) !important; }
    .btn-add-main { background-color: #001f3f; border: none; border-radius: 10px; font-weight: 600; color: #fff; transition: all 0.3s; }
    .btn-add-main:hover { background-color: #003366; color: #fff; transform: translateY(-1px); }
    
    .stat-card {
        background-color: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        border-left: 5px solid #001f3f;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
</style>
@endsection

@section('admin_content')
<div id="prod-alert"></div>

{{-- Top Bar --}}
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-4 mt-2">
    <div>
        <h3 class="fw-bold mb-0 text-navy">📦 Inventory Management</h3>
        <p class="text-muted mb-0">Monitor and manage your product catalogue</p>
    </div>
    <div class="d-flex gap-3">
        <div class="position-relative">
            <input type="text" class="form-control search-box" style="width:280px" id="product-search" placeholder="Search by name, brand...">
        </div>
        <button class="btn btn-add-main px-4 d-flex align-items-center" onclick="openAddModal()">
            <i class="bi bi-plus-lg me-2"></i> Add Product
        </button>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-5">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color: #001f3f;">
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Products</div>
            <div class="h3 fw-bold text-navy mb-0" id="stat-total">—</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color: #28a745;">
            <div class="text-muted small fw-bold text-uppercase mb-1">In Stock</div>
            <div class="h3 fw-bold text-success mb-0" id="stat-instock">—</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color: #ffc107;">
            <div class="text-muted small fw-bold text-uppercase mb-1">Low Stock</div>
            <div class="h3 fw-bold text-warning mb-0" id="stat-lowstock">—</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color: #dc3545;">
            <div class="text-muted small fw-bold text-uppercase mb-1">Out of Stock</div>
            <div class="h3 fw-bold text-danger mb-0" id="stat-outstock">—</div>
        </div>
    </div>
</div>

{{-- Products Grid --}}
<div class="row g-4" id="products-grid">
    <div class="col-12 text-center py-5">
        <div class="spinner-border text-navy"></div>
        <p class="mt-2 text-muted">Fetching inventory...</p>
    </div>
</div>

{{-- == ADD PRODUCT MODAL == --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Add New Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="section-title mt-3">Basic Info</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-navy small">Product Name *</label>
                        <input type="text" class="form-control" id="add-name" placeholder="e.g. iPhone 15 Pro">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-navy small">Brand *</label>
                        <input type="text" class="form-control" id="add-brand" placeholder="e.g. Apple">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-navy small">Category *</label>
                        <select class="form-select" id="add-category">
                            <option value="">Loading...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-navy small">Price (৳) *</label>
                        <input type="number" class="form-control" id="add-price" placeholder="0.00" step="0.01" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-navy small">Stock Quantity *</label>
                        <input type="number" class="form-control" id="add-stock" placeholder="0" min="0">
                    </div>
                </div>
                <div class="section-title">Details & Media</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-navy small">Description *</label>
                        <textarea class="form-control" id="add-desc" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold text-navy small">Specification *</label>
                        <textarea class="form-control" id="add-spec" rows="3"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-navy small">Warranty</label>
                        <input type="text" class="form-control" id="add-warranty">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-navy small">Image URL</label>
                        <input type="text" class="form-control" id="add-image">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-navy px-4 fw-bold" onclick="submitAddProduct()">Save Product</button>
            </div>
        </div>
    </div>
</div>

{{-- == EDIT PRODUCT MODAL == --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <input type="hidden" id="edit-product-id">
                <div class="section-title mt-3">Product Information</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-navy small">Product Name</label>
                        <input type="text" class="form-control" id="edit-name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-navy small">Brand</label>
                        <input type="text" class="form-control" id="edit-brand">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-navy small">Category</label>
                        <select class="form-select" id="edit-category"></select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-navy small">Price (৳)</label>
                        <input type="number" class="form-control" id="edit-price" step="0.01" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-navy small">Stock Level</label>
                        <input type="number" class="form-control" id="edit-stock" min="0">
                    </div>
                </div>
                <div class="section-title">Description & Tech Specs</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-navy small">Detailed Description</label>
                        <textarea class="form-control" id="edit-desc" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold text-navy small">Full Specification</label>
                        <textarea class="form-control" id="edit-spec" rows="3"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-navy small">Warranty Policy</label>
                        <input type="text" class="form-control" id="edit-warranty">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-navy small">Product Image URL</label>
                        <input type="text" class="form-control" id="edit-image">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Discard</button>
                <button class="btn btn-navy px-4 fw-bold" onclick="submitEditProduct()">Update Inventory</button>
            </div>
        </div>
    </div>
</div>

{{-- == OFFER MODAL == --}}
<div class="modal fade" id="offerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger">
                <h5 class="modal-title fw-bold"><i class="bi bi-tag me-2"></i>Launch Special Offer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <input type="hidden" id="offer-product-id">
                <input type="hidden" id="offer-product-price">
                <div class="d-flex align-items-center gap-2 mb-4 mt-3 bg-light p-3 rounded border border-info border-opacity-25 shadow-sm">
                    <span class="text-muted small">Item:</span>
                    <strong class="text-navy h6 mb-0" id="offer-product-name">—</strong>
                    <span class="ms-auto badge bg-navy p-2" id="offer-product-price-badge">৳0</span>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-navy small">Discount Type</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="discount-type" id="type-percent" value="percent" checked>
                            <label class="btn btn-outline-info fw-bold" for="type-percent">Percentage (%)</label>
                            <input type="radio" class="btn-check" name="discount-type" id="type-flat" value="flat">
                            <label class="btn btn-outline-info fw-bold" for="type-flat">Flat Amount (৳)</label>
                        </div>
                    </div>
                    <div class="col-12" id="percent-input-group">
                        <label class="form-label fw-bold text-navy small">Percentage Off *</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="offer-percent" placeholder="e.g. 15" min="1" max="99">
                            <span class="input-group-text bg-light text-navy fw-bold">%</span>
                        </div>
                        <div class="form-text mt-2 text-info fw-bold" id="offer-live-preview"></div>
                    </div>
                    <div class="col-12 d-none" id="flat-input-group">
                        <label class="form-label fw-bold text-navy small">Discount Value (৳) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-navy fw-bold">৳</span>
                            <input type="number" class="form-control" id="offer-amount" placeholder="e.g. 500">
                        </div>
                        <div class="form-text mt-2 text-info fw-bold" id="offer-flat-preview"></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold text-navy small">Start Date</label>
                        <input type="date" class="form-control" id="offer-start">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold text-navy small">End Date</label>
                        <input type="date" class="form-control" id="offer-end">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger px-4 fw-bold shadow-sm" id="offer-submit-btn" onclick="submitOffer()">Confirm Promotion</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allProducts = [];
let categories  = [];

document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    loadCategories();
    document.getElementById('product-search').addEventListener('input', filterProducts);
});

// --- Load Content ---
function loadProducts() {
    fetch(`${API_URL}/admin/products`, { headers: getHeaders() })
    .then(r => { if(r.status===401 || r.status===403) logout(); return r.json(); })
    .then(data => {
        allProducts = Array.isArray(data) ? data : (data.products || []);
        updateStatsView(allProducts);
        renderProductsView(allProducts);
    })
    .catch(() => {
        document.getElementById('products-grid').innerHTML = '<div class="col-12 text-center text-danger py-5">⚠️ Critical connection error</div>';
    });
}

function loadCategories() {
    fetch(`${API_URL}/admin/categories`, { headers: getHeaders() })
    .then(r => r.json())
    .then(data => {
        categories = data;
        const opts = categories.map(c => `<option value="${c.CategoryID}">${c.CategoryName}</option>`).join('');
        document.getElementById('add-category').innerHTML = '<option value="">Choose Category</option>' + opts;
        document.getElementById('edit-category').innerHTML = '<option value="">Choose Category</option>' + opts;
    });
}

// --- View Rendering ---
function updateStatsView(products) {
    document.getElementById('stat-total').textContent    = products.length;
    document.getElementById('stat-instock').textContent  = products.filter(p => p.Stock > 5).length;
    document.getElementById('stat-lowstock').textContent = products.filter(p => p.Stock > 0 && p.Stock <= 5).length;
    document.getElementById('stat-outstock').textContent = products.filter(p => p.Stock === 0).length;
}

function renderProductsView(products) {
    const grid = document.getElementById('products-grid');
    if (!products.length) {
        grid.innerHTML = '<div class="col-12 text-center py-5 text-muted h5">No items found in inventory.</div>';
        return;
    }
    grid.innerHTML = products.map(p => {
        const catName   = p.category ? p.category.CategoryName : 'Uncategorized';
        const hasOffer  = (p.offers && p.offers.length > 0);
        const imgSrc    = p.detail && p.detail.Image ? p.detail.Image : null;
        const stockCol  = p.Stock === 0 ? 'danger' : (p.Stock <= 5 ? 'warning' : 'success');
        const stockTxt  = p.Stock === 0 ? 'Out of Stock' : (p.Stock <= 5 ? `Low Stock (${p.Stock})` : `${p.Stock} In Stock`);

        return `
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card product-card h-100 overflow-hidden">
                <div class="position-relative">
                    ${imgSrc
                        ? `<img src="${imgSrc}" class="product-img" alt="${p.ProductName}" onerror="this.onerror=null;this.src='https://via.placeholder.com/400x300?text=No+Image';">`
                        : `<div class="product-img-placeholder"><i class="bi bi-box"></i></div>`
                    }
                    ${hasOffer ? `<span class="badge bg-danger position-absolute top-0 start-0 m-3 shadow-sm"><i class="bi bi-percent me-1"></i>PROMO</span>` : ''}
                </div>
                <div class="card-body p-3 d-flex flex-column">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <span class="badge bg-navy bg-opacity-10 text-navy border border-navy border-opacity-25 small px-2 py-1">${catName}</span>
                        <span class="text-muted small fw-bold">${p.Brand}</span>
                    </div>
                    <h6 class="fw-bold text-navy mb-3 mt-1" style="line-height:1.4;">${p.ProductName}</h6>
                    <div class="mt-auto">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="price-tag">৳${parseFloat(p.Price).toLocaleString()}</span>
                            <span class="badge bg-${stockCol} bg-opacity-10 text-${stockCol} border border-${stockCol} border-opacity-25 px-2 py-1" style="font-size:0.75rem;">${stockTxt}</span>
                        </div>
                        <div class="d-flex gap-2 pt-3 border-top border-light">
                            <button class="btn btn-sm btn-outline-info flex-fill fw-bold" onclick='openEditModal(${JSON.stringify(p)})'><i class="bi bi-pencil me-1"></i>Edit</button>
                            <button class="btn btn-sm btn-outline-danger flex-fill fw-bold" onclick='openOfferModal(${JSON.stringify(p)})'><i class="bi bi-tag me-1"></i>Offer</button>
                            <button class="btn btn-sm btn-outline-secondary px-2" onclick="deleteProduct(${p.ProductID}, this)"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
}

function filterProducts() {
    const q = document.getElementById('product-search').value.toLowerCase();
    const filtered = allProducts.filter(p =>
        p.ProductName.toLowerCase().includes(q) ||
        p.Brand.toLowerCase().includes(q) ||
        (p.category && p.category.CategoryName.toLowerCase().includes(q))
    );
    renderProductsView(filtered);
}

// --- CRUD Actions ---
function submitAddProduct() {
    const body = {
        CategoryID: document.getElementById('add-category').value,
        ProductName: document.getElementById('add-name').value.trim(),
        Brand: document.getElementById('add-brand').value.trim(),
        Price: document.getElementById('add-price').value,
        Stock: document.getElementById('add-stock').value,
        Description: document.getElementById('add-desc').value.trim(),
        Specification: document.getElementById('add-spec').value.trim(),
        Warranty: document.getElementById('add-warranty').value.trim(),
        Image: document.getElementById('add-image').value.trim(),
    };

    const btn = event.target;
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Pending...';

    fetch(`${API_URL}/admin/products`, {
        method: 'POST', headers: getHeaders(), body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
        showAlertView('✅ Successfully uploaded product!', 'success');
        loadProducts();
    })
    .catch(() => showAlertView('Failed to persist product.', 'danger'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '✅ Save Product'; });
}

function openEditModal(p) {
    document.getElementById('edit-product-id').value = p.ProductID;
    document.getElementById('edit-name').value  = p.ProductName;
    document.getElementById('edit-brand').value = p.Brand;
    document.getElementById('edit-price').value = p.Price;
    document.getElementById('edit-stock').value = p.Stock;
    document.getElementById('edit-desc').value  = p.detail ? p.detail.Description : '';
    document.getElementById('edit-spec').value  = p.detail ? p.detail.Specification : '';
    document.getElementById('edit-warranty').value = p.detail ? p.detail.Warranty : '';
    document.getElementById('edit-image').value    = p.detail ? p.detail.Image : '';
    document.getElementById('edit-category').value = p.CategoryID;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function submitEditProduct() {
    const id = document.getElementById('edit-product-id').value;
    const body = {
        CategoryID: document.getElementById('edit-category').value,
        ProductName: document.getElementById('edit-name').value.trim(),
        Brand: document.getElementById('edit-brand').value.trim(),
        Price: document.getElementById('edit-price').value,
        Stock: document.getElementById('edit-stock').value,
        Description: document.getElementById('edit-desc').value.trim(),
        Specification: document.getElementById('edit-spec').value.trim(),
        Warranty: document.getElementById('edit-warranty').value.trim(),
        Image: document.getElementById('edit-image').value.trim(),
    };

    fetch(`${API_URL}/admin/products/${id}`, {
        method: 'PUT', headers: getHeaders(), body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
        showAlertView('✅ Updates applied to inventory.', 'success');
        loadProducts();
    })
    .catch(() => showAlertView('Update protocol failed.', 'danger'));
}

function deleteProduct(id, btn) {
    if (!confirm('Permanently remove this product from catalogue?')) return;
    btn.disabled = true;
    fetch(`${API_URL}/admin/products/${id}`, { method:'DELETE', headers: getHeaders() })
    .then(r => r.json())
    .then(() => { showAlertView('🗑️ Item purged from database.', 'success'); loadProducts(); })
    .catch(() => { showAlertView('Purge request failed.', 'danger'); btn.disabled = false; });
}

// --- Offer Management ---
function openOfferModal(p) {
    document.getElementById('offer-product-id').value    = p.ProductID;
    document.getElementById('offer-product-price').value = p.Price;
    document.getElementById('offer-product-name').textContent  = p.ProductName;
    document.getElementById('offer-product-price-badge').textContent = '৳' + parseFloat(p.Price).toLocaleString();
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('offer-start').value = today;
    document.getElementById('offer-end').value   = today;
    new bootstrap.Modal(document.getElementById('offerModal')).show();
}

document.querySelectorAll('input[name="discount-type"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const isPct = document.getElementById('type-percent').checked;
        document.getElementById('percent-input-group').classList.toggle('d-none', !isPct);
        document.getElementById('flat-input-group').classList.toggle('d-none', isPct);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const priceInput = document.getElementById('offer-product-price');
    document.getElementById('offer-percent').addEventListener('input', (e) => {
        const p = parseFloat(priceInput.value), v = parseFloat(e.target.value) || 0;
        const save = (p * v / 100);
        document.getElementById('offer-live-preview').textContent = v > 0 ? `Savings: ৳${save.toLocaleString()} → Final: ৳${(p-save).toLocaleString()}` : '';
    });
    document.getElementById('offer-amount').addEventListener('input', (e) => {
        const p = parseFloat(priceInput.value), v = parseFloat(e.target.value) || 0;
        document.getElementById('offer-flat-preview').textContent = v > 0 ? `Savings: ৳${v.toLocaleString()} → Final: ৳${(p-v).toLocaleString()}` : '';
    });
});

function submitOffer() {
    const isPct = document.getElementById('type-percent').checked;
    const price = parseFloat(document.getElementById('offer-product-price').value);
    let disc;

    if (isPct) {
        const v = parseFloat(document.getElementById('offer-percent').value);
        if(!v || v < 1 || v > 99) return showAlertView('Enter 1-99%', 'warning');
        disc = (price * v / 100);
    } else {
        disc = parseFloat(document.getElementById('offer-amount').value);
        if(!disc || disc <= 0 || disc >= price) return showAlertView('Invalid amount', 'warning');
    }

    const payload = {
        ProductID:      document.getElementById('offer-product-id').value,
        DiscountAmount: disc.toFixed(2),
        StartDate:      document.getElementById('offer-start').value,
        EndDate:        document.getElementById('offer-end').value,
    };

    fetch(`${API_URL}/admin/offers`, {
        method: 'POST', headers: getHeaders(), body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('offerModal')).hide();
        showAlertView('🏷️ Live offer deployed.', 'success');
        loadProducts();
    })
    .catch(() => showAlertView('Promotion launch failed.', 'danger'));
}

function showAlertView(m, t) {
    const el = document.getElementById('prod-alert');
    el.innerHTML = `<div class="alert alert-${t} border-0 shadow-sm alert-dismissible fade show fw-bold text-center" role="alert">${m}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
    setTimeout(() => { bootstrap.Alert.getOrCreateInstance(el.querySelector('.alert'))?.close(); }, 4000);
}
</script>
@endsection
