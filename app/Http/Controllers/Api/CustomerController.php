<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    // ─── Profile ──────────────────────────────────────────────────────────────

    public function getProfile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $customer = $request->user();
        $customer->update($request->only(['CustomerName', 'Phone', 'Address']));
        return response()->json(['message' => 'Profile updated successfully', 'customer' => $customer]);
    }

    public function changePassword(Request $request)
    {
        $customer = $request->user();

        if (!Hash::check($request->current_password, $customer->Password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        if (!$request->new_password || strlen($request->new_password) < 6) {
            return response()->json(['message' => 'New password must be at least 6 characters'], 422);
        }

        $customer->Password = Hash::make($request->new_password);
        $customer->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    // ─── Cart ─────────────────────────────────────────────────────────────────

    /**
     * Get or create the customer's cart, return items with product info.
     */
    private function getOrCreateCart($customerID)
    {
        return Cart::firstOrCreate(['CustomerID' => $customerID]);
    }

    public function getCart(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);
        $items = CartItem::with('product.detail')
            ->where('CartID', $cart->CartID)
            ->get()
            ->map(function ($item) {
                return [
                    'id'       => $item->ProductID,
                    'name'     => $item->product->ProductName ?? 'Unknown',
                    'price'    => (float) $item->UnitPrice,
                    'quantity' => $item->Quantity,
                    'image'    => $item->product->detail->Image ?? null,
                ];
            });

        return response()->json($items);
    }

    /**
     * Sync entire cart from client (merge localStorage on login).
     * Body: [ { id, name, price, quantity }, ... ]
     */
    public function syncCart(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);
        $items = $request->items ?? [];

        // Clear existing items and rebuild
        CartItem::where('CartID', $cart->CartID)->delete();

        foreach ($items as $item) {
            CartItem::create([
                'CartID'    => $cart->CartID,
                'ProductID' => $item['id'],
                'Quantity'  => $item['quantity'],
                'UnitPrice' => $item['price'],
            ]);
        }

        return response()->json(['message' => 'Cart synced successfully']);
    }

    /**
     * Add one item (or increment quantity if already in cart).
     * Body: { id, name, price, quantity }
     */
    public function addToCart(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);

        $existing = CartItem::where('CartID', $cart->CartID)
            ->where('ProductID', $request->id)
            ->first();

        if ($existing) {
            $existing->Quantity += ($request->quantity ?? 1);
            $existing->save();
        } else {
            CartItem::create([
                'CartID'    => $cart->CartID,
                'ProductID' => $request->id,
                'Quantity'  => $request->quantity ?? 1,
                'UnitPrice' => $request->price,
            ]);
        }

        return response()->json(['message' => 'Item added to cart']);
    }

    /**
     * Update quantity of a specific product in cart.
     * Body: { quantity }
     */
    public function updateCartItem(Request $request, $productId)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);

        $item = CartItem::where('CartID', $cart->CartID)
            ->where('ProductID', $productId)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        if ($request->quantity <= 0) {
            $item->delete();
        } else {
            $item->Quantity = $request->quantity;
            $item->save();
        }

        return response()->json(['message' => 'Cart updated']);
    }

    /**
     * Remove one product from cart.
     */
    public function removeCartItem(Request $request, $productId)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);

        CartItem::where('CartID', $cart->CartID)
            ->where('ProductID', $productId)
            ->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    /**
     * Clear all items from cart.
     */
    public function clearCart(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);
        CartItem::where('CartID', $cart->CartID)->delete();
        return response()->json(['message' => 'Cart cleared']);
    }

    // ─── Orders ───────────────────────────────────────────────────────────────

    public function placeOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'CustomerID'  => $request->user()->CustomerID,
                'CouponID'    => $request->CouponID,
                'OrderStatus' => 'Pending',
                'TotalAmount' => $request->TotalAmount,
                'OrderDate'   => now()->format('Y-m-d'),
                'Address'     => $request->Address,
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['ProductID']);
                if (!$product) {
                    throw new \Exception("Product ID {$item['ProductID']} not found.");
                }
                if ($product->Stock < $item['Quantity']) {
                    throw new \Exception("Insufficient stock for {$product->ProductName}. Only {$product->Stock} left.");
                }

                $product->Stock -= $item['Quantity'];
                $product->save();

                OrderItem::create([
                    'OrderID'   => $order->OrderID,
                    'ProductID' => $item['ProductID'],
                    'Quantity'  => $item['Quantity'],
                    'UnitPrice' => $item['UnitPrice'],
                ]);
            }

            // Clear DB cart after order placed
            $cart = Cart::where('CustomerID', $request->user()->CustomerID)->first();
            if ($cart) {
                CartItem::where('CartID', $cart->CartID)->delete();
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
        $orders = Order::with(['items.product.detail', 'delivery'])
            ->where('CustomerID', $request->user()->CustomerID)
            ->where('OrderStatus', '!=', 'Cancelled')
            ->orderByDesc('OrderDate')
            ->get();
        return response()->json($orders);
    }

    public function cancelOrder(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $order = Order::with('items')->where('OrderID', $id)
                ->where('CustomerID', $request->user()->CustomerID)
                ->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($order->OrderStatus !== 'Pending') {
                return response()->json(['message' => 'Only pending orders can be cancelled'], 400);
            }

            foreach ($order->items as $item) {
                $product = Product::find($item->ProductID);
                if ($product) {
                    $product->Stock += $item->Quantity;
                    $product->save();
                }
            }

            $order->OrderStatus = 'Cancelled';
            $order->save();

            DB::commit();
            return response()->json(['message' => 'Order cancelled successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error cancelling order: ' . $e->getMessage()], 500);
        }
    }

    // ─── Coupons ──────────────────────────────────────────────────────────────

    public function validateCoupon(Request $request)
    {
        // Try stored procedure first, fallback to direct query
        try {
            $coupon = DB::select('EXEC ValidateCoupon ?', [$request->CouponCode]);
        } catch (\Exception $e) {
            $coupon = [];
        }

        // Fallback: direct query if stored procedure fails or returns nothing
        if (empty($coupon)) {
            $today = now()->format('Y-m-d');
            $couponModel = Coupon::where('CouponCode', $request->CouponCode)
                ->where('StartDate', '<=', $today)
                ->where('EndDate', '>=', $today)
                ->first();

            if ($couponModel) {
                return response()->json([
                    'valid'  => true,
                    'coupon' => $couponModel,
                ]);
            }

            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon']);
        }

        return response()->json(['valid' => true, 'coupon' => $coupon[0]]);
    }
}
