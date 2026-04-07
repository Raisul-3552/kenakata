<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryMan;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeliveryController extends Controller
{
    public function getAssignedDeliveries(Request $request)
    {
        $deliveries = Delivery::with(['order.customer'])
            ->where('DelManID', $request->user()->DelManID)
            ->get();
        return response()->json($deliveries);
    }

    public function updateStatus(Request $request, $id)
    {
        $delivery = Delivery::where('DeliveryID', $id)->first();
        $delivery->update([
            'DeliveryStatus' => $request->DeliveryStatus,
            'DeliveryDate' => ($request->DeliveryStatus == 'Delivered' || $request->DeliveryStatus == 'Cancelled') ? now()->format('Y-m-d') : null,
        ]);

        // If delivered or cancelled, set rider back to Available and sync Order status
        if ($request->DeliveryStatus == 'Delivered' || $request->DeliveryStatus == 'Cancelled') {
            DeliveryMan::where('DelManID', $delivery->DelManID)->update(['Status' => 'Available']);
            
            // Sync status to the Order table
            \App\Models\Order::where('OrderID', $delivery->OrderID)->update(['OrderStatus' => $request->DeliveryStatus]);
        }

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function getProfile(Request $request)
    {
        $rider = $request->user();
        $lifetimeDeliveries = Delivery::where('DelManID', $rider->DelManID)
            ->where('DeliveryStatus', 'Delivered')
            ->count();
            
        $avgRating = Delivery::where('DelManID', $rider->DelManID)
            ->whereNotNull('Rating')
            ->avg('Rating') ?? 0;
            
        return response()->json([
            'rider' => $rider,
            'lifetime_deliveries' => $lifetimeDeliveries,
            'avg_rating' => number_format($avgRating, 1),
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

        $data = [
            'DelManName' => $request->DelManName,
            'Phone' => $request->Phone,
            'Address' => $request->Address,
        ];

        $rider->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'rider' => $rider,
        ]);
    }

    public function changePassword(Request $request)
    {
        $rider = $request->user();

        if (!Hash::check($request->current_password, $rider->Password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $request->validate(['new_password' => 'required|string|min:6']);
        $rider->Password = Hash::make($request->new_password);
        $rider->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $rider = $request->user();
        $rider->Status = $rider->Status === 'Available' ? 'Busy' : 'Available';
        $rider->save();

        return response()->json([
            'message' => 'Status updated successfully',
            'status' => $rider->Status,
            'rider' => $rider,
        ]);
    }
}
