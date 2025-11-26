<?php

namespace App\Http\Controllers;

use App\Models\BlockWorkOrder;
use App\Models\BlockWorkOrderImage;
use App\Models\Block;
use App\Models\BlockIssue;
use App\Models\BlockUnit;
use App\Models\BlockBuilding;
use App\Models\IssueLog;
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

        return view('block-work-orders.create', compact('blocks', 'blockIssues', 'blockUnits', 'blockBuildings'));
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
            'property_manager_id' => 'nullable|exists:users,id',
            'preferred_start_date_time' => 'nullable|date',
            'preferred_end_date_time' => 'nullable|date',
            'deadline_date' => 'nullable|date',
            'comment' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            // Check if this is an AJAX request
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
        ];
        
        // Handle contractor or property manager assignment
        if ($request->property_manager_id) {
            $data['contractor_id'] = $request->property_manager_id;
            // You might want to add a flag to distinguish: $data['is_inhouse'] = true;
        } elseif ($request->contractor_id) {
            $data['contractor_id'] = $request->contractor_id;
            // $data['is_inhouse'] = false;
        }
        
        // Auto-generate reference number
        $data['ref_no'] = $this->generateWorkOrderRefNo();
        
        // Set default status to Pending (1)
        $data['status'] = 1;
        
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

        // Handle image uploads
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
            }
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

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Block work order created successfully!',
                'work_order_id' => $workOrder->id,
                'redirect_url' => route('block-work-orders.index')
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
        $blockWorkOrder->load(['block', 'blockIssue', 'blockUnit', 'blockBuilding', 'issuedBy', 'creator', 'images', 'contractCompany', 'contractor.userType']);
        
        // Determine if contractor is a property manager (for backward compatibility)
        $isPropertyManager = false;
        if ($blockWorkOrder->contractor && $blockWorkOrder->contractor->userType) {
            $isPropertyManager = $blockWorkOrder->contractor->userType->name === 'Property manager';
        }
        
        // Return JSON data for AJAX requests (edit modal)
        if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'data' => array_merge($blockWorkOrder->toArray(), [
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
        
        $blockWorkOrder->load('images');
        
        return view('block-work-orders.edit', compact('blockWorkOrder', 'blocks', 'blockIssues', 'blockUnits', 'blockBuildings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlockWorkOrder $blockWorkOrder)
    {
        // Simplified validation for AJAX requests from issue details page
        $validator = Validator::make($request->all(), [
            'block_issue_id' => 'required|exists:block_issues,id',
            'priority_id' => 'required|integer|min:1|max:5',
            'contractor_id' => ['nullable', \Illuminate\Validation\Rule::exists(\App\Models\Contractor::class, 'id')],
            'property_manager_id' => 'nullable|exists:users,id',
            'preferred_start_date_time' => 'nullable|date',
            'preferred_end_date_time' => 'nullable|date',
            'deadline_date' => 'nullable|date',
            'comment' => 'nullable|string',
            'status' => 'nullable|integer|min:1|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            // Check if this is an AJAX request
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
        if ($request->property_manager_id) {
            $data['contractor_id'] = $request->property_manager_id;
        } elseif ($request->contractor_id) {
            $data['contractor_id'] = $request->contractor_id;
        }
        
        // Update status if provided
        if ($request->has('status')) {
            $data['status'] = $request->status;
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

        // Handle image uploads
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
            }
        }

        $blockWorkOrder->update($data);

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Work order updated successfully!',
                'data' => $blockWorkOrder
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
        // Delete associated images
        foreach ($blockWorkOrder->images as $image) {
            Storage::delete('public/' . $image->image_path . '/' . $image->image_name);
            $image->delete();
        }

        // Delete PDF if exists
        if ($blockWorkOrder->pdf_path && $blockWorkOrder->pdf_name) {
            Storage::delete('public/' . $blockWorkOrder->pdf_path . '/' . $blockWorkOrder->pdf_name);
        }

        $blockWorkOrder->delete();

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
}
