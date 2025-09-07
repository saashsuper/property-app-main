<?php

use App\Http\Controllers\BlockUnitController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Block Issues Search API
Route::get('/block-issues/search', function (Request $request) {
    $query = \App\Models\BlockIssue::with(['blockUnit', 'priority', 'issueStatus']);
    
    // Filter by block_id
    if ($request->has('block_id')) {
        $query->where('block_id', $request->block_id);
    }
    
    // Filter by unit
    if ($request->has('unit') && $request->unit) {
        $query->where('block_unit_id', $request->unit);
    }
    
    // Filter by status
    if ($request->has('state') && $request->state) {
        $query->where('issue_status_id', $request->state);
    }
    
    // Filter by type
    if ($request->has('type') && $request->type) {
        $query->where('issue_type', $request->type);
    }
    
    // Filter by priority
    if ($request->has('priority') && $request->priority) {
        $query->where('priority_id', $request->priority);
    }
    
    // Filter by keyword (search in issue title, description, and ref_no)
    if ($request->has('keyword') && $request->keyword) {
        $keyword = $request->keyword;
        $query->where(function($q) use ($keyword) {
            $q->where('issue', 'LIKE', "%{$keyword}%")
              ->orWhere('fault_details', 'LIKE', "%{$keyword}%")
              ->orWhere('ref_no', 'LIKE', "%{$keyword}%");
        });
    }
    
    $issues = $query->orderBy('created_at', 'desc')->get();
    
    return response()->json([
        'success' => true,
        'data' => $issues
    ]);
});

// Block Unit Details API
Route::get('/block-units/{blockUnit}', [BlockUnitController::class, 'show']);
