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
                                <span class="text-muted text-uppercase fw-medium">Block Types</span>
                                <h4 class="mb-0">
                                    <a href="{{ route('block-types.index') }}" class="text-decoration-none text-success">
                                        {{ $stats['total_block_types'] }}
                                    </a>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <a href="{{ route('block-types.index') }}" class="text-decoration-none">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                            <i class="ph-grid-four"></i>
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
                                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                                <i class="ph-grid-four"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Block Types</h5>
                                        <p class="card-text text-muted">Manage different types of property blocks</p>
                                        <a href="{{ route('block-types.index') }}" class="btn btn-info">Manage Types</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                                <i class="ph-gear-six"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">System Settings</h5>
                                        <p class="card-text text-muted">Configure system preferences and settings</p>
                                        <a href="#" class="btn btn-warning">Settings</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Orders and Emergency Issues Row -->
        <div class="row">
            <div class="col-xl-4">
                <!-- Recent Blocks -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Blocks</h4>
                    </div>
                    <div class="card-body">
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
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ph-buildings fs-2"></i>
                                    <p class="mt-2">No blocks found</p>
                                    <a href="{{ route('blocks.create') }}" class="btn btn-primary btn-sm">
                                        Create Your First Block
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-4">

                <!-- Pending Work Orders -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="ph-clock me-2 text-warning"></i>Pending Work Orders
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(isset($recentPendingWorkOrders) && $recentPendingWorkOrders->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentPendingWorkOrders as $workOrder)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($workOrder->issue ?? 'No title', 30) }}</h6>
                                                <small class="text-muted">{{ $workOrder->block->name ?? 'N/A' }} - {{ $workOrder->ref_no }}</small>
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        <i class="ph-user me-1"></i>{{ $workOrder->contractor->name ?? 'Unassigned' }}
                                                    </small>
                                                </div>
                                            </div>
                                            <span class="badge bg-warning">Pending</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ph-clock fs-2"></i>
                                    <p class="mt-2">No pending work orders</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ongoing Work Orders -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="ph-spinner-gap me-2 text-info"></i>Ongoing Work Orders
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(isset($recentOngoingWorkOrders) && $recentOngoingWorkOrders->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentOngoingWorkOrders as $workOrder)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($workOrder->issue ?? 'No title', 30) }}</h6>
                                                <small class="text-muted">{{ $workOrder->block->name ?? 'N/A' }} - {{ $workOrder->ref_no }}</small>
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        <i class="ph-user me-1"></i>{{ $workOrder->contractor->name ?? 'Unassigned' }}
                                                    </small>
                                                </div>
                                            </div>
                                            <span class="badge bg-info">In Progress</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ph-spinner-gap fs-2"></i>
                                    <p class="mt-2">No ongoing work orders</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
            <div class="col-xl-4">
                <!-- Emergency Issues -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="ph-warning me-2 text-danger"></i>Emergency Issues
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(isset($emergencyIssues) && $emergencyIssues->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($emergencyIssues as $issue)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($issue->issue ?? 'No title', 30) }}</h6>
                                                <small class="text-muted">{{ $issue->block->name ?? 'N/A' }} - {{ $issue->ref_no }}</small>
                                                <div class="mt-1">
                                                    <span class="badge bg-danger-subtle text-danger">
                                                        {{ $issue->priority->label ?? 'High Priority' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="badge bg-{{ $issue->status_color ?? 'secondary' }}">{{ $issue->status_text ?? 'Open' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ph-shield-check fs-2 text-success"></i>
                                    <p class="mt-2">No emergency issues</p>
                                    <small>All critical issues resolved</small>
                                </div>
                            </div>
                        @endif
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
