<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Offer;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Concerns\InteractsWithAccountEmails;

class AdminController extends Controller
{
    use InteractsWithAccountEmails;

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
            'Email' => 'required|email',
        ]);

        if ($this->emailExistsAcrossAccounts($request->Email)) {
            return response()->json(['errors' => ['Email' => ['This email is already registered.']]], 422);
        }

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

        $email = $request->input('Email', $request->input('email'));
        $name = $request->input('AdminName', $request->input('admin_name'));

        $request->validate([
            'AdminName' => 'required|string|max:255',
            'Email' => 'nullable|email',
            'email' => 'nullable|email',
        ]);

        $incomingEmail = $request->input('Email', $request->input('email'));
        if ($incomingEmail && $this->emailExistsAcrossAccounts($incomingEmail, ['model' => \App\Models\Admin::class, 'id' => $admin->AdminID])) {
            return response()->json(['message' => 'Email already exists in another account.'], 422);
        }

        $admin->AdminName = $name;
        if (!is_null($email) && $email !== '') {
            $admin->Email = $email;
        }
        $admin->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'admin' => $admin,
        ]);
    }

    public function changePassword(Request $request)
    {
        $admin = $request->user();

        if (!Hash::check($request->current_password, $admin->Password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $request->validate(['new_password' => 'required|string|min:6']);
        $admin->Password = Hash::make($request->new_password);
        $admin->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function getAdmins(Request $request)
    {
        $currentAdmin = $request->user();
        return response()->json(
            \App\Models\Admin::where('AdminID', '!=', $currentAdmin->AdminID)->get()
        );
    }

    public function addAdmin(Request $request)
    {
        $request->validate([
            'AdminName' => 'required|string|max:255',
            'Email' => 'required|email',
        ]);

        if ($this->emailExistsAcrossAccounts($request->Email)) {
            return response()->json(['errors' => ['Email' => ['This email is already registered.']]], 422);
        }

        $admin = \App\Models\Admin::create([
            'AdminName' => $request->AdminName,
            'Email' => $request->Email,
            'Password' => Hash::make('password'), // default password for new admins
        ]);

        return response()->json($admin, 201);
    }

    public function deleteAdmin($id)
    {
        // Prevent deleting the last admin
        if (\App\Models\Admin::count() <= 1) {
            return response()->json(['message' => 'Cannot delete the only admin.'], 403);
        }
        
        // Prevent deleting oneself if we had the context, but simpler to just delete
        \App\Models\Admin::where('AdminID', $id)->delete();
        return response()->json(['message' => 'Admin deleted successfully']);
    }
}
