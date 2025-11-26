@extends('layouts.master')

@section('title') Contract Companies @endsection

@section('css')
<x-datatable-base />

<style>
/* Contract Companies-specific column widths */
#contract-companies-table th:nth-child(1) { width: 25%; } /* Company Name */
#contract-companies-table th:nth-child(2) { width: 30%; } /* Address */
#contract-companies-table th:nth-child(3) { width: 15%; } /* Phone Number */
#contract-companies-table th:nth-child(4) { width: 15%; } /* Website */
#contract-companies-table th:nth-child(5) { width: 15%; } /* Actions */
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Dashboard @endslot
@slot('title') Contract Companies @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Contract Companies</h4>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Export Buttons -->
                        <div class="btn-group" role="group">
                            <a href="{{ route('export.pdf', 'contract-companies') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-danger btn-sm" title="Export to PDF">
                                <i class="ph-file-pdf"></i>
                            </a>
                            <a href="{{ route('export.excel', 'contract-companies') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-success btn-sm" title="Export to Excel">
                                <i class="ph-file-xls"></i>
                            </a>
                            <a href="{{ route('export.print', 'contract-companies') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
                                <i class="ph-printer"></i>
                            </a>
                        </div>

                        <!-- Add Button -->
                        <a href="{{ route('contract-companies.create') }}" class="btn btn-primary">
                            <i class="ph-plus me-2"></i>Add Contract Company
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body mb-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ph-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ph-warning me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <x-datatable-loader 
                    id="contract-companies-table-loading" 
                    message="Loading contract companies..." 
                    tableId="contract-companies-table" 
                />

                <div class="table-responsive">
                    <table id="contract-companies-table" class="table table-bordered table-striped table-hover" style="display: none;">
                        <thead class="table-light">
                            <tr>
                                <th>Company Name</th>
                                <th>Address</th>
                                <th>Phone Number</th>
                                <th>Website</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $company->company_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-cell-truncate" title="{{ $company->address ?? 'N/A' }}">
                                        {{ $company->address ? Str::limit($company->address, 50) : 'N/A' }}
                                    </td>
                                    <td>{{ $company->phone_number ?? 'N/A' }}</td>
                                    <td>
                                        @if($company->website)
                                            <a href="{{ $company->website }}" target="_blank" class="text-primary">
                                                {{ Str::limit($company->website, 30) }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('contract-companies.show', $company->id) }}" class="btn btn-sm btn-outline-primary" title="View Company">
                                                <i class="ph-eye"></i>
                                            </a>
                                            <a href="{{ route('contract-companies.edit', $company->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Company">
                                                <i class="ph-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn" title="Delete Company" data-id="{{ $company->id }}">
                                                <i class="ph-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="py-4">
                                            <i class="ph-buildings ph-3x text-muted mb-3"></i>
                                            <h5>No Contract Companies Found</h5>
                                            <p class="text-muted">Create your first contract company to get started</p>
                                            <a href="{{ route('contract-companies.create') }}" class="btn btn-primary">
                                                <i class="ph-plus"></i> Add Contract Company
                                            </a>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this contract company? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<x-datatable-scripts />

<script>
$(document).ready(function() {
    $('#contract-companies-table').DataTable({
        responsive: true,
        scrollX: false,
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
        order: [[0, 'asc']], // default sort by Company Name
        columnDefs: [
            { targets: [4], orderable: false }, // Actions (last column)
            { targets: [0], width: '25%' }, // Company Name
            { targets: [1], width: '30%' }, // Address
            { targets: [2], width: '15%' }, // Phone Number
            { targets: [3], width: '15%' }, // Website
            { targets: [4], width: '15%' }  // Actions
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            search: "Search companies:",
            lengthMenu: "Show _MENU_ companies per page",
            info: "Showing _START_ to _END_ of _TOTAL_ companies",
            infoEmpty: "Showing 0 to 0 of 0 companies",
            infoFiltered: "(filtered from _MAX_ total companies)",
            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" },
            emptyTable: "No contract companies found. Create your first contract company to get started."
        },
        initComplete: function() {
            // Hide loader and show table
            $('#contract-companies-table-loading').addClass('d-none');
            $('#contract-companies-table').show();
            
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

    // Delete button handler
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        const form = $('#deleteForm');
        form.attr('action', '{{ url("contract-companies") }}/' + id);
        $('#deleteModal').modal('show');
    });
});
</script>
@endsection

