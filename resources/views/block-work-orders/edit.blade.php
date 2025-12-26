@extends('layouts.master')
@section('title')
    Edit Block Work Order - PROMAN
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
                    <h4 class="mb-sm-0">Edit Block Work Order</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-work-orders.index') }}">Block Work Orders</a></li>
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
                        <h4 class="card-title">Edit Block Work Order: {{ $blockWorkOrder->ref_no }}</h4>
                    </div>
                    <div class="card-body">
                        @php
                            $isCompleted = $blockWorkOrder->status == 3;
                            $isAdmin = auth()->user()->isAdmin();
                        @endphp
                        
                        @if($isCompleted && !$isAdmin)
                            <div class="alert alert-warning">
                                <i class="ph-warning me-2"></i>
                                This work order has been completed and cannot be edited.
                            </div>
                        @elseif($isCompleted && $isAdmin)
                            <div class="alert alert-info">
                                <i class="ph-info me-2"></i>
                                <strong>Admin Edit Mode:</strong> You can update photos and notes for this completed work order. You can also regenerate the work docket after making changes.
                            </div>
                        @endif
                        
                        <form action="{{ route('block-work-orders.update', $blockWorkOrder) }}" method="POST" enctype="multipart/form-data" id="workOrderForm">
                            @csrf
                            @method('PUT')
                            
                            <div id="workOrderMessage" class="alert d-none mb-3" role="alert"></div>
                            
                            @if($isCompleted && $isAdmin)
                                <input type="hidden" name="regenerate_docket" id="regenerate_docket" value="0">
                            @endif
                            
                            @if($isCompleted && $isAdmin)
                                <!-- For completed work orders, only show photos and notes -->
                                <!-- Skip most fields, show only comment and images -->
                            @else
                            <!-- Row 1: Work Order Type, Contractor Assignment, Priority -->
                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <label for="work_order_type" class="form-label">Work Order Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('work_order_type') is-invalid @enderror" id="work_order_type" name="work_order_type" required @if($isCompleted && $isAdmin) disabled @endif>
                                        @php
                                            // Determine default work order type from existing contractor
                                            $defaultType = 'outsource';
                                            if ($blockWorkOrder->contractor && $blockWorkOrder->contractor->userType) {
                                                $defaultType = ($blockWorkOrder->contractor->userType->name === 'Property manager') ? 'inhouse' : 'outsource';
                                            }
                                        @endphp
                                        <option value="outsource" {{ old('work_order_type', $defaultType) == 'outsource' ? 'selected' : '' }}>Outsource</option>
                                        <option value="inhouse" {{ old('work_order_type', $defaultType) == 'inhouse' ? 'selected' : '' }}>In House</option>
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
                                                    @php
                                                        $isSelected = false;
                                                        if ($blockWorkOrder->contractor_id == $company->id) {
                                                            if (!$blockWorkOrder->contractor) {
                                                                $isSelected = true; // Contractor is null, assume it's a contract company
                                                            } elseif (!$blockWorkOrder->contractor->userType) {
                                                                $isSelected = true; // No user type, assume it's a contract company
                                                            } elseif ($blockWorkOrder->contractor->userType->name !== 'Property manager') {
                                                                $isSelected = true; // Not a property manager, so it's a contract company
                                                            }
                                                        }
                                                        $isSelected = old('contract_company_id', $isSelected ? $company->id : '') == $company->id;
                                                    @endphp
                                                    <option value="{{ $company->id }}" {{ $isSelected ? 'selected' : '' }}>
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
                                                    @php
                                                        $isSelected = false;
                                                        if ($blockWorkOrder->contractor_id == $manager->id) {
                                                            if ($blockWorkOrder->contractor && $blockWorkOrder->contractor->userType && $blockWorkOrder->contractor->userType->name === 'Property manager') {
                                                                $isSelected = true;
                                                            }
                                                        }
                                                        $isSelected = old('property_manager_id', $isSelected ? $manager->id : '') == $manager->id;
                                                    @endphp
                                                    <option value="{{ $manager->id }}" {{ $isSelected ? 'selected' : '' }}>
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
                                            <option value="1" {{ old('priority_id', $blockWorkOrder->priority_id) == '1' ? 'selected' : '' }}>Low</option>
                                            <option value="2" {{ old('priority_id', $blockWorkOrder->priority_id) == '2' ? 'selected' : '' }}>Normal</option>
                                            <option value="3" {{ old('priority_id', $blockWorkOrder->priority_id) == '3' ? 'selected' : '' }}>High</option>
                                            <option value="4" {{ old('priority_id', $blockWorkOrder->priority_id) == '4' ? 'selected' : '' }}>Urgent</option>
                                            <option value="5" {{ old('priority_id', $blockWorkOrder->priority_id) == '5' ? 'selected' : '' }}>Critical</option>
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
                                            <option value="{{ $block->id }}" {{ old('block_id', $blockWorkOrder->block_id) == $block->id ? 'selected' : '' }}>
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
                            
                            <!-- Hidden field for status (auto-managed by backend) -->
                            <input type="hidden" id="status" name="status" value="{{ $blockWorkOrder->status }}">

                            <!-- Reference Number (Read-only) -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="ref_no" class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" id="ref_no" value="{{ $blockWorkOrder->ref_no }}" readonly>
                                    <small class="text-muted">Auto-generated reference number</small>
                                </div>
                            </div>

                            <!-- Hidden field for block_building_id (auto-populated from issue) -->
                            <input type="hidden" id="block_building_id" name="block_building_id" value="{{ $blockWorkOrder->block_building_id }}">

                            <!-- Row 3: Scheduling -->
                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <label for="preferred_start_date_time" class="form-label">Preferred Start Date/Time</label>
                                        <input type="datetime-local" class="form-control @error('preferred_start_date_time') is-invalid @enderror" 
                                               id="preferred_start_date_time" name="preferred_start_date_time" 
                                               value="{{ old('preferred_start_date_time', $blockWorkOrder->preferred_start_date_time ? $blockWorkOrder->preferred_start_date_time->format('Y-m-d\TH:i') : '') }}">
                                        @error('preferred_start_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                <div class="col-md-4 mb-3">
                                    <label for="preferred_end_date_time" class="form-label">Preferred End Date/Time</label>
                                        <input type="datetime-local" class="form-control @error('preferred_end_date_time') is-invalid @enderror" 
                                               id="preferred_end_date_time" name="preferred_end_date_time" 
                                               value="{{ old('preferred_end_date_time', $blockWorkOrder->preferred_end_date_time ? $blockWorkOrder->preferred_end_date_time->format('Y-m-d\TH:i') : '') }}">
                                        @error('preferred_end_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                <div class="col-md-4 mb-3">
                                        <label for="deadline_date" class="form-label">Deadline Date</label>
                                        <input type="date" class="form-control @error('deadline_date') is-invalid @enderror" 
                                               id="deadline_date" name="deadline_date" 
                                               value="{{ old('deadline_date', $blockWorkOrder->deadline_date ? $blockWorkOrder->deadline_date->format('Y-m-d') : '') }}">
                                        @error('deadline_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <!-- Row 4: Comments -->
                            <div class="row mb-3">
                                <div class="col-md-12 mb-3">
                                    <label for="comment" class="form-label">Comments / Notes</label>
                                    <textarea class="form-control @error('comment') is-invalid @enderror" 
                                              id="comment" name="comment" rows="4" placeholder="Enter comments or notes...">{{ old('comment', $blockWorkOrder->comment) }}</textarea>
                                    @error('comment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @if($isCompleted && $isAdmin)
                                        <div class="form-text">You can update notes for this completed work order.</div>
                                    @endif
                                </div>
                            </div>
                            
                            @if($isCompleted && $isAdmin)
                            <!-- Regenerate Work Docket Option -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="regenerate_docket_checkbox" name="regenerate_docket_checkbox">
                                        <label class="form-check-label" for="regenerate_docket_checkbox">
                                            <strong>Regenerate Work Docket</strong> - Check this box to regenerate the work docket PDF after saving changes. The old PDF will be replaced.
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($isCompleted && $isAdmin)
                            @else
                            <!-- Hidden fields for contact information (auto-populated from issue) -->
                            <input type="hidden" id="contact_name" name="contact_name" value="{{ $blockWorkOrder->contact_name }}">
                            <input type="hidden" id="contact_mobile" name="contact_mobile" value="{{ $blockWorkOrder->contact_mobile }}">
                            <input type="hidden" id="contact_email" name="contact_email" value="{{ $blockWorkOrder->contact_email }}">
                            <input type="hidden" id="note_for_access" name="note_for_access" value="{{ $blockWorkOrder->note_for_access }}">
                            <input type="hidden" id="issue" name="issue" value="{{ $blockWorkOrder->issue }}">

                            <!-- Current Files -->
                            @if($blockWorkOrder->pdf_name)
                            <div class="mb-3">
                                <h5 class="mb-3">Current PDF</h5>
                                <div class="alert alert-info">
                                                                                    <i class="ph-file-pdf me-2"></i>
                                    <a href="{{ $blockWorkOrder->pdf_url }}" target="_blank">{{ $blockWorkOrder->pdf_name }}</a>
                                </div>
                            </div>
                            @endif

                            @if($blockWorkOrder->images->count() > 0)
                            <div class="mb-3">
                                <h5 class="mb-3">Current Images ({{ $blockWorkOrder->images->count() }})</h5>
                                <div class="row">
                                    @foreach($blockWorkOrder->images as $image)
                                    <div class="col-md-3 mb-2">
                                        <div class="card">
                                            <img src="{{ $image->image_url }}" class="card-img-top" alt="Work Order Image" style="height: 150px; object-fit: cover;">
                                            <div class="card-body p-2">
                                                <small class="text-muted">{{ $image->image_name }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- File Uploads -->
                            <div class="row">
                                @if(!($isCompleted && $isAdmin))
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="mb-3">PDF Document</h5>
                                        <label for="pdf" class="form-label">Upload New PDF</label>
                                        <input type="file" class="form-control @error('pdf') is-invalid @enderror" 
                                               id="pdf" name="pdf" accept=".pdf">
                                        <div class="form-text">Maximum file size: 10MB</div>
                                        @error('pdf')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <div class="{{ !($isCompleted && $isAdmin) ? 'col-md-6' : 'col-md-12' }}">
                                    <div class="mb-3">
                                        <h5 class="mb-3">Images</h5>
                                        <label for="images" class="form-label">Upload Additional Images</label>
                                        <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                               id="images" name="images[]" multiple accept="image/*">
                                        <div class="form-text">You can select multiple images. Maximum file size: 2MB each.</div>
                                        @error('images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($isCompleted && $isAdmin)
                                            <div class="form-text text-info">
                                                <i class="ph-info me-1"></i>You can add more photos to this completed work order.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('block-work-orders.index') }}" class="btn btn-secondary">
                            <i class="ph-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-floppy-disk me-1"></i> Update Block Work Order
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

@section('script')
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
                
                // If editing, try to select the existing unit and issue
                @if($blockWorkOrder->block_unit_id)
                const existingUnitId = {{ $blockWorkOrder->block_unit_id }};
                const existingIssueId = {{ $blockWorkOrder->block_issue_id ?? 'null' }};
                if (existingUnitId) {
                    // Wait a bit for the select to be ready, then set value
                    setTimeout(() => {
                        unitSelect.value = existingUnitId;
                        // Trigger change to update building ID
                        const event = new Event('change', { bubbles: true });
                        unitSelect.dispatchEvent(event);
                        // Load issues and select the existing issue
                        if (existingIssueId) {
                            loadIssuesForUnit(existingUnitId, existingIssueId);
                        } else {
                            loadIssuesForUnit(existingUnitId);
                        }
                    }, 100);
                }
                @endif
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
    function loadIssuesForUnit(unitId, selectIssueId = null) {
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
        .then(data => {
            issueSelect.innerHTML = '<option value="">Select Issue</option>';
            if (data && data.length > 0) {
                data.forEach(issue => {
                    const option = document.createElement('option');
                    option.value = issue.id;
                    option.textContent = `${issue.ref_no} - ${issue.issue}`;
                    issueSelect.appendChild(option);
                });
                issueSelect.disabled = false;
                
                // Select the issue if provided (for edit mode)
                if (selectIssueId) {
                    issueSelect.value = selectIssueId;
                }
            } else {
                issueSelect.innerHTML = '<option value="">No issues found for this unit</option>';
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
        loadIssuesForUnit(this.value);
        
        // Auto-populate building ID from unit (if available)
        const selectedOption = this.options[this.selectedIndex];
        const buildingId = selectedOption.getAttribute('data-building-id');
        if (buildingId) {
            document.getElementById('block_building_id').value = buildingId;
        }
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
    
    // Determine work order type from existing data and populate fields
    @if($blockWorkOrder->contractor && $blockWorkOrder->contractor->userType)
        @php
            $isPropertyManager = $blockWorkOrder->contractor->userType->name === 'Property manager';
            $contractorId = $blockWorkOrder->contractor_id;
        @endphp
        @if($isPropertyManager)
            // Set to inhouse mode
            workOrderTypeSelect.value = 'inhouse';
            setupWorkOrderTypeToggle(true);
            // Wait for toggle to complete, then set property manager value
            setTimeout(() => {
                propertyManagerSelect.value = {{ $contractorId }};
            }, 150);
        @else
            // Set to outsource mode
            workOrderTypeSelect.value = 'outsource';
            setupWorkOrderTypeToggle(true);
            // Wait for toggle to complete, then set contract company value
            setTimeout(() => {
                contractCompanySelect.value = {{ $contractorId }};
            }, 150);
        @endif
    @else
        // No contractor assigned, default to outsource
        workOrderTypeSelect.value = 'outsource';
        setupWorkOrderTypeToggle(true);
    @endif
    
    // Load units for the current block (this will also trigger unit/issue selection)
    if (blockSelect.value) {
        // Small delay to ensure all initialization is complete
        setTimeout(() => {
            loadUnitsForBlock(blockSelect.value);
        }, 200);
    }
    
    // Form submission handler
    document.getElementById('workOrderForm').addEventListener('submit', function(e) {
        @if($isCompleted && $isAdmin)
        // For completed work orders, handle regenerate docket checkbox
        const regenerateCheckbox = document.getElementById('regenerate_docket_checkbox');
        const regenerateInput = document.getElementById('regenerate_docket');
        if (regenerateCheckbox && regenerateInput) {
            regenerateInput.value = regenerateCheckbox.checked ? '1' : '0';
        }
        @else
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
        @endif
    });
});
</script>
@endsection 