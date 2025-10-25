@extends('layouts.master')
@section('title')
    Block Work Orders - PROMAN
@endsection
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
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Block Work Orders</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Block Work Orders</li>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Block Work Orders List</h4>
                            <div class="d-flex align-items-center gap-3">
                                <!-- Export Buttons -->
                                <div class="btn-group" role="group">
                                    <a href="{{ route('export.pdf', 'block-work-orders') }}?{{ http_build_query(request()->query()) }}" 
                                       class="btn btn-outline-danger btn-sm" title="Export to PDF">
                                        <i class="ph-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('export.excel', 'block-work-orders') }}?{{ http_build_query(request()->query()) }}" 
                                       class="btn btn-outline-success btn-sm" title="Export to Excel">
                                        <i class="ph-file-xls"></i>
                                    </a>
                                    <a href="{{ route('export.print', 'block-work-orders') }}?{{ http_build_query(request()->query()) }}" 
                                       class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
                                        <i class="ph-printer"></i>
                                    </a>
                                </div>

                                <!-- Add Button -->
                                @if(!auth()->user()->hasType('Contractor Admin'))
                                <a href="{{ route('block-work-orders.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add New Work Order
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Block Work Orders Table -->
                        <div class="table-responsive">
                            <table id="blockWorkOrdersTable" class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref No</th>
                                        <th>Block</th>
                                        <th>Unit</th>
                                        <th>Issue</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Contact</th>
                                        <th>Deadline</th>
                                        <th>Issued By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($workOrders as $workOrder)
                                    <tr>
                                        <td>
                                            <a href="{{ route('block-work-orders.show', $workOrder) }}" class="text-decoration-none">
                                                <strong>#{{ $workOrder->ref_no }}</strong>
                                            </a>
                                        </td>
                                        <td>{{ $workOrder->block->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($workOrder->blockUnit)
                                                <span class="badge bg-secondary">{{ $workOrder->blockUnit->unit_name }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($workOrder->issue, 50) ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $priorityColors = [
                                                    1 => 'success',
                                                    2 => 'info',
                                                    3 => 'warning',
                                                    4 => 'danger',
                                                    5 => 'dark'
                                                ];
                                                $color = $priorityColors[$workOrder->priority_id] ?? 'info';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ $workOrder->priority_text }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    1 => 'warning',
                                                    2 => 'info',
                                                    3 => 'success',
                                                    4 => 'secondary',
                                                    5 => 'danger'
                                                ];
                                                $color = $statusColors[$workOrder->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ $workOrder->status_text }}</span>
                                        </td>
                                        <td>
                                            @if($workOrder->contact_name)
                                                <div>{{ $workOrder->contact_name }}</div>
                                                @if($workOrder->contact_email)
                                                    <small class="text-muted">{{ $workOrder->contact_email }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($workOrder->deadline_date)
                                                {{ $workOrder->deadline_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $workOrder->issuedBy->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $workOrder->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('block-work-orders.show', $workOrder) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="ph-eye"></i>
                                                </a>
                                                @if(!auth()->user()->hasType('Contractor Admin'))
                                                <a href="{{ route('block-work-orders.edit', $workOrder) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="ph-pencil"></i>
                                                </a>
                                                <form action="{{ route('block-work-orders.destroy', $workOrder) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this work order?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="ph-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>No work orders found. 
                                                    @if(!auth()->user()->hasType('Contractor Admin'))
                                                    <a href="{{ route('block-work-orders.create') }}" class="text-primary">Create your first work order</a>
                                                    @else
                                                    Contact an administrator to create work orders.
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination handled by DataTables -->
                        {{-- @if($workOrders->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $workOrders->firstItem() ?? 0 }} to {{ $workOrders->lastItem() ?? 0 }} 
                                of {{ $workOrders->total() }} block work orders
                            </div>
                            <div>
                                {{ $workOrders->appends(request()->query())->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                        @endif --}}
                    </div>
                </div>
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
    // Initialize DataTable
    var table = $('#blockWorkOrdersTable').DataTable({
        responsive: true,
        scrollX: false,
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
        order: [[0, 'desc']], // default sort by Ref No descending
        columnDefs: [
            { targets: [9], orderable: false }, // Actions column
            { targets: [0], width: '8%' },   // Ref No
            { targets: [1], width: '12%' },  // Block
            { targets: [2], width: '10%' },  // Unit
            { targets: [3], width: '18%' },  // Issue
            { targets: [4], width: '8%' },   // Priority
            { targets: [5], width: '8%' },   // Status
            { targets: [6], width: '10%' },  // Contact
            { targets: [7], width: '8%' },   // Deadline
            { targets: [8], width: '12%' },  // Issued By
            { targets: [9], width: '6%' }    // Actions
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            search: "Search work orders:",
            lengthMenu: "Show _MENU_ work orders per page",
            info: "Showing _START_ to _END_ of _TOTAL_ work orders",
            infoEmpty: "Showing 0 to 0 of 0 work orders",
            infoFiltered: "(filtered from _MAX_ total work orders)",
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

    // Update export button states based on table data
    function updateExportButtons() {
        var hasData = table.data().count() > 0;
        $('a[href*="export.pdf"], a[href*="export.excel"], a[href*="export.print"]').toggleClass('disabled', !hasData);
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