@extends('layouts.master')
@section('title')
    Create Block Issue - PROMAN
@endsection
@section('css')
    <!-- add your css here -->
@endsection
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Create Block Issue</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-issues.index') }}">Block Issues</a></li>
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
                        <h4 class="card-title">Create New Block Issue</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('block-issues.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <!-- Row 0: Block (required outside block context) -->
                                <div class="col-md-4 mb-3">
                                    <label for="block_display" class="form-label">Block <span class="text-danger">*</span></label>
                                    <div class="autoComplete_wrapper" id="blockAutoCompleteWrapper">
                                        <input type="text"
                                               class="form-control @error('block_id') is-invalid @enderror"
                                               id="block_display"
                                               name="block_display"
                                               placeholder="Search and select a block..."
                                               autocomplete="off"
                                               required>
                                        <input type="hidden" id="block_id" name="block_id" value="{{ old('block_id') }}">
                                    </div>
                                    @error('block_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                
                                <!-- Row 1: Unit Selection, Assigned To -->

                                <div class="col-md-4 mb-3">
                                    <label for="block_unit_id" class="form-label">Unit Selection <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('block_unit_id') is-invalid @enderror" 
                                           id="block_unit_id" name="block_unit_display" placeholder="Select a block to search units..." autocomplete="off" required disabled>
                                    <input type="hidden" id="block_unit_id_hidden" name="block_unit_id" value="{{ old('block_unit_id') }}">
                                    @error('block_unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="assigned_to" class="form-label">Assigned To <span class="text-danger">*</span></label>
                                    <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to" required>
                                        <option value="">Select Property Manager</option>
                                        @foreach($users as $user)
                                            @if ($user->userType && $user->userType->name === 'Property manager')
                                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Row 2: Single row with Contact Method, Priority, Email -->
                                <div class="col-md-4 mb-3">
                                    <label for="contact_method_id" class="form-label">Contact Method <span class="text-danger">*</span></label>
                                    <select class="form-select @error('contact_method_id') is-invalid @enderror" id="contact_method_id" name="contact_method_id" required>
                                        <option value="">Select Contact Method</option>
                                        <option value="1" {{ old('contact_method_id') == '1' ? 'selected' : '' }}>Phone</option>
                                        <option value="2" {{ old('contact_method_id') == '2' ? 'selected' : '' }}>Email</option>
                                        <option value="3" {{ old('contact_method_id') == '3' ? 'selected' : '' }}>SMS</option>
                                        <option value="4" {{ old('contact_method_id') == '4' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="5" {{ old('contact_method_id') == '5' ? 'selected' : '' }}>In Person</option>
                                    </select>
                                    @error('contact_method_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority_id') is-invalid @enderror" id="priority_id" name="priority_id" required>
                                        <option value="">Select Priority</option>
                                        <option value="1" {{ old('priority_id') == '1' ? 'selected' : '' }}>Low</option>
                                        <option value="2" {{ old('priority_id') == '2' ? 'selected' : '' }} selected>Normal</option>
                                        <option value="3" {{ old('priority_id') == '3' ? 'selected' : '' }}>High</option>
                                        <option value="4" {{ old('priority_id') == '4' ? 'selected' : '' }}>Urgent</option>
                                        <option value="5" {{ old('priority_id') == '5' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('priority_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="contact_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                           id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required>
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Row 3: Left column (Category), Right two columns (Problem Overview and Issue Details) -->
                                <div class="col-md-4 mb-3">
                                    <label for="issue_type" class="form-label">{{ __('translation.issue-category') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('issue_type') is-invalid @enderror" id="issue_type" name="issue_type" required>
                                        <option value="">{{ __('translation.select-issue-category') }}</option>
                                        <option value="plumbing" {{ old('issue_type') == 'plumbing' ? 'selected' : '' }}>Plumbing</option>
                                        <option value="electrical" {{ old('issue_type') == 'electrical' ? 'selected' : '' }}>Electrical</option>
                                        <option value="hvac" {{ old('issue_type') == 'hvac' ? 'selected' : '' }}>HVAC</option>
                                        <option value="structural" {{ old('issue_type') == 'structural' ? 'selected' : '' }}>Structural</option>
                                        <option value="security" {{ old('issue_type') == 'security' ? 'selected' : '' }}>Security</option>
                                        <option value="fire_safety" {{ old('issue_type') == 'fire_safety' ? 'selected' : '' }}>Fire Safety</option>
                                        <option value="water_leakage" {{ old('issue_type') == 'water_leakage' ? 'selected' : '' }}>Water Leakage</option>
                                        <option value="noise" {{ old('issue_type') == 'noise' ? 'selected' : '' }}>Noise Complaint</option>
                                        <option value="parking" {{ old('issue_type') == 'parking' ? 'selected' : '' }}>Parking Issue</option>
                                        <option value="landscaping" {{ old('landscaping') == 'landscaping' ? 'selected' : '' }}>Landscaping</option>
                                        <option value="elevator" {{ old('elevator') == 'elevator' ? 'selected' : '' }}>Elevator</option>
                                        <option value="internet" {{ old('internet') == 'internet' ? 'selected' : '' }}>Internet</option>
                                        <option value="trash" {{ old('trash') == 'trash' ? 'selected' : '' }}>Trash Collection</option>
                                        <option value="lighting" {{ old('lighting') == 'lighting' ? 'selected' : '' }}>Lighting</option>
                                        <option value="access_control" {{ old('access_control') == 'access_control' ? 'selected' : '' }}>Access Control</option>
                                    </select>
                                    @error('issue_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                    <label for="issue" class="form-label">{{ __('translation.problem-overview') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('issue') is-invalid @enderror" 
                                           id="issue" name="issue" value="{{ old('issue') }}" required>
                                    @error('issue')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                </div>

                                <!-- Row 4: Issue Details spanning full row -->
                                <div class="col-12">
                                    <div class="mb-3">
                                    <label for="issue_details" class="form-label">Issue Details</label>
                                    <textarea class="form-control @error('issue_details') is-invalid @enderror" 
                                                  id="issue_details" name="issue_details" rows="4" placeholder="Describe the issue in detail...">{{ old('issue_details') }}</textarea>
                                    @error('issue_details')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>

                                <!-- Hidden contact_details to satisfy backend validation; synced from default_contact_details -->
                                <input type="hidden" id="contact_details_hidden" name="contact_details" value="{{ old('contact_details') }}">
                                
                                <!-- Row 4: Default Contact Details -->
                                <div class="col-12 mb-3">
                                    <label for="default_contact_details" class="form-label">Default Contact Details</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="use_default_contact" checked>
                                        <label class="form-check-label" for="use_default_contact">
                                            Use default contact details
                                        </label>
                                    </div>
                                    <textarea class="form-control @error('default_contact_details') is-invalid @enderror" 
                                              id="default_contact_details" name="default_contact_details" rows="2" placeholder="Enter default contact information..." readonly>{{ old('default_contact_details') }}</textarea>
                                    @error('default_contact_details')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Row 5: File Upload -->
                                <div class="col-12 mb-3">
                                    <label for="images" class="form-label">Upload Images</label>
                                    <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                           id="images" name="images[]" multiple accept="image/*">
                                    <small class="form-text text-muted">You can select multiple images. Maximum file size: 2MB each.</small>
                                    @error('images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Additional Details -->
                                <div class="col-12">
                                    <h5 class="mb-3">Additional Details</h5>
                                    


                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                                                <a href="{{ route('block-issues.index') }}" class="btn btn-secondary">
                            <i class="ph-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-floppy-disk me-1"></i> Create Block Issue
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

@section('script')
<style>
/* Custom styling for autocomplete dropdowns to match Bootstrap form design */
.autoComplete_wrapper {
    position: relative;
    display: block;
    width: 100%;
}

.autoComplete_wrapper > input {
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.autoComplete_wrapper > input:focus {
    color: #495057;
    background-color: #fff;
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.autoComplete_wrapper > ul {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    margin: 0;
    padding: 0;
    list-style: none;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.25rem 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    max-height: 200px;
    overflow-y: auto;
}

.autoComplete_wrapper > ul > li {
    padding: 0.375rem 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
}

.autoComplete_wrapper > ul > li:last-child {
    border-bottom: none;
}

.autoComplete_wrapper > ul > li:hover,
.autoComplete_wrapper > ul > li[aria-selected="true"] {
    background-color: #e9ecef;
    color: #495057;
}

.autoComplete_wrapper > ul > li mark {
    background-color: #fff3cd;
    color: #495057;
    padding: 0;
}

/* Hide the lens icon in autocomplete */
.autoComplete_wrapper > input::before {
    display: none !important;
}

.autoComplete_wrapper > input::-webkit-search-cancel-button,
.autoComplete_wrapper > input::-webkit-search-decoration,
.autoComplete_wrapper > input::-webkit-search-results-button,
.autoComplete_wrapper > input::-webkit-search-results-decoration {
    display: none !important;
}
</style>

<script>

// --- Searchable Block selector (autoComplete) ---
let blockAutoComplete = null;
let unitAutoCompleteInstance = null;
let cachedUnitsForBlock = [];

function initBlockAutoComplete(blocksData) {
    if (blockAutoComplete && typeof blockAutoComplete.unInit === 'function') {
        try { blockAutoComplete.unInit(); } catch(e) {}
        blockAutoComplete = null;
    }
    blockAutoComplete = new autoComplete({
        selector: () => document.getElementById("block_display"),
        placeHolder: "Search and select a block...",
    data: {
            src: blocksData.map(function(b){
                return {
                    id: b.id,
                    name: b.name,
                    searchValue: (b.name || '').toLowerCase()
                };
            }),
            keys: ["searchValue"]
        },
        resultItem: { highlight: false, element: (item, data) => { item.innerHTML = data.value.name || ''; } },
    events: {
        input: {
            selection: (event) => {
                const selection = event.detail.selection.value;
                    document.getElementById("block_display").value = selection.name;
                    document.getElementById("block_id").value = selection.id;
                    
                    // Enable and reset unit field
                    const unitInput = document.getElementById("block_unit_id");
                    const unitHidden = document.getElementById("block_unit_id_hidden");
                    unitInput.value = '';
                    unitHidden.value = '';
                    unitInput.placeholder = "Search for units...";
                    unitInput.disabled = false;
                    
                    // Load units for selected block
                    loadUnitsForBlock(selection.id);
                }
            }
        },
        threshold: 1,
        debounce: 200,
        searchEngine: function (query, record) {
            if (!record) return 0;
            return record.toLowerCase().includes((query || '').toLowerCase()) ? 1 : 0;
        },
        maxResults: 10
    });
}

function loadBlocksAndInit() {
    fetch(`{{ route('api.blocks') }}`)
        .then(resp => resp.json())
        .then(json => {
            const blocks = json && json.success ? (json.data || []) : [];
            initBlockAutoComplete(blocks);
            // If old value exists, try to prefill display
            const oldBlockId = document.getElementById('block_id').value;
            if (oldBlockId) {
                const found = blocks.find(b => String(b.id) === String(oldBlockId));
                if (found) {
                    document.getElementById("block_display").value = found.name || '';
                    // Also load units for that block
                    loadUnitsForBlock(found.id);
                }
            }
        })
        .catch(() => initBlockAutoComplete([]));
}

// --- Units autocomplete scoped to selected block ---
function initUnitAutoComplete(units) {
    // Destroy existing
    if (unitAutoCompleteInstance && typeof unitAutoCompleteInstance.unInit === 'function') {
        try { unitAutoCompleteInstance.unInit(); } catch (e) {}
        unitAutoCompleteInstance = null;
    }
    const mapped = (units || []).map(function(u){
        const labelParts = [];
        if (u.unit_code) labelParts.push(u.unit_code);
        if (u.unit_name && u.unit_name !== u.unit_code) labelParts.push(u.unit_name);
        const label = labelParts.length ? labelParts.join(' - ') : `Unit #${u.id}`;
        const searchValue = [u.unit_code, u.unit_name].filter(Boolean).join(' ').toLowerCase();
        return { id: u.id, label: label, searchValue: searchValue };
    });
    unitAutoCompleteInstance = new autoComplete({
        selector: () => document.getElementById("block_unit_id"),
        placeHolder: "Search for units...",
        data: { src: mapped, keys: ["searchValue"] },
        resultItem: { highlight: false, element: (item, data) => { item.innerHTML = data.value.label || ''; } },
    events: {
        input: {
            selection: (event) => {
                const selection = event.detail.selection.value;
                    document.getElementById("block_unit_id").value = selection.label;
                    document.getElementById("block_unit_id_hidden").value = selection.id;
                    // Populate contact details from unit
                    getUnitContactDetails(selection.id);
                }
            }
        },
        threshold: 1,
        debounce: 200,
        searchEngine: function (query, record) {
            if (!record) return 0;
            return record.toLowerCase().includes((query || '').toLowerCase()) ? 1 : 0;
        },
        maxResults: 10
    });
}

function loadUnitsForBlock(blockId) {
    const unitInput = document.getElementById("block_unit_id");
    unitInput.value = '';
                    document.getElementById("block_unit_id_hidden").value = '';
    unitInput.placeholder = 'Loading units...';
    unitInput.disabled = true;
    
    fetch(`/block-units/block/${blockId}`)
        .then(resp => resp.json())
        .then(json => {
            if (json && json.success) {
                cachedUnitsForBlock = json.data || [];
            } else {
                cachedUnitsForBlock = [];
            }
            initUnitAutoComplete(cachedUnitsForBlock);
            unitInput.placeholder = 'Search for units...';
            unitInput.disabled = false;
            
            // Prefill if old unit selected
            const oldUnitId = document.getElementById('block_unit_id_hidden').value;
            if (oldUnitId) {
                const found = cachedUnitsForBlock.find(u => String(u.id) === String(oldUnitId));
                if (found) {
                    const labelParts = [];
                    if (found.unit_code) labelParts.push(found.unit_code);
                    if (found.unit_name && found.unit_name !== found.unit_code) labelParts.push(found.unit_name);
                    unitInput.value = labelParts.length ? labelParts.join(' - ') : `Unit #${found.id}`;
                    // Load contact details for pre-selected unit
                    getUnitContactDetails(found.id);
                }
            }
        })
        .catch(() => {
            cachedUnitsForBlock = [];
            initUnitAutoComplete(cachedUnitsForBlock);
            unitInput.placeholder = 'No units found';
            unitInput.disabled = false;
        });
}

/**
 * Get unit contact details and populate default contact details
 * Similar to the function in block edit tab's create issue popup
 */
function getUnitContactDetails(unitId) {
    if (!unitId) return;
    
    // Only populate if use_default_contact is checked
    const useDefaultCheckbox = document.getElementById('use_default_contact');
    if (!useDefaultCheckbox || !useDefaultCheckbox.checked) {
        return;
    }
    
    fetch('/api/block-unit-contact-details?block_unit_id=' + unitId, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            const unit = data.data;
            const defaultDetails = document.getElementById('default_contact_details');
            const hiddenContact = document.getElementById('contact_details_hidden');
            
            // Use the formatted contact_details string from the API
            if (unit.contact_details) {
                if (defaultDetails) {
                    defaultDetails.value = unit.contact_details;
                }
                if (hiddenContact) {
                    hiddenContact.value = unit.contact_details;
                }
            } else {
                // Fallback: build contact details string if API doesn't provide formatted string
                let contactDetails = '';
                if (unit.mobile_no) {
                    contactDetails += `Mobile: ${unit.mobile_no}`;
                }
                if (unit.phone_number) {
                    contactDetails += contactDetails ? `\nPhone: ${unit.phone_number}` : `Phone: ${unit.phone_number}`;
                }
                if (unit.email) {
                    contactDetails += contactDetails ? `\nEmail: ${unit.email}` : `Email: ${unit.email}`;
                }
                if (unit.owners_name) {
                    contactDetails += contactDetails ? `\nOwner: ${unit.owners_name}` : `Owner: ${unit.owners_name}`;
                }
                
                if (defaultDetails) {
                    defaultDetails.value = contactDetails;
                }
                if (hiddenContact) {
                    hiddenContact.value = contactDetails;
                }
            }
        }
    })
    .catch(error => {
        console.error('Error loading unit contact details:', error);
    });
}

// Default contact details checkbox
document.getElementById('use_default_contact').addEventListener('change', function() {
    const textarea = document.getElementById('default_contact_details');
    textarea.readOnly = this.checked;
    if (this.checked) {
        // If a unit is selected, reload its contact details
        const unitId = document.getElementById('block_unit_id_hidden').value;
        if (unitId) {
            getUnitContactDetails(unitId);
        } else {
            textarea.value = 'Default contact information will be used';
        }
    } else {
        textarea.value = '';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadBlocksAndInit();
    
    // Keep hidden contact_details in sync when default contact details change manually
    const defaultDetails = document.getElementById('default_contact_details');
    if (defaultDetails) {
        defaultDetails.addEventListener('input', function() {
            const hiddenContact = document.getElementById('contact_details_hidden');
            if (hiddenContact) {
                hiddenContact.value = this.value || '';
            }
        });
    }
});
</script>
@endsection 