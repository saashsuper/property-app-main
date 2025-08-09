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
<style>
/* Show sorting indicators on sortable headers */
table.dataTable thead th { position: relative; padding-right: 1.5rem !important; }
table.dataTable thead .sorting:after,
table.dataTable thead .sorting_asc:after,
table.dataTable thead .sorting_desc:after {
  position: absolute;
  right: 0.5rem;
  top: 50%;
  transform: translateY(-50%);
  display: inline-block;
  font-size: 0.75rem;
  opacity: 0.6;
}
table.dataTable thead .sorting:after { content: "\2195"; }       /* ↕ */
table.dataTable thead .sorting_asc:after { content: "\25B2"; opacity: 0.9; }  /* ▲ */
table.dataTable thead .sorting_desc:after { content: "\25BC"; opacity: 0.9; } /* ▼ */
table.dataTable thead .sorting_disabled:after { display: none; }
</style>

@section('content')
@component('components.breadcrumb')
@slot('li_1') Dashboard @endslot
@slot('title') Blocks Management @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Blocks List</h4>
                    </div>
                    <div class="col-auto">
                        <form action="{{ route('blocks.index') }}" method="GET" class="d-flex me-3">
                            <div class="input-group" style="min-width: 250px;">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search blocks..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('blocks.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                        @admin
                        <a href="{{ route('blocks.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Block
                        </a>
                        @endadmin
                    </div>
                </div>
            </div>
            <div class="card-body">
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

                @if(request('search'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-search me-2"></i>
                        Search results for "{{ request('search') }}": {{ $blocks->total() }} block(s) found
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="blocks-table" class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Management Company</th>
                                <th>Address</th>
                                <th>Units</th>
                                <th>Car Spaces</th>
                                <th>Created By</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blocks as $block)
                            <tr>
                                <td>{{ $block->id }}</td>
                                <td>
                                    @if($block->image_url)
                                        <img src="{{ $block->image_url }}" alt="{{ $block->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-building text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $block->name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $block->blockType->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $block->management_company }}</td>
                                <td>
                                    <small class="text-muted">{{ $block->full_address }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $block->no_of_units ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $block->car_spaces }}</span>
                                </td>
                                <td>
                                    <small>{{ $block->creator->name ?? 'System' }}</small>
                                </td>
                                <td data-order="{{ $block->created_at->timestamp }}">
                                    <small>{{ $block->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('blocks.show', $block->id) }}">
                                                    <i class="fas fa-eye me-2"></i>View
                                                </a>
                                            </li>
                                            @admin
                                            <li>
                                                <a class="dropdown-item" href="{{ route('blocks.edit', $block->id) }}">
                                                    <i class="fas fa-edit me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('blocks.destroy', $block->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this block?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                            @endadmin
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
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

                @if($blocks->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $blocks->appends(request()->query())->links('vendor.pagination.datatables') }}
                    </div>
                @endif
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
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
        order: [[9, 'desc']], // default sort by Created Date (0-based index)
        columnDefs: [
            { targets: [1, 10], orderable: false }, // Image, Actions
            { targets: [6, 7], type: 'num' } // Units, Car Spaces
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
        }
    });
});
</script>
@endsection 