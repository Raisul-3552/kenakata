<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryMan;
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

        // If delivered or cancelled, set rider back to Available
        if ($request->DeliveryStatus == 'Delivered' || $request->DeliveryStatus == 'Cancelled') {
            DeliveryMan::where('DelManID', $delivery->DelManID)->update(['Status' => 'Available']);
        }

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function getProfile(Request $request)
    {
        $rider = $request->user();
        $lifetimeDeliveries = Delivery::where('DelManID', $rider->DelManID)
            ->where('DeliveryStatus', 'Delivered')
            ->count();
            
        return response()->json([
            'rider' => $rider,
            'lifetime_deliveries' => $lifetimeDeliveries,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $rider = $request->user();
        
        $request->validate([
            'DelManName' => 'required|string|max:255',
            'Email' => 'required|email|unique:DeliveryMan,Email,' . $rider->DelManID . ',DelManID',
            'Password' => 'nullable|string|min:6',
        ]);

        $data = [
            'DelManName' => $request->DelManName,
            'Email' => $request->Email,
        ];

        if ($request->filled('Password')) {
            $data['Password'] = Hash::make($request->Password);
        }

        $rider->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'rider' => $rider,
        ]);
    }
}
