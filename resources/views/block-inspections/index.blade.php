@extends('layouts.master')

@section('title') @lang('translation.block-inspections') @endsection

@section('css')
<x-datatable-base />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.blocks') @endslot
        @slot('title') @lang('translation.block-inspections') @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Block Inspections</h4>
                        <div class="d-flex align-items-center gap-3">
                            <!-- Export Buttons -->
                            <div class="btn-group" role="group">
                                <a href="{{ route('export.pdf', 'block-inspections') }}?{{ http_build_query(request()->query()) }}" 
                                   class="btn btn-outline-danger btn-sm" title="Export to PDF">
                                    <i class="ph-file-pdf"></i>
                                </a>
                                <a href="{{ route('export.excel', 'block-inspections') }}?{{ http_build_query(request()->query()) }}" 
                                   class="btn btn-outline-success btn-sm" title="Export to Excel">
                                    <i class="ph-file-xls"></i>
                                </a>
                                <a href="{{ route('export.print', 'block-inspections') }}?{{ http_build_query(request()->query()) }}" 
                                   class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
                                    <i class="ph-printer"></i>
                                </a>
                            </div>
                            <!-- Add Button -->
                            @admin
                            <a href="{{ route('block-inspections.create') }}" class="btn btn-primary">
                                <i class="ph-plus me-1"></i>Add Inspection
                            </a>
                            @endadmin
                        </div>
                    </div>
                </div>
                <div class="card-body mb-3">

                    <!-- Filters -->
                    <form action="{{ route('block-inspections.index') }}" method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="inspection-status" name="status" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>In Progress</option>
                                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Completed</option>
                                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>On Hold</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="{{ route('block-inspections.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <x-datatable-loader 
                        id="inspections-table-loading" 
                        message="Loading inspections..." 
                        tableId="blockInspectionsTable" 
                    />

                    <div class="table-responsive">
                        <table id="blockInspectionsTable" class="table table-bordered table-striped table-hover" style="display: none;">
                            <thead class="table-light">
                                <tr>
                                    <th>Reference</th>
                                    <th>Block</th>
                                    <th>Scheduled Date</th>
                                    <th>Start Date/Time</th>
                                    <th>End Date/Time</th>
                                    <th>Status</th>
                                    <th>Lead Inspector</th>
                                    <th>Team Size</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inspections as $inspection)
                                    <tr>
                                        <td>
                                            <a href="{{ route('block-inspections.show', $inspection->id) }}" class="text-primary fw-bold">
                                                {{ $inspection->ref_no }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($inspection->block)
                                                <a href="{{ route('blocks.show', $inspection->block->id) }}" class="text-decoration-none">
                                                    {{ $inspection->block->name }}
                                                </a>
                                            @else
                                                <span class="text-muted">Block not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $inspection->scheduled_date_time->format('M d, Y H:i') }}
                                        </td>
                                        <td>
                                            @if($inspection->start_date_time)
                                                {{ $inspection->start_date_time->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">Not started</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inspection->end_date_time)
                                                {{ $inspection->end_date_time->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">Not completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $inspection->status_color }}-subtle text-{{ $inspection->status_color }}">
                                                {{ $inspection->status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $leadInspector = $inspection->inspectionTeams->where('is_lead', true)->first();
                                            @endphp
                                            {{ $leadInspector && $leadInspector->user ? $leadInspector->user->name : 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                {{ $inspection->inspectionTeams->count() }} members
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('block-inspections.show', $inspection->id) }}" class="btn btn-sm btn-outline-primary" title="View Inspection">
                                                    <i class="ph-eye"></i>
                                                </a>
                                                @if($inspection->job_status_id == 3)
                                                <a href="{{ route('block-inspections.download-pdf', $inspection->id) }}" class="btn btn-sm btn-outline-danger" title="Download PDF Report">
                                                    <i class="ph-file-pdf"></i>
                                                </a>
                                                @endif
                                                @admin
                                                <a href="{{ route('block-inspections.edit', $inspection->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Inspection">
                                                    <i class="ph-pencil"></i>
                                                </a>
                                                <form action="{{ route('block-inspections.destroy', $inspection->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this inspection?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Inspection">
                                                        <i class="ph-trash"></i>
                                                    </button>
                                                </form>
                                                @endadmin
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ph-clipboard-text display-4"></i>
                                                <h5 class="mt-2">No inspections found</h5>
                                                <p>No block inspections match your current filters.</p>
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
            $('#inspections-table-loading').addClass('d-none');
            $('#blockInspectionsTable').show();
            return;
        }
        
        $('#blockInspectionsTable').DataTable({
            responsive: true,
            scrollX: false,
            autoWidth: false,
            processing: true,
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
            order: [[2, 'desc']], // default sort by Scheduled Date descending
            columnDefs: [
                { targets: [8], orderable: false }, // Actions column not sortable (9th column, index 8)
            ],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "Search inspections:",
                lengthMenu: "Show _MENU_ inspections per page",
                info: "Showing _START_ to _END_ of _TOTAL_ inspections",
                infoEmpty: "Showing 0 to 0 of 0 inspections",
                infoFiltered: "(filtered from _MAX_ total inspections)",
                zeroRecords: "No inspections found",
                paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" },
                processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading...'
            },
            initComplete: function() {
                // Hide loader and show table
                $('#inspections-table-loading').addClass('d-none');
                $('#blockInspectionsTable').show();
                
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
        $('#inspections-table-loading').addClass('d-none');
        $('#blockInspectionsTable').show();
    }
    
    // Fallback timeout - if table is still not visible after 3 seconds, force show it
    setTimeout(function() {
        if ($('#blockInspectionsTable').is(':hidden')) {
            console.warn('DataTable initialization timeout - forcing table display');
            $('#inspections-table-loading').addClass('d-none');
            $('#blockInspectionsTable').show();
        }
    }, 3000);
});
</script>
@endsection
