<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Building Information</h6>
            <div class="d-flex align-items-center gap-2">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('export.pdf', 'block-buildings') }}?block_id={{ $block->id }}" 
                       id="exportPdfBtn"
                       class="btn btn-outline-danger btn-sm {{ (!$block->buildings || $block->buildings->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->buildings || $block->buildings->count() === 0) ? 'No data to export' : 'Export to PDF' }}"
                       {{ (!$block->buildings || $block->buildings->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-pdf"></i>
                    </a>
                    <a href="{{ route('export.excel', 'block-buildings') }}?block_id={{ $block->id }}" 
                       id="exportExcelBtn"
                       class="btn btn-outline-success btn-sm {{ (!$block->buildings || $block->buildings->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->buildings || $block->buildings->count() === 0) ? 'No data to export' : 'Export to Excel' }}"
                       {{ (!$block->buildings || $block->buildings->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-xls"></i>
                    </a>
                    <a href="{{ route('export.print', 'block-buildings') }}?block_id={{ $block->id }}" 
                       id="exportPrintBtn"
                       class="btn btn-outline-secondary btn-sm {{ (!$block->buildings || $block->buildings->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->buildings || $block->buildings->count() === 0) ? 'No data to export' : 'Print' }}" 
                       target="_blank"
                       {{ (!$block->buildings || $block->buildings->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-printer"></i>
                    </a>
                </div>
                <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#buildingModal" onclick="openBuildingModal('add')">
                    <i class="ph-plus align-bottom me-1"></i> Add Building
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="blockBuildingsTable" class="table table-bordered table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Building Name</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Roof Type</th>
                        <th>No of Lifts</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($block->buildings && $block->buildings->count() > 0)
                        @foreach($block->buildings as $building)
                            <tr>
                                <td>{{ $building->name ?? 'N/A' }}</td>
                                <td>{{ $building->buildingType->name ?? 'N/A' }}</td>
                                <td>{{ $building->floor_no ?? 'N/A' }}</td>
                                <td>{{ $building->roof_type ?? 'N/A' }}</td>
                                <td>{{ $building->no_lift ?? 'N/A' }}</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editBuilding({{ $building->id }})" title="Edit Building">
                                        <i class="ph-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="buildingShowDeleteConfirmation({{ $building->id }}, {
                                        name: '{{ $building->name }}',
                                        description: '{{ Str::limit($building->description ?? 'No description provided', 30) }}',
                                        status: '{{ $building->status ?? 'N/A' }}'
                                    })" title="Delete Building">
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

@include('blocks.tabs.edit.buildings.modals')

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
    // Global configuration for the buildings module
    window.blockId = {{ $block->id }};
    window.csrfToken = '{{ csrf_token() }}';
    window.routes = {
        blockBuildings: {
            store: '{{ route("block-buildings.store") }}',
            update: '{{ route("block-buildings.update", ":id") }}'
        }
    };
    
    // Define editBuilding function early so it's available for initial HTML buttons
    window.editBuilding = function(id) {
        // This will be overridden by the full function definition in scripts.blade.php
        // For now, just show an alert to indicate the function is working
        console.log('Edit building called with ID:', id);
    };
    
    // Define showDeleteConfirmation function early so it's available for initial HTML buttons
    window.buildingShowDeleteConfirmation = function(buildingId, buildingData) {
        if (typeof window.buildingConfirmDeletion === 'function') {
            window.buildingConfirmDeletion(buildingId, buildingData);
        }
    };
</script>

<!-- Buildings JavaScript -->
@include('blocks.tabs.edit.buildings.scripts')
@endpush
