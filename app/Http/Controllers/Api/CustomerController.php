<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    private function getOrCreateWallet($customerID)
    {
        $wallet = DB::selectOne("SELECT * FROM Wallet WHERE CustomerID = ?", [$customerID]);

        if (!$wallet) {
            DB::insert("INSERT INTO Wallet (CustomerID, Balance) VALUES (?, 0)", [$customerID]);
            $wallet = DB::selectOne("SELECT * FROM Wallet WHERE CustomerID = ?", [$customerID]);
        }

        return $wallet;
    }

    private function getOrCreateCart($customerID)
    {
        $cart = DB::selectOne("SELECT * FROM Cart WHERE CustomerID = ?", [$customerID]);

        if (!$cart) {
            DB::insert("INSERT INTO Cart (CustomerID) VALUES (?)", [$customerID]);
            $cart = DB::selectOne("SELECT * FROM Cart WHERE CustomerID = ?", [$customerID]);
        }

        return $cart;
    }

    private function getLeastBusyEmployeeId()
    {
        $employee = DB::selectOne("\n            SELECT TOP 1 e.EmployeeID\n            FROM Employee e\n            LEFT JOIN [Order] o\n                ON e.EmployeeID = o.EmployeeID\n               AND o.OrderStatus NOT IN ('Delivered', 'Cancelled')\n            GROUP BY e.EmployeeID\n            ORDER BY COUNT(o.OrderID) ASC, e.EmployeeID ASC\n        ");

        return $employee ? (int) $employee->EmployeeID : null;
    }

    public function getProfile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $customer = $request->user();
        $name = $request->input('CustomerName', $customer->CustomerName);
        $phone = $request->input('Phone', $customer->Phone);
        $address = $request->input('Address', $customer->Address);

        DB::update("\n            UPDATE Customer\n            SET CustomerName = ?, Phone = ?, Address = ?\n            WHERE CustomerID = ?\n        ", [$name, $phone, $address, $customer->CustomerID]);

        $updatedCustomer = DB::selectOne("SELECT * FROM Customer WHERE CustomerID = ?", [$customer->CustomerID]);

        return response()->json(['message' => 'Profile updated successfully', 'customer' => $updatedCustomer]);
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

        $hashedPassword = Hash::make($request->new_password);

        DB::update("UPDATE Customer SET Password = ? WHERE CustomerID = ?", [$hashedPassword, $customer->CustomerID]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function getCart(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);

        $items = DB::select("\n            SELECT\n                ci.CartItemID, ci.ProductID, p.ProductName, ci.UnitPrice,\n                ci.Quantity, pd.Image\n            FROM CartItem ci\n            INNER JOIN Product p ON ci.ProductID = p.ProductID\n            LEFT JOIN ProductDetails pd ON p.ProductID = pd.ProductID\n            WHERE ci.CartID = ?\n            ORDER BY ci.CartItemID\n        ", [$cart->CartID]);

        $mappedItems = array_map(function ($item) {
            return [
                'id' => $item->ProductID,
                'name' => $item->ProductName ?? 'Unknown',
                'price' => (float) $item->UnitPrice,
                'quantity' => (int) $item->Quantity,
                'image' => $item->Image ?? null,
            ];
        }, $items);

        return response()->json($mappedItems);
    }

    public function syncCart(Request $request)
    {
        DB::beginTransaction();

        try {
            $cart = $this->getOrCreateCart($request->user()->CustomerID);
            $items = $request->items ?? [];

            DB::delete("DELETE FROM CartItem WHERE CartID = ?", [$cart->CartID]);

            foreach ($items as $item) {
                DB::insert("\n                    INSERT INTO CartItem (CartID, ProductID, Quantity, UnitPrice)\n                    VALUES (?, ?, ?, ?)\n                ", [
                    $cart->CartID,
                    $item['id'],
                    $item['quantity'],
                    $item['price'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Cart synced successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error syncing cart: ' . $e->getMessage()], 500);
        }
    }

    public function addToCart(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);

        $existing = DB::selectOne("\n            SELECT * FROM CartItem WHERE CartID = ? AND ProductID = ?\n        ", [$cart->CartID, $request->id]);

        if ($existing) {
            DB::update("\n                UPDATE CartItem\n                SET Quantity = Quantity + ?\n                WHERE CartID = ? AND ProductID = ?\n            ", [
                $request->quantity ?? 1,
                $cart->CartID,
                $request->id,
            ]);
        } else {
            DB::insert("\n                INSERT INTO CartItem (CartID, ProductID, Quantity, UnitPrice)\n                VALUES (?, ?, ?, ?)\n            ", [
                $cart->CartID,
                $request->id,
                $request->quantity ?? 1,
                $request->price,
            ]);
        }

        return response()->json(['message' => 'Item added to cart']);
    }

    public function updateCartItem(Request $request, $productId)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);

        $item = DB::selectOne("\n            SELECT * FROM CartItem WHERE CartID = ? AND ProductID = ?\n        ", [$cart->CartID, $productId]);

        if (!$item) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        if ($request->quantity <= 0) {
            DB::delete("DELETE FROM CartItem WHERE CartID = ? AND ProductID = ?", [$cart->CartID, $productId]);
        } else {
            DB::update("\n                UPDATE CartItem\n                SET Quantity = ?\n                WHERE CartID = ? AND ProductID = ?\n            ", [$request->quantity, $cart->CartID, $productId]);
        }

        return response()->json(['message' => 'Cart updated']);
    }

    public function removeCartItem(Request $request, $productId)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);

        DB::delete("\n            DELETE FROM CartItem WHERE CartID = ? AND ProductID = ?\n        ", [$cart->CartID, $productId]);

        return response()->json(['message' => 'Item removed from cart']);
    }

    public function clearCart(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user()->CustomerID);
        DB::delete("DELETE FROM CartItem WHERE CartID = ?", [$cart->CartID]);

        return response()->json(['message' => 'Cart cleared']);
    }

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

            DB::insert("\n                INSERT INTO [Order] (CustomerID, EmployeeID, CouponID, OrderStatus, TotalAmount, OrderDate, Address)\n                VALUES (?, ?, ?, ?, ?, ?, ?)\n            ", [
                $request->user()->CustomerID,
                $employeeId,
                $request->CouponID,
                'Pending',
                $finalTotal,
                now()->format('Y-m-d'),
                $request->Address,
            ]);

            $order = DB::selectOne("\n                SELECT TOP 1 OrderID FROM [Order] WHERE CustomerID = ? ORDER BY OrderID DESC\n            ", [$request->user()->CustomerID]);

            foreach ($request->items as $item) {
                $product = DB::selectOne("SELECT * FROM Product WHERE ProductID = ?", [$item['ProductID']]);

                if (!$product) {
                    throw new \Exception("Product ID {$item['ProductID']} not found.");
                }

                if ($product->Stock < $item['Quantity']) {
                    throw new \Exception("Insufficient stock for {$product->ProductName}. Only {$product->Stock} left.");
                }

                DB::update("\n                    UPDATE Product SET Stock = Stock - ? WHERE ProductID = ?\n                ", [$item['Quantity'], $item['ProductID']]);

                DB::insert("\n                    INSERT INTO OrderItem (OrderID, ProductID, Quantity, UnitPrice)\n                    VALUES (?, ?, ?, ?)\n                ", [
                    $order->OrderID,
                    $item['ProductID'],
                    $item['Quantity'],
                    $item['UnitPrice'],
                ]);
            }

            $cart = DB::selectOne("SELECT * FROM Cart WHERE CustomerID = ?", [$request->user()->CustomerID]);

            if ($cart) {
                DB::delete("DELETE FROM CartItem WHERE CartID = ?", [$cart->CartID]);
            }

            DB::update("UPDATE Wallet SET Balance = Balance - ? WHERE WalletID = ?", [$finalTotal, $wallet->WalletID]);

            DB::insert("\n                INSERT INTO WalletTransaction (WalletID, Amount, TransactionType, Description, TransactionDate)\n                VALUES (?, ?, ?, ?, ?)\n            ", [
                $wallet->WalletID,
                $finalTotal,
                'Debit',
                'Order payment for Order #' . $order->OrderID,
                now(),
            ]);

            DB::commit();

            $fullOrder = DB::selectOne("SELECT * FROM [Order] WHERE OrderID = ?", [$order->OrderID]);

            return response()->json($fullOrder, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getOrderHistory(Request $request)
    {
        $orders = DB::select("\n            SELECT\n                o.OrderID, o.CustomerID, o.EmployeeID, o.CouponID, o.OrderStatus,\n                o.TotalAmount, o.OrderDate, o.Address\n            FROM [Order] o\n            WHERE o.CustomerID = ? AND o.OrderStatus != 'Cancelled'\n            ORDER BY o.OrderDate DESC\n        ", [$request->user()->CustomerID]);

        $result = array_map(function ($order) {
            $items = DB::select("\n                SELECT oi.*, p.ProductName, p.Price\n                FROM OrderItem oi\n                INNER JOIN Product p ON oi.ProductID = p.ProductID\n                WHERE oi.OrderID = ?\n            ", [$order->OrderID]);

            $delivery = DB::selectOne("\n                SELECT d.*, dm.DelManName\n                FROM Delivery d\n                LEFT JOIN DeliveryMan dm ON d.DelManID = dm.DelManID\n                WHERE d.OrderID = ?\n            ", [$order->OrderID]);

            return [
                'OrderID' => $order->OrderID,
                'CustomerID' => $order->CustomerID,
                'EmployeeID' => $order->EmployeeID,
                'CouponID' => $order->CouponID,
                'OrderStatus' => $order->OrderStatus,
                'TotalAmount' => (float) $order->TotalAmount,
                'OrderDate' => $order->OrderDate,
                'Address' => $order->Address,
                'items' => $items,
                'delivery' => $delivery ? [
                    'DeliveryID' => $delivery->DeliveryID,
                    'OrderID' => $delivery->OrderID,
                    'DelManID' => $delivery->DelManID,
                    'DeliveryStatus' => $delivery->DeliveryStatus,
                    'DeliveryDate' => $delivery->DeliveryDate,
                    'delivery_man' => [
                        'DelManID' => $delivery->DelManID,
                        'DelManName' => $delivery->DelManName,
                    ],
                ] : null,
            ];
        }, $orders);

        return response()->json($result);
    }

    public function cancelOrder(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $order = DB::selectOne("\n                SELECT * FROM [Order]\n                WHERE OrderID = ? AND CustomerID = ?\n            ", [$id, $request->user()->CustomerID]);

            if (!$order) {
                DB::rollBack();
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($order->OrderStatus !== 'Pending') {
                DB::rollBack();
                return response()->json(['message' => 'Only pending orders can be cancelled'], 400);
            }

            $items = DB::select("SELECT * FROM OrderItem WHERE OrderID = ?", [$id]);

            foreach ($items as $item) {
                DB::update("\n                    UPDATE Product\n                    SET Stock = Stock + ?\n                    WHERE ProductID = ?\n                ", [$item->Quantity, $item->ProductID]);
            }

            $wallet = $this->getOrCreateWallet($order->CustomerID);
            $refundAmount = (float) $order->TotalAmount;

            DB::update("UPDATE Wallet SET Balance = Balance + ? WHERE WalletID = ?", [$refundAmount, $wallet->WalletID]);

            DB::insert("\n                INSERT INTO WalletTransaction (WalletID, Amount, TransactionType, Description, TransactionDate)\n                VALUES (?, ?, ?, ?, ?)\n            ", [
                $wallet->WalletID,
                $refundAmount,
                'Credit',
                'Refund for cancelled Order #' . $order->OrderID,
                now(),
            ]);

            DB::update("UPDATE [Order] SET OrderStatus = 'Cancelled' WHERE OrderID = ?", [$id]);

            DB::commit();

            return response()->json(['message' => 'Order cancelled successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error cancelling order: ' . $e->getMessage()], 500);
        }
    }

    public function validateCoupon(Request $request)
    {
        try {
            $coupon = DB::select('EXEC ValidateCoupon ?', [$request->CouponCode]);
        } catch (\Exception $e) {
            $coupon = [];
        }

        if (empty($coupon)) {
            $today = now()->format('Y-m-d');
            $couponRow = DB::selectOne("\n                SELECT TOP 1 *\n                FROM Coupon\n                WHERE CouponCode = ?\n                  AND StartDate <= ?\n                  AND EndDate >= ?\n            ", [$request->CouponCode, $today, $today]);

            if ($couponRow) {
                return response()->json(['valid' => true, 'coupon' => $couponRow]);
            }

            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon']);
        }

        return response()->json(['valid' => true, 'coupon' => $coupon[0]]);
    }

    public function getWallet(Request $request)
    {
        $wallet = $this->getOrCreateWallet($request->user()->CustomerID);

        $transactions = DB::select("\n            SELECT *\n            FROM WalletTransaction\n            WHERE WalletID = ?\n            ORDER BY TransactionDate DESC\n            OFFSET 0 ROWS FETCH NEXT 50 ROWS ONLY\n        ", [$wallet->WalletID]);

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

            DB::update("UPDATE Wallet SET Balance = Balance + ? WHERE WalletID = ?", [$amount, $wallet->WalletID]);

            DB::insert("\n                INSERT INTO WalletTransaction (WalletID, Amount, TransactionType, Description, TransactionDate)\n                VALUES (?, ?, ?, ?, ?)\n            ", [
                $wallet->WalletID,
                $amount,
                'Credit',
                $request->Description ?: 'Wallet top-up',
                now(),
            ]);

            $updatedWallet = DB::selectOne("SELECT * FROM Wallet WHERE WalletID = ?", [$wallet->WalletID]);

            DB::commit();

            return response()->json([
                'message' => 'Balance added successfully',
                'wallet' => $updatedWallet,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to add balance: ' . $e->getMessage()], 500);
        }
    }
    public function rateRider(Request $request, $deliveryId)
    {
        $request->validate([
            'Rating' => 'required|integer|min:1|max:5',
            'RatingComment' => 'nullable|string|max:500',
        ]);

        $delivery = \App\Models\Delivery::where('DeliveryID', $deliveryId)
            ->whereHas('order', function($q) use ($request) {
                $q->where('CustomerID', $request->user()->CustomerID);
            })
            ->firstOrFail();

        $delivery->update([
            'Rating' => $request->Rating,
            'RatingComment' => $request->RatingComment,
        ]);

        return response()->json(['message' => 'Rider rated successfully']);
    }
}
