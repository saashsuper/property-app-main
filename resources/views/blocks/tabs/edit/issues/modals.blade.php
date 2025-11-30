<!-- Issue Modal (Add/Edit) -->
<div class="modal fade" id="issueModal" tabindex="-1" aria-labelledby="issueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 95vw;">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="issueModalLabel" style="color: white !important; padding-bottom: 15px;">Create Issue</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="issueForm" method="POST" action="{{ route('block-issues.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <!-- Message container -->
                    <div id="issueMessage" class="alert d-none" role="alert">
                        <i class="ph-check-circle me-2"></i>
                        <span class="message-text"></span>
                    </div>
                    
                    <!-- Step Indicator -->
                    <div class="step-wizard mb-4">
                        <ul class="step-wizard-list">
                            <li class="step-wizard-item active" data-step="1">
                                <span class="step-wizard-icon">
                                    <i class="ph-file-text"></i>
                                </span>
                                <span class="step-wizard-label">Issue Details</span>
                            </li>
                            <li class="step-wizard-item" data-step="2">
                                <span class="step-wizard-icon">
                                    <i class="ph-images"></i>
                                </span>
                                <span class="step-wizard-label">Upload Images</span>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Step 1: Issue Details -->
                    <div class="step-content" id="step1" data-step="1">
                        <div class="row">
                            <!-- Full Width Form Fields -->
                            <div class="col-12">
                                <div class="row">
                                    <!-- Row 1: Unit Selection, Contact Method, Assigned To -->
                                    <div class="col-md-4 mb-3">
                                        <label for="issue_block_unit_id" class="form-label">Unit Selection <span class="text-danger">*</span></label>
                                        <div class="autoComplete_wrapper" id="unitAutoCompleteWrapper">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="issue_block_unit_id" 
                                                   name="block_unit_display" 
                                                   placeholder="Search for units..." 
                                                   autocomplete="off" 
                                                   required>
                                            <input type="hidden" id="issue_block_unit_id_hidden" name="block_unit_id" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="contact_method_id" class="form-label">Contact Method <span class="text-danger">*</span></label>
                                        <select class="form-select" id="contact_method_id" name="contact_method_id" required>
                                            <option value="">Select Contact Method</option>
                                            @foreach ($contactMethods as $contactMethod)
                                                <option value="{{ $contactMethod->id }}">{{ $contactMethod->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="assigned_to" class="form-label">Assigned To<span class="text-danger">*</span></label>
                                        <select class="form-select" id="assigned_to" name="assigned_to" required>
                                            <option value="">Select Property Manager</option>
                                            @foreach ($users as $user)
                                                @if ($user->userType && $user->userType->name === 'Property manager')
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Row 2: Category, Priority, Problem Overview -->
                                    <div class="col-md-4 mb-3">
                                        <label for="issue_type" class="form-label">{{ __('translation.issue-category') }} <span class="text-danger">*</span></label>
                                        <select class="form-select" id="issue_type" name="issue_type" required>
                                            <option value="">{{ __('translation.select-issue-category') }}</option>
                                            @foreach ($issueTypes as $issueType)
                                                <option value="{{ $issueType->name }}">
                                                    {{ ucfirst(str_replace('_', ' ', $issueType->name)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
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
                                    <div class="col-md-4 mb-3">
                                        <label for="issue" class="form-label">{{ __('translation.problem-overview') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="issue" name="issue" required>
                                    </div>

                                    <!-- Row 3: Dynamic Contact Details based on Contact Method -->
                                    <div class="col-md-6 mb-3" id="contact_details_container">
                                        <label for="contact_details" class="form-label">Reported By <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="contact_details" name="contact_details" placeholder="Enter contact details..." required>
                                        <div class="form-text">Please provide relevant contact information</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="issue_details" class="form-label">Issue Details</label>
                                        <textarea class="form-control" id="issue_details" name="issue_details" rows="2" placeholder="Describe the issue in detail..."></textarea>
                                    </div>

                                    <!-- Row 4: Default Contact Details -->
                                    <div class="col-12 mb-2">
                                        <label for="default_contact_details" class="form-label">Default Contact Details</label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="use_default_contact" checked>
                                            <label class="form-check-label" for="use_default_contact">
                                                Use default contact details
                                            </label>
                                        </div>
                                        <textarea class="form-control" id="default_contact_details" name="default_contact_details" rows="2" placeholder="Enter default contact information..." readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Image Upload -->
                    <div class="step-content d-none" id="step2" data-step="2">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label fw-semibold mb-3">Upload Images <span class="text-muted">(Optional)</span></label>
                                <div id="issueImageDropzone" class="dropzone">
                                    <div class="dz-message text-center py-5">
                                        <div class="mb-3 text-primary">
                                            <i class="ph-cloud-arrow-up" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="fw-semibold mb-2">Drag &amp; drop images here</h5>
                                        <p class="text-muted mb-0">or click to browse your files</p>
                                        <p class="text-muted small mt-2">Supported formats: JPG, PNG, GIF (Max 10MB per file)</p>
                                    </div>
                                </div>
                                <div id="issueImagePreview" class="mt-3 row g-2">
                                    <!-- Uploaded images preview will appear here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevStepBtn" style="display: none;">
                        <i class="ph-arrow-left me-1"></i> Previous
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="nextStepBtn">
                        <i class="ph-arrow-right me-1"></i> Next
                    </button>
                    <button type="submit" class="btn btn-primary d-none" id="issueSubmitBtn">
                        <i class="ph-check me-1"></i> Create Issue
                    </button>
                </div>
            </form>
            
            <!-- Open Issues in Same Unit Table -->
            <div class="modal-body border-top">
                <div class="card border">
                    <div class="card-header d-flex justify-content-between align-items-center py-2 bg-light">
                        <h6 class="mb-0">Open Issues in Same Unit</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 200px;">
                            <table class="table table-sm table-hover mb-0" id="openIssuesTable">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Ref #</th>
                                        <th>Issue</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Reported</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="openIssuesTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            <i class="ph-info-circle"></i> Select a unit to view open issues
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Upload Modal -->
<div class="modal fade" id="photoUploadModal" tabindex="-1" aria-labelledby="photoUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header photo-upload-header border-0 py-3 px-4">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="photo-upload-icon">
                            <i class="ph-camera"></i>
                        </span>
                        <h5 class="modal-title text-white mb-0" id="photoUploadModalLabel">Upload Photos</h5>
                    </div>
                    <p class="text-white-50 mb-0 small">
                        Ref: <span class="fw-semibold text-white" id="photoUploadModalIssueRef">---</span>
                    </p>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Message container -->
                <div id="photoUploadMessage" class="alert d-none" role="alert">
                    <i class="ph-check-circle me-2"></i>
                    <span class="message-text"></span>
                </div>
                
                <!-- Issue Info -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm issue-upload-summary">
                            <div class="card-body py-3 px-3 px-lg-4">
                                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                    <div>
                                        <p class="text-muted mb-1 small text-uppercase">Issue</p>
                                        <h5 class="mb-1" id="photoUploadModalIssueTitle">Loading...</h5>
                                        <div class="text-secondary small">
                                            Reported unit: <span class="fw-semibold" id="photoUploadModalUnit">--</span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 issue-upload-badges text-nowrap">
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary fw-semibold" id="photoUploadModalType">Type: --</span>
                                        <span id="photoUploadModalPriority"></span>
                                        <span id="photoUploadModalStatus"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Dropzone Container -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted text-uppercase small">Upload Images <span class="text-danger">*</span></label>
                    <div id="photoDropzone" class="dropzone">
                        <div class="dz-message text-center py-4">
                            <div class="mb-2 text-primary">
                                <i class="ph-cloud-arrow-up fs-1"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Drag &amp; drop images here</h5>
                            <p class="text-muted mb-0 small">or click to browse your files</p>
                        </div>
                    </div>
                </div>
                
                <!-- Custom CSS to override Dropzone defaults -->
                <style>
                    #photoDropzone.dropzone {
                        min-height: 96px !important;
                        border: 2px dashed rgba(102, 126, 234, 0.45) !important;
                        border-radius: 12px !important;
                        background: #f8f9ff !important;
                        transition: all 0.25s ease-in-out;
                    }
                    
                    #photoDropzone.dropzone:hover,
                    #photoDropzone.dropzone.dz-drag-hover {
                        border-color: #667eea !important;
                        background: #eef1ff !important;
                        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15) !important;
                    }
                    
                    #photoDropzone .dz-message {
                        padding: 15px 8px !important;
                        margin: 0 !important;
                        text-align: center !important;
                        color: #4b5563 !important;
                    }
                    
                    #photoDropzone .dz-message h5 {
                        margin: 6px 0 3px 0 !important;
                        font-size: 0.9rem !important;
                        color: #1f2937 !important;
                    }
                    
                    #photoDropzone .dz-message p {
                        margin: 0 !important;
                        font-size: 0.75rem !important;
                        line-height: 1.3 !important;
                    }
                    
                    .photo-upload-header {
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    }
                    
                    .photo-upload-icon {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        width: 38px;
                        height: 38px;
                        border-radius: 12px;
                        background: rgba(255, 255, 255, 0.18);
                        color: #ffffff;
                        font-size: 1.25rem;
                        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                    }
                    
                    .photo-upload-header .btn-close {
                        opacity: 0.85;
                    }
                    
                    .photo-upload-header .btn-close:hover {
                        opacity: 1;
                    }
                    
                    .issue-upload-summary {
                        background: linear-gradient(135deg, rgba(102,126,234,0.08), rgba(118,75,162,0.08));
                        border-radius: 14px;
                    }
                    
                    .issue-upload-summary h5 {
                        font-weight: 600;
                        color: #111827;
                    }
                    
                    .issue-upload-summary .issue-upload-badges .badge {
                        font-size: 0.75rem;
                        padding: 0.45rem 0.7rem;
                        border-radius: 999px;
                        letter-spacing: 0.02em;
                        background: #f1f5f9;
                        color: #334155;
                    }
                    
                    #photoUploadModalPriority .badge,
                    #photoUploadModalStatus .badge {
                        border-radius: 999px;
                        font-size: 0.75rem;
                        padding: 0.45rem 0.7rem;
                    }
                    
                    #photoUploadModalPriority .badge {
                        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
                    }
                    
                    #photoUploadModalStatus .badge {
                        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.18);
                    }
                </style>
                
                <!-- Upload Status -->
                <div class="mb-3">
                    <div id="photoUploadStatus" class="mt-2"></div>
                </div>
                
                <!-- Existing Photos -->
                <div class="row" id="existingPhotosSection" style="display: none;">
                    <div class="col-12">
                        <label class="form-label">Existing Photos</label>
                        <div id="existingPhotos" class="row">
                            <!-- Existing photos will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Close
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="clearPhotosBtn">
                        <i class="ph-x me-1"></i> Clear All
                    </button>
                    <button type="button" class="btn btn-primary" id="uploadPhotosBtn">
                        <i class="ph-cloud-upload me-1"></i> Upload Photos
                    </button>
                </div>
        </div>
    </div>
</div>

<!-- Photo Preview Modal -->
<div class="modal fade" id="photoPreviewModal" tabindex="-1" aria-labelledby="photoPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoPreviewModalLabel">
                    <span id="currentPhotoInfo">Photo Preview</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Photo Carousel -->
                <div id="photoCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner" id="carouselInner">
                        <!-- Photos will be dynamically added here -->
                    </div>
                    
                    <!-- Navigation Arrows -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                
                <!-- Photo Info -->
                <div class="p-3 text-center bg-light">
                    <h6 id="previewPhotoName" class="mb-1"></h6>
                    <small class="text-muted" id="photoCounter"></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Photo Confirmation Modal -->
<div class="modal fade" id="deletePhotoModal" tabindex="-1" aria-labelledby="deletePhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePhotoModalLabel">
                    <i class="ph-warning text-warning me-2"></i>Delete Photo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this photo?</p>
                <p class="text-muted mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeletePhotoBtn">
                    <i class="ph-trash me-1"></i>Yes, Delete Photo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteIssueModal" tabindex="-1" aria-labelledby="deleteIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteIssueModalLabel">
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
                        <h6 class="mb-1">Are you sure you want to delete this issue?</h6>
                        <p class="text-muted mb-0">This action cannot be undone. All issue data will be permanently removed.</p>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <strong>Issue Details:</strong>
                    <div id="deleteIssueDetails" class="mt-2">
                        <!-- Issue details will be populated here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteIssueBtn">
                    <i class="ph-trash me-1"></i> Delete Issue
                </button>
            </div>
        </div>
    </div>
</div>
