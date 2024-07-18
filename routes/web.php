<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/route-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return redirect()->route('home')->with('cache','System Cache Has Been Removed.');
});
// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [CustomAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomAuthController::class, 'login'])->name('custom.login');
Route::get('/register', [CustomAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [CustomAuthController::class, 'register'])->name('custom.register');
Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/workstations', [AdminController::class, 'workstations'])->name('admin.workstations');
    Route::get('/settings', function () {
        return redirect()->route('admin.manage-statuses');
    })->name('admin.settings');

    Route::get('/settings/manage-statuses', [AdminController::class, 'manageStatuses'])->name('admin.manage-statuses');
    Route::post('/add_status', [AdminController::class, 'addStatuses'])->name('admin.add_status');
    Route::post('/delete_status', [AdminController::class, 'deleteStatus'])->name('admin.delete_status');
    Route::get('/settings/manage-emails', [AdminController::class, 'manageEmails'])->name('admin.manage-emails');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/barcode-scanner', function () {
    return view('barcode-scanner');
});



Route::post('/api/save_order', [\App\Http\Controllers\OrdersController::class, 'store']);


require __DIR__ . '/auth.php';
