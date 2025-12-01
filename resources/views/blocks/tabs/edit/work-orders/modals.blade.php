
<!-- Create Work Order Modal -->
<div class="modal fade" id="createWorkOrderModal" tabindex="-1" aria-labelledby="createWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 95vw;">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="createWorkOrderModalLabel">Create Work Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createWorkOrderForm" method="POST" action="{{ route('block-work-orders.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="block_id" id="work_order_block_id" value="{{ $block->id }}">
                <input type="hidden" name="block_issue_id" id="work_order_block_issue_id">
                <input type="hidden" name="block_unit_id" id="work_order_block_unit_id">
                <input type="hidden" name="block_building_id" id="work_order_block_building_id">
                <input type="hidden" name="issued_by" value="{{ auth()->id() }}">
                <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                <input type="hidden" name="updated_by" value="{{ auth()->id() }}">
                
                <div class="modal-body">
                    <div id="createWorkOrderMessage" class="alert d-none" role="alert"></div>
                    
                    <!-- Unit and Issue Selection -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label for="work_order_unit_id" class="form-label">Select Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="work_order_unit_id" name="block_unit_id" required>
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
                            <input type="hidden" id="work_order_block_unit_id" name="block_unit_id">
                            <input type="hidden" id="work_order_block_building_id" name="block_building_id">
                            <small class="text-muted">Select a unit from the block</small>
                        </div>
                        
                        <div class="col-md-8 mb-3">
                            <label for="block_issue_id_select" class="form-label">Select Issue <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_issue_id_select" name="block_issue_id" required>
                                <option value="">Select Issue</option>
                                @if(isset($block) && $block->issues)
                                    @foreach($block->issues as $issue)
                                        <option value="{{ $issue->id }}">
                                            {{ $issue->ref_no }} - {{ Str::limit($issue->issue ?? 'N/A', 50) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Select an issue from the block</small>
                        </div>
                    </div>

                    <div id="workOrderFormFields">
                        <div class="row">
                            <!-- Column 1: Basic Information -->
                            <div class="col-md-4">
                                <div class="work-order-section">
                                    <h6 class="mb-3">Basic Information</h6>

                                    <div class="mb-3">
                                        <label for="work_order_priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                        <select class="form-select" id="work_order_priority_id" name="priority_id" required>
                                            <option value="">Select Priority</option>
                                            @foreach ($priorities as $priority)
                                                <option value="{{ $priority->value }}">{{ $priority->label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="work_order_status" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="1" selected>Pending</option>
                                            <option value="2">In Progress</option>
                                            <option value="3">Completed</option>
                                            <option value="4">Cancelled</option>
                                            <option value="5">On Hold</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Column 2: Contact Information -->
                            <div class="col-md-4">
                                <div class="work-order-section">
                                    <h6 class="mb-3">Contact Information</h6>

                                    <div class="mb-3">
                                        <label for="work_order_contact_name" class="form-label">Contact Name</label>
                                        <input type="text" class="form-control" id="work_order_contact_name" name="contact_name" placeholder="Enter contact name">
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_contact_mobile" class="form-label">Contact Mobile</label>
                                        <input type="text" class="form-control" id="work_order_contact_mobile" name="contact_mobile" placeholder="Enter contact mobile">
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_contact_email" class="form-label">Contact Email</label>
                                        <input type="email" class="form-control" id="work_order_contact_email" name="contact_email" placeholder="Enter contact email">
                                    </div>
                                </div>
                            </div>

                            <!-- Column 3: Scheduling -->
                            <div class="col-md-4">
                                <div class="work-order-section">
                                    <h6 class="mb-3">Scheduling</h6>

                                    <div class="mb-3">
                                        <label for="work_order_preferred_start" class="form-label">Preferred Start Date/Time</label>
                                        <input type="datetime-local" class="form-control" id="work_order_preferred_start" name="preferred_start_date_time">
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_preferred_end" class="form-label">Preferred End Date/Time</label>
                                        <input type="datetime-local" class="form-control" id="work_order_preferred_end" name="preferred_end_date_time">
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_deadline" class="form-label">Deadline Date</label>
                                        <input type="date" class="form-control" id="work_order_deadline" name="deadline_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Comments Section -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="work_order_comment" class="form-label">Comments</label>
                                    <textarea class="form-control" id="work_order_comment" name="comment" rows="4" placeholder="Enter comments..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="createWorkOrderSubmitBtn">
                        <i class="ph-check me-1"></i> Create Work Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Work Order Modal -->
<div class="modal fade" id="editWorkOrderModal" tabindex="-1" aria-labelledby="editWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 95vw;">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="editWorkOrderModalLabel">Edit Work Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editWorkOrderForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="block_id" id="edit_work_order_block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div id="editWorkOrderMessage" class="alert d-none" role="alert"></div>
                    
                    <!-- Unit and Issue Selection -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label for="edit_work_order_unit_id" class="form-label">Select Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_work_order_unit_id" name="block_unit_id" required>
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
                            <input type="hidden" id="edit_work_order_block_unit_id" name="block_unit_id">
                            <input type="hidden" id="edit_work_order_block_building_id" name="block_building_id">
                            <small class="text-muted">Select a unit from the block</small>
                        </div>
                        
                        <div class="col-md-8 mb-3">
                            <label for="edit_block_issue_id_select" class="form-label">Select Issue <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_block_issue_id_select" name="block_issue_id" required>
                                <option value="">Select Issue</option>
                                @if(isset($block) && $block->issues)
                                    @foreach($block->issues as $issue)
                                        <option value="{{ $issue->id }}">
                                            {{ $issue->ref_no }} - {{ Str::limit($issue->issue ?? 'N/A', 50) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Select an issue from the block</small>
                        </div>
                    </div>

                    <div id="editWorkOrderFormFields">
                        <div class="row">
                            <!-- Column 1: Basic Information -->
                            <div class="col-md-4">
                                <div class="work-order-section">
                                    <h6 class="mb-3">Basic Information</h6>

                                    <div class="mb-3">
                                        <label for="edit_priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_priority_id" name="priority_id" required>
                                            <option value="">Select Priority</option>
                                            @foreach ($priorities as $priority)
                                                <option value="{{ $priority->value }}">{{ $priority->label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_status" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="1">Pending</option>
                                            <option value="2">In Progress</option>
                                            <option value="3">Completed</option>
                                            <option value="4">Cancelled</option>
                                            <option value="5">On Hold</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Column 2: Contact Information -->
                            <div class="col-md-4">
                                <div class="work-order-section">
                                    <h6 class="mb-3">Contact Information</h6>

                                    <div class="mb-3">
                                        <label for="edit_contact_name" class="form-label">Contact Name</label>
                                        <input type="text" class="form-control" id="edit_contact_name" name="contact_name" placeholder="Enter contact name">
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_contact_mobile" class="form-label">Contact Mobile</label>
                                        <input type="text" class="form-control" id="edit_contact_mobile" name="contact_mobile" placeholder="Enter contact mobile">
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_contact_email" class="form-label">Contact Email</label>
                                        <input type="email" class="form-control" id="edit_contact_email" name="contact_email" placeholder="Enter contact email">
                                    </div>
                                </div>
                            </div>

                            <!-- Column 3: Scheduling -->
                            <div class="col-md-4">
                                <div class="work-order-section">
                                    <h6 class="mb-3">Scheduling</h6>

                                    <div class="mb-3">
                                        <label for="edit_preferred_start_date_time" class="form-label">Preferred Start Date/Time</label>
                                        <input type="datetime-local" class="form-control" id="edit_preferred_start_date_time" name="preferred_start_date_time">
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_preferred_end_date_time" class="form-label">Preferred End Date/Time</label>
                                        <input type="datetime-local" class="form-control" id="edit_preferred_end_date_time" name="preferred_end_date_time">
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_deadline_date" class="form-label">Deadline Date</label>
                                        <input type="date" class="form-control" id="edit_deadline_date" name="deadline_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Comments Section -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="edit_comment" class="form-label">Comments</label>
                                    <textarea class="form-control" id="edit_comment" name="comment" rows="4" placeholder="Enter comments..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Update Work Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Work Order Modal -->
<div class="modal fade" id="deleteWorkOrderModal" tabindex="-1" aria-labelledby="deleteWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteWorkOrderModalLabel">
                    <i class="ph-warning me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="ph-warning-circle text-danger" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Are you sure you want to delete this work order?</h6>
                        <p class="text-muted mb-0">This action cannot be undone. The work order record will be permanently removed.</p>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <strong>Work Order Details:</strong>
                    <div id="deleteWorkOrderDetails" class="mt-2">
                        <!-- Details will be populated by JavaScript -->
                    </div>
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
