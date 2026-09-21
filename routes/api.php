<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// admin

Route::post('/admin/register', [AuthController::class, 'adminRegister'])
->name('admin.register');

Route::post('/admin/login', [AuthController::class, 'adminLogin'])
->name('admin.login_process');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('admin')->group(function() {
        Route::get('users-data', [AdminController::class, 'users']);
        Route::post('add-user', [AdminController::class, 'addUser'])->name('add_user_process');
        Route::get('user-data', [AdminController::class, 'search'])->name('user_search_process');
        Route::get('user/{id}', [AdminController::class, 'getUser'])->name('get_user');
        Route::put('{id}/edit-user', [AdminController::class, 'editUser'])->name('edit_user');
        Route::delete('{id}/delete-user', [AdminController::class, 'deleteUser'])->name('delete_user_process');
        Route::post('logout', [AuthController::class, 'adminLogout'])->name('admin_logout');
    });
});



// user

Route::post('user/register', [AuthController::class, 'userRegister'])->name('user_register_process');
Route::post('user/login', [AuthController::class, 'userLogin'])->name('user_login_process');

Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('user')->group(function() {
        Route::get('', [UserController::class, 'currentUser'])->name('get_current_user');
        Route::post('logout', [AuthController::class, 'userLogout'])->name('user_logout');
        Route::put('edit-user', [UserController::class, 'editInfo'])->name('edit_info_process');
    });
});