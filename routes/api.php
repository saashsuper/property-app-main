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
            $user = $request->user();
            $user->load(['userType', 'contractCompany']);
            
            $query = \App\Models\BlockWorkOrder::with(['blockUnit', 'priority', 'jobStatus']);
            
            // Check if user is Contractor Admin
            $isContractorAdmin = $user->userType && $user->userType->name === 'Contractor Admin';
            
            if ($isContractorAdmin && $user->contract_company_id) {
                // For Contractor Admin: show all work orders for users in the same contract company
                // Get all user IDs from the same contract company
                $companyUserIds = \App\Models\User::where('contract_company_id', $user->contract_company_id)
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($companyUserIds)) {
                    // Filter work orders where:
                    // 1. contractor_id is in the list of company user IDs, OR
                    // 2. work order has team members from the same company
                    $query->where(function($q) use ($companyUserIds) {
                        $q->whereIn('contractor_id', $companyUserIds)
                          ->orWhereHas('teamMembers', function($teamQuery) use ($companyUserIds) {
                              $teamQuery->whereIn('user_id', $companyUserIds);
                          });
                    });
                } else {
                    // No other users in company, only show work orders assigned to this user
                    $query->where('contractor_id', $user->id);
                }
            } else {
                // For regular users: only show work orders assigned to them
                $query->where('contractor_id', $user->id);
            }
            
            $workOrders = $query->latest()->paginate(20);
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
                'images.creator',
                'notes.creator',
                'contractor',
                'issuedBy',
                'creator',
                'updater'
            ])->findOrFail($id);
            return response()->json($workOrder);
        });
        
        // Accept work order
        Route::post('/{id}/accept', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            
            // Check if already accepted or rejected
            if ($workOrder->acceptance_status === 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Work order has already been accepted'
                ], 422);
            }
            
            if ($workOrder->acceptance_status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Work order has already been rejected. Cannot accept a rejected work order.'
                ], 422);
            }
            
            // Update acceptance status
            $workOrder->acceptance_status = 'accepted';
            $workOrder->updated_by = $request->user()->id;
            $workOrder->save();
            
            // Reload with relationships
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
                'message' => 'Work order accepted successfully',
                'data' => $workOrder
            ]);
        });
        
        // Reject work order
        Route::post('/{id}/reject', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            
            // Validate rejection reason
            $request->validate([
                'reason' => 'required|string|max:1000',
            ]);
            
            // Check if already accepted or rejected
            if ($workOrder->acceptance_status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Work order has already been rejected'
                ], 422);
            }
            
            if ($workOrder->acceptance_status === 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Work order has already been accepted. Cannot reject an accepted work order.'
                ], 422);
            }
            
            // Update acceptance status
            $workOrder->acceptance_status = 'rejected';
            $workOrder->updated_by = $request->user()->id;
            $workOrder->save();
            
            // Create a note with rejection reason
            \App\Models\BlockWorkOrderNote::create([
                'block_work_order_id' => $workOrder->id,
                'note' => 'Rejection Reason: ' . $request->reason,
                'note_type' => 'rejection_reason',
                'created_by' => $request->user()->id,
            ]);
            
            // Reload with relationships
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
                'message' => 'Work order rejected successfully',
                'data' => $workOrder
            ]);
        });
        
        // Start work order (update status to "In Progress")
        Route::post('/{id}/start', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            
            // Check if work order has been accepted
            if ($workOrder->acceptance_status !== 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Work order must be accepted before it can be started'
                ], 422);
            }
            
            // Get "In Progress" job status
            $inProgressStatus = \App\Models\JobStatus::where('name', 'In Progress')->first();
            
            if (!$inProgressStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'In Progress status not found'
                ], 404);
            }
            
            // Update status to "In Progress"
            $workOrder->status = $inProgressStatus->id;
            $workOrder->updated_by = $request->user()->id;
            $workOrder->save();
            
            // Reload with relationships
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
                'message' => 'Work order started successfully',
                'data' => $workOrder
            ]);
        });
        
        // Pause work order (update status to "On Hold")
        Route::post('/{id}/pause', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            
            // Validate pause reason
            $request->validate([
                'reason' => 'required|string|max:1000',
            ]);
            
            // Get "On Hold" job status
            $onHoldStatus = \App\Models\JobStatus::where('name', 'On Hold')->first();
            
            if (!$onHoldStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'On Hold status not found'
                ], 404);
            }
            
            // Update status to "On Hold"
            $workOrder->status = $onHoldStatus->id;
            $workOrder->updated_by = $request->user()->id;
            $workOrder->save();
            
            // Create a note with pause reason
            \App\Models\BlockWorkOrderNote::create([
                'block_work_order_id' => $workOrder->id,
                'note' => 'Pause Reason: ' . $request->reason,
                'note_type' => 'pause_reason',
                'created_by' => $request->user()->id,
            ]);
            
            // Reload with relationships
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
                'message' => 'Work order paused successfully',
                'data' => $workOrder
            ]);
        });
        
        // Resume work order (update status from "On Hold" to "In Progress")
        Route::post('/{id}/resume', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            
            // Get "In Progress" job status
            $inProgressStatus = \App\Models\JobStatus::where('name', 'In Progress')->first();
            
            if (!$inProgressStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'In Progress status not found'
                ], 404);
            }
            
            // Update status to "In Progress"
            $workOrder->status = $inProgressStatus->id;
            $workOrder->updated_by = $request->user()->id;
            $workOrder->save();
            
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
        
        // Complete work order (update status to "Completed")
        Route::post('/{id}/complete', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            
            // Get "Completed" job status
            $completedStatus = \App\Models\JobStatus::where('name', 'Completed')->first();
            
            if (!$completedStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Completed status not found'
                ], 404);
            }
            
            // Update status to "Completed"
            $workOrder->status = $completedStatus->id;
            $workOrder->updated_by = $request->user()->id;
            $workOrder->save();
            
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
                'message' => 'Work order completed successfully',
                'data' => $workOrder
            ]);
        });
        
        // Upload photos for work order
        Route::post('/{id}/photos', function (\Illuminate\Http\Request $request, $id) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            
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
            
            // Check if job is completed
            $workOrder->load('jobStatus');
            if ($workOrder->jobStatus && $workOrder->jobStatus->name === 'Completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete photos from a completed work order',
                ], 403);
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
            
            // Check if job is completed
            $workOrder->load('jobStatus');
            if ($workOrder->jobStatus && $workOrder->jobStatus->name === 'Completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add notes to a completed work order',
                ], 403);
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
        
        // Delete note from work order
        Route::delete('/{id}/notes/{noteId}', function (\Illuminate\Http\Request $request, $id, $noteId) {
            $workOrder = \App\Models\BlockWorkOrder::findOrFail($id);
            $user = $request->user();
            
            // Check if job is completed
            $workOrder->load('jobStatus');
            if ($workOrder->jobStatus && $workOrder->jobStatus->name === 'Completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete notes from a completed work order',
                ], 403);
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
            // 2. Contractor Admin can delete any note from their team's work orders
            $canDelete = false;
            
            if ($note->created_by === $user->id) {
                // User created this note
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
                    'message' => 'You do not have permission to delete this note',
                ], 403);
            }
            
            // Delete note
            $note->delete();
            
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
            
            return response()->json($blockIssue);
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

