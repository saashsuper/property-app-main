<?php

namespace App\Http\Controllers;

use App\Models\BlockIssue;
use App\Models\Block;
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
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
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
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $blockIssues = $query->orderBy('created_at', 'desc')->paginate(10);
        $blocks = Block::orderBy('name')->get();
        $users = User::orderBy('name')->get();

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
            'ref_no' => 'required|string|max:100|unique:block_issues',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|integer|min:1|max:5',
            'status' => 'required|integer|min:1|max:5',
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
        $data['reported_by'] = $data['reported_by'] ?? Auth::id();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $blockIssue = BlockIssue::create($data);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = 'block-issues/images';
                
                $image->storeAs('public/' . $imagePath, $imageName);
                
                // You can create a BlockIssueImage model if needed
                // BlockIssueImage::create([
                //     'block_issue_id' => $blockIssue->id,
                //     'image_name' => $imageName,
                //     'image_path' => $imagePath,
                // ]);
            }
        }

        return redirect()->route('block-issues.index')
            ->with('success', 'Block issue created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlockIssue $blockIssue)
    {
        $blockIssue->load(['block', 'reportedBy', 'assignedTo', 'creator', 'updater']);
        
        return view('block-issues.show', compact('blockIssue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlockIssue $blockIssue)
    {
        $blocks = Block::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('block-issues.edit', compact('blockIssue', 'blocks', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlockIssue $blockIssue)
    {
        $validator = Validator::make($request->all(), [
            'block_id' => 'required|exists:blocks,id',
            'ref_no' => 'required|string|max:100|unique:block_issues,ref_no,' . $blockIssue->id,
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|integer|min:1|max:5',
            'status' => 'required|integer|min:1|max:5',
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
                
                // You can create a BlockIssueImage model if needed
                // BlockIssueImage::create([
                //     'block_issue_id' => $blockIssue->id,
                //     'image_name' => $imageName,
                //     'image_path' => $imagePath,
                // ]);
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
    public function getBlockIssues()
    {
        $blockIssues = BlockIssue::with(['block', 'reportedBy', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $blockIssues
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
}
