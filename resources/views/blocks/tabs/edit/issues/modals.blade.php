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
                    
                    <div class="row">
                        <!-- Full Width Form Fields -->
                        <div class="col-12">
                            <div class="row">
                                <!-- Row 1: Unit Selection, Contact Method, Assigned To -->
                                <div class="col-md-4 mb-3">
                                    <label for="block_unit_id" class="form-label">Unit Selection <span class="text-danger">*</span></label>
                                    <select class="form-select" id="block_unit_id" name="block_unit_id" required>
                                        <option value="">Select Unit</option>
                                        @foreach ($block->units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->unit_code }} - {{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
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

                                <!-- Row 2: Issue Type, Priority, Issue Title -->
                                <div class="col-md-4 mb-3">
                                    <label for="issue_type" class="form-label">Issue Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="issue_type" name="issue_type" required>
                                        <option value="">Select Issue Type</option>
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
                                    <label for="issue" class="form-label">Issue Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="issue" name="issue" required>
                                </div>

                                <!-- Row 3: Dynamic Contact Details based on Contact Method -->
                                <div class="col-md-6 mb-3" id="contact_details_container">
                                    <label for="contact_details" class="form-label">Reported from <span class="text-danger">*</span></label>
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
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="issueSubmitBtn">
                        <i class="ph-check me-1"></i> Create
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
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
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="photoUploadModalLabel">
                    <i class="ph-camera me-2"></i>Upload Photos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <h6 class="mb-1" id="photoUploadIssueTitle">Issue: Loading...</h6>
                                <small class="text-muted" id="photoUploadIssueRef">Reference: Loading...</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Dropzone Container -->
                <div class="mb-3">
                    <label class="form-label">Upload Images <span class="text-danger">*</span></label>
                    <div id="photoDropzone" class="dropzone">
                        <div class="dz-message">
                            <div class="mb-3">
                                <i class="ph-cloud-upload display-4 text-muted"></i>
                            </div>
                            <h4>Drop images here or click to upload</h4>
                            <p class="text-muted font-size-16">
                                <strong>Requirements:</strong><br>
                                • Maximum 10 images<br>
                                • Each image max 2MB<br>
                                • Total size max 10MB<br>
                                • Formats: JPEG, PNG, JPG, GIF
                            </p>
                        </div>
                    </div>
                </div>
                
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
                    <button type="button" class="btn btn-outline-info btn-sm" id="testDropzoneBtn" onclick="testDropzone()">
                        <i class="ph-bug me-1"></i> Test
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
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="ph-trash me-1"></i> Delete Issue
                </button>
            </div>
        </div>
    </div>
</div>
