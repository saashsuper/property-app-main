<?php

namespace App\Http\Controllers;

use App\Models\BlockWorkOrder;
use App\Models\BlockWorkOrderImage;
use App\Models\BlockWorkOrderLog;
use App\Models\Block;
use App\Models\BlockIssue;
use App\Models\BlockUnit;
use App\Models\BlockBuilding;
use App\Models\IssueLog;
use App\Services\WorkDocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlockWorkOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlockWorkOrder::with(['block', 'blockIssue', 'issuedBy', 'creator', 'blockUnit'])->active();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhere('issue', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%")
                  ->orWhereHas('block', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('issuedBy', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority_id', $request->priority);
        }

        // Filter by block
        if ($request->filled('block_id')) {
            $query->where('block_id', $request->block_id);
        }

        $workOrders = $query->orderBy('created_at', 'desc')->get();
        $blocks = Block::orderBy('name')->get();

        return view('block-work-orders.index', compact('workOrders', 'blocks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $blocks = Block::orderBy('name')->get();
        $blockIssues = BlockIssue::with('block')->orderBy('created_at', 'desc')->get();
        $blockUnits = BlockUnit::with('block')->orderBy('unit_name')->get();
        $blockBuildings = BlockBuilding::with('block')->orderBy('name')->get();
        
        // Load contract companies and property managers for work order type selection
        $contractCompanies = \App\Models\ContractCompany::orderBy('company_name')->get();
        $propertyManagers = \App\Models\User::whereHas('userType', function($query) {
            $query->where('name', 'Property manager');
        })->orderBy('name')->get();

        return view('block-work-orders.create', compact('blocks', 'blockIssues', 'blockUnits', 'blockBuildings', 'contractCompanies', 'propertyManagers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate only user-input fields
        $validator = Validator::make($request->all(), [
            'block_issue_id' => 'required|exists:block_issues,id',
            'priority_id' => 'required|integer|min:1|max:5',
            'contractor_id' => ['nullable', \Illuminate\Validation\Rule::exists(\App\Models\Contractor::class, 'id')],
            'contract_company_id' => ['nullable', \Illuminate\Validation\Rule::exists(\App\Models\ContractCompany::class, 'id')],
            'property_manager_id' => 'nullable|exists:users,id',
            'preferred_start_date_time' => 'nullable|date',
            'preferred_end_date_time' => 'nullable|date',
            'deadline_date' => 'nullable|date',
            'comment' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            // Check if this is an AJAX request (for modal submissions from block edit page)
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
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

        // Fetch the issue with related data from database
        $issue = \App\Models\BlockIssue::with(['block', 'blockUnit', 'blockBuilding'])
            ->findOrFail($request->block_issue_id);

        // Populate data from issue (single source of truth)
        $data = [
            'block_id' => $issue->block_id,
            'block_issue_id' => $issue->id,
            'block_unit_id' => $issue->block_unit_id,
            'block_building_id' => $issue->block_building_id,
            'priority_id' => $request->priority_id ?? $issue->priority_id,
            'status' => $request->status ?? 1, // Default to Pending
            'issue' => $request->issue ?? $issue->issue,
            'contact_name' => $request->contact_name ?? $issue->contact_name,
            'contact_mobile' => $request->contact_mobile ?? $issue->contact_mobile,
            'contact_email' => $request->contact_email ?? $issue->contact_email,
            'note_for_access' => $request->note_for_access ?? $issue->note_for_access,
            'preferred_start_date_time' => $request->preferred_start_date_time ?? $issue->preferred_start_date_time,
            'preferred_end_date_time' => $request->preferred_end_date_time ?? $issue->preferred_end_date_time,
            'deadline_date' => $request->deadline_date,
            'comment' => $request->comment,
        ];
        
        // Handle contractor or property manager assignment
        if ($request->property_manager_id) {
            $data['contractor_id'] = $request->property_manager_id;
            // You might want to add a flag to distinguish: $data['is_inhouse'] = true;
        } elseif ($request->contractor_id) {
            $data['contractor_id'] = $request->contractor_id;
            // $data['is_inhouse'] = false;
        } elseif ($request->contract_company_id) {
            // If contract_company_id is provided, try to use it as contractor_id
            // Note: This assumes ContractCompany and Contractor might share IDs or need mapping
            // For now, we'll attempt to use it directly, but this may need adjustment based on your data model
            $data['contractor_id'] = $request->contract_company_id;
        }
        
        // Auto-generate reference number
        $data['ref_no'] = $this->generateWorkOrderRefNo();
        
        // Set system fields
        $data['issued_by'] = Auth::id();
        $data['issued_from'] = 1;
        $data['from_id'] = Auth::id();
        $data['issued_date_time'] = now();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // Handle PDF upload
        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $pdfName = time() . '_' . $pdf->getClientOriginalName();
            $pdfPath = 'work-orders/pdfs';
            
            $pdf->storeAs('public/' . $pdfPath, $pdfName);
            
            $data['pdf_path'] = $pdfPath;
            $data['pdf_name'] = $pdfName;
        }

        $workOrder = BlockWorkOrder::create($data);
        
        // Log work order creation
        BlockWorkOrderLog::createLog(
            $workOrder->id,
            'created',
            'Work order created',
            [
                'user_id' => Auth::id(),
            ]
        );

        // Handle image uploads
        $imagesCount = 0;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = 'work-orders/images';
                
                $image->storeAs('public/' . $imagePath, $imageName);
                
                BlockWorkOrderImage::create([
                    'block_work_order_id' => $workOrder->id,
                    'image_name' => $imageName,
                    'image_path' => $imagePath,
                    's3_status' => 0,
                ]);
                $imagesCount++;
            }
        }
        
        // Log image uploads if any
        if ($imagesCount > 0) {
            BlockWorkOrderLog::createLog(
                $workOrder->id,
                'attachment_added',
                "{$imagesCount} photo(s) uploaded during creation",
                [
                    'field_name' => 'images',
                    'new_value' => $imagesCount,
                    'user_id' => Auth::id(),
                ]
            );
        }

        // Create issue log entry
        if ($workOrder->block_issue_id) {
            $contractor = $workOrder->contractCompany ? $workOrder->contractCompany->name : ($workOrder->contractor ? $workOrder->contractor->name : 'contractor');
            IssueLog::createLog(
                $workOrder->block_issue_id,
                'work_order_created',
                "Work order {$workOrder->ref_no} raised and assigned to {$contractor}",
                [
                    'related_id' => $workOrder->id,
                    'related_type' => 'BlockWorkOrder',
                ]
            );
        }

        // Check if this is an AJAX request (for modal submissions from block edit page)
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Block work order created successfully!',
                'work_order_id' => $workOrder->id
                // No redirect_url - let JavaScript handle the refresh
            ]);
        }

        return redirect()->route('block-work-orders.index')
            ->with('success', 'Block work order created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, BlockWorkOrder $blockWorkOrder)
    {
        $blockWorkOrder->load([
            'block', 
            'block.blockType',
            'blockIssue.priority', 
            'blockIssue.issueStatus', 
            'blockIssue.issueType', 
            'blockIssue.contactMethod',
            'blockIssue.reportedBy',
            'blockIssue.assignedTo',
            'blockIssue.blockUnit',
            'blockIssue.blockBuilding',
            'blockUnit', 
            'blockBuilding', 
            'issuedBy', 
            'creator', 
            'updater',
            'images', 
            'notes.creator',
            'contractCompany', 
            'contractor',
            'contractor.userType',
            'priority',
            'logs.user'
        ]);
        
        // Determine if contractor is a property manager (for backward compatibility)
        $isPropertyManager = false;
        if ($blockWorkOrder->contractor && $blockWorkOrder->contractor->userType) {
            $isPropertyManager = $blockWorkOrder->contractor->userType->name === 'Property manager';
        }
        
        // Return JSON data for AJAX requests (edit modal)
        if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
            $data = $blockWorkOrder->toArray();
            
            // Explicitly add contract_company_id and property_manager_id for edit modal
            // When outsource: contractor_id is the contract_company_id
            // When inhouse: contractor_id is the property_manager_id
            if (!$isPropertyManager && $blockWorkOrder->contractor_id) {
                // It's a contract company (outsource)
                $data['contract_company_id'] = $blockWorkOrder->contractor_id;
                $data['property_manager_id'] = null;
            } else if ($isPropertyManager && $blockWorkOrder->contractor_id) {
                // It's a property manager (inhouse)
                $data['property_manager_id'] = $blockWorkOrder->contractor_id;
                $data['contract_company_id'] = null;
            }
            
            return response()->json([
                'success' => true,
                'data' => array_merge($data, [
                    'is_property_manager' => $isPropertyManager
                ])
            ]);
        }
        
        return view('block-work-orders.show', compact('blockWorkOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlockWorkOrder $blockWorkOrder)
    {
        $blocks = Block::orderBy('name')->get();
        $blockIssues = BlockIssue::with('block')->orderBy('created_at', 'desc')->get();
        $blockUnits = BlockUnit::with('block')->orderBy('unit_name')->get();
        $blockBuildings = BlockBuilding::with('block')->orderBy('name')->get();
        
        // Load contract companies and property managers for work order type selection
        $contractCompanies = \App\Models\ContractCompany::orderBy('company_name')->get();
        $propertyManagers = \App\Models\User::whereHas('userType', function($query) {
            $query->where('name', 'Property manager');
        })->orderBy('name')->get();
        
        $blockWorkOrder->load(['images', 'contractor.userType']);
        
        return view('block-work-orders.edit', compact('blockWorkOrder', 'blocks', 'blockIssues', 'blockUnits', 'blockBuildings', 'contractCompanies', 'propertyManagers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlockWorkOrder $blockWorkOrder)
    {
        $isCompleted = $blockWorkOrder->status == 3;
        $isAdmin = auth()->user()->isAdmin();
        
        // If completed and not admin, prevent editing
        if ($isCompleted && !$isAdmin) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Completed work orders cannot be edited.',
                ], 403);
            }
            return redirect()->back()
                ->with('error', 'Completed work orders cannot be edited.');
        }
        
        // Simplified validation for AJAX requests from issue details page
        $validationRules = [
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
            'comment' => 'nullable|string',
        ];
        
        // For completed work orders, only allow editing photos, notes, and regenerate docket
        if ($isCompleted && $isAdmin) {
            // Only allow comment (notes) and images for completed work orders
            $validationRules['regenerate_docket'] = 'nullable|boolean';
        } else {
            // Full validation for non-completed work orders
            $validationRules = array_merge($validationRules, [
                'block_issue_id' => 'required|exists:block_issues,id',
                'priority_id' => 'required|integer|min:1|max:5',
                'contractor_id' => ['nullable', \Illuminate\Validation\Rule::exists(\App\Models\Contractor::class, 'id')],
                'contract_company_id' => ['nullable', \Illuminate\Validation\Rule::exists(\App\Models\ContractCompany::class, 'id')],
                'property_manager_id' => 'nullable|exists:users,id',
                'preferred_start_date_time' => 'nullable|date',
                'preferred_end_date_time' => 'nullable|date',
                'deadline_date' => 'nullable|date',
                'status' => 'nullable|integer|min:1|max:5',
            ]);
        }
        
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            // Check if this is an AJAX request (for modal submissions from block edit page)
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
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

        // For completed work orders, only update comment and images
        if ($isCompleted && $isAdmin) {
            $data = [
                'updated_by' => Auth::id(),
            ];
            
            // Update comment if provided
            if ($request->filled('comment')) {
                $data['comment'] = $request->comment;
            }
            
            $oldStatus = $blockWorkOrder->status;
            $isCompleting = false;
        } else {
            // Full update for non-completed work orders
            // Fetch the issue with related data from database
            $issue = \App\Models\BlockIssue::with(['block', 'blockUnit', 'blockBuilding'])
                ->findOrFail($request->block_issue_id);

            // Populate data from issue (single source of truth)
            $data = [
                'block_id' => $issue->block_id,
                'block_issue_id' => $issue->id,
                'block_unit_id' => $issue->block_unit_id,
                'block_building_id' => $issue->block_building_id,
                'priority_id' => $request->priority_id,
                'preferred_start_date_time' => $request->preferred_start_date_time,
                'preferred_end_date_time' => $request->preferred_end_date_time,
                'deadline_date' => $request->deadline_date,
                'comment' => $request->comment,
                'updated_by' => Auth::id(),
            ];
            
            // Handle contractor or property manager assignment
            // Priority: property_manager_id > contractor_id > contract_company_id
            if ($request->filled('property_manager_id')) {
                $data['contractor_id'] = $request->property_manager_id;
            } elseif ($request->filled('contractor_id')) {
                $data['contractor_id'] = $request->contractor_id;
            } elseif ($request->filled('contract_company_id')) {
                // If contract_company_id is provided, use it as contractor_id
                $data['contractor_id'] = $request->contract_company_id;
            }
            
            // Handle status update if provided
            $oldStatus = $blockWorkOrder->status;
            if ($request->filled('status')) {
                $data['status'] = $request->status;
            }
            
            // Check if work order is being completed
            $isCompleting = ($oldStatus != 3 && isset($data['status']) && $data['status'] == 3);
        }

        // Handle PDF upload
        if ($request->hasFile('pdf')) {
            // Delete old PDF if exists
            if ($blockWorkOrder->pdf_path && $blockWorkOrder->pdf_name) {
                Storage::delete('public/' . $blockWorkOrder->pdf_path . '/' . $blockWorkOrder->pdf_name);
            }

            $pdf = $request->file('pdf');
            $pdfName = time() . '_' . $pdf->getClientOriginalName();
            $pdfPath = 'work-orders/pdfs';
            
            $pdf->storeAs('public/' . $pdfPath, $pdfName);
            
            $data['pdf_path'] = $pdfPath;
            $data['pdf_name'] = $pdfName;
        }

        // Track changes for logging (capture OLD values BEFORE update)
        $oldStatus = $blockWorkOrder->status;
        $oldPriority = $blockWorkOrder->priority_id;
        $hasStatusChange = isset($data['status']) && $oldStatus != $data['status'];
        $hasPriorityChange = isset($data['priority_id']) && $oldPriority != $data['priority_id'];
        $hasCommentChange = isset($data['comment']) && $blockWorkOrder->comment != $data['comment'];
        
        // Handle image uploads
        $imagesCount = 0;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = 'work-orders/images';
                
                $image->storeAs('public/' . $imagePath, $imageName);
                
                BlockWorkOrderImage::create([
                    'block_work_order_id' => $blockWorkOrder->id,
                    'image_name' => $imageName,
                    'image_path' => $imagePath,
                    's3_status' => 0,
                ]);
                $imagesCount++;
            }
        }

        $blockWorkOrder->update($data);
        
        // Log changes
        $userId = Auth::id();
        
        // Log status change
        if ($hasStatusChange) {
            $statusLabels = [
                1 => 'Pending',
                2 => 'In Progress',
                3 => 'Completed',
                4 => 'Cancelled',
                5 => 'On Hold'
            ];
            $oldStatusText = $statusLabels[$oldStatus] ?? 'Unknown';
            $newStatusText = $statusLabels[$data['status']] ?? 'Unknown';
            
            BlockWorkOrderLog::createLog(
                $blockWorkOrder->id,
                'status_changed',
                "Status changed from {$oldStatusText} to {$newStatusText}",
                [
                    'field_name' => 'status',
                    'old_value' => $oldStatusText,
                    'new_value' => $newStatusText,
                    'user_id' => $userId,
                ]
            );
        }
        
        // Log priority change
        if ($hasPriorityChange) {
            $priorityLabels = [
                1 => 'Low',
                2 => 'Normal',
                3 => 'High',
                4 => 'Urgent',
                5 => 'Critical'
            ];
            $oldPriorityText = $priorityLabels[$oldPriority] ?? 'Unknown';
            $newPriorityText = $priorityLabels[$data['priority_id']] ?? 'Unknown';
            
            BlockWorkOrderLog::createLog(
                $blockWorkOrder->id,
                'priority_changed',
                "Priority changed from {$oldPriorityText} to {$newPriorityText}",
                [
                    'field_name' => 'priority_id',
                    'old_value' => $oldPriorityText,
                    'new_value' => $newPriorityText,
                    'user_id' => $userId,
                ]
            );
        }
        
        // Log comment/notes update
        if ($hasCommentChange && !empty($data['comment'])) {
            BlockWorkOrderLog::createLog(
                $blockWorkOrder->id,
                'comment_added',
                'Notes/comments updated',
                [
                    'field_name' => 'comment',
                    'user_id' => $userId,
                ]
            );
        }
        
        // Log photo uploads
        if ($imagesCount > 0) {
            BlockWorkOrderLog::createLog(
                $blockWorkOrder->id,
                'attachment_added',
                "{$imagesCount} photo(s) uploaded",
                [
                    'field_name' => 'images',
                    'new_value' => $imagesCount,
                    'user_id' => $userId,
                ]
            );
        }
        
        // Log general update if no specific changes were logged
        if (!$hasStatusChange && !$hasPriorityChange && !$hasCommentChange && $imagesCount == 0) {
            BlockWorkOrderLog::createLog(
                $blockWorkOrder->id,
                'updated',
                'Work order details updated',
                [
                    'user_id' => $userId,
                ]
            );
        }

        // Generate or regenerate work docket PDF
        $shouldRegenerate = false;
        $isRegenerating = false;
        
        if ($isCompleting) {
            // Generate work docket when work order is completed
            $shouldRegenerate = true;
        } elseif ($isCompleted && $isAdmin && $request->filled('regenerate_docket') && $request->regenerate_docket) {
            // Regenerate work docket for completed work order if admin requests it
            $shouldRegenerate = true;
            $isRegenerating = true;
        }
        
        if ($shouldRegenerate) {
            try {
                $workDocketService = new WorkDocketService();
                $workDocketService->generateWorkDocket($blockWorkOrder->fresh());
                
                // Log the generation/regeneration
                $logType = $isRegenerating ? 'work_docket_regenerated' : 'work_docket_generated';
                $logDescription = $isRegenerating 
                    ? 'Work docket regenerated by admin after editing' 
                    : 'Work docket generated upon completion';
                    
                BlockWorkOrderLog::createLog(
                    $blockWorkOrder->id,
                    $logType,
                    $logDescription,
                    ['user_id' => Auth::id()]
                );
            } catch (\Exception $e) {
                \Log::error('Failed to generate work docket on update', [
                    'work_order_id' => $blockWorkOrder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Work order updated successfully!',
                'data' => $blockWorkOrder->fresh()
            ]);
        }

        return redirect()->route('block-work-orders.index')
            ->with('success', 'Block work order updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlockWorkOrder $blockWorkOrder)
    {
        // Soft delete the work order (matches inspection pattern)
        $blockWorkOrder->delete();

        // Check if request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Work order deleted successfully!'
            ]);
        }

        return redirect()->route('block-work-orders.index')
            ->with('success', 'Block work order deleted successfully!');
    }

    /**
     * Get block work orders for API/JSON response.
     */
    public function getBlockWorkOrders()
    {
        $workOrders = BlockWorkOrder::with(['block', 'blockIssue', 'issuedBy'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $workOrders
        ]);
    }

    /**
     * Get a specific block work order for API/JSON response.
     */
    public function getBlockWorkOrder(BlockWorkOrder $blockWorkOrder)
    {
        $blockWorkOrder->load(['block', 'blockIssue', 'blockUnit', 'blockBuilding', 'issuedBy', 'images']);

        return response()->json([
            'success' => true,
            'data' => $blockWorkOrder
        ]);
    }

    /**
     * Get work orders for a specific block for API/JSON response.
     */
    public function getWorkOrdersByBlock($blockId)
    {
        $workOrders = BlockWorkOrder::with(['block', 'blockIssue', 'blockUnit', 'blockBuilding', 'issuedBy'])
            ->where('block_id', $blockId)
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $workOrders
        ]);
    }

    /**
     * Generate a unique work order reference number
     */
    private function generateWorkOrderRefNo()
    {
        $prefix = 'WO';
        $year = date('Y');
        $month = date('m');
        
        // Get the last work order number for this month
        $lastWorkOrder = BlockWorkOrder::where('ref_no', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastWorkOrder) {
            // Extract the number part and increment
            $parts = explode('-', $lastWorkOrder->ref_no);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format: WO-202509-001
        return sprintf('%s-%s%s-%03d', $prefix, $year, $month, $newNumber);
    }

    /**
     * Reassign rejected work order to another contractor admin
     */
    public function reassign(Request $request, BlockWorkOrder $blockWorkOrder)
    {
        $user = Auth::user();
        
        // Check if work order is rejected
        if ($blockWorkOrder->acceptance_status !== 'rejected') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only rejected work orders can be reassigned.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Only rejected work orders can be reassigned.');
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'contractor_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Invalid contractor selected.');
        }

        // Verify the new contractor is a Contractor Admin
        $newContractor = \App\Models\User::with('userType')->find($request->contractor_id);
        if (!$newContractor || !$newContractor->userType || $newContractor->userType->name !== 'Contractor Admin') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user must be a Contractor Admin.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Selected user must be a Contractor Admin.');
        }

        // Store old contractor info before updating
        $oldContractorId = $blockWorkOrder->contractor_id;
        $oldContractor = $oldContractorId ? \App\Models\User::find($oldContractorId) : null;
        $oldContractorName = $oldContractor ? $oldContractor->name : 'N/A';

        // Update the work order
        $blockWorkOrder->update([
            'contractor_id' => $request->contractor_id,
            'acceptance_status' => 'pending', // Reset to pending for new contractor
            'updated_by' => $user->id,
        ]);

        // Create a note about the reassignment
        \App\Models\BlockWorkOrderNote::create([
            'block_work_order_id' => $blockWorkOrder->id,
            'note' => "Work order reassigned from {$oldContractorName} (ID: {$oldContractorId}) to {$newContractor->name} (ID: {$request->contractor_id})",
            'note_type' => 'reassignment',
            'created_by' => $user->id,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Work order reassigned successfully!',
                'data' => $blockWorkOrder->fresh()
            ]);
        }

        return redirect()->back()->with('success', 'Work order reassigned successfully!');
    }

    /**
     * Get all contractor admins for reassignment dropdown
     */
    public function getContractorAdmins(Request $request)
    {
        $contractorAdmins = \App\Models\User::whereHas('userType', function($query) {
            $query->where('name', 'Contractor Admin');
        })
        ->where('is_active', true)
        ->with('userType')
        ->orderBy('name')
        ->get(['id', 'name', 'email']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $contractorAdmins
            ]);
        }

        return $contractorAdmins;
    }

    /**
     * Download work docket PDF
     */
    public function downloadWorkDocket(BlockWorkOrder $blockWorkOrder)
    {
        $workDocketService = new WorkDocketService();
        $response = $workDocketService->downloadWorkDocket($blockWorkOrder);

        if (!$response) {
            return redirect()->back()
                ->with('error', 'Work docket PDF not found. Please ensure the work order is completed.');
        }

        return $response;
    }

    /**
     * Upload photos for completed work order (admin only)
     */
    public function uploadPhotos(Request $request, BlockWorkOrder $blockWorkOrder)
    {
        // Check if work order is completed and user is admin
        if ($blockWorkOrder->status != 3 || !auth()->user()->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $currentPhotoCount = $blockWorkOrder->images()->count();
        $photos = $request->file('photos');
        $totalPhotos = $currentPhotoCount + count($photos);

        // Check if adding these photos would exceed the limit of 6
        if ($totalPhotos > 6) {
            $allowed = 6 - $currentPhotoCount;
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Maximum 6 photos allowed. You can add {$allowed} more photo(s).",
                ], 422);
            }
            return redirect()->back()->with('error', "Maximum 6 photos allowed. You can add {$allowed} more photo(s).");
        }

        $photosCount = count($photos);
        foreach ($photos as $photo) {
            $imagePath = 'work-orders/' . $blockWorkOrder->id;
            $imageName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            
            $photo->storeAs('public/' . $imagePath, $imageName);
            
            BlockWorkOrderImage::create([
                'block_work_order_id' => $blockWorkOrder->id,
                'image_path' => $imagePath,
                'image_name' => $imageName,
                's3_status' => 0,
                'created_by' => auth()->id(),
            ]);
        }

        // Log photo upload
        BlockWorkOrderLog::createLog(
            $blockWorkOrder->id,
            'attachment_added',
            "{$photosCount} photo(s) uploaded",
            [
                'field_name' => 'images',
                'new_value' => $photosCount,
                'user_id' => auth()->id(),
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            $blockWorkOrder->load('images');
            return response()->json([
                'success' => true,
                'message' => 'Photos uploaded successfully',
                'data' => $blockWorkOrder
            ]);
        }

        return redirect()->back()->with('success', 'Photos uploaded successfully');
    }

    /**
     * Delete photo from completed work order (admin only)
     */
    public function deletePhoto(Request $request, BlockWorkOrder $blockWorkOrder, BlockWorkOrderImage $photo)
    {
        // Check if work order is completed and user is admin
        if ($blockWorkOrder->status != 3 || !auth()->user()->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // Verify photo belongs to work order
        if ($photo->block_work_order_id != $blockWorkOrder->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Photo not found');
        }

        // Delete file from storage
        $filePath = 'public/' . $photo->image_path . '/' . $photo->image_name;
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        $photoId = $photo->id;
        $photo->delete();

        // Log photo deletion
        BlockWorkOrderLog::createLog(
            $blockWorkOrder->id,
            'attachment_deleted',
            'Photo deleted',
            [
                'field_name' => 'images',
                'related_id' => $photoId,
                'user_id' => auth()->id(),
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully',
            ]);
        }

        return redirect()->back()->with('success', 'Photo deleted successfully');
    }

    /**
     * Add note to completed work order (admin only)
     */
    public function addNote(Request $request, BlockWorkOrder $blockWorkOrder)
    {
        // Check if work order is completed and user is admin
        if ($blockWorkOrder->status != 3 || !auth()->user()->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'note' => 'required|string|max:5000',
        ]);

        $note = \App\Models\BlockWorkOrderNote::create([
            'block_work_order_id' => $blockWorkOrder->id,
            'note' => $request->note,
            'created_by' => auth()->id(),
        ]);

        // Log note addition
        BlockWorkOrderLog::createLog(
            $blockWorkOrder->id,
            'comment_added',
            'Note added to work order',
            [
                'field_name' => 'notes',
                'related_id' => $note->id,
                'user_id' => auth()->id(),
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            $blockWorkOrder->load('notes.creator');
            return response()->json([
                'success' => true,
                'message' => 'Note added successfully',
                'data' => $blockWorkOrder
            ]);
        }

        return redirect()->back()->with('success', 'Note added successfully');
    }

    /**
     * Update note in completed work order (admin only)
     */
    public function updateNote(Request $request, BlockWorkOrder $blockWorkOrder, \App\Models\BlockWorkOrderNote $note)
    {
        // Check if work order is completed and user is admin
        if ($blockWorkOrder->status != 3 || !auth()->user()->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // Verify note belongs to work order
        if ($note->block_work_order_id != $blockWorkOrder->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Note not found');
        }

        $request->validate([
            'note' => 'required|string|max:5000',
        ]);

        $note->note = $request->note;
        $note->save();

        // Log note update
        BlockWorkOrderLog::createLog(
            $blockWorkOrder->id,
            'comment_updated',
            'Note updated',
            [
                'field_name' => 'notes',
                'related_id' => $note->id,
                'user_id' => auth()->id(),
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            $blockWorkOrder->load('notes.creator');
            return response()->json([
                'success' => true,
                'message' => 'Note updated successfully',
                'data' => $blockWorkOrder
            ]);
        }

        return redirect()->back()->with('success', 'Note updated successfully');
    }

    /**
     * Delete note from completed work order (admin only)
     */
    public function deleteNote(Request $request, BlockWorkOrder $blockWorkOrder, \App\Models\BlockWorkOrderNote $note)
    {
        // Check if work order is completed and user is admin
        if ($blockWorkOrder->status != 3 || !auth()->user()->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // Verify note belongs to work order
        if ($note->block_work_order_id != $blockWorkOrder->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Note not found');
        }

        $noteId = $note->id;
        $note->delete();

        // Log note deletion
        BlockWorkOrderLog::createLog(
            $blockWorkOrder->id,
            'comment_updated',
            'Note deleted from work order',
            [
                'field_name' => 'notes',
                'related_id' => $noteId,
                'user_id' => auth()->id(),
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully',
            ]);
        }

        return redirect()->back()->with('success', 'Note deleted successfully');
    }
}
