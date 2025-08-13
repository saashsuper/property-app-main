@extends('layouts.master')

@section('title')
    Create Block - PROMAN
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Create New Block</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blocks.index') }}">Blocks</a></li>
                            <li class="breadcrumb-item active">Create</li>
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
                        <h4 class="card-title">Block Information</h4>
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

                        <form action="{{ route('blocks.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Block Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required>
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
                                                <option value="{{ $propertyManager->id }}" {{ old('block_manager_id') == $propertyManager->id ? 'selected' : '' }}>
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
                                                <option value="{{ $blockType->id }}" {{ old('block_type_id') == $blockType->id ? 'selected' : '' }}>
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
                                               id="management_company" name="management_company" value="{{ old('management_company') }}" required>
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
                                                  id="management_company_address" name="management_company_address" rows="3">{{ old('management_company_address') }}</textarea>
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
                                                  id="block_address" name="block_address" rows="3" required>{{ old('block_address') }}</textarea>
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
                                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
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
                                                        {{ old('state_id') == $state->id ? 'selected' : '' }}>
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
                                               id="car_spaces" name="car_spaces" value="{{ old('car_spaces', 0) }}" min="0" required>
                                        @error('car_spaces')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="no_of_units" class="form-label">No. of Units</label>
                                        <input type="number" class="form-control @error('no_of_units') is-invalid @enderror" 
                                               id="no_of_units" name="no_of_units" value="{{ old('no_of_units') }}" min="0">
                                        @error('no_of_units')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="inspection_count" class="form-label">Number of Inspections in a Year</label>
                                        <input type="number" class="form-control @error('inspection_count') is-invalid @enderror" 
                                               id="inspection_count" name="inspection_count" value="{{ old('inspection_count') }}" min="0">
                                        @error('inspection_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Block Image</label>
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
                                            <i class="ri-save-line align-bottom me-1"></i> Create Block
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
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
        
        // Reset state selection
        stateSelect.value = '';
    }

    // Initial update
    updateStates();

    // Update states when country changes
    countrySelect.addEventListener('change', updateStates);
});
</script>
@endpush 