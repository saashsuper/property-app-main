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
});

// Catch-all route for SPA - must be last
Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->where('any', '.*')->name('index');
