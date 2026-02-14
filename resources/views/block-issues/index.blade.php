@extends('layouts.master')
@section('title')
    Block Issues - PROMAN
@endsection
@section('css')
<x-datatable-base />
<!-- Dropzone CSS -->
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

<style>
/* Custom Dropzone Styling - Page Specific */
.dropzone {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    background: #f8f9fa;
    min-height: 150px;
    padding: 15px;
    transition: all 0.3s ease;
}

.dropzone:hover {
    border-color: #667eea;
    background: #f0f2ff;
}

.dropzone.dz-drag-hover {
    border-color: #667eea;
    background: #e8f0fe;
}

.dropzone .dz-message {
    margin: 0;
    color: #6c757d;
}

.dropzone .dz-message h4 {
    color: #495057;
    margin-bottom: 10px;
}

.dropzone .dz-message p {
    margin-bottom: 0;
    font-size: 14px;
}

.dropzone .dz-preview {
    margin: 8px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.dropzone .dz-preview .dz-image {
    width: 120px;
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
}

.dropzone .dz-preview .dz-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.dropzone .dz-preview .dz-details {
    padding: 8px;
    background: #fff;
    border-top: 1px solid #dee2e6;
}

.dropzone .dz-preview .dz-filename {
    font-size: 12px;
    font-weight: 500;
    color: #495057;
    margin-bottom: 4px;
}

.dropzone .dz-preview .dz-size {
    font-size: 11px;
    color: #6c757d;
}

.dropzone .dz-preview .dz-remove {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dropzone .dz-preview .dz-remove:hover {
    background: rgba(220, 53, 69, 1);
}
</style>
@endsection
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">@lang('translation.block-issues')</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Block Issues</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center mb-3 gap-3">
                            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">
                                @lang('translation.block-issues-management')
                            </h6>
                            <button class="btn btn-outline-primary btn-sm" id="toggleSearchBtn" title="Search & Filter Issues">
                                <i class="ph-funnel"></i>
                            </button>
                            <div class="d-flex align-items-center gap-2">
                                <!-- Export Buttons -->
                                <div class="btn-group" role="group">
                                    <a href="{{ route('export.pdf', 'block-issues') }}?{{ http_build_query(request()->query()) }}" 
                                       id="exportPdfBtn"
                                       class="btn btn-outline-danger btn-sm" title="Export to PDF">
                                        <i class="ph-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('export.excel', 'block-issues') }}?{{ http_build_query(request()->query()) }}" 
                                       id="exportExcelBtn"
                                       class="btn btn-outline-success btn-sm" title="Export to Excel">
                                        <i class="ph-file-xls"></i>
                                    </a>
                                    <a href="{{ route('export.print', 'block-issues') }}?{{ http_build_query(request()->query()) }}" 
                                       id="exportPrintBtn"
                                       class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
                                        <i class="ph-printer"></i>
                                    </a>
                                </div>
                                <!-- Add Button -->
                                <a href="{{ route('block-issues.create') }}" class="btn btn-primary btn-sm">
                                    <i class="ph-plus me-1"></i>@lang('translation.create-block-issue')
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Issues Panel (Hidden by default) -->
                        <div id="searchIssuesPanel" class="card mb-3" style="display: none;">
                            <div class="p-2 bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="ph-magnifying-glass me-2"></i>
                                    Search Issues
                                </h6>
                                <button type="button" class="btn btn-sm" id="closeSearchHeaderBtn" title="Close Search">
                                    <i class="ph-x"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <form id="searchIssuesForm" class="row g-3">
                                    <!-- Block Filter -->
                                    <div class="col-6 mb-3">
                                        <label for="search_block_id" class="form-label">Block</label>
                                        <select class="form-select" id="search_block_id" name="block_id">
                                            <option value="">All Blocks</option>
                                            @foreach($blocks as $block)
                                                <option value="{{ $block->id }}">{{ $block->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Status Filter -->
                                    <div class="col-6 mb-3">
                                        <label for="search_status" class="form-label">Status</label>
                                        <select class="form-select" id="search_status" name="status">
                                            <option value="">All Status</option>
                                            <option value="1">Open</option>
                                            <option value="2">In Progress</option>
                                            <option value="3">Resolved</option>
                                            <option value="4">Closed</option>
                                            <option value="5">On Hold</option>
                                        </select>
                                    </div>

                                    <!-- Priority Filter -->
                                    <div class="col-6 mb-3">
                                        <label for="search_priority" class="form-label">Priority</label>
                                        <select class="form-select" id="search_priority" name="priority">
                                            <option value="">All Priority</option>
                                            <option value="1">Low</option>
                                            <option value="2">Normal</option>
                                            <option value="3">High</option>
                                            <option value="4">Urgent</option>
                                            <option value="5">Critical</option>
                                        </select>
                                    </div>

                                    <!-- Keyword Search -->
                                    <div class="col-6 mb-3">
                                        <label for="search_keyword" class="form-label">Keyword Search</label>
                                        <input type="text" class="form-control" id="search_keyword" name="search"
                                            placeholder="{{ __('translation.search-by-problem-overview') }}">
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-primary" id="searchIssuesBtn">
                                    <i class="ph-magnifying-glass me-1"></i> Search
                                </button>
                                <button type="button" class="btn btn-secondary" id="clearSearchBtn">
                                    <i class="ph-x me-1"></i> Clear
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="showAllBtn">
                                    <i class="ph-list me-1"></i> Show All
                                </button>
                            </div>
                        </div>

                        <!-- Issues Table -->
                        <x-datatable-loader 
                            id="issues-table-loading" 
                            message="Loading issues..." 
                            tableId="blockIssuesTable" 
                        />
                        
                        <div class="table-responsive">
                            <table id="blockIssuesTable" class="table table-bordered table-striped table-hover" style="display: none;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Issue ID</th>
                                        <th>Title</th>
                                        <th>Block</th>
                                        <th>Unit</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Reported Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($blockIssues as $issue)
                                    <tr>
                                        <td>
                                            <a href="{{ route('block-issues.show', $issue) }}" class="text-decoration-none">
                                                <b>{{ $issue->ref_no }}</b>
                                            </a>
                                        </td>
                                        <td>{{ $issue->issue ?? 'N/A' }}</td>
                                        <td>
                                            @if($issue->block)
                                                {{ $issue->block->name }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($issue->blockUnit)
                                                {{ $issue->blockUnit->unit_name }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($issue->issue_type)
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($issue->priority)
                                                <span class="badge bg-{{ $issue->priority->btn_class ?? 'secondary' }}">
                                                    {{ $issue->priority->label ?? 'Unknown' }}
                                                </span>
                                            @else
                                                <span class="badge bg-{{ $issue->priority_color }}">
                                                    {{ $issue->priority_text }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $issue->status_color }}">{{ $issue->status_text }}</span>
                                        </td>
                                        <td>{{ $issue->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('block-issues.show', $issue) }}" 
                                                   class="btn btn-outline-primary" title="View">
                                                    <i class="ph-eye"></i>
                                                </a>
                                                <button class="btn btn-outline-secondary" onclick="editIssue({{ $issue->id }})" title="Edit">
                                                    <i class="ph-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-info" onclick="openPhotoUploadModal({{ $issue->id }})" title="Upload Photos">
                                                    <i class="ph-camera"></i>
                                                </button>
                                                @php
                                                    // Check if issue has related entities (work orders, site visits, or actions) to determine archive vs delete
                                                    $hasWorkOrders = $issue->hasWorkOrders();
                                                    $hasSiteVisits = $issue->hasSiteVisits();
                                                    $hasActions = $issue->hasActions();
                                                    $hasRelatedEntities = $hasWorkOrders || $hasSiteVisits || $hasActions;
                                                    
                                                    $workOrdersCount = $issue->workOrders ? $issue->workOrders->count() : ($hasWorkOrders ? $issue->workOrders()->count() : 0);
                                                    $siteVisitsCount = $issue->relatedSiteVisits ? $issue->relatedSiteVisits->count() : ($hasSiteVisits ? $issue->relatedSiteVisits()->count() : 0);
                                                    $actionsCount = $issue->actions ? $issue->actions->count() : ($hasActions ? $issue->actions()->count() : 0);
                                                    
                                                    $actionText = $hasRelatedEntities ? 'Archive' : 'Delete';
                                                    $actionIcon = $hasRelatedEntities ? 'ph-archive' : 'ph-trash';
                                                    $actionColor = $hasRelatedEntities ? 'warning' : 'danger';
                                                @endphp
                                                <button class="btn btn-outline-{{ $actionColor }}" 
                                                        title="{{ $actionText }}"
                                                        onclick="showDeleteIssueModal({{ $issue->id }}, {
                                                            ref_no: '{{ addslashes($issue->ref_no) }}',
                                                            issue: '{{ addslashes($issue->issue ?? 'N/A') }}',
                                                            has_related_entities: {{ $hasRelatedEntities ? 'true' : 'false' }},
                                                            has_work_orders: {{ $hasWorkOrders ? 'true' : 'false' }},
                                                            has_site_visits: {{ $hasSiteVisits ? 'true' : 'false' }},
                                                            has_actions: {{ $hasActions ? 'true' : 'false' }},
                                                            work_orders_count: {{ $workOrdersCount }},
                                                            site_visits_count: {{ $siteVisitsCount }},
                                                            actions_count: {{ $actionsCount }}
                                                        })">
                                                    <i class="{{ $actionIcon }}"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ph-warning font-size-24 mb-2"></i>
                                                 <p>@lang('translation.no-block-issues-found')</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination handled by DataTables -->
                        {{-- @if($blockIssues->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $blockIssues->firstItem() }} to {{ $blockIssues->lastItem() }} of {{ $blockIssues->total() }} results
                            </div>
                            <div>
                                {{ $blockIssues->appends(request()->query())->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete/Archive Confirmation Modal for Issue -->
    <div class="modal fade" id="deleteIssueModal" tabindex="-1" aria-labelledby="deleteIssueModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="deleteIssueModalHeader">
                    <h5 class="modal-title" id="deleteIssueModalLabel">
                        <i class="ph-warning me-2"></i>Confirm Action
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteIssueModalMessage">Are you sure you want to perform this action?</p>
                    <div class="alert" id="deleteIssueModalAlert">
                        <i class="ph-warning me-2"></i>
                        <span id="deleteIssueModalAlertMessage"></span>
                    </div>
                    <div class="alert alert-info">
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
                    <button type="button" class="btn" id="confirmDeleteIssueBtn">
                        <i class="ph-trash me-1"></i> Confirm
                    </button>
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

    <!-- Edit Issue Modal -->
    <div class="modal fade" id="editIssueModal" tabindex="-1" aria-labelledby="editIssueModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="max-width: 95vw;">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                    <h5 class="modal-title" id="editIssueModalLabel" style="color: white !important; padding-bottom: 15px;">Edit Issue <span id="editIssueModalSubtitle" class="fw-normal opacity-90"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
                </div>
                <form id="editIssueForm" method="POST" action="#" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Message container -->
                        <div id="editIssueMessage" class="alert d-none" role="alert">
                            <i class="ph-check-circle me-2"></i>
                            <span class="message-text"></span>
                        </div>
                        
                        <div class="row">
                            <!-- Full Width Form Fields -->
                            <div class="col-12">
                                <div class="row">
                                    <!-- Row 1: Unit Selection, Category, Assigned To -->
                                    <div class="col-md-4 mb-3">
                                        <label for="edit_block_unit_id" class="form-label">Unit Selection <span class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_block_unit_id" name="block_unit_id" required>
                                            <option value="">Select Unit</option>
                                            <!-- Units will be loaded dynamically -->
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="edit_issue_type" class="form-label">{{ __('translation.issue-category') }} <span class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_issue_type" name="issue_type" required>
                                            <option value="">{{ __('translation.select-issue-category') }}</option>
                                            @foreach ($issueTypes ?? [] as $issueType)
                                                <option value="{{ $issueType->name }}">
                                                    {{ ucfirst(str_replace('_', ' ', $issueType->name)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="edit_assigned_to" class="form-label">Assigned To<span class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_assigned_to" name="assigned_to" required>
                                            <option value="">Select Property Manager</option>
                                            @foreach ($users ?? [] as $user)
                                                @if ($user->userType && $user->userType->name === 'Property manager')
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Row 2: Contact Method, Priority, Problem Overview -->
                                    <div class="col-md-4 mb-3">
                                        <label for="edit_contact_method_id" class="form-label">Contact Method <span class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_contact_method_id" name="contact_method_id" required>
                                            <option value="">Select Contact Method</option>
                                            @foreach ($contactMethods ?? [] as $contactMethod)
                                                <option value="{{ $contactMethod->id }}">{{ $contactMethod->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
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
                                    <div class="col-md-4 mb-3" id="edit_contact_details_container">
                                        <label for="edit_contact_details" class="form-label">Reported from <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_contact_details" name="contact_details" placeholder="Enter contact details..." required>
                                        <div class="form-text">Please provide relevant contact information</div>
                                    </div>

                                    <!-- Row 3: Default Contact Details -->
                                    <div class="col-12 mb-3">
                                        <label for="edit_default_contact_details" class="form-label">Default Contact Details</label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_use_default_contact" checked>
                                            <label class="form-check-label" for="edit_use_default_contact">
                                                Use default contact details
                                            </label>
                                        </div>
                                        <textarea class="form-control" id="edit_default_contact_details" name="default_contact_details" rows="3" placeholder="Enter default contact information..." readonly></textarea>
                                    </div>

                                    <!-- Row 4: Problem Overview -->
                                    <div class="col-12 mb-2">
                                        <label for="edit_issue" class="form-label">{{ __('translation.problem-overview') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_issue" name="issue" required>
                                    </div>

                                    <!-- Row 5: Issue Details -->
                                    <div class="col-12 mb-3">
                                        <label for="edit_issue_details" class="form-label">Issue Details</label>
                                        <textarea class="form-control" id="edit_issue_details" name="issue_details" rows="2" placeholder="Describe the issue in detail..."></textarea>
                                    </div>

                                    <!-- Row 6: File Upload -->
                                    <div class="col-12 mb-3">
                                        <label for="edit_images" class="form-label">Upload Images</label>
                                        <input type="file" class="form-control" id="edit_images" name="images[]" multiple accept="image/*">
                                        <small class="form-text text-muted">You can select multiple images. Maximum file size: 2MB each.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="editIssueSubmitBtn">
                            <i class="ph-check me-1"></i> Update
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ph-x me-1"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('confirmDeleteModal');
        if (!modalEl) return;
        modalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var action = button.getAttribute('data-action');
            var ref = button.getAttribute('data-ref');
            modalEl.querySelector('#deleteForm').setAttribute('action', action);
            modalEl.querySelector('#deleteRef').textContent = ref || '';
        });
    });

    // Photo Upload Function
    function openPhotoUploadModal(issueId) {
        console.log('openPhotoUploadModal called with ID:', issueId);
        
        // Show loading state
        const uploadBtn = document.querySelector(`button[onclick="openPhotoUploadModal(${issueId})"]`);
        if (uploadBtn) {
            const originalText = uploadBtn.innerHTML;
            uploadBtn.innerHTML = '<i class="ph-spinner ph-spin me-1"></i>Loading...';
            uploadBtn.disabled = true;
        }
        
        // Load issue data and existing photos
        loadIssueForPhotoUpload(issueId);
    }
    
    // Load issue data and existing photos for the upload modal
    function loadIssueForPhotoUpload(issueId) {
        currentIssueId = issueId; // Set the current issue ID for dropzone
        
        fetch(`/block-issues/${issueId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const issue = data.data;
                
                // Update modal with issue info
                document.getElementById('photoUploadIssueTitle').textContent = `Issue: ${issue.issue || 'N/A'}`;
                document.getElementById('photoUploadIssueRef').textContent = `Reference: ${issue.ref_no || 'N/A'}`;
                
                // Load existing photos
                loadExistingPhotos(issueId);
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('photoUploadModal'));
                modal.show();
            } else {
                showPhotoMessage('danger', 'Error loading issue data');
            }
        })
        .catch(error => {
            console.error('Error loading issue:', error);
            showPhotoMessage('danger', 'Error loading issue data');
        })
        .finally(() => {
            // Reset button state
            const uploadBtn = document.querySelector(`button[onclick="openPhotoUploadModal(${issueId})"]`);
            if (uploadBtn) {
                uploadBtn.innerHTML = '<i class="ph-camera me-2"></i> Upload Photos';
                uploadBtn.disabled = false;
            }
        });
    }
    
    // Load existing photos for the issue
    function loadExistingPhotos(issueId) {
        fetch(`/api/block-issues/${issueId}/photos`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                displayExistingPhotos(data.data);
                document.getElementById('existingPhotosSection').style.display = 'block';
            } else {
                document.getElementById('existingPhotosSection').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading existing photos:', error);
            document.getElementById('existingPhotosSection').style.display = 'none';
        });
    }
    
    // Display existing photos in the modal
    function displayExistingPhotos(photos) {
        const container = document.getElementById('existingPhotos');
        container.innerHTML = '';
        
        photos.forEach(function(photo, index) {
            const photoHtml = `
                <div class="col-md-4 mb-2">
                    <div class="card">
                        <img src="/storage/${photo.image_path}/${photo.image_name}" 
                             class="card-img-top" 
                             style="height: 120px; object-fit: cover;"
                             alt="Photo ${index + 1}">
                        <div class="card-body p-2">
                            <small class="text-muted">${photo.image_name}</small>
                            <button type="button" class="btn btn-sm btn-outline-danger float-end" 
                                    onclick="deletePhoto(${photo.id})" title="Delete Photo">
                                <i class="ph-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', photoHtml);
        });
    }
    
    // Show message in photo upload modal
    function showPhotoMessage(type, message) {
        const messageDiv = document.getElementById('photoUploadMessage');
        if (messageDiv) {
            messageDiv.className = `alert alert-${type}`;
            messageDiv.querySelector('.message-text').textContent = message;
            messageDiv.classList.remove('d-none');
            
            // Auto-hide success messages
            if (type === 'success') {
                setTimeout(() => {
                    messageDiv.classList.add('d-none');
                }, 5000);
            }
        }
    }
    
    // Delete a photo
    function deletePhoto(photoId) {
        if (!confirm('Are you sure you want to delete this photo?')) {
            return;
        }
        
        fetch(`/api/block-issue-photos/${photoId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPhotoMessage('success', 'Photo deleted successfully');
                // Reload existing photos
                const issueId = document.getElementById('photoUploadForm').getAttribute('action').split('/')[4];
                loadExistingPhotos(issueId);
            } else {
                showPhotoMessage('danger', 'Error deleting photo');
            }
        })
        .catch(error => {
            console.error('Error deleting photo:', error);
            showPhotoMessage('danger', 'Error deleting photo');
        });
    }

    // Edit Issue Function
    function editIssue(issueId) {
        console.log('editIssue called with ID:', issueId);
        
        // Show loading state
        const editBtn = document.querySelector(`button[onclick="editIssue(${issueId})"]`);
        if (editBtn) {
            const originalText = editBtn.innerHTML;
            editBtn.innerHTML = '<i class="ph-spinner ph-spin me-1"></i>Loading...';
            editBtn.disabled = true;
        }
        
        // Make AJAX request to get issue data
        fetch(`/block-issues/${issueId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Issue data loaded:', data);
            if (data.success) {
                const issue = data.data;
                const blockId = issue.block_id || (issue.block && issue.block.id);
                const unitSelect = document.getElementById('edit_block_unit_id');
                
                // Update modal header: Edit Issue + Issue ID + Block name (same row)
                const refNo = issue.ref_no || ('#' + issueId);
                const blockName = (issue.block && issue.block.name) ? issue.block.name : (issue.block_name || 'N/A');
                const subtitleEl = document.getElementById('editIssueModalSubtitle');
                if (subtitleEl) {
                    subtitleEl.textContent = '— ' + refNo + ' · ' + blockName;
                }
                document.getElementById('editIssueSubmitBtn').innerHTML = '<i class="ph-check me-1"></i> Update';
                document.getElementById('editIssueForm').setAttribute('action', `/block-issues/${issueId}`);
                
                // Load units for the issue's block, then show modal and populate form
                function showEditModalWithForm() {
                    const editModal = document.getElementById('editIssueModal');
                    editModal.addEventListener('shown.bs.modal', function() {
                        document.getElementById('edit_contact_method_id').value = issue.contact_method_id || '';
                        document.getElementById('edit_block_unit_id').value = issue.block_unit_id || '';
                        document.getElementById('edit_assigned_to').value = issue.assigned_to?.id || issue.assigned_to || '';
                        document.getElementById('edit_issue_type').value = issue.issue_type || '';
                        document.getElementById('edit_priority_id').value = issue.priority_id || '';
                        document.getElementById('edit_issue').value = issue.issue || '';
                        document.getElementById('edit_contact_details').value = issue.contact_details || '';
                        document.getElementById('edit_issue_details').value = issue.issue_details || '';
                        
                        editModal.removeEventListener('shown.bs.modal', arguments.callee);
                    }, { once: true });
                    
                    const modal = new bootstrap.Modal(document.getElementById('editIssueModal'));
                    modal.show();
                }
                
                if (blockId) {
                    unitSelect.innerHTML = '<option value="">Loading units...</option>';
                    unitSelect.disabled = true;
                    fetch(`/block-units/block/${blockId}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(unitsResp => {
                        unitSelect.innerHTML = '<option value="">Select Unit</option>';
                        const units = unitsResp.data && Array.isArray(unitsResp.data) ? unitsResp.data : [];
                        units.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id;
                            const unitLabel = (unit.unit_code || '') + (unit.unit_name && unit.unit_name !== unit.unit_code ? ' - ' + unit.unit_name : '') + (unit.unit_type && unit.unit_type.name ? ' (' + unit.unit_type.name + ')' : '');
                            option.textContent = unitLabel || ('Unit #' + unit.id);
                            unitSelect.appendChild(option);
                        });
                        unitSelect.disabled = false;
                        if (issue.block_unit_id) unitSelect.value = issue.block_unit_id;
                        showEditModalWithForm();
                    })
                    .catch(err => {
                        console.error('Error loading units:', err);
                        unitSelect.innerHTML = '<option value="">Select Unit</option>';
                        unitSelect.disabled = false;
                        if (issue.block_unit_id) {
                            const opt = document.createElement('option');
                            opt.value = issue.block_unit_id;
                            opt.textContent = (issue.block_unit && issue.block_unit.unit_name) ? issue.block_unit.unit_name : ('Unit #' + issue.block_unit_id);
                            unitSelect.appendChild(opt);
                            unitSelect.value = issue.block_unit_id;
                        }
                        showEditModalWithForm();
                    });
                } else {
                    unitSelect.innerHTML = '<option value="">Select Unit</option>';
                    unitSelect.disabled = false;
                    showEditModalWithForm();
                }
            } else {
                showEditMessage('danger', 'Error loading issue data');
            }
        })
        .catch(error => {
            console.error('Error loading issue:', error);
            showEditMessage('danger', 'Error loading issue data');
        })
        .finally(() => {
            // Reset button state
            if (editBtn) {
                editBtn.innerHTML = '<i class="ph-pencil me-2"></i> Edit';
                editBtn.disabled = false;
            }
        });
    }
    
    // Show message function for edit modal
    function showEditMessage(type, message) {
        const messageDiv = document.getElementById('editIssueMessage');
        if (messageDiv) {
            messageDiv.className = `alert alert-${type}`;
            messageDiv.querySelector('.message-text').textContent = message;
            messageDiv.classList.remove('d-none');
            
            // Auto-hide success messages
            if (type === 'success') {
                setTimeout(() => {
                    messageDiv.classList.add('d-none');
                }, 5000);
            }
        }
    }
    
    // Handle assigned_to change to populate default contact details
    document.getElementById('edit_assigned_to').addEventListener('change', function() {
        const userId = this.value;
        if (userId && document.getElementById('edit_use_default_contact').checked) {
            getUserDetailsForEdit(userId);
        }
    });
    
    // Handle default contact checkbox change
    document.getElementById('edit_use_default_contact').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('edit_default_contact_details').readOnly = true;
            document.getElementById('edit_default_contact_details').classList.add('bg-light');
            const userId = document.getElementById('edit_assigned_to').value;
            if (userId) {
                getUserDetailsForEdit(userId);
            }
        } else {
            document.getElementById('edit_default_contact_details').readOnly = false;
            document.getElementById('edit_default_contact_details').classList.remove('bg-light');
        }
    });
    
    // Get user details for edit modal
    function getUserDetailsForEdit(userId) {
        if (!userId) return;
        
        fetch(`/api/users/${userId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const user = data.data;
                let contactDetails = '';
                
                // Build contact details string
                if (user.phone) {
                    contactDetails += `Phone: ${user.phone}`;
                }
                if (user.email) {
                    contactDetails += contactDetails ? `\nEmail: ${user.email}` : `Email: ${user.email}`;
                }
                if (user.address) {
                    contactDetails += contactDetails ? `\nAddress: ${user.address}` : `Address: ${user.address}`;
                }
                
                // Update default contact details field
                document.getElementById('edit_default_contact_details').value = contactDetails;
            }
        })
        .catch(error => {
            console.error('Error loading user details:', error);
        });
    }
    
    // Handle edit form submission
    document.getElementById('editIssueForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('editIssueSubmitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="ph-spinner ph-spin me-1"></i> Updating...';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showEditMessage('success', 'Issue updated successfully!');
                setTimeout(() => {
                    location.reload(); // Reload page to show updated data
                }, 1000);
            } else {
                showEditMessage('danger', data.message || 'Error updating issue');
            }
        })
        .catch(error => {
            console.error('Error updating issue:', error);
            showEditMessage('danger', 'Error updating issue');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // ========================================
    // DROPZONE INITIALIZATION FOR BLOCK-ISSUES LIST
    // ========================================
    
    let photoDropzone;
    let currentIssueId = null;
    
    // Initialize Dropzone when photo upload modal is shown
    document.getElementById('photoUploadModal').addEventListener('shown.bs.modal', function() {
        // Destroy existing dropzone if it exists
        if (photoDropzone) {
            photoDropzone.destroy();
            photoDropzone = null;
        }
        initializePhotoDropzone();
    });
    
    // Cleanup when modal is hidden
    document.getElementById('photoUploadModal').addEventListener('hidden.bs.modal', function() {
        if (photoDropzone) {
            photoDropzone.destroy();
            photoDropzone = null;
        }
    });
    
    // Initialize Photo Dropzone
    function initializePhotoDropzone() {
        // Disable auto discover to prevent conflicts
        Dropzone.autoDiscover = false;
        
        // Ensure element is clean
        const dropzoneElement = document.getElementById('photoDropzone');
        if (dropzoneElement.dropzone) {
            dropzoneElement.dropzone.destroy();
        }
        
        photoDropzone = new Dropzone("#photoDropzone", {
            url: "#", // Disable auto-upload
                paramName: "images",
                uploadMultiple: true,
                parallelUploads: 10,
                maxFiles: 10,
                maxFilesize: 2, // 2MB per file
                acceptedFiles: "image/*",
                addRemoveLinks: true,
                clickable: true, // Enable click to upload
                autoProcessQueue: false, // Don't auto-upload
                dictDefaultMessage: "Drop images here or click to upload",
                dictRemoveFile: "Remove",
                dictCancelUpload: "Cancel",
                dictUploadCanceled: "Upload canceled",
                dictInvalidFileType: "You can't upload files of this type.",
                dictFileTooBig: "File is too big. Max filesize: 2MB.",
                dictMaxFilesExceeded: "You can not upload more than 10 files.",
                dictResponseError: "Server responded with an error.",
                dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
                dictRemoveFileConfirmation: "Are you sure you want to remove this file?",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            init: function() {
                const dz = this;
                
                // Custom styling
                this.on("addedfile", function(file) {
                    // Add custom styling to file preview
                    const preview = file.previewElement;
                    preview.classList.add('dz-image-preview-custom');
                    
                    // Add file size info
                    const sizeInfo = preview.querySelector('.dz-size');
                    if (!sizeInfo) {
                        const details = preview.querySelector('.dz-details');
                        if (details) {
                            const sizeDiv = document.createElement('div');
                            sizeDiv.className = 'dz-size';
                            sizeDiv.innerHTML = '<span data-dz-size></span>';
                            details.appendChild(sizeDiv);
                        }
                    }
                });
                
                // Handle file addition (preview mode)
                this.on("addedfile", function(file) {
                    // Add custom styling to file preview
                    const preview = file.previewElement;
                    preview.classList.add('dz-image-preview-custom');
                    
                    // Add file size info
                    const sizeInfo = preview.querySelector('.dz-size');
                    if (!sizeInfo) {
                        const details = preview.querySelector('.dz-details');
                        if (details) {
                            const sizeDiv = document.createElement('div');
                            sizeDiv.className = 'dz-size';
                            sizeDiv.innerHTML = '<span data-dz-size></span>';
                            details.appendChild(sizeDiv);
                        }
                    }
                    
                    // Show preview message
                    showPhotoMessage('info', 'Photos added to preview. Click "Upload Photos" to save them.');
                });
                
                // Handle successful upload
                this.on("successmultiple", function(files, response) {
                    showPhotoMessage('success', 'Photos uploaded successfully!');
                    
                    // Clear dropzone
                    this.removeAllFiles(true);
                    
                    // Reload existing photos
                    loadExistingPhotos(currentIssueId);
                    
                    // Close modal after successful upload
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('photoUploadModal'));
                        if (modal) modal.hide();
                    }, 1500);
                });
                
                // Handle upload errors
                this.on("errormultiple", function(files, response) {
                    let errorMessage = 'Upload failed. Please try again.';
                    
                    if (response && response.message) {
                        errorMessage = response.message;
                    } else if (response && response.errors) {
                        const errors = [];
                        Object.values(response.errors).forEach(errorArray => {
                            errors.push(...errorArray);
                        });
                        errorMessage = errors.join('<br>');
                    }
                    
                    showPhotoMessage('danger', errorMessage);
                });
                
                // Handle individual file errors
                this.on("error", function(file, errorMessage) {
                    showPhotoMessage('danger', errorMessage);
                });
                
                // Custom validation for total file size
                this.on("addedfiles", function(files) {
                    let totalSize = 0;
                    const maxTotalSize = 10 * 1024 * 1024; // 10MB
                    
                    files.forEach(file => {
                        totalSize += file.size;
                    });
                    
                    if (totalSize > maxTotalSize) {
                        showPhotoMessage('warning', 'Total file size exceeds 10MB limit. Please reduce the number of files or their size.');
                        files.forEach(file => {
                            this.removeFile(file);
                        });
                    }
                });
            }
        });
    }
    
    // Clear all files
    document.getElementById('clearPhotosBtn').addEventListener('click', function() {
        if (photoDropzone) {
            photoDropzone.removeAllFiles(true);
        }
    });
    
    // Upload photos when submit button is clicked
    document.getElementById('uploadPhotosBtn').addEventListener('click', function() {
        if (photoDropzone && photoDropzone.files.length > 0) {
            // Set the correct URL for upload
            photoDropzone.options.url = `/block-issues/${currentIssueId}/photos`;
            
            // Process the queue
            photoDropzone.processQueue();
        } else {
            showPhotoMessage('warning', 'Please select photos to upload.');
        }
    });
    
    // Debug function to test dropzone
    window.testDropzone = function() {
        if (photoDropzone) {
            showPhotoMessage('success', 'Dropzone is working correctly!');
        } else {
            showPhotoMessage('warning', 'Dropzone not initialized. Try opening the upload modal first.');
        }
    };
    
    // ========================================
    // ISSUE DELETE/ARCHIVE MODAL
    // ========================================
    
    // Global variable to store the issue ID for deletion
    let issueToDelete = null;
    
    /**
     * Shows the delete/archive confirmation modal for an issue
     * 
     * @param {number} issueId - The ID of the issue to delete/archive
     * @param {object} issueData - The issue data to display in confirmation
     */
    window.showDeleteIssueModal = function(issueId, issueData) {
        issueToDelete = issueId;
        
        const hasRelatedEntities = issueData.has_related_entities || false;
        const hasWorkOrders = issueData.has_work_orders || false;
        const hasSiteVisits = issueData.has_site_visits || false;
        const hasActions = issueData.has_actions || false;
        const workOrdersCount = issueData.work_orders_count || 0;
        const siteVisitsCount = issueData.site_visits_count || 0;
        const actionsCount = issueData.actions_count || 0;
        
        const actionText = hasRelatedEntities ? 'Archive' : 'Delete';
        const actionIcon = hasRelatedEntities ? 'ph-archive' : 'ph-trash';
        const actionColor = hasRelatedEntities ? 'warning' : 'danger';
        
        // Update modal header
        const $header = $('#deleteIssueModalHeader');
        $header.removeClass('bg-danger bg-warning text-white');
        $header.addClass(hasRelatedEntities ? 'bg-warning text-white' : 'bg-danger text-white');
        
        // Update modal title
        $('#deleteIssueModalLabel').html(`<i class="ph-warning me-2"></i>Confirm ${actionText} Issue`);
        
        // Update modal message
        const messageText = hasRelatedEntities 
            ? `Are you sure you want to archive <strong>${issueData.issue || issueData.ref_no || 'this issue'}</strong>?`
            : `Are you sure you want to permanently delete <strong>${issueData.issue || issueData.ref_no || 'this issue'}</strong>?`;
        $('#deleteIssueModalMessage').html(messageText);
        
        // Update alert message
        const $alert = $('#deleteIssueModalAlert');
        $alert.removeClass('alert-danger alert-warning');
        if (hasRelatedEntities) {
            $alert.addClass('alert-warning');
            
            // Build list of related entities
            const relatedEntities = [];
            if (hasWorkOrders) {
                relatedEntities.push(`${workOrdersCount} ${workOrdersCount === 1 ? 'work order' : 'work orders'}`);
            }
            if (hasSiteVisits) {
                relatedEntities.push(`${siteVisitsCount} ${siteVisitsCount === 1 ? 'site visit' : 'site visits'}`);
            }
            if (hasActions) {
                relatedEntities.push(`${actionsCount} ${actionsCount === 1 ? 'action' : 'actions'}`);
            }
            
            const entitiesText = relatedEntities.join(', ');
            $('#deleteIssueModalAlertMessage').html(
                `This issue contains <strong>${entitiesText}</strong>. ` +
                `<strong>The issue will be archived</strong> and can be restored later. The associated entities will remain in the database.`
            );
        } else {
            $alert.addClass('alert-danger');
            $('#deleteIssueModalAlertMessage').html(
                `This issue has no related entities (work orders, site visits, or actions). <strong>This action will permanently delete the issue</strong> and cannot be undone. All issue data and images will be permanently removed.`
            );
        }
        
        // Populate issue details
        let detailsHtml = `
            <div class="row">
                <div class="col-6"><strong>Ref No:</strong></div>
                <div class="col-6">${issueData.ref_no || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Issue:</strong></div>
                <div class="col-6">${issueData.issue || 'N/A'}</div>
            </div>
        `;
        
        if (hasRelatedEntities) {
            detailsHtml += '<div class="row mt-2"><div class="col-12"><strong>Related Entities:</strong></div></div>';
            if (hasWorkOrders) {
                detailsHtml += `
                    <div class="row">
                        <div class="col-6"><strong>Work Orders:</strong></div>
                        <div class="col-6"><span class="badge bg-warning">${workOrdersCount} ${workOrdersCount === 1 ? 'Work Order' : 'Work Orders'}</span></div>
                    </div>
                `;
            }
            if (hasSiteVisits) {
                detailsHtml += `
                    <div class="row">
                        <div class="col-6"><strong>Site Visits:</strong></div>
                        <div class="col-6"><span class="badge bg-info">${siteVisitsCount} ${siteVisitsCount === 1 ? 'Site Visit' : 'Site Visits'}</span></div>
                    </div>
                `;
            }
            if (hasActions) {
                detailsHtml += `
                    <div class="row">
                        <div class="col-6"><strong>Actions:</strong></div>
                        <div class="col-6"><span class="badge bg-secondary">${actionsCount} ${actionsCount === 1 ? 'Action' : 'Actions'}</span></div>
                    </div>
                `;
            }
        }
        
        $('#deleteIssueDetails').html(detailsHtml);
        
        // Update confirm button
        const $confirmBtn = $('#confirmDeleteIssueBtn');
        $confirmBtn.removeClass('btn-danger btn-warning');
        $confirmBtn.addClass(`btn-${actionColor}`);
        $confirmBtn.html(`<i class="${actionIcon} me-1"></i>Yes, ${actionText} Issue`);
        
        // Set up the confirm button to actually delete/archive
        $confirmBtn.off('click').on('click', function() {
            deleteIssue(issueId);
        });
        
        // Show the modal
        $('#deleteIssueModal').modal('show');
    };

    /**
     * Deletes/archives an issue via AJAX
     */
    function deleteIssue(issueId) {
        if (!issueId) {
            return;
        }
        
        // Show loading state
        const $confirmBtn = $('#confirmDeleteIssueBtn');
        const originalText = $confirmBtn.html();
        $confirmBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Processing...').prop('disabled', true);
        
        $.ajax({
            url: `/block-issues/${issueId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                // Hide the modal
                $('#deleteIssueModal').modal('hide');
                
                if (response && response.success) {
                    // Show success message
                    const message = response.message || 'Issue processed successfully';
                    alert(message);
                    
                    // Reload the page to reflect changes
                    location.reload();
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            error: function(xhr) {
                $('#deleteIssueModal').modal('hide');
                
                let errorMessage = 'An error occurred while processing the request.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            },
            complete: function() {
                // Restore button state
                $confirmBtn.html(originalText).prop('disabled', false);
            }
        });
    }
    </script>
@endsection

@section('script')
<x-datatable-scripts />

<!-- Dropzone JS -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<script>
$(document).ready(function() {
    var table;
    
    try {
        // Check if DataTables is available
        if (!$.fn.DataTable) {
            console.error('DataTables library not loaded');
            $('#issues-table-loading').addClass('d-none');
            $('#blockIssuesTable').show();
            return;
        }
        
        // Initialize DataTable
        table = $('#blockIssuesTable').DataTable({
            responsive: true,
            scrollX: false,
            autoWidth: false,
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
            order: [[7, 'desc']], // default sort by Reported Date descending
            columnDefs: [
                { targets: [8], orderable: false }, // Actions column
                { targets: [0], width: '10%' },  // Issue ID
                { targets: [1], width: '18%' },  // Title
                { targets: [2], width: '12%' },  // Block
                { targets: [3], width: '12%' },  // Unit
                { targets: [4], width: '10%' },  // Type
                { targets: [5], width: '10%' },  // Priority
                { targets: [6], width: '10%' },  // Status
                { targets: [7], width: '13%' },  // Reported Date
                { targets: [8], width: '10%' }   // Actions
            ],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "Search block issues:",
                lengthMenu: "Show _MENU_ block issues per page",
                info: "Showing _START_ to _END_ of _TOTAL_ block issues",
                infoEmpty: "Showing 0 to 0 of 0 block issues",
                infoFiltered: "(filtered from _MAX_ total block issues)",
                zeroRecords: "No block issues found",
                paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
            },
            initComplete: function() {
                // Hide loader and show table
                $('#issues-table-loading').addClass('d-none');
                $('#blockIssuesTable').show();
                
                // Style the search box
                $('.dataTables_filter input')
                    .addClass('form-control')
                    .removeClass('mb-3')
                    .css({
                        'width': '300px',
                        'height': '38px',
                        'font-size': '14px',
                        'margin-left': '10px',
                        'margin-bottom': '0 !important'
                    });
                
                // Style the page length dropdown
                $('.dataTables_length select')
                    .addClass('form-select')
                    .css({
                        'width': 'auto',
                        'height': '38px',
                        'font-size': '14px',
                        'margin': '0 10px'
                    });
                
                // Ensure labels and inputs are on the same line
                $('.dataTables_length label').css({
                    'display': 'flex',
                    'align-items': 'center',
                    'margin-bottom': '0'
                });
                
                $('.dataTables_filter label').css({
                    'display': 'flex',
                    'align-items': 'center',
                    'margin-bottom': '0'
                });
            }
        });
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        // Fallback: hide loader and show table anyway
        $('#issues-table-loading').addClass('d-none');
        $('#blockIssuesTable').show();
    }
    
    // Fallback timeout - if table is still not visible after 3 seconds, force show it
    setTimeout(function() {
        if ($('#blockIssuesTable').is(':hidden')) {
            console.warn('DataTable initialization timeout - forcing table display');
            $('#issues-table-loading').addClass('d-none');
            $('#blockIssuesTable').show();
        }
    }, 3000);

    // Toggle search panel
    $('#toggleSearchBtn').on('click', function() {
        $('#searchIssuesPanel').slideToggle();
    });

    // Close search panel
    $('#closeSearchHeaderBtn').on('click', function() {
        $('#searchIssuesPanel').slideUp();
    });

    // Search functionality
    $('#searchIssuesBtn').on('click', function() {
        if (!table) return;
        
        var blockId = $('#search_block_id').val();
        var status = $('#search_status').val();
        var priority = $('#search_priority').val();
        var keyword = $('#search_keyword').val();

        // Build search query
        var searchQuery = '';
        if (keyword) searchQuery += keyword;

        table.search(searchQuery).draw();
        $('#searchIssuesPanel').slideUp();
    });

    // Clear search
    $('#clearSearchBtn').on('click', function() {
        if (!table) return;
        
        $('#searchIssuesForm')[0].reset();
        table.search('').draw();
    });

    // Show all
    $('#showAllBtn').on('click', function() {
        if (!table) return;
        
        $('#searchIssuesForm')[0].reset();
        table.search('').draw();
        $('#searchIssuesPanel').slideUp();
    });

    // Update export button states based on table data
    function updateExportButtons() {
        var hasData = table.data().count() > 0;
        $('#exportPdfBtn, #exportExcelBtn, #exportPrintBtn').toggleClass('disabled', !hasData);
    }

    // Update export buttons on table draw
    table.on('draw', function() {
        updateExportButtons();
    });

    // Initial update
    updateExportButtons();
});
</script>
@endsection 