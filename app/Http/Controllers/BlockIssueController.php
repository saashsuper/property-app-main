<?php

namespace App\Http\Controllers;

use App\Models\BlockIssue;
use App\Models\BlockIssueImage;
use App\Models\Block;
use App\Models\IssueStatus;
use App\Models\Priority;
use App\Models\User;
use App\Models\JobReason;
use App\Models\JobStatus;
use App\Models\IssueType;
use App\Models\BlockIssueAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlockIssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlockIssue::with(['block', 'reportedBy', 'assignedTo', 'creator', 'priority', 'issueStatus']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhere('issue', 'like', "%{$search}%")
                  ->orWhere('issue_details', 'like', "%{$search}%")
                  ->orWhereHas('block', function ($blockQuery) use ($search) {
                      $blockQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by block
        if ($request->filled('block_id')) {
            $query->where('block_id', $request->block_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('issue_status_id', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority_id', $request->priority);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by block unit
        if ($request->filled('block_unit_id')) {
            $query->where('block_unit_id', $request->block_unit_id);
        }

        // Filter by issue type
        if ($request->filled('issue_type')) {
            $query->where('issue_type', $request->issue_type);
        }

        $blockIssues = $query->orderBy('created_at', 'desc')->paginate(10);
        $blocks = Block::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        // Add data needed for edit modal
        $contactMethods = \App\Models\ContactMethod::orderBy('name')->get();
        $issueTypes = \App\Models\IssueType::where('is_active', true)->orderBy('name')->get();
        
        if($request->exists('type')&&$request->type=='api'){
            return response()->json([
                'success' => true,
                'data' => $blockIssues
            ]);
        }

        return view('block-issues.index', compact('blockIssues', 'blocks', 'users', 'contactMethods', 'issueTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $blocks = Block::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('block-issues.create', compact('blocks', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'block_id' => 'required|exists:blocks,id',
            'assigned_to' => 'required|exists:users,id',
            'issue' => 'required|string|max:255',
            'issue_type' => 'required|string|max:100',
            'priority_id' => 'required|integer|min:1|max:5',
            'contact_details' => 'required|string|max:500',
            'contact_method_id' => 'required|exists:contact_methods,id',
            'issue_details' => 'nullable|string',
            'default_contact_details' => 'nullable|string',
            'block_unit_id' => 'required|exists:block_units,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'block_id' => $request->block_id,
                'assigned_to' => $request->assigned_to_hidden ?: $request->assigned_to,
                'issue' => $request->issue,
                'issue_type' => $request->issue_type,
                'priority_id' => $request->priority_id,
                'issue_status_id' => 1, // Default to 'Open' status
                'contact_details' => $request->contact_details,
                'contact_method_id' => $request->contact_method_id_hidden ?: $request->contact_method_id,
                'issue_details' => $request->issue_details,
                'default_contact_details' => $request->default_contact_details,
                'block_unit_id' => $request->block_unit_id_hidden ?: $request->block_unit_id,
                'reported_by' => Auth::id(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'issued_by' => Auth::id(),
            ];

            $blockIssue = BlockIssue::create($data);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $imagePath = 'block-issues/images';
                    
                    $image->storeAs('public/' . $imagePath, $imageName);
                    
                    BlockIssueImage::create([
                        'block_issue_id' => $blockIssue->id,
                        'image_path' => $imagePath,
                        'image_name' => $imageName,
                        's3_status' => false,
                    ]);
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Issue created successfully!',
                    'data' => $blockIssue
                ]);
            }

            return redirect()->route('block-issues.index')
                ->with('success', 'Block issue created successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create issue: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create issue: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BlockIssue $blockIssue)
    {
        $blockIssue->load(['block', 'reportedBy', 'assignedTo', 'creator', 'updater', 'priority', 'issueStatus']);
        
        // Return JSON data for AJAX requests (edit modal)
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $blockIssue
            ]);
        }
        
        return view('block-issues.show', compact('blockIssue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlockIssue $blockIssue)
    {
        $blocks = Block::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $priorities = Priority::orderBy('id')->get();
        $issue_status = IssueStatus::orderBy('id')->get();
        $jobReasons = JobReason::orderBy('name')->get();
        $jobStatuses = JobStatus::orderBy('name')->get();
        $issueTypes = IssueType::where('is_active', true)->orderBy('name')->get();
        
        // Load work orders for this issue
        $workOrders = $blockIssue->workOrders()
            ->with(['issuedBy', 'creator', 'priority'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Load site visits for this issue (both specific to this issue and general block visits)
        $siteVisits = $blockIssue->block->blockVisits()
            ->with(['jobReason', 'jobStatus', 'team.user', 'createdByUser', 'blockIssue'])
            ->orderBy('scheduled_date_time', 'desc')
            ->get();

        // Also load site visits specifically created for this issue
        $relatedSiteVisits = $blockIssue->relatedSiteVisits()
            ->with(['jobReason', 'jobStatus', 'team.user', 'createdByUser'])
            ->orderBy('scheduled_date_time', 'desc')
            ->get();

        // Load actions for this issue
        $actions = $blockIssue->actions()
            ->with(['performedBy', 'createdBy', 'updatedBy'])
            ->orderBy('action_date', 'desc')
            ->get();

        return view('block-issues.edit', compact('blockIssue', 'blocks', 'users', 'priorities', 'issue_status', 'workOrders', 'siteVisits', 'relatedSiteVisits', 'jobReasons', 'jobStatuses', 'issueTypes', 'actions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlockIssue $blockIssue)
    {
        $validator = Validator::make($request->all(), [
            'contact_method_id' => 'required|exists:contact_methods,id',
            'block_unit_id' => 'required|exists:block_units,id',
            'assigned_to' => 'required|exists:users,id',
            'issue_type' => 'required|string|max:255',
            'priority_id' => 'required|integer|min:1|max:5',
            'issue' => 'required|string|max:255',
            'contact_details' => 'required|string',
            'issue_details' => 'nullable|string',
            'default_contact_details' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('images');
        $data['updated_by'] = Auth::id();
        
        // No field mapping needed - using consistent field names

        $blockIssue->update($data);

        // Handle additional image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = 'block-issues/images';
                
                $image->storeAs('public/' . $imagePath, $imageName);
                
                // Create BlockIssueImage record
                BlockIssueImage::create([
                    'block_issue_id' => $blockIssue->id,
                    'image_name' => $imageName,
                    'image_path' => $imagePath,
                    's3_status' => false,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Block issue updated successfully!'
            ]);
        }

        return redirect()->route('block-issues.index')
            ->with('success', 'Block issue updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlockIssue $blockIssue)
    {
        try {
            $blockIssue->delete();
            return redirect()->route('block-issues.index')
                ->with('success', 'Block issue deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('block-issues.index')
                ->with('error', 'Failed to delete block issue. It may be referenced by other records.');
        }
    }

    /**
     * Get block issues for API
     */
    public function getBlockIssues(Request $request)
    {
        $blockIssues = BlockIssue::with(['block'])
            ->orderBy('created_at', 'desc');
            
        if($request->has('block_id')){
            $blockIssues->where('block_id', $request->block_id);
        }
        if($request->has('status')){
            $blockIssues->where('issue_status_id', $request->status);
        }
        if($request->has('priority')){
            $blockIssues->where('priority_id', $request->priority);
        }
        if($request->has('contact_method_id')){
            $blockIssues->where('contact_method_id', $request->contact_method_id);
        }
        if($request->has('block_unit_id')){
            $blockIssues->where('block_unit_id', $request->block_unit_id);
        }
        if($request->has('issue_type')){
            $blockIssues->where('issue_type', $request->issue_type);
        }
        if($request->has('ref_no')){
            $blockIssues->where('ref_no', 'like', "%{$request->ref_no}%");
        }
        
        // Only get open issues (status_id = 1)
        $blockIssues->where('issue_status_id', 1);
        
        return response()->json([
            'success' => true,
            'data' => $blockIssues->get()
        ]);
    }

    /**
     * Get specific block issue for API
     */
    public function getBlockIssue(BlockIssue $blockIssue)
    {
        $blockIssue->load(['block', 'reportedBy', 'assignedTo', 'creator', 'updater']);

        return response()->json([
            'success' => true,
            'data' => $blockIssue
        ]);
    }
    
    /**
     * Upload photos for a block issue
     */
    public function uploadPhotos(Request $request, BlockIssue $blockIssue)
    {
        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $uploadedImages = [];
            
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $imagePath = 'block-issues/images';
                    
                    $image->storeAs('public/' . $imagePath, $imageName);
                    
                    $blockIssueImage = BlockIssueImage::create([
                        'block_issue_id' => $blockIssue->id,
                        'image_name' => $imageName,
                        'image_path' => $imagePath,
                        's3_status' => false,
                    ]);
                    
                    $uploadedImages[] = $blockIssueImage;
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Photos uploaded successfully!',
                    'data' => $uploadedImages
                ]);
            }

            return redirect()->back()
                ->with('success', 'Photos uploaded successfully!');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error uploading photos: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error uploading photos: ' . $e->getMessage());
        }
    }
    
    /**
     * Get photos for a block issue
     */
    public function getPhotos(BlockIssue $blockIssue)
    {
        $photos = $blockIssue->images()->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $photos
        ]);
    }
    
    /**
     * Get active issues for a specific unit
     */
    public function getActiveIssuesForUnit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required|exists:block_units,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid unit ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $unitId = $request->unit_id;
            
            // Get active issues for the unit (status 1 = Open, 2 = In Progress)
            $issues = BlockIssue::where('block_unit_id', $unitId)
                ->whereIn('issue_status_id', [1, 2]) // Open and In Progress
                ->with(['priority', 'issueStatus'])
                ->orderBy('created_at', 'desc') // Most recent first
                ->get();

            return response()->json([
                'success' => true,
                'data' => $issues
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching issues: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a photo
     */
    public function deletePhoto(BlockIssueImage $photo)
    {
        try {
            // Delete the file from storage
            $filePath = storage_path('app/public/' . $photo->image_path . '/' . $photo->image_name);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Delete the database record
            $photo->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contact methods for autocomplete
     */
    public function getContactMethodsAutocomplete(Request $request)
    {
        $query = $request->get('query', '');
        $limit = config('autocomplete.default_limit', 10);
        
        $contactMethods = \App\Models\ContactMethod::where('name', 'like', "%{$query}%")
            ->limit($limit)
            ->get(['id', 'name'])
            ->map(function($method) {
                return [
                    'id' => $method->id,
                    'text' => $method->name
                ];
            });

        return response()->json($contactMethods);
    }

    /**
     * Get property managers for autocomplete
     */
    public function getPropertyManagersAutocomplete(Request $request)
    {
        $query = $request->get('query', '');
        $limit = config('autocomplete.default_limit', 10);
        
        $propertyManagers = \App\Models\User::with('userType')
            ->whereHas('userType', function($q) {
                $q->where('name', 'Property Manager');
            })
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'email'])
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name . ' (' . $user->email . ')'
                ];
            });

        return response()->json($propertyManagers);
    }

    /**
     * Get block unit contact details for API
     */
    public function getBlockUnitContactDetails(Request $request)
    {
        try {
            $blockUnitId = $request->get('block_unit_id');
            
            if (!$blockUnitId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Block unit ID is required.'
                ], 400);
            }

            $blockUnit = \App\Models\BlockUnit::find($blockUnitId);
            
            if (!$blockUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Block unit not found.'
                ], 404);
            }

            // Build contact details string
            $contactDetails = [];
            
            if (!empty($blockUnit->mobile_no)) {
                $contactDetails[] = 'Mobile: ' . $blockUnit->mobile_no;
            }
            
            if (!empty($blockUnit->phone_number)) {
                $contactDetails[] = 'Phone: ' . $blockUnit->phone_number;
            }
            
            if (!empty($blockUnit->email)) {
                $contactDetails[] = 'Email: ' . $blockUnit->email;
            }
            
            if (!empty($blockUnit->owners_name)) {
                $contactDetails[] = 'Owner: ' . $blockUnit->owners_name;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'mobile_no' => $blockUnit->mobile_no,
                    'phone_number' => $blockUnit->phone_number,
                    'email' => $blockUnit->email,
                    'owners_name' => $blockUnit->owners_name,
                    'contact_details' => implode("\n", $contactDetails)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch block unit details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an image from a block issue.
     */
    public function deleteImage(BlockIssueImage $image)
    {
        try {
            // Check if the user has permission to delete this image
            $blockIssue = $image->blockIssue;
            if (!$blockIssue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found or issue not found.'
                ], 404);
            }

            // Delete the physical file
            $filePath = storage_path('app/public/' . $image->image_path . '/' . $image->image_name);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete the database record
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new action for a block issue.
     */
    public function storeAction(Request $request, BlockIssue $blockIssue)
    {
        $validator = Validator::make($request->all(), [
            'action_type' => 'required|string|max:50',
            'description' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'performed_by' => 'required|exists:users,id',
            'action_date' => 'required|date',
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
            'cost' => 'nullable|numeric|min:0|max:999999.99',
            'priority' => 'nullable|string|in:low,normal,high,urgent,critical',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->only([
                'action_type',
                'description', 
                'notes',
                'performed_by',
                'action_date',
                'status',
                'cost',
                'priority'
            ]);
            
            $data['block_issue_id'] = $blockIssue->id;
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $action = BlockIssueAction::create($data);
            $action->load(['performedBy', 'createdBy']);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Action created successfully!',
                    'data' => $action
                ]);
            }

            return redirect()->back()
                ->with('success', 'Action created successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create action: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create action: ' . $e->getMessage())
                ->withInput();
        }
    }
}
