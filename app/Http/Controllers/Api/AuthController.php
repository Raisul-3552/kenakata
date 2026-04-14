<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        DB::beginTransaction();
        try {
            // MSSQL Query: Insert new customer
            DB::insert("
                INSERT INTO Customer (CustomerName, Phone, Email, Password, Address)
                VALUES (?, ?, ?, ?, ?)
            ", [
                $request->CustomerName,
                $request->Phone,
                $request->Email,
                Hash::make($request->Password),
                $request->Address,
            ]);

            // Get the newly created customer as Model instance
            $customer = \App\Models\Customer::where('Email', $request->Email)->first();

            DB::commit();

            // Create token using Sanctum
            $token = $customer->createToken('customer-token')->plainTextToken;

            return response()->json([
                'message' => 'Registration successful',
                'token' => $token,
                'user' => $customer,
                'role' => 'customer'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registration failed: ' . $e->getMessage()], 500);
        }
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

        DB::beginTransaction();
        try {
            // MSSQL Query: Insert new delivery man
            DB::insert("
                INSERT INTO DeliveryMan (DelManName, Phone, Email, Password, Address, Status)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [
                $request->DelManName,
                $request->Phone,
                $request->Email,
                Hash::make($request->Password),
                $request->Address,
                'Available',
            ]);

            // Get the newly created delivery man as Model instance
            $deliveryMan = \App\Models\DeliveryMan::where('Email', $request->Email)->first();

            DB::commit();

            // Create token using Sanctum
            $token = $deliveryMan->createToken('deliveryman-token')->plainTextToken;

            return response()->json([
                'message' => 'Registration successful',
                'token' => $token,
                'user' => $deliveryMan,
                'role' => 'deliveryman'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'Email' => 'required|email',
            'Password' => 'required',
        ]);

        $account = $this->findAccountByEmail($request->Email);
        
        if (!$account) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = $account['user'] ?? null;

        if (!$user || !Hash::check($request->Password, $user->Password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Get the actual Model instance to create a token
        $modelClass = $this->getModelClassForRole($account['role']);
        if (!$modelClass) {
            return response()->json(['message' => 'Invalid user role'], 401);
        }

        try {
            $userModel = $modelClass::where('Email', $request->Email)->firstOrFail();
            
            // Create token using Sanctum
            $tokenResponse = $userModel->createToken($account['token']);
            $token = $tokenResponse->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $userModel,
                'role' => $account['role'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Authentication failed: ' . $e->getMessage()], 500);
        }
    }

    private function getModelClassForRole($role)
    {
        $roleModelMap = [
            'admin' => \App\Models\Admin::class,
            'employee' => \App\Models\Employee::class,
            'customer' => \App\Models\Customer::class,
            'deliveryman' => \App\Models\DeliveryMan::class,
        ];

        return $roleModelMap[$role] ?? null;
    }
    
    public function logout(Request $request)
    {
        // In Sanctum, you'd delete the current access token
        // For this raw SQL version, we just return success
        // You may need to implement token revocation separately
        return response()->json(['message' => 'Logged out successfully']);
    }
}
