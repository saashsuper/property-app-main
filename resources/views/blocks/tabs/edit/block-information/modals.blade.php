<!-- Block Information Modal (Add/Edit) -->
<div class="modal fade" id="blockInformationModal" tabindex="-1" aria-labelledby="blockInformationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="blockInformationModalLabel" style="color: white !important; padding-bottom: 15px;">Add Block Information</h5>
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
                        <div class="col-md-6 mb-3">
                            <label for="information_type_id" class="form-label">Information Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="information_type_id" name="information_type_id" required>
                                <option value="">Select Information Type</option>
                                @foreach($blockInformationTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->display_name ?? ucwords(str_replace('_', ' ', $type->name)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter block information description" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Save Block Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Block Information Details Modal -->
<div class="modal fade" id="viewBlockInformationModal" tabindex="-1" aria-labelledby="viewBlockInformationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info text-white" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="viewBlockInformationModalLabel" style="color: white !important; padding-bottom: 15px;">
                    <i class="ph-eye me-2"></i>Block Information Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Information Type:</label>
                        <p class="form-control-plaintext" id="viewInformationType">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Added Date:</label>
                        <p class="form-control-plaintext" id="viewAddedDate">-</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Added By:</label>
                        <p class="form-control-plaintext" id="viewAddedBy">-</p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold text-muted">Description:</label>
                        <div class="border rounded p-3 bg-light" id="viewDescription" style="min-height: 100px;">
                            -
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Block Information Confirmation Modal -->
<div class="modal fade" id="deleteBlockInformationModal" tabindex="-1" aria-labelledby="deleteBlockInformationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-danger text-white" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="deleteBlockInformationModalLabel" style="color: white !important; padding-bottom: 15px;">
                    <i class="ph-trash me-2"></i>Delete Block Information
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="ph-warning-circle me-2"></i>
                    <div>
                        <strong>Warning!</strong> This action cannot be undone. The following block information will be permanently deleted:
                    </div>
                </div>
                
                <div class="border rounded p-3 bg-light">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Type:</strong> <span id="deleteInformationType">-</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Added Date:</strong> <span id="deleteAddedDate">-</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Added By:</strong> <span id="deleteAddedBy">-</span>
                        </div>
                        <div class="col-12">
                            <strong>Description:</strong> <span id="deleteDescription">-</span>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" id="deleteBlockInformationId" value="">
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBlockInformation">
                    <i class="ph-trash me-1"></i> Delete Block Information
                </button>
            </div>
        </div>
    </div>
</div>