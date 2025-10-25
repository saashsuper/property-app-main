<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BlockWorkOrder;
use App\Models\BlockWorkOrderImage;
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
        $perPage = $request->input('per_page', 20);
        $status = $request->input('status');

        $query = BlockWorkOrder::with([
            'block',
            'blockUnit',
            'priority',
            'contractor',
            'images',
        ])->where('contractor_id', $user->id);

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

        $workOrder->update([
            'status' => $request->status,
            'comment' => $request->comment ?? $workOrder->comment,
            'updated_by' => Auth::id(),
        ]);

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
     * Mark work order as complete
     */
    public function complete(Request $request, BlockWorkOrder $workOrder): JsonResponse
    {
        $request->validate([
            'comment' => 'nullable|string',
        ]);

        $workOrder->update([
            'status' => 3, // Completed
            'comment' => $request->comment ?? $workOrder->comment,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Work order marked as completed',
            'data' => $workOrder->fresh(['block', 'blockUnit', 'priority', 'contractor', 'images']),
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

