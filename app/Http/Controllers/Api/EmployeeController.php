<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Offer;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Category;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function getProducts()
    {
        return response()->json(Product::with([
            'category',
            'detail',
            'offers' => function ($query) {
                $query->orderByDesc('StartDate');
            },
        ])->get());
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
        $offer = Offer::updateOrCreate(
            ['ProductID' => $request->ProductID],
            [
                'DiscountAmount' => $request->DiscountAmount,
                'StartDate' => $request->StartDate,
                'EndDate' => $request->EndDate,
            ]
        );

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
        DB::beginTransaction();
        try {
            $order = Order::with('items')->find($id);
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }
            if ($order->OrderStatus !== 'Cancelled') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->ProductID);
                    if ($product) {
                        $product->Stock += $item->Quantity;
                        $product->save();
                    }
                }
                $order->OrderStatus = 'Cancelled';
                $order->save();
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
        return response()->json(DeliveryMan::where('Status', 'Available')->get());
    }

    public function getDeliveryMen()
    {
        return response()->json(DeliveryMan::orderByDesc('DelManID')->get());
    }

    public function addDeliveryMan(Request $request)
    {
        $request->validate([
            'DelManName' => 'required|string|max:255',
            'Phone' => 'required|string|max:20',
            'Email' => 'required|email|unique:DeliveryMan,Email',
            'Address' => 'required|string|max:255',
            'Password' => 'nullable|string|min:6',
        ]);

        $deliveryMan = DeliveryMan::create([
            'DelManName' => $request->DelManName,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password ?? 'password'),
            'Address' => $request->Address,
            'Status' => $request->Status ?? 'Available',
        ]);

        return response()->json($deliveryMan, 201);
    }

    public function deleteDeliveryMan($id)
    {
        $deliveryMan = DeliveryMan::where('DelManID', $id)->first();
        if (!$deliveryMan) {
            return response()->json(['message' => 'Deliveryman not found'], 404);
        }

        $hasActiveDelivery = $deliveryMan->deliveries()
            ->whereIn('DeliveryStatus', ['Pending', 'In Progress'])
            ->exists();

        if ($hasActiveDelivery) {
            return response()->json(['message' => 'Cannot delete a deliveryman with active deliveries.'], 422);
        }

        $deliveryMan->delete();

        return response()->json(['message' => 'Deliveryman deleted successfully']);
    }

    public function getAllDeliveryMenStatus()
    {
        $deliveryMen = DeliveryMan::with(['deliveries' => function ($query) {
            $query->whereIn('DeliveryStatus', ['Pending', 'In Progress'])
                  ->with('order.customer');
        }])->get();
        return response()->json($deliveryMen);
    }

    public function assignDelivery($id, Request $request)
    {
        // Using Stored Procedure defined in Step 1
        DB::statement('EXEC AssignDelivery ?, ?', [$id, $request->DelManID]);
        
        // Update DeliveryMan status to Busy
        DeliveryMan::where('DelManID', $request->DelManID)->update(['Status' => 'Busy']);
        
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
        $employee->update($request->only(['EmployeeName', 'Phone', 'Address']));
        return response()->json(['message' => 'Profile updated successfully', 'employee' => $employee]);
    }

    public function changePassword(Request $request)
    {
        $employee = $request->user();

        if (!Hash::check($request->current_password, $employee->Password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $request->validate(['new_password' => 'required|string|min:6']);
        $employee->Password = Hash::make($request->new_password);
        $employee->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    // --- Offers ---
    public function getOffers()
    {
        return response()->json(Offer::with('product')->get());
    }

    public function deleteOffer($id)
    {
        Offer::where('OfferID', $id)->delete();
        return response()->json(['message' => 'Offer removed successfully']);
    }

    // --- Coupons ---
    public function getCoupons()
    {
        return response()->json(Coupon::all());
    }

    public function deleteCoupon($id)
    {
        Coupon::where('CouponID', $id)->delete();
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

        $existingNames = Category::query()
            ->pluck('CategoryName')
            ->map(fn ($name) => strtolower(trim((string) $name)))
            ->toArray();

        foreach ($defaultCategories as $category) {
            if (!in_array(strtolower($category['CategoryName']), $existingNames, true)) {
                Category::create($category);
            }
        }

        return response()->json(Category::orderBy('CategoryName')->get());
    }
}
