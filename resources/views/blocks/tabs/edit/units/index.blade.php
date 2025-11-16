<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Unit Information</h6>
            <div class="d-flex align-items-center gap-2">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('export.pdf', 'block-units') }}?block_id={{ $block->id }}" 
                       id="exportPdfBtn"
                       class="btn btn-outline-danger btn-sm {{ (!$block->units || $block->units->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->units || $block->units->count() === 0) ? 'No data to export' : 'Export to PDF' }}"
                       {{ (!$block->units || $block->units->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-pdf"></i>
                    </a>
                    <a href="{{ route('export.excel', 'block-units') }}?block_id={{ $block->id }}" 
                       id="exportExcelBtn"
                       class="btn btn-outline-success btn-sm {{ (!$block->units || $block->units->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->units || $block->units->count() === 0) ? 'No data to export' : 'Export to Excel' }}"
                       {{ (!$block->units || $block->units->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-xls"></i>
                    </a>
                    <a href="{{ route('export.print', 'block-units') }}?block_id={{ $block->id }}" 
                       id="exportPrintBtn"
                       class="btn btn-outline-secondary btn-sm {{ (!$block->units || $block->units->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->units || $block->units->count() === 0) ? 'No data to export' : 'Print' }}" 
                       target="_blank"
                       {{ (!$block->units || $block->units->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-printer"></i>
                    </a>
                </div>
                <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#unitModal" onclick="openUnitModal('add')">
                    <i class="ph-plus align-bottom me-1"></i> Add Unit
                </button>
                <button class="btn btn-success custom-toggle" data-bs-toggle="modal" data-bs-target="#uploadUnitModal">
                    <i class="ph-upload align-bottom me-1"></i> Upload Unit
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="blockUnitsTable" class="table table-bordered table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Unit Code</th>
                        <th>Unit Name</th>
                        <th>Type</th>
                        <th>Owner's Name</th>
                        <th>Email</th>
                        <th>Resident</th>
                        <th>Mobile</th>
                        <th>Letting Agent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($block->units && $block->units->count() > 0)
                        @foreach($block->units as $unit)
                            <tr>
                                <td>{{ $unit->unit_code ?? 'N/A' }}</td>
                                <td>{{ $unit->unit_name ?? 'N/A' }}</td>
                                <td>{{ $unit->unitType->name ?? 'N/A' }}</td>
                                <td>{{ $unit->owners_name ?? 'N/A' }}</td>
                                <td>{{ $unit->email ?? 'N/A' }}</td>
                                <td>{{ $unit->resident ? 'Yes' : 'No' }}</td>
                                <td>{{ $unit->mobile_no ?? 'N/A' }}</td>
                                <td>{{ $unit->letting_agent ?? 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewUnit({{ $unit->id }})" title="View Unit">
                                        <i class="ph-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editUnit({{ $unit->id }})" title="Edit Unit">
                                        <i class="ph-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="unitShowDeleteConfirmation({{ $unit->id }}, {
                                        unit_code: '{{ $unit->unit_code }}',
                                        unit_name: '{{ $unit->unit_name }}',
                                        owners_name: '{{ $unit->owners_name }}',
                                        unit_type: { name: '{{ $unit->unitType->name ?? 'N/A' }}' }
                                    })" title="Delete Unit">
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

@include('blocks.tabs.edit.units.modals')

@push('styles')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<!-- DataTables Responsive CSS -->
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- DataTables Buttons CSS -->
<link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
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

/* Force DataTables sort icons to display */
table.dataTable thead > tr > th.sorting:before,
table.dataTable thead > tr > th.sorting_asc:before,
table.dataTable thead > tr > th.sorting_desc:before,
table.dataTable thead > tr > th.sorting:after,
table.dataTable thead > tr > th.sorting_asc:after,
table.dataTable thead > tr > th.sorting_desc:after {
    opacity: 0.3 !important;
    display: block !important;
}

table.dataTable thead > tr > th.sorting_asc:before,
table.dataTable thead > tr > th.sorting_desc:after {
    opacity: 1 !important;
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

<!-- Global Configuration -->
<script>
    // Global configuration for the units module
    window.blockId = {{ $block->id }};
    window.csrfToken = '{{ csrf_token() }}';
    window.routes = {
        blockUnits: {
            store: '{{ route("block-units.store") }}',
            upload: '{{ route("block-units.upload") }}'
        }
    };
    
    // Define editUnit function early so it's available for initial HTML buttons
    window.editUnit = function(id) {
        // This will be overridden by the full function definition in scripts.blade.php
        if (typeof openUnitModal === 'function') {
            openUnitModal('edit', id);
        }
    };
    
    // Proxy functions to ensure initial HTML buttons work
    window.unitShowDeleteConfirmation = function(unitId, unitData) {
        if (typeof window.unitConfirmDeletion === 'function') {
            window.unitConfirmDeletion(unitId, unitData);
        }
    };
</script>

<!-- Units JavaScript -->
@include('blocks.tabs.edit.units.scripts')
@endpush
