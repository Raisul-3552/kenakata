<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function getEmployees()
    {
        return response()->json(Employee::all());
    }

    public function searchCustomers(Request $request)
    {
        $q = $request->query('q');
        $customers = Customer::where('CustomerName', 'LIKE', "%$q%")
            ->orWhere('Email', 'LIKE', "%$q%")
            ->get(['CustomerID', 'CustomerName', 'Email']);
            
        return response()->json($customers);
    }

    public function addEmployee(Request $request)
    {
        $request->validate([
            'EmployeeName' => 'required|string|max:255',
            'Email' => 'required|email|unique:Employee,Email',
        ]);

        $employee = Employee::create([
            'AdminID' => $request->user()->AdminID,
            'EmployeeName' => $request->EmployeeName,
            'Phone' => $request->Phone ?? 'N/A',
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password ?? 'password'),
            'Address' => $request->Address ?? 'N/A',
        ]);

        return response()->json($employee, 201);
    }

    public function deleteEmployee($id)
    {
        Employee::where('EmployeeID', $id)->delete();
        return response()->json(['message' => 'Employee deleted successfully']);
    }

    public function dashboardStats()
    {
        $stats = [
            'total_employees' => DB::table('Employee')->count(),
            'total_customers' => DB::table('Customer')->count(),
            'total_products' => DB::table('Product')->count(),
            'total_orders' => DB::table('Order')->count(),
            'total_revenue' => DB::table('Order')->where('OrderStatus', 'Confirmed')->sum('TotalAmount'),
        ];
        return response()->json($stats);
    }

    public function getProfile(Request $request)
    {
        $admin = $request->user();
        $employees = $admin->employees()->get();
        return response()->json([
            'admin' => $admin,
            'employees' => $employees,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $admin = $request->user();
        
        $request->validate([
            'AdminName' => 'required|string|max:255',
            'Email' => 'required|email|unique:Admin,Email,' . $admin->AdminID . ',AdminID',
            'Password' => 'nullable|string|min:6',
            'Photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'AdminName' => $request->AdminName,
            'Email' => $request->Email,
        ];

        if ($request->filled('Password')) {
            $data['Password'] = Hash::make($request->Password);
        }

        if ($request->hasFile('Photo')) {
            $file = $request->file('Photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profiles'), $filename);
            $data['Photo'] = '/uploads/profiles/' . $filename;
        }

        $admin->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'admin' => $admin,
        ]);
    }
}
