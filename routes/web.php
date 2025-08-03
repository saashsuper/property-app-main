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

// Block Management Routes
Route::middleware(['auth'])->group(function () {
    // Block Types
    Route::resource('block-types', App\Http\Controllers\BlockTypeController::class);
    Route::get('api/block-types', [App\Http\Controllers\BlockTypeController::class, 'getBlockTypes'])->name('api.block-types');
    
    // Blocks
    Route::resource('blocks', App\Http\Controllers\BlockController::class);
    Route::get('api/blocks', [App\Http\Controllers\BlockController::class, 'getBlocks'])->name('api.blocks');
    Route::get('api/blocks/{block}', [App\Http\Controllers\BlockController::class, 'getBlock'])->name('api.blocks.show');
    
    // Work Orders
    Route::resource('work-orders', App\Http\Controllers\WorkOrderController::class);
    Route::get('api/work-orders', [App\Http\Controllers\WorkOrderController::class, 'getWorkOrders'])->name('api.work-orders');
    Route::get('api/work-orders/{workOrder}', [App\Http\Controllers\WorkOrderController::class, 'getWorkOrder'])->name('api.work-orders.show');
    
    // Block Work Orders
    Route::resource('block-work-orders', App\Http\Controllers\BlockWorkOrderController::class);
    Route::get('api/block-work-orders', [App\Http\Controllers\BlockWorkOrderController::class, 'getBlockWorkOrders'])->name('api.block-work-orders');
    Route::get('api/block-work-orders/{blockWorkOrder}', [App\Http\Controllers\BlockWorkOrderController::class, 'getBlockWorkOrder'])->name('api.block-work-orders.show');
    
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
