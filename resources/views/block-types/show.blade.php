@extends('layouts.master')

@section('title') Block Type Overview - {{ $blockType->name }} @endsection

@section('css')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<!-- DataTables Responsive CSS -->
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ph-stack me-2 text-primary"></i>
                        BLOCK TYPE OVERVIEW
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-types.index') }}">Block Types</a></li>
                            <li class="breadcrumb-item active">{{ $blockType->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Block Type Header -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body bg-gradient-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="mb-1 text-white">{{ $blockType->name }}</h2>
                                <p class="mb-0 text-white-50">Block Type ID: #{{ str_pad($blockType->id, 4, '0', STR_PAD_LEFT) }}</p>
                                <div class="mt-3">
                                    <span class="badge bg-white bg-opacity-25 text-white fs-6">
                                        <i class="ph-buildings me-1"></i>{{ $blockType->blocks->count() }} {{ Str::plural('Block', $blockType->blocks->count()) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="d-flex flex-column align-items-md-end">
                                    <div class="mb-2">
                                        <i class="ph-calendar text-white-50 me-2"></i>
                                        <span class="text-white-50">Created: {{ $blockType->created_at->format('d M, Y') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <i class="ph-clock text-white-50 me-2"></i>
                                        <span class="text-white-50">Updated: {{ $blockType->updated_at->format('d M, Y') }}</span>
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        @admin
                                        <a href="{{ route('block-types.edit', $blockType) }}" class="btn btn-light btn-sm">
                                            <i class="ph-pencil me-2"></i>Edit Block Type
                                        </a>
                                        @endadmin
                                        <a href="{{ route('block-types.index') }}" class="btn btn-outline-light btn-sm">
                                            <i class="ph-list me-2"></i>Back to List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-lg-4">
                <!-- Block Type Information -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0 text-dark">
                            <i class="ph-info me-2 text-primary"></i>Block Type Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-semibold" style="width: 40%;">
                                            <i class="ph-hash text-primary me-2"></i>ID
                                        </td>
                                        <td>{{ $blockType->id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">
                                            <i class="ph-text-aa text-primary me-2"></i>Name
                                        </td>
                                        <td><strong>{{ $blockType->name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">
                                            <i class="ph-buildings text-primary me-2"></i>Total Blocks
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">{{ $blockType->blocks->count() }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">
                                            <i class="ph-calendar-plus text-primary me-2"></i>Created At
                                        </td>
                                        <td>
                                            <small>{{ $blockType->created_at->format('M d, Y') }}</small><br>
                                            <small class="text-muted">{{ $blockType->created_at->format('h:i A') }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">
                                            <i class="ph-calendar-check text-primary me-2"></i>Last Updated
                                        </td>
                                        <td>
                                            <small>{{ $blockType->updated_at->format('M d, Y') }}</small><br>
                                            <small class="text-muted">{{ $blockType->updated_at->format('h:i A') }}</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0 text-dark">
                            <i class="ph-chart-bar me-2 text-primary"></i>Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm bg-primary bg-opacity-10 rounded">
                                    <div class="avatar-title bg-transparent text-primary fs-3">
                                        <i class="ph-buildings"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $blockType->blocks->count() }}</h4>
                                <p class="text-muted mb-0">Total Blocks</p>
                            </div>
                        </div>
                        @php
                            $totalUnits = $blockType->blocks->sum('no_of_units');
                            $totalCarSpaces = $blockType->blocks->sum('car_spaces');
                        @endphp
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm bg-success bg-opacity-10 rounded">
                                    <div class="avatar-title bg-transparent text-success fs-3">
                                        <i class="ph-users"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $totalUnits }}</h4>
                                <p class="text-muted mb-0">Total Units</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm bg-info bg-opacity-10 rounded">
                                    <div class="avatar-title bg-transparent text-info fs-3">
                                        <i class="ph-car"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $totalCarSpaces }}</h4>
                                <p class="text-muted mb-0">Total Car Spaces</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Blocks -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-dark">
                                <i class="ph-buildings me-2 text-primary"></i>Related Blocks ({{ $blockType->blocks->count() }})
                            </h5>
                            @if($blockType->blocks->count() > 0)
                            <a href="{{ route('blocks.index') }}?block_type_id={{ $blockType->id }}" class="btn btn-sm btn-outline-primary">
                                <i class="ph-eye me-1"></i>View All
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($blockType->blocks->count() > 0)
                        <div class="table-responsive">
                            <table id="blocks-table" class="table table-bordered table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Management Company</th>
                                        <th>Address</th>
                                        <th>Units</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($blockType->blocks as $block)
                                    <tr>
                                        <td>
                                            <strong><a href="{{ route('blocks.show', $block->id) }}" class="text-decoration-none">{{ $block->name }}</a></strong>
                                        </td>
                                        <td>{{ $block->management_company ?? 'N/A' }}</td>
                                        <td>
                                            <small class="text-muted">{{ $block->block_address ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info">{{ $block->no_of_units ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <small>{{ $block->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('blocks.show', $block->id) }}" class="btn btn-sm btn-outline-primary" title="View Block">
                                                <i class="ph-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="ph-package display-1 mb-3 text-primary opacity-25"></i>
                                <p class="fs-5">No blocks found for this type.</p>
                                @admin
                                <p class="text-muted">
                                    <a href="{{ route('blocks.create') }}" class="text-primary">Create a new block</a> with this type.
                                </p>
                                @endadmin
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@if($blockType->blocks->count() > 0)
<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- DataTables Responsive JavaScript -->
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    $('#blocks-table').DataTable({
        responsive: true,
        scrollX: false,
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
        order: [[0, 'asc']],
        columnDefs: [
            { targets: [5], orderable: false }, // Actions column
            { targets: [3], type: 'num' }, // Units
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
                    'width': '250px',
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
            $('.dataTables_length label, .dataTables_filter label').css({
                'display': 'flex',
                'align-items': 'center',
                'margin-bottom': '0'
            });
        }
    });
});
</script>
@endif
@endsection
