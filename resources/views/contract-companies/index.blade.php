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

/* Ensure table shows when DataTable is initialized or fails */
#contract-companies-table.dt-initialized,
#contract-companies-table.show-table {
    display: table !important;
}

/* Hide loader when table is shown */
#contract-companies-table-loading.d-none {
    display: none !important;
}
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
                <div class="d-flex align-items-center mb-3 gap-3">
                    <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">
                        Contract Companies
                    </h6>
                    <button class="btn btn-outline-primary btn-sm" id="toggleSearchBtn" title="Search & Filter">
                        <i class="ph-funnel"></i>
                    </button>
                    <div class="d-flex align-items-center gap-2">
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
                        <a href="{{ route('contract-companies.create') }}" class="btn btn-primary btn-sm">
                            <i class="ph-plus me-1"></i>Add Contract Company
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search & Filter Panel (hidden by default) -->
                <div id="searchFilterPanel" class="card mb-3" style="display: none;">
                    <div class="p-2 bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="ph-magnifying-glass me-2"></i>
                            Search & Filter
                        </h6>
                        <button type="button" class="btn btn-sm" id="closeSearchHeaderBtn" title="Close">
                            <i class="ph-x"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <form id="searchFilterForm" method="GET" action="{{ route('contract-companies.index') }}" class="row g-3">
                            <!-- Status Filter -->
                            <div class="col-md-4 mb-3">
                                <label for="search_status" class="form-label">Status</label>
                                <select class="form-select" id="search_status" name="status">
                                    <option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                                </select>
                            </div>
                            <!-- Keyword Search -->
                            <div class="col-md-4 mb-3">
                                <label for="search_keyword" class="form-label">Keyword Search</label>
                                <input type="text" class="form-control" id="search_keyword" name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Company name, address, phone, website...">
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" id="searchFilterBtn">
                            <i class="ph-magnifying-glass me-1"></i> Search
                        </button>
                        <button type="button" class="btn btn-secondary" id="clearSearchBtn">
                            <i class="ph-x me-1"></i> Clear
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="showAllBtn">
                            <i class="ph-list me-1"></i> Show All
                        </button>
                    </div>
                </div>

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
                                            <a href="{{ route('contract-companies.edit', $company->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Company">
                                                <i class="ph-pencil"></i>
                                            </a>
                                            @php
                                                $hasRelatedEntities = $company->hasRelatedEntities();
                                                $actionText = $hasRelatedEntities ? 'Archive' : 'Delete';
                                                $actionIcon = $hasRelatedEntities ? 'ph-archive' : 'ph-trash';
                                                $actionColor = $hasRelatedEntities ? 'warning' : 'danger';
                                            @endphp
                                            <button type="button" class="btn btn-sm btn-outline-{{ $actionColor }} delete-btn" title="{{ $actionText }} Company" data-id="{{ $company->id }}" data-has-related="{{ $hasRelatedEntities ? '1' : '0' }}">
                                                <i class="{{ $actionIcon }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="no-data-row">
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

<!-- Delete/Archive Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="ph-warning text-danger me-2" id="deleteModalIcon"></i>
                    <span id="deleteModalTitle">Confirm Delete Contract Company</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteModalMessage">Are you sure you want to delete this contract company?</p>
                <div id="deleteModalAlert" class="alert mb-2" style="display: none;">
                    <i class="ph-warning me-2"></i>
                    <span id="deleteModalAlertText"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" id="deleteModalButton">
                        <i class="me-1" id="deleteModalButtonIcon"></i>
                        <span id="deleteModalButtonText">Delete</span>
                    </button>
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
    // Filter panel (refer block-issues pattern)
    $('#toggleSearchBtn').on('click', function() {
        $('#searchFilterPanel').slideToggle();
    });
    $('#closeSearchHeaderBtn').on('click', function() {
        $('#searchFilterPanel').slideUp();
    });
    $('#searchFilterBtn').on('click', function() {
        $('#searchFilterForm').submit();
    });
    $('#clearSearchBtn').on('click', function() {
        $('#searchFilterForm')[0].reset();
        window.location.href = '{{ route('contract-companies.index') }}';
    });
    $('#showAllBtn').on('click', function() {
        $('#searchFilterForm')[0].reset();
        window.location.href = '{{ route('contract-companies.index') }}';
        $('#searchFilterPanel').slideUp();
    });
    // Show filter panel when page has active filters
    @if((request()->filled('status') && request('status') !== 'active') || request()->filled('search'))
    $('#searchFilterPanel').show();
    @endif

    // Ensure table is shown and loader is hidden even if DataTable fails
    function showTable() {
        $('#contract-companies-table-loading').addClass('d-none');
        $('#contract-companies-table').addClass('show-table').show().css('display', 'table');
    }

    try {
        // Check if DataTable is available
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTable is not loaded');
            // Fallback: show table without DataTable
            showTable();
            return;
        }

        var table = $('#contract-companies-table').DataTable({
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
                showTable();
                
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
            },
            drawCallback: function() {
                // Ensure table is visible after each draw
                showTable();
            }
        });
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        // Fallback: show table without DataTable
        showTable();
    }

    // Delete/Archive confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteModalTitle = document.getElementById('deleteModalTitle');
    const deleteModalMessage = document.getElementById('deleteModalMessage');
    const deleteModalAlert = document.getElementById('deleteModalAlert');
    const deleteModalAlertText = document.getElementById('deleteModalAlertText');
    const deleteModalButton = document.getElementById('deleteModalButton');
    const deleteModalButtonText = document.getElementById('deleteModalButtonText');
    const deleteModalButtonIcon = document.getElementById('deleteModalButtonIcon');
    const deleteModalIcon = document.getElementById('deleteModalIcon');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const companyId = this.getAttribute('data-id');
            const hasRelated = this.getAttribute('data-has-related') === '1';
            const companyName = this.closest('tr').querySelector('h6').textContent.trim();
            
            deleteForm.action = `{{ url('contract-companies') }}/${companyId}`;
            
            if (hasRelated) {
                // Archive mode
                deleteModalTitle.textContent = 'Confirm Archive Contract Company';
                deleteModalMessage.innerHTML = `Are you sure you want to archive <strong>${companyName}</strong>?`;
                deleteModalAlert.className = 'alert alert-warning mb-2';
                deleteModalAlert.style.display = 'block';
                deleteModalAlertText.innerHTML = 'This contract company has users associated with it. <strong>The company will be archived</strong> and can be restored later. The associated data will remain in the database.';
                deleteModalButton.className = 'btn btn-warning';
                deleteModalButtonText.textContent = 'Archive';
                deleteModalButtonIcon.className = 'ph-archive me-1';
                deleteModalIcon.className = 'ph-warning text-warning me-2';
            } else {
                // Delete mode
                deleteModalTitle.textContent = 'Confirm Delete Contract Company';
                deleteModalMessage.innerHTML = `Are you sure you want to delete <strong>${companyName}</strong>?`;
                deleteModalAlert.className = 'alert alert-danger mb-2';
                deleteModalAlert.style.display = 'block';
                deleteModalAlertText.innerHTML = 'This contract company has no related data. <strong>This action will permanently delete the company</strong> and cannot be undone.';
                deleteModalButton.className = 'btn btn-danger';
                deleteModalButtonText.textContent = 'Delete';
                deleteModalButtonIcon.className = 'ph-trash me-1';
                deleteModalIcon.className = 'ph-warning text-danger me-2';
            }
            
            // Show modal using Bootstrap 5
            const bsModal = new bootstrap.Modal(deleteModal);
            bsModal.show();
        });
    });

    // Fallback timeout to ensure table shows even if DataTable doesn't initialize
    setTimeout(function() {
        if ($('#contract-companies-table-loading').is(':visible')) {
            console.warn('DataTable initialization timeout - showing table anyway');
            showTable();
        }
    }, 3000);
});
</script>
@endsection

