<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    //  SHOW FORMS
    // ═══════════════════════════════════════════════════════════════

    public function showLoginForm()
    {
        if (session('kenakata_user')) {
            return $this->redirectByType(session('kenakata_user')['type']);
        }
        return view('login');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    // ═══════════════════════════════════════════════════════════════
    //  LOGIN
    // ═══════════════════════════════════════════════════════════════

    public function login(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:Admin,Employee,Customer,DeliveryMan',
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        $type  = $request->user_type;
        $email = $request->email;
        $pass  = $request->password;

        switch ($type) {
            case 'Admin':
                $user = DB::table('Admin')->where('Email', $email)->first();
                if ($user && Hash::check($pass, $user->Password)) {
                    $this->setSession($user->AdminID, $user->AdminName, $email, 'Admin');
                    return redirect()->route('admin.profile');
                }
                break;

            case 'Employee':
                $user = DB::table('Employee')->where('Email', $email)->first();
                if ($user && Hash::check($pass, $user->Password)) {
                    $this->setSession($user->EmployeeID, $user->EmployeeName, $email, 'Employee');
                    return redirect()->route('employee.profile');
                }
                break;

            case 'Customer':
                $user = DB::table('Customer')->where('Email', $email)->first();
                if ($user && Hash::check($pass, $user->Password)) {
                    $this->setSession($user->CustomerID, $user->CustomerName, $email, 'Customer');
                    return redirect()->route('customer.profile');
                }
                break;

            case 'DeliveryMan':
                $user = DB::table('DeliveryMan')->where('Email', $email)->first();
                if ($user && Hash::check($pass, $user->Password)) {
                    $this->setSession($user->DelManID, $user->DelManName, $email, 'DeliveryMan');
                    return redirect()->route('deliveryman.profile');
                }
                break;
        }

        return back()
            ->withErrors(['email' => 'Invalid credentials. Check your email, password, and user type.'])
            ->withInput($request->only('email', 'user_type'));
    }

    // ═══════════════════════════════════════════════════════════════
    //  REGISTER
    // ═══════════════════════════════════════════════════════════════

    public function register(Request $request)
    {
        $type = $request->input('user_type');

        switch ($type) {
            // ── Admin ─────────────────────────────────────────────
            case 'Admin':
                $request->validate([
                    'admin_name' => 'required|string|max:255',
                    'email'      => 'required|email|max:255',
                    'password'   => 'required|min:6|confirmed',
                ]);
                if (DB::table('Admin')->where('Email', $request->email)->exists()) {
                    return back()->withErrors(['email' => 'This email is already registered.'])->withInput();
                }
                $nextId = (DB::table('Admin')->max('AdminID') ?? 0) + 1;
                DB::table('Admin')->insert([
                    'AdminID'   => $nextId,
                    'AdminName' => $request->admin_name,
                    'Email'     => $request->email,
                    'Password'  => Hash::make($request->password),
                ]);
                break;

            // ── Employee ──────────────────────────────────────────
            case 'Employee':
                $request->validate([
                    'employee_name' => 'required|string|max:255',
                    'phone'         => 'required|string|max:20',
                    'email'         => 'required|email|max:255',
                    'password'      => 'required|min:6|confirmed',
                    'address'       => 'required|string|max:500',
                    'reg_code'      => 'required|string',
                ]);
                if (DB::table('Employee')->where('Email', $request->email)->exists()) {
                    return back()->withErrors(['email' => 'This email is already registered.'])->withInput();
                }
                
                // Validate and consume the EmploymentCode
                $code = DB::table('EmploymentCode')->where('RegCode', $request->reg_code)->first();
                
                if (!$code) {
                    return back()->withErrors(['reg_code' => 'Invalid Employment Code.'])->withInput();
                }
                if ($code->IsUsed) {
                    return back()->withErrors(['reg_code' => 'This Employment Code has already been used.'])->withInput();
                }

                DB::table('Employee')->insert([
                    'EmployeeName' => $request->employee_name,
                    'Phone'        => $request->phone,
                    'Email'        => $request->email,
                    'Password'     => Hash::make($request->password),
                    'Address'      => $request->address,
                    'CodeID'       => $code->CodeID,
                ]);

                // Mark the code as used
                DB::table('EmploymentCode')
                    ->where('CodeID', $code->CodeID)
                    ->update(['IsUsed' => 1]);
                break;

            // ── Customer ──────────────────────────────────────────
            case 'Customer':
                $request->validate([
                    'customer_name' => 'required|string|max:255',
                    'phone'         => 'required|string|max:20',
                    'email'         => 'required|email|max:255',
                    'password'      => 'required|min:6|confirmed',
                    'address'       => 'required|string|max:500',
                ]);
                if (DB::table('Customer')->where('Email', $request->email)->exists()) {
                    return back()->withErrors(['email' => 'This email is already registered.'])->withInput();
                }
                DB::table('Customer')->insert([
                    'CustomerName' => $request->customer_name,
                    'Phone'        => $request->phone,
                    'Email'        => $request->email,
                    'Password'     => Hash::make($request->password),
                    'Address'      => $request->address,
                ]);
                break;

            // ── Delivery Man ──────────────────────────────────────
            case 'DeliveryMan':
                $request->validate([
                    'delman_name' => 'required|string|max:255',
                    'phone'       => 'required|string|max:20',
                    'email'       => 'required|email|max:255',
                    'password'    => 'required|min:6|confirmed',
                    'address'     => 'required|string|max:500',
                ]);
                if (DB::table('DeliveryMan')->where('Email', $request->email)->exists()) {
                    return back()->withErrors(['email' => 'This email is already registered.'])->withInput();
                }
                DB::table('DeliveryMan')->insert([
                    'DelManName' => $request->delman_name,
                    'Phone'      => $request->phone,
                    'Email'      => $request->email,
                    'Password'   => Hash::make($request->password),
                    'Address'    => $request->address,
                ]);
                break;

            default:
                return back()->withErrors(['user_type' => 'Please select a valid user type.'])->withInput();
        }

        return redirect()->route('login.form')
            ->with('success', 'Account created successfully! You can now log in.');
    }

    // ═══════════════════════════════════════════════════════════════
    //  LOGOUT
    // ═══════════════════════════════════════════════════════════════

    public function logout(Request $request)
    {
        $request->session()->forget('kenakata_user');
        return redirect()->route('login.form')->with('success', 'You have been logged out.');
    }

    // ═══════════════════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════════════════

    private function setSession($id, $name, $email, $type): void
    {
        session(['kenakata_user' => [
            'id'    => $id,
            'name'  => $name,
            'email' => $email,
            'type'  => $type,
        ]]);
    }

    private function redirectByType(string $type)
    {
        return match ($type) {
            'Admin'       => redirect()->route('admin.profile'),
            'Employee'    => redirect()->route('employee.profile'),
            'Customer'    => redirect()->route('customer.profile'),
            'DeliveryMan' => redirect()->route('deliveryman.profile'),
            default       => redirect()->route('login.form'),
        };
    }
}