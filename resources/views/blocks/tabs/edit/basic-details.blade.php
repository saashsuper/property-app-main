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

<form action="{{ route('blocks.update', $block) }}" method="POST">
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
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('blocks.index') }}" class="btn btn-secondary">
                    <i class="ph-arrow-left align-bottom me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ph-floppy-disk align-bottom me-1"></i> Update Block
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Country-State dependency logic
    const countrySelect = document.getElementById('country_id');
    const stateSelect = document.getElementById('state_id');
    
    if (!countrySelect || !stateSelect) {
        console.warn('Country or State select elements not found in basic-details tab');
        return;
    }
    
    console.log('Country-State dependency initialized in basic-details tab');

    function loadStatesByCountry(countryId) {
        if (!countryId) {
            // Clear states if no country selected
            stateSelect.innerHTML = '<option value="">Select County/State</option>';
            return;
        }

        console.log('Loading states for country ID:', countryId);
        
        // Show loading state
        stateSelect.innerHTML = '<option value="">Loading states...</option>';
        stateSelect.disabled = true;

        // Make AJAX request to get states
        fetch(`/api/states/${countryId}`)
            .then(response => response.json())
            .then(states => {
                console.log('Received states:', states);
                
                // Clear existing options
                stateSelect.innerHTML = '<option value="">Select County/State</option>';
                
                // Add new state options
                states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.id;
                    option.textContent = state.name;
                    stateSelect.appendChild(option);
                });
                
                stateSelect.disabled = false;
                console.log('States loaded successfully:', states.length, 'states');
            })
            .catch(error => {
                console.error('Error loading states:', error);
                stateSelect.innerHTML = '<option value="">Error loading states</option>';
                stateSelect.disabled = false;
            });
    }

    function updateStates() {
        const selectedCountryId = countrySelect.value;
        console.log('Selected country ID:', selectedCountryId);
        
        // Load states for the selected country
        loadStatesByCountry(selectedCountryId);
    }

    // Initialize states on page load
    updateStates();

    // Update states when country changes
    countrySelect.addEventListener('change', updateStates);
});
</script>
@endpush
