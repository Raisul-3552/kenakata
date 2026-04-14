@extends('layouts.customer')

@section('title', 'Products')

@section('customer_styles')
<style>
    .product-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
        border-color: var(--accent-cyan) !important;
    }

    .product-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 12px;
        background-color: #020617;
    }

    .product-img-placeholder {
        width: 100%;
        height: 180px;
        border-radius: 8px;
        margin-bottom: 12px;
        background-color: #020617;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        border: 1px solid var(--border-color);
    }

    .product-badge {
        background-color: rgba(14, 165, 233, 0.1);
        color: var(--accent-cyan);
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        border: 1px solid rgba(14, 165, 233, 0.2);
    }

    .offer-badge {
        background-color: rgba(239, 68, 68, 0.15);
        color: #f87171;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    .price-text {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--accent-cyan);
    }

    .price-original {
        font-size: 0.88rem;
        color: var(--text-secondary);
        text-decoration: line-through;
    }

    .detail-item {
        font-size: 0.85rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-panel {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .filter-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 8px;
    }

    .category-select {
        background: rgba(2, 6, 23, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.25);
        color: #f8fafc;
    }

    .category-select:focus {
        border-color: var(--accent-cyan);
        box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.15);
        background: rgba(2, 6, 23, 0.95);
        color: #f8fafc;
    }
</style>
@endsection

@section('customer_content')
<div class="container">
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="mb-2">Quality Products</h1>
            <p class="text-muted">Browse our collection of premium items</p>
            <div style="height: 4px; width: 60px; background-color: var(--accent-cyan); border-radius: 2px;"></div>
        </div>
    </div>

    <div class="filter-panel">
        <div class="row align-items-end g-3">
            <div class="col-md-4">
                <label for="category-filter" class="filter-label">Filter by Category</label>
                <select id="category-filter" class="form-select category-select">
                    <option value="all">All Categories</option>
                </select>
            </div>
            <div class="col-md-8 text-md-end">
                <div class="text-muted small" id="category-filter-summary">Showing all products</div>
            </div>
        </div>
    </div>

    <div class="row g-4" id="product-list">
        <!-- Products loaded via JS -->
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customer_scripts')
<script>
    let allProducts = [];

    document.addEventListener('DOMContentLoaded', loadProducts);

    function formatDetailLabel(key) {
        const normalized = key.toLowerCase();
        const knownLabels = {
            specification: 'Specification',
            warranty: 'Warranty',
            warrenty: 'Warranty',
            warenty: 'Warranty',
            description: 'Description',
            brand: 'Brand',
            model: 'Model',
            color: 'Color',
            weight: 'Weight',
            size: 'Size'
        };

        if (knownLabels[normalized]) return knownLabels[normalized];

        return key
            .replace(/([a-z0-9])([A-Z])/g, '$1 $2')
            .replace(/[_-]+/g, ' ')
            .replace(/\b\w/g, ch => ch.toUpperCase())
            .trim();
    }

    function renderDetailAttributes(detail) {
        const excludedKeys = ['productid', 'description', 'image'];

        return Object.entries(detail)
            .filter(([key, value]) => !excludedKeys.includes(key.toLowerCase()) && value)
            .map(([key, value]) => {
                const label = formatDetailLabel(key);
                return `<div class="detail-item mb-1"><span class="text-info">●</span> <span><strong>${label}:</strong> ${value}</span></div>`;
            })
            .join('');
    }

    function getActiveOffer(offers) {
        if (!offers || offers.length === 0) return null;
        const today = new Date().toISOString().split('T')[0];
        return offers.find(o => o.StartDate <= today && o.EndDate >= today) || null;
    }

    function getProductCategoryName(prod) {
        return prod?.category?.CategoryName || 'Miscellaneous';
    }

    function populateCategoryFilter(products) {
        const select = document.getElementById('category-filter');
        const categories = [...new Set(products.map(getProductCategoryName))].sort((a, b) => a.localeCompare(b));

        select.innerHTML = '<option value="all">All Categories</option>' + categories
            .map(category => `<option value="${category.replace(/"/g, '&quot;')}">${category}</option>`)
            .join('');

        select.onchange = renderFilteredProducts;
    }

    function renderFilteredProducts() {
        const productList = document.getElementById('product-list');
        const selectedCategory = document.getElementById('category-filter').value;
        const summary = document.getElementById('category-filter-summary');

        const filteredProducts = selectedCategory === 'all'
            ? allProducts
            : allProducts.filter(prod => getProductCategoryName(prod) === selectedCategory);

        summary.textContent = selectedCategory === 'all'
            ? `Showing all products (${filteredProducts.length})`
            : `Showing ${filteredProducts.length} product${filteredProducts.length === 1 ? '' : 's'} in ${selectedCategory}`;

        if (filteredProducts.length === 0) {
            productList.innerHTML = '<div class="col-12 py-5 text-center"><p class="text-muted">No products found for this category.</p></div>';
            return;
        }

        productList.innerHTML = filteredProducts.map(prod => {
            const detail = prod.detail || {};
            const offer  = getActiveOffer(prod.offers);

            const originalPrice = parseFloat(prod.Price);
            const discount      = offer ? parseFloat(offer.DiscountAmount) : 0;
            const finalPrice    = Math.max(0, originalPrice - discount);

            const priceHtml = offer
                ? `<div class="d-flex align-items-center gap-2 flex-wrap">
                       <div class="price-text">Tk ${finalPrice.toFixed(0)}</div>
                       <div class="price-original">Tk ${originalPrice.toFixed(0)}</div>
                       <span class="offer-badge">-Tk ${discount.toFixed(0)} OFF</span>
                   </div>`
                : `<div class="price-text">Tk ${originalPrice.toFixed(0)}</div>`;

            const imgHtml = detail.Image
                ? `<img src="${detail.Image}" alt="${prod.ProductName}" class="product-img" onerror="this.outerHTML='<div class=\\'product-img-placeholder\\'>📦</div>'">`
                : `<div class="product-img-placeholder">📦</div>`;

            return `
            <div class="col-md-4 col-sm-6">
                <div class="dark-card product-card p-4">
                    ${imgHtml}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1 text-white">${prod.ProductName}</h5>
                            <span class="text-muted small">${getProductCategoryName(prod)}</span>
                        </div>
                        <span class="product-badge">${prod.Brand}</span>
                    </div>

                    <div class="mb-4">
                        <p class="text-muted small mb-3">${detail.Description || 'High quality product from Kenakata.'}</p>
                        ${renderDetailAttributes(detail)}
                    </div>

                    <div class="mt-auto pt-3 border-top border-secondary border-opacity-25">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            ${priceHtml}
                            <div class="text-muted small">Stock: ${prod.Stock}</div>
                        </div>
                        <button class="btn btn-cyan w-100 py-2" onclick='addToCart({id: ${prod.ProductID}, name: "${prod.ProductName.replace(/"/g,"&quot;")}", price: ${finalPrice}})'>
                            🛒 Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        `}).join('');
    }

    function loadProducts() {
        const productList = document.getElementById('product-list');

        // Use public products endpoint - it returns the same data
        // Will work even if auth has any issues, and customer is authenticated via localStorage token
        fetch(`${API_URL}/products`, { headers: getHeaders() })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}: ${res.statusText}`);
            }
            return res.json();
        })
        .then(data => {
            console.log('Products loaded:', data); // Debug log
            
            if (!data) {
                productList.innerHTML = '<div class="col-12 py-5 text-center"><p class="text-muted">No response from server.</p></div>';
                return;
            }
            
            if (Array.isArray(data) && data.length > 0) {
                allProducts = data;
                populateCategoryFilter(data);
                renderFilteredProducts();
            } else {
                productList.innerHTML = '<div class="col-12 py-5 text-center"><p class="text-muted">No products found. Please check back later.</p></div>';
            }
        })
        .catch(err => {
            console.error('Product loading error:', err);
            productList.innerHTML = `<div class="col-12 py-5 text-center text-danger"><div>Error loading items</div><small class="text-muted">${err.message}</small></div>`;
        });
    }
</script>
@endsection
