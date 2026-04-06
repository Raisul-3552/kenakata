<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;

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
}
