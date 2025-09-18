<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isContractorAdmin = $user->hasType('Contractor Admin');
        
        $query = User::with(['userType'])->active();

        // If Contractor Admin, only show users they created
        if ($isContractorAdmin) {
            $query->where('created_by', $user->id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('userType', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by user type
        if ($request->filled('user_type_id')) {
            $query->where('user_type_id', $request->user_type_id);
        }

        $users = $query->orderBy('updated_at', 'desc')->paginate(10);
        
        // For Contractor Admin, only show Contractor User type in filter
        if ($isContractorAdmin) {
            $userTypes = UserType::where('name', 'Contractor User')->get();
        } else {
            $userTypes = UserType::visible()->orderBy('name')->get();
        }

        return view('users.index', compact('users', 'userTypes', 'isContractorAdmin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $isContractorAdmin = $user->hasType('Contractor Admin');
        
        // For Contractor Admin, only allow creating Contractor User types
        if ($isContractorAdmin) {
            $userTypes = UserType::where('name', 'Contractor User')->get();
        } else {
            $userTypes = UserType::visible()->orderBy('name')->get();
        }
        
        return view('users.create', compact('userTypes', 'isContractorAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'user_type_id' => 'required|exists:user_types,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = auth()->user();
        $isContractorAdmin = $user->hasType('Contractor Admin');
        
        // Validate that Contractor Admin can only create Contractor User types
        if ($isContractorAdmin) {
            $selectedType = UserType::find($request->user_type_id);
            if (!$selectedType || $selectedType->name !== 'Contractor User') {
                return redirect()->back()
                    ->withErrors(['user_type_id' => 'You can only create Contractor User accounts.'])
                    ->withInput();
            }
        }

        $data = $request->except(['password', 'password_confirmation', 'avatar']);
        
        // Set the creator
        $data['created_by'] = auth()->id();
        
        // If contractor admin with web login required, set default password
        $selectedType = UserType::find($request->user_type_id);
        if ($request->has('is_web_login_required') && $selectedType && $selectedType->name === 'Contractor Admin') {
            $data['password'] = Hash::make('password123');
        } else {
            // Otherwise require provided password
            if (!$request->filled('password')) {
                return redirect()->back()->withErrors(['password' => 'Password is required unless web login is auto-enabled for Contractor Admin.'])->withInput();
            }
            $data['password'] = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '_' . $avatar->getClientOriginalName();
            $avatarPath = 'avatars';
            
            $avatar->storeAs('public/' . $avatarPath, $avatarName);
            $data['avatar'] = $avatarPath . '/' . $avatarName;
        }

        User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['userType']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();
        $isContractorAdmin = $currentUser->hasType('Contractor Admin');
        
        // If Contractor Admin, only allow editing users they created
        if ($isContractorAdmin && $user->created_by !== $currentUser->id) {
            return redirect()->route('users.index')
                ->with('error', 'You can only edit users you created.');
        }
        
        // For Contractor Admin, only allow Contractor User type
        if ($isContractorAdmin) {
            $userTypes = UserType::where('name', 'Contractor User')->get();
        } else {
            $userTypes = UserType::visible()->orderBy('name')->get();
        }
        
        return view('users.edit', compact('user', 'userTypes', 'isContractorAdmin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'user_type_id' => 'required|exists:user_types,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $currentUser = auth()->user();
        $isContractorAdmin = $currentUser->hasType('Contractor Admin');
        
        // If Contractor Admin, only allow updating users they created
        if ($isContractorAdmin && $user->created_by !== $currentUser->id) {
            return redirect()->route('users.index')
                ->with('error', 'You can only update users you created.');
        }
        
        // Validate that Contractor Admin can only update to Contractor User type
        if ($isContractorAdmin) {
            $selectedType = UserType::find($request->user_type_id);
            if (!$selectedType || $selectedType->name !== 'Contractor User') {
                return redirect()->back()
                    ->withErrors(['user_type_id' => 'You can only assign Contractor User type.'])
                    ->withInput();
            }
        }

        $data = $request->except(['password', 'password_confirmation', 'avatar']);
        
        // Track who is updating the user
        $data['updated_by'] = $currentUser->id;

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                \Storage::delete('public/' . $user->avatar);
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '_' . $avatar->getClientOriginalName();
            $avatarPath = 'avatars';
            
            $avatar->storeAs('public/' . $avatarPath, $avatarName);
            $data['avatar'] = $avatarPath . '/' . $avatarName;
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();
        $isContractorAdmin = $currentUser->hasType('Contractor Admin');
        
        // If Contractor Admin, only allow deleting users they created
        if ($isContractorAdmin && $user->created_by !== $currentUser->id) {
            return redirect()->route('users.index')
                ->with('error', 'You can only delete users you created.');
        }
        
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete your own account.');
        }

        // Track who is deleting the user
        $user->update(['deleted_by' => $currentUser->id]);
        
        // Soft delete the user
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Get users for API/JSON response.
     */
    public function getUsers()
    {
        $users = User::with(['userType'])
            ->active()
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get a specific user for API/JSON response.
     */
    public function getUser(User $user)
    {
        $user->load(['userType']);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}
