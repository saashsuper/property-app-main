@extends('layouts.master')

@section('title') Blocks @endsection

@section('css')
<x-datatable-base />

<style>
/* Blocks-specific column width management */
#blocks-table th:nth-child(1) { width: 25%; } /* Name */
#blocks-table th:nth-child(2) { width: 20%; } /* Management Company */
#blocks-table th:nth-child(3) { width: 15%; } /* Block Manager */
#blocks-table th:nth-child(4) { width: 25%; } /* Address */
#blocks-table th:nth-child(5) { width: 8%; }  /* Units */
#blocks-table th:nth-child(6) { width: 8%; }  /* Issues */
#blocks-table th:nth-child(7) { width: 10%; } /* Work Orders */
#blocks-table th:nth-child(8) { width: 9%; }  /* Actions */
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
                        <a href="{{ route('blocks.create') }}" class="btn btn-primary" dusk="add-block-button">
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

                <x-datatable-loader 
                    id="blocks-table-loading" 
                    message="Loading blocks..." 
                    tableId="blocks-table" 
                />

                <div class="table-responsive">
                    <table id="blocks-table" class="table table-bordered table-striped table-hover" dusk="blocks-table" style="display: none;">
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
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Block" dusk="delete-block-{{ $block->id }}">
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

                {{-- Pagination handled by DataTables --}}
                {{-- @if($blocks->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $blocks->appends(request()->query())->links('vendor.pagination.datatables') }}
                    </div>
                @endif --}}
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
            $('#blocks-table-loading').addClass('d-none');
            $('#blocks-table').show();
            return;
        }

        $('#blocks-table').DataTable({
            responsive: true,
            scrollX: false,
            autoWidth: false,
            processing: true, // Show processing indicator during sort/search
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
                paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" },
                processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading...'
            },
            initComplete: function() {
                // Hide loading spinner and show table
                $('#blocks-table-loading').addClass('d-none');
                $('#blocks-table').show();
                
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
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        // Fallback: hide loader and show table anyway
        $('#blocks-table-loading').addClass('d-none');
        $('#blocks-table').show();
    }
    
    // Fallback timeout - if table is still not visible after 3 seconds, force show it
    setTimeout(function() {
        if ($('#blocks-table').is(':hidden')) {
            console.warn('DataTable initialization timeout - forcing table display');
            $('#blocks-table-loading').addClass('d-none');
            $('#blocks-table').show();
        }
    }, 3000);
});
</script>
@endsection 