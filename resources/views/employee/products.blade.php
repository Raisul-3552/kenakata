@extends('layouts.employee')

@section('title', 'Products')

@section('employee_styles')
<style>
    .product-card {
        background: rgba(22, 33, 62, 0.85) !important;
        border: 1px solid rgba(255,255,255,0.08) !important;
        border-radius: 14px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.35) !important;
    }
    .product-img {
        width: 100%; height: 180px; object-fit: cover;
        border-radius: 10px 10px 0 0;
        background: linear-gradient(135deg, #0f3460, #1a1a2e);
    }
    .product-img-placeholder {
        width: 100%; height: 180px;
        background: linear-gradient(135deg, #0f3460, #1a1a2e);
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem; border-radius: 10px 10px 0 0;
    }
    .price-tag { color: #2ecc71; font-size: 1.15rem; font-weight: 700; }
    .stock-badge { font-size: 0.75rem; }
    .offer-badge {
        position: absolute; top: 10px; left: 10px;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: #fff; border-radius: 20px; padding: 2px 10px; font-size: 0.75rem; font-weight: 700;
    }
    .section-title {
        font-size: 0.85rem; font-weight: 700; letter-spacing: 0.06em;
        color: #2ecc71; text-transform: uppercase;
        border-bottom: 1px solid rgba(40,167,69,0.2); padding-bottom: 0.4rem; margin-bottom: 1rem;
    }
    .modal-dark { background: rgba(15, 27, 52, 0.98) !important; color: #fff; }
    .modal-dark .modal-header { background: #0f3460; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .search-box { background: rgba(255,255,255,0.07) !important; border: 1px solid rgba(255,255,255,0.15) !important; color: #fff !important; border-radius: 10px; }
    .search-box::placeholder { color: rgba(255,255,255,0.35); }
    .search-box:focus { background: rgba(255,255,255,0.12) !important; box-shadow: 0 0 0 0.2rem rgba(40,167,69,0.2) !important; border-color: #28a745 !important; }
    .btn-add-main { background: linear-gradient(135deg,#28a745,#20c997); border: none; border-radius: 10px; font-weight:600; }
    .btn-add-main:hover { opacity: 0.9; transform: translateY(-1px); }
</style>
@endsection

@section('employee_content')
<div id="prod-alert"></div>

{{-- Top Bar --}}
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-white">📦 Product Management</h4>
        <small class="text-white-50">Add, edit, apply discounts to products</small>
    </div>
    <div class="d-flex gap-2">
        <input type="text" class="form-control search-box" style="width:220px" id="product-search" placeholder="🔍 Search products...">
        <button class="btn btn-success btn-add-main text-white px-4" onclick="openAddModal()">+ Add Product</button>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center" style="background:linear-gradient(145deg,#1b3a4b,#102a3a);">
            <div class="h4 text-info mb-1" id="stat-total">—</div>
            <small class="text-white-50">Total Products</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center" style="background:linear-gradient(145deg,#1a4d2e,#143d24);">
            <div class="h4 text-success mb-1" id="stat-instock">—</div>
            <small class="text-white-50">In Stock</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center" style="background:linear-gradient(145deg,#3d2e1a,#2d2214);">
            <div class="h4 text-warning mb-1" id="stat-lowstock">—</div>
            <small class="text-white-50">Low Stock (&lt;5)</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center" style="background:linear-gradient(145deg,#3d1a1a,#2d1414);">
            <div class="h4 text-danger mb-1" id="stat-outstock">—</div>
            <small class="text-white-50">Out of Stock</small>
        </div>
    </div>
</div>

{{-- Products Grid --}}
<div class="row g-3" id="products-grid">
    <div class="col-12 text-center py-5">
        <div class="spinner-border text-success"></div>
        <p class="mt-2 text-white-50">Loading products...</p>
    </div>
</div>

{{-- == ADD PRODUCT MODAL == --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-dark border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">+ Add New Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="section-title">Basic Info</div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Product Name *</label>
                        <input type="text" class="form-control" id="add-name" placeholder="e.g. Samsung Galaxy S24">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Brand *</label>
                        <input type="text" class="form-control" id="add-brand" placeholder="e.g. Samsung">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-white-50 small">Category *</label>
                        <select class="form-select" id="add-category">
                            <option value="">Loading...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-white-50 small">Price (৳) *</label>
                        <input type="number" class="form-control" id="add-price" placeholder="0.00" step="0.01" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-white-50 small">Stock Quantity *</label>
                        <input type="number" class="form-control" id="add-stock" placeholder="0" min="0">
                    </div>
                </div>
                <div class="section-title">Details</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-white-50 small">Description *</label>
                        <textarea class="form-control" id="add-desc" rows="2" placeholder="Brief product description"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-white-50 small">Specification *</label>
                        <textarea class="form-control" id="add-spec" rows="2" placeholder="Technical specifications"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Warranty</label>
                        <input type="text" class="form-control" id="add-warranty" placeholder="e.g. 1 Year Manufacturer">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Image URL</label>
                        <input type="text" class="form-control" id="add-image" placeholder="https://...">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success px-4 fw-bold" onclick="submitAddProduct()">✅ Add Product</button>
            </div>
        </div>
    </div>
</div>

{{-- == EDIT PRODUCT MODAL == --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-dark border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">✏️ Edit Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <input type="hidden" id="edit-product-id">
                <div class="section-title">Basic Info</div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Product Name</label>
                        <input type="text" class="form-control" id="edit-name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Brand</label>
                        <input type="text" class="form-control" id="edit-brand">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-white-50 small">Category</label>
                        <select class="form-select" id="edit-category"></select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-white-50 small">Price (৳)</label>
                        <input type="number" class="form-control" id="edit-price" step="0.01" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-white-50 small">Stock</label>
                        <input type="number" class="form-control" id="edit-stock" min="0">
                    </div>
                </div>
                <div class="section-title">Details</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-white-50 small">Description</label>
                        <textarea class="form-control" id="edit-desc" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-white-50 small">Specification</label>
                        <textarea class="form-control" id="edit-spec" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Warranty</label>
                        <input type="text" class="form-control" id="edit-warranty">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Image URL</label>
                        <input type="text" class="form-control" id="edit-image">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success px-4 fw-bold" onclick="submitEditProduct()">💾 Save Changes</button>
            </div>
        </div>
    </div>
</div>

{{-- == OFFER MODAL == --}}
<div class="modal fade" id="offerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modal-dark border-0">
            <div class="modal-header" style="background: linear-gradient(135deg,#e74c3c,#c0392b);">
                <h5 class="modal-title fw-bold">🏷️ Add Discount Offer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <input type="hidden" id="offer-product-id">
                <input type="hidden" id="offer-product-price">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="text-white-50 small">Product:</span>
                    <strong class="text-white" id="offer-product-name">—</strong>
                    <span class="badge bg-success ms-auto" id="offer-product-price-badge">৳0</span>
                </div>
                <div class="row g-3">
                    {{-- Discount Type Toggle --}}
                    <div class="col-12">
                        <label class="form-label text-white-50 small">Discount Type</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="discount-type" id="type-percent" value="percent" checked>
                            <label class="btn btn-outline-warning fw-semibold" for="type-percent">% Percentage</label>
                            <input type="radio" class="btn-check" name="discount-type" id="type-flat" value="flat">
                            <label class="btn btn-outline-danger fw-semibold" for="type-flat">৳ Flat Amount</label>
                        </div>
                    </div>
                    {{-- Percent input --}}
                    <div class="col-12" id="percent-input-group">
                        <label class="form-label text-white-50 small">Discount Percentage *</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="offer-percent" placeholder="e.g. 10" min="1" max="99" step="1">
                            <span class="input-group-text bg-transparent text-white border-secondary">%</span>
                        </div>
                        <div class="form-text mt-1" id="offer-live-preview" style="color:#2ecc71; font-weight:600;"></div>
                    </div>
                    {{-- Flat input (hidden by default) --}}
                    <div class="col-12 d-none" id="flat-input-group">
                        <label class="form-label text-white-50 small">Discount Amount (৳) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent text-white border-secondary">৳</span>
                            <input type="number" class="form-control" id="offer-amount" placeholder="e.g. 500" step="0.01" min="1">
                        </div>
                        <div class="form-text mt-1" id="offer-flat-preview" style="color:#2ecc71; font-weight:600;"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Start Date *</label>
                        <input type="date" class="form-control" id="offer-start">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">End Date *</label>
                        <input type="date" class="form-control" id="offer-end">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger px-4 fw-bold" id="offer-submit-btn" onclick="submitOffer()">🏷️ Apply Offer</button>
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

// --- Load Data ---
function loadProducts() {
    fetch(`${API_URL}/employee/products`, { headers: getHeaders() })
    .then(r => { if(r.status===401) logout(); return r.json(); })
    .then(data => {
        allProducts = Array.isArray(data) ? data : [];
        updateStats(allProducts);
        renderProducts(allProducts);
    })
    .catch(() => {
        document.getElementById('products-grid').innerHTML =
            '<div class="col-12 text-center text-danger py-5">⚠️ Failed to load products</div>';
    });
}

function loadCategories() {
    fetch(`${API_URL}/employee/categories`, { headers: getHeaders() })
    .then(r => r.json())
    .then(data => {
        categories = Array.isArray(data) ? data : [];
        const opts = categories.map(c => `<option value="${c.CategoryID}">${c.CategoryName}</option>`).join('');
        document.getElementById('add-category').innerHTML = '<option value="">Select Category</option>' + opts;
        document.getElementById('edit-category').innerHTML = '<option value="">Select Category</option>' + opts;
    });
}

// --- Render ---
function updateStats(products) {
    document.getElementById('stat-total').textContent    = products.length;
    document.getElementById('stat-instock').textContent  = products.filter(p => p.Stock > 5).length;
    document.getElementById('stat-lowstock').textContent = products.filter(p => p.Stock > 0 && p.Stock <= 5).length;
    document.getElementById('stat-outstock').textContent = products.filter(p => p.Stock === 0).length;
}

function renderProducts(products) {
    const grid = document.getElementById('products-grid');
    if (!products.length) {
        grid.innerHTML = '<div class="col-12 text-center py-5 text-white-50">No products found. Click "+ Add Product" to get started.</div>';
        return;
    }
    grid.innerHTML = products.map(p => {
        const catName   = p.category ? p.category.CategoryName : '—';
        const hasOffer  = p.offers && p.offers.length > 0;
        const imgSrc    = p.detail && p.detail.Image ? p.detail.Image : null;
        const stockBadge = p.Stock === 0
            ? '<span class="badge bg-danger stock-badge">Out of Stock</span>'
            : p.Stock <= 5
                ? `<span class="badge bg-warning text-dark stock-badge">Low: ${p.Stock}</span>`
                : `<span class="badge bg-success stock-badge">${p.Stock} in stock</span>`;

        return `
        <div class="col-sm-6 col-lg-4 col-xl-3 product-item">
            <div class="card product-card h-100">
                <div class="position-relative">
                    ${imgSrc
                        ? `<img src="${imgSrc}" class="product-img" alt="${p.ProductName}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                           <div class="product-img-placeholder" style="display:none;">📦</div>`
                        : `<div class="product-img-placeholder">📦</div>`
                    }
                    ${hasOffer ? `<span class="offer-badge">🏷️ Offer Active</span>` : ''}
                </div>
                <div class="card-body p-3 d-flex flex-column">
                    <div class="mb-1">
                        <span class="badge bg-primary bg-opacity-75 small">${catName}</span>
                        <span class="ms-1 badge bg-secondary bg-opacity-75 small">${p.Brand}</span>
                    </div>
                    <h6 class="fw-bold text-white mb-1" style="font-size:0.9rem; line-height:1.3;">${p.ProductName}</h6>
                    <div class="d-flex align-items-center justify-content-between mt-auto pt-2">
                        <span class="price-tag">৳${parseFloat(p.Price).toLocaleString()}</span>
                        ${stockBadge}
                    </div>
                    <div class="d-flex gap-1 mt-3">
                        <button class="btn btn-sm btn-outline-light flex-fill" onclick='openEditModal(${JSON.stringify(p)})' title="Edit">✏️</button>
                        <button class="btn btn-sm btn-outline-warning flex-fill" onclick='openOfferModal(${JSON.stringify({ProductID:p.ProductID, ProductName:p.ProductName, Price:p.Price})})' title="Add Offer">🏷️</button>
                        <button class="btn btn-sm btn-outline-danger flex-fill" onclick="deleteProduct(${p.ProductID}, this)" title="Delete">🗑️</button>
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
    renderProducts(filtered);
}

// --- Add Product ---
function openAddModal() {
    document.getElementById('add-name').value = '';
    document.getElementById('add-brand').value = '';
    document.getElementById('add-price').value = '';
    document.getElementById('add-stock').value = '';
    document.getElementById('add-desc').value = '';
    document.getElementById('add-spec').value = '';
    document.getElementById('add-warranty').value = '';
    document.getElementById('add-image').value = '';
    new bootstrap.Modal(document.getElementById('addModal')).show();
}

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

    if (!body.ProductName || !body.Brand || !body.CategoryID || !body.Price || !body.Stock) {
        showAlert('Please fill all required fields', 'warning'); return;
    }

    const btn = document.querySelector('#addModal .btn-success');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';

    fetch(`${API_URL}/employee/products`, {
        method: 'POST', headers: getHeaders(), body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
        showAlert('✅ Product added successfully!', 'success');
        loadProducts();
    })
    .catch(() => showAlert('Failed to add product', 'danger'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '✅ Add Product'; });
}

// --- Edit Product ---
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
    // Set category
    const sel = document.getElementById('edit-category');
    if (sel) sel.value = p.CategoryID;
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

    const btn = document.querySelector('#editModal .btn-success');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    fetch(`${API_URL}/employee/products/${id}`, {
        method: 'PUT', headers: getHeaders(), body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
        showAlert('✅ Product updated successfully!', 'success');
        loadProducts();
    })
    .catch(() => showAlert('Failed to update product', 'danger'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '💾 Save Changes'; });
}

// --- Delete Product ---
function deleteProduct(id, btn) {
    if (!confirm('Delete this product? This cannot be undone.')) return;
    btn.disabled = true;
    fetch(`${API_URL}/employee/products/${id}`, { method:'DELETE', headers: getHeaders() })
    .then(r => r.json())
    .then(() => { showAlert('🗑️ Product deleted', 'success'); loadProducts(); })
    .catch(() => { showAlert('Failed to delete product', 'danger'); btn.disabled = false; });
}

// --- Offer ---
function openOfferModal(p) {
    document.getElementById('offer-product-id').value    = p.ProductID;
    document.getElementById('offer-product-price').value = p.Price;
    document.getElementById('offer-product-name').textContent  = p.ProductName;
    document.getElementById('offer-product-price-badge').textContent = '৳' + parseFloat(p.Price).toLocaleString();
    document.getElementById('offer-percent').value = '';
    document.getElementById('offer-amount').value  = '';
    document.getElementById('offer-live-preview').textContent = '';
    document.getElementById('offer-flat-preview').textContent = '';
    // Default to percent mode
    document.getElementById('type-percent').checked = true;
    document.getElementById('percent-input-group').classList.remove('d-none');
    document.getElementById('flat-input-group').classList.add('d-none');
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('offer-start').value = today;
    document.getElementById('offer-end').value   = today;
    new bootstrap.Modal(document.getElementById('offerModal')).show();
}

// Discount type toggle
document.querySelectorAll('input[name="discount-type"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const isPercent = document.getElementById('type-percent').checked;
        document.getElementById('percent-input-group').classList.toggle('d-none', !isPercent);
        document.getElementById('flat-input-group').classList.toggle('d-none', isPercent);
        document.getElementById('offer-live-preview').textContent = '';
        document.getElementById('offer-flat-preview').textContent = '';
    });
});

// Live percent preview
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('offer-percent').addEventListener('input', () => {
        const price   = parseFloat(document.getElementById('offer-product-price').value) || 0;
        const pct     = parseFloat(document.getElementById('offer-percent').value) || 0;
        const saving  = (price * pct / 100).toFixed(2);
        const newPrice = (price - saving).toFixed(2);
        document.getElementById('offer-live-preview').textContent =
            pct > 0 && price > 0 ? `৳${parseFloat(saving).toLocaleString()} off → New price: ৳${parseFloat(newPrice).toLocaleString()}` : '';
    });
    document.getElementById('offer-amount').addEventListener('input', () => {
        const price  = parseFloat(document.getElementById('offer-product-price').value) || 0;
        const flat   = parseFloat(document.getElementById('offer-amount').value) || 0;
        const newPrice = Math.max(0, price - flat).toFixed(2);
        document.getElementById('offer-flat-preview').textContent =
            flat > 0 && price > 0 ? `৳${flat.toLocaleString()} off → New price: ৳${parseFloat(newPrice).toLocaleString()}` : '';
    });
});

function submitOffer() {
    const isPercent = document.getElementById('type-percent').checked;
    const price     = parseFloat(document.getElementById('offer-product-price').value) || 0;
    let discountAmount;

    if (isPercent) {
        const pct = parseFloat(document.getElementById('offer-percent').value);
        if (!pct || pct < 1 || pct > 99) { showAlert('Enter a valid percentage between 1–99', 'warning'); return; }
        discountAmount = parseFloat((price * pct / 100).toFixed(2));
    } else {
        discountAmount = parseFloat(document.getElementById('offer-amount').value);
        if (!discountAmount || discountAmount <= 0) { showAlert('Enter a valid discount amount', 'warning'); return; }
        if (discountAmount >= price) { showAlert('Discount cannot be more than or equal to the product price', 'warning'); return; }
    }

    const startDate = document.getElementById('offer-start').value;
    const endDate   = document.getElementById('offer-end').value;
    if (!startDate || !endDate) { showAlert('Please select start and end dates', 'warning'); return; }
    if (endDate < startDate) { showAlert('End date must be after start date', 'warning'); return; }

    const body = {
        ProductID:      document.getElementById('offer-product-id').value,
        DiscountAmount: discountAmount,
        StartDate:      startDate,
        EndDate:        endDate,
    };

    const btn = document.getElementById('offer-submit-btn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Applying...';

    fetch(`${API_URL}/employee/offers`, {
        method: 'POST', headers: getHeaders(), body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('offerModal')).hide();
        showAlert('🏷️ Offer applied successfully!', 'success');
        loadProducts();
    })
    .catch(() => showAlert('Failed to apply offer', 'danger'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '🏷️ Apply Offer'; });
}

// --- Utility ---
function showAlert(msg, type) {
    const el = document.getElementById('prod-alert');
    el.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show shadow-sm" role="alert">
        ${msg} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    setTimeout(() => { el.innerHTML = ''; }, 5000);
}
</script>
@endsection
