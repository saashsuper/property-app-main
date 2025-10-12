<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Site Visit History</h6>
            <div class="d-flex align-items-center gap-2">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('export.pdf', 'block-visits') }}?block_id={{ $block->id }}" 
                       id="exportPdfBtn"
                       class="btn btn-outline-danger btn-sm {{ (!$block->blockVisits || $block->blockVisits->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->blockVisits || $block->blockVisits->count() === 0) ? 'No data to export' : 'Export to PDF' }}"
                       {{ (!$block->blockVisits || $block->blockVisits->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-pdf"></i>
                    </a>
                    <a href="{{ route('export.excel', 'block-visits') }}?block_id={{ $block->id }}" 
                       id="exportExcelBtn"
                       class="btn btn-outline-success btn-sm {{ (!$block->blockVisits || $block->blockVisits->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->blockVisits || $block->blockVisits->count() === 0) ? 'No data to export' : 'Export to Excel' }}"
                       {{ (!$block->blockVisits || $block->blockVisits->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-xls"></i>
                    </a>
                    <a href="{{ route('export.print', 'block-visits') }}?block_id={{ $block->id }}" 
                       id="exportPrintBtn"
                       class="btn btn-outline-secondary btn-sm {{ (!$block->blockVisits || $block->blockVisits->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->blockVisits || $block->blockVisits->count() === 0) ? 'No data to export' : 'Print' }}" 
                       target="_blank"
                       {{ (!$block->blockVisits || $block->blockVisits->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-printer"></i>
                    </a>
                </div>
                <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#siteVisitModal" onclick="openSiteVisitModal('add')">
                    <i class="ph-plus align-bottom me-1"></i> Assign Site Visit
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="blockSiteVisitsTable" class="table table-bordered table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Visit Date</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($block->blockVisits && $block->blockVisits->count() > 0)
                        @foreach($block->blockVisits as $visit)
                            <tr>
                                <td>
                                    <a href="#" class="text-primary fw-bold view-site-visit-details" data-visit-id="{{ $visit->id }}" style="text-decoration: none;">
                                        {{ $visit->ref_no ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>{{ $visit->scheduled_date_time ? \Carbon\Carbon::parse($visit->scheduled_date_time)->format('M d, Y H:i') : 'N/A' }}</td>
                                <td>
                                    @if($visit->team && $visit->team->count() > 0)
                                        {{ $visit->team->first()->user->name ?? 'N/A' }}
                                    @else
                                        {{ $visit->createdByUser->name ?? 'N/A' }}
                                    @endif
                                </td>
                                <td>
                                    @if($visit->end_date_time)
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($visit->start_date_time)
                                        <span class="badge bg-warning">In Progress</span>
                                    @else
                                        <span class="badge bg-info">Scheduled</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($visit->notes, 50) ?? 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editSiteVisit({{ $visit->id }})" title="Edit Site Visit">
                                        <i class="ph-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="blockSiteVisitShowDeleteConfirmation({{ $visit->id }}, {
                                        ref_no: '{{ $visit->ref_no ?? 'N/A' }}',
                                        visit_date: '{{ $visit->scheduled_date_time ? \Carbon\Carbon::parse($visit->scheduled_date_time)->format('M d, Y H:i') : 'N/A' }}',
                                        user: '{{ $visit->team && $visit->team->count() > 0 ? $visit->team->first()->user->name ?? 'N/A' : $visit->createdByUser->name ?? 'N/A' }}',
                                        status: '{{ $visit->end_date_time ? 'Completed' : ($visit->start_date_time ? 'In Progress' : 'Scheduled') }}'
                                    })" title="Delete Site Visit">
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

@include('blocks.tabs.edit.site-visits.modals')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
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

/* Site visit specific styling */
#blockSiteVisitsTable th:nth-child(5),
#blockSiteVisitsTable td:nth-child(5) {
    min-width: 200px;
    max-width: none;
}
</style>
@endpush

@push('scripts')
<!-- jQuery and DataTables Dependencies -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

<!-- Global Configuration -->
<script>
    // Global configuration for the site visits module
    window.blockId = {{ $block->id }};
    window.csrfToken = '{{ csrf_token() }}';
    window.routes = {
        blockSiteVisits: {
            store: '{{ route("block-visits.store") }}',
            update: '{{ route("block-visits.update", ":id") }}'
        }
    };
    
    // Define editSiteVisit function early so it's available for initial HTML buttons
    window.editSiteVisit = function(id) {
        // This will be overridden by the full function definition in scripts.blade.php
        console.log('Edit site visit called with ID:', id);
    };
    
    // Define delete confirmation function early so it's available for initial HTML buttons
    window.blockSiteVisitShowDeleteConfirmation = function(visitId, visitData) {
        if (typeof window.blockSiteVisitConfirmDeletion === 'function') {
            window.blockSiteVisitConfirmDeletion(visitId, visitData);
        }
    };
</script>

<!-- Site Visits JavaScript -->
@include('blocks.tabs.edit.site-visits.scripts')
@endpush
