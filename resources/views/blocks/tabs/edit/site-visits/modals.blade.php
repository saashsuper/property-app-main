<!-- Site Visit Modal (Add/Edit) -->
<div class="modal fade" id="siteVisitModal" tabindex="-1" aria-labelledby="siteVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="siteVisitModalLabel" style="color: white !important; padding-bottom: 15px;">Assign Site Visit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="siteVisitForm" method="POST" action="{{ route('block-visits.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Message container -->
                    <div id="siteVisitMessage" class="alert d-none" role="alert">
                        <i class="ph-check-circle me-2"></i>
                        <span class="message-text"></span>
                    </div>
                    
                    <!-- Block and Unit Row -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Block</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                <strong>{{ $block->name }}</strong> - {{ $block->management_company }}
                                @if($block->blockType)
                                    ({{ $block->blockType->name }})
                                @endif
                            </div>
                            <input type="hidden" name="block_id" value="{{ $block->id }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="block_unit_id" class="form-label">Unit</label>
                            <select class="form-select" id="block_unit_id" name="block_unit_id">
                                <option value="">Select Unit (Optional)</option>
                                @foreach($block->units as $unit)
                                    <option value="{{ $unit->id }}">
                                        {{ $unit->unit_code }}
                                        @if($unit->unit_name)
                                            - {{ $unit->unit_name }}
                                        @endif
                                        @if($unit->unitType)
                                            ({{ $unit->unitType->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Assigned User <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select Contractor Admin</option>
                                @foreach($siteVisitUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_time" class="form-label">Scheduled Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="scheduled_time" name="scheduled_time" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="scheduled_date" class="form-label">Scheduled Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" required placeholder="Enter visit notes and details..."></textarea>
                    </div>
                    
                    <!-- File Upload Section with Dropzone -->
                    <div class="mb-3">
                        <label class="form-label">Upload Photos or Documents</label>
                        <small class="text-muted d-block mb-2">Optional - Upload multiple files</small>
                        <div id="blockSiteVisitDropzone" class="dropzone">
                            <div class="dz-message">
                                <div class="mb-2">
                                    <i class="ph-cloud-upload display-4 text-muted"></i>
                                </div>
                                <h5>Drop files here or click to upload</h5>
                                <p class="text-muted font-size-14 mb-0">
                                    <strong>Requirements:</strong><br>
                                    • Maximum 10 files<br>
                                    • Each file max 5MB<br>
                                    • Formats: Images, PDF, DOC, DOCX
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dropzone Custom Styles -->
                    <style>
                        #blockSiteVisitDropzone.dropzone {
                            min-height: 120px !important;
                            border: 2px dashed #ccc !important;
                            border-radius: 6px !important;
                            background: #fafafa;
                        }
                        
                        #blockSiteVisitDropzone .dz-message {
                            padding: 20px !important;
                            margin: 0 !important;
                        }
                        
                        #blockSiteVisitDropzone.dz-drag-hover {
                            border-color: #0d6efd !important;
                            background: #e7f3ff !important;
                        }
                        
                        #blockSiteVisitDropzone .dz-preview {
                            margin: 10px !important;
                        }
                        
                        #blockSiteVisitDropzone .dz-preview .dz-image {
                            border-radius: 4px !important;
                        }
                    </style>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="siteVisitSubmitBtn">
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

<!-- Site Visit Details Modal -->
<div class="modal fade" id="siteVisitDetailsModal" tabindex="-1" aria-labelledby="siteVisitDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="siteVisitDetailsModalLabel" style="color: white !important; padding-bottom: 15px;">
                    <i class="ph-eye me-2"></i>Site Visit Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Reference Number:</label>
                            <p class="form-control-plaintext" id="detail_ref_no">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <p class="form-control-plaintext" id="detail_status">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Scheduled Date & Time:</label>
                            <p class="form-control-plaintext" id="detail_scheduled_date_time">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Assigned User:</label>
                            <p class="form-control-plaintext" id="detail_user">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created By:</label>
                            <p class="form-control-plaintext" id="detail_created_by">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Start Date & Time:</label>
                            <p class="form-control-plaintext" id="detail_start_date_time">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">End Date & Time:</label>
                            <p class="form-control-plaintext" id="detail_end_date_time">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Notes:</label>
                    <p class="form-control-plaintext" id="detail_notes">-</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Comments:</label>
                    <p class="form-control-plaintext" id="detail_comments">-</p>
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
<div class="modal fade" id="deleteSiteVisitModal" tabindex="-1" aria-labelledby="deleteSiteVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="deleteSiteVisitModalLabel" style="color: white !important; padding-bottom: 15px;">
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
                        <h6 class="mb-1">Are you sure you want to delete this site visit?</h6>
                        <p class="text-muted mb-0">This action cannot be undone. All site visit data will be permanently removed.</p>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <strong>Site Visit Details:</strong>
                    <div id="deleteSiteVisitDetails" class="mt-2">
                        <!-- Site visit details will be populated here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteSiteVisitBtn">
                    <i class="ph-trash me-1"></i> Delete Site Visit
                </button>
            </div>
        </div>
    </div>
</div>
