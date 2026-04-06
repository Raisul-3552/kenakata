<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\Concerns\InteractsWithAccountEmails;

class AuthController extends Controller
{
    use InteractsWithAccountEmails;

    public function adminLogin(Request $request)
    {
        return $this->login($request);
    }

    public function employeeLogin(Request $request)
    {
        return $this->login($request);
    }

    public function customerLogin(Request $request)
    {
        return $this->login($request);
    }

    public function deliveryManLogin(Request $request)
    {
        return $this->login($request);
    }

    public function customerRegister(Request $request)
    {
        return $this->register($request);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'CustomerName' => 'required|string|max:255',
            'Phone' => 'required|string|max:20',
            'Email' => 'required|string|email|max:255',
            'Password' => 'required|string|min:6',
            'Address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($this->emailExistsAcrossAccounts($request->Email)) {
            return response()->json(['errors' => ['Email' => ['This email is already registered.']]], 422);
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

    public function deliveryManRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'DelManName' => 'required|string|max:255',
            'Phone' => 'required|string|max:20',
            'Email' => 'required|string|email|max:255',
            'Password' => 'required|string|min:6',
            'Address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($this->emailExistsAcrossAccounts($request->Email)) {
            return response()->json(['errors' => ['Email' => ['This email is already registered.']]], 422);
        }

        $deliveryMan = DeliveryMan::create([
            'DelManName' => $request->DelManName,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
            'Address' => $request->Address,
            'Status' => 'Available'
        ]);

        $token = $deliveryMan->createToken('deliveryman-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $deliveryMan,
            'role' => 'deliveryman'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'Email' => 'required|email',
            'Password' => 'required',
        ]);

        $account = $this->findAccountByEmail($request->Email);
        $user = $account['user'] ?? null;

        if (!$user || !Hash::check($request->Password, $user->Password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken($account['token'])->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'role' => $account['role'],
        ]);
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
