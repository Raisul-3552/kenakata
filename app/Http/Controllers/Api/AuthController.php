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

            $customerRow = DB::selectOne("\n                SELECT TOP 1 * FROM Customer WHERE Email = ?\n            ", [$request->Email]);

            DB::commit();

            $customer = $this->makeTokenableUser('customer', $customerRow);
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

            $deliveryManRow = DB::selectOne("\n                SELECT TOP 1 * FROM DeliveryMan WHERE Email = ?\n            ", [$request->Email]);

            DB::commit();

            $deliveryMan = $this->makeTokenableUser('deliveryman', $deliveryManRow);
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

        try {
            $userModel = $this->makeTokenableUser($account['role'], $user);

            if (!$userModel) {
                return response()->json(['message' => 'Invalid user role'], 401);
            }

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

    private function makeTokenableUser(string $role, object $row)
    {
        $modelMap = [
            'admin' => new \App\Models\Admin(),
            'employee' => new \App\Models\Employee(),
            'customer' => new \App\Models\Customer(),
            'deliveryman' => new \App\Models\DeliveryMan(),
        ];

        $model = $modelMap[$role] ?? null;

        if (!$model) {
            return null;
        }

        $model->forceFill((array) $row);
        $model->exists = true;

        return $model;
    }
    
    public function logout(Request $request)
    {
        return response()->json(['message' => 'Logged out successfully']);
    }
}
