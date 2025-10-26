<?php

namespace App\Http\Controllers;

use App\Models\BlockInspection;
use App\Models\BlockInspectionAsset;
use App\Models\BlockInspectionAssetImage;
use App\Models\BlockInspectionValue;
use App\Models\BlockGeneralAsset;
use App\Models\Block;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BlockInspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlockInspection::with([
            'block' => function($query) {
                $query->withTrashed();
            }, 
            'creator' => function($query) {
                $query->withTrashed();
            }, 
            'inspectionTeams.user' => function($query) {
                $query->withTrashed();
            }
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('block', function ($blockQuery) use ($search) {
                      $blockQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('job_status_id', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date_time', '<=', $request->date_to);
        }

        $inspections = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('block-inspections.index', compact('inspections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $blocks = Block::orderBy('name')->get();
        $users = User::whereHas('userType', function($query) {
            $query->where('name', 'Property manager');
        })->with('userType')->orderBy('name')->get();
        
        return view('block-inspections.create', compact('blocks', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'scheduled_date_time' => 'required|date|after:now',
            'notes' => 'nullable|string|max:255',
            'team_members' => 'required|array|min:1',
            'team_members.*' => 'exists:users,id',
            'lead_inspector' => 'required|exists:users,id',
        ]);

        $inspection = BlockInspection::create([
            'block_id' => $request->block_id,
            'ref_no' => BlockInspection::generateRefNo(),
            'scheduled_date_time' => $request->scheduled_date_time,
            'notes' => $request->notes,
            'job_status_id' => 1, // Scheduled
            'created_by' => Auth::id(),
        ]);

        // Create team members
        $teamMembers = array_unique(array_merge($request->team_members, [$request->lead_inspector]));
        
        foreach ($teamMembers as $userId) {
            $inspection->inspectionTeams()->create([
                'user_id' => $userId,
                'role' => $userId == $request->lead_inspector ? 'Lead Inspector' : 'Inspector',
                'is_lead' => $userId == $request->lead_inspector,
            ]);
        }

        return redirect()->route('block-inspections.index')
            ->with('success', 'Inspection scheduled successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, BlockInspection $blockInspection)
    {
        $blockInspection->load([
            'block' => function($query) {
                $query->withTrashed();
            }, 
            'creator' => function($query) {
                $query->withTrashed();
            }, 
            'inspectionTeams.user' => function($query) {
                $query->withTrashed();
            }, 
            'inspectionAssets.buildingAsset', 
            'inspectionAssets.inspectionValue'
        ]);
        
        // Return JSON when requested (AJAX/Accept headers)
        if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
            $data = [
                'id' => $blockInspection->id,
                'ref_no' => $blockInspection->ref_no,
                'scheduled_date_time' => $blockInspection->scheduled_date_time,
                'start_date_time' => $blockInspection->start_date_time,
                'end_date_time' => $blockInspection->end_date_time,
                'notes' => $blockInspection->notes,
                'job_status_id' => $blockInspection->job_status_id,
                'created_by' => $blockInspection->created_by,
                'created_at' => $blockInspection->created_at,
                'updated_at' => $blockInspection->updated_at,
                // Relations in camelCase to match frontend expectations
                'creator' => $blockInspection->creator,
                'block' => $blockInspection->block,
                'inspectionTeams' => $blockInspection->inspectionTeams->map(function($member) {
                    return [
                        'id' => $member->id,
                        'user_id' => $member->user_id,
                        'is_lead' => (bool) $member->is_lead,
                        'role' => $member->role,
                        'user' => $member->user ? [
                            'id' => $member->user->id,
                            'name' => $member->user->name,
                            'email' => $member->user->email
                        ] : null,
                    ];
                })->values(),
                // Also provide with snake_case for backward compatibility
                'inspection_teams' => $blockInspection->inspectionTeams->map(function($member) {
                    return [
                        'id' => $member->id,
                        'user_id' => $member->user_id,
                        'is_lead' => (bool) $member->is_lead,
                        'role' => $member->role,
                        'user' => $member->user ? [
                            'id' => $member->user->id,
                            'name' => $member->user->name,
                            'email' => $member->user->email
                        ] : null,
                    ];
                })->values(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }
        
        return view('block-inspections.show', compact('blockInspection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlockInspection $blockInspection)
    {
        $blocks = Block::orderBy('name')->get();
        $users = User::whereHas('userType', function($query) {
            $query->where('name', 'Property manager');
        })->with('userType')->orderBy('name')->get();
        $blockInspection->load([
            'inspectionTeams.user' => function($query) {
                $query->withTrashed();
            }, 
            'block' => function($query) {
                $query->withTrashed();
            }
        ]);
        
        // Load general assets for the General Assets tab
        $generalAssets = \App\Models\BlockGeneralAsset::orderBy('id')->get();
        
        // Load existing inspection assets data for general assets
        $existingInspectionAssets = BlockInspectionAsset::where('block_inspection_id', $blockInspection->id)
            ->whereNotNull('block_general_asset_id')
            ->with(['generalAsset', 'inspectionValue', 'images'])
            ->get()
            ->keyBy('block_general_asset_id'); // Key by general asset ID for easy lookup
        
        return view('block-inspections.edit', compact('blockInspection', 'blocks', 'users', 'generalAssets', 'existingInspectionAssets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlockInspection $blockInspection)
    {
        // Log incoming request for debugging
        \Log::info('BlockInspection Update Request', [
            'inspection_id' => $blockInspection->id,
            'request_data' => $request->all(),
            'method' => $request->method(),
        ]);

        // Build dynamic validation rules for assets
        $rules = [
            'block_id' => 'required|exists:blocks,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable',
            'end_date' => 'nullable|date',
            'end_time' => 'nullable',
            'lead_inspector' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
            'job_status_id' => 'required|integer|in:1,2,3,4,5',
        ];

        // Add validation for general assets fields (dynamic based on asset IDs)
        $generalAssets = \App\Models\BlockGeneralAsset::all();
        foreach ($generalAssets as $asset) {
            $assetId = $asset->id;
            $rules["asset_status_{$assetId}"] = 'nullable|in:working,not_working,na';
            $rules["notes_{$assetId}"] = 'nullable|string|max:500';
            $rules["photos_{$assetId}"] = 'nullable|array';
            $rules["photos_{$assetId}.*"] = 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120'; // Max 5MB per image
        }

        try {
            $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('BlockInspection Validation Failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            throw $e;
        }

        // Validate that the user is a Property Manager
        $user = User::with('userType')->find($request->lead_inspector);
        if (!$user || $user->userType->name !== 'Property manager') {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Property Manager users can be assigned as Lead Inspector.'
                ], 422);
            }
            return redirect()->back()->withErrors(['lead_inspector' => 'Only Property Manager users can be assigned as Lead Inspector.']);
        }

        // Combine date and time fields into datetime
        $scheduledDateTime = $request->scheduled_date . ' ' . $request->scheduled_time;
        
        $startDateTime = null;
        if ($request->start_date && $request->start_time) {
            $startDateTime = $request->start_date . ' ' . $request->start_time;
        }
        
        $endDateTime = null;
        if ($request->end_date && $request->end_time) {
            $endDateTime = $request->end_date . ' ' . $request->end_time;
        }

        $updateData = [
            'block_id' => $request->block_id,
            'scheduled_date_time' => $scheduledDateTime,
            'start_date_time' => $startDateTime,
            'end_date_time' => $endDateTime,
            'notes' => $request->notes,
            'job_status_id' => $request->job_status_id,
            'updated_by' => Auth::id(),
        ];

        $blockInspection->update($updateData);

        // Update team member (single user for modal)
        $blockInspection->inspectionTeams()->delete();
        $blockInspection->inspectionTeams()->create([
            'user_id' => $request->lead_inspector,
            'role' => 'Lead Inspector',
            'is_lead' => true,
        ]);

        // Process General Assets data
        $this->processGeneralAssets($request, $blockInspection);

        // Load the updated inspection with relationships
        $blockInspection->load([
            'block' => function($query) {
                $query->withTrashed();
            }, 
            'creator' => function($query) {
                $query->withTrashed();
            }, 
            'inspectionTeams.user' => function($query) {
                $query->withTrashed();
            }
        ]);

        \Log::info('BlockInspection Updated Successfully', [
            'inspection_id' => $blockInspection->id,
            'updated_data' => $updateData,
        ]);

        // Check if request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Inspection updated successfully!',
                'inspection' => $blockInspection,
                'status_text' => $blockInspection->status_text,
                'status_color' => $blockInspection->status_color
            ]);
        }

        return redirect()->route('block-inspections.index')
            ->with('success', 'Inspection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlockInspection $blockInspection)
    {
        $blockInspection->update(['deleted_by' => Auth::id()]);
        $blockInspection->delete();

        // Check if request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Inspection deleted successfully!'
            ]);
        }

        return redirect()->route('block-inspections.index')
            ->with('success', 'Inspection deleted successfully.');
    }

    /**
     * Start the inspection.
     */
    public function start(BlockInspection $blockInspection)
    {
        $blockInspection->update([
            'start_date_time' => now(),
            'job_status_id' => 2, // In Progress
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('block-inspections.show', $blockInspection)
            ->with('success', 'Inspection started successfully.');
    }

    /**
     * Complete the inspection.
     */
    public function complete(BlockInspection $blockInspection)
    {
        $blockInspection->update([
            'end_date_time' => now(),
            'job_status_id' => 3, // Completed
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('block-inspections.show', $blockInspection)
            ->with('success', 'Inspection completed successfully.');
    }

    /**
     * Store inspection from modal form.
     */
    public function storeFromModal(Request $request)
    {
        $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'user_id' => 'required|exists:users,id',
            'scheduled_date_time' => 'required|date|after:now',
            'notes' => 'required|string|max:500',
            'job_status_id' => 'nullable|integer|exists:issue_statuses,value',
        ]);

        // Validate that the user is a Property Manager
        $user = User::with('userType')->find($request->user_id);
        if (!$user || $user->userType->name !== 'Property manager') {
            return response()->json([
                'success' => false,
                'message' => 'Only Property Manager users can be assigned as Lead Inspector.'
            ], 422);
        }


        try {
            $inspection = BlockInspection::create([
                'block_id' => $request->block_id,
                'ref_no' => BlockInspection::generateRefNo(),
                'scheduled_date_time' => $request->scheduled_date_time,
                'notes' => $request->notes,
                'job_status_id' => $request->job_status_id ?? 1, // Use provided status or default to Created
                'created_by' => Auth::id(),
            ]);

            // Create team member (single user for modal)
            $inspection->inspectionTeams()->create([
                'user_id' => $request->user_id,
                'role' => 'Inspector',
                'is_lead' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inspection scheduled successfully!',
                'inspection' => $inspection
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule inspection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inspections for a specific block.
     */
    public function getBlockInspections(Block $block)
    {
        $inspections = BlockInspection::where('block_id', $block->id)
            ->with([
                'creator' => function($query) {
                    $query->withTrashed();
                }, 
                'inspectionTeams.user' => function($query) {
                    $query->withTrashed();
                }, 
                'block' => function($query) {
                    $query->withTrashed();
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($inspection) {
                return [
                    'id' => $inspection->id,
                    'ref_no' => $inspection->ref_no,
                    'scheduled_date_time' => $inspection->scheduled_date_time,
                    'start_date_time' => $inspection->start_date_time,
                    'end_date_time' => $inspection->end_date_time,
                    'notes' => $inspection->notes,
                    'job_status_id' => $inspection->job_status_id,
                    'status_text' => $inspection->status_text,
                    'status_color' => $inspection->status_color,
                    'created_at' => $inspection->created_at,
                    'updated_at' => $inspection->updated_at,
                    'creator' => $inspection->creator,
                    'inspection_teams' => $inspection->inspectionTeams->map(function($member) {
                        return [
                            'id' => $member->id,
                            'user_id' => $member->user_id,
                            'is_lead' => (bool) $member->is_lead,
                            'role' => $member->role,
                            'user' => $member->user,
                        ];
                    })->values(),
                    'inspectionTeams' => $inspection->inspectionTeams->map(function($member) {
                        return [
                            'id' => $member->id,
                            'user_id' => $member->user_id,
                            'is_lead' => (bool) $member->is_lead,
                            'role' => $member->role,
                            'user' => $member->user,
                        ];
                    })->values(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $inspections
        ]);
    }

    /**
     * Process general assets data from the inspection edit form.
     */
    private function processGeneralAssets(Request $request, BlockInspection $blockInspection)
    {
        \Log::info('=== processGeneralAssets START ===', [
            'inspection_id' => $blockInspection->id,
            'block_id' => $blockInspection->block_id,
        ]);

        // Get all general assets
        $generalAssets = BlockGeneralAsset::all();
        \Log::info('General Assets Count', ['count' => $generalAssets->count()]);
        
        // Get the first building of the block for asset association (if available)
        // Note: For general assets, building is optional
        $firstBuilding = $blockInspection->block->buildings()->first();
        
        \Log::info('Building Check', [
            'building_found' => $firstBuilding ? true : false,
            'building_id' => $firstBuilding ? $firstBuilding->id : null,
        ]);

        // Map status values to inspection value IDs
        $statusToValueMap = $this->getStatusToValueMap();
        \Log::info('Status to Value Map', $statusToValueMap);

        $processedCount = 0;
        foreach ($generalAssets as $asset) {
            $assetId = $asset->id;
            
            // Check if this asset has any data submitted
            $statusKey = "asset_status_{$assetId}";
            $notesKey = "notes_{$assetId}";
            $photosKey = "photos_{$assetId}";
            
            \Log::info("Checking Asset {$asset->name}", [
                'asset_id' => $assetId,
                'status_key' => $statusKey,
                'has_status' => $request->has($statusKey),
                'status_value' => $request->input($statusKey),
                'has_notes' => $request->has($notesKey),
                'notes_value' => $request->input($notesKey),
                'has_photos' => $request->hasFile($photosKey),
            ]);
            
            // Skip if no status selected
            if (!$request->has($statusKey)) {
                \Log::info("Skipping asset {$assetId} - no status selected");
                continue;
            }

            $status = $request->input($statusKey);
            $notes = $request->input($notesKey);
            
            // Ensure notes is either a string or null (not empty string for database)
            if (empty($notes)) {
                $notes = null;
            }
            
            // Get the appropriate inspection value ID based on status
            $inspectionValueId = $statusToValueMap[$status] ?? $statusToValueMap['na'];

            \Log::info("Creating/Updating Inspection Asset", [
                'asset_id' => $assetId,
                'status' => $status,
                'inspection_value_id' => $inspectionValueId,
                'notes' => $notes,
            ]);

            // Create or update the inspection asset record
            // For general assets, we use block_general_asset_id instead of building_asset_id
            $inspectionAsset = BlockInspectionAsset::updateOrCreate(
                [
                    'block_inspection_id' => $blockInspection->id,
                    'block_general_asset_id' => $assetId,
                ],
                [
                    'block_building_id' => $firstBuilding ? $firstBuilding->id : null,
                    'building_asset_id' => null, // General assets don't use building_asset_id
                    'block_inspection_value_id' => $inspectionValueId,
                    'comments' => $notes,
                ]
            );

            \Log::info("Inspection Asset saved", [
                'id' => $inspectionAsset->id,
                'was_recently_created' => $inspectionAsset->wasRecentlyCreated,
            ]);

            $processedCount++;

            // Process uploaded photos
            if ($request->hasFile($photosKey)) {
                \Log::info("Processing photos for asset {$assetId}");
                $this->processAssetImages($request, $inspectionAsset, $blockInspection, $firstBuilding, $assetId, $photosKey);
            }
        }

        \Log::info('=== processGeneralAssets END ===', [
            'total_assets' => $generalAssets->count(),
            'processed_count' => $processedCount,
        ]);
    }

    /**
     * Process and store asset images.
     */
    private function processAssetImages(Request $request, BlockInspectionAsset $inspectionAsset, BlockInspection $blockInspection, $building, $assetId, $photosKey)
    {
        $photos = $request->file($photosKey);
        
        foreach ($photos as $photo) {
            // Generate unique filename
            $timestamp = now()->timestamp;
            $randomString = substr(md5(uniqid()), 0, 8);
            $extension = $photo->getClientOriginalExtension();
            $filename = "{$timestamp}_{$randomString}_asset_{$assetId}.{$extension}";
            
            // Define storage path
            $storagePath = "inspection-assets/{$blockInspection->id}/{$inspectionAsset->id}";
            
            // Store the file
            $path = $photo->storeAs($storagePath, $filename, 'public');
            
            // Create database record
            BlockInspectionAssetImage::create([
                'block_inspection_asset_id' => $inspectionAsset->id,
                'block_inspection_id' => $blockInspection->id,
                'block_building_id' => $building ? $building->id : null,
                'building_asset_id' => $assetId,
                'image_path' => $storagePath,
                'image_name' => $filename,
                's3_status' => 0, // 0 = stored locally, not yet uploaded to S3
            ]);
        }
    }

    /**
     * Map status values to inspection value IDs.
     * Based on block_inspection_values table.
     */
    private function getStatusToValueMap()
    {
        // Map form status values to database inspection values
        // working → Good (id: 2) or Operational (id: 6)
        // not_working → Poor (id: 4) or Non-Operational (id: 8)
        // na → Fair (id: 3) as neutral/not applicable status
        
        $workingValue = BlockInspectionValue::whereIn('name', ['Good', 'Operational'])->first();
        $notWorkingValue = BlockInspectionValue::whereIn('name', ['Poor', 'Non-Operational'])->first();
        $naValue = BlockInspectionValue::whereIn('name', ['Fair', 'Pending'])->first();

        // Fallback to specific IDs if queries fail
        return [
            'working' => $workingValue->id ?? 2, // Good
            'not_working' => $notWorkingValue->id ?? 4, // Poor
            'na' => $naValue->id ?? 3, // Fair (neutral status)
        ];
    }

    /**
     * Map inspection value IDs back to form status values.
     * This is the reverse of getStatusToValueMap.
     */
    public static function getValueToStatusMap($inspectionValueId)
    {
        // Get the inspection value
        $inspectionValue = BlockInspectionValue::find($inspectionValueId);
        
        if (!$inspectionValue) {
            return 'na'; // Default to N/A if value not found
        }
        
        // Map based on the value name
        $name = strtolower($inspectionValue->value);
        
        if (in_array($name, ['good', 'operational'])) {
            return 'working';
        } elseif (in_array($name, ['poor', 'non-operational'])) {
            return 'not_working';
        } else {
            return 'na'; // Fair, Pending, or any other status
        }
    }

    /**
     * Delete an inspection asset image.
     */
    public function deleteImage(BlockInspectionAssetImage $image)
    {
        try {
            // Delete the physical file from storage
            if ($image->image_path && $image->image_name) {
                $filePath = $image->image_path . '/' . $image->image_name;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            // Delete the database record
            $image->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image deleted successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Image deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to delete inspection asset image', [
                'image_id' => $image->id,
                'error' => $e->getMessage()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete image: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Failed to delete image.']);
        }
    }
}
