@extends('layouts.master')

@section('title')
    Edit Block - PROMAN
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Block</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blocks.index') }}">Blocks</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Block Information</h4>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Animation Nav Tabs -->
                                                        <ul class="nav nav-tabs nav-tabs-custom" id="blockEditTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="basic-details-tab" data-bs-toggle="tab" href="#basic-details" role="tab" aria-controls="basic-details" aria-selected="true">
                                    <span>Basic Details</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="block-info-tab" data-bs-toggle="tab" href="#block-info" role="tab" aria-controls="block-info" aria-selected="false">
                                    <span>Block Information</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="building-core-tab" data-bs-toggle="tab" href="#building-core" role="tab" aria-controls="building-core" aria-selected="false">
                                    <span>BUILDING/CORE</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="contractors-tab" data-bs-toggle="tab" href="#contractors" role="tab" aria-controls="contractors" aria-selected="false">
                                    <span>CONTRACTORS</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="units-tab" data-bs-toggle="tab" href="#units" role="tab" aria-controls="units" aria-selected="false">
                                    <span>Units</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="site-visit-tab" data-bs-toggle="tab" href="#site-visit" role="tab" aria-controls="site-visit" aria-selected="false">
                                    <span>Site Visit</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="inspections-tab" data-bs-toggle="tab" href="#inspections" role="tab" aria-controls="inspections" aria-selected="false">
                                    <span>Inspections</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="issues-tab" data-bs-toggle="tab" href="#issues" role="tab" aria-controls="issues" aria-selected="false">
                                    <span>Issues</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="work-orders-tab" data-bs-toggle="tab" href="#work-orders" role="tab" aria-controls="work-orders" aria-selected="false">
                                    <span>Work Orders</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-4" id="blockEditTabContent">
                            <!-- Basic Details Tab -->
                            <div class="tab-pane fade show active" id="basic-details" role="tabpanel" aria-labelledby="basic-details-tab">
                                <form action="{{ route('blocks.update', $block) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Block Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" name="name" value="{{ old('name', $block->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="block_manager_id" class="form-label">Block Manager</label>
                                                <select class="form-select @error('block_manager_id') is-invalid @enderror" 
                                                        id="block_manager_id" name="block_manager_id">
                                                    <option value="">Select Block Manager</option>
                                                    @foreach($propertyManagers as $propertyManager)
                                                        <option value="{{ $propertyManager->id }}" 
                                                            {{ old('block_manager_id', $block->block_manager_id) == $propertyManager->id ? 'selected' : '' }}>
                                                            {{ $propertyManager->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('block_manager_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Select a property manager to oversee this block</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="block_type_id" class="form-label">Block Type <span class="text-danger">*</span></label>
                                                <select class="form-select @error('block_type_id') is-invalid @enderror" 
                                                        id="block_type_id" name="block_type_id" required>
                                                    <option value="">Select Block Type</option>
                                                    @foreach($blockTypes as $blockType)
                                                        <option value="{{ $blockType->id }}" 
                                                            {{ old('block_type_id', $block->block_type_id) == $blockType->id ? 'selected' : '' }}>
                                                            {{ $blockType->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('block_type_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="management_company" class="form-label">Management Company <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('management_company') is-invalid @enderror" 
                                                       id="management_company" name="management_company" value="{{ old('management_company', $block->management_company) }}" required>
                                                @error('management_company')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="management_company_address" class="form-label">Management Company Address</label>
                                                <textarea class="form-control @error('management_company_address') is-invalid @enderror" 
                                                          id="management_company_address" name="management_company_address" rows="3">{{ old('management_company_address', $block->management_company_address) }}</textarea>
                                                @error('management_company_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Enter the management company's address</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="block_address" class="form-label">Block Address <span class="text-danger">*</span></label>
                                                <textarea class="form-control @error('block_address') is-invalid @enderror" 
                                                          id="block_address" name="block_address" rows="3" required>{{ old('block_address', $block->block_address) }}</textarea>
                                                @error('block_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Enter the complete address of the block</div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                                                <select class="form-select @error('country_id') is-invalid @enderror" 
                                                        id="country_id" name="country_id" required>
                                                    <option value="">Select Country</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}" 
                                                                {{ old('country_id', $block->country_id) == $country->id ? 'selected' : '' }}>
                                                            {{ $country->country_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('country_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="state_id" class="form-label">County/State <span class="text-danger">*</span></label>
                                                <select class="form-select @error('state_id') is-invalid @enderror" 
                                                        id="state_id" name="state_id" required>
                                                    <option value="">Select County/State</option>
                                                    @foreach($states as $state)
                                                        <option value="{{ $state->id }}" 
                                                                data-country="{{ $state->country_id }}"
                                                                {{ old('state_id', $block->state_id) == $state->id ? 'selected' : '' }}>
                                                            {{ $state->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('state_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="car_spaces" class="form-label">No. of Car Spaces <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('car_spaces') is-invalid @enderror" 
                                                       id="car_spaces" name="car_spaces" value="{{ old('car_spaces', $block->car_spaces) }}" min="0" required>
                                                @error('car_spaces')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="no_of_units" class="form-label">No. of Units</label>
                                                <input type="number" class="form-control @error('no_of_units') is-invalid @enderror" 
                                                       id="no_of_units" name="no_of_units" value="{{ old('no_of_units', $block->no_of_units) }}" min="0">
                                                @error('no_of_units')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="inspection_count" class="form-label">Number of Inspections in a Year</label>
                                                <input type="number" class="form-control @error('inspection_count') is-invalid @enderror" 
                                                       id="inspection_count" name="inspection_count" value="{{ old('inspection_count', $block->inspection_count) }}" min="0">
                                                @error('inspection_count')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Change Block Image</label>
                                                @if($block->image_url)
                                                    <div class="mb-2">
                                                        <img src="{{ $block->image_url }}" alt="{{ $block->name }}" 
                                                             class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                                        <small class="d-block text-muted">Current image</small>
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                                       id="image" name="image" accept="image/*">
                                                <div class="form-text">Accepted formats: JPEG, PNG, JPG, GIF (Max: 2MB)</div>
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <!-- Empty column for layout balance -->
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('blocks.index') }}" class="btn btn-secondary">
                                                    <i class="ri-arrow-left-line align-bottom me-1"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ri-save-line align-bottom me-1"></i> Update Block
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Block Information Tab -->
                            <div class="tab-pane fade" id="block-info" role="tabpanel" aria-labelledby="block-info-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Block Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-borderless mb-0">
                                                    <tr>
                                                        <td class="fw-semibold">Block ID:</td>
                                                        <td>#{{ $block->id }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Created:</td>
                                                        <td>{{ $block->created_at->format('M d, Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Last Updated:</td>
                                                        <td>{{ $block->updated_at->format('M d, Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Status:</td>
                                                        <td><span class="badge bg-success">Active</span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Management Info</h6>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-borderless mb-0">
                                                    <tr>
                                                        <td class="fw-semibold">Owner:</td>
                                                        <td>{{ $block->user->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Block Manager:</td>
                                                        <td>{{ $block->blockManager->name ?? 'Not Assigned' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Block Type:</td>
                                                        <td>{{ $block->blockType->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Country:</td>
                                                        <td>{{ $block->country->country_name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">State:</td>
                                                        <td>{{ $block->state->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Block Address:</td>
                                                        <td>{{ $block->block_address ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Company Address:</td>
                                                        <td>{{ $block->management_company_address ?? 'Not Provided' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Building/Core Tab -->
                            <div class="tab-pane fade" id="building-core" role="tabpanel" aria-labelledby="building-core-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Building Information</h6>
                                            <button class="btn btn-sm btn-primary">
                                                <i class="ri-add-line align-bottom me-1"></i> Add Building
                                            </button>
                                        </div>
                                        
                                        @if($block->buildings && $block->buildings->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Building Name</th>
                                                            <th>Type</th>
                                                            <th>Floor</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($block->buildings as $building)
                                                            <tr>
                                                                <td>{{ $building->name ?? 'N/A' }}</td>
                                                                <td>{{ $building->buildingType->name ?? 'N/A' }}</td>
                                                                <td>{{ $building->floor_no ?? 'N/A' }}</td>
                                                                <td><span class="badge bg-success">Active</span></td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="ri-building-line text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">No buildings found for this block.</p>
                                                <button class="btn btn-primary">Add First Building</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Contractors Tab -->
                            <div class="tab-pane fade" id="contractors" role="tabpanel" aria-labelledby="contractors-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Contractor Information</h6>
                                            <button class="btn btn-sm btn-primary">
                                                <i class="ri-add-line align-bottom me-1"></i> Add Contractor
                                            </button>
                                        </div>
                                        
                                        @if($block->contractors && $block->contractors->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Contractor ID</th>
                                                            <th>Type ID</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($block->contractors as $contractor)
                                                            <tr>
                                                                <td>#{{ $contractor->contractor_id ?? 'N/A' }}</td>
                                                                <td>#{{ $contractor->contractor_type_id ?? 'N/A' }}</td>
                                                                <td>
                                                                    @if($contractor->status == 1)
                                                                        <span class="badge bg-success">Default</span>
                                                                    @else
                                                                        <span class="badge bg-info">Active</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="ri-user-settings-line text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">No contractors assigned to this block.</p>
                                                <button class="btn btn-primary">Assign Contractor</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Units Tab -->
                            <div class="tab-pane fade" id="units" role="tabpanel" aria-labelledby="units-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Unit Information</h6>
                                            <button class="btn btn-sm btn-primary">
                                                <i class="ri-add-line align-bottom me-1"></i> Add Unit
                                            </button>
                                        </div>
                                        
                                        @if($block->units && $block->units->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Unit Code</th>
                                                            <th>Unit Name</th>
                                                            <th>Type</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($block->units as $unit)
                                                            <tr>
                                                                <td>{{ $unit->unit_code ?? 'N/A' }}</td>
                                                                <td>{{ $unit->unit_name ?? 'N/A' }}</td>
                                                                <td>{{ $unit->unitType->name ?? 'N/A' }}</td>
                                                                <td><span class="badge bg-success">Active</span></td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="ri-home-line text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">No units found for this block.</p>
                                                <button class="btn btn-primary">Add First Unit</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Site Visit Tab -->
                            <div class="tab-pane fade" id="site-visit" role="tabpanel" aria-labelledby="site-visit-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Site Visit History</h6>
                                            <button class="btn btn-sm btn-primary">
                                                <i class="ri-add-line align-bottom me-1"></i> Schedule Visit
                                            </button>
                                        </div>
                                        
                                        @if($block->blockVisits && $block->blockVisits->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Visit Date</th>
                                                            <th>Reference</th>
                                                            <th>Job Reason</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($block->blockVisits as $visit)
                                                            <tr>
                                                                <td>{{ $visit->scheduled_date_time ? \Carbon\Carbon::parse($visit->scheduled_date_time)->format('M d, Y') : 'N/A' }}</td>
                                                                <td>{{ $visit->ref_no ?? 'N/A' }}</td>
                                                                <td>{{ $visit->job_reason_id ?? 'N/A' }}</td>
                                                                <td>
                                                                    @if($visit->end_date_time)
                                                                        <span class="badge bg-success">Completed</span>
                                                                    @elseif($visit->start_date_time)
                                                                        <span class="badge bg-warning">In Progress</span>
                                                                    @else
                                                                        <span class="badge bg-info">Scheduled</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="ri-calendar-check-line text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">No site visits scheduled for this block.</p>
                                                <button class="btn btn-primary">Schedule First Visit</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Inspections Tab -->
                            <div class="tab-pane fade" id="inspections" role="tabpanel" aria-labelledby="inspections-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Inspection History</h6>
                                            <button class="btn btn-sm btn-primary">
                                                <i class="ri-add-line align-bottom me-1"></i> Schedule Inspection
                                            </button>
                                        </div>
                                        
                                        @if(isset($blockInspections) && $blockInspections->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Inspection Date</th>
                                                            <th>Reference</th>
                                                            <th>Inspector</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($blockInspections as $inspection)
                                                            <tr>
                                                                <td>{{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('M d, Y') : 'N/A' }}</td>
                                                                <td>{{ $inspection->ref_no ?? 'N/A' }}</td>
                                                                <td>{{ $inspection->creator->name ?? 'N/A' }}</td>
                                                                <td>
                                                                    @if($inspection->job_status_id == 1)
                                                                        <span class="badge bg-info">Scheduled</span>
                                                                    @elseif($inspection->job_status_id == 2)
                                                                        <span class="badge bg-warning">In Progress</span>
                                                                    @elseif($inspection->job_status_id == 3)
                                                                        <span class="badge bg-success">Completed</span>
                                                                    @elseif($inspection->job_status_id == 4)
                                                                        <span class="badge bg-danger">Cancelled</span>
                                                                    @elseif($inspection->job_status_id == 5)
                                                                        <span class="badge bg-secondary">On Hold</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Unknown</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="ri-search-line text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">No inspections scheduled for this block.</p>
                                                <button class="btn btn-primary">Schedule First Inspection</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Issues Tab -->
                            <div class="tab-pane fade" id="issues" role="tabpanel" aria-labelledby="issues-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Block Issues</h6>
                                            <button class="btn btn-sm btn-primary">
                                                <i class="ri-add-line align-bottom me-1"></i> Report Issue
                                            </button>
                                        </div>
                                        
                                        @if($block->issues && $block->issues->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Issue ID</th>
                                                            <th>Title</th>
                                                            <th>Priority</th>
                                                            <th>Status</th>
                                                            <th>Reported Date</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($block->issues as $issue)
                                                            <tr>
                                                                <td>#{{ $issue->id }}</td>
                                                                <td>{{ $issue->issue ?? 'N/A' }}</td>
                                                                <td>
                                                                    @if($issue->priority_id == 1)
                                                                        <span class="badge bg-success">Low</span>
                                                                    @elseif($issue->priority_id == 2)
                                                                        <span class="badge bg-info">Normal</span>
                                                                    @elseif($issue->priority_id == 3)
                                                                        <span class="badge bg-warning">High</span>
                                                                    @elseif($issue->priority_id == 4)
                                                                        <span class="badge bg-danger">Urgent</span>
                                                                    @elseif($issue->priority_id == 5)
                                                                        <span class="badge bg-dark">Critical</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Unknown</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($issue->issue_status_id == 1)
                                                                        <span class="badge bg-warning">Open</span>
                                                                    @elseif($issue->issue_status_id == 2)
                                                                        <span class="badge bg-info">In Progress</span>
                                                                    @elseif($issue->issue_status_id == 3)
                                                                        <span class="badge bg-success">Resolved</span>
                                                                    @elseif($issue->issue_status_id == 4)
                                                                        <span class="badge bg-secondary">Closed</span>
                                                                    @elseif($issue->issue_status_id == 5)
                                                                        <span class="badge bg-danger">On Hold</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Unknown</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $issue->created_at ? $issue->created_at->format('M d, Y') : 'N/A' }}</td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="ri-error-warning-line text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">No issues reported for this block.</p>
                                                <button class="btn btn-primary">Report First Issue</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Work Orders Tab -->
                            <div class="tab-pane fade" id="work-orders" role="tabpanel" aria-labelledby="work-orders-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Work Orders</h6>
                                            <button class="btn btn-sm btn-primary">
                                                <i class="ri-add-line align-bottom me-1"></i> Create Work Order
                                            </button>
                                        </div>
                                        
                                        @if(isset($blockWorkOrders) && $blockWorkOrders->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Work Order #</th>
                                                            <th>Title</th>
                                                            <th>Priority</th>
                                                            <th>Status</th>
                                                            <th>Created Date</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($blockWorkOrders as $workOrder)
                                                            <tr>
                                                                <td>#{{ $workOrder->id }}</td>
                                                                <td>{{ $workOrder->issue ?? 'N/A' }}</td>
                                                                <td>
                                                                    @if($workOrder->priority_id == 1)
                                                                        <span class="badge bg-success">Low</span>
                                                                    @elseif($workOrder->priority_id == 2)
                                                                        <span class="badge bg-info">Normal</span>
                                                                    @elseif($workOrder->priority_id == 3)
                                                                        <span class="badge bg-warning">High</span>
                                                                    @elseif($workOrder->priority_id == 4)
                                                                        <span class="badge bg-danger">Urgent</span>
                                                                    @elseif($workOrder->priority_id == 5)
                                                                        <span class="badge bg-dark">Critical</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Unknown</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($workOrder->status == 1)
                                                                        <span class="badge bg-warning">Open</span>
                                                                    @elseif($workOrder->status == 2)
                                                                        <span class="badge bg-info">In Progress</span>
                                                                    @else
                                                                        <span class="badge bg-success">Completed</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $workOrder->created_at ? $workOrder->created_at->format('M d, Y') : 'N/A' }}</td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="ri-file-list-line text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">No work orders created for this block.</p>
                                                <button class="btn btn-primary">Create First Work Order</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country_id');
    const stateSelect = document.getElementById('state_id');
    const stateOptions = stateSelect.querySelectorAll('option[data-country]');

    function updateStates() {
        const selectedCountryId = countrySelect.value;
        
        // Hide all state options
        stateOptions.forEach(option => {
            option.style.display = 'none';
        });
        
        // Show only states for selected country
        if (selectedCountryId) {
            stateOptions.forEach(option => {
                if (option.dataset.country === selectedCountryId) {
                    option.style.display = '';
                }
            });
        }
        
        // Reset state selection if it doesn't belong to selected country
        const currentStateId = stateSelect.value;
        const currentStateOption = stateSelect.querySelector(`option[value="${currentStateId}"]`);
        if (currentStateOption && currentStateOption.dataset.country !== selectedCountryId) {
            stateSelect.value = '';
        }
    }

    // Initial update
    updateStates();

    // Update states when country changes
    countrySelect.addEventListener('change', updateStates);

    // Initialize Bootstrap tabs
    const triggerTabList = document.querySelectorAll('#blockEditTabs a[data-bs-toggle="tab"]');
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', event => {
            event.preventDefault();
            tabTrigger.show();
        });
    });
});
</script>
@endpush 