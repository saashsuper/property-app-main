<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BlockWorkOrder;
use App\Models\BlockWorkOrderImage;
use App\Services\WorkDocketService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class WorkOrderController extends Controller
{
    /**
     * Get list of work orders
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $status = $request->input('status');

        $query = BlockWorkOrder::with([
            'block',
            'blockUnit',
            'priority',
            'contractor',
            'images',
        ]);

        if ($status) {
            $query->where('status', $status);
        }

        $workOrders = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $workOrders,
        ]);
    }

    /**
     * Get work orders assigned to current contractor
     */
    public function myWorkOrders(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['userType', 'contractCompany']);
        $perPage = $request->input('per_page', 20);
        $status = $request->input('status');

        $query = BlockWorkOrder::with([
            'block',
            'blockUnit',
            'priority',
            'contractCompany',
            'jobStatus',
            'images',
        ])->active(); // Only show active work orders

        // Check if user is Contractor Admin
        $isContractorAdmin = $user->userType && $user->userType->name === 'Contractor Admin';
        
        if ($isContractorAdmin && $user->contract_company_id) {
            // For Contractor Admin: show all work orders assigned to their contract company
            // Work orders are assigned to Contractor IDs (from 1_contractors table)
            // The contract_company_id should match the contractor_id in block_work_orders
            $query->where('contractor_id', $user->contract_company_id);
        } else {
            // For regular users: only show work orders assigned to their user ID
            // Note: This may not match many work orders if assignments are company-based
            $query->where('contractor_id', $user->id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $workOrders = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $workOrders,
        ]);
    }

    /**
     * Get single work order
     */
    public function show(BlockWorkOrder $workOrder): JsonResponse
    {
        $workOrder->load([
            'block',
            'blockUnit',
            'blockBuilding',
            'priority',
            'contractor',
            'jobStatus',
            'issuedBy',
            'images',
            'blockIssue',
        ]);

        return response()->json([
            'success' => true,
            'data' => $workOrder,
        ]);
    }

    /**
     * Update work order status
     */
    public function updateStatus(Request $request, BlockWorkOrder $workOrder): JsonResponse
    {
        $request->validate([
            'status' => 'required|integer|in:1,2,3,4,5', // 1=Pending, 2=In Progress, 3=Completed, 4=Cancelled, 5=On Hold
            'comment' => 'nullable|string',
        ]);

        $oldStatus = $workOrder->status;
        $isCompleting = ($oldStatus != 3 && $request->status == 3);

        $workOrder->update([
            'status' => $request->status,
            'comment' => $request->comment ?? $workOrder->comment,
            'updated_by' => Auth::id(),
        ]);

        // Generate work docket PDF when work order is completed
        if ($isCompleting) {
            try {
                $workDocketService = new WorkDocketService();
                $workDocketService->generateWorkDocket($workOrder->fresh());
            } catch (\Exception $e) {
                \Log::error('Failed to generate work docket on status update', [
                    'work_order_id' => $workOrder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Work order status updated successfully',
            'data' => $workOrder->fresh(['block', 'blockUnit', 'priority', 'contractor', 'images']),
        ]);
    }

    /**
     * Upload photos for work order
     */
    public function uploadPhotos(Request $request, BlockWorkOrder $workOrder): JsonResponse
    {
        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'latitude.*' => 'nullable|numeric',
            'longitude.*' => 'nullable|numeric',
        ]);

        $uploadedPhotos = [];
        $photos = $request->file('photos');
        $latitudes = $request->input('latitude', []);
        $longitudes = $request->input('longitude', []);

        foreach ($photos as $index => $photo) {
            $imagePath = 'work-orders/' . $workOrder->id;
            $imageName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();

            // Store the image
            $photo->storeAs('public/' . $imagePath, $imageName);

            // Create database record
            $workOrderImage = BlockWorkOrderImage::create([
                'block_work_order_id' => $workOrder->id,
                'image_path' => $imagePath,
                'image_name' => $imageName,
                'latitude' => $latitudes[$index] ?? null,
                'longitude' => $longitudes[$index] ?? null,
            ]);

            $uploadedPhotos[] = [
                'id' => $workOrderImage->id,
                'image_url' => asset('storage/' . $imagePath . '/' . $imageName),
                'latitude' => $workOrderImage->latitude,
                'longitude' => $workOrderImage->longitude,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Photos uploaded successfully',
            'data' => $uploadedPhotos,
        ]);
    }

    /**
     * Delete photo
     */
    public function deletePhoto(BlockWorkOrder $workOrder, $photoId): JsonResponse
    {
        $photo = BlockWorkOrderImage::where('id', $photoId)
            ->where('block_work_order_id', $workOrder->id)
            ->first();

        if (!$photo) {
            return response()->json([
                'success' => false,
                'message' => 'Photo not found',
            ], 404);
        }

        // Delete file from storage
        Storage::delete('public/' . $photo->image_path . '/' . $photo->image_name);

        // Delete database record
        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Photo deleted successfully',
        ]);
    }

    /**
     * Mark work order as complete.
     * For accepted jobs, only team members can change status.
     */
    public function complete(Request $request, $id): JsonResponse
    {
        $request->validate([
            'comment' => 'nullable|string',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        // Find work order explicitly like resume/pause do
        $workOrder = BlockWorkOrder::with('teamMembers')->findOrFail($id);

        if (!$workOrder->isUserTeamMember($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only team members can change work order status after it has been accepted.',
            ], 403);
        }

        // Get "Completed" job status
        $completedStatus = \App\Models\JobStatus::where('name', 'Completed')->first();
        
        if (!$completedStatus) {
            return response()->json([
                'success' => false,
                'message' => 'Completed status not found'
            ], 404);
        }

        $wasAlreadyCompleted = $workOrder->status == $completedStatus->id;
        
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

        // Update status to "Completed" (exact same pattern as resume/pause)
        $workOrder->status = $completedStatus->id;
        $workOrder->updated_by = $request->user()->id;
        
        // Update comment if provided
        if ($request->filled('comment')) {
            $workOrder->comment = $request->comment;
        }
        
        $workOrder->save();

        // Log status change
        \App\Models\BlockWorkOrderLog::createLog(
            $workOrder->id,
            'completed',
            'Work order completed' . ($request->filled('comment') ? ' with comment' : ''),
            [
                'field_name' => 'status',
                'old_value' => $oldStatusText,
                'new_value' => 'Completed',
                'user_id' => $request->user()->id,
            ]
        );
        
        // Log comment if provided
        if ($request->filled('comment')) {
            \App\Models\BlockWorkOrderLog::createLog(
                $workOrder->id,
                'comment_added',
                'Comment added upon completion',
                [
                    'field_name' => 'comment',
                    'user_id' => $request->user()->id,
                ]
            );
        }

        // Generate work docket PDF when work order is completed (only if not already completed)
        if (!$wasAlreadyCompleted) {
            try {
                $workDocketService = new WorkDocketService();
                // Reload fresh instance for work docket generation
                $freshWorkOrder = $workOrder->fresh();
                $workDocketService->generateWorkDocket($freshWorkOrder);
                
                // Log work docket generation
                \App\Models\BlockWorkOrderLog::createLog(
                    $workOrder->id,
                    'work_docket_generated',
                    'Work docket PDF generated',
                    [
                        'user_id' => $request->user()->id,
                    ]
                );
            } catch (\Exception $e) {
                \Log::error('Failed to generate work docket on completion', [
                    'work_order_id' => $workOrder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Refresh from DB to include pdf_path/pdf_name set by work docket generation
        $workOrder->refresh();
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
            'message' => 'Work order marked as completed',
            'data' => $workOrder,
        ]);
    }

    /**
     * Add note to work order
     */
    public function addNote(Request $request, BlockWorkOrder $workOrder): JsonResponse
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        $existingComment = $workOrder->comment ?? '';
        $newComment = $existingComment . "\n[" . now()->format('Y-m-d H:i') . "] " . $request->note;

        $workOrder->update([
            'comment' => $newComment,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully',
            'data' => $workOrder->fresh(['block', 'blockUnit', 'priority', 'contractor', 'images']),
        ]);
    }
}

