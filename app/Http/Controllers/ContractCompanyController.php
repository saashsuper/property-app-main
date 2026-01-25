<?php

namespace App\Http\Controllers;

use App\Models\ContractCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContractCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContractCompany::query()->with(['creator', 'updater']);

        // Filter by status (all | active | archived)
        $status = $request->get('status', 'active');
        if ($status === 'archived') {
            $query->onlyTrashed()->where('archive_status', ContractCompany::STATUS_ARCHIVED);
        } elseif ($status === 'all') {
            $query->withTrashed();
        } else {
            $query->active();
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('website', 'like', "%{$search}%");
            });
        }

        $companies = $query->orderBy('updated_at', 'desc')->get();

        return view('contract-companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contract-companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        ContractCompany::create($data);

        return redirect()->route('contract-companies.index')
            ->with('success', 'Contract company created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractCompany $contractCompany)
    {
        $contractCompany->load(['creator', 'updater']);
        return view('contract-companies.show', compact('contractCompany'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContractCompany $contractCompany)
    {
        return view('contract-companies.edit', compact('contractCompany'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContractCompany $contractCompany)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $contractCompany->update($data);

        return redirect()->route('contract-companies.index')
            ->with('success', 'Contract company updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * If company has no related entities, it will be permanently deleted.
     * If company has related entities, it will be archived (soft deleted with archived status).
     */
    public function destroy(ContractCompany $contractCompany)
    {
        // Check if company has any related entities
        if ($contractCompany->hasRelatedEntities()) {
            // Company has related entities - only archive it
            $contractCompany->archive();
            
            $message = 'Contract company archived successfully. It can be restored later if needed.';
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return redirect()->route('contract-companies.index')
                ->with('success', $message);
        } else {
            // No related entities - permanently delete
            $contractCompany->update(['deleted_by' => Auth::id()]);
            $contractCompany->forceDelete();
            
            $message = 'Contract company deleted successfully!';
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return redirect()->route('contract-companies.index')
                ->with('success', $message);
        }
    }
}
