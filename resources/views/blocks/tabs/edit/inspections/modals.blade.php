<!-- Add Inspection Modal -->
<div class="modal fade" id="addInspectionModal" tabindex="-1" aria-labelledby="addInspectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="addInspectionModalLabel" style="color: white !important; padding-bottom: 15px;">Add Inspection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="addInspectionForm" method="POST" action="{{ route('block-inspections.store-from-modal') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <!-- Message container -->
                    <div id="addInspectionMessage" class="alert d-none" role="alert">
                        <i class="ph-check-circle me-2"></i>
                        <span class="message-text"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Lead Inspector <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select Lead Inspector</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Only Property Manager users can be assigned as Lead Inspector</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="job_status_id" class="form-label">Status</label>
                            <select class="form-select" id="job_status_id" name="job_status_id">
                                <option value="">Select Status</option>
                                <option value="1">Scheduled</option>
                                <option value="2">In Progress</option>
                                <option value="3">Completed</option>
                                <option value="4">Cancelled</option>
                                <option value="5">On Hold</option>
                                <option value="6">Rescheduled</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="scheduled_date" class="form-label">Scheduled Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_time" class="form-label">Scheduled Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="scheduled_time" name="scheduled_time" required>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">Notes <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Enter inspection notes..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Save
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Inspection Modal -->
<div class="modal fade" id="editInspectionModal" tabindex="-1" aria-labelledby="editInspectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="editInspectionModalLabel" style="color: white !important; padding-bottom: 15px;">Edit Inspection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="editInspectionForm" method="POST" action="{{ route('block-inspections.update', 0) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="inspection_id" id="edit_inspection_id">
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <!-- Message container -->
                    <div id="editInspectionMessage" class="alert d-none" role="alert">
                        <i class="ph-check-circle me-2"></i>
                        <span class="message-text"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_ref_no" class="form-label">Reference Number</label>
                            <input type="text" class="form-control" id="edit_ref_no" name="ref_no" readonly style="background-color: #f8f9fa;">
                            <div class="form-text">Reference number cannot be changed</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_job_status_id" class="form-label">Status</label>
                            <select class="form-select" id="edit_job_status_id" name="job_status_id">
                                <option value="">Select Status</option>
                                <option value="1">Scheduled</option>
                                <option value="2">In Progress</option>
                                <option value="3">Completed</option>
                                <option value="4">Cancelled</option>
                                <option value="5">On Hold</option>
                                <option value="6">Rescheduled</option>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="edit_user_id" class="form-label">Lead Inspector <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_user_id" name="user_id" required>
                                <option value="">Select Lead Inspector</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->userType->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Only Property Manager users can be assigned as Lead Inspector</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_scheduled_date" class="form-label">Scheduled Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_scheduled_date" name="scheduled_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_scheduled_time" class="form-label">Scheduled Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="edit_scheduled_time" name="scheduled_time" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="edit_start_date" name="start_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_start_time" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="edit_start_time" name="start_time">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_time" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="edit_end_time" name="end_time">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="edit_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="4" placeholder="Enter inspection notes..."></textarea>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <small class="text-muted">
                                        <strong>Created:</strong> <span id="edit_created_info">-</span><br>
                                        <strong>Last Updated:</strong> <span id="edit_updated_info">-</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Update Inspection
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Inspection Modal -->
<div class="modal fade" id="deleteInspectionModal" tabindex="-1" aria-labelledby="deleteInspectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteInspectionModalLabel">
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
                        <h6 class="mb-1">Are you sure you want to delete this inspection?</h6>
                        <p class="text-muted mb-0">This action cannot be undone. The inspection record will be permanently removed.</p>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <strong>Inspection Details:</strong>
                    <div id="deleteInspectionDetails" class="mt-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteInspectionBtn">
                    <i class="ph-trash me-1"></i> Delete Inspection
                </button>
            </div>
        </div>
    </div>
</div>

