<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">
                Block Issues
            </h6>
            <button class="btn btn-outline-primary btn-sm" id="toggleSearchBtn" title="Search & Filter Issues">
                <i class="ph-funnel"></i>
            </button>
            <div class="d-flex align-items-center gap-2">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('export.pdf', 'block-issues') }}?block_id={{ $block->id }}" 
                       id="exportPdfBtn"
                       class="btn btn-outline-danger btn-sm {{ (!$block->issues || $block->issues->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->issues || $block->issues->count() === 0) ? 'No data to export' : 'Export to PDF' }}"
                       {{ (!$block->issues || $block->issues->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-pdf"></i>
                    </a>
                    <a href="{{ route('export.excel', 'block-issues') }}?block_id={{ $block->id }}" 
                       id="exportExcelBtn"
                       class="btn btn-outline-success btn-sm {{ (!$block->issues || $block->issues->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->issues || $block->issues->count() === 0) ? 'No data to export' : 'Export to Excel' }}"
                       {{ (!$block->issues || $block->issues->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-xls"></i>
                    </a>
                    <a href="{{ route('export.print', 'block-issues') }}?block_id={{ $block->id }}" 
                       id="exportPrintBtn"
                       class="btn btn-outline-secondary btn-sm {{ (!$block->issues || $block->issues->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->issues || $block->issues->count() === 0) ? 'No data to export' : 'Print' }}" 
                       target="_blank"
                       {{ (!$block->issues || $block->issues->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-printer"></i>
                    </a>
                </div>
                @if (empty($viewOnly))
                    <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#issueModal" onclick="openIssueModal('add')">
                        <i class="ph-plus align-bottom me-1"></i> Report Issue
                    </button>
                @endif
            </div>
        </div>
        
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
                <form id="searchIssuesForm">
                    <div class="row">
                        <!-- Unit Selection -->
                        <div class="col-md-4 mb-3">
                            <label for="search_unit" class="form-label">Unit</label>
                            <select class="form-select" id="search_unit" name="block_unit_id">
                                <option value="">All Units</option>
                                @foreach ($block->units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_code }} - {{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="type" value="api">
                        <input type="hidden" name="block_id" value="{{ $block->id }}">
                        <!-- State Selection -->
                        <div class="col-md-4 mb-3">
                            <label for="search_state" class="form-label">Status</label>
                            <select class="form-select" id="search_state" name="status">
                                <option value="">All Statuses</option>
                                <option value="1">Open</option>
                                <option value="2">In Progress</option>
                                <option value="3">Resolved</option>
                                <option value="4">Closed</option>
                                <option value="5">On Hold</option>
                            </select>
                        </div>

                        <!-- Type Selection -->
                        <div class="col-md-4 mb-3">
                            <label for="search_type" class="form-label">{{ __('translation.issue-category') }}</label>
                            <select class="form-select" id="search_type" name="issue_type">
                                <option value="">All Types</option>
                                @foreach ($issueTypes as $issueType)
                                    <option value="{{ $issueType->name }}">
                                        {{ ucfirst(str_replace('_', ' ', $issueType->name)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Priority Selection -->
                        <div class="col-md-6 mb-3">
                            <label for="search_priority" class="form-label">Priority</label>
                            <select class="form-select" id="search_priority" name="priority">
                                <option value="">All Priorities</option>
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
        
        <div class="table-responsive">
            <table id="blockIssuesTable" class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Issue ID</th>
                        <th>Title</th>
                        <th>Unit</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Reported Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($block->issues && $block->issues->count() > 0)
                        @foreach($block->issues as $issue)
                            <tr>
                                <td>
                                    <a href="{{ route('block-issues.show', $issue) }}">
                                        <b>#{{ $issue->ref_no }}</b>
                                    </a>
                                </td>
                                <td>{{ $issue->issue ?? 'N/A' }}</td>
                                <td>
                                    @if ($issue->blockUnit)
                                        <span class="badge bg-secondary">{{ $issue->blockUnit->unit_name ?? 'N/A' }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($issue->issue_type)
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($issue->priority_id == 1)
                                        <span class="badge bg-success">Low</span>
                                    @elseif($issue->priority_id == 2)
                                        <span class="badge bg-info">Normal</span>
                                    @elseif($issue->priority_id == 3)
                                        <span class="badge bg-warning">High</span>
                                    @elseif($issue->priority_id == 4)
                                        <span class="badge bg-danger">Urgent</span>
                                    @elseif($issue->priority_id == 5)
                                        <span class="badge bg-dark">Critical</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($issue->issue_status_id == 1)
                                        <span class="badge bg-warning">Open</span>
                                    @elseif($issue->issue_status_id == 2)
                                        <span class="badge bg-info">In Progress</span>
                                    @elseif($issue->issue_status_id == 3)
                                        <span class="badge bg-success">Resolved</span>
                                    @elseif($issue->issue_status_id == 4)
                                        <span class="badge bg-secondary">Closed</span>
                                    @elseif($issue->issue_status_id == 5)
                                        <span class="badge bg-danger">On Hold</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $issue->created_at ? $issue->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editIssue({{ $issue->id }})" title="Edit Issue">
                                        <i class="ph-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="openPhotoUploadModal({{ $issue->id }})" title="Upload Photos">
                                        <i class="ph-camera"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="showDeleteConfirmation({{ $issue->id }}, {
                                        ref_no: '{{ $issue->ref_no }}',
                                        issue: '{{ $issue->issue }}',
                                        priority: '{{ $issue->priority_id }}',
                                        status: '{{ $issue->issue_status_id }}'
                                    })" title="Delete Issue">
                                        <i class="ph-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('blocks.tabs.edit.issues.modals')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
<!-- Dropzone CSS -->
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<!-- AutoComplete.js CSS (overridden globally in head-css.blade.php) -->

<style>
/* Export button disabled state styling */
.btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.btn.disabled:hover {
    opacity: 0.5;
}

/* DataTable search box styling */
.dataTables_filter {
    margin-bottom: 1rem;
}

.dataTables_filter input {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    margin-left: 0.5rem;
}

.dataTables_filter input:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Custom search input styling */
.custom-search-input {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
    transition: all 0.3s ease;
}

.custom-search-input:focus {
    background-color: #fff;
    border-left-color: #0056b3;
    box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
}

/* Custom page length dropdown styling */
.custom-page-length-select {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-left: 4px solid #28a745;
    transition: all 0.3s ease;
}

.custom-page-length-select:focus {
    background-color: #fff;
    border-left-color: #1e7e34;
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
}

/* Ensure proper spacing for DataTable wrapper */
.dataTables_wrapper {
    margin-top: 1rem;
}

/* Contact details container styling */
#contact_details_container {
    transition: all 0.3s ease;
}

#contact_details_container input:focus,
#contact_details_container textarea:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

#contact_details_container .form-text {
    font-size: 0.875em;
    margin-top: 0.25rem;
}

.is-valid {
    border-color: #198754 !important;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25) !important;
}

.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
}

/* Large modal styling */
#issueModal .modal-body {
    max-height: 80vh;
    overflow-y: auto;
    padding: 2rem;
}

#issueModal .form-label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

#issueModal .form-control,
#issueModal .form-select {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

/* Additional large modal styling */
#issueModal .modal-dialog {
    margin: 1rem auto;
}

#issueModal .modal-content {
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

#issueModal .row {
    margin-bottom: 1rem;
}

#issueModal .col-md-4,
#issueModal .col-md-6,
#issueModal .col-12 {
    margin-bottom: 0.5rem;
}

/* Custom Dropzone Styling */
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
@endpush

@push('scripts')
<!-- jQuery and DataTables Dependencies -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
<!-- Dropzone JS -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
// Verify Dropzone is loaded
if (typeof Dropzone === 'undefined') {
    console.error('Dropzone failed to load from CDN');
    // Fallback to local or alternative CDN
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"><\/script>');
}
</script>

<!-- Global Configuration -->
<script>
    // Global configuration for the issues module
    window.blockId = {{ $block->id }};
    window.csrfToken = '{{ csrf_token() }}';
    window.routes = {
        blockIssues: {
            store: '{{ route("block-issues.store") }}',
            update: '{{ route("block-issues.update", ":id") }}'
        }
    };
    
    // Define editIssue function early so it's available for initial HTML buttons
    window.editIssue = function(id) {
        // This will be overridden by the full function definition in scripts.blade.php
        if (typeof window.openIssueModal === 'function') {
            window.openIssueModal('edit', id);
        }
    };
    
    // Define showDeleteConfirmation function early so it's available for initial HTML buttons
    window.issuesShowDeleteConfirmation = function(issueId, issueData) {
        if (typeof window.issuesConfirmDeletion === 'function') {
            window.issuesConfirmDeletion(issueId, issueData);
        }
    };
    
    // Define openPhotoUploadModal function early so it's available for initial HTML buttons
    window.openPhotoUploadModal = function(issueId) {
        // This will be overridden by the full function definition in scripts.blade.php
        // Placeholder function to prevent errors before full implementation loads
        console.log('openPhotoUploadModal placeholder called with ID:', issueId);
    };
</script>

<!-- Issues JavaScript -->
@include('blocks.tabs.edit.issues.scripts')
@endpush
