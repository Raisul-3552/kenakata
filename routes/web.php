<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

// Admin
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

// Employee
Route::get('/employee/dashboard', function () {
    return view('employee.dashboard');
});

// Customer
Route::get('/customer/dashboard', function () {
    return view('customer.home');
});
Route::get('/customer/cart', function () {
    return view('customer.cart');
});

// DeliveryMan
Route::get('/deliveryman/dashboard', function () {
    return view('deliveryman.dashboard');
});
