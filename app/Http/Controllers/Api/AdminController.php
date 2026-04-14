<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Concerns\InteractsWithAccountEmails;

class AdminController extends Controller
{
    use InteractsWithAccountEmails;

    public function getEmployees()
    {
        // MSSQL Query: Get all employees
        $employees = DB::select("SELECT * FROM Employee");
        return response()->json($employees);
    }

    public function searchCustomers(Request $request)
    {
        $q = $request->query('q');
        
        // MSSQL Query: Search customers by name or email
        $customers = DB::select("
            SELECT CustomerID, CustomerName, Email, Phone, Address
            FROM Customer
            WHERE CustomerName LIKE ? OR Email LIKE ?
        ", ["%$q%", "%$q%"]);
            
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

        $hashedPassword = Hash::make($request->Password ?? 'password');
        
        // MSSQL Query: Insert new employee
        DB::insert("
            INSERT INTO Employee (AdminID, EmployeeName, Phone, Email, Password, Address)
            VALUES (?, ?, ?, ?, ?, ?)
        ", [
            $request->user()->AdminID,
            $request->EmployeeName,
            $request->Phone ?? 'N/A',
            $request->Email,
            $hashedPassword,
            $request->Address ?? 'N/A',
        ]);

        // Get the inserted employee
        $employee = DB::selectOne("
            SELECT * FROM Employee WHERE Email = ?
        ", [$request->Email]);

        return response()->json($employee, 201);
    }

    public function deleteEmployee($id)
    {
        // MSSQL Query: Delete employee
        DB::delete("DELETE FROM Employee WHERE EmployeeID = ?", [$id]);
        return response()->json(['message' => 'Employee deleted successfully']);
    }

    public function dashboardStats()
    {
        // MSSQL Query: Get dashboard statistics
        $stats = DB::selectOne("
            SELECT 
                (SELECT COUNT(*) FROM Employee) as total_employees,
                (SELECT COUNT(*) FROM Customer) as total_customers,
                (SELECT COUNT(*) FROM Product) as total_products,
                (SELECT COUNT(*) FROM [Order]) as total_orders,
                (SELECT SUM(TotalAmount) FROM [Order] WHERE OrderStatus = 'Confirmed') as total_revenue
        ");
        
        return response()->json([
            'total_employees' => $stats->total_employees ?? 0,
            'total_customers' => $stats->total_customers ?? 0,
            'total_products' => $stats->total_products ?? 0,
            'total_orders' => $stats->total_orders ?? 0,
            'total_revenue' => $stats->total_revenue ?? 0,
        ]);
    }

    public function getProfile(Request $request)
    {
        $admin = $request->user();
        
        // MSSQL Query: Get all employees under this admin
        $employees = DB::select("
            SELECT * FROM Employee WHERE AdminID = ?
        ", [$admin->AdminID]);
        
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
        if ($incomingEmail && $this->emailExistsAcrossAccounts($incomingEmail, ['model' => 'Admin', 'id' => $admin->AdminID])) {
            return response()->json(['message' => 'Email already exists in another account.'], 422);
        }

        // MSSQL Query: Update admin profile
        if (!is_null($email) && $email !== '') {
            DB::update("
                UPDATE [Admin] SET AdminName = ?, Email = ? WHERE AdminID = ?
            ", [$name, $email, $admin->AdminID]);
        } else {
            DB::update("
                UPDATE [Admin] SET AdminName = ? WHERE AdminID = ?
            ", [$name, $admin->AdminID]);
        }

        // Get updated admin data
        $updatedAdmin = DB::selectOne("
            SELECT * FROM [Admin] WHERE AdminID = ?
        ", [$admin->AdminID]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'admin' => $updatedAdmin,
        ]);
    }

    public function changePassword(Request $request)
    {
        $admin = $request->user();

        if (!Hash::check($request->current_password, $admin->Password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $request->validate(['new_password' => 'required|string|min:6']);
        
        $hashedPassword = Hash::make($request->new_password);
        
        // MSSQL Query: Update admin password
        DB::update("
            UPDATE [Admin] SET Password = ? WHERE AdminID = ?
        ", [$hashedPassword, $admin->AdminID]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function getAdmins(Request $request)
    {
        $currentAdmin = $request->user();
        
        // MSSQL Query: Get all other admins
        $admins = DB::select("
            SELECT * FROM [Admin] WHERE AdminID != ?
        ", [$currentAdmin->AdminID]);
        
        return response()->json($admins);
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

        $hashedPassword = Hash::make('password');
        
        // MSSQL Query: Insert new admin
        DB::insert("
            INSERT INTO [Admin] (AdminName, Email, Password)
            VALUES (?, ?, ?)
        ", [
            $request->AdminName,
            $request->Email,
            $hashedPassword,
        ]);

        // Get the inserted admin
        $admin = DB::selectOne("
            SELECT * FROM [Admin] WHERE Email = ?
        ", [$request->Email]);

        return response()->json($admin, 201);
    }

    public function deleteAdmin($id)
    {
        // MSSQL Query: Count admins to prevent deleting the last one
        $adminCount = DB::selectOne("
            SELECT COUNT(*) as count FROM [Admin]
        ");
        
        if ($adminCount->count <= 1) {
            return response()->json(['message' => 'Cannot delete the only admin.'], 403);
        }
        
        // MSSQL Query: Delete admin
        DB::delete("DELETE FROM [Admin] WHERE AdminID = ?", [$id]);
        return response()->json(['message' => 'Admin deleted successfully']);
    }
}
