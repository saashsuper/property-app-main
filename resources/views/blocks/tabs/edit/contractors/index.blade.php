<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Contractor Information</h6>
            <div class="d-flex align-items-center gap-2">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('export.pdf', 'block-contractors') }}?block_id={{ $block->id }}" 
                       id="exportPdfBtn"
                       class="btn btn-outline-danger btn-sm {{ (!$block->contractors || $block->contractors->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->contractors || $block->contractors->count() === 0) ? 'No data to export' : 'Export to PDF' }}"
                       {{ (!$block->contractors || $block->contractors->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-pdf"></i>
                    </a>
                    <a href="{{ route('export.excel', 'block-contractors') }}?block_id={{ $block->id }}" 
                       id="exportExcelBtn"
                       class="btn btn-outline-success btn-sm {{ (!$block->contractors || $block->contractors->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->contractors || $block->contractors->count() === 0) ? 'No data to export' : 'Export to Excel' }}"
                       {{ (!$block->contractors || $block->contractors->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-xls"></i>
                    </a>
                    <a href="{{ route('export.print', 'block-contractors') }}?block_id={{ $block->id }}" 
                       id="exportPrintBtn"
                       class="btn btn-outline-secondary btn-sm {{ (!$block->contractors || $block->contractors->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$block->contractors || $block->contractors->count() === 0) ? 'No data to export' : 'Print' }}" 
                       target="_blank"
                       {{ (!$block->contractors || $block->contractors->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-printer"></i>
                    </a>
                </div>
                <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#contractorModal" onclick="openContractorModal('add')">
                    <i class="ph-plus align-bottom me-1"></i> Add Contractor
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="blockContractorsTable" class="table table-bordered table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Contractor Name</th>
                        <th>Email</th>
                        <th>Contract Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($block->contractors && $block->contractors->count() > 0)
                        @foreach($block->contractors as $contractor)
                            <tr>
                                <td>{{ $contractor->contractor->name ?? 'N/A' }}</td>
                                <td>{{ $contractor->contractor->email ?? 'N/A' }}</td>
                                <td>{{ $contractor->contractorType->name ?? 'N/A' }}</td>
                                <td>
                                    @if($contractor->status == 1)
                                        <span class="badge bg-success">Default</span>
                                    @else
                                        <span class="badge bg-info">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editContractor({{ $contractor->id }})" title="Edit Contractor">
                                        <i class="ph-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="contractorShowDeleteConfirmation({{ $contractor->id }}, {
                                        name: '{{ $contractor->name }}',
                                        company: '{{ $contractor->company_name }}',
                                        phone: '{{ $contractor->phone }}',
                                        email: '{{ $contractor->email }}'
                                    })" title="Delete Contractor">
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

@include('blocks.tabs.edit.contractors.modals')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
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
    // Global configuration for the contractors module
    window.blockId = {{ $block->id }};
    window.csrfToken = '{{ csrf_token() }}';
    window.routes = {
        blockContractors: {
            store: '{{ route("block-contractors.store") }}',
            update: '{{ route("block-contractors.update", ":id") }}'
        }
    };
    
    // Define editContractor function early so it's available for initial HTML buttons
    window.editContractor = function(id) {
        // This will be overridden by the full function definition in scripts.blade.php
        // For now, just show an alert to indicate the function is working
        console.log('Edit contractor called with ID:', id);
    };
    
    // Define showDeleteConfirmation function early so it's available for initial HTML buttons
    window.contractorShowDeleteConfirmation = function(contractorId, contractorData) {
        if (typeof window.contractorConfirmDeletion === 'function') {
            window.contractorConfirmDeletion(contractorId, contractorData);
        }
    };
</script>

<!-- Contractors JavaScript -->
@include('blocks.tabs.edit.contractors.scripts')
@endpush
