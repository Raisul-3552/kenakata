<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $sessionUser = session('kenakata_user');
        if (!$sessionUser || $sessionUser['type'] !== 'Employee') {
            return redirect()->route('login.form');
        }
        $employee = DB::table('Employee')->where('EmployeeID', $sessionUser['id'])->first();
        if (!$employee) return redirect()->route('login.form');

        return view('employee.dashboard', compact('employee'));
    }

    public function profile()
    {
        $sessionUser = session('kenakata_user');
        if (!$sessionUser || $sessionUser['type'] !== 'Employee') {
            return redirect()->route('login.form')->withErrors(['auth' => 'Please login as Employee.']);
        }

        $employee = DB::table('Employee')->where('EmployeeID', $sessionUser['id'])->first();
        if (!$employee) {
            return redirect()->route('login.form');
        }

        $admin = null;
        if ($employee && $employee->CodeID) {
            $codeInfo = DB::table('EmploymentCode')->where('CodeID', $employee->CodeID)->first();
            if ($codeInfo) {
                $admin = DB::table('Admin')->where('AdminID', $codeInfo->AdminID)->first();
            }
        }

        return view('employee.profile', compact('employee', 'admin'));
    }
}
