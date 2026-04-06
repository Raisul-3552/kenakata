<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Offer;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function getProducts()
    {
        return response()->json(Product::with(['category', 'detail'])->get());
    }

    public function addProduct(Request $request)
    {
        $product = Product::create([
            'EmployeeID' => $request->user()->EmployeeID,
            'CategoryID' => $request->CategoryID,
            'ProductName' => $request->ProductName,
            'Brand' => $request->Brand,
            'Price' => $request->Price,
            'Stock' => $request->Stock,
        ]);

        ProductDetail::create([
            'ProductID' => $product->ProductID,
            'Description' => $request->Description,
            'Specification' => $request->Specification,
            'Warranty' => $request->Warranty,
            'Image' => $request->Image,
        ]);

        return response()->json($product, 201);
    }

    public function editProduct(Request $request, $id)
    {
        $product = Product::where('ProductID', $id)->first();
        $product->update($request->only(['ProductName', 'Brand', 'Price', 'Stock', 'CategoryID']));
        
        $detail = ProductDetail::where('ProductID', $id)->first();
        $detail->update($request->only(['Description', 'Specification', 'Warranty', 'Image']));

        return response()->json($product);
    }

    public function deleteProduct($id)
    {
        Product::where('ProductID', $id)->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function addOffer(Request $request)
    {
        $offer = Offer::create($request->all());
        return response()->json($offer, 201);
    }

    public function addCoupon(Request $request)
    {
        $coupon = Coupon::create($request->all());
        return response()->json($coupon, 201);
    }

    public function getOrders()
    {
        return response()->json(Order::with(['customer', 'items.product', 'delivery.deliveryMan'])->get());
    }

    public function confirmOrder($id, Request $request)
    {
        // Using Stored Procedure defined in Step 1
        DB::statement('EXEC ConfirmOrder ?, ?', [$id, $request->user()->EmployeeID]);
        return response()->json(['message' => 'Order confirmed successfully']);
    }

    public function cancelOrder($id)
    {
        Order::where('OrderID', $id)->update(['OrderStatus' => 'Cancelled']);
        return response()->json(['message' => 'Order cancelled successfully']);
    }

    public function getAvailableDeliveryMen()
    {
        return response()->json(DeliveryMan::where('Status', 'Available')->get());
    }

    public function assignDelivery($id, Request $request)
    {
        // Using Stored Procedure defined in Step 1
        DB::statement('EXEC AssignDelivery ?, ?', [$id, $request->DelManID]);
        
        // Update DeliveryMan status to Busy
        DeliveryMan::where('DelManID', $request->DelManID)->update(['Status' => 'Busy']);
        
        return response()->json(['message' => 'Delivery assigned successfully']);
    }
}
