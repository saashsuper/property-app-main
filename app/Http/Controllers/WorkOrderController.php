<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WorkOrder::with(['user', 'creator'])->active();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('fault_description', 'like', "%{$search}%")
                  ->orWhere('issue_category', 'like', "%{$search}%")
                  ->orWhere('issue_type', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('creator', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('common_status_id', $request->status);
        }

        $workOrders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('work-orders.index', compact('workOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('work-orders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:work_orders',
            'property_id' => 'required|integer',
            'contractor_id' => 'required|integer',
            'priority' => 'required|integer|min:1|max:5',
            'priority_label' => 'required|string|max:15',
            'fault_description' => 'required|string|max:4000',
            'issue_category' => 'required|string|max:1000',
            'issue_type' => 'required|string|max:1000',
            'issued_date' => 'required|string|max:20',
            'deadline' => 'required|string|max:10',
            'pricing' => 'required|string|max:5',
            'contact_name' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:15',
            'contact_email' => 'nullable|email|max:100',
            'preferred_day' => 'nullable|string|max:10',
            'time_from' => 'nullable|string|max:10',
            'time_to' => 'nullable|string|max:10',
            'note' => 'nullable|string|max:100',
            'report' => 'nullable|string|max:200',
            'type' => 'required|string|max:15',
            'type_id' => 'required|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('images');
        $data['user_id'] = Auth::id();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $workOrder = WorkOrder::create($data);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = 'work-orders';
                
                $image->storeAs('public/' . $imagePath, $imageName);
                
                WorkOrderImage::create([
                    'work_order_id' => $workOrder->id,
                    'image' => $imageName,
                    'common_status_id' => 1,
                ]);
            }
        }

        return redirect()->route('work-orders.index')
            ->with('success', 'Work order created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['user', 'creator', 'images']);
        
        return view('work-orders.show', compact('workOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkOrder $workOrder)
    {
        $workOrder->load('images');
        return view('work-orders.edit', compact('workOrder'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkOrder $workOrder)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:work_orders,code,' . $workOrder->id,
            'property_id' => 'required|integer',
            'contractor_id' => 'required|integer',
            'priority' => 'required|integer|min:1|max:5',
            'priority_label' => 'required|string|max:15',
            'fault_description' => 'required|string|max:4000',
            'issue_category' => 'required|string|max:1000',
            'issue_type' => 'required|string|max:1000',
            'issued_date' => 'required|string|max:20',
            'deadline' => 'required|string|max:10',
            'pricing' => 'required|string|max:5',
            'contact_name' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:15',
            'contact_email' => 'nullable|email|max:100',
            'preferred_day' => 'nullable|string|max:10',
            'time_from' => 'nullable|string|max:10',
            'time_to' => 'nullable|string|max:10',
            'note' => 'nullable|string|max:100',
            'report' => 'nullable|string|max:200',
            'type' => 'required|string|max:15',
            'type_id' => 'required|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('images');
        $data['updated_by'] = Auth::id();

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = 'work-orders';
                
                $image->storeAs('public/' . $imagePath, $imageName);
                
                WorkOrderImage::create([
                    'work_order_id' => $workOrder->id,
                    'image' => $imageName,
                    'common_status_id' => 1,
                ]);
            }
        }

        $workOrder->update($data);

        return redirect()->route('work-orders.index')
            ->with('success', 'Work order updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkOrder $workOrder)
    {
        // Delete associated images
        foreach ($workOrder->images as $image) {
            Storage::delete('public/work-orders/' . $image->image);
            $image->delete();
        }

        $workOrder->delete();

        return redirect()->route('work-orders.index')
            ->with('success', 'Work order deleted successfully!');
    }

    /**
     * Get work orders for API/JSON response.
     */
    public function getWorkOrders()
    {
        $workOrders = WorkOrder::with(['user'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $workOrders
        ]);
    }

    /**
     * Get a specific work order for API/JSON response.
     */
    public function getWorkOrder(WorkOrder $workOrder)
    {
        $workOrder->load(['user', 'images']);

        return response()->json([
            'success' => true,
            'data' => $workOrder
        ]);
    }
}
