<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    private function getOrCreateWallet($customerID)
    {
        return Wallet::firstOrCreate(
            ['CustomerID' => $customerID],
            ['Balance' => 0]
        );
    }

    private function getLeastBusyEmployeeId()
    {
        $employee = DB::selectOne("\n            SELECT TOP 1 e.EmployeeID\n            FROM Employee e\n            LEFT JOIN [Order] o\n                ON e.EmployeeID = o.EmployeeID\n               AND o.OrderStatus NOT IN ('Delivered', 'Cancelled')\n            GROUP BY e.EmployeeID\n            ORDER BY COUNT(o.OrderID) ASC, e.EmployeeID ASC\n        ");

        return $employee ? (int) $employee->EmployeeID : null;
    }

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
                    'id' => $item->ProductID,
                    'name' => $item->product->ProductName ?? 'Unknown',
                    'price' => (float) $item->UnitPrice,
                    'quantity' => $item->Quantity,
                    'image' => $item->product->detail->Image ?? null,
                ];
            });

        return response()->json($items);
    }

    public function syncCart(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);
        $items = $request->items ?? [];

        CartItem::where('CartID', $cart->CartID)->delete();

        foreach ($items as $item) {
            CartItem::create([
                'CartID' => $cart->CartID,
                'ProductID' => $item['id'],
                'Quantity' => $item['quantity'],
                'UnitPrice' => $item['price'],
            ]);
        }

        return response()->json(['message' => 'Cart synced successfully']);
    }

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
                'CartID' => $cart->CartID,
                'ProductID' => $request->id,
                'Quantity' => $request->quantity ?? 1,
                'UnitPrice' => $request->price,
            ]);
        }

        return response()->json(['message' => 'Item added to cart']);
    }

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

    public function removeCartItem(Request $request, $productId)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);

        CartItem::where('CartID', $cart->CartID)
            ->where('ProductID', $productId)
            ->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

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
            $wallet = $this->getOrCreateWallet($request->user()->CustomerID);
            $finalTotal = (float) $request->TotalAmount;
            $employeeId = $this->getLeastBusyEmployeeId();

            if (!$employeeId) {
                DB::rollBack();
                return response()->json(['message' => 'No employees are available to handle the order right now.'], 422);
            }

            if ($wallet->Balance < $finalTotal) {
                DB::rollBack();
                return response()->json(['message' => 'Insufficient wallet balance. Please add funds first.'], 422);
            }

            $order = Order::create([
                'CustomerID' => $request->user()->CustomerID,
                'EmployeeID' => $employeeId,
                'CouponID' => $request->CouponID,
                'OrderStatus' => 'Pending',
                'TotalAmount' => $finalTotal,
                'OrderDate' => now()->format('Y-m-d'),
                'Address' => $request->Address,
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
                    'OrderID' => $order->OrderID,
                    'ProductID' => $item['ProductID'],
                    'Quantity' => $item['Quantity'],
                    'UnitPrice' => $item['UnitPrice'],
                ]);
            }

            $cart = Cart::where('CustomerID', $request->user()->CustomerID)->first();

            if ($cart) {
                CartItem::where('CartID', $cart->CartID)->delete();
            }

            $wallet->Balance -= $finalTotal;
            $wallet->save();

            WalletTransaction::create([
                'WalletID' => $wallet->WalletID,
                'Amount' => $finalTotal,
                'TransactionType' => 'Debit',
                'Description' => 'Order payment for Order #' . $order->OrderID,
                'TransactionDate' => now(),
            ]);

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
            $order = Order::with('items')
                ->where('OrderID', $id)
                ->where('CustomerID', $request->user()->CustomerID)
                ->first();

            if (!$order) {
                DB::rollBack();
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($order->OrderStatus !== 'Pending') {
                DB::rollBack();
                return response()->json(['message' => 'Only pending orders can be cancelled'], 400);
            }

            foreach ($order->items as $item) {
                $product = Product::find($item->ProductID);

                if ($product) {
                    $product->Stock += $item->Quantity;
                    $product->save();
                }
            }

            $wallet = $this->getOrCreateWallet($order->CustomerID);
            $refundAmount = (float) $order->TotalAmount;

            $wallet->Balance += $refundAmount;
            $wallet->save();

            WalletTransaction::create([
                'WalletID' => $wallet->WalletID,
                'Amount' => $refundAmount,
                'TransactionType' => 'Credit',
                'Description' => 'Refund for cancelled Order #' . $order->OrderID,
                'TransactionDate' => now(),
            ]);

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
        try {
            $coupon = DB::select('EXEC ValidateCoupon ?', [$request->CouponCode]);
        } catch (\Exception $e) {
            $coupon = [];
        }

        if (empty($coupon)) {
            $today = now()->format('Y-m-d');
            $couponModel = Coupon::where('CouponCode', $request->CouponCode)
                ->where('StartDate', '<=', $today)
                ->where('EndDate', '>=', $today)
                ->first();

            if ($couponModel) {
                return response()->json([
                    'valid' => true,
                    'coupon' => $couponModel,
                ]);
            }

            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon']);
        }

        return response()->json(['valid' => true, 'coupon' => $coupon[0]]);
    }

    public function getWallet(Request $request)
    {
        $wallet = $this->getOrCreateWallet($request->user()->CustomerID);

        $transactions = WalletTransaction::where('WalletID', $wallet->WalletID)
            ->orderByDesc('TransactionDate')
            ->limit(50)
            ->get();

        return response()->json([
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }

    public function addWalletBalance(Request $request)
    {
        $request->validate([
            'Amount' => 'required|numeric|min:1',
            'Description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $wallet = $this->getOrCreateWallet($request->user()->CustomerID);
            $amount = (float) $request->Amount;

            $wallet->Balance += $amount;
            $wallet->save();

            WalletTransaction::create([
                'WalletID' => $wallet->WalletID,
                'Amount' => $amount,
                'TransactionType' => 'Credit',
                'Description' => $request->Description ?: 'Wallet top-up',
                'TransactionDate' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Balance added successfully',
                'wallet' => $wallet,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to add balance: ' . $e->getMessage()], 500);
        }
    }
}
