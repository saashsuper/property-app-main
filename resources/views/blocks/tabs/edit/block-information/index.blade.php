
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Block Information</h6>
            <div class="d-flex align-items-center gap-2">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('export.pdf', 'block-information') }}?block_id={{ $block->id }}" 
                       id="exportPdfBtn"
                       class="btn btn-outline-danger btn-sm {{ (!$blockInformation || $blockInformation->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$blockInformation || $blockInformation->count() === 0) ? 'No data to export' : 'Export to PDF' }}"
                       {{ (!$blockInformation || $blockInformation->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-pdf"></i>
                    </a>
                    <a href="{{ route('export.excel', 'block-information') }}?block_id={{ $block->id }}" 
                       id="exportExcelBtn"
                       class="btn btn-outline-success btn-sm {{ (!$blockInformation || $blockInformation->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$blockInformation || $blockInformation->count() === 0) ? 'No data to export' : 'Export to Excel' }}"
                       {{ (!$blockInformation || $blockInformation->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-xls"></i>
                    </a>
                    <a href="{{ route('export.print', 'block-information') }}?block_id={{ $block->id }}" 
                       id="exportPrintBtn"
                       class="btn btn-outline-secondary btn-sm {{ (!$blockInformation || $blockInformation->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$blockInformation || $blockInformation->count() === 0) ? 'No data to export' : 'Print' }}" 
                       target="_blank"
                       {{ (!$blockInformation || $blockInformation->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-printer"></i>
                    </a>
                </div>
                <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#blockInformationModal" onclick="openBlockInformationModal('add')">
                    <i class="ph-plus align-bottom me-1"></i> Add Block Information
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="blockInformationTable" class="table table-bordered table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Information Type</th>
                        <th>Description</th>
                        <th>Added Date</th>
                        <th>Added By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($blockInformation && $blockInformation->count() > 0)
                        @foreach($blockInformation as $info)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $info->information_type ? $info->information_type->name : 'N/A' }}</span>
                                </td>
                                <td>{{ Str::limit($info->description ?? 'No description provided', 50) }}</td>
                                <td>{{ $info->created_at ? $info->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    {{ $info->creator ? $info->creator->name : 'N/A' }}
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editBlockInformation({{ $info->id }})" title="Edit Block Information">
                                        <i class="ph-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewBlockInformationDetails({{ $info->id }})" title="View Details">
                                        <i class="ph-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="blockInformationShowDeleteConfirmation({{ $info->id }}, {
                                        type: '{{ $info->information_type->name ?? 'N/A' }}',
                                        description: '{{ Str::limit($info->description ?? 'No description', 30) }}',
                                        added_date: '{{ $info->created_at ? $info->created_at->format('M d, Y') : 'N/A' }}',
                                        added_by: '{{ $info->creator->name ?? 'N/A' }}'
                                    })" title="Delete Block Information">
                                        <i class="ph-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">No block information available.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('blocks.tabs.edit.block-information.modals')

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
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.custom-search-input:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Custom page length dropdown styling */
.custom-page-length-select {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.custom-page-length-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Ensure proper spacing for DataTable wrapper */
.dataTables_wrapper {
    margin-top: 1rem;
}

/* Block information specific styling */
#blockInformationTable th:nth-child(2),
#blockInformationTable td:nth-child(2) {
    min-width: 200px;
    max-width: none;
}
</style>
@endpush

@push('scripts')
<!-- DataTables Dependencies (jQuery already loaded in layout) -->
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
    window.blockId = {{ $block->id }};
    // CSRF token is available via meta tag: $('meta[name="csrf-token"]').attr('content')
    window.routes = {
        blockInformation: {
            store: '{{ route("block-information.store") }}',
            update: '{{ route("block-information.update", ":id") }}',
            show: '{{ route("block-information.show", ":id") }}',
            destroy: '{{ route("block-information.destroy", ":id") }}',
            getByBlock: '{{ route("block-information.get-by-block", $block->id) }}'
        }
    };
    // Define delete confirmation function early so it's available for initial HTML buttons
    window.blockInformationShowDeleteConfirmation = function(infoId, infoData) {
        if (typeof window.blockInformationConfirmDeletion === 'function') {
            window.blockInformationConfirmDeletion(infoId, infoData);
        }
    };
</script>

<!-- Block Information JavaScript -->
@include('blocks.tabs.edit.block-information.scripts')
@endpush
