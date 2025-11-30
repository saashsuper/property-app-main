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
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div id="createWorkOrderMessage" class="alert d-none" role="alert"></div>
                    
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            
                            <div class="mb-3">
                                <label for="block_issue_id" class="form-label">Related Issue <span class="text-danger">*</span></label>
                                <select class="form-select" id="block_issue_id" name="block_issue_id" required>
                                    <option value="">Select Issue</option>
                                    @foreach($block->issues as $issue)
                                        <option value="{{ $issue->id }}">
                                            {{ $issue->ref_no }} - {{ $issue->issue }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ref_no" class="form-label">Reference Number</label>
                                <input type="text" class="form-control" id="ref_no" name="ref_no" value="Auto-generated" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                <small class="text-muted">Reference number will be automatically generated upon creation</small>
                            </div>

                            <div class="mb-3">
                                <label for="priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select" id="priority_id" name="priority_id" required>
                                    <option value="">Select Priority</option>
                                    <option value="1">Low</option>
                                    <option value="2" selected>Normal</option>
                                    <option value="3">High</option>
                                    <option value="4">Urgent</option>
                                    <option value="5">Critical</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="1" selected>Pending</option>
                                    <option value="2">In Progress</option>
                                    <option value="3">Completed</option>
                                    <option value="4">Cancelled</option>
                                    <option value="5">On Hold</option>
                                </select>
                            </div>
                        </div>

                        <!-- Location & Assignment -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Location & Assignment</h5>
                            
                            <div class="mb-3">
                                <label for="block_unit_id" class="form-label">Block Unit</label>
                                <select class="form-select" id="block_unit_id" name="block_unit_id">
                                    <option value="">Select Unit (Optional)</option>
                                    @foreach($block->units as $unit)
                                        <option value="{{ $unit->id }}">
                                            {{ $unit->unit_name }} - {{ $unit->unit_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="block_building_id" class="form-label">Block Building</label>
                                <select class="form-select" id="block_building_id" name="block_building_id">
                                    <option value="">Select Building (Optional)</option>
                                    @foreach($block->buildings as $building)
                                        <option value="{{ $building->id }}">
                                            {{ $building->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="contractor_id" class="form-label">Assign to Contractor Admin</label>
                                <select class="form-select" id="contractor_id" name="contractor_id">
                                    <option value="">Select Contractor Admin (Optional)</option>
                                    @foreach($contractors as $contractor)
                                        @if($contractor->userType && $contractor->userType->name === 'Contractor Admin')
                                            <option value="{{ $contractor->id }}">
                                                {{ $contractor->name }} ({{ $contractor->email }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="repair_category_id" class="form-label">Repair Category ID</label>
                                <input type="number" class="form-control" id="repair_category_id" name="repair_category_id">
                            </div>
                        </div>
                    </div>

                    <!-- Issue Description -->
                    <div class="mb-3">
                        <label for="issue" class="form-label">Issue Description</label>
                        <textarea class="form-control" id="issue" name="issue" rows="3" placeholder="Describe the work order issue..."></textarea>
                    </div>

                    <div class="row">
                        <!-- Schedule Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Schedule</h5>
                            
                            <div class="mb-3">
                                <label for="issued_date_time" class="form-label">Issued Date & Time</label>
                                <input type="datetime-local" class="form-control" id="issued_date_time" name="issued_date_time">
                            </div>

                            <div class="mb-3">
                                <label for="preferred_start_date_time" class="form-label">Preferred Start Date & Time</label>
                                <input type="datetime-local" class="form-control" id="preferred_start_date_time" name="preferred_start_date_time">
                            </div>

                            <div class="mb-3">
                                <label for="preferred_end_date_time" class="form-label">Preferred End Date & Time</label>
                                <input type="datetime-local" class="form-control" id="preferred_end_date_time" name="preferred_end_date_time">
                            </div>

                            <div class="mb-3">
                                <label for="deadline_date" class="form-label">Deadline Date</label>
                                <input type="date" class="form-control" id="deadline_date" name="deadline_date">
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Contact Information</h5>
                            
                            <div class="mb-3">
                                <label for="contact_name" class="form-label">Contact Name</label>
                                <input type="text" class="form-control" id="contact_name" name="contact_name">
                            </div>

                            <div class="mb-3">
                                <label for="contact_mobile" class="form-label">Contact Mobile</label>
                                <input type="text" class="form-control" id="contact_mobile" name="contact_mobile">
                            </div>

                            <div class="mb-3">
                                <label for="contact_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email">
                            </div>

                            <div class="mb-3">
                                <label for="note_for_access" class="form-label">Note for Access</label>
                                <input type="text" class="form-control" id="note_for_access" name="note_for_access" placeholder="Access instructions for contractor...">
                            </div>
                        </div>
                    </div>

                    <!-- Comments -->
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comments</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="Additional comments or instructions..."></textarea>
                    </div>

                    <!-- File Uploads -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="mb-3">PDF Document</h5>
                                <label for="pdf" class="form-label">Upload PDF</label>
                                <input type="file" class="form-control" id="pdf" name="pdf" accept=".pdf">
                                <div class="form-text">Maximum file size: 10MB</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="mb-3">Images</h5>
                                <label for="images" class="form-label">Upload Images</label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                                <div class="form-text">You can select multiple images. Maximum file size: 2MB each.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
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
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div id="editWorkOrderMessage" class="alert d-none" role="alert"></div>
                    
                    <!-- Similar form structure as create modal -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            
                            <div class="mb-3">
                                <label for="edit_block_issue_id" class="form-label">Related Issue <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_block_issue_id" name="block_issue_id" required>
                                    <option value="">Select Issue</option>
                                    @foreach($block->issues as $issue)
                                        <option value="{{ $issue->id }}">
                                            {{ $issue->ref_no }} - {{ $issue->issue }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="edit_ref_no" class="form-label">Reference Number</label>
                                <input type="text" class="form-control" id="edit_ref_no" name="ref_no" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                <small class="text-muted">Reference number cannot be changed</small>
                            </div>

                            <div class="mb-3">
                                <label for="edit_priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_priority_id" name="priority_id" required>
                                    <option value="">Select Priority</option>
                                    <option value="1">Low</option>
                                    <option value="2">Normal</option>
                                    <option value="3">High</option>
                                    <option value="4">Urgent</option>
                                    <option value="5">Critical</option>
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

                        <div class="col-md-6">
                            <h5 class="mb-3">Location & Assignment</h5>
                            
                            <div class="mb-3">
                                <label for="edit_block_unit_id" class="form-label">Block Unit</label>
                                <select class="form-select" id="edit_block_unit_id" name="block_unit_id">
                                    <option value="">Select Unit (Optional)</option>
                                    @foreach($block->units as $unit)
                                        <option value="{{ $unit->id }}">
                                            {{ $unit->unit_name }} - {{ $unit->unit_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="edit_block_building_id" class="form-label">Block Building</label>
                                <select class="form-select" id="edit_block_building_id" name="block_building_id">
                                    <option value="">Select Building (Optional)</option>
                                    @foreach($block->buildings as $building)
                                        <option value="{{ $building->id }}">
                                            {{ $building->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="edit_contractor_id" class="form-label">Assign to Contractor Admin</label>
                                <select class="form-select" id="edit_contractor_id" name="contractor_id">
                                    <option value="">Select Contractor Admin (Optional)</option>
                                    @foreach($contractors as $contractor)
                                        @if($contractor->userType && $contractor->userType->name === 'Contractor Admin')
                                            <option value="{{ $contractor->id }}">
                                                {{ $contractor->name }} ({{ $contractor->email }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="edit_repair_category_id" class="form-label">Repair Category ID</label>
                                <input type="number" class="form-control" id="edit_repair_category_id" name="repair_category_id">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_issue" class="form-label">Issue Description</label>
                        <textarea class="form-control" id="edit_issue" name="issue" rows="3" placeholder="Describe the work order issue..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Schedule</h5>
                            
                            <div class="mb-3">
                                <label for="edit_issued_date_time" class="form-label">Issued Date & Time</label>
                                <input type="datetime-local" class="form-control" id="edit_issued_date_time" name="issued_date_time">
                            </div>

                            <div class="mb-3">
                                <label for="edit_preferred_start_date_time" class="form-label">Preferred Start Date & Time</label>
                                <input type="datetime-local" class="form-control" id="edit_preferred_start_date_time" name="preferred_start_date_time">
                            </div>

                            <div class="mb-3">
                                <label for="edit_preferred_end_date_time" class="form-label">Preferred End Date & Time</label>
                                <input type="datetime-local" class="form-control" id="edit_preferred_end_date_time" name="preferred_end_date_time">
                            </div>

                            <div class="mb-3">
                                <label for="edit_deadline_date" class="form-label">Deadline Date</label>
                                <input type="date" class="form-control" id="edit_deadline_date" name="deadline_date">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3">Contact Information</h5>
                            
                            <div class="mb-3">
                                <label for="edit_contact_name" class="form-label">Contact Name</label>
                                <input type="text" class="form-control" id="edit_contact_name" name="contact_name">
                            </div>

                            <div class="mb-3">
                                <label for="edit_contact_mobile" class="form-label">Contact Mobile</label>
                                <input type="text" class="form-control" id="edit_contact_mobile" name="contact_mobile">
                            </div>

                            <div class="mb-3">
                                <label for="edit_contact_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="edit_contact_email" name="contact_email">
                            </div>

                            <div class="mb-3">
                                <label for="edit_note_for_access" class="form-label">Note for Access</label>
                                <input type="text" class="form-control" id="edit_note_for_access" name="note_for_access" placeholder="Access instructions for contractor...">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_comment" class="form-label">Comments</label>
                        <textarea class="form-control" id="edit_comment" name="comment" rows="4" placeholder="Additional comments or instructions..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="mb-3">PDF Document</h5>
                                <label for="edit_pdf" class="form-label">Upload PDF</label>
                                <input type="file" class="form-control" id="edit_pdf" name="pdf" accept=".pdf">
                                <div class="form-text">Maximum file size: 10MB</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="mb-3">Images</h5>
                                <label for="edit_images" class="form-label">Upload Images</label>
                                <input type="file" class="form-control" id="edit_images" name="images[]" multiple accept="image/*">
                                <div class="form-text">You can select multiple images. Maximum file size: 2MB each.</div>
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
                <h5 class="modal-title" id="deleteWorkOrderModalLabel">Delete Work Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this work order?</p>
                <div class="alert alert-warning">
                    <strong>Work Order Details:</strong>
                    <ul class="mb-0 mt-2" id="deleteWorkOrderDetails">
                        <!-- Details will be populated by JavaScript -->
                    </ul>
                </div>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
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
