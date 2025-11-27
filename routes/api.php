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
Route::middleware(\App\Http\Middleware\AuthenticateWithSanctum::class)->group(function () {
    
    // Auth & Profile
    Route::prefix('auth')->group(function () {
        Route::get('/user', function (\Illuminate\Http\Request $request) {
            $user = $request->user()->load(['userType', 'roles', 'contractCompany']);
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'avatar' => $user->avatar,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'user_type' => $user->userType ? [
                    'id' => $user->userType->id,
                    'name' => $user->userType->name,
                    'description' => $user->userType->description,
                ] : null,
                'contract_company' => $user->contractCompany ? [
                    'id' => $user->contractCompany->id,
                    'name' => $user->contractCompany->name,
                    'description' => $user->contractCompany->description ?? null,
                ] : null,
                'roles' => $user->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                ])->toArray(),
            ]);
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
            $workOrder = \App\Models\BlockWorkOrder::with([
                'blockUnit',
                'blockBuilding',
                'block',
                'blockIssue',
                'priority',
                'jobStatus',
                'images',
                'contractor',
                'issuedBy',
                'creator',
                'updater'
            ])->findOrFail($id);
            return response()->json($workOrder);
        });
    });
    
    // Inspections
    Route::prefix('inspections')->group(function () {
        // Get inspections assigned to current user (inspector)
        Route::get('/my-inspections', function (\Illuminate\Http\Request $request) {
            $inspections = \App\Models\BlockInspection::with(['block', 'inspectionTeams'])
                ->whereHas('inspectionTeams', function($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                })
                ->latest()
                ->paginate(20);
            return response()->json($inspections);
        });
        
        Route::get('/', function () {
            // All inspections (admin view)
            $inspections = \App\Models\BlockInspection::with(['block'])
                ->latest()
                ->paginate(20);
            return response()->json($inspections);
        });
        
        Route::get('/{id}', function ($id) {
            $inspection = \App\Models\BlockInspection::with([
                'block',
                'inspectionAssets.images',
                'inspectionTeams.user',
                'creator',
                'updater'
            ])->findOrFail($id);
            
            return response()->json([
                'id' => $inspection->id,
                'ref_no' => $inspection->ref_no,
                'block' => $inspection->block ? [
                    'id' => $inspection->block->id,
                    'name' => $inspection->block->name,
                    'management_company' => $inspection->block->management_company,
                    'address' => trim(($inspection->block->address1 ?? '') . ', ' . ($inspection->block->address2 ?? '') . ', ' . ($inspection->block->address3 ?? ''), ', '),
                    'no_of_units' => $inspection->block->no_of_units,
                    'car_spaces' => $inspection->block->car_spaces,
                ] : null,
                'scheduled_date_time' => $inspection->scheduled_date_time,
                'start_date_time' => $inspection->start_date_time,
                'end_date_time' => $inspection->end_date_time,
                'notes' => $inspection->notes,
                'status' => [
                    'id' => $inspection->job_status_id,
                    'name' => $inspection->status_text,
                    'color' => $inspection->status_color,
                ],
                'is_mobile' => $inspection->is_mobile,
                'inspection_teams' => $inspection->inspectionTeams->map(function($team) {
                    return [
                        'id' => $team->id,
                        'user' => $team->user ? [
                            'id' => $team->user->id,
                            'name' => $team->user->name,
                            'email' => $team->user->email,
                        ] : null,
                        'role' => $team->role,
                        'is_lead' => $team->is_lead,
                    ];
                })->toArray(),
                'inspection_assets' => $inspection->inspectionAssets->map(function($asset) {
                    return [
                        'id' => $asset->id,
                        'asset_name' => $asset->asset_name ?? 'N/A',
                        'notes' => $asset->notes,
                        'images_count' => $asset->images->count(),
                    ];
                })->toArray(),
                'created_by' => $inspection->creator ? [
                    'id' => $inspection->creator->id,
                    'name' => $inspection->creator->name,
                ] : null,
                'created_at' => $inspection->created_at,
                'updated_at' => $inspection->updated_at,
            ]);
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

