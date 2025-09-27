@extends('layouts.master')

@section('title') Blocks @endsection

@section('css')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<!-- DataTables Responsive CSS -->
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- DataTables Buttons CSS -->
<link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />

<style>
/* Custom pagination styling inspired by DataTables */
.pagination {
    display: flex;
    padding-left: 0;
    list-style: none;
    border-radius: 0.375rem;
    margin: 0;
}

.page-item {
    margin: 0 2px;
}

.page-link {
    position: relative;
    display: block;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.25;
    color: #6c757d;
    background-color: #fff;
    border: 1px solid #dee2e6;
    text-decoration: none;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.page-link:hover {
    z-index: 2;
    color: #495057;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-link:focus {
    z-index: 3;
    color: #495057;
    background-color: #e9ecef;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.page-item:first-child .page-link {
    margin-left: 0;
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.page-item:last-child .page-link {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

.page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

/* DataTables-inspired button styling */
.dt-button {
    position: relative;
    display: inline-block;
    box-sizing: border-box;
    margin: 0 2px;
    padding: 0.5rem 1rem;
    border: 1px solid rgba(0, 0, 0, 0.3);
    border-radius: 0.25rem;
    cursor: pointer;
    font-size: 0.875rem;
    line-height: 1.5;
    color: #212529;
    white-space: nowrap;
    overflow: hidden;
    background-color: #f8f9fa;
    text-decoration: none;
    outline: none;
    transition: all 0.15s ease-in-out;
}

.dt-button:hover:not(.disabled) {
    border-color: #6c757d;
    background-color: #e9ecef;
    color: #495057;
}

.dt-button.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

.dt-button.disabled {
    cursor: default;
    opacity: 0.6;
    pointer-events: none;
}

/* Table column width management */
#blocks-table th:nth-child(1) { width: 25%; } /* Name */
#blocks-table th:nth-child(2) { width: 20%; } /* Management Company */
#blocks-table th:nth-child(3) { width: 15%; } /* Block Manager */
#blocks-table th:nth-child(4) { width: 25%; } /* Address */
#blocks-table th:nth-child(5) { width: 8%; }  /* Units */
#blocks-table th:nth-child(6) { width: 8%; }  /* Issues */
#blocks-table th:nth-child(7) { width: 10%; } /* Work Orders */
#blocks-table th:nth-child(8) { width: 9%; }  /* Actions */

/* Text truncation for long content */
.table-cell-truncate {
    max-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Responsive pagination */
@media (max-width: 768px) {
    .pagination {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .page-item {
        margin: 2px;
    }
    
    .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
    }
}
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Dashboard @endslot
@slot('title') Blocks Management @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Blocks List</h4>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Export Buttons -->
                        <div class="btn-group" role="group">
                            <a href="{{ route('export.pdf', 'blocks') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-danger btn-sm" title="Export to PDF">
                                <i class="ph-file-pdf"></i>
                            </a>
                            <a href="{{ route('export.excel', 'blocks') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-success btn-sm" title="Export to Excel">
                                <i class="ph-file-xls"></i>
                            </a>
                            <a href="{{ route('export.print', 'blocks') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
                                <i class="ph-printer"></i>
                            </a>
                        </div>


                        <!-- Add Button -->
                        @admin
                        <a href="{{ route('blocks.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Block
                        </a>
                        @endadmin
                    </div>
                </div>
            </div>
            <div class="card-body mb-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif


                <div class="table-responsive">
                    <table id="blocks-table" class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Management Company</th>
                                <th>Block Manager</th>
                                <th>Address</th>
                                <th>Units</th>
                                <th>Issues</th>
                                <th>Work Orders</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blocks as $block)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <strong><a href="{{ route('blocks.show', $block->id) }}" class="text-decoration-none">{{ $block->name }}</a></strong>
                                        @if($block->blockType)
                                            @if($block->blockType->name === 'Residential')
                                                <span class="badge bg-primary" title="Residential">R</span>
                                            @elseif($block->blockType->name === 'Commercial')
                                                <span class="badge bg-primary" title="Commercial">C</span>
                                            @elseif($block->blockType->name === 'Residential + Commercial')
                                                <span class="badge bg-primary" title="Residential + Commercial">R+C</span>
                                            @else
                                                <span class="badge bg-primary" title="{{ $block->blockType->name }}">{{ substr($block->blockType->name, 0, 1) }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="table-cell-truncate" title="{{ $block->management_company }}">{{ $block->management_company }}</td>
                                <td class="table-cell-truncate" title="{{ $block->blockManager->name ?? 'N/A' }}">{{ $block->blockManager->name ?? 'N/A' }}</td>
                                <td class="table-cell-truncate" title="{{ $block->block_address }}">{{ $block->block_address }}</td>
                                <td><span class="badge bg-info">{{ $block->units->count() }}</span></td>
                                <td>
                                    @php
                                        $totalIssuesCount = $block->issues->count();
                                    @endphp
                                    @if($totalIssuesCount > 0)
                                        <a href="{{ route('block-issues.index', ['block_id' => $block->id]) }}" class="badge bg-warning text-decoration-none" style="cursor: pointer;" title="Click to view issues for this block">
                                            {{ $totalIssuesCount }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">{{ $totalIssuesCount }}</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-success">{{ $block->workOrders->where('status', 1)->count() }}</span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('blocks.show', $block->id) }}" class="btn btn-sm btn-outline-primary" title="View Block">
                                            <i class="ph-eye"></i>
                                        </a>
                                        @admin
                                        <a href="{{ route('blocks.edit', $block->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Block">
                                            <i class="ph-pencil"></i>
                                        </a>
                                        <form action="{{ route('blocks.destroy', $block->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this block?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Block">
                                                <i class="ph-trash"></i>
                                            </button>
                                        </form>
                                        @endadmin
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>No blocks found. 
                                            @admin
                                            <a href="{{ route('blocks.create') }}" class="text-primary">Create your first block</a>
                                            @else
                                            Contact an administrator to create blocks.
                                            @endadmin
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- @if($blocks->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $blocks->appends(request()->query())->links('vendor.pagination.datatables') }}
                    </div>
                @endif -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- DataTables Responsive JavaScript -->
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script>
<!-- DataTables Buttons JavaScript -->
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>

<script>
$(document).ready(function() {
    $('#blocks-table').DataTable({
        responsive: true,
        scrollX: false,
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
        // buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
        order: [[0, 'asc']], // default sort by Name (0-based index)
        columnDefs: [
            { targets: [7], orderable: false }, // Actions (last column)
            { targets: [4, 5, 6], type: 'num' }, // Units, Issues, Work Orders
            { targets: [0], width: '25%' }, // Name
            { targets: [1], width: '20%' }, // Management Company
            { targets: [2], width: '15%' }, // Block Manager
            { targets: [3], width: '25%' }, // Address
            { targets: [4], width: '8%' },  // Units
            { targets: [5], width: '8%' },  // Issues
            { targets: [6], width: '10%' }, // Work Orders
            { targets: [7], width: '9%' }   // Actions
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            search: "Search blocks:",
            lengthMenu: "Show _MENU_ blocks per page",
            info: "Showing _START_ to _END_ of _TOTAL_ blocks",
            infoEmpty: "Showing 0 to 0 of 0 blocks",
            infoFiltered: "(filtered from _MAX_ total blocks)",
            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
        },
        initComplete: function() {
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
});
</script>
@endsection 