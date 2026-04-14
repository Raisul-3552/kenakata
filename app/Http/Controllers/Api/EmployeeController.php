<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\Concerns\InteractsWithAccountEmails;

class EmployeeController extends Controller
{
    use InteractsWithAccountEmails;

    private function getOrCreateWallet($customerID)
    {
        // MSSQL Query: Get or create wallet
        $wallet = DB::selectOne("
            SELECT * FROM Wallet WHERE CustomerID = ?
        ", [$customerID]);

        if (!$wallet) {
            DB::insert("
                INSERT INTO Wallet (CustomerID, Balance) VALUES (?, 0)
            ", [$customerID]);
            $wallet = DB::selectOne("
                SELECT * FROM Wallet WHERE CustomerID = ?
            ", [$customerID]);
        }

        return $wallet;
    }

    private function getLeastBusyEmployeeId()
    {
        $employee = DB::selectOne("\n            SELECT TOP 1 e.EmployeeID\n            FROM Employee e\n            LEFT JOIN [Order] o\n                ON e.EmployeeID = o.EmployeeID\n               AND o.OrderStatus NOT IN ('Delivered', 'Cancelled')\n            GROUP BY e.EmployeeID\n            ORDER BY COUNT(o.OrderID) ASC, e.EmployeeID ASC\n        ");

        return $employee ? (int) $employee->EmployeeID : null;
    }

    public function getProducts()
    {
        // MSSQL Query: Get all products with category and details (no LEFT JOIN duplication)
        $products = DB::select("
            SELECT 
                p.ProductID, p.EmployeeID, p.CategoryID, p.ProductName, p.Brand, p.Price, p.Stock,
                c.CategoryName, c.Description as CategoryDescription,
                COALESCE(pd.Description, NULL) as DetailDescription,
                COALESCE(pd.Specification, NULL) as Specification,
                COALESCE(pd.Warranty, NULL) as Warranty,
                COALESCE(pd.Image, NULL) as Image
            FROM Product p
            INNER JOIN Category c ON p.CategoryID = c.CategoryID
            LEFT JOIN ProductDetails pd ON p.ProductID = pd.ProductID
            ORDER BY p.ProductName
        ");

        // Get all offers separately
        $offers = DB::select("
            SELECT * FROM Offer
        ");

        // Map products with offers
        $result = array_map(function ($product) use ($offers) {
            // Find offers for this product
            $productOffers = array_filter($offers, function ($offer) use ($product) {
                return $offer->ProductID == $product->ProductID;
            });

            return [
                'ProductID' => $product->ProductID,
                'EmployeeID' => $product->EmployeeID,
                'CategoryID' => $product->CategoryID,
                'ProductName' => $product->ProductName,
                'Brand' => $product->Brand,
                'Price' => floatval($product->Price),
                'Stock' => intval($product->Stock),
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

    public function addProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            // MSSQL Query: Insert product
            DB::insert("
                INSERT INTO Product (EmployeeID, CategoryID, ProductName, Brand, Price, Stock)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [
                $request->user()->EmployeeID,
                $request->CategoryID,
                $request->ProductName,
                $request->Brand,
                $request->Price,
                $request->Stock,
            ]);

            // Get the product ID
            $product = DB::selectOne("
                SELECT TOP 1 * FROM Product WHERE EmployeeID = ? AND ProductName = ? ORDER BY ProductID DESC
            ", [$request->user()->EmployeeID, $request->ProductName]);

            // MSSQL Query: Insert product details
            DB::insert("
                INSERT INTO ProductDetails (ProductID, Description, Specification, Warranty, Image)
                VALUES (?, ?, ?, ?, ?)
            ", [
                $product->ProductID,
                $request->Description,
                $request->Specification,
                $request->Warranty,
                $request->Image,
            ]);

            DB::commit();
            return response()->json($product, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function editProduct(Request $request, $id)
    {
        // MSSQL Query: Update product
        DB::update("
            UPDATE Product
            SET ProductName = ?, Brand = ?, Price = ?, Stock = ?, CategoryID = ?
            WHERE ProductID = ?
        ", [
            $request->ProductName,
            $request->Brand,
            $request->Price,
            $request->Stock,
            $request->CategoryID,
            $id,
        ]);
        
        // MSSQL Query: Update product details
        DB::update("
            UPDATE ProductDetails
            SET Description = ?, Specification = ?, Warranty = ?, Image = ?
            WHERE ProductID = ?
        ", [
            $request->Description,
            $request->Specification,
            $request->Warranty,
            $request->Image,
            $id,
        ]);

        // Get updated product
        $product = DB::selectOne("
            SELECT * FROM Product WHERE ProductID = ?
        ", [$id]);

        return response()->json($product);
    }

    public function deleteProduct($id)
    {
        // MSSQL Query: Delete product (cascade deletes ProductDetails)
        DB::delete("DELETE FROM Product WHERE ProductID = ?", [$id]);
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function addOffer(Request $request)
    {
        // MSSQL Query: Check if offer exists
        $existingOffer = DB::selectOne("
            SELECT * FROM Offer WHERE ProductID = ?
        ", [$request->ProductID]);

        if ($existingOffer) {
            // Update existing offer
            DB::update("
                UPDATE Offer
                SET DiscountAmount = ?, StartDate = ?, EndDate = ?
                WHERE ProductID = ?
            ", [
                $request->DiscountAmount,
                $request->StartDate,
                $request->EndDate,
                $request->ProductID,
            ]);
            $offer = DB::selectOne("
                SELECT * FROM Offer WHERE ProductID = ?
            ", [$request->ProductID]);
        } else {
            // Insert new offer
            DB::insert("
                INSERT INTO Offer (ProductID, DiscountAmount, StartDate, EndDate)
                VALUES (?, ?, ?, ?)
            ", [
                $request->ProductID,
                $request->DiscountAmount,
                $request->StartDate,
                $request->EndDate,
            ]);
            $offer = DB::selectOne("
                SELECT * FROM Offer WHERE ProductID = ? ORDER BY OfferID DESC
            ", [$request->ProductID]);
        }

        return response()->json($offer, 201);
    }

    public function addCoupon(Request $request)
    {
        // MSSQL Query: Insert coupon
        DB::insert("
            INSERT INTO Coupon (CouponCode, DiscountAmount, StartDate, EndDate)
            VALUES (?, ?, ?, ?)
        ", [
            $request->CouponCode,
            $request->DiscountAmount,
            $request->StartDate,
            $request->EndDate,
        ]);

        // Get inserted coupon
        $coupon = DB::selectOne("
            SELECT * FROM Coupon WHERE CouponCode = ?
        ", [$request->CouponCode]);

        return response()->json($coupon, 201);
    }

    public function getOrders()
    {
        // MSSQL Query: Get all orders with customer info only (avoid LEFT JOIN duplication)
        $orders = DB::select("
            SELECT 
                o.OrderID, o.CustomerID, o.EmployeeID, o.CouponID, o.OrderStatus, 
                o.TotalAmount, o.OrderDate, o.Address,
                c.CustomerName, c.Phone, c.Email
            FROM [Order] o
            INNER JOIN Customer c ON o.CustomerID = c.CustomerID
            ORDER BY o.OrderDate DESC
        ");

        // For each order, fetch items and delivery info separately
        $result = array_map(function ($order) {
            // Get order items
            $items = DB::select("
                SELECT oi.*, p.ProductName, p.Price
                FROM OrderItem oi
                INNER JOIN Product p ON oi.ProductID = p.ProductID
                WHERE oi.OrderID = ?
            ", [$order->OrderID]);

            // Get delivery info if exists
            $delivery = DB::selectOne("
                SELECT d.*, dm.DelManName
                FROM Delivery d
                LEFT JOIN DeliveryMan dm ON d.DelManID = dm.DelManID
                WHERE d.OrderID = ?
            ", [$order->OrderID]);

            return [
                'OrderID' => $order->OrderID,
                'CustomerID' => $order->CustomerID,
                'CustomerName' => $order->CustomerName,
                'customer' => [
                    'CustomerID' => $order->CustomerID,
                    'CustomerName' => $order->CustomerName,
                    'Phone' => $order->Phone,
                    'Email' => $order->Email,
                ],
                'Email' => $order->Email,
                'EmployeeID' => $order->EmployeeID,
                'CouponID' => $order->CouponID,
                'OrderStatus' => $order->OrderStatus,
                'TotalAmount' => floatval($order->TotalAmount),
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

    public function confirmOrder($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $order = DB::selectOne("\n                SELECT * FROM [Order] WHERE OrderID = ?\n            ", [$id]);

            if (!$order) {
                DB::rollBack();
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($order->OrderStatus !== 'Pending') {
                DB::rollBack();
                return response()->json(['message' => 'Only pending orders can be confirmed'], 400);
            }

            $employeeId = $order->EmployeeID ? (int) $order->EmployeeID : $this->getLeastBusyEmployeeId();

            if (!$employeeId) {
                DB::rollBack();
                return response()->json(['message' => 'No employees are available to handle the order right now.'], 422);
            }

            DB::update("\n                UPDATE [Order]\n                SET OrderStatus = 'Confirmed', EmployeeID = ?\n                WHERE OrderID = ?\n            ", [$employeeId, $id]);

            DB::commit();

            return response()->json([
                'message' => 'Order confirmed successfully',
                'EmployeeID' => $employeeId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function cancelOrder($id)
    {
        DB::beginTransaction();
        try {
            // MSSQL Query: Get order with items
            $order = DB::selectOne("
                SELECT * FROM [Order] WHERE OrderID = ?
            ", [$id]);

            if (!$order) {
                DB::rollBack();
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($order->OrderStatus !== 'Cancelled') {
                // MSSQL Query: Get order items
                $items = DB::select("
                    SELECT * FROM OrderItem WHERE OrderID = ?
                ", [$id]);

                // Restore product stock
                foreach ($items as $item) {
                    DB::update("
                        UPDATE Product SET Stock = Stock + ? WHERE ProductID = ?
                    ", [$item->Quantity, $item->ProductID]);
                }

                // Get or create wallet
                $wallet = $this->getOrCreateWallet($order->CustomerID);
                $refundAmount = (float) $order->TotalAmount;

                // Update wallet balance
                DB::update("
                    UPDATE Wallet SET Balance = Balance + ? WHERE WalletID = ?
                ", [$refundAmount, $wallet->WalletID]);

                // Insert wallet transaction
                DB::insert("
                    INSERT INTO WalletTransaction (WalletID, Amount, TransactionType, Description, TransactionDate)
                    VALUES (?, ?, ?, ?, ?)
                ", [
                    $wallet->WalletID,
                    $refundAmount,
                    'Credit',
                    'Refund for cancelled Order #' . $order->OrderID,
                    now(),
                ]);

                // Update order status
                DB::update("
                    UPDATE [Order] SET OrderStatus = 'Cancelled' WHERE OrderID = ?
                ", [$id]);
            }

            DB::commit();
            return response()->json(['message' => 'Order cancelled successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error cancelling order: ' . $e->getMessage()], 500);
        }
    }

    public function getAvailableDeliveryMen()
    {
        // MSSQL Query: Get available delivery men
        $deliveryMen = DB::select("
            SELECT * FROM DeliveryMan WHERE Status = 'Available'
        ");

        return response()->json($deliveryMen);
    }

    public function getDeliveryMen()
    {
        // MSSQL Query: Get all delivery men ordered by ID
        $deliveryMen = DB::select("
            SELECT * FROM DeliveryMan ORDER BY DelManID DESC
        ");

        return response()->json($deliveryMen);
    }

    public function addDeliveryMan(Request $request)
    {
        $request->validate([
            'DelManName' => 'required|string|max:255',
            'Phone' => 'required|string|max:20',
            'Email' => 'required|email',
            'Address' => 'required|string|max:255',
            'Password' => 'nullable|string|min:6',
        ]);

        if ($this->emailExistsAcrossAccounts($request->Email)) {
            return response()->json(['errors' => ['Email' => ['This email is already registered.']]], 422);
        }

        $hashedPassword = Hash::make($request->Password ?? 'password');
        
        // MSSQL Query: Insert delivery man
        DB::insert("
            INSERT INTO DeliveryMan (DelManName, Phone, Email, Password, Address, Status)
            VALUES (?, ?, ?, ?, ?, ?)
        ", [
            $request->DelManName,
            $request->Phone,
            $request->Email,
            $hashedPassword,
            $request->Address,
            $request->Status ?? 'Available',
        ]);

        // Get inserted delivery man
        $deliveryMan = DB::selectOne("
            SELECT * FROM DeliveryMan WHERE Email = ?
        ", [$request->Email]);

        return response()->json($deliveryMan, 201);
    }

    public function deleteDeliveryMan($id)
    {
        // MSSQL Query: Check if delivery man exists
        $deliveryMan = DB::selectOne("
            SELECT * FROM DeliveryMan WHERE DelManID = ?
        ", [$id]);

        if (!$deliveryMan) {
            return response()->json(['message' => 'Deliveryman not found'], 404);
        }

        // MSSQL Query: Check for active deliveries
        $activeDeliveries = DB::selectOne("
            SELECT COUNT(*) as count FROM Delivery 
            WHERE DelManID = ? AND DeliveryStatus IN ('Pending', 'In Progress')
        ", [$id]);

        if ($activeDeliveries->count > 0) {
            return response()->json(['message' => 'Cannot delete a deliveryman with active deliveries.'], 422);
        }

        // MSSQL Query: Delete delivery man
        DB::delete("DELETE FROM DeliveryMan WHERE DelManID = ?", [$id]);

        return response()->json(['message' => 'Deliveryman deleted successfully']);
    }

    public function getAllDeliveryMenStatus()
    {
        // MSSQL Query: Get all delivery men
        $deliveryMen = DB::select("
            SELECT DelManID, DelManName, Phone, Address, Status
            FROM DeliveryMan
            ORDER BY DelManID
        ");

        // For each delivery man, fetch their active deliveries
        $result = array_map(function ($dm) {
            // Get active deliveries for this delivery man
            $deliveries = DB::select("
                SELECT 
                    d.DeliveryID, d.OrderID, d.DeliveryStatus,
                    o.TotalAmount, o.Address as CustomerAddress, c.CustomerName, c.Phone as CustomerPhone
                FROM Delivery d
                INNER JOIN [Order] o ON d.OrderID = o.OrderID
                INNER JOIN Customer c ON o.CustomerID = c.CustomerID
                WHERE d.DelManID = ? AND d.DeliveryStatus IN ('Pending', 'In Progress')
            ", [$dm->DelManID]);

            return [
                'DelManID' => $dm->DelManID,
                'DelManName' => $dm->DelManName,
                'Phone' => $dm->Phone,
                'Address' => $dm->Address,
                'Status' => $dm->Status,
                'activeDeliveries' => $deliveries,
            ];
        }, $deliveryMen);

        return response()->json($result);
    }

    public function assignDelivery($id, Request $request)
    {
        // MSSQL Stored Procedure: AssignDelivery
        DB::statement('EXEC AssignDelivery ?, ?', [$id, $request->DelManID]);
        
        // Update DeliveryMan status to Busy
        DB::update("
            UPDATE DeliveryMan SET Status = 'Busy' WHERE DelManID = ?
        ", [$request->DelManID]);
        
        return response()->json(['message' => 'Delivery assigned successfully']);
    }

    // --- Profile ---
    public function getProfile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $employee = $request->user();
        
        // MSSQL Query: Update employee profile
        DB::update("
            UPDATE Employee SET EmployeeName = ?, Phone = ?, Address = ? WHERE EmployeeID = ?
        ", [
            $request->EmployeeName,
            $request->Phone,
            $request->Address,
            $employee->EmployeeID,
        ]);

        // Get updated employee
        $updatedEmployee = DB::selectOne("
            SELECT * FROM Employee WHERE EmployeeID = ?
        ", [$employee->EmployeeID]);

        return response()->json(['message' => 'Profile updated successfully', 'employee' => $updatedEmployee]);
    }

    public function changePassword(Request $request)
    {
        $employee = $request->user();

        if (!Hash::check($request->current_password, $employee->Password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $request->validate(['new_password' => 'required|string|min:6']);
        
        $hashedPassword = Hash::make($request->new_password);
        
        // MSSQL Query: Update employee password
        DB::update("
            UPDATE Employee SET Password = ? WHERE EmployeeID = ?
        ", [$hashedPassword, $employee->EmployeeID]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    // --- Offers ---
    public function getOffers()
    {
        // MSSQL Query: Get all offers with product info
        $offers = DB::select("
            SELECT o.OfferID, o.ProductID, o.DiscountAmount, o.StartDate, o.EndDate,
                   p.ProductName, p.Brand, p.Price
            FROM Offer o
            INNER JOIN Product p ON o.ProductID = p.ProductID
        ");

        return response()->json($offers);
    }

    public function deleteOffer($id)
    {
        // MSSQL Query: Delete offer
        DB::delete("DELETE FROM Offer WHERE OfferID = ?", [$id]);
        return response()->json(['message' => 'Offer removed successfully']);
    }

    // --- Coupons ---
    public function getCoupons()
    {
        // MSSQL Query: Get all coupons
        $coupons = DB::select("
            SELECT * FROM Coupon ORDER BY CouponID DESC
        ");

        return response()->json($coupons);
    }

    public function deleteCoupon($id)
    {
        // MSSQL Query: Delete coupon
        DB::delete("DELETE FROM Coupon WHERE CouponID = ?", [$id]);
        return response()->json(['message' => 'Coupon deleted successfully']);
    }

    // --- Categories ---
    public function getCategories()
    {
        $defaultCategories = [
            ['CategoryName' => 'Fruits', 'Description' => 'Fresh seasonal fruits'],
            ['CategoryName' => 'Vegetables', 'Description' => 'Fresh vegetables and greens'],
            ['CategoryName' => 'Groceries', 'Description' => 'Rice, pulses, oil and daily essentials'],
            ['CategoryName' => 'Sanitary Items', 'Description' => 'Hygiene and cleaning products'],
            ['CategoryName' => 'Home Tools', 'Description' => 'Small home and utility tools'],
            ['CategoryName' => 'Kitchenware', 'Description' => 'Kitchen and cookware items'],
            ['CategoryName' => 'Electronics', 'Description' => 'Electronic appliances and accessories'],
            ['CategoryName' => 'Mobile Devices', 'Description' => 'Phones, tablets and related accessories'],
            ['CategoryName' => 'Computers', 'Description' => 'Laptops, desktops and computer accessories'],
        ];

        // MSSQL Query: Get existing category names
        $existingCategories = DB::select("
            SELECT LOWER(TRIM(CategoryName)) as CategoryName FROM Category
        ");

        $existingNames = array_map(fn($cat) => $cat->CategoryName, $existingCategories);

        // MSSQL Query: Insert missing categories
        foreach ($defaultCategories as $category) {
            $categoryNameLower = strtolower(trim($category['CategoryName']));
            if (!in_array($categoryNameLower, $existingNames, true)) {
                DB::insert("
                    INSERT INTO Category (CategoryName, Description) VALUES (?, ?)
                ", [$category['CategoryName'], $category['Description']]);
            }
        }

        // MSSQL Query: Get all categories ordered by name
        $categories = DB::select("
            SELECT * FROM Category ORDER BY CategoryName
        ");

        return response()->json($categories);
    }
}
