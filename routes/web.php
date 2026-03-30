<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryManController;

// ── Root ──────────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login.form'));

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login',   [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register',[AuthController::class, 'register'])->name('register');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::get('/codes', [AdminController::class, 'codes'])->name('codes');
    Route::post('/codes/generate', [AdminController::class, 'generateCode'])->name('generate_code');
});

// ── Employee ──────────────────────────────────────────────────────────────────
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [EmployeeController::class, 'profile'])->name('profile');
});

// ── Customer ──────────────────────────────────────────────────────────────────
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
});

// ── Delivery Man ──────────────────────────────────────────────────────────────
Route::prefix('deliveryman')->name('deliveryman.')->group(function () {
    Route::get('/dashboard', [DeliveryManController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [DeliveryManController::class, 'profile'])->name('profile');
});