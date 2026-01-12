
<!-- Work Order Modal (Create/Edit) -->
<div class="modal fade" id="createWorkOrderModal" tabindex="-1" aria-labelledby="createWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 95vw;">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="createWorkOrderModalLabel">Create Work Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createWorkOrderForm" method="POST" action="{{ route('block-work-orders.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="work_order_method" value="POST">
                <input type="hidden" name="block_id" id="work_order_block_id" value="{{ $block->id }}">
                <input type="hidden" name="block_issue_id" id="work_order_block_issue_id">
                <input type="hidden" name="block_unit_id" id="work_order_block_unit_id">
                <input type="hidden" name="block_building_id" id="work_order_block_building_id">
                <input type="hidden" name="issued_by" value="{{ auth()->id() }}">
                <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                <input type="hidden" name="updated_by" value="{{ auth()->id() }}">
                
                <div class="modal-body">
                    <div id="createWorkOrderMessage" class="alert d-none" role="alert"></div>
                    
                    <!-- Row 1: Work Order Type, Conditional Dropdown, Priority -->
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="work_order_type" class="form-label">Work Order Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="work_order_type" name="work_order_type" required>
                                <option value="outsource" selected>Outsource</option>
                                <option value="inhouse">In House</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div id="contractCompanyFieldContainer">
                                <label for="work_order_contract_company_id" class="form-label">Contract Company <span class="text-danger">*</span></label>
                                <select class="form-select" id="work_order_contract_company_id" name="contract_company_id" required>
                                    <option value="">Select Contract Company</option>
                                    @if(isset($contractCompanies) && $contractCompanies->count() > 0)
                                        @foreach($contractCompanies as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div id="propertyManagerFieldContainer" style="display: none;">
                                <label for="work_order_property_manager_id" class="form-label">Property Manager <span class="text-danger">*</span></label>
                                <select class="form-select" id="work_order_property_manager_id" name="property_manager_id">
                                    <option value="">Select Property Manager</option>
                                    @if(isset($propertyManagers) && $propertyManagers->count() > 0)
                                        @foreach($propertyManagers as $manager)
                                            <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->email }})</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="work_order_priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="work_order_priority_id" name="priority_id" required>
                                <option value="">Select Priority</option>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->value }}">{{ $priority->label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Row 2: Unit, Issue -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="work_order_unit_id" class="form-label">Select Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="work_order_unit_id" name="work_order_unit_id" required>
                                <option value="">Select Unit</option>
                                @if(isset($block) && $block->units)
                                    @foreach($block->units as $unit)
                                        <option value="{{ $unit->id }}" 
                                                data-building-id="{{ $unit->block_building_id ?? '' }}">
                                            @if($unit->unit_code){{ $unit->unit_code }}@endif
                                            @if($unit->unit_name && $unit->unit_name !== $unit->unit_code) - {{ $unit->unit_name }}@endif
                                            @if($unit->unitType) ({{ $unit->unitType->name }})@endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Select a unit from the block</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="block_issue_id_select" class="form-label">Select Issue <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_issue_id_select" name="block_issue_id" required disabled>
                                <option value="">Select a unit first to see issues</option>
                            </select>
                            <small class="text-muted">Select an issue from the selected unit</small>
                        </div>
                    </div>
                    
                    <!-- Hidden field for status (auto-managed by backend) -->
                    <input type="hidden" id="work_order_status" name="status" value="1">

                    <div id="workOrderFormFields">
                        <!-- Row 3: Scheduling -->
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label for="work_order_preferred_start" class="form-label">Preferred Start Date/Time</label>
                                <input type="datetime-local" class="form-control" id="work_order_preferred_start" name="preferred_start_date_time">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="work_order_preferred_end" class="form-label">Preferred End Date/Time</label>
                                <input type="datetime-local" class="form-control" id="work_order_preferred_end" name="preferred_end_date_time">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="work_order_deadline" class="form-label">Deadline Date</label>
                                <input type="date" class="form-control" id="work_order_deadline" name="deadline_date">
                            </div>
                        </div>
                        
                        <!-- Row 4: Comments -->
                        <div class="row mb-3">
                            <div class="col-md-12 mb-3">
                                <label for="work_order_comment" class="form-label">Comments</label>
                                <textarea class="form-control" id="work_order_comment" name="comment" rows="4" placeholder="Enter comments..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="workOrderSubmitBtn">
                        <i class="ph-check me-1"></i> Create Work Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete/Archive Work Order Modal -->
<div class="modal fade" id="deleteWorkOrderModal" tabindex="-1" aria-labelledby="deleteWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white" id="deleteWorkOrderModalHeader">
                <h5 class="modal-title" id="deleteWorkOrderModalLabel">
                    <i class="ph-warning me-2"></i>Confirm Action
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="deleteWorkOrderDetails">
                    <!-- Details will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteWorkOrderBtn">
                    <i class="ph-trash me-1"></i> Delete Work Order
                </button>
            </div>
        </div>
    </div>
</div>

