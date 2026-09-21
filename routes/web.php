<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



// user
Route::prefix('user')->group(function() {

    Route::get('', [UserController::class, 'index']);

    Route::get('/login', function() {
        return view('users.login');
    })
    ->name('user_login');

    // Route::post('/login/process', [UserController::class, 'login'])->name('login.process');
    // Route::get('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/register', function() {
        return view('users.register');
    })->name('user_register');
    // Route::post('/create', [UserController::class, 'store'])->name('register_process');
    Route::get('/home', [UserController::class, 'home'])->name('user_home');
    Route::get('edit-user', [UserController::class, 'editPage'])->name('edit_user_page');
});



// admin

Route::prefix('admin')->group(function() {
    // login
    Route::get('/login', function() {
        return view('admin.login');
    })->name('admin_login');

    Route::post('/login/process', [AdminController::class, 'login'])->name('admin_login_process');

    
    // logout
    // Route::get('/logout', [AdminController::class, 'logout'])->name('admin_logout');

    // registration
    Route::get('/register', function() {
        return view('admin.register');
    })->name('admin_register');

    // Route::post('/register', [AdminController::class, 'register'])->name('admin_registration_process');

    
    // view users
    // Route::get('/list', [AdminController::class, 'users'])->name('user_list');


    // add users
    Route::get('add-user', [AdminController::class, 'addUserPage'])->name('add_user_page');

    // Route::post('/addUser', [AdminController::class, 'addUser'])->name('add_user_process');

    
    // search
    // Route::get('/search', [AdminController::class, 'search'])->name('search');


    // edit user
    Route::get('edit-user/{id}', function ($id) {
    return view('admin.edit', ['id' => $id]);
    })->name('view_edit_user');

    // Route::put('/{id}/edit', [AdminController::class, 'update'])->name('update_user_process');

    // delete user
    // Route::delete('/{id}/delete', [AdminController::class, 'destroy'])->name('user_delete');

    
    Route::get('home', [AdminController::class, 'home'])->name('admin_home');
});