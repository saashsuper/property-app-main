<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UserType::withCount('users');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $userTypes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('user-types.index', compact('userTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191|unique:user_types,name',
            'description' => 'nullable|string|max:191',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        UserType::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('user-types.index')
            ->with('success', 'User type created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserType $userType)
    {
        $userType->load(['users' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        
        return view('user-types.show', compact('userType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserType $userType)
    {
        return view('user-types.edit', compact('userType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserType $userType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191|unique:user_types,name,' . $userType->id,
            'description' => 'nullable|string|max:191',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userType->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('user-types.index')
            ->with('success', 'User type updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserType $userType)
    {
        // Check if user type has associated users
        if ($userType->users()->count() > 0) {
            return redirect()->route('user-types.index')
                ->with('error', 'Cannot delete user type. It has associated users.');
        }

        $userType->delete();

        return redirect()->route('user-types.index')
            ->with('success', 'User type deleted successfully!');
    }

    /**
     * Get user types for API/JSON response.
     */
    public function getUserTypes()
    {
        $userTypes = UserType::withCount('users')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $userTypes
        ]);
    }

    /**
     * Get a specific user type for API/JSON response.
     */
    public function getUserType(UserType $userType)
    {
        $userType->load(['users' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return response()->json([
            'success' => true,
            'data' => $userType
        ]);
    }
}
