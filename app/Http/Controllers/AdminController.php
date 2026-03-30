<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private function guard()
    {
        $u = session('kenakata_user');
        if (!$u || $u['type'] !== 'Admin') {
            abort(redirect()->route('login.form')->withErrors(['auth' => 'Please login as Admin.']));
        }
        return $u;
    }

    public function dashboard()
    {
        $sessionUser = $this->guard();
        $admin = DB::table('Admin')->where('AdminID', $sessionUser['id'])->first();
        if (!$admin) return redirect()->route('login.form');

        return view('admin.dashboard', compact('admin'));
    }

    public function profile()
    {
        $sessionUser = session('kenakata_user');
        if (!$sessionUser || $sessionUser['type'] !== 'Admin') {
            return redirect()->route('login.form')->withErrors(['auth' => 'Please login as Admin.']);
        }

        $admin = DB::table('Admin')->where('AdminID', $sessionUser['id'])->first();
        if (!$admin) {
            return redirect()->route('login.form');
        }

        $employeeCount = DB::table('Employee')
            ->join('EmploymentCode', 'Employee.CodeID', '=', 'EmploymentCode.CodeID')
            ->where('EmploymentCode.AdminID', $admin->AdminID)
            ->count();
        $customerCount   = DB::table('Customer')->count();
        $deliveryManCount = DB::table('DeliveryMan')->count();

        return view('admin.profile', compact('admin', 'employeeCount', 'customerCount', 'deliveryManCount'));
    }

    public function codes()
    {
        $sessionUser = $this->guard();
        
        $admin = DB::table('Admin')->where('AdminID', $sessionUser['id'])->first();
        if (!$admin) {
            return redirect()->route('login.form');
        }

        $codes = DB::table('EmploymentCode')
                   ->where('AdminID', $admin->AdminID)
                   ->orderBy('CreatedAt', 'desc')
                   ->get();

        return view('admin.codes', compact('admin', 'codes'));
    }

    public function generateCode(\Illuminate\Http\Request $request)
    {
        $sessionUser = $this->guard();
        
        $request->validate([
            'reg_code' => 'required|string|max:100'
        ]);

        // Check uniqueness manually for safety with custom table names
        if (\Illuminate\Support\Facades\DB::table('EmploymentCode')->where('RegCode', $request->reg_code)->exists()) {
            return back()->withErrors(['reg_code' => 'This employment code already exists. Please choose a different one.'])->withInput();
        }

        \Illuminate\Support\Facades\DB::table('EmploymentCode')->insert([
            'RegCode' => $request->reg_code,
            'AdminID' => $sessionUser['id'],
            'IsUsed'  => 0,
            'CreatedAt' => now()
        ]);

        return redirect()->route('admin.codes')->with('success', 'Custom Employment Code created successfully!');
    }
}
