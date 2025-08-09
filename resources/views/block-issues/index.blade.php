@extends('layouts.master')
@section('title')
    Block Issues - PROMAN
@endsection
@section('css')
    <!-- add your css here -->
@endsection
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">@lang('translation.block-issues')</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Block Issues</li>
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
                            <h4 class="card-title mb-0">@lang('translation.block-issues-management')</h4>
                            <a href="{{ route('block-issues.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> @lang('translation.create-block-issue-btn')
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filters -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <form method="GET" action="{{ route('block-issues.index') }}" class="row g-3">
                                    <div class="col-md-4">
                                         <input type="text" class="form-control" name="search" 
                                                placeholder="@lang('translation.search-issues')" value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="block_id">
                                             <option value="">@lang('translation.all-blocks')</option>
                                            @foreach($blocks as $block)
                                                <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                                                    {{ $block->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="status">
                                             <option value="">@lang('translation.all-status')</option>
                                             <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>@lang('translation.open')</option>
                                             <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>@lang('translation.in-progress')</option>
                                             <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>@lang('translation.resolved')</option>
                                             <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>@lang('translation.closed')</option>
                                             <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>@lang('translation.on-hold')</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="priority">
                                             <option value="">@lang('translation.all-priority')</option>
                                             <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>@lang('translation.low')</option>
                                             <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>@lang('translation.normal')</option>
                                             <option value="3" {{ request('priority') == '3' ? 'selected' : '' }}>@lang('translation.high')</option>
                                             <option value="4" {{ request('priority') == '4' ? 'selected' : '' }}>@lang('translation.urgent')</option>
                                             <option value="5" {{ request('priority') == '5' ? 'selected' : '' }}>@lang('translation.critical')</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                         <button type="submit" class="btn btn-primary w-100">
                                             <i class="ri-search-line me-1"></i> @lang('translation.search')
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4 text-end">
                                 <a href="{{ route('block-issues.index') }}" class="btn btn-secondary">
                                     <i class="ri-refresh-line me-1"></i> @lang('translation.clear-filters')
                                </a>
                            </div>
                        </div>

                        <!-- Issues Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>@lang('translation.reference')</th>
                                        <th>@lang('translation.title')</th>
                                        <th>@lang('translation.block')</th>
                                        <th>@lang('translation.priority-label')</th>
                                        <th>@lang('translation.status-label')</th>
                                        <th>@lang('translation.assigned-to')</th>
                                        <th>@lang('translation.reported-by')</th>
                                        <th>@lang('translation.created')</th>
                                        <th>@lang('translation.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($blockIssues as $issue)
                                    <tr>
                                        <td>
                                            <span class="fw-medium">{{ $issue->ref_no }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <span class="avatar-title bg-soft-primary rounded-3">
                                                        <i class="ri-error-warning-line font-size-16 text-primary"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $issue->issue ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ Str::limit($issue->issue_details ?? 'No description', 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($issue->block)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <span class="avatar-title bg-soft-info rounded-3">
                                                            <i class="ri-building-line font-size-16 text-info"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $issue->block->name }}</div>
                                                        <small class="text-muted">{{ $issue->block->blockType->name ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
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
                                                            <i class="ri-user-line font-size-16 text-success"></i>
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
                                                            <i class="ri-user-line font-size-16 text-warning"></i>
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
                                            <small class="text-muted">{{ $issue->created_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="ri-settings-3-line"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('block-issues.show', $issue) }}">
                                                             <i class="ri-eye-line me-2"></i> @lang('translation.view')
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('block-issues.edit', $issue) }}">
                                                             <i class="ri-edit-line me-2"></i> @lang('translation.edit')
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#confirmDeleteModal"
                                                                data-action="{{ route('block-issues.destroy', $issue) }}"
                                                                data-ref="{{ $issue->ref_no }}">
                                                            <i class="ri-delete-bin-line me-2"></i> @lang('translation.delete')
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ri-error-warning-line font-size-24 mb-2"></i>
                                                 <p>@lang('translation.no-block-issues-found')</p>
                                                <a href="{{ route('block-issues.create') }}" class="btn btn-primary btn-sm">
                                                     <i class="ri-add-line me-1"></i> @lang('translation.create-first-issue')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($blockIssues->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $blockIssues->firstItem() }} to {{ $blockIssues->lastItem() }} of {{ $blockIssues->total() }} results
                            </div>
                            <div>
                                {{ $blockIssues->appends(request()->query())->links() }}
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
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLabel">@lang('translation.confirm-deletion')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @lang('translation.confirm-deletion-text', ['ref' => '<strong id="deleteRef"></strong>'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('translation.cancel')</button>
                    <form id="deleteForm" method="POST" action="#">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">@lang('translation.delete-verb')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('confirmDeleteModal');
        if (!modalEl) return;
        modalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var action = button.getAttribute('data-action');
            var ref = button.getAttribute('data-ref');
            modalEl.querySelector('#deleteForm').setAttribute('action', action);
            modalEl.querySelector('#deleteRef').textContent = ref || '';
        });
    });
    </script>
@endsection 