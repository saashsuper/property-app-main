@extends('layouts.master')
@section('title')
    Create Block Work Order - PROMAN
@endsection
@section('css')
    <style>
    .autoComplete_wrapper {
        position: relative;
    }
    .autoComplete_wrapper > ul {
        z-index: 2050 !important; /* above modals/dropdowns */
        max-height: 240px;
        overflow-y: auto;
    }
    .autoComplete_wrapper > ul > li mark {
        background-color: #fff3cd;
        color: #495057;
        padding: 0;
    }
    </style>
@endsection
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Create Block Work Order</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-work-orders.index') }}">Block Work Orders</a></li>
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
                        <h4 class="card-title">Create New Block Work Order</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('block-work-orders.store') }}" method="POST" enctype="multipart/form-data" id="workOrderForm">
                            @csrf
                            
                            <div id="workOrderMessage" class="alert d-none mb-3" role="alert"></div>
                            
                            <!-- Row 1: Work Order Type, Contractor Assignment, Priority -->
                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <label for="work_order_type" class="form-label">Work Order Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('work_order_type') is-invalid @enderror" id="work_order_type" name="work_order_type" required>
                                        <option value="outsource" {{ old('work_order_type', 'outsource') == 'outsource' ? 'selected' : '' }}>Outsource</option>
                                        <option value="inhouse" {{ old('work_order_type') == 'inhouse' ? 'selected' : '' }}>In House</option>
                                    </select>
                                    @error('work_order_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div id="contractCompanyFieldContainer">
                                        <label for="work_order_contract_company_id" class="form-label">Contract Company <span class="text-danger">*</span></label>
                                        <select class="form-select @error('contract_company_id') is-invalid @enderror" id="work_order_contract_company_id" name="contract_company_id">
                                            <option value="">Select Contract Company</option>
                                            @if(isset($contractCompanies) && $contractCompanies->count() > 0)
                                                @foreach($contractCompanies as $company)
                                                    <option value="{{ $company->id }}" {{ old('contract_company_id') == $company->id ? 'selected' : '' }}>
                                                        {{ $company->company_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('contract_company_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div id="propertyManagerFieldContainer" style="display: none;">
                                        <label for="work_order_property_manager_id" class="form-label">Property Manager <span class="text-danger">*</span></label>
                                        <select class="form-select @error('property_manager_id') is-invalid @enderror" id="work_order_property_manager_id" name="property_manager_id">
                                            <option value="">Select Property Manager</option>
                                            @if(isset($propertyManagers) && $propertyManagers->count() > 0)
                                                @foreach($propertyManagers as $manager)
                                                    <option value="{{ $manager->id }}" {{ old('property_manager_id') == $manager->id ? 'selected' : '' }}>
                                                        {{ $manager->name }} ({{ $manager->email }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('property_manager_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority_id') is-invalid @enderror" id="priority_id" name="priority_id" required>
                                        <option value="">Select Priority</option>
                                        <option value="1" {{ old('priority_id') == '1' ? 'selected' : '' }}>Low</option>
                                        <option value="2" {{ old('priority_id') == '2' ? 'selected' : '' }}>Normal</option>
                                        <option value="3" {{ old('priority_id') == '3' ? 'selected' : '' }}>High</option>
                                        <option value="4" {{ old('priority_id') == '4' ? 'selected' : '' }}>Urgent</option>
                                        <option value="5" {{ old('priority_id') == '5' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('priority_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Row 2: Block, Unit, Issue -->
                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <label for="block_id" class="form-label">Block <span class="text-danger">*</span></label>
                                    <select class="form-select @error('block_id') is-invalid @enderror" id="block_id" name="block_id" required>
                                        <option value="">Select Block</option>
                                        @foreach($blocks as $block)
                                            <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                                {{ $block->name }} - {{ $block->blockType->name ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('block_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="work_order_unit_id" class="form-label">Select Unit <span class="text-danger">*</span></label>
                                    <select class="form-select @error('block_unit_id') is-invalid @enderror" id="work_order_unit_id" name="block_unit_id" required>
                                        <option value="">Select Unit</option>
                                    </select>
                                    <small class="text-muted">Select a unit from the block</small>
                                    @error('block_unit_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="block_issue_id_select" class="form-label">Select Issue <span class="text-danger">*</span></label>
                                    <select class="form-select @error('block_issue_id') is-invalid @enderror" id="block_issue_id_select" name="block_issue_id" required disabled>
                                        <option value="">Select a unit first to see issues</option>
                                    </select>
                                    <small class="text-muted">Select an issue from the selected unit</small>
                                    @error('block_issue_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Hidden field for block_building_id (auto-populated from issue) -->
                            <input type="hidden" id="block_building_id" name="block_building_id">
                            
                            <!-- Row 3: Scheduling -->
                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <label for="preferred_start_date_time" class="form-label">Preferred Start Date/Time</label>
                                    <input type="datetime-local" class="form-control @error('preferred_start_date_time') is-invalid @enderror" 
                                           id="preferred_start_date_time" name="preferred_start_date_time" value="{{ old('preferred_start_date_time') }}">
                                    @error('preferred_start_date_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="preferred_end_date_time" class="form-label">Preferred End Date/Time</label>
                                    <input type="datetime-local" class="form-control @error('preferred_end_date_time') is-invalid @enderror" 
                                           id="preferred_end_date_time" name="preferred_end_date_time" value="{{ old('preferred_end_date_time') }}">
                                    @error('preferred_end_date_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="deadline_date" class="form-label">Deadline Date</label>
                                    <input type="date" class="form-control @error('deadline_date') is-invalid @enderror" 
                                           id="deadline_date" name="deadline_date" value="{{ old('deadline_date') }}">
                                    @error('deadline_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Row 4: Comments -->
                            <div class="row mb-3">
                                <div class="col-md-12 mb-3">
                                    <label for="comment" class="form-label">Comments</label>
                                    <textarea class="form-control @error('comment') is-invalid @enderror" 
                                              id="comment" name="comment" rows="4" placeholder="Enter comments...">{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Hidden fields for contact information (auto-populated from issue) -->
                            <input type="hidden" id="contact_name" name="contact_name">
                            <input type="hidden" id="contact_mobile" name="contact_mobile">
                            <input type="hidden" id="contact_email" name="contact_email">
                            <input type="hidden" id="note_for_access" name="note_for_access">
                            <input type="hidden" id="issue" name="issue">

                            <!-- File Uploads -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="mb-3">PDF Document</h5>
                                        <label for="pdf" class="form-label">Upload PDF</label>
                                        <input type="file" class="form-control @error('pdf') is-invalid @enderror" 
                                               id="pdf" name="pdf" accept=".pdf">
                                        <div class="form-text">Maximum file size: 10MB</div>
                                        @error('pdf')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="mb-3">Images</h5>
                                        <label for="images" class="form-label">Upload Images</label>
                                        <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                               id="images" name="images[]" multiple accept="image/*">
                                        <div class="form-text">You can select multiple images. Maximum file size: 2MB each.</div>
                                        @error('images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('block-work-orders.index') }}" class="btn btn-secondary">
                            <i class="ph-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-floppy-disk me-1"></i> Create Block Work Order
                        </button>
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
    const workOrderTypeSelect = document.getElementById('work_order_type');
    const contractCompanyContainer = document.getElementById('contractCompanyFieldContainer');
    const propertyManagerContainer = document.getElementById('propertyManagerFieldContainer');
    const contractCompanySelect = document.getElementById('work_order_contract_company_id');
    const propertyManagerSelect = document.getElementById('work_order_property_manager_id');
    const blockSelect = document.getElementById('block_id');
    const unitSelect = document.getElementById('work_order_unit_id');
    const issueSelect = document.getElementById('block_issue_id_select');
    
    // Work Order Type Toggle
    function setupWorkOrderTypeToggle(preserveValue = false) {
        const selectedType = workOrderTypeSelect.value;
        
        if (selectedType === 'inhouse') {
            propertyManagerContainer.style.display = 'block';
            contractCompanyContainer.style.display = 'none';
            propertyManagerSelect.required = true;
            contractCompanySelect.required = false;
            if (!preserveValue) {
                contractCompanySelect.value = '';
            }
        } else {
            contractCompanyContainer.style.display = 'block';
            propertyManagerContainer.style.display = 'none';
            contractCompanySelect.required = true;
            propertyManagerSelect.required = false;
            if (!preserveValue) {
                propertyManagerSelect.value = '';
            }
        }
    }
    
    workOrderTypeSelect.addEventListener('change', function() {
        setupWorkOrderTypeToggle(false);
    });
    
    // Load units for selected block
    function loadUnitsForBlock(blockId) {
        if (!blockId) {
            unitSelect.innerHTML = '<option value="">Select Unit</option>';
            unitSelect.disabled = true;
            issueSelect.innerHTML = '<option value="">Select a unit first to see issues</option>';
            issueSelect.disabled = true;
            return;
        }
        
        unitSelect.disabled = true;
        unitSelect.innerHTML = '<option value="">Loading units...</option>';
        
        fetch(`/block-units/block/${blockId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            unitSelect.innerHTML = '<option value="">Select Unit</option>';
            if (data.data && data.data.length > 0) {
                data.data.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = (unit.unit_code || '') + (unit.unit_name && unit.unit_name !== unit.unit_code ? ' - ' + unit.unit_name : '') + (unit.unit_type ? ' (' + unit.unit_type.name + ')' : '');
                    option.setAttribute('data-building-id', unit.block_building_id || '');
                    unitSelect.appendChild(option);
                });
                unitSelect.disabled = false;
            } else {
                unitSelect.innerHTML = '<option value="">No units found for this block</option>';
            }
        })
        .catch(error => {
            console.error('Error loading units:', error);
            unitSelect.innerHTML = '<option value="">Error loading units</option>';
        });
    }
    
    // Load issues for selected unit
    function loadIssuesForUnit(unitId) {
        if (!unitId) {
            issueSelect.innerHTML = '<option value="">Select a unit first to see issues</option>';
            issueSelect.disabled = true;
            return;
        }
        
        issueSelect.disabled = true;
        issueSelect.innerHTML = '<option value="">Loading issues...</option>';
        
        fetch(`/api/block-unit-active-issues?unit_id=${unitId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(response => {
            issueSelect.innerHTML = '<option value="">Select Issue</option>';
            
            // Handle API response format: { success: true, data: [...] }
            const issues = (response && response.success && response.data) ? response.data : 
                         (Array.isArray(response) ? response : []);
            
            if (issues && issues.length > 0) {
                issues.forEach(issue => {
                    const option = document.createElement('option');
                    option.value = issue.id;
                    option.textContent = `${issue.ref_no} - ${issue.issue}`;
                    issueSelect.appendChild(option);
                });
                issueSelect.disabled = false;
            } else {
                issueSelect.innerHTML = '<option value="">No issues found for this unit</option>';
                issueSelect.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error loading issues:', error);
            issueSelect.innerHTML = '<option value="">Error loading issues</option>';
        });
    }
    
    // Block change handler
    blockSelect.addEventListener('change', function() {
        loadUnitsForBlock(this.value);
        issueSelect.innerHTML = '<option value="">Select a unit first to see issues</option>';
        issueSelect.disabled = true;
    });
    
    // Unit change handler
    unitSelect.addEventListener('change', function() {
        const unitId = this.value;

        // Auto-populate building ID from unit (if available)
        const selectedOption = this.options[this.selectedIndex];
        const buildingId = selectedOption ? selectedOption.getAttribute('data-building-id') : null;
        if (buildingId) {
            document.getElementById('block_building_id').value = buildingId;
        }

        if (!unitId) {
            issueSelect.innerHTML = '<option value="">Select a unit first to see issues</option>';
            issueSelect.disabled = true;
            return;
        }

        issueSelect.innerHTML = '<option value="">Loading issues...</option>';
        issueSelect.disabled = true;

        // Delay loading issues so unit selection is fully complete before fetching
        setTimeout(() => {
            loadIssuesForUnit(unitId);
        }, 600);
    });
    
    // Issue change handler - auto-populate building_id from issue
    issueSelect.addEventListener('change', function() {
        // Building ID will be populated from the issue by backend
        // But we can also try to get it from the selected unit
        const unitId = unitSelect.value;
        if (unitId) {
            const selectedUnitOption = unitSelect.options[unitSelect.selectedIndex];
            const buildingId = selectedUnitOption.getAttribute('data-building-id');
            if (buildingId) {
                document.getElementById('block_building_id').value = buildingId;
            }
        }
    });
    
    // Initialize on page load
    setupWorkOrderTypeToggle(true);
    
    // Load units for the current block if one is selected
    if (blockSelect.value) {
        loadUnitsForBlock(blockSelect.value);
    }
    
    // Form submission handler
    document.getElementById('workOrderForm').addEventListener('submit', function(e) {
        // Validate work order type assignment
        const selectedType = workOrderTypeSelect.value;
        if (selectedType === 'inhouse' && !propertyManagerSelect.value) {
            e.preventDefault();
            alert('Please select a Property Manager');
            return false;
        } else if (selectedType === 'outsource' && !contractCompanySelect.value) {
            e.preventDefault();
            alert('Please select a Contract Company');
            return false;
        }
    });
});
</script>
@endpush