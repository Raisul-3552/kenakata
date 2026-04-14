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
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
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
    
    // Admin management
    Route::get('/all-admins', [AdminController::class, 'getAdmins']);
    Route::post('/all-admins', [AdminController::class, 'addAdmin']);
    Route::delete('/all-admins/{id}', [AdminController::class, 'deleteAdmin']);

    Route::get('/dashboard-stats', [AdminController::class, 'dashboardStats']);
    Route::get('/profile', [AdminController::class, 'getProfile']);
    Route::put('/profile', [AdminController::class, 'updateProfile']);
    Route::post('/profile/change-password', [AdminController::class, 'changePassword']);
});

// --- Protected Employee Routes ---
Route::middleware('auth:employee')->prefix('employee')->group(function () {
    // Profile
    Route::get('/profile', [EmployeeController::class, 'getProfile']);
    Route::put('/profile', [EmployeeController::class, 'updateProfile']);
    Route::post('/profile/change-password', [EmployeeController::class, 'changePassword']);

    // Delivery
    Route::get('/deliverymen/available', [EmployeeController::class, 'getAvailableDeliveryMen']);
    Route::get('/deliverymen/all', [EmployeeController::class, 'getAllDeliveryMenStatus']);
    Route::get('/deliverymen', [EmployeeController::class, 'getDeliveryMen']);
    Route::post('/deliverymen', [EmployeeController::class, 'addDeliveryMan']);
    Route::delete('/deliverymen/{id}', [EmployeeController::class, 'deleteDeliveryMan']);
    Route::get('/products', [EmployeeController::class, 'getProducts']);
    Route::post('/products', [EmployeeController::class, 'addProduct']);
    Route::put('/products/{id}', [EmployeeController::class, 'editProduct']);
    Route::delete('/products/{id}', [EmployeeController::class, 'deleteProduct']);

    // Offers
    Route::get('/offers', [EmployeeController::class, 'getOffers']);
    Route::post('/offers', [EmployeeController::class, 'addOffer']);
    Route::delete('/offers/{id}', [EmployeeController::class, 'deleteOffer']);

    // Coupons
    Route::get('/coupons', [EmployeeController::class, 'getCoupons']);
    Route::post('/coupons', [EmployeeController::class, 'addCoupon']);
    Route::delete('/coupons/{id}', [EmployeeController::class, 'deleteCoupon']);

    // Orders
    Route::get('/orders', [EmployeeController::class, 'getOrders']);
    Route::post('/orders/{id}/confirm', [EmployeeController::class, 'confirmOrder']);
    Route::post('/orders/{id}/cancel', [EmployeeController::class, 'cancelOrder']);
    Route::post('/orders/{id}/assign-delivery', [EmployeeController::class, 'assignDelivery']);

    // Categories (for dropdowns)
    Route::get('/categories', [EmployeeController::class, 'getCategories']);
});

// --- Protected Customer Routes ---
Route::middleware('auth:customer')->prefix('customer')->group(function () {
    // Profile
    Route::get('/profile', [CustomerController::class, 'getProfile']);
    Route::put('/profile', [CustomerController::class, 'updateProfile']);
    Route::post('/profile/change-password', [CustomerController::class, 'changePassword']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Orders
    Route::post('/orders', [CustomerController::class, 'placeOrder']);
    Route::get('/orders', [CustomerController::class, 'getOrderHistory']);
    Route::post('/orders/{id}/cancel', [CustomerController::class, 'cancelOrder']);

    // Coupons
    Route::post('/coupons/validate', [CustomerController::class, 'validateCoupon']);
    Route::post('/deliveries/{id}/rate', [CustomerController::class, 'rateRider']);
});

// --- Protected DeliveryMan Routes ---
Route::middleware('auth:deliveryman')->prefix('deliveryman')->group(function () {
    Route::get('/deliveries', [DeliveryController::class, 'getAssignedDeliveries']);
    Route::post('/deliveries/{id}/update-status', [DeliveryController::class, 'updateStatus']);
    Route::get('/profile', [DeliveryController::class, 'getProfile']);
    Route::put('/profile', [DeliveryController::class, 'updateProfile']);
    Route::post('/profile/change-password', [DeliveryController::class, 'changePassword']);
    Route::post('/profile/toggle-status', [DeliveryController::class, 'toggleStatus']);
});

// Common logout
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
