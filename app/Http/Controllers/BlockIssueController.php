<?php

namespace App\Http\Controllers;

use App\Models\BlockIssue;
use App\Models\BlockIssueImage;
use App\Models\Block;
use App\Models\IssueStatus;
use App\Models\Priority;
use App\Models\User;
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
        $query = BlockIssue::with(['block', 'reportedBy', 'assignedTo', 'creator']);

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
        if($request->exists('type')&&$request->type=='api'){
            return response()->json([
                'success' => true,
                'data' => $blockIssues
            ]);
        }

        return view('block-issues.index', compact('blockIssues', 'blocks', 'users'));
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
            'fault_details' => 'nullable|string',
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
                'fault_details' => $request->fault_details,
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

        return view('block-issues.edit', compact('blockIssue', 'blocks', 'users', 'priorities', 'issue_status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlockIssue $blockIssue)
    {
        $validator = Validator::make($request->all(), [
            'block_id' => 'required|exists:blocks,id',
            'ref_no' => 'required|string|max:100|unique:block_issues,ref_no,' . $blockIssue->id,
            'issue' => 'required|string|max:255',
            'issue_details' => 'required|string',
            'priority_id' => 'required|integer|min:1|max:5',
            'issue_status_id' => 'required|integer|min:1|max:5',
            'assigned_to' => 'nullable|exists:users,id',
            'reported_by' => 'nullable|exists:users,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('images');
        $data['updated_by'] = Auth::id();

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
}
