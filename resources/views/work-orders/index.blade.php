@extends('layouts.master')
@section('title')
    Work Orders - PROMAN
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
                    <h4 class="mb-sm-0">Work Orders</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Work Orders</li>
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
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h4 class="card-title mb-0">Work Orders Management</h4>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route('work-orders.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line align-middle me-1"></i> Create Work Order
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filters -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <form method="GET" action="{{ route('work-orders.index') }}" class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Search work orders..." 
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="priority">
                                            <option value="">All Priorities</option>
                                            <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>Low</option>
                                            <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>Normal</option>
                                            <option value="3" {{ request('priority') == '3' ? 'selected' : '' }}>High</option>
                                            <option value="4" {{ request('priority') == '4' ? 'selected' : '' }}>Urgent</option>
                                            <option value="5" {{ request('priority') == '5' ? 'selected' : '' }}>Critical</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="status">
                                            <option value="">All Status</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="ri-search-line"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-refresh-line"></i> Clear Filters
                                </a>
                            </div>
                        </div>

                        <!-- Work Orders Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Priority</th>
                                        <th>Category</th>
                                        <th>Contact</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($workOrders as $workOrder)
                                    <tr>
                                        <td>
                                            <strong>{{ $workOrder->code }}</strong>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $workOrder->fault_description }}">
                                                {{ Str::limit($workOrder->fault_description, 50) }}
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $priorityColors = [
                                                    1 => 'success',
                                                    2 => 'info',
                                                    3 => 'warning',
                                                    4 => 'danger',
                                                    5 => 'dark'
                                                ];
                                                $color = $priorityColors[$workOrder->priority] ?? 'info';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ $workOrder->priority_label }}</span>
                                        </td>
                                        <td>{{ $workOrder->issue_category }}</td>
                                        <td>
                                            @if($workOrder->contact_name)
                                                <div>{{ $workOrder->contact_name }}</div>
                                                <small class="text-muted">{{ $workOrder->contact_email }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $workOrder->deadline }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $workOrder->common_status_id == 1 ? 'success' : 'secondary' }}">
                                                {{ $workOrder->status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <span class="avatar-title bg-soft-primary rounded-3">
                                                        <i class="ri-user-line font-size-16 text-primary"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $workOrder->creator->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $workOrder->created_at->format('M d, Y') }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="ri-more-2-line"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('work-orders.show', $workOrder) }}">
                                                            <i class="ri-eye-line me-2"></i> View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('work-orders.edit', $workOrder) }}">
                                                            <i class="ri-edit-line me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('work-orders.destroy', $workOrder) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this work order?')">
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
                                                <i class="ri-file-list-line fs-2"></i>
                                                <p class="mt-2">No work orders found</p>
                                                <a href="{{ route('work-orders.create') }}" class="btn btn-primary btn-sm">
                                                    Create Your First Work Order
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $workOrders->firstItem() ?? 0 }} to {{ $workOrders->lastItem() ?? 0 }} 
                                of {{ $workOrders->total() }} work orders
                            </div>
                            <div>
                                {{ $workOrders->appends(request()->query())->links() }}
                            </div>
                        </div>
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