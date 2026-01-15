<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserType;
use App\Models\ContractCompany;
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

        $users = $query->orderBy('updated_at', 'desc')->get();
        
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
        
        // Get contract companies for dropdown
        $contractCompanies = ContractCompany::orderBy('company_name')->get();
        
        return view('users.create', compact('userTypes', 'isContractorAdmin', 'contractCompanies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).+$/',
            'user_type_id' => 'required|exists:user_types,id',
            'contract_company_id' => 'nullable|exists:contract_companies,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address (e.g., user@example.com).',
            'email.unique' => 'This email address is already registered. Please use a different email.',
            'email.max' => 'Email address cannot exceed 191 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter and one special character.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = auth()->user();
        $isContractorAdmin = $user->hasType('Contractor Admin');
        
        $selectedType = UserType::find($request->user_type_id);

        // Validate that Contractor Admin can only create Contractor User types
        if ($isContractorAdmin) {
            if (!$selectedType || $selectedType->name !== 'Contractor User') {
                return redirect()->back()
                    ->withErrors(['user_type_id' => 'You can only create Contractor User accounts.'])
                    ->withInput();
            }
        }

        // Validate that only one Contractor Admin can exist per contract company
        if ($selectedType && $selectedType->name === 'Contractor Admin' && $request->filled('contract_company_id')) {
            $existingAdmin = User::where('user_type_id', $selectedType->id)
                ->where('contract_company_id', $request->contract_company_id)
                ->whereNull('deleted_at')
                ->first();
            
            if ($existingAdmin) {
                return redirect()->back()
                    ->withErrors(['contract_company_id' => 'A Contractor Admin already exists for this contract company. Only one Contractor Admin is allowed per contract company.'])
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

        $newUser = User::create($data);
        
        $this->syncDefaultRolesForUser($newUser, $selectedType);

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
        
        // Get contract companies for dropdown
        $contractCompanies = ContractCompany::orderBy('company_name')->get();
        
        return view('users.edit', compact('user', 'userTypes', 'isContractorAdmin', 'contractCompanies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).+$/',
            'user_type_id' => 'required|exists:user_types,id',
            'contract_company_id' => 'nullable|exists:contract_companies,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address (e.g., user@example.com).',
            'email.unique' => 'This email address is already registered. Please use a different email.',
            'email.max' => 'Email address cannot exceed 191 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter and one special character.',
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
        
        $selectedType = UserType::find($request->user_type_id);

        // Validate that Contractor Admin can only update to Contractor User type
        if ($isContractorAdmin) {
            if (!$selectedType || $selectedType->name !== 'Contractor User') {
                return redirect()->back()
                    ->withErrors(['user_type_id' => 'You can only assign Contractor User type.'])
                    ->withInput();
            }
        }

        // Validate that only one Contractor Admin can exist per contract company
        // Check if user is being set to Contractor Admin or is already a Contractor Admin changing contract company
        $isBecomingContractorAdmin = $selectedType && $selectedType->name === 'Contractor Admin';
        $contractorAdminType = UserType::where('name', 'Contractor Admin')->first();
        $isAlreadyContractorAdmin = $contractorAdminType && $user->user_type_id == $contractorAdminType->id;
        $contractCompanyChanged = $request->filled('contract_company_id') && 
                                  $user->contract_company_id != $request->contract_company_id;
        
        if (($isBecomingContractorAdmin || ($isAlreadyContractorAdmin && $contractCompanyChanged)) && 
            $request->filled('contract_company_id') && $contractorAdminType) {
            $existingAdmin = User::where('user_type_id', $contractorAdminType->id)
                ->where('contract_company_id', $request->contract_company_id)
                ->where('id', '!=', $user->id) // Exclude the current user being updated
                ->whereNull('deleted_at')
                ->first();
            
            if ($existingAdmin) {
                return redirect()->back()
                    ->withErrors(['contract_company_id' => 'A Contractor Admin already exists for this contract company. Only one Contractor Admin is allowed per contract company.'])
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
        $this->syncDefaultRolesForUser($user, $selectedType);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * If user has no related entities, it will be permanently deleted.
     * If user has related entities, it will be archived (soft deleted with archived status).
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();
        $isContractorAdmin = $currentUser->hasType('Contractor Admin');
        
        // If Contractor Admin, only allow deleting users they created
        if ($isContractorAdmin && $user->created_by !== $currentUser->id) {
            $errorMessage = 'You can only delete users you created.';
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 403);
            }
            
            return redirect()->route('users.index')
                ->with('error', $errorMessage);
        }
        
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            $errorMessage = 'Cannot delete your own account.';
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 403);
            }
            
            return redirect()->route('users.index')
                ->with('error', $errorMessage);
        }

        // Check if user has any related entities
        if ($user->hasRelatedEntities()) {
            // User has related entities - only archive it
            // Delete avatar if exists
            if ($user->avatar) {
                \Storage::delete('public/' . $user->avatar);
            }

            $user->archive();

            $message = 'User has been archived because it contains related data (blocks, issues, work orders, etc.).';
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            
            return redirect()->route('users.index')
                ->with('success', $message);
        } else {
            // User has no related entities - permanently delete
            // Delete avatar if exists
            if ($user->avatar) {
                \Storage::delete('public/' . $user->avatar);
            }

            $user->forceDelete();

            $message = 'User permanently deleted successfully!';
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            
            return redirect()->route('users.index')
                ->with('success', $message);
        }
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

    /**
     * Determine which roles should be assigned to a user type.
     */
    protected function getDefaultRolesForUserType(?UserType $userType): array
    {
        if (!$userType) {
            return config('user_type_roles.default', []);
        }

        $mapping = config('user_type_roles', []);

        return $mapping[$userType->name] ?? $mapping['default'] ?? [];
    }

    /**
     * Sync default roles for a user based on their type.
     */
    protected function syncDefaultRolesForUser(User $user, ?UserType $userType = null): void
    {
        $roles = $this->getDefaultRolesForUserType($userType ?? $user->userType);

        $user->syncRoles($roles);
    }
}
