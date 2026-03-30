<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryManController extends Controller
{
    public function dashboard()
    {
        $sessionUser = session('kenakata_user');
        if (!$sessionUser || $sessionUser['type'] !== 'DeliveryMan') {
            return redirect()->route('login.form');
        }
        $deliveryman = DB::table('DeliveryMan')->where('DelManID', $sessionUser['id'])->first();
        if (!$deliveryman) return redirect()->route('login.form');

        return view('deliveryman.dashboard', compact('deliveryman'));
    }

    public function profile()
    {
        $sessionUser = session('kenakata_user');
        if (!$sessionUser || $sessionUser['type'] !== 'DeliveryMan') {
            return redirect()->route('login.form')->withErrors(['auth' => 'Please login as Delivery Man.']);
        }

        $deliveryman = DB::table('DeliveryMan')->where('DelManID', $sessionUser['id'])->first();
        if (!$deliveryman) {
            return redirect()->route('login.form');
        }

        return view('deliveryman.profile', compact('deliveryman'));
    }
}
