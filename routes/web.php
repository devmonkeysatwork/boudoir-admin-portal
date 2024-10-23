<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomAuthController;
use \App\Http\Controllers\OrdersController;
use App\Http\Controllers\EmailTemplatesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


Route::get('/route-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return redirect()->route('home')->with('cache','System Cache Has Been Removed.');
});
// Public routes
Route::get('/',[CustomAuthController::class, 'showLoginForm'])->name('home');

//Route::get('/login', [CustomAuthController::class, 'showLoginForm'])->name('login');
Route::post('/my_login', [CustomAuthController::class, 'login'])->name('custom.login');
Route::get('/register', [CustomAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [CustomAuthController::class, 'register'])->name('custom.register');
Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    //Admin Routes
    Route::post('/add_worker', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'createUser'])->name('admin.add_worker');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/areas', [AdminController::class, 'areas'])->name('admin.areas');
    Route::get('/orders', [OrdersController::class, 'index'])->name('admin.orders');


    Route::get('/notification', [AdminController::class, 'notification'])->name('admin.notification');

    Route::get('/search_orders', [OrdersController::class, 'search'])->name('search.orders');
    Route::post('/update_order_status', [AdminController::class, 'updateOrderStatus'])->name('admin.update_order_status');
    Route::post('/get_order_details', [OrdersController::class, 'getOrderDetails'])->name('admin.get_order_details');
    Route::post('/order_add_comment', [OrdersController::class, 'addComment'])->name('order.add_comment');
    Route::post('/comment_add_reply', [OrdersController::class, 'addComment'])->name('order.add_reply');

    Route::get('/my_orders', [OrdersController::class, 'myOrders'])->name('worker.my_orders');
    Route::post('/update_order_log', [OrdersController::class, 'updateOrderStatus'])->name('order.add_log');
    Route::post('/end_order_phase', [OrdersController::class, 'endOrderPhase'])->name('order.end_log');

    Route::get('/orders/export', [OrdersController::class, 'exportCSV'])->name('order.csv');
    Route::get('/export-pdf', [OrdersController::class, 'exportPDF'])->name('order.pdf');

    Route::get('/orders/{id}/download-pdf', [OrdersController::class, 'packing_slip'])->name('orders.downloadPDF');


    Route::get('/workstations/{id}', [AdminController::class, 'getWorkstationDetails']);

    Route::get('/team', [AdminController::class, 'team'])->name('admin.team');
    Route::get('/team/{id}', [AdminController::class, 'getTeamDetails']);

    Route::get('/settings', function () {
        return redirect()->route('admin.manage-statuses');
    })->name('admin.settings');
    Route::get('/settings/manage-statuses', [AdminController::class, 'manageStatuses'])->name('admin.manage-statuses');
    Route::post('/add_status', [AdminController::class, 'addStatuses'])->name('admin.add_status');
    Route::post('/delete_status', [AdminController::class, 'deleteStatus'])->name('admin.delete_status');
    Route::post('/update_status', [AdminController::class, 'updateStatus'])->name('admin.update_status');
    Route::get('/barcode/{status_name}', [AdminController::class, 'generateBarcode'])->name('barcode.generate');


    Route::get('/settings/manage-emails', [EmailTemplatesController::class, 'manageEmails'])->name('admin.manage-emails');
    Route::post('/email/add', [EmailTemplatesController::class, 'addEmailTemplate'])->name('email.add');
    Route::post('/email/update_status', [EmailTemplatesController::class, 'updateStatus'])->name('email.update_status');
    Route::delete('/email/delete/{id}', [EmailTemplatesController::class, 'deleteEmail'])->name('email.delete');
    Route::get('/send-template-email/{templateId}/{recipientEmail}', [EmailTemplatesController::class, 'sendTemplateEmail']);
    Route::get('/email/get-template/{id}', [EmailTemplatesController::class, 'getTemplateData'])->name('email.get-template');
    Route::post('/email/update-template/{id}', [EmailTemplatesController::class, 'updateTemplate'])->name('email.update-template');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    Route::get('/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');
    Route::get('/compare', [DashboardController::class, 'compare'])->name('dashboard.compare');
});

Route::get('/barcode-scanner', function () {
    return view('barcode-scanner');
});
Route::get('/barcode-scanner-mobile', function () {
    return view('barcode-scanner-mobile');
});
Route::get('/send_email', [AdminController::class,'sendSummaryEmail'])->name('send-summary-email');



Route::post('/api/save_order', [OrdersController::class, 'store']);

require __DIR__ . '/auth.php';
