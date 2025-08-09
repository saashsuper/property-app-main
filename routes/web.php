<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes(['verify' => true]);

Route::get('index/{locale}',[App\Http\Controllers\HomeController::class, 'lang']);
Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('api/dashboard/stats', [App\Http\Controllers\DashboardController::class, 'getDashboardStats'])->name('api.dashboard.stats');
    Route::get('api/dashboard/issues-stats', [App\Http\Controllers\DashboardController::class, 'getIssuesStats'])->name('api.dashboard.issues-stats');
    Route::get('api/dashboard/recent-issues', [App\Http\Controllers\DashboardController::class, 'getRecentIssues'])->name('api.dashboard.recent-issues');
    Route::get('api/dashboard/recent-blocks', [App\Http\Controllers\DashboardController::class, 'getRecentBlocks'])->name('api.dashboard.recent-blocks');
});

// Block Management Routes
Route::middleware(['auth'])->group(function () {
    // Block Types - Admin only
    Route::resource('block-types', App\Http\Controllers\BlockTypeController::class)->middleware('role:Admin');
    Route::get('api/block-types', [App\Http\Controllers\BlockTypeController::class, 'getBlockTypes'])->name('api.block-types');
    
    // Blocks - Admin only for create, edit, delete
    Route::get('blocks', [App\Http\Controllers\BlockController::class, 'index'])->name('blocks.index');
    Route::get('blocks/{block}', [App\Http\Controllers\BlockController::class, 'show'])->name('blocks.show');
    Route::get('api/blocks', [App\Http\Controllers\BlockController::class, 'getBlocks'])->name('api.blocks');
    Route::get('api/blocks/{block}', [App\Http\Controllers\BlockController::class, 'getBlock'])->name('api.blocks.show');
    
    // Admin-only block routes
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('blocks/create', [App\Http\Controllers\BlockController::class, 'create'])->name('blocks.create');
        Route::post('blocks', [App\Http\Controllers\BlockController::class, 'store'])->name('blocks.store');
        Route::get('blocks/{block}/edit', [App\Http\Controllers\BlockController::class, 'edit'])->name('blocks.edit');
        Route::put('blocks/{block}', [App\Http\Controllers\BlockController::class, 'update'])->name('blocks.update');
        Route::delete('blocks/{block}', [App\Http\Controllers\BlockController::class, 'destroy'])->name('blocks.destroy');
    });
    
    // Work Orders
    Route::resource('work-orders', App\Http\Controllers\WorkOrderController::class);
    Route::get('api/work-orders', [App\Http\Controllers\WorkOrderController::class, 'getWorkOrders'])->name('api.work-orders');
    Route::get('api/work-orders/{workOrder}', [App\Http\Controllers\WorkOrderController::class, 'getWorkOrder'])->name('api.work-orders.show');
    
    // Block Work Orders
    Route::resource('block-work-orders', App\Http\Controllers\BlockWorkOrderController::class);
    Route::get('api/block-work-orders', [App\Http\Controllers\BlockWorkOrderController::class, 'getBlockWorkOrders'])->name('api.block-work-orders');
    Route::get('api/block-work-orders/{blockWorkOrder}', [App\Http\Controllers\BlockWorkOrderController::class, 'getBlockWorkOrder'])->name('api.block-work-orders.show');
    
    // Site Visits (Block Visits)
    Route::resource('block-visits', App\Http\Controllers\BlockVisitController::class);

    // Block Issues
    Route::resource('block-issues', App\Http\Controllers\BlockIssueController::class);
    Route::get('api/block-issues', [App\Http\Controllers\BlockIssueController::class, 'getBlockIssues'])->name('api.block-issues');
    Route::get('api/block-issues/{blockIssue}', [App\Http\Controllers\BlockIssueController::class, 'getBlockIssue'])->name('api.block-issues.show');
    
    // General Issues
    Route::resource('issues', App\Http\Controllers\IssueController::class);
    Route::get('api/issues', [App\Http\Controllers\IssueController::class, 'getIssues'])->name('api.issues');
    Route::get('api/issues/{issue}', [App\Http\Controllers\IssueController::class, 'getIssue'])->name('api.issues.show');
});

// Catch-all route for SPA - must be last
Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->where('any', '.*')->name('index');
