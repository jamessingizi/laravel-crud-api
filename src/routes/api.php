<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/user/{id}', [AdminController::class, 'user']);
    Route::post('/create_user', [AdminController::class, 'createUser']);
    Route::put('/update_user', [AdminController::class, 'updateUser']);
});

Route::prefix('user')->middleware(['auth:sanctum', 'user'])->group(function () {
    Route::get('/{id}', [UserController::class, 'user']);
    Route::put('/update_user', [UserController::class, 'updateUser']);
});
