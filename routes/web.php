<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get(uri: '/', action: function () {
    return view(view: 'welcome');
});

//Role CRUD (may add some middlewares later)

Route::get(uri: '/roles',action: [RoleController::class,'create'])->name(name: 'roles.create');
Route::get(uri: '/roles/{id}/edit', action: [RoleController::class, 'edit'])->name(name: 'roles.edit');
Route::delete(uri: '/roles/{id}', action: [RoleController::class, 'destroy'])->name(name: 'roles.destroy');
Route::post(uri: '/roles',action: [RoleController::class,'insert'])->name(name: 'roles.insert');
Route::put(uri: '/roles/{id}', action: [RoleController::class, 'update'])->name(name: 'roles.update');


//subscription routes

Route::get('/subscribe', [SubscriptionController::class,'subscribe'])->name(name: 'subscribe');
Route::post('/subscribe', [SubscriptionController::class,'store'])->name(name: 'subscribe.store');
Route::post('/subscribe/{id}',[SubscriptionController::class,'process'])->name('subscribe.process');
Route::get('/register/{token}',[SubscriptionController::class,'choosePassword'])->name('register.complete');
Route::post('/register',[SubscriptionController::class,'completeRegistration'])->name('register.finalization');


// admin routes

Route::get('/admin', [AdminController::class,'index'])->name(name:'admin');


// Auth routes

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//forgotten password routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');
