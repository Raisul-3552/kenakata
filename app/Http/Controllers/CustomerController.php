<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $sessionUser = session('kenakata_user');
        if (!$sessionUser || $sessionUser['type'] !== 'Customer') {
            return redirect()->route('login.form');
        }
        $customer = DB::table('Customer')->where('CustomerID', $sessionUser['id'])->first();
        if (!$customer) return redirect()->route('login.form');

        return view('customer.dashboard', compact('customer'));
    }

    public function profile()
    {
        $sessionUser = session('kenakata_user');
        if (!$sessionUser || $sessionUser['type'] !== 'Customer') {
            return redirect()->route('login.form')->withErrors(['auth' => 'Please login as Customer.']);
        }

        $customer = DB::table('Customer')->where('CustomerID', $sessionUser['id'])->first();
        if (!$customer) {
            return redirect()->route('login.form');
        }

        return view('customer.profile', compact('customer'));
    }
}
