<?php

namespace App\Http\Controllers;

use App\Models\BlockWorkOrder;
use App\Models\BlockWorkOrderImage;
use App\Models\Block;
use App\Models\BlockIssue;
use App\Models\BlockUnit;
use App\Models\BlockBuilding;
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
        $query = BlockWorkOrder::with(['block', 'blockIssue', 'issuedBy', 'creator'])->active();

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

        $workOrders = $query->orderBy('created_at', 'desc')->paginate(10);
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
        $validator = Validator::make($request->all(), [
            'block_id' => 'required|exists:blocks,id',
            'block_issue_id' => 'required|exists:block_issues,id',
            'issued_from' => 'nullable|integer',
            'from_id' => 'nullable|integer',
            'block_unit_id' => 'nullable|exists:block_units,id',
            'block_building_id' => 'nullable|exists:block_buildings,id',
            'priority_id' => 'required|integer|min:1|max:5',
            'issued_date_time' => 'nullable|date',
            'contractor_id' => 'nullable|integer',
            'contact_name' => 'nullable|string|max:100',
            'contact_mobile' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:100',
            'preferred_start_date_time' => 'nullable|date',
            'preferred_end_date_time' => 'nullable|date',
            'deadline_date' => 'nullable|date',
            'status' => 'required|integer|min:1|max:5',
            'ref_no' => 'required|string|max:100|unique:block_work_orders',
            'repair_category_id' => 'nullable|integer',
            'issue' => 'nullable|string|max:255',
            'note_for_access' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['images', 'pdf']);
        $data['issued_by'] = Auth::id();
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

        return redirect()->route('block-work-orders.index')
            ->with('success', 'Block work order created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlockWorkOrder $blockWorkOrder)
    {
        $blockWorkOrder->load(['block', 'blockIssue', 'blockUnit', 'blockBuilding', 'issuedBy', 'creator', 'images']);
        
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
        $validator = Validator::make($request->all(), [
            'block_id' => 'required|exists:blocks,id',
            'block_issue_id' => 'required|exists:block_issues,id',
            'issued_from' => 'nullable|integer',
            'from_id' => 'nullable|integer',
            'block_unit_id' => 'nullable|exists:block_units,id',
            'block_building_id' => 'nullable|exists:block_buildings,id',
            'priority_id' => 'required|integer|min:1|max:5',
            'issued_date_time' => 'nullable|date',
            'contractor_id' => 'nullable|integer',
            'contact_name' => 'nullable|string|max:100',
            'contact_mobile' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:100',
            'preferred_start_date_time' => 'nullable|date',
            'preferred_end_date_time' => 'nullable|date',
            'deadline_date' => 'nullable|date',
            'status' => 'required|integer|min:1|max:5',
            'ref_no' => 'required|string|max:100|unique:block_work_orders,ref_no,' . $blockWorkOrder->id,
            'repair_category_id' => 'nullable|integer',
            'issue' => 'nullable|string|max:255',
            'note_for_access' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['images', 'pdf']);
        $data['updated_by'] = Auth::id();

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
}
