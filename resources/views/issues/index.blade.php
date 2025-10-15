@extends('layouts.master')
@section('title')
    Issues - PROMAN
@endsection
@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<style>
/* Custom DataTables styling */
.dataTables_wrapper .dataTables_length select {
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.dataTables_wrapper .dataTables_filter input {
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    margin-left: 0.5rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.375rem 0.75rem;
    margin-left: 2px;
    border-radius: 0.25rem;
}

.table-cell-truncate {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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
                    <h4 class="mb-sm-0">General Issues</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Issues</li>
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
                            <h4 class="card-title mb-0">Issues Management</h4>
                            <div class="d-flex align-items-center gap-3">
                                <!-- Export Buttons -->
                                <div class="btn-group" role="group">
                                    <a href="{{ route('export.pdf', 'issues') }}?{{ http_build_query(request()->query()) }}" 
                                       class="btn btn-outline-danger btn-sm" title="Export to PDF">
                                        <i class="ph-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('export.excel', 'issues') }}?{{ http_build_query(request()->query()) }}" 
                                       class="btn btn-outline-success btn-sm" title="Export to Excel">
                                        <i class="ph-file-xls"></i>
                                    </a>
                                    <a href="{{ route('export.print', 'issues') }}?{{ http_build_query(request()->query()) }}" 
                                       class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
                                        <i class="ph-printer"></i>
                                    </a>
                                </div>

                                <!-- Add Button -->
                                <a href="{{ route('issues.create') }}" class="btn btn-primary">
                                    <i class="ph-plus me-2"></i>Add New Issue
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Issues Table -->
                        <div class="table-responsive">
                            <table id="issues-table" class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Reported By</th>
                                        <th>Location</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($issues as $issue)
                                    <tr>
                                        <td>
                                            <span class="fw-medium">{{ $issue->ref_no }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <span class="avatar-title bg-soft-primary rounded-3">
                                                        <i class="ph-warning font-size-16 text-primary"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $issue->title }}</div>
                                                    <small class="text-muted">{{ Str::limit($issue->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $issue->category }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $issue->priority_color }}">{{ $issue->priority_text }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $issue->status_color }}">{{ $issue->status_text }}</span>
                                        </td>
                                        <td>
                                            @if($issue->assignedTo)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <span class="avatar-title bg-soft-success rounded-3">
                                                            <i class="ph-user font-size-16 text-success"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $issue->assignedTo->name }}</div>
                                                        <small class="text-muted">{{ $issue->assignedTo->email }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($issue->reportedBy)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <span class="avatar-title bg-soft-warning rounded-3">
                                                            <i class="ph-user font-size-16 text-warning"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $issue->reportedBy->name }}</div>
                                                        <small class="text-muted">{{ $issue->reportedBy->email }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $issue->location ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $issue->created_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="ph-gear-six"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('issues.show', $issue) }}">
                                                            <i class="ph-eye me-2"></i> View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('issues.edit', $issue) }}">
                                                            <i class="ph-pencil me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('issues.destroy', $issue) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this issue?')">
                                                                <i class="ph-trash me-2"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ph-warning font-size-24 mb-2"></i>
                                                <p>No issues found</p>
                                                <a href="{{ route('issues.create') }}" class="btn btn-primary btn-sm">
                                                    <i class="ph-plus me-1"></i> Create First Issue
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination handled by DataTables --}}
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
    $('#issues-table').DataTable({
        responsive: true,
        scrollX: false,
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
        order: [[8, 'desc']], // Sort by Created date (newest first)
        columnDefs: [
            { targets: [9], orderable: false }, // Actions column
            { targets: [0], width: '10%' },  // Reference
            { targets: [1], width: '20%' },  // Title
            { targets: [2], width: '10%' },  // Category
            { targets: [3], width: '8%' },   // Priority
            { targets: [4], width: '8%' },   // Status
            { targets: [5], width: '15%' },  // Assigned To
            { targets: [6], width: '15%' },  // Reported By
            { targets: [7], width: '10%' },  // Location
            { targets: [8], width: '10%' },  // Created
            { targets: [9], width: '8%' }    // Actions
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            search: "Search issues:",
            lengthMenu: "Show _MENU_ issues per page",
            info: "Showing _START_ to _END_ of _TOTAL_ issues",
            infoEmpty: "Showing 0 to 0 of 0 issues",
            infoFiltered: "(filtered from _MAX_ total issues)",
            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" },
            emptyTable: "No issues found"
        },
        initComplete: function() {
            // Style the search box
            $('.dataTables_filter input')
                .addClass('form-control')
                .css({
                    'width': '300px',
                    'height': '38px',
                    'font-size': '14px',
                    'margin-left': '10px'
                });
            
            // Style the page length dropdown
            $('.dataTables_length select')
                .addClass('form-select')
                .css({
                    'width': 'auto',
                    'display': 'inline-block',
                    'margin': '0 5px'
                });
            
            // Add custom dropdown filters after search box
            var filterHtml = `
                <div class="d-flex gap-2 ms-3">
                    <select id="category-filter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">All Categories</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Repair">Repair</option>
                        <option value="Security">Security</option>
                        <option value="Cleaning">Cleaning</option>
                        <option value="Other">Other</option>
                    </select>
                    <select id="status-filter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">All Status</option>
                        <option value="Open">Open</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Closed">Closed</option>
                        <option value="On Hold">On Hold</option>
                    </select>
                    <select id="priority-filter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">All Priority</option>
                        <option value="Low">Low</option>
                        <option value="Normal">Normal</option>
                        <option value="High">High</option>
                        <option value="Urgent">Urgent</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>
            `;
            $('.dataTables_filter').append(filterHtml);
            
            // Add event listeners for custom filters
            $('#category-filter, #status-filter, #priority-filter').on('change', function() {
                var table = $('#issues-table').DataTable();
                table.draw();
            });
        }
    });
    
    // Custom filtering function for dropdowns
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var categoryFilter = $('#category-filter').val();
            var statusFilter = $('#status-filter').val();
            var priorityFilter = $('#priority-filter').val();
            
            var category = data[2] || ''; // Category column
            var status = data[4] || '';   // Status column
            var priority = data[3] || ''; // Priority column
            
            // Extract text from badge HTML
            category = category.replace(/<[^>]*>/g, '').trim();
            status = status.replace(/<[^>]*>/g, '').trim();
            priority = priority.replace(/<[^>]*>/g, '').trim();
            
            if (categoryFilter && category.indexOf(categoryFilter) === -1) {
                return false;
            }
            if (statusFilter && status.indexOf(statusFilter) === -1) {
                return false;
            }
            if (priorityFilter && priority.indexOf(priorityFilter) === -1) {
                return false;
            }
            
            return true;
        }
    );
});
</script>
@endsection 