@extends('layouts.master')
@section('title') Site Visits @endsection

@section('css')
<x-datatable-base />
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Dashboard @endslot
@slot('title') Site Visits @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Site Visits</h4>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Export Buttons -->
                        <div class="btn-group" role="group">
                            <a href="{{ route('export.pdf', 'block-visits') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-danger btn-sm" title="Export to PDF">
                                <i class="ph-file-pdf"></i>
                            </a>
                            <a href="{{ route('export.excel', 'block-visits') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-success btn-sm" title="Export to Excel">
                                <i class="ph-file-xls"></i>
                            </a>
                            <a href="{{ route('export.print', 'block-visits') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
                                <i class="ph-printer"></i>
                            </a>
                        </div>

                        <!-- Add Button -->
                        <a href="{{ route('block-visits.create') }}" class="btn btn-primary">
                            <i class="ph-plus me-1"></i>New Visit
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body mb-3">
                <x-datatable-loader 
                    id="visits-table-loading" 
                    message="Loading site visits..." 
                    tableId="blockVisitsTable" 
                />
                
                <div class="table-responsive">
                    <table id="blockVisitsTable" class="table table-bordered table-striped table-hover" style="display: none;">
                        <thead class="table-light">
                            <tr>
                                <th>Ref</th>
                                <th>Block</th>
                                <th>Block Type</th>
                                <th>Scheduled</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visits as $visit)
                            <tr>
                                <td>
                                    <a href="{{ route('block-visits.show', $visit) }}" class="text-decoration-none">
                                        <strong>{{ $visit->ref_no }}</strong>
                                    </a>
                                </td>
                                <td>
                                    @if($visit->block)
                                        <a href="{{ route('blocks.show', $visit->block) }}" class="text-decoration-none">
                                            <strong>{{ $visit->block->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $visit->block->management_company }}</small>
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($visit->block && $visit->block->blockType)
                                        <span class="badge bg-primary">{{ $visit->block->blockType->name }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ optional($visit->scheduled_date_time)->format('M d, Y H:i') }}</td>
                                <td>{{ optional($visit->start_date_time)->format('M d, Y H:i') }}</td>
                                <td>{{ optional($visit->end_date_time)->format('M d, Y H:i') }}</td>
                                <td>
                                    @if($visit->start_date_time && $visit->end_date_time)
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($visit->start_date_time)
                                        <span class="badge bg-warning">In Progress</span>
                                    @else
                                        <span class="badge bg-info">Scheduled</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('block-visits.show', $visit) }}" class="btn btn-sm btn-outline-primary" title="View Visit">
                                            <i class="ph-eye"></i>
                                        </a>
                                        <a href="{{ route('block-visits.edit', $visit) }}" class="btn btn-sm btn-outline-secondary" title="Edit Visit">
                                            <i class="ph-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ph-calendar-blank ph-3x mb-3"></i>
                                        <p>No site visits found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
            $('#visits-table-loading').addClass('d-none');
            $('#blockVisitsTable').show();
            return;
        }
        
        $('#blockVisitsTable').DataTable({
            responsive: true,
            scrollX: false,
            autoWidth: false,
            processing: true,
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
            order: [[3, 'desc']], // default sort by Scheduled date descending (newest first)
            columnDefs: [
                { targets: [7], orderable: false }, // Actions column not sortable
            ],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "Search visits:",
                lengthMenu: "Show _MENU_ visits per page",
                info: "Showing _START_ to _END_ of _TOTAL_ visits",
                infoEmpty: "Showing 0 to 0 of 0 visits",
                infoFiltered: "(filtered from _MAX_ total visits)",
                zeroRecords: "No site visits found",
                paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" },
                processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading...'
            },
            initComplete: function() {
                // Hide loader and show table
                $('#visits-table-loading').addClass('d-none');
                $('#blockVisitsTable').show();
                
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
                        'height': '38px',
                        'font-size': '14px',
                        'margin': '0 10px'
                    });
            }
        });
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        // Fallback: hide loader and show table anyway
        $('#visits-table-loading').addClass('d-none');
        $('#blockVisitsTable').show();
    }
    
    // Fallback timeout - if table is still not visible after 3 seconds, force show it
    setTimeout(function() {
        if ($('#blockVisitsTable').is(':hidden')) {
            console.warn('DataTable initialization timeout - forcing table display');
            $('#visits-table-loading').addClass('d-none');
            $('#blockVisitsTable').show();
        }
    }, 3000);
});
</script>
@endsection

