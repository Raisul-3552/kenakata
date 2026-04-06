<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Get all products with their categories and current offers
        $products = Product::with(['category', 'offers', 'detail'])->get();
        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'offers', 'detail'])->find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }
}
