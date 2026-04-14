<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function getAssignedDeliveries(Request $request)
    {
        // MSSQL Query: Get all deliveries with order and customer info for a delivery man
        $deliveries = DB::select("
            SELECT 
                d.DeliveryID, d.OrderID, d.DelManID, d.DeliveryStatus, d.DeliveryDate,
                o.OrderID, o.CustomerID, o.OrderStatus, o.TotalAmount, o.OrderDate, o.Address,
                c.CustomerID, c.CustomerName, c.Phone, c.Email
            FROM Delivery d
            INNER JOIN [Order] o ON d.OrderID = o.OrderID
            INNER JOIN Customer c ON o.CustomerID = c.CustomerID
            WHERE d.DelManID = ?
        ", [$request->user()->DelManID]);
        
        return response()->json($deliveries);
    }

    public function updateStatus(Request $request, $id)
    {
        // MSSQL Query: Get delivery details first
        $delivery = DB::selectOne("
            SELECT * FROM Delivery WHERE DeliveryID = ?
        ", [$id]);

        if (!$delivery) {
            return response()->json(['message' => 'Delivery not found'], 404);
        }

        // MSSQL Query: Update delivery status
        $deliveryDate = ($request->DeliveryStatus == 'Delivered' || $request->DeliveryStatus == 'Cancelled') 
                        ? now()->format('Y-m-d') 
                        : null;

        DB::update("
            UPDATE Delivery 
            SET DeliveryStatus = ?, DeliveryDate = ?
            WHERE DeliveryID = ?
        ", [$request->DeliveryStatus, $deliveryDate, $id]);

        // Keep Order table in sync so employee dashboard reflects final delivery state
        if ($request->DeliveryStatus == 'Delivered') {
            DB::update("
                UPDATE [Order] SET OrderStatus = 'Delivered' WHERE OrderID = ?
            ", [$delivery->OrderID]);
        }

        // If delivered or cancelled, set rider back to Available
        if ($request->DeliveryStatus == 'Delivered' || $request->DeliveryStatus == 'Cancelled') {
            DB::update("
                UPDATE DeliveryMan SET Status = 'Available' WHERE DelManID = ?
            ", [$delivery->DelManID]);
        }

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function getProfile(Request $request)
    {
        $rider = $request->user();
        
        // MSSQL Query: Count lifetime deliveries
        $result = DB::selectOne("
            SELECT COUNT(*) as LifetimeDeliveries
            FROM Delivery
            WHERE DelManID = ? AND DeliveryStatus = 'Delivered'
        ", [$rider->DelManID]);
            
        $avgRating = Delivery::where('DelManID', $rider->DelManID)
            ->whereNotNull('Rating')
            ->avg('Rating') ?? 0;
            
        return response()->json([
            'rider' => $rider,
            'lifetime_deliveries' => $result->LifetimeDeliveries ?? 0,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $rider = $request->user();
        
        $request->validate([
            'DelManName' => 'required|string|max:255',
            'Phone' => 'required|string|max:20',
            'Address' => 'required|string|max:255',
        ]);

        // MSSQL Query: Update delivery man profile
        DB::update("
            UPDATE DeliveryMan 
            SET DelManName = ?, Phone = ?, Address = ?
            WHERE DelManID = ?
        ", [
            $request->DelManName,
            $request->Phone,
            $request->Address,
            $rider->DelManID
        ]);

        // Fetch updated data
        $updatedRider = DB::selectOne("
            SELECT * FROM DeliveryMan WHERE DelManID = ?
        ", [$rider->DelManID]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'rider' => $updatedRider,
        ]);
    }

    public function changePassword(Request $request)
    {
        $rider = $request->user();

        if (!Hash::check($request->current_password, $rider->Password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $request->validate(['new_password' => 'required|string|min:6']);
        
        $hashedPassword = Hash::make($request->new_password);
        
        // MSSQL Query: Update password
        DB::update("
            UPDATE DeliveryMan SET Password = ? WHERE DelManID = ?
        ", [$hashedPassword, $rider->DelManID]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $rider = $request->user();
        
        // MSSQL Query: Toggle status between Available and Busy
        $newStatus = $rider->Status === 'Available' ? 'Busy' : 'Available';
        
        DB::update("
            UPDATE DeliveryMan SET Status = ? WHERE DelManID = ?
        ", [$newStatus, $rider->DelManID]);

        // Fetch updated data
        $updatedRider = DB::selectOne("
            SELECT * FROM DeliveryMan WHERE DelManID = ?
        ", [$rider->DelManID]);

        return response()->json([
            'message' => 'Status updated successfully',
            'status' => $newStatus,
            'rider' => $updatedRider,
        ]);
    }
}
