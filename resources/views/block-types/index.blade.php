@extends('layouts.master')

@section('title') Block Types @endsection

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
@component('components.breadcrumb')
@slot('li_1') Dashboard @endslot
@slot('title') Block Types Management @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Block Types List</h4>
                    </div>
                    <div class="col-auto">
                        <form action="{{ route('block-types.index') }}" method="GET" class="d-flex me-3">
                            <div class="input-group" style="min-width: 250px;">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search block types..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('block-types.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                        <a href="{{ route('block-types.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Block Type
                        </a>
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
                        Search results for "{{ request('search') }}": {{ $blockTypes->total() }} block type(s) found
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="block-types-table" class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Blocks Count</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blockTypes as $blockType)
                            <tr>
                                <td>{{ $blockType->id }}</td>
                                <td>
                                    <strong>{{ $blockType->name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $blockType->blocks_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <small>{{ $blockType->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('block-types.show', $blockType->id) }}">
                                                    <i class="fas fa-eye me-2"></i>View
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('block-types.edit', $blockType->id) }}">
                                                    <i class="fas fa-edit me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('block-types.destroy', $blockType->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this block type?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>No block types found. <a href="{{ route('block-types.create') }}" class="text-primary">Create your first block type</a></p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($blockTypes->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $blockTypes->appends(request()->query())->links('vendor.pagination.datatables') }}
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
    $('#block-types-table').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        ],
        pageLength: 15,
        lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]],
        language: {
            search: "Search block types:",
            lengthMenu: "Show _MENU_ block types per page",
            info: "Showing _START_ to _END_ of _TOTAL_ block types",
            infoEmpty: "Showing 0 to 0 of 0 block types",
            infoFiltered: "(filtered from _MAX_ total block types)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});
</script>
@endsection 