<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================================================
// Mobile App API Routes
// ============================================================================

// Public Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'apiLogin']);
});

// Protected Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth & Profile
    Route::prefix('auth')->group(function () {
        Route::get('/user', function (\Illuminate\Http\Request $request) {
            return response()->json($request->user());
        });
        Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'apiLogout']);
    });
    
    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', function () {
            // TODO: Create DashboardController
            return response()->json([
                'total_blocks' => \App\Models\Block::count(),
                'total_units' => \App\Models\BlockUnit::count(),
                'total_issues' => \App\Models\BlockIssue::count(),
                'total_work_orders' => \App\Models\BlockWorkOrder::count(),
            ]);
        });
    });
    
    // Work Orders
    Route::prefix('work-orders')->group(function () {
        // Get work orders assigned to current user (contractor)
        Route::get('/my-work-orders', function (\Illuminate\Http\Request $request) {
            $workOrders = \App\Models\BlockWorkOrder::with(['blockUnit', 'priority', 'jobStatus'])
                ->where('contractor_id', $request->user()->id)
                ->latest()
                ->paginate(20);
            return response()->json($workOrders);
        });
        
        Route::get('/', function () {
            // All work orders (admin/inspector view)
            $workOrders = \App\Models\BlockWorkOrder::with(['blockUnit', 'priority', 'jobStatus'])
                ->latest()
                ->paginate(20);
            return response()->json($workOrders);
        });
        
        Route::get('/{id}', function ($id) {
            $workOrder = \App\Models\BlockWorkOrder::with(['blockUnit', 'priority', 'jobStatus', 'images'])
                ->findOrFail($id);
            return response()->json($workOrder);
        });
    });
    
    // Inspections
    Route::prefix('inspections')->group(function () {
        // Get inspections assigned to current user (inspector)
        Route::get('/my-inspections', function (\Illuminate\Http\Request $request) {
            $inspections = \App\Models\Inspection::with(['block'])
                ->where('inspector_id', $request->user()->id)
                ->latest()
                ->paginate(20);
            return response()->json($inspections);
        });
        
        Route::get('/', function () {
            // All inspections (admin view)
            $inspections = \App\Models\Inspection::with(['block'])
                ->latest()
                ->paginate(20);
            return response()->json($inspections);
        });
        
        Route::get('/{id}', function ($id) {
            $inspection = \App\Models\Inspection::with(['block', 'inspectionAssets'])
                ->findOrFail($id);
            return response()->json($inspection);
        });
    });
    
    // Blocks
    Route::prefix('blocks')->group(function () {
        Route::get('/', function () {
            $blocks = \App\Models\Block::with(['units'])->get();
            return response()->json($blocks);
        });
        
        Route::get('/{id}', function ($id) {
            $block = \App\Models\Block::with(['units', 'buildings'])->findOrFail($id);
            return response()->json($block);
        });
    });
    
    // Reference Data
    Route::prefix('reference')->group(function () {
        Route::get('/priorities', function () {
            return response()->json(\App\Models\Priority::all());
        });
        Route::get('/job-statuses', function () {
            return response()->json(\App\Models\JobStatus::all());
        });
        Route::get('/issue-statuses', function () {
            return response()->json(\App\Models\IssueStatus::all());
        });
    });
});

