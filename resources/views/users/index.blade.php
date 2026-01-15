@extends('layouts.master')

@section('title') @lang('translation.list-users') @endsection

@section('css')
<x-datatable-base />

<style>
/* Users-specific column widths */
#users-table th:nth-child(1) { width: 20%; } /* Name */
#users-table th:nth-child(2) { width: 25%; } /* Email */
#users-table th:nth-child(3) { width: 15%; } /* User Type */
#users-table th:nth-child(4) { width: 15%; } /* Email Verified */
#users-table th:nth-child(5) { width: 15%; } /* Created At */
#users-table th:nth-child(6) { width: 10%; } /* Actions */
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.dashboard') @endslot
@slot('title') @lang('translation.user-management') @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">@lang('translation.list-users')</h4>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Export Buttons -->
                        <div class="btn-group" role="group">
                            <a href="{{ route('export.pdf', 'users') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-danger btn-sm" title="Export to PDF">
                                <i class="ph-file-pdf"></i>
                            </a>
                            <a href="{{ route('export.excel', 'users') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-success btn-sm" title="Export to Excel">
                                <i class="ph-file-xls"></i>
                            </a>
                            <a href="{{ route('export.print', 'users') }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
                                <i class="ph-printer"></i>
                            </a>
                        </div>

                        <!-- Add Button -->
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="ph-plus me-2"></i>@lang('translation.create-user')
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
                    id="users-table-loading" 
                    message="Loading users..." 
                    tableId="users-table" 
                />

                <div class="table-responsive">
                    <table id="users-table" class="table table-bordered table-striped table-hover" style="display: none;">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('translation.name')</th>
                                <th>@lang('translation.email')</th>
                                <th>@lang('translation.user-type')</th>
                                <th>@lang('translation.email-verified')</th>
                                <th>@lang('translation.created-at')</th>
                                <th>@lang('translation.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-cell-truncate" title="{{ $user->email }}">{{ $user->email }}</td>
                                    <td>
                                        @if($user->userType)
                                            <span class="badge bg-info">{{ $user->userType->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">@lang('translation.email-verified')</span>
                                        @else
                                            <span class="badge bg-warning">@lang('translation.email-not-verified')</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-primary" title="View User">
                                                <i class="ph-eye"></i>
                                            </a>
                                            @if(!($isContractorAdmin ?? false) || $user->created_by == auth()->id())
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning" title="Edit User">
                                                <i class="ph-pencil"></i>
                                            </a>
                                            @endif
                                            @if($user->id !== auth()->id() && (!($isContractorAdmin ?? false) || $user->created_by == auth()->id()))
                                                @php
                                                    $hasRelatedEntities = $user->hasRelatedEntities();
                                                    $actionText = $hasRelatedEntities ? 'Archive' : 'Delete';
                                                    $actionIcon = $hasRelatedEntities ? 'ph-archive' : 'ph-trash';
                                                    $actionColor = $hasRelatedEntities ? 'warning' : 'danger';
                                                @endphp
                                                <button type="button" class="btn btn-sm btn-outline-{{ $actionColor }} delete-btn" title="{{ $actionText }} User" data-id="{{ $user->id }}" data-has-related="{{ $hasRelatedEntities ? '1' : '0' }}">
                                                    <i class="{{ $actionIcon }}"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="py-4">
                                            <i class="ph-users ph-3x text-muted mb-3"></i>
                                            <h5>@lang('translation.no-users-found')</h5>
                                            <p class="text-muted">@lang('translation.create-first-user')</p>
                                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                                <i class="ph-plus"></i> @lang('translation.create-user')
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
                    <span id="deleteModalTitle">Confirm Delete User</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteModalMessage">Are you sure you want to delete this user?</p>
                <div id="deleteModalAlert" class="alert mb-2" style="display: none;">
                    <i class="ph-warning me-2"></i>
                    <span id="deleteModalAlertText"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('translation.cancel')</button>
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
    $('#users-table').DataTable({
        responsive: true,
        scrollX: false,
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
        order: [[0, 'asc']], // default sort by Name
        columnDefs: [
            { targets: [5], orderable: false }, // Actions (last column)
            { targets: [0], width: '20%' }, // Name
            { targets: [1], width: '25%' }, // Email
            { targets: [2], width: '15%' }, // User Type
            { targets: [3], width: '15%' }, // Email Verified
            { targets: [4], width: '15%' }, // Created At
            { targets: [5], width: '10%' }  // Actions
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            search: "Search users:",
            lengthMenu: "Show _MENU_ users per page",
            info: "Showing _START_ to _END_ of _TOTAL_ users",
            infoEmpty: "Showing 0 to 0 of 0 users",
            infoFiltered: "(filtered from _MAX_ total users)",
            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
        },
        initComplete: function() {
            // Hide loader and show table
            $('#users-table-loading').addClass('d-none');
            $('#users-table').show();
            
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
            const userId = this.getAttribute('data-id');
            const hasRelated = this.getAttribute('data-has-related') === '1';
            const userName = this.closest('tr').querySelector('h6').textContent.trim();
            
            deleteForm.action = `{{ url('users') }}/${userId}`;
            
            if (hasRelated) {
                // Archive mode
                deleteModalTitle.textContent = 'Confirm Archive User';
                deleteModalMessage.innerHTML = `Are you sure you want to archive <strong>${userName}</strong>?`;
                deleteModalAlert.className = 'alert alert-warning mb-2';
                deleteModalAlert.style.display = 'block';
                deleteModalAlertText.innerHTML = 'This user contains related data (blocks, issues, work orders, etc.). <strong>The user will be archived</strong> and can be restored later. The associated data will remain in the database.';
                deleteModalButton.className = 'btn btn-warning';
                deleteModalButtonText.textContent = 'Archive';
                deleteModalButtonIcon.className = 'ph-archive me-1';
                deleteModalIcon.className = 'ph-warning text-warning me-2';
            } else {
                // Delete mode
                deleteModalTitle.textContent = 'Confirm Delete User';
                deleteModalMessage.innerHTML = `Are you sure you want to delete <strong>${userName}</strong>?`;
                deleteModalAlert.className = 'alert alert-danger mb-2';
                deleteModalAlert.style.display = 'block';
                deleteModalAlertText.innerHTML = 'This user has no related data. <strong>This action will permanently delete the user</strong> and cannot be undone.';
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
});
</script>
@endsection
