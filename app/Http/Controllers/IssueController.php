<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Issue::with(['reportedBy', 'assignedTo', 'creator']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
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

        $issues = $query->orderBy('created_at', 'desc')->paginate(10);
        $users = User::orderBy('name')->get();

        return view('issues.index', compact('issues', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('issues.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ref_no' => 'required|string|max:100|unique:issues',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'priority' => 'required|integer|min:1|max:5',
            'status' => 'required|integer|min:1|max:5',
            'assigned_to' => 'nullable|exists:users,id',
            'reported_by' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
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

        $issue = Issue::create($data);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = 'issues/images';
                
                $image->storeAs('public/' . $imagePath, $imageName);
                
                // You can create an IssueImage model if needed
                // IssueImage::create([
                //     'issue_id' => $issue->id,
                //     'image_name' => $imageName,
                //     'image_path' => $imagePath,
                // ]);
            }
        }

        return redirect()->route('issues.index')
            ->with('success', 'Issue created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Issue $issue)
    {
        $issue->load(['reportedBy', 'assignedTo', 'creator', 'updater']);
        
        return view('issues.show', compact('issue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Issue $issue)
    {
        $users = User::orderBy('name')->get();

        return view('issues.edit', compact('issue', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Issue $issue)
    {
        $validator = Validator::make($request->all(), [
            'ref_no' => 'required|string|max:100|unique:issues,ref_no,' . $issue->id,
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'priority' => 'required|integer|min:1|max:5',
            'status' => 'required|integer|min:1|max:5',
            'assigned_to' => 'nullable|exists:users,id',
            'reported_by' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('images');
        $data['updated_by'] = Auth::id();

        $issue->update($data);

        // Handle additional image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = 'issues/images';
                
                $image->storeAs('public/' . $imagePath, $imageName);
                
                // You can create an IssueImage model if needed
                // IssueImage::create([
                //     'issue_id' => $issue->id,
                //     'image_name' => $imageName,
                //     'image_path' => $imagePath,
                // ]);
            }
        }

        return redirect()->route('issues.index')
            ->with('success', 'Issue updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Issue $issue)
    {
        try {
            $issue->delete();
            return redirect()->route('issues.index')
                ->with('success', 'Issue deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('issues.index')
                ->with('error', 'Failed to delete issue. It may be referenced by other records.');
        }
    }

    /**
     * Get issues for API
     */
    public function getIssues()
    {
        $issues = Issue::with(['reportedBy', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $issues
        ]);
    }

    /**
     * Get specific issue for API
     */
    public function getIssue(Issue $issue)
    {
        $issue->load(['reportedBy', 'assignedTo', 'creator', 'updater']);

        return response()->json([
            'success' => true,
            'data' => $issue
        ]);
    }
}
