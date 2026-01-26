@extends('layouts.master')
@section('title')
    Block Work Orders - PROMAN
@endsection
@section('css')
<x-datatable-base />
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
                        <x-datatable-loader 
                            id="work-orders-table-loading" 
                            message="Loading work orders..." 
                            tableId="blockWorkOrdersTable" 
                        />
                        
                        <div class="table-responsive">
                            <table id="blockWorkOrdersTable" class="table table-bordered table-striped table-hover" style="display: none;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref No</th>
                                        <th>Block</th>
                                        <th>Unit</th>
                                        <th>Issue</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Contractor Company</th>
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
                                                <strong>{{ $workOrder->ref_no }}</strong>
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
                                        <td>
                                            @php
                                                $issueText = $workOrder->blockIssue->issue ?? $workOrder->issue ?? null;
                                            @endphp
                                            @if($issueText)
                                                {{ Str::limit($issueText, 50) }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
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
                                            @php
                                                // Get contractor company name
                                                $contractorName = null;
                                                if ($workOrder->contractCompany) {
                                                    $contractorName = $workOrder->contractCompany->name ?? null;
                                                } elseif ($workOrder->contractor) {
                                                    // Check if contractor is a property manager
                                                    $isPropertyManager = $workOrder->contractor->userType && $workOrder->contractor->userType->name === 'Property manager';
                                                    if ($isPropertyManager) {
                                                        $contractorName = $workOrder->contractor->name . ' (Property Manager)';
                                                    } else {
                                                        $contractorName = $workOrder->contractor->name ?? null;
                                                    }
                                                }
                                            @endphp
                                            @if($contractorName)
                                                <span class="badge bg-info">{{ $contractorName }}</span>
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
                                                @php
                                                    $hasBeenUpdated = $workOrder->hasBeenUpdated();
                                                    $actionText = $hasBeenUpdated ? 'Archive' : 'Delete';
                                                    $actionIcon = $hasBeenUpdated ? 'ph-archive' : 'ph-trash';
                                                    $actionColor = $hasBeenUpdated ? 'warning' : 'danger';
                                                @endphp
                                                <form action="{{ route('block-work-orders.destroy', $workOrder) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to {{ strtolower($actionText) }} this work order?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $actionColor }}" title="{{ $actionText }}">
                                                        <i class="{{ $actionIcon }}"></i> {{ $actionText }}
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
<x-datatable-scripts />

<script>
$(document).ready(function() {
    try {
        // Check if DataTables is available
        if (!$.fn.DataTable) {
            console.error('DataTables library not loaded');
            $('#work-orders-table-loading').addClass('d-none');
            $('#blockWorkOrdersTable').show();
            return;
        }

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
                { targets: [6], width: '14%' },  // Contractor Company
                { targets: [7], width: '10%' },  // Deadline
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
                zeroRecords: "No work orders found",
                paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
            },
            initComplete: function() {
                // Hide loader and show table
                $('#work-orders-table-loading').addClass('d-none');
                $('#blockWorkOrdersTable').show();
                
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
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        // Fallback: hide loader and show table anyway
        $('#work-orders-table-loading').addClass('d-none');
        $('#blockWorkOrdersTable').show();
    }
    
    // Fallback timeout - if table is still not visible after 3 seconds, force show it
    setTimeout(function() {
        if ($('#blockWorkOrdersTable').is(':hidden')) {
            console.warn('DataTable initialization timeout - forcing table display');
            $('#work-orders-table-loading').addClass('d-none');
            $('#blockWorkOrdersTable').show();
        }
    }, 3000);
});
</script>
@endsection 