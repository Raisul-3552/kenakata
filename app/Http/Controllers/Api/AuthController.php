<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function adminLogin(Request $request)
    {
        return $this->login($request, Admin::class, 'admin-token');
    }

    public function employeeLogin(Request $request)
    {
        return $this->login($request, Employee::class, 'employee-token');
    }

    public function customerLogin(Request $request)
    {
        return $this->login($request, Customer::class, 'customer-token');
    }

    public function deliveryManLogin(Request $request)
    {
        return $this->login($request, DeliveryMan::class, 'deliveryman-token');
    }

    public function customerRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'CustomerName' => 'required|string|max:255',
            'Phone' => 'required|string|max:20',
            'Email' => 'required|string|email|max:255|unique:Customer',
            'Password' => 'required|string|min:6',
            'Address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::create([
            'CustomerName' => $request->CustomerName,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
            'Address' => $request->Address,
        ]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $customer,
            'role' => 'customer'
        ], 201);
    }

    private function login(Request $request, $modelClass, $tokenName)
    {
        $request->validate([
            'Email' => 'required|email',
            'Password' => 'required',
        ]);

        $user = $modelClass::where('Email', $request->Email)->first();

        if (!$user || !Hash::check($request->Password, $user->Password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'role' => strtolower(class_basename($modelClass))
        ]);
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
