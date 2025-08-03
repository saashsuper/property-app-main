@extends('layouts.master')
@section('title')
    Issues - PROMAN
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
                    <h4 class="mb-sm-0">General Issues</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Issues</li>
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
                            <h4 class="card-title mb-0">General Issues Management</h4>
                            <a href="{{ route('issues.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> Create Issue
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filters -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <form method="GET" action="{{ route('issues.index') }}" class="row g-3">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Search issues..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="category">
                                            <option value="">All Categories</option>
                                            <option value="Maintenance" {{ request('category') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                            <option value="Repair" {{ request('category') == 'Repair' ? 'selected' : '' }}>Repair</option>
                                            <option value="Security" {{ request('category') == 'Security' ? 'selected' : '' }}>Security</option>
                                            <option value="Cleaning" {{ request('category') == 'Cleaning' ? 'selected' : '' }}>Cleaning</option>
                                            <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
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
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="ri-search-line me-1"></i> Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('issues.index') }}" class="btn btn-secondary">
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
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Reported By</th>
                                        <th>Location</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($issues as $issue)
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
                                                    <div class="fw-medium">{{ $issue->title }}</div>
                                                    <small class="text-muted">{{ Str::limit($issue->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $issue->category }}</span>
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
                                            <span class="text-muted">{{ $issue->location ?? 'N/A' }}</span>
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
                                                        <a class="dropdown-item" href="{{ route('issues.show', $issue) }}">
                                                            <i class="ri-eye-line me-2"></i> View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('issues.edit', $issue) }}">
                                                            <i class="ri-edit-line me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('issues.destroy', $issue) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this issue?')">
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
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ri-error-warning-line font-size-24 mb-2"></i>
                                                <p>No issues found</p>
                                                <a href="{{ route('issues.create') }}" class="btn btn-primary btn-sm">
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
                        @if($issues->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $issues->firstItem() }} to {{ $issues->lastItem() }} of {{ $issues->total() }} results
                            </div>
                            <div>
                                {{ $issues->appends(request()->query())->links() }}
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