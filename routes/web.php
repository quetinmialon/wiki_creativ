<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use League\Uri\Uri;

Route::get(uri: '/', action: function () {
    return view(view: 'welcome');
});

//Role CRUD (may add some middlewares later)

Route::get(uri: '/roles',action: [RoleController::class,'create'])->name(name: 'roles.create');
Route::get(uri: '/roles/{id}/edit', action: [RoleController::class, 'edit'])->name(name: 'roles.edit');
Route::delete(uri: '/roles/{id}', action: [RoleController::class, 'destroy'])->name(name: 'roles.destroy');
Route::post(uri: '/roles',action: [RoleController::class,'insert'])->name(name: 'roles.insert');
Route::put(uri: '/roles/{id}', action: [RoleController::class, 'update'])->name(name: 'roles.update');

