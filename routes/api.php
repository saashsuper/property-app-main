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
    Route::get('/vapid-public-key', function () {
        return response()->json(['publicKey' => config('webpush.vapid.public_key')]);
    });
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
        Route::post('/update-fcm-token', [\App\Http\Controllers\Auth\LoginController::class, 'updateFcmToken']);
        Route::post('/update-push-subscription', [\App\Http\Controllers\Auth\LoginController::class, 'updatePushSubscription']);
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
        Route::get('/my-work-orders', [\App\Http\Controllers\Api\Mobile\WorkOrderController::class, 'myWorkOrders']);
        
        Route::get('/', function () {
            // All work orders (admin/inspector view)
            $workOrders = \App\Models\BlockWorkOrder::with(['blockUnit', 'priority', 'jobStatus'])
                ->latest()
                ->paginate(20);
            return response()->json($workOrders);
        });
        
        Route::get('/{id}', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::with([
                'blockUnit',
                'blockBuilding',
                'block',
                'blockIssue',
                'priority',
                'jobStatus',
                'images.creator',
                'notes.creator',
                'contractor',
                'issuedBy',
                'creator',
                'updater',
                'teamMembers',
            ])->findOrFail($id);
            // For accepted jobs, only team members can change status; expose so PWA can show/hide controls
            $user = $request->user();
            $workOrder->setAttribute('is_team_member', $user ? $workOrder->isUserTeamMember($user) : false);
            return response()->json($workOrder);
        });
        
        // Accept work order (uses controller method)
        Route::post('/{blockWorkOrder}/accept', [\App\Http\Controllers\BlockWorkOrderController::class, 'accept']);
        
        // Reject work order (uses controller method)
        Route::post('/{blockWorkOrder}/reject', [\App\Http\Controllers\BlockWorkOrderController::class, 'reject']);
        
        // Start work order (update status from "Accepted" to "In Progress")
        // For accepted jobs, only team members can change status
        Route::post('/{blockWorkOrder}/start', function (\Illuminate\Http\Request $request, \App\Models\BlockWorkOrder $blockWorkOrder) {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            $blockWorkOrder->load('teamMembers');
            if (!$blockWorkOrder->isUserTeamMember($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only team members can change work order status after it has been accepted.',
                ], 403);
            }

            // Get Accepted and In Progress status IDs
            $acceptedStatus = \App\Models\JobStatus::where('name', 'Accepted')->first();
            $inProgressStatus = \App\Models\JobStatus::where('name', 'In Progress')->first();
            
            if (!$acceptedStatus || !$inProgressStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Required job statuses not found in system. Please contact administrator.'
                ], 500);
            }
            
            // Check if work order is in Accepted status
            if ($blockWorkOrder->status != $acceptedStatus->id) {
                $blockWorkOrder->load('jobStatus');
                return response()->json([
                    'success' => false,
                    'message' => 'Work order must be accepted before it can be started. Current status: ' . ($blockWorkOrder->jobStatus ? $blockWorkOrder->jobStatus->name : 'Unknown')
                ], 422);
            }
            
            // Store old status before update
            $oldStatus = $blockWorkOrder->status;
            
            // Update status to In Progress
            $blockWorkOrder->update([
                'status' => $inProgressStatus->id, // In Progress
                'updated_by' => $request->user() ? $request->user()->id : null,
            ]);
            
            // Create log entry
            \App\Models\BlockWorkOrderLog::createLog(
                $blockWorkOrder->id,
                'status_changed',
                'Work order started - status changed to In Progress',
                [
                    'field_name' => 'status',
                    'old_value' => $oldStatus,
                    'new_value' => $inProgressStatus->id,
                    'user_id' => $request->user() ? $request->user()->id : null,
                ]
            );
            
            // Reload with relationships
            $blockWorkOrder->load([
                'blockUnit',
                'blockBuilding',
                'block',
                'blockIssue',
                'priority',
                'jobStatus',
                'images.creator',
                'notes.creator',
                'contractor',
                'issuedBy',
                'creator',
                'updater'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Work order started successfully!',
                'data' => $blockWorkOrder
            ]);
        });
        
        // Pause work order (update status to "On Hold")
        // For accepted jobs, only team members can change status
        Route::post('/{blockWorkOrder}/pause', function (\Illuminate\Http\Request $request, \App\Models\BlockWorkOrder $blockWorkOrder) {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            $blockWorkOrder->load('teamMembers');
            if (!$blockWorkOrder->isUserTeamMember($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only team members can change work order status after it has been accepted.',
                ], 403);
            }

            // Validate pause reason (optional, defaults to generic message)
            $request->validate([
                'reason' => 'nullable|string|max:1000',
            ]);
            
            $pauseReason = $request->reason ?? 'Work order paused by user';
            
            // Get "On Hold" job status
            $onHoldStatus = \App\Models\JobStatus::where('name', 'On Hold')->first();
            
            if (!$onHoldStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'On Hold status not found'
                ], 404);
            }
            
            // Load jobStatus if not already loaded
            if (!$blockWorkOrder->relationLoaded('jobStatus')) {
                $blockWorkOrder->load('jobStatus');
            }
            
            // Store old status before update
            $oldStatusText = $blockWorkOrder->jobStatus ? $blockWorkOrder->jobStatus->name : 'Unknown';
            
            // Update work order status to "On Hold"
            $blockWorkOrder->update([
                'status' => $onHoldStatus->id,
                'updated_by' => $request->user() ? $request->user()->id : null,
            ]);
            
            // Create log entry
            \App\Models\BlockWorkOrderLog::createLog(
                $blockWorkOrder->id,
                'paused',
                "Work order paused. Reason: {$pauseReason}",
                [
                    'field_name' => 'status',
                    'old_value' => $oldStatusText,
                    'new_value' => 'On Hold',
                    'user_id' => $request->user() ? $request->user()->id : null,
                ]
            );
            
            // Create a note with pause reason
            \App\Models\BlockWorkOrderNote::create([
                'block_work_order_id' => $blockWorkOrder->id,
                'note' => 'Pause Reason: ' . $pauseReason,
                'note_type' => 'pause_reason',
                'created_by' => $request->user() ? $request->user()->id : null,
            ]);
            
            // Reload with relationships
            $blockWorkOrder->load([
                'blockUnit',
                'blockBuilding',
                'block',
                'blockIssue',
                'priority',
                'jobStatus',
                'images.creator',
                'notes.creator',
                'contractor',
                'issuedBy',
                'creator',
                'updater'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Work order paused successfully',
                'data' => $blockWorkOrder
            ]);
        });
        
        // Resume work order (update status from "On Hold" to "In Progress")
        // For accepted jobs, only team members can change status
        Route::post('/{id}/resume', function (\Illuminate\Http\Request $request, $id) {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            $workOrder = \App\Models\BlockWorkOrder::with('teamMembers')->findOrFail($id);
            if (!$workOrder->isUserTeamMember($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only team members can change work order status after it has been accepted.',
                ], 403);
            }
            
            // Get "In Progress" job status
            $inProgressStatus = \App\Models\JobStatus::where('name', 'In Progress')->first();
            
            if (!$inProgressStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'In Progress status not found'
                ], 404);
            }
            
            // Get old status for logging
            $oldStatus = $workOrder->status;
            $statusLabels = [
                1 => 'Pending',
                2 => 'In Progress',
                3 => 'Completed',
                4 => 'Cancelled',
                5 => 'On Hold'
            ];
            $oldStatusText = $statusLabels[$oldStatus] ?? 'Unknown';
            
            // Update status to "In Progress"
            $workOrder->status = $inProgressStatus->id;
            $workOrder->updated_by = $request->user()->id;
            $workOrder->save();
            
            // Log status change
            \App\Models\BlockWorkOrderLog::createLog(
                $workOrder->id,
                'resumed',
                'Work order resumed - status changed to In Progress',
                [
                    'field_name' => 'status',
                    'old_value' => $oldStatusText,
                    'new_value' => 'In Progress',
                    'user_id' => $request->user()->id,
                ]
            );
            
            // Reload with relationships
            $workOrder->load([
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
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Work order resumed successfully',
                'data' => $workOrder
            ]);
        });
        
        // Complete work order (update status to "Completed" and generate work docket)
        Route::post('/{id}/complete', [\App\Http\Controllers\Api\Mobile\WorkOrderController::class, 'complete']);
        
        // Download work docket PDF
        Route::get('/{id}/download-docket', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            
            $workDocketService = new \App\Services\WorkDocketService();
            $response = $workDocketService->downloadWorkDocket($workOrder);
            
            if (!$response) {
                return response()->json([
                    'success' => false,
                    'message' => 'Work docket PDF not found. Please ensure the work order is completed.'
                ], 404);
            }
            
            return $response;
        });
        
        // Upload photos for work order
        Route::post('/{id}/photos', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            $user = $request->user();
            
            // Check if job is completed - allow admins to upload photos
            $workOrder->load('jobStatus');
            if ($workOrder->jobStatus && $workOrder->jobStatus->name === 'Completed') {
                $user->load('userType');
                if (!$user->userType || !in_array($user->userType->name, ['Admin', 'Super Admin'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot upload photos to a completed work order',
                    ], 403);
                }
            }
            
            // Check current photo count
            $currentPhotoCount = $workOrder->images()->count();
            
            // Validate
            $request->validate([
                'photos.*' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            ]);
            
            $photos = $request->file('photos');
            $totalPhotos = $currentPhotoCount + count($photos);
            
            // Check if adding these photos would exceed the limit of 6
            if ($totalPhotos > 6) {
                $allowed = 6 - $currentPhotoCount;
                return response()->json([
                    'success' => false,
                    'message' => "Maximum 6 photos allowed. You can add {$allowed} more photo(s).",
                ], 422);
            }
            
            $uploadedPhotos = [];
            $photosCount = count($photos);
            
            foreach ($photos as $photo) {
                $imagePath = 'work-orders/' . $workOrder->id;
                $imageName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                
                // Store the image
                $photo->storeAs('public/' . $imagePath, $imageName);
                
                // Create database record
                $workOrderImage = \App\Models\BlockWorkOrderImage::create([
                    'block_work_order_id' => $workOrder->id,
                    'image_path' => $imagePath,
                    'image_name' => $imageName,
                    's3_status' => 0,
                    'created_by' => $request->user()->id,
                ]);
                
                $uploadedPhotos[] = [
                    'id' => $workOrderImage->id,
                    'image_path' => $imagePath,
                    'image_name' => $imageName,
                ];
            }
            
            // Log photo upload
            \App\Models\BlockWorkOrderLog::createLog(
                $workOrder->id,
                'attachment_added',
                "{$photosCount} photo(s) uploaded",
                [
                    'field_name' => 'images',
                    'new_value' => $photosCount,
                    'user_id' => $request->user()->id,
                ]
            );
            
            // Reload work order with all relationships
            $workOrder->load([
                'blockUnit',
                'blockBuilding',
                'block',
                'blockIssue',
                'priority',
                'jobStatus',
                'images.creator',
                'contractor',
                'issuedBy',
                'creator',
                'updater'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Photos uploaded successfully',
                'data' => $workOrder
            ]);
        });
        
        // Delete photo from work order
        Route::delete('/{id}/photos/{photoId}', function (\Illuminate\Http\Request $request, $id, $photoId) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            $user = $request->user();
            
            // Check if job is completed - allow admins to delete photos
            $workOrder->load('jobStatus');
            if ($workOrder->jobStatus && $workOrder->jobStatus->name === 'Completed') {
                // Allow admins to delete photos from completed work orders
                $user->load('userType');
                if (!$user->userType || !in_array($user->userType->name, ['Admin', 'Super Admin'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete photos from a completed work order',
                    ], 403);
                }
            }
            
            // Find the photo
            $photo = \App\Models\BlockWorkOrderImage::where('id', $photoId)
                ->where('block_work_order_id', $workOrder->id)
                ->first();
            
            if (!$photo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo not found',
                ], 404);
            }
            
            // Check permissions: 
            // 1. User can delete if they created it
            // 2. Contractor Admin can delete any photo from their team's work orders
            $canDelete = false;
            
            if ($photo->created_by === $user->id) {
                // User created this photo
                $canDelete = true;
            } else {
                // Check if user is Contractor Admin and this work order belongs to their team
                $user->load('userType');
                if ($user->userType && $user->userType->name === 'Contractor Admin') {
                    // Check if work order's contractor is in the same contract company
                    if ($workOrder->contractor_id) {
                        $workOrderContractor = \App\Models\User::find($workOrder->contractor_id);
                        if ($workOrderContractor) {
                            $workOrderContractor->load('contractCompany');
                            $user->load('contractCompany');
                            // Check if they're in the same contract company
                            if ($workOrderContractor->contract_company_id === $user->contract_company_id) {
                                $canDelete = true;
                            }
                        }
                    }
                }
            }
            
            if (!$canDelete) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this photo',
                ], 403);
            }
            
            // Delete file from storage
            $filePath = 'public/' . $photo->image_path . '/' . $photo->image_name;
            if (\Illuminate\Support\Facades\Storage::exists($filePath)) {
                \Illuminate\Support\Facades\Storage::delete($filePath);
            }
            
            // Delete database record
            $photo->delete();
            
            // Log photo deletion
            \App\Models\BlockWorkOrderLog::createLog(
                $workOrder->id,
                'attachment_deleted',
                'Photo deleted',
                [
                    'field_name' => 'images',
                    'related_id' => $photoId,
                    'user_id' => $request->user()->id,
                ]
            );
            
            // Reload work order with all relationships
            $workOrder->load([
                'blockUnit',
                'blockBuilding',
                'block',
                'blockIssue',
                'priority',
                'jobStatus',
                'images.creator',
                'contractor',
                'issuedBy',
                'creator',
                'updater'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully',
                'data' => $workOrder
            ]);
        });
        
        // Add note to work order
        Route::post('/{id}/notes', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            
            // Check if job is completed - allow admins to add notes
            $workOrder->load('jobStatus');
            if ($workOrder->jobStatus && $workOrder->jobStatus->name === 'Completed') {
                // Allow admins to add notes to completed work orders
                $user = $request->user();
                $user->load('userType');
                if (!$user->userType || !in_array($user->userType->name, ['Admin', 'Super Admin'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add notes to a completed work order',
                    ], 403);
                }
            }
            
            $request->validate([
                'note' => 'required|string|max:5000',
            ]);
            
            // Create new note
            $note = \App\Models\BlockWorkOrderNote::create([
                'block_work_order_id' => $workOrder->id,
                'note' => $request->note,
                'created_by' => $request->user()->id,
            ]);
            
            // Log note addition
            \App\Models\BlockWorkOrderLog::createLog(
                $workOrder->id,
                'comment_added',
                'Note added to work order',
                [
                    'field_name' => 'notes',
                    'related_id' => $note->id,
                    'user_id' => $request->user()->id,
                ]
            );
            
            // Reload work order with all relationships
            $workOrder->load([
                'blockUnit',
                'blockBuilding',
                'block',
                'blockIssue',
                'priority',
                'jobStatus',
                'images.creator',
                'notes.creator',
                'contractor',
                'issuedBy',
                'creator',
                'updater'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Note added successfully',
                'data' => $workOrder
            ]);
        });
        
        // Update note in work order
        Route::put('/{id}/notes/{noteId}', function (\Illuminate\Http\Request $request, $id, $noteId) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            $user = $request->user();
            
            // Check if job is completed - allow admins to update notes
            $workOrder->load('jobStatus');
            if ($workOrder->jobStatus && $workOrder->jobStatus->name === 'Completed') {
                $user->load('userType');
                if (!$user->userType || !in_array($user->userType->name, ['Admin', 'Super Admin'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot update notes in a completed work order',
                    ], 403);
                }
            }
            
            $request->validate([
                'note' => 'required|string|max:5000',
            ]);
            
            // Find the note
            $note = \App\Models\BlockWorkOrderNote::where('id', $noteId)
                ->where('block_work_order_id', $workOrder->id)
                ->first();
            
            if (!$note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found',
                ], 404);
            }
            
            // Update note
            $note->note = $request->note;
            $note->save();
            
            // Log note update
            \App\Models\BlockWorkOrderLog::createLog(
                $workOrder->id,
                'comment_updated',
                'Note updated',
                [
                    'field_name' => 'notes',
                    'related_id' => $note->id,
                    'user_id' => $user->id,
                ]
            );
            
            // Reload work order with all relationships
            $workOrder->load([
                'blockUnit',
                'blockBuilding',
                'block',
                'blockIssue',
                'priority',
                'jobStatus',
                'images.creator',
                'notes.creator',
                'contractor',
                'issuedBy',
                'creator',
                'updater'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Note updated successfully',
                'data' => $workOrder
            ]);
        });
        
        // Delete note from work order
        Route::delete('/{id}/notes/{noteId}', function (\Illuminate\Http\Request $request, $id, $noteId) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            $user = $request->user();
            
            // Check if job is completed - allow admins to delete notes
            $workOrder->load('jobStatus');
            if ($workOrder->jobStatus && $workOrder->jobStatus->name === 'Completed') {
                // Allow admins to delete notes from completed work orders
                $user->load('userType');
                if (!$user->userType || !in_array($user->userType->name, ['Admin', 'Super Admin'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete notes from a completed work order',
                    ], 403);
                }
            }
            
            // Find the note
            $note = \App\Models\BlockWorkOrderNote::where('id', $noteId)
                ->where('block_work_order_id', $workOrder->id)
                ->first();
            
            if (!$note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found',
                ], 404);
            }
            
            // Check permissions: 
            // 1. User can delete if they created it
            // 2. Admin/Super Admin can delete any note (especially from completed work orders)
            // 3. Contractor Admin can delete any note from their team's work orders
            $canDelete = false;
            $user->load('userType');
            
            if ($note->created_by === $user->id) {
                // User created this note
                $canDelete = true;
            } elseif ($user->userType && in_array($user->userType->name, ['Admin', 'Super Admin'])) {
                // Admin can delete any note
                $canDelete = true;
            } elseif ($user->userType && $user->userType->name === 'Contractor Admin') {
                // Check if work order's contractor is in the same contract company
                if ($workOrder->contractor_id) {
                    $workOrderContractor = \App\Models\User::find($workOrder->contractor_id);
                    if ($workOrderContractor) {
                        $workOrderContractor->load('contractCompany');
                        $user->load('contractCompany');
                        // Check if they're in the same contract company
                        if ($workOrderContractor->contract_company_id === $user->contract_company_id) {
                            $canDelete = true;
                        }
                    }
                }
            }
            
            if (!$canDelete) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this note',
                ], 403);
            }
            
            // Delete note
            $note->delete();
            
            // Log note deletion
            \App\Models\BlockWorkOrderLog::createLog(
                $workOrder->id,
                'comment_updated',
                'Note deleted from work order',
                [
                    'field_name' => 'notes',
                    'related_id' => $noteId,
                    'user_id' => $request->user()->id,
                ]
            );
            
            // Reload work order with all relationships
            $workOrder->load([
                'blockUnit',
                'blockBuilding',
                'block',
                'blockIssue',
                'priority',
                'jobStatus',
                'images.creator',
                'notes.creator',
                'contractor',
                'issuedBy',
                'creator',
                'updater'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully',
                'data' => $workOrder
            ]);
        });
        
        // Get team members for work order
        Route::get('/{id}/team', function ($id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            $teamMembers = [];
            $addedUserIds = [];
            
            // Add primary contractor if exists (contractor_id could be User ID)
            if ($workOrder->contractor_id) {
                // Try to find as User first
                $contractor = \App\Models\User::find($workOrder->contractor_id);
                if ($contractor) {
                    $contractor->load('userType', 'contractCompany');
                    $teamMembers[] = [
                        'id' => 'contractor-' . $contractor->id,
                        'user' => [
                            'id' => $contractor->id,
                            'name' => $contractor->name,
                            'email' => $contractor->email,
                            'phone' => $contractor->phone,
                            'avatar' => $contractor->avatar,
                            'user_type' => $contractor->userType ? [
                                'id' => $contractor->userType->id,
                                'name' => $contractor->userType->name,
                            ] : null,
                        ],
                        'role' => 'Contractor',
                        'is_lead' => true,
                    ];
                    $addedUserIds[] = $contractor->id;
                }
            }
            
            // Add admin (issued_by) if exists and different from contractor
            if ($workOrder->issued_by && !in_array($workOrder->issued_by, $addedUserIds)) {
                $admin = \App\Models\User::find($workOrder->issued_by);
                if ($admin) {
                    $admin->load('userType');
                    $teamMembers[] = [
                        'id' => 'admin-' . $admin->id,
                        'user' => [
                            'id' => $admin->id,
                            'name' => $admin->name,
                            'email' => $admin->email,
                            'phone' => $admin->phone,
                            'avatar' => $admin->avatar,
                            'user_type' => $admin->userType ? [
                                'id' => $admin->userType->id,
                                'name' => $admin->userType->name,
                            ] : null,
                        ],
                        'role' => 'Admin',
                        'is_lead' => true,
                    ];
                    $addedUserIds[] = $admin->id;
                }
            }
            
            // Add team members from block_work_order_teams table (exclude users already added as contractor/admin)
            $workOrder->load(['teamMembers.user.userType', 'teamMembers.addedBy']);
            foreach ($workOrder->teamMembers as $teamMember) {
                if ($teamMember->user && !in_array($teamMember->user->id, $addedUserIds)) {
                    $teamMembers[] = [
                        'id' => $teamMember->id,
                        'user' => [
                            'id' => $teamMember->user->id,
                            'name' => $teamMember->user->name,
                            'email' => $teamMember->user->email,
                            'phone' => $teamMember->user->phone,
                            'avatar' => $teamMember->user->avatar,
                            'user_type' => $teamMember->user->userType ? [
                                'id' => $teamMember->user->userType->id,
                                'name' => $teamMember->user->userType->name,
                            ] : null,
                        ],
                        'role' => $teamMember->role ?? 'Contractor',
                        'is_lead' => $teamMember->is_lead ?? false,
                        'added_by' => $teamMember->added_by,
                        'addedBy' => $teamMember->addedBy ? [
                            'id' => $teamMember->addedBy->id,
                            'name' => $teamMember->addedBy->name,
                            'email' => $teamMember->addedBy->email,
                        ] : null,
                    ];
                    $addedUserIds[] = $teamMember->user->id;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $teamMembers
            ]);
        });
        
        // Get available users to add to team (same contract company)
        Route::get('/{id}/team/available-users', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            $user = $request->user();
            
            // Check if user is Contractor Admin
            $user->load('userType');
            if (!$user->userType || $user->userType->name !== 'Contractor Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Contractor Admin can add team members',
                ], 403);
            }
            
            // Get users from same contract company
            $user->load('contractCompany');
            if (!$user->contract_company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not associated with a contract company',
                ], 403);
            }
            
            // Get existing team member user IDs to exclude
            $excludedUserIds = [];
            
            // Add contractor if exists
            if ($workOrder->contractor_id) {
                $contractor = \App\Models\User::find($workOrder->contractor_id);
                if ($contractor) {
                    $excludedUserIds[] = $contractor->id;
                }
            }
            
            // Add admin if exists
            if ($workOrder->issued_by) {
                $excludedUserIds[] = $workOrder->issued_by;
            }
            
            // Add existing team members from team table
            $workOrder->load('teamMembers');
            foreach ($workOrder->teamMembers as $teamMember) {
                if ($teamMember->user_id) {
                    $excludedUserIds[] = $teamMember->user_id;
                }
            }
            
            // Get all users from same contract company (Contractor Users and Contractor Admin) excluding existing team members
            $availableUsers = \App\Models\User::whereHas('userType', function($q) {
                $q->whereIn('name', ['Contractor User', 'Contractor Admin']);
            })
            ->where('contract_company_id', $user->contract_company_id)
            ->where('is_active', true)
            ->whereNotIn('id', array_unique($excludedUserIds))
            ->with('userType')
            ->orderBy('name')
            ->get();
            
            $usersList = [];
            foreach ($availableUsers as $availableUser) {
                $usersList[] = [
                    'id' => $availableUser->id,
                    'name' => $availableUser->name,
                    'email' => $availableUser->email,
                    'phone' => $availableUser->phone,
                    'avatar' => $availableUser->avatar,
                    'user_type' => $availableUser->userType ? [
                        'id' => $availableUser->userType->id,
                        'name' => $availableUser->userType->name,
                    ] : null,
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $usersList
            ]);
        });
        
        // Add team member
        Route::post('/{id}/team', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            $user = $request->user();
            
            // Check if user is Contractor Admin
            $user->load('userType');
            if (!$user->userType || $user->userType->name !== 'Contractor Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Contractor Admin can add team members',
                ], 403);
            }
            
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);
            
            $newUserId = $request->user_id;
            
            // Get the user to add
            $userToAdd = \App\Models\User::findOrFail($newUserId);
            $userToAdd->load('contractCompany');
            
            // Verify user is from same contract company
            $user->load('contractCompany');
            if ($userToAdd->contract_company_id !== $user->contract_company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only add users from your contract company',
                ], 403);
            }
            
            // Check if user is already in team table
            // Allow contractor admin to add themselves even if they're the primary contractor
            // (They can be both the primary contractor AND in the team table)
            if ($workOrder->contractor_id == $newUserId && $newUserId !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user is already the primary contractor',
                ], 422);
            }
            
            $existingTeamMember = \App\Models\BlockWorkOrderTeam::where('block_work_order_id', $workOrder->id)
                ->where('user_id', $newUserId)
                ->first();
            
            if ($existingTeamMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user is already in the team',
                ], 422);
            }
            
            // Allow contractor admin to add themselves even if they're the primary contractor
            // (This allows them to be in both roles)
            
            // Add team member
            $teamMember = \App\Models\BlockWorkOrderTeam::create([
                'block_work_order_id' => $workOrder->id,
                'user_id' => $newUserId,
                'role' => 'Contractor',
                'is_lead' => false,
                'added_by' => $user->id,
            ]);
            
            // Reload team members
            $workOrder->load(['teamMembers.user.userType', 'teamMembers.addedBy']);
            
            // Build team members list (same structure as GET endpoint)
            $teamMembers = [];
            if ($workOrder->contractor_id) {
                $contractor = \App\Models\User::find($workOrder->contractor_id);
                if ($contractor) {
                    $contractor->load('userType');
                    $teamMembers[] = [
                        'id' => 'contractor-' . $contractor->id,
                        'user' => [
                            'id' => $contractor->id,
                            'name' => $contractor->name,
                            'email' => $contractor->email,
                            'phone' => $contractor->phone,
                            'avatar' => $contractor->avatar,
                            'user_type' => $contractor->userType ? [
                                'id' => $contractor->userType->id,
                                'name' => $contractor->userType->name,
                            ] : null,
                        ],
                        'role' => 'Contractor',
                        'is_lead' => true,
                    ];
                }
            }
            if ($workOrder->issued_by && (!$workOrder->contractor_id || $workOrder->issued_by != $workOrder->contractor_id)) {
                $admin = \App\Models\User::find($workOrder->issued_by);
                if ($admin) {
                    $admin->load('userType');
                    $teamMembers[] = [
                        'id' => 'admin-' . $admin->id,
                        'user' => [
                            'id' => $admin->id,
                            'name' => $admin->name,
                            'email' => $admin->email,
                            'phone' => $admin->phone,
                            'avatar' => $admin->avatar,
                            'user_type' => $admin->userType ? [
                                'id' => $admin->userType->id,
                                'name' => $admin->userType->name,
                            ] : null,
                        ],
                        'role' => 'Admin',
                        'is_lead' => true,
                    ];
                }
            }
            foreach ($workOrder->teamMembers as $tm) {
                if ($tm->user) {
                    $teamMembers[] = [
                        'id' => $tm->id,
                        'user' => [
                            'id' => $tm->user->id,
                            'name' => $tm->user->name,
                            'email' => $tm->user->email,
                            'phone' => $tm->user->phone,
                            'avatar' => $tm->user->avatar,
                            'user_type' => $tm->user->userType ? [
                                'id' => $tm->user->userType->id,
                                'name' => $tm->user->userType->name,
                            ] : null,
                        ],
                        'role' => $tm->role ?? 'Contractor',
                        'is_lead' => $tm->is_lead ?? false,
                        'added_by' => $tm->added_by,
                        'addedBy' => $tm->addedBy ? [
                            'id' => $tm->addedBy->id,
                            'name' => $tm->addedBy->name,
                            'email' => $tm->addedBy->email,
                        ] : null,
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Team member added successfully',
                'data' => $teamMembers
            ]);
        });
        
        // Remove team member
        Route::delete('/{id}/team/{teamMemberId}', function (\Illuminate\Http\Request $request, $id, $teamMemberId) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            $user = $request->user();
            
            // Check if user is Contractor Admin
            $user->load('userType');
            if (!$user->userType || $user->userType->name !== 'Contractor Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Contractor Admin can remove team members',
                ], 403);
            }
            
            // Find team member
            $teamMember = \App\Models\BlockWorkOrderTeam::where('id', $teamMemberId)
                ->where('block_work_order_id', $workOrder->id)
                ->first();
            
            if (!$teamMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found',
                ], 404);
            }
            
            // Cannot remove lead members (primary contractor/admin) unless it's the current user removing themselves
            if ($teamMember->is_lead && $teamMember->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove primary team members',
                ], 403);
            }
            
            // Allow contractor admin to remove themselves even if they're a lead
            // (This allows them to remove themselves from the team table even if they're the primary contractor)
            
            // Verify team member is from same contract company
            $teamMemberUser = \App\Models\User::find($teamMember->user_id);
            if ($teamMemberUser) {
                $teamMemberUser->load('contractCompany');
                $user->load('contractCompany');
                if ($teamMemberUser->contract_company_id !== $user->contract_company_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can only remove team members from your contract company',
                    ], 403);
                }
            }
            
            // Remove team member
            $teamMember->delete();
            
            // Reload team members
            $workOrder->load(['teamMembers.user.userType', 'teamMembers.addedBy']);
            
            // Build team members list (same structure as GET endpoint)
            $teamMembers = [];
            if ($workOrder->contractor_id) {
                $contractor = \App\Models\User::find($workOrder->contractor_id);
                if ($contractor) {
                    $contractor->load('userType');
                    $teamMembers[] = [
                        'id' => 'contractor-' . $contractor->id,
                        'user' => [
                            'id' => $contractor->id,
                            'name' => $contractor->name,
                            'email' => $contractor->email,
                            'phone' => $contractor->phone,
                            'avatar' => $contractor->avatar,
                            'user_type' => $contractor->userType ? [
                                'id' => $contractor->userType->id,
                                'name' => $contractor->userType->name,
                            ] : null,
                        ],
                        'role' => 'Contractor',
                        'is_lead' => true,
                    ];
                }
            }
            if ($workOrder->issued_by && (!$workOrder->contractor_id || $workOrder->issued_by != $workOrder->contractor_id)) {
                $admin = \App\Models\User::find($workOrder->issued_by);
                if ($admin) {
                    $admin->load('userType');
                    $teamMembers[] = [
                        'id' => 'admin-' . $admin->id,
                        'user' => [
                            'id' => $admin->id,
                            'name' => $admin->name,
                            'email' => $admin->email,
                            'phone' => $admin->phone,
                            'avatar' => $admin->avatar,
                            'user_type' => $admin->userType ? [
                                'id' => $admin->userType->id,
                                'name' => $admin->userType->name,
                            ] : null,
                        ],
                        'role' => 'Admin',
                        'is_lead' => true,
                    ];
                }
            }
            foreach ($workOrder->teamMembers as $tm) {
                if ($tm->user) {
                    $teamMembers[] = [
                        'id' => $tm->id,
                        'user' => [
                            'id' => $tm->user->id,
                            'name' => $tm->user->name,
                            'email' => $tm->user->email,
                            'phone' => $tm->user->phone,
                            'avatar' => $tm->user->avatar,
                            'user_type' => $tm->user->userType ? [
                                'id' => $tm->user->userType->id,
                                'name' => $tm->user->userType->name,
                            ] : null,
                        ],
                        'role' => $tm->role ?? 'Contractor',
                        'is_lead' => $tm->is_lead ?? false,
                        'added_by' => $tm->added_by,
                        'addedBy' => $tm->addedBy ? [
                            'id' => $tm->addedBy->id,
                            'name' => $tm->addedBy->name,
                            'email' => $tm->addedBy->email,
                        ] : null,
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Team member removed successfully',
                'data' => $teamMembers
            ]);
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
    
    // Block Issues
    Route::prefix('block-issues')->group(function () {
        // Create issue - POST (must be before /{id})
        Route::post('/', function (\Illuminate\Http\Request $request) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'block_id' => 'required|exists:blocks,id',
                'assigned_to' => 'required|exists:users,id',
                'reported_by' => 'nullable|exists:users,id',
                'issue' => 'required|string|max:255',
                'issue_type' => 'required|string|max:100',
                'priority_id' => 'required|integer|min:1|max:5',
                'contact_details' => 'required|string|max:500',
                'contact_method_id' => 'required|exists:contact_methods,id',
                'issue_details' => 'nullable|string',
                'block_unit_id' => ['nullable', 'exists:block_units,id', \Illuminate\Validation\Rule::exists('block_units', 'id')->where('block_id', $request->block_id)],
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }
            try {
                $reportedBy = $request->reported_by ?: auth()->id() ?: $request->assigned_to;
                $data = [
                    'block_id' => $request->block_id,
                    'assigned_to' => $request->assigned_to,
                    'reported_by' => $reportedBy,
                    'issue' => $request->issue,
                    'issue_type' => $request->issue_type,
                    'priority_id' => $request->priority_id,
                    'issue_status_id' => 1,
                    'status' => \App\Models\BlockIssue::STATUS_ACTIVE,
                    'contact_details' => $request->contact_details,
                    'contact_method_id' => $request->contact_method_id,
                    'issue_details' => $request->issue_details,
                    'block_unit_id' => $request->block_unit_id ?: null,
                    'created_by' => auth()->id() ?: $reportedBy,
                    'updated_by' => auth()->id() ?: $reportedBy,
                    'issued_by' => auth()->id() ?: $reportedBy,
                ];
                $blockIssue = \App\Models\BlockIssue::create($data);
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $imageName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $image->getClientOriginalExtension();
                        $imagePath = 'block-issues/images';
                        $image->storeAs('public/' . $imagePath, $imageName);
                        \App\Models\BlockIssueImage::create([
                            'block_issue_id' => $blockIssue->id,
                            'image_path' => $imagePath,
                            'image_name' => $imageName,
                            's3_status' => false,
                        ]);
                    }
                }
                return response()->json(['success' => true, 'message' => 'Issue created successfully', 'data' => $blockIssue]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Failed to create issue: ' . $e->getMessage()], 500);
            }
        });
        
        // Create form lookup data
        Route::get('/create-data', function () {
            $contactMethods = \App\Models\ContactMethod::orderBy('name')->get(['id', 'name']);
            $issueTypes = \App\Models\IssueType::where('is_active', true)->orderBy('name')->get(['id', 'name']);
            $propertyManagers = \App\Models\User::whereHas('userType', fn($q) => $q->where('name', 'Property manager'))
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
            $users = \App\Models\User::orderBy('name')->get(['id', 'name', 'email']);
            return response()->json([
                'success' => true,
                'data' => [
                    'contact_methods' => $contactMethods,
                    'issue_types' => $issueTypes,
                    'property_managers' => $propertyManagers,
                    'users' => $users,
                ]
            ]);
        });
        
        // Get issues assigned to property manager
        Route::get('/my-issues', function () {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'data' => []
                ], 401);
            }
            
            // Get issues where property manager is assigned or where the block belongs to this PM
            $issues = \App\Models\BlockIssue::with([
                'block',
                'blockUnit',
                'priority',
                'issueStatus',
                'issuedBy'
            ])
            ->where(function($query) use ($user) {
                $query->where('assigned_to_id', $user->id)
                      ->orWhereHas('block', function($q) use ($user) {
                          $q->where('block_manager_id', $user->id);
                      });
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
            return response()->json([
                'success' => true,
                'data' => $issues
            ]);
        });
        
        Route::get('/{id}', function ($id) {
            $blockIssue = \App\Models\BlockIssue::with([
                'block',
                'blockUnit',
                'blockBuilding',
                'priority',
                'issueStatus',
                'issueType',
                'reportedBy',
                'assignedTo',
                'issuedBy',
                'creator',
                'updater',
                'contactMethod',
                'images'
            ])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $blockIssue
            ]);
        });
        
        Route::get('/{id}/photos', function ($id) {
            $blockIssue = \App\Models\BlockIssue::findOrFail($id);
            $photos = $blockIssue->images()->orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $photos
            ]);
        });
    });
    
    // Blocks
    Route::prefix('blocks')->group(function () {
        // Get blocks assigned to property manager (using block_manager_id)
        Route::get('/my-blocks', function (\Illuminate\Http\Request $request) {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'data' => []
                ], 401);
            }
            
            $blocks = \App\Models\Block::with(['units', 'blockManager', 'state', 'country'])
                ->where('block_manager_id', $user->id)
                ->withCount(['units', 'blockIssues as issues_count', 'blockWorkOrders as work_orders_count'])
                ->orderBy('name')
                ->get()
                ->map(function($block) {
                    return [
                        'id' => $block->id,
                        'name' => $block->name,
                        'address' => $block->address1,
                        'city' => $block->state ? $block->state->name : null,
                        'state' => $block->state ? $block->state->name : null,
                        'zip_code' => null,
                        'country' => $block->country ? $block->country->name : null,
                        'description' => $block->management_company,
                        'status' => $block->status === 'active' ? 1 : 0,
                        'property_manager_id' => $block->block_manager_id,
                        'property_manager' => $block->blockManager ? [
                            'id' => $block->blockManager->id,
                            'name' => $block->blockManager->name,
                            'email' => $block->blockManager->email,
                        ] : null,
                        'units_count' => $block->units_count ?? 0,
                        'issues_count' => $block->issues_count ?? 0,
                        'work_orders_count' => $block->work_orders_count ?? 0,
                        'created_at' => $block->created_at,
                        'updated_at' => $block->updated_at,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $blocks
            ]);
        });
        
        Route::get('/', function () {
            $blocks = \App\Models\Block::with(['units'])->get();
            return response()->json($blocks);
        });
        
        Route::get('/{id}', function ($id) {
            $block = \App\Models\Block::with(['units', 'buildings', 'blockManager', 'state', 'country'])
                ->withCount(['units', 'blockIssues as issues_count', 'blockWorkOrders as work_orders_count'])
                ->findOrFail($id);
            
            // Transform for API response (flatten nested objects to strings for frontend)
            $data = [
                'id' => $block->id,
                'name' => $block->name,
                'address' => $block->address1,
                'city' => $block->state ? $block->state->name : null,
                'state' => $block->state ? $block->state->name : null,
                'state_name' => $block->state ? $block->state->name : null,
                'zip_code' => null,
                'country' => $block->country ? $block->country->name : null,
                'description' => $block->management_company,
                'status' => $block->status === 'active' ? 1 : 0,
                'property_manager_id' => $block->block_manager_id,
                'property_manager' => $block->blockManager ? [
                    'id' => $block->blockManager->id,
                    'name' => $block->blockManager->name,
                    'email' => $block->blockManager->email,
                ] : null,
                'units_count' => $block->units_count ?? 0,
                'issues_count' => $block->issues_count ?? 0,
                'work_orders_count' => $block->work_orders_count ?? 0,
                'created_at' => $block->created_at,
                'updated_at' => $block->updated_at,
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        });
        
        // Get issues for a specific block
        Route::get('/{id}/issues', function ($id) {
            $issues = \App\Models\BlockIssue::with([
                'blockUnit',
                'priority',
                'issueStatus',
                'issuedBy',
                'assignedTo'
            ])
            ->withCount([
                'workOrders as active_work_orders_count' => function ($q) {
                    $q->where('status', '!=', 3); // Exclude Completed
                },
            ])
            ->where('block_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
            
            return response()->json([
                'success' => true,
                'data' => $issues
            ]);
        });
        
        // Get unit details (full info, issues, work orders) - must be before /{id}/units
        Route::get('/{blockId}/units/{unitId}', function ($blockId, $unitId) {
            $unit = \App\Models\BlockUnit::with([
                'block', 'building', 'unitType', 'state', 'country'
            ])
                ->where('block_id', $blockId)
                ->where('id', $unitId)
                ->firstOrFail();
            
            $issues = \App\Models\BlockIssue::with(['priority', 'issueStatus', 'issuedBy', 'assignedTo'])
                ->withCount([
                    'workOrders as active_work_orders_count' => function ($q) {
                        $q->where('status', '!=', 3); // Exclude Completed
                    },
                ])
                ->where('block_unit_id', $unitId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($issue) {
                    return [
                        'id' => $issue->id,
                        'issue' => $issue->issue,
                        'title' => $issue->title,
                        'issue_details' => $issue->issue_details,
                        'priority' => $issue->priority ? ['id' => $issue->priority->id, 'name' => $issue->priority->name] : null,
                        'issue_status' => $issue->issueStatus ? ['id' => $issue->issueStatus->id, 'name' => $issue->issueStatus->label ?? $issue->issueStatus->value] : null,
                        'assigned_to' => $issue->assignedTo ? ['id' => $issue->assignedTo->id, 'name' => $issue->assignedTo->name] : null,
                        'active_work_orders_count' => $issue->active_work_orders_count ?? 0,
                    ];
                });
            
            $workOrders = \App\Models\BlockWorkOrder::with(['priority', 'jobStatus', 'contractor'])
                ->where('block_unit_id', $unitId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($wo) {
                    return [
                        'id' => $wo->id,
                        'ref_no' => $wo->ref_no,
                        'issue' => $wo->issue,
                        'job_status' => $wo->jobStatus ? ['id' => $wo->jobStatus->id, 'name' => $wo->jobStatus->name] : null,
                        'priority' => $wo->priority ? ['id' => $wo->priority->id, 'name' => $wo->priority->name] : null,
                        'contractor' => $wo->contractor ? ['id' => $wo->contractor->id, 'name' => $wo->contractor->name] : null,
                    ];
                });
            
            $data = [
                'id' => $unit->id,
                'block_id' => $unit->block_id,
                'unit_code' => $unit->unit_code,
                'unit_name' => $unit->unit_name,
                'owners_name' => $unit->owners_name,
                'salutation' => $unit->salutation,
                'email' => $unit->email,
                'mobile_no' => $unit->mobile_no,
                'phone_number' => $unit->phone_number,
                'address1' => $unit->address1,
                'address2' => $unit->address2,
                'address3' => $unit->address3,
                'letting_agent' => $unit->letting_agent,
                'misc_info' => $unit->misc_info,
                'resident' => $unit->resident,
                'status' => $unit->status,
                'block' => $unit->block ? [
                    'id' => $unit->block->id,
                    'name' => $unit->block->name,
                    'address' => $unit->block->address1,
                ] : null,
                'building' => $unit->building ? ['id' => $unit->building->id, 'name' => $unit->building->name] : null,
                'unit_type' => $unit->unitType ? ['id' => $unit->unitType->id, 'name' => $unit->unitType->name] : null,
                'state' => $unit->state ? $unit->state->name : null,
                'country' => $unit->country ? $unit->country->name : null,
                'zip' => $unit->zip,
                'issues' => $issues->values()->all(),
                'work_orders' => $workOrders->values()->all(),
                'created_at' => $unit->created_at,
                'updated_at' => $unit->updated_at,
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        });
        
        // Get paginated units for a block (descending by created_at)
        Route::get('/{id}/units', function ($id) {
            $perPage = request()->get('per_page', 10);
            $units = \App\Models\BlockUnit::where('block_id', $id)
                ->withCount([
                    'issues as active_issues_count' => function ($q) {
                        $q->where('status', 'active')->whereNull('deleted_at');
                    },
                    'workOrders as active_work_orders_count' => function ($q) {
                        $q->where('status', '!=', 3); // Exclude Completed
                    },
                ])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
            
            $items = $units->getCollection()->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'block_id' => $unit->block_id,
                    'unit_code' => $unit->unit_code,
                    'unit_name' => $unit->unit_name,
                    'active_issues_count' => $unit->active_issues_count ?? 0,
                    'active_work_orders_count' => $unit->active_work_orders_count ?? 0,
                    'created_at' => $unit->created_at,
                    'updated_at' => $unit->updated_at,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $items,
                'meta' => [
                    'current_page' => $units->currentPage(),
                    'last_page' => $units->lastPage(),
                    'per_page' => $units->perPage(),
                    'total' => $units->total(),
                    'from' => $units->firstItem(),
                    'to' => $units->lastItem(),
                ]
            ]);
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

