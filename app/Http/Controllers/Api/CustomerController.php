<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function getProfile(Request $request)
    {
        return response()->json($request->user());
    }

    public function placeOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'CustomerID' => $request->user()->CustomerID,
                'CouponID' => $request->CouponID,
                'OrderStatus' => 'Pending',
                'TotalAmount' => $request->TotalAmount,
                'OrderDate' => now()->format('Y-m-d'),
                'Address' => $request->Address,
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'OrderID' => $order->OrderID,
                    'ProductID' => $item['ProductID'],
                    'Quantity' => $item['Quantity'],
                    'UnitPrice' => $item['UnitPrice'],
                ]);
            }

            DB::commit();
            return response()->json($order, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getOrderHistory(Request $request)
    {
        $orders = Order::with(['items.product', 'delivery'])
            ->where('CustomerID', $request->user()->CustomerID)
            ->get();
        return response()->json($orders);
    }

    public function validateCoupon(Request $request)
    {
        // Using Stored Procedure defined in Step 1
        $coupon = DB::select('EXEC ValidateCoupon ?', [$request->CouponCode]);
        
        if (count($coupon) > 0) {
            return response()->json(['valid' => true, 'coupon' => $coupon[0]]);
        }
        
        return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon']);
    }
}
