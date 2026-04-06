<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DeliveryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Public Routes ---
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/employee/login', [AuthController::class, 'employeeLogin']);
Route::post('/customer/login', [AuthController::class, 'customerLogin']);
Route::post('/deliveryman/login', [AuthController::class, 'deliveryManLogin']);
Route::post('/customer/register', [AuthController::class, 'customerRegister']);
Route::post('/deliveryman/register', [AuthController::class, 'deliveryManRegister']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// --- Protected Admin Routes ---
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/employees', [AdminController::class, 'getEmployees']);
    Route::get('/customers/search', [AdminController::class, 'searchCustomers']);
    Route::post('/employees', [AdminController::class, 'addEmployee']);
    Route::delete('/employees/{id}', [AdminController::class, 'deleteEmployee']);
    Route::get('/dashboard-stats', [AdminController::class, 'dashboardStats']);
    Route::get('/profile', [AdminController::class, 'getProfile']);
    Route::post('/profile/update', [AdminController::class, 'updateProfile']);
});

// --- Protected Employee Routes ---
Route::middleware('auth:employee')->prefix('employee')->group(function () {
    Route::get('/deliverymen/available', [EmployeeController::class, 'getAvailableDeliveryMen']);
    Route::get('/deliverymen/all', [EmployeeController::class, 'getAllDeliveryMenStatus']);
    Route::get('/products', [EmployeeController::class, 'getProducts']);
    Route::post('/products', [EmployeeController::class, 'addProduct']);
    Route::put('/products/{id}', [EmployeeController::class, 'editProduct']);
    Route::delete('/products/{id}', [EmployeeController::class, 'deleteProduct']);
    
    Route::post('/offers', [EmployeeController::class, 'addOffer']);
    Route::post('/coupons', [EmployeeController::class, 'addCoupon']);
    
    Route::get('/orders', [EmployeeController::class, 'getOrders']);
    Route::post('/orders/{id}/confirm', [EmployeeController::class, 'confirmOrder']);
    Route::post('/orders/{id}/cancel', [EmployeeController::class, 'cancelOrder']);
    Route::post('/orders/{id}/assign-delivery', [EmployeeController::class, 'assignDelivery']);
});

// --- Protected Customer Routes ---
Route::middleware('auth:customer')->prefix('customer')->group(function () {
    Route::get('/products', [ProductController::class, 'index']); // reuse public browse
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/orders', [CustomerController::class, 'placeOrder']);
    Route::get('/orders', [CustomerController::class, 'getOrderHistory']);
    Route::post('/coupons/validate', [CustomerController::class, 'validateCoupon']);
});

// --- Protected DeliveryMan Routes ---
Route::middleware('auth:deliveryman')->prefix('deliveryman')->group(function () {
    Route::get('/deliveries', [DeliveryController::class, 'getAssignedDeliveries']);
    Route::post('/deliveries/{id}/update-status', [DeliveryController::class, 'updateStatus']);
    Route::get('/profile', [DeliveryController::class, 'getProfile']);
    Route::post('/profile/update', [DeliveryController::class, 'updateProfile']);
});

// Common logout
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
