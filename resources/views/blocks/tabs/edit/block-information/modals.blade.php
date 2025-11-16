<!-- Block Information Modal (Add/Edit) -->
<div class="modal fade" id="blockInformationModal" tabindex="-1" aria-labelledby="blockInformationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <div class="d-flex flex-column">
                    <h5 class="modal-title" id="blockInformationModalLabel" style="color: white !important; padding-bottom: 6px;">Add Block Information</h5>
                    <small id="blockInformationEditMetaHeader" class="text-white-50 d-none" style="margin-top: -4px;"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="blockInformationForm" method="POST" action="{{ route('block-information.store') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <!-- Message container -->
                    <div id="blockInformationMessage" class="alert d-none" role="alert">
                        <i class="ph-check-circle me-2"></i>
                        <span class="message-text"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="information_type_id" class="form-label">Information Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="information_type_id" name="information_type_id" required>
                                <option value="">Select Information Type</option>
                                @foreach($blockInformationTypes as $infoType)
                                    <option value="{{ $infoType->id }}">{{ $infoType->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Select the type of information you want to add</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Enter detailed description of the information..."></textarea>
                            <div class="form-text">Provide a detailed description of the information</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="blockInformationSubmitBtn">
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

<!-- Block Information Details Modal -->
<div class="modal fade" id="blockInformationDetailsModal" tabindex="-1" aria-labelledby="blockInformationDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="blockInformationDetailsModalLabel" style="color: white !important; padding-bottom: 15px;">
                    <i class="ph-eye me-2"></i>Block Information Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Information Type:</label>
                            <p class="form-control-plaintext" id="detail_information_type">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Added Date:</label>
                            <p class="form-control-plaintext" id="detail_added_date">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Added By:</label>
                            <p class="form-control-plaintext" id="detail_added_by">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Updated:</label>
                            <p class="form-control-plaintext" id="detail_updated_at">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Description:</label>
                    <div class="form-control-plaintext" id="detail_description" style="min-height: 100px; white-space: pre-wrap; border: 1px solid #dee2e6; padding: 0.75rem; border-radius: 0.375rem; background-color: #f8f9fa;">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteBlockInformationModal" tabindex="-1" aria-labelledby="deleteBlockInformationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="deleteBlockInformationModalLabel" style="color: white !important; padding-bottom: 15px;">
                    <i class="ph-warning me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="ph-warning-circle text-danger" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Are you sure you want to delete this block information?</h6>
                        <p class="text-muted mb-0">This action cannot be undone. All information data will be permanently removed.</p>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <strong>Block Information Details:</strong>
                    <div id="deleteBlockInformationDetails" class="mt-2">
                        <!-- Block information details will be populated here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBlockInformationBtn">
                    <i class="ph-trash me-1"></i> Delete Block Information
                </button>
            </div>
        </div>
    </div>
</div>

