<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        // MSSQL Query: Get all products with their categories and details
        $products = DB::select("
            SELECT
                p.ProductID, p.ProductName, p.Price, p.Stock, p.CategoryID, p.Brand,
                c.CategoryName, c.Description as CategoryDescription,
                COALESCE(pd.Image, NULL) as Image,
                COALESCE(pd.Description, NULL) as DetailDescription,
                COALESCE(pd.Specification, NULL) as Specification,
                COALESCE(pd.Warranty, NULL) as Warranty
            FROM Product p
            LEFT JOIN Category c ON p.CategoryID = c.CategoryID
            LEFT JOIN ProductDetails pd ON p.ProductID = pd.ProductID
            ORDER BY p.ProductID
        ");

        // MSSQL Query: Get all offers
        $offers = DB::select("
            SELECT * FROM Offer
        ");

        // Build response with correct structure
        $result = array_map(function ($product) use ($offers) {
            // Find offers for this product
            $productOffers = array_filter($offers, function ($offer) use ($product) {
                return $offer->ProductID == $product->ProductID;
            });

            return [
                'ProductID' => $product->ProductID,
                'ProductName' => $product->ProductName,
                'Brand' => $product->Brand,
                'Price' => floatval($product->Price),
                'Stock' => intval($product->Stock),
                'CategoryID' => $product->CategoryID,
                'category' => [
                    'CategoryID' => $product->CategoryID,
                    'CategoryName' => $product->CategoryName,
                    'Description' => $product->CategoryDescription,
                ],
                'detail' => [
                    'Image' => $product->Image,
                    'Description' => $product->DetailDescription,
                    'Specification' => $product->Specification,
                    'Warranty' => $product->Warranty,
                ],
                'offers' => array_values($productOffers),
            ];
        }, $products);

        return response()->json($result);
    }

    public function show($id)
    {
        // MSSQL Query: Get product with category and details
        $product = DB::selectOne("
            SELECT
                p.ProductID, p.ProductName, p.Price, p.Stock, p.CategoryID, p.Brand,
                c.CategoryName, c.Description as CategoryDescription,
                COALESCE(pd.Image, NULL) as Image,
                COALESCE(pd.Description, NULL) as DetailDescription,
                COALESCE(pd.Specification, NULL) as Specification,
                COALESCE(pd.Warranty, NULL) as Warranty
            FROM Product p
            LEFT JOIN Category c ON p.CategoryID = c.CategoryID
            LEFT JOIN ProductDetails pd ON p.ProductID = pd.ProductID
            WHERE p.ProductID = ?
        ", [$id]);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // MSSQL Query: Get offers for this product
        $offers = DB::select("
            SELECT * FROM Offer WHERE ProductID = ?
        ", [$id]);

        // Format response
        $response = [
            'ProductID' => $product->ProductID,
            'ProductName' => $product->ProductName,
            'Brand' => $product->Brand,
            'Price' => floatval($product->Price),
            'Stock' => intval($product->Stock),
            'CategoryID' => $product->CategoryID,
            'category' => [
                'CategoryID' => $product->CategoryID,
                'CategoryName' => $product->CategoryName,
                'Description' => $product->CategoryDescription,
            ],
            'detail' => [
                'Image' => $product->Image,
                'Description' => $product->DetailDescription,
                'Specification' => $product->Specification,
                'Warranty' => $product->Warranty,
            ],
            'offers' => $offers,
        ];

        return response()->json($response);
    }
}
