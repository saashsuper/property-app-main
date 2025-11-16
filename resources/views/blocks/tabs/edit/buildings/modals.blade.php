<!-- Building Modal (Add/Edit) -->
<div class="modal fade" id="buildingModal" tabindex="-1" aria-labelledby="buildingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="buildingModalLabel" style="color: white !important; padding-bottom: 15px;">Add Building</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="buildingForm" method="POST" action="{{ route('block-buildings.store') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <!-- Message container -->
                    <div id="buildingMessage" class="alert d-none" role="alert">
                        <i class="ph-check-circle me-2"></i>
                        <span class="message-text"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label for="building_type_id" class="form-label">Building Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="building_type_id" name="building_type_id" required>
                            <option value="">Select Building Type</option>
                            @foreach($blockBuildingTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="building_name" class="form-label">Building Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="building_name" name="building_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_of_floors" class="form-label">No of Floors <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="no_of_floors" name="no_of_floors" required min="1" max="999" maxlength="3">
                    </div>
                    <div class="mb-3">
                        <label for="roof_type" class="form-label">Roof Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="roof_type" name="roof_type" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_lift" class="form-label">No of Lifts <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="no_lift" name="no_lift" required min="0" max="999" maxlength="3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="buildingSubmitBtn">
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

<!-- Building Details Modal -->
<div class="modal fade" id="buildingDetailsModal" tabindex="-1" aria-labelledby="buildingDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="buildingDetailsModalLabel" style="color: white !important; padding-bottom: 15px;">
                    <i class="ph-eye me-2"></i>Building Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Building/Core:</label>
                            <p class="form-control-plaintext" id="detail_building_name">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type:</label>
                            <p class="form-control-plaintext" id="detail_building_type">-</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Floors:</label>
                            <p class="form-control-plaintext" id="detail_building_floors">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lifts:</label>
                            <p class="form-control-plaintext" id="detail_building_lifts">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Roof Type:</label>
                            <p class="form-control-plaintext" id="detail_building_roof">-</p>
                        </div>
                    </div>
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
<div class="modal fade" id="deleteBuildingModal" tabindex="-1" aria-labelledby="deleteBuildingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteBuildingModalLabel">
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
                        <h6 class="mb-1">Are you sure you want to delete this building?</h6>
                        <p class="text-muted mb-0">This action cannot be undone. All building data will be permanently removed.</p>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <strong>Building Details:</strong>
                    <div id="deleteBuildingDetails" class="mt-2">
                        <!-- Building details will be populated here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBuildingBtn">
                    <i class="ph-trash me-1"></i> Delete Building
                </button>
            </div>
        </div>
    </div>
</div>
