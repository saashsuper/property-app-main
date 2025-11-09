@extends('layouts.master')
@section('title')
    Dashboard - PROMAN
@endsection
@section('css')
    <!-- add your css here -->
@endsection
@section('content')
<style>
/* Dashboard specific layout fixes */
.main-content {
    margin-top: 70px; /* Add top margin to account for fixed header */
}

/* Ensure proper spacing for dashboard content */
.page-content {
    padding-top: 1rem;
}

/* Fix for dashboard cards */
.card {
    margin-bottom: 1rem;
}
</style>

        @if($isContractorAdmin ?? false)
        <!-- Contractor Admin Dashboard -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Contractor Users</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('users.index') }}" class="text-decoration-none text-primary">
                                        {{ $stats['total_contractor_users'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('users.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                            <i class="ph-users"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Total Work Orders</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('work-orders.index') }}" class="text-decoration-none text-success">
                                        {{ $stats['total_work_orders'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('work-orders.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                            <i class="ph-list-dashes"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Assigned to Me</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('work-orders.index') }}" class="text-decoration-none text-warning">
                                        {{ $stats['assigned_work_orders'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('work-orders.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                            <i class="ph-user-check"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Completed</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('work-orders.index') }}" class="text-decoration-none text-info">
                                        {{ $stats['completed_work_orders'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('work-orders.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                            <i class="ph-check-circle"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Regular Admin Dashboard -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Total Blocks</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('blocks.index') }}" class="text-decoration-none text-primary">
                                        {{ $stats['total_blocks'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('blocks.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                            <i class="ph-buildings"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Issue Management</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('block-issues.index') }}" class="text-decoration-none text-danger">
                                        {{ $stats['total_issues'] ?? 0 }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('block-issues.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                            <i class="ph-clipboard-text"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Total Units</span>
                                <h4 class="mb-0">{{ $stats['total_units'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                        <i class="ph-house-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Completed Work Orders</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('block-work-orders.index') }}" class="text-decoration-none text-success">
                                        {{ $stats['completed_work_orders'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('block-work-orders.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                            <i class="ph-check"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(!($isContractorAdmin ?? false))
        <!-- Work Order & Emergency Issue Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Pending Work Orders</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('block-work-orders.index') }}" class="text-decoration-none text-warning">
                                        {{ $stats['pending_work_orders'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('block-work-orders.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                            <i class="ph-clock"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Ongoing Work Orders</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('block-work-orders.index') }}" class="text-decoration-none text-info">
                                        {{ $stats['ongoing_work_orders'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('block-work-orders.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                            <i class="ph-spinner-gap"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Emergency Issues</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('block-issues.index') }}" class="text-decoration-none text-danger">
                                        {{ $stats['emergency_issues'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('block-issues.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                            <i class="ph-warning"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Total Work Orders</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('block-work-orders.index') }}" class="text-decoration-none text-primary">
                                        {{ $stats['total_work_orders'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('block-work-orders.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                            <i class="ph-wrench"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Issue Charts Row -->
        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Issues by Status</h4>
                    </div>
                    <div class="card-body">
                        <div id="issues_status_chart" data-colors='["#f1b44c", "#50a5f1", "#34c38f", "#f46a6a", "#74788d"]'></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Issues by Priority</h4>
                    </div>
                    <div class="card-body">
                        <div id="issues_priority_chart" data-colors='["#34c38f", "#50a5f1", "#f1b44c", "#f46a6a", "#343a40"]'></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Issues Trend Chart -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Issues Trend (Last 30 Days)</h4>
                    </div>
                    <div class="card-body">
                        <div id="issues_trend_chart" data-colors='["#50a5f1", "#f1b44c", "#34c38f", "#f46a6a"]'></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Issues by Block</h4>
                    </div>
                    <div class="card-body">
                        <div id="issues_by_block_chart" data-colors='["#50a5f1", "#f1b44c", "#34c38f", "#f46a6a", "#74788d", "#6f42c1"]'></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($isContractorAdmin ?? false)
        <!-- Contractor Admin Quick Actions and Recent Data -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                                <i class="ph-users"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Manage Users</h5>
                                        <p class="card-text text-muted">View and manage contractor users</p>
                                        <a href="{{ route('users.index') }}" class="btn btn-primary">View Users</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                                <i class="ph-plus"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Add New User</h5>
                                        <p class="card-text text-muted">Create a new contractor user</p>
                                        <a href="{{ route('users.create') }}" class="btn btn-success">Create User</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                                <i class="ph-list-dashes"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Block Work Orders</h5>
                                        <p class="card-text text-muted">View assigned block work orders</p>
                                        <a href="{{ route('block-work-orders.index') }}" class="btn btn-info">View Work Orders</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                                <i class="ph-user-check"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">My Assignments</h5>
                                        <p class="card-text text-muted">View work orders assigned to me</p>
                                        <a href="{{ route('work-orders.index') }}" class="btn btn-warning">My Tasks</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Contractor Users</h4>
                    </div>
                    <div class="card-body">
                        @if(isset($recentContractorUsers) && $recentContractorUsers->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentContractorUsers as $user)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ph-users fs-2"></i>
                                    <p class="mt-2">No contractor users found</p>
                                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                        Create Your First User
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Work Orders</h4>
                    </div>
                    <div class="card-body">
                        @if(isset($recentWorkOrders) && $recentWorkOrders->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentWorkOrders as $workOrder)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($workOrder->issue ?? 'No description', 30) }}</h6>
                                                <small class="text-muted">{{ $workOrder->ref_no ?? 'N/A' }}</small>
                                            </div>
                                            <span class="badge bg-{{ $workOrder->status == 3 ? 'success' : 'warning' }}">{{ $workOrder->status_text }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ph-list-dashes fs-2"></i>
                                    <p class="mt-2">No work orders found</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Regular Admin Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                                <i class="ph-buildings"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Manage Blocks</h5>
                                        <p class="card-text text-muted">Create, edit, and manage your property blocks</p>
                                        <a href="{{ route('blocks.index') }}" class="btn btn-primary">View Blocks</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                                <i class="ph-plus"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Add New Block</h5>
                                        <p class="card-text text-muted">Create a new property block with all details</p>
                                        <a href="{{ route('blocks.create') }}" class="btn btn-success">Create Block</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                                <i class="ph-clipboard-text"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Issue Management</h5>
                                        <p class="card-text text-muted">Track and resolve block issues quickly</p>
                                        <a href="{{ route('block-issues.index') }}" class="btn btn-danger">View Issues</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                                <i class="ph-wrench"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Work Orders</h5>
                                        <p class="card-text text-muted">Review and manage open work orders</p>
                                        <a href="{{ route('block-work-orders.index') }}" class="btn btn-info">Manage Work Orders</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Orders and Emergency Issues Row -->
        <div class="row gy-4 pb-4">
            <div class="col-xl-4 col-lg-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">Work Order Activity</h4>
                            <small class="text-muted">Monitor pending and in-progress work orders</small>
                        </div>
                        <a href="{{ route('block-work-orders.index') }}" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills nav-fill mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pending-work-orders-tab" data-bs-toggle="tab" data-bs-target="#pending-work-orders-pane" type="button" role="tab" aria-controls="pending-work-orders-pane" aria-selected="true">
                                    <i class="ph-clock me-1 text-warning"></i> Pending
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ongoing-work-orders-tab" data-bs-toggle="tab" data-bs-target="#ongoing-work-orders-pane" type="button" role="tab" aria-controls="ongoing-work-orders-pane" aria-selected="false">
                                    <i class="ph-spinner-gap me-1 text-info"></i> Ongoing
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pending-work-orders-pane" role="tabpanel" aria-labelledby="pending-work-orders-tab">
                                @if(isset($recentPendingWorkOrders) && $recentPendingWorkOrders->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($recentPendingWorkOrders as $workOrder)
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ Str::limit($workOrder->issue ?? 'No title', 40) }}</h6>
                                                        <small class="text-muted d-block">
                                                            <i class="ph-buildings me-1"></i>{{ $workOrder->block->name ?? 'N/A' }}
                                                            <span class="mx-1">•</span>
                                                            <span>Ref: {{ $workOrder->ref_no }}</span>
                                                        </small>
                                                        <small class="text-muted">
                                                            <i class="ph-user me-1"></i>{{ $workOrder->contractor->name ?? 'Unassigned' }}
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-warning">Pending</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="ph-clock fs-2"></i>
                                        <p class="mt-2 mb-0">No pending work orders</p>
                                        <small>Great job staying on top of things!</small>
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="ongoing-work-orders-pane" role="tabpanel" aria-labelledby="ongoing-work-orders-tab">
                                @if(isset($recentOngoingWorkOrders) && $recentOngoingWorkOrders->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($recentOngoingWorkOrders as $workOrder)
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ Str::limit($workOrder->issue ?? 'No title', 40) }}</h6>
                                                        <small class="text-muted d-block">
                                                            <i class="ph-buildings me-1"></i>{{ $workOrder->block->name ?? 'N/A' }}
                                                            <span class="mx-1">•</span>
                                                            <span>Ref: {{ $workOrder->ref_no }}</span>
                                                        </small>
                                                        <small class="text-muted">
                                                            <i class="ph-user me-1"></i>{{ $workOrder->contractor->name ?? 'Unassigned' }}
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-info">In Progress</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="ph-spinner-gap fs-2"></i>
                                        <p class="mt-2 mb-0">No ongoing work orders</p>
                                        <small>All assignments are either pending or completed.</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="ph-warning me-2 text-danger"></i>Emergency Issue Alerts
                        </h4>
                        <small class="text-muted">Critical issues requiring immediate attention</small>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="flex-grow-1">
                            @if(isset($emergencyIssues) && $emergencyIssues->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($emergencyIssues as $issue)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ Str::limit($issue->issue ?? 'No title', 42) }}</h6>
                                                    <small class="text-muted d-block">
                                                        <i class="ph-buildings me-1"></i>{{ $issue->block->name ?? 'N/A' }}
                                                        <span class="mx-1">•</span>
                                                        <span>Ref: {{ $issue->ref_no }}</span>
                                                    </small>
                                                    <span class="badge bg-danger-subtle text-danger mt-2">
                                                        {{ $issue->priority->label ?? 'High Priority' }}
                                                    </span>
                                                </div>
                                                <span class="badge bg-{{ $issue->status_color ?? 'secondary' }}">{{ $issue->status_text ?? 'Open' }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="ph-shield-check fs-2 text-success"></i>
                                    <p class="mt-2 mb-0">No emergency issues</p>
                                    <small>All critical issues resolved</small>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0 mt-auto text-end">
                        <a href="{{ route('block-issues.index') }}" class="btn btn-sm btn-outline-danger">
                            Go to Issue Management
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="ph-buildings me-2 text-primary"></i>Recent Blocks
                        </h4>
                        <small class="text-muted">Latest additions from your portfolio</small>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="flex-grow-1">
                            @if(isset($recentBlocks) && $recentBlocks->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($recentBlocks as $block)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $block->name }}</h6>
                                                <small class="text-muted">{{ $block->blockType->name ?? 'N/A' }}</small>
                                            </div>
                                            <a href="{{ route('blocks.show', $block) }}" class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="ph-buildings fs-2"></i>
                                    <p class="mt-2 mb-0">No blocks found</p>
                                    <small>Add a block to start tracking assets.</small>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0 mt-auto text-end">
                        <a href="{{ route('blocks.create') }}" class="btn btn-sm btn-outline-primary">
                            Create Block
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
@endsection

@section('script')
    <!-- add your js here -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="{{ URL::asset('resources/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('resources/js/pages/dashboard-issues.init.js') }}"></script>
@endsection
