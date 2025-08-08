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
                    <h4 class="mb-sm-0">Block Issues</h4>
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
                            <h4 class="card-title mb-0">Block Issues Management</h4>
                            <a href="{{ route('block-issues.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> Create Block Issue
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
                                               placeholder="Search issues..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="block_id">
                                            <option value="">All Blocks</option>
                                            @foreach($blocks as $block)
                                                <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                                                    {{ $block->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="status">
                                            <option value="">All Status</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Open</option>
                                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>In Progress</option>
                                            <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Resolved</option>
                                            <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Closed</option>
                                            <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>On Hold</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="priority">
                                            <option value="">All Priority</option>
                                            <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>Low</option>
                                            <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>Normal</option>
                                            <option value="3" {{ request('priority') == '3' ? 'selected' : '' }}>High</option>
                                            <option value="4" {{ request('priority') == '4' ? 'selected' : '' }}>Urgent</option>
                                            <option value="5" {{ request('priority') == '5' ? 'selected' : '' }}>Critical</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="ri-search-line me-1"></i> Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('block-issues.index') }}" class="btn btn-secondary">
                                    <i class="ri-refresh-line me-1"></i> Clear Filters
                                </a>
                            </div>
                        </div>

                        <!-- Issues Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Title</th>
                                        <th>Block</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Reported By</th>
                                        <th>Created</th>
                                        <th>Actions</th>
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
                                                            <i class="ri-eye-line me-2"></i> View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('block-issues.edit', $issue) }}">
                                                            <i class="ri-edit-line me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('block-issues.destroy', $issue) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this block issue?')">
                                                                <i class="ri-delete-bin-line me-2"></i> Delete
                                                            </button>
                                                        </form>
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
                                                <p>No block issues found</p>
                                                <a href="{{ route('block-issues.create') }}" class="btn btn-primary btn-sm">
                                                    <i class="ri-add-line me-1"></i> Create First Issue
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
    <!-- add your js here -->
@endsection 