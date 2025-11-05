<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockVisit;
use App\Models\IssueLog;
use App\Models\JobReason;
use App\Models\JobStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlockVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlockVisit::with(['block', 'jobReason', 'jobStatus', 'createdByUser', 'team.user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('block', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('jobReason', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('team.user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by job reason
        if ($request->filled('job_reason_id')) {
            $query->where('job_reason_id', $request->job_reason_id);
        }

        // Filter by job status
        if ($request->filled('job_status_id')) {
            $query->where('job_status_id', $request->job_status_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date_time', '<=', $request->date_to);
        }

        $visits = $query->orderBy('scheduled_date_time', 'desc')->get();

        // Get job reasons and statuses for filters
        $jobReasons = JobReason::all();
        $jobStatuses = JobStatus::all();

        return view('block-visits.index', compact('visits', 'jobReasons', 'jobStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $blocks = Block::all();
        $users = User::all();
        $jobReasons = JobReason::all();
        $jobStatuses = JobStatus::all();

        return view('block-visits.create', compact('blocks', 'users', 'jobReasons', 'jobStatuses'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'block_id' => 'required|exists:blocks,id',
            'block_issue_id' => 'nullable|exists:block_issues,id',
            'block_unit_id' => 'nullable|exists:block_units,id',
            'user_id' => 'required|exists:users,id',
            'scheduled_date_time' => 'required|date',
            'job_reason_id' => 'nullable|exists:job_reasons,id',
            'notes' => 'nullable|string|max:255',
            'files.*' => 'nullable|file|max:5120|mimes:jpeg,jpg,png,pdf,doc,docx', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Log incoming request data for debugging
            \Log::info('BlockVisit Store Request', [
                'has_files' => $request->hasFile('files'),
                'files_count' => $request->file('files') ? count($request->file('files')) : 0,
                'all_files' => $request->allFiles(),
            ]);

            $blockVisit = BlockVisit::create([
                'block_id' => $request->block_id,
                'block_issue_id' => $request->block_issue_id,
                'block_unit_id' => $request->block_unit_id,
                'ref_no' => 'SV-' . strtoupper(Str::random(6)),
                'scheduled_date_time' => $request->scheduled_date_time,
                'job_reason_id' => $request->job_reason_id,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Create team member (user assigned to the visit)
            $blockVisit->team()->create([
                'user_id' => $request->user_id,
                'leed' => true, // Set as lead team member
                'created_by' => auth()->id(),
            ]);

            // Handle file uploads
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                \Log::info('Processing files', ['count' => count($files)]);
                
                foreach ($files as $file) {
                    $timestamp = now()->format('YmdHis');
                    $randomString = Str::random(8);
                    $originalName = $file->getClientOriginalName();
                    $fileName = $timestamp . '_' . $randomString . '_' . $originalName;
                    
                    // Store file in storage/app/public/block-visits/{block_visit_id}
                    $path = $file->storeAs('block-visits/' . $blockVisit->id, $fileName, 'public');
                    
                    \Log::info('File stored', ['path' => $path, 'fileName' => $fileName]);
                    
                    // Save to database
                    $blockVisit->images()->create([
                        'image_path' => 'block-visits/' . $blockVisit->id,
                        'image_name' => $fileName,
                        's3_status' => false,
                    ]);
                }
            } else {
                \Log::info('No files in request');
            }

            // Create issue log entry if this site visit is related to an issue
            if ($blockVisit->block_issue_id) {
                $assignedUser = $blockVisit->team->first()->user ?? null;
                $userName = $assignedUser ? $assignedUser->name : 'user';
                IssueLog::createLog(
                    $blockVisit->block_issue_id,
                    'site_visit_assigned',
                    "Site visit {$blockVisit->ref_no} assigned to {$userName}",
                    [
                        'related_id' => $blockVisit->id,
                        'related_type' => 'BlockVisit',
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Site visit scheduled successfully',
                'data' => $blockVisit->load('jobReason', 'createdByUser', 'team', 'images')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error scheduling site visit: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, BlockVisit $blockVisit)
    {
        $blockVisit->load([
            'block.blockType', 
            'blockUnit.unitType', 
            'blockIssue.issueType', 
            'blockIssue.issueStatus',
            'blockIssue.priority',
            'jobReason', 
            'jobStatus', 
            'createdByUser', 
            'updatedByUser', 
            'team.user', 
            'images',
            'results'
        ]);

        if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
            $data = [
                'id' => $blockVisit->id,
                'ref_no' => $blockVisit->ref_no,
                'scheduled_date_time' => $blockVisit->scheduled_date_time,
                'job_reason_id' => $blockVisit->job_reason_id,
                'notes' => $blockVisit->notes,
                'start_date_time' => $blockVisit->start_date_time,
                'end_date_time' => $blockVisit->end_date_time,
                'created_by' => $blockVisit->created_by,
                // relations in camelCase to match frontend expectations
                'jobReason' => $blockVisit->jobReason,
                'jobStatus' => $blockVisit->jobStatus,
                'createdByUser' => $blockVisit->createdByUser,
                'updatedByUser' => $blockVisit->updatedByUser,
                'images' => $blockVisit->images,
                'team' => $blockVisit->team->map(function($member) {
                    return [
                        'user_id' => $member->user_id,
                        'leed' => (bool) $member->leed,
                        'user' => $member->user,
                    ];
                })->values(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        // Get data for edit modal
        $users = User::active()->orderBy('name')->get();
        $jobReasons = JobReason::all();
        $jobStatuses = JobStatus::all();
        
        return view('block-visits.show', compact('blockVisit', 'users', 'jobReasons', 'jobStatuses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlockVisit $blockVisit)
    {
        $blockVisit->load(['team.user']);
        $blocks = Block::all();
        $users = User::all();
        $jobReasons = JobReason::all();
        $jobStatuses = JobStatus::all();

        return view('block-visits.edit', compact('blockVisit', 'blocks', 'users', 'jobReasons', 'jobStatuses'));
    }

    public function update(Request $request, BlockVisit $blockVisit)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'scheduled_date_time' => 'required|date',
            'start_date_time' => 'nullable|date',
            'end_date_time' => 'nullable|date',
            'job_reason_id' => 'nullable|exists:job_reasons,id',
            'job_status_id' => 'nullable|exists:job_statuses,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $blockVisit->update([
                'scheduled_date_time' => $request->scheduled_date_time,
                'start_date_time' => $request->start_date_time,
                'end_date_time' => $request->end_date_time,
                'job_reason_id' => $request->job_reason_id,
                'job_status_id' => $request->job_status_id,
                'notes' => $request->notes,
                'updated_by' => auth()->id(),
            ]);

            // Update team member if user_id is provided
            if ($request->filled('user_id')) {
                // First, remove existing team members
                $blockVisit->team()->delete();
                
                // Add the new team member
                $blockVisit->team()->create([
                    'user_id' => $request->user_id,
                    'leed' => true, // Set as lead team member
                    'created_by' => auth()->id(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Site visit updated successfully',
                'data' => $blockVisit->load('jobReason', 'updatedByUser', 'team')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating site visit: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(BlockVisit $blockVisit)
    {
        try {
            $blockVisit->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Site visit deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting site visit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get block visits for a specific block
     */
    public function getBlockVisits($blockId)
    {
        try {
            $visits = BlockVisit::where('block_id', $blockId)
                ->with(['jobReason', 'jobStatus', 'team.user'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($visit) {
                    return [
                        'id' => $visit->id,
                        'ref_no' => $visit->ref_no,
                        'scheduled_date_time' => $visit->scheduled_date_time,
                        'job_reason_name' => $visit->jobReason->name ?? 'N/A',
                        'job_status_name' => $visit->jobStatus->name ?? 'N/A',
                        'user_name' => $visit->team->first()->user->name ?? 'N/A',
                        'notes' => $visit->notes,
                        'created_at' => $visit->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $visits
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching block visits: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload images for a block visit
     */
    public function uploadImages(Request $request, BlockVisit $blockVisit)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'required|file|max:5120|mimes:jpeg,jpg,png,gif,webp',
        ], [
            'images.required' => 'Please select at least one image to upload.',
            'images.*.max' => 'Each image must not exceed 5MB.',
            'images.*.mimes' => 'Only JPEG, PNG, GIF, and WEBP images are allowed.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $uploadedCount = 0;
            $images = $request->file('images');

            // Validate total size
            $totalSize = 0;
            foreach ($images as $image) {
                $totalSize += $image->getSize();
            }

            $maxTotalSize = 15 * 1024 * 1024; // 15MB
            if ($totalSize > $maxTotalSize) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total file size exceeds 15MB limit. Current total: ' . round($totalSize / 1024 / 1024, 2) . 'MB'
                ], 422);
            }

            // Validate max 10 files
            if (count($images) > 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can upload a maximum of 10 images at once.'
                ], 422);
            }

            foreach ($images as $image) {
                $timestamp = now()->format('YmdHis');
                $randomString = Str::random(8);
                $originalName = $image->getClientOriginalName();
                $fileName = $timestamp . '_' . $randomString . '_' . $originalName;
                
                // Store file in storage/app/public/block-visits/{block_visit_id}
                $path = $image->storeAs('block-visits/' . $blockVisit->id, $fileName, 'public');
                
                // Save to database
                $blockVisit->images()->create([
                    'image_path' => 'block-visits/' . $blockVisit->id,
                    'image_name' => $fileName,
                    's3_status' => false,
                ]);

                $uploadedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => $uploadedCount . ' image(s) uploaded successfully!',
                'uploaded_count' => $uploadedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Block Visit Image Upload Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an image from a block visit
     */
    public function deleteImage(Request $request, BlockVisit $blockVisit)
    {
        $validator = Validator::make($request->all(), [
            'image_id' => 'required|exists:block_visit_images,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $blockVisit->images()->find($request->image_id);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found or does not belong to this visit.'
                ], 404);
            }

            // Delete file from storage
            $filePath = $image->image_path . '/' . $image->image_name;
            if (\Storage::disk('public')->exists($filePath)) {
                \Storage::disk('public')->delete($filePath);
            }

            // Delete from database
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Block Visit Image Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
            ], 500);
        }
    }
}


