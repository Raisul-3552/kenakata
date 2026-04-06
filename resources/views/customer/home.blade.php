@extends('layouts.customer')

@section('customer_content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-success">Available Products</h2>
    </div>
</div>
<div class="row" id="product-list">
    <!-- Products loaded via JS -->
</div>
@endsection

@section('customer_scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadProducts();
    });

    function loadProducts() {
        const productList = document.getElementById('product-list');
        
        // This is a public/customer route, depends how api is setup. Customer is logged in.
        fetch(`${API_URL}/customer/products`, {
            headers: getHeaders()
        })
        .then(res => {
            if(res.status === 401 || res.status === 403) logout();
            return res.json();
        })
        .then(data => {
            if(data && data.length > 0) {
                productList.innerHTML = data.map(prod => `
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <h5 class="card-title">${prod.ProductName}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">${prod.Brand}</h6>
                                <p class="card-text text-success fw-bold">Tk ${prod.Price}</p>
                                <p class="card-text small">Stock: ${prod.Stock}</p>
                                <button class="btn btn-kenakata btn-sm" onclick='addToCart({id: ${prod.ProductID}, name: "${prod.ProductName}", price: ${prod.Price}})'>Add to Cart</button>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                productList.innerHTML = '<div class="col-12"><p>No products available.</p></div>';
            }
        })
        .catch(err => console.error(err));
    }
</script>
@endsection
