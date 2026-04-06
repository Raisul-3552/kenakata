@extends('layouts.customer')

@section('customer_content')
<div class="container">
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="mb-2">Quality Products</h1>
            <p class="text-muted">Browse our collection of premium items</p>
            <div style="height: 4px; width: 60px; background-color: var(--accent-cyan); border-radius: 2px;"></div>
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

    .price-text {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--accent-cyan);
    }

    .detail-item {
        font-size: 0.85rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>
@endsection

@section('customer_scripts')
<script>
    document.addEventListener('DOMContentLoaded', loadProducts);

    function loadProducts() {
        const productList = document.getElementById('product-list');
        
        fetch(`${API_URL}/customer/products`, { headers: getHeaders() })
        .then(res => {
            if(res.status === 401 || res.status === 403) logout();
            return res.json();
        })
        .then(data => {
            if(data && data.length > 0) {
                productList.innerHTML = data.map(prod => {
                    const detail = prod.detail || {};
                    return `
                    <div class="col-md-4 col-sm-6">
                        <div class="dark-card product-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1 text-white">${prod.ProductName}</h5>
                                    <span class="text-muted small">${prod.category ? prod.category.CategoryName : 'Miscellaneous'}</span>
                                </div>
                                <span class="product-badge">${prod.Brand}</span>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-muted small mb-3">${detail.Description || 'High quality product from Kenakata.'}</p>
                                ${detail.Specification ? `<div class="detail-item mb-1"><span class="text-info">●</span> ${detail.Specification}</div>` : ''}
                                ${detail.Warranty ? `<div class="detail-item"><span class="text-warning">●</span> ${detail.Warranty}</div>` : ''}
                            </div>

                            <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                                <div class="price-text">Tk ${prod.Price}</div>
                                <div class="text-muted small">Stock: ${prod.Stock}</div>
                            </div>
                            
                            <button class="btn btn-cyan mt-4 py-2" onclick='addToCart({id: ${prod.ProductID}, name: "${prod.ProductName}", price: ${prod.Price}})'>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                `}).join('');
            } else {
                productList.innerHTML = '<div class="col-12 py-5 text-center"><p class="text-muted">No products found.</p></div>';
            }
        })
        .catch(err => {
            console.error(err);
            productList.innerHTML = '<div class="col-12 py-5 text-center text-danger">Error loading items</div>';
        });
    }
</script>
@endsection
