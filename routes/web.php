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
Route::get('/admin/profile', function () {
    return view('admin.profile');
});
Route::get('/admin/products', function () {
    return view('admin.products');
});

// Employee
Route::get('/employee/dashboard', function () {
    return view('employee.dashboard');
});
Route::get('/employee/profile', function () {
    return view('employee.profile');
});
Route::get('/employee/products', function () {
    return view('employee.products');
});
Route::get('/employee/coupons', function () {
    return view('employee.coupons');
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
Route::get('/deliveryman/profile', function () {
    return view('deliveryman.profile');
});
