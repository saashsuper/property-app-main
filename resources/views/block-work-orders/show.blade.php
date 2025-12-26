@extends('layouts.master')
@section('title')
    Block Work Order Details - PROMAN
@endsection
@section('css')
<style>
    /* Gradient header styling */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    /* Card enhancements */
    .card.border-0.shadow-sm {
        transition: box-shadow 0.2s ease-in-out;
    }
    
    .card.border-0.shadow-sm:hover {
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Work order thumbnail images styling */
    .work-order-thumbnail-container {
        width: 100%;
        height: 120px;
        overflow: hidden;
        border: 2px solid #dee2e6;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
    }
    
    .work-order-thumbnail-container:hover {
        border-color: #0d6efd;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .work-order-thumbnail {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
    }
    
    /* Delete button on thumbnail */
    .remove-photo-btn-thumbnail {
        z-index: 10;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        padding: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    .remove-photo-btn-thumbnail:hover {
        opacity: 1 !important;
        transform: scale(1.1);
    }
    
    .remove-photo-btn-thumbnail i {
        font-size: 1rem;
    }
    
    /* Timeline styling */
    .timeline {
        position: relative;
        margin-left: 0.75rem;
    }
    
    .timeline::before {
        content: "";
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1.25rem;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .timeline-item::before {
        content: "";
        position: absolute;
        left: 4px;
        top: 2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #0d6efd;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #e9ecef;
    }
    
    .timeline-time {
        font-size: 0.8125rem;
        color: #6c757d;
    }
    
    /* Status badge styling */
    .status-badge-large {
        font-size: 1rem;
        padding: 0.5rem 1rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ph-clipboard-text me-2 text-primary"></i>
                        WORK ORDER DETAILS
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-work-orders.index') }}">Work Orders</a></li>
                            <li class="breadcrumb-item active">Work Order Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @if(session('error'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ph-warning me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
        @endif

        @if(session('success'))
                        <div class="row">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ph-check me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                        </div>
        </div>
        @endif

        <!-- Work Order Overview Header with Gradient -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    @php
                        $statusColors = [
                            1 => 'bg-gradient-warning',
                            2 => 'bg-gradient-info',
                            3 => 'bg-gradient-success',
                            4 => 'secondary',
                            5 => 'bg-gradient-warning'
                        ];
                        $gradientClass = $statusColors[$blockWorkOrder->status] ?? 'bg-gradient-primary';
                        $statusText = $blockWorkOrder->status_text ?? 'Unknown';
                        $statusBadgeColors = [
                            1 => 'warning',
                            2 => 'info',
                            3 => 'success',
                            4 => 'secondary',
                            5 => 'warning'
                        ];
                        $badgeColor = $statusBadgeColors[$blockWorkOrder->status] ?? 'secondary';
                    @endphp
                    <div class="card-body {{ $gradientClass }} text-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="mb-1 text-white">Work Order: {{ $blockWorkOrder->ref_no ?? 'WO-' . str_pad($blockWorkOrder->id, 6, '0', STR_PAD_LEFT) }}</h2>
                                <p class="mb-0 text-white-50">{{ $blockWorkOrder->issue ?? 'No description' }}</p>
                                <div class="mt-3">
                                    <span class="badge bg-white bg-opacity-25 text-white me-2 status-badge-large">
                                        <i class="ph-clock me-1"></i>{{ $statusText }}
                                                        </span>
                                    @if($blockWorkOrder->priority)
                                        @php
                                            $priorityColors = [
                                                1 => 'success',
                                                2 => 'info',
                                                3 => 'warning',
                                                4 => 'danger',
                                                5 => 'dark'
                                            ];
                                            $priorityColor = $priorityColors[$blockWorkOrder->priority_id] ?? 'info';
                                        @endphp
                                        <span class="badge bg-white bg-opacity-25 text-white me-2 status-badge-large">
                                            <i class="ph-flag me-1"></i>{{ $blockWorkOrder->priority_text }}
                                        </span>
                                    @endif
                                    @if($blockWorkOrder->block)
                                        <span class="badge bg-white bg-opacity-25 text-white status-badge-large">
                                            <i class="ph-buildings me-1"></i>{{ $blockWorkOrder->block->name }}
                                        </span>
                                    @endif
                                                    </div>
                                                    </div>
                            <div class="col-md-6 text-md-end">
                                <div class="d-flex flex-column align-items-md-end">
                                    <div class="mb-2">
                                        <i class="ph-calendar text-white-50 me-2"></i>
                                        <span class="text-white-50">Created: {{ $blockWorkOrder->created_at ? $blockWorkOrder->created_at->format('d M, Y') : 'N/A' }}</span>
                                                </div>
                                    <div class="mb-2">
                                        <i class="ph-clock text-white-50 me-2"></i>
                                        <span class="text-white-50">{{ $blockWorkOrder->created_at ? $blockWorkOrder->created_at->format('h:i A') : 'N/A' }}</span>
                                            </div>
                                    <div class="d-flex gap-2">
                                        @if($blockWorkOrder->status == 3 && $blockWorkOrder->pdf_path && $blockWorkOrder->pdf_name)
                                            <a href="{{ route('block-work-orders.download-work-docket', $blockWorkOrder) }}" 
                                               class="btn btn-light btn-sm" 
                                               title="Download Work Docket">
                                                <i class="ph-download me-2"></i>Download Work Docket
                                            </a>
                                        @endif
                                        @admin
                                            @if($blockWorkOrder->status == 3)
                                                <form action="{{ route('block-work-orders.regenerate-docket', $blockWorkOrder) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to regenerate the work docket? The old PDF will be replaced.');">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="btn btn-light btn-sm" title="Regenerate Work Docket">
                                                        <i class="ph-arrow-clockwise me-2"></i>Regenerate Work Docket
                                                    </button>
                                                </form>
                                            @endif
                                        @endadmin
                                        @if($blockWorkOrder->status != 3)
                                            <a href="{{ route('block-work-orders.edit', $blockWorkOrder) }}" class="btn btn-light btn-sm">
                                                <i class="ph-pencil me-2"></i>Edit Work Order
                                            </a>
                                        @endif
                                        <a href="{{ route('block-work-orders.index') }}" class="btn btn-outline-light btn-sm">
                                            <i class="ph-list me-2"></i>Work Orders List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Status Alert -->
        @if($blockWorkOrder->status == 3)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="ph-check-circle me-2" style="font-size: 1.5rem;"></i>
                    <div class="flex-grow-1">
                        <strong>Work Order Completed</strong>
                        <p class="mb-0 small">
                            @admin
                                This work order has been completed. As an admin, you can still edit photos and notes, and regenerate the work docket.
                                                @else
                                This work order has been completed and cannot be edited.
                            @endadmin
                        </p>
                    </div>
                    @if($blockWorkOrder->pdf_path && $blockWorkOrder->pdf_name)
                        <a href="{{ route('block-work-orders.download-work-docket', $blockWorkOrder) }}" 
                           class="btn btn-success">
                            <i class="ph-download me-2"></i>Download Work Docket
                        </a>
                                                @endif
                                            </div>
                                        </div>
        </div>
        @endif

        <!-- Main Content Layout -->
        <div class="row">
            <!-- Left Column - Main Content -->
            <div class="col-lg-8">
                <!-- Quick Statistics Cards -->
                <div class="row">
                    <!-- Work Order Information -->
                    <div class="col-lg-6 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Work Order Info</h6>
                                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                                        <i class="ph-clipboard-text text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="mb-2"><strong>Reference:</strong> {{ $blockWorkOrder->ref_no ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong>Status:</strong> 
                                        <span class="badge bg-{{ $badgeColor }}">{{ $statusText }}</span>
                                    </p>
                                    <p class="mb-2"><strong>Priority:</strong> 
                                        @if($blockWorkOrder->priority)
                                                @php
                                                    $priorityColors = [
                                                        1 => 'success',
                                                        2 => 'info',
                                                        3 => 'warning',
                                                        4 => 'danger',
                                                        5 => 'dark'
                                                    ];
                                                $priorityColor = $priorityColors[$blockWorkOrder->priority_id] ?? 'info';
                                                @endphp
                                            <span class="badge bg-{{ $priorityColor }}">{{ $blockWorkOrder->priority_text }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </p>
                                    @if($blockWorkOrder->acceptance_status)
                                        <p class="mb-0"><strong>Acceptance:</strong> 
                                            <span class="badge bg-{{ $blockWorkOrder->acceptance_status === 'accepted' ? 'success' : ($blockWorkOrder->acceptance_status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($blockWorkOrder->acceptance_status) }}
                                            </span>
                                        </p>
                                    @endif
                                            </div>
                                        </div>
                                            </div>
                                        </div>

                    <!-- Location Details -->
                    <div class="col-lg-6 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Location</h6>
                                    <div class="bg-success bg-opacity-10 p-2 rounded">
                                        <i class="ph-map-pin text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    @if($blockWorkOrder->block)
                                        <p class="mb-2">
                                            <strong>Block:</strong> 
                                            <div class="d-flex align-items-center mt-1">
                                                <div class="avatar-sm me-2">
                                                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                                        <i class="ph-buildings"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $blockWorkOrder->block->name }}</div>
                                                    <small class="text-muted">{{ $blockWorkOrder->block->blockType->name ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </p>
                                    @endif
                                    @if($blockWorkOrder->blockBuilding)
                                        <p class="mb-2"><strong>Building:</strong> {{ $blockWorkOrder->blockBuilding->name }}</p>
                                    @endif
                                    @if($blockWorkOrder->blockUnit)
                                        <p class="mb-0">
                                            <strong>Unit:</strong> 
                                            <div class="d-flex align-items-center mt-1">
                                                <div class="avatar-sm me-2">
                                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                                        <i class="ph-house-line"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $blockWorkOrder->blockUnit->unit_name ?? $blockWorkOrder->blockUnit->name ?? 'Unit #' . $blockWorkOrder->blockUnit->id }}</div>
                                                </div>
                                            </div>
                                        </p>
                                    @endif
                                </div>
                            </div>
                                    </div>
                                </div>
                            </div>

                <!-- Work Description -->
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="ph-file-text me-2 text-primary"></i>
                                    Work Description
                                </h6>
                                <p class="mb-0">{{ $blockWorkOrder->issue ?? 'No work description provided' }}</p>
                                    </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Information -->
                <div class="row">
                    <div class="col-lg-6 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="ph-calendar me-2 text-info"></i>
                                    Schedule
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @if($blockWorkOrder->issued_date_time)
                                            <tr>
                                                <td class="fw-medium" style="width: 150px;">Issued:</td>
                                                <td>{{ $blockWorkOrder->issued_date_time->format('M d, Y H:i') }}</td>
                                            </tr>
                                            @endif
                                            @if($blockWorkOrder->preferred_start_date_time)
                                            <tr>
                                                <td class="fw-medium">Preferred Start:</td>
                                                <td>{{ $blockWorkOrder->preferred_start_date_time->format('M d, Y H:i') }}</td>
                                            </tr>
                                            @endif
                                            @if($blockWorkOrder->preferred_end_date_time)
                                            <tr>
                                                <td class="fw-medium">Preferred End:</td>
                                                <td>{{ $blockWorkOrder->preferred_end_date_time->format('M d, Y H:i') }}</td>
                                            </tr>
                                            @endif
                                            @if($blockWorkOrder->deadline_date)
                                            <tr>
                                                <td class="fw-medium">Deadline:</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $blockWorkOrder->deadline_date->format('M d, Y') }}</span>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                        </div>
                                        </div>
                                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="col-lg-6 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="ph-phone me-2 text-success"></i>
                                    Contact Information
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @if($blockWorkOrder->contact_name)
                                            <tr>
                                                <td class="fw-medium" style="width: 150px;">Name:</td>
                                                <td>{{ $blockWorkOrder->contact_name }}</td>
                                            </tr>
                                            @endif
                                            @if($blockWorkOrder->contact_mobile)
                                            <tr>
                                                <td class="fw-medium">Mobile:</td>
                                                <td><a href="tel:{{ $blockWorkOrder->contact_mobile }}" class="text-decoration-none">{{ $blockWorkOrder->contact_mobile }}</a></td>
                                            </tr>
                                            @endif
                                            @if($blockWorkOrder->contact_email)
                                            <tr>
                                                <td class="fw-medium">Email:</td>
                                                <td><a href="mailto:{{ $blockWorkOrder->contact_email }}" class="text-decoration-none">{{ $blockWorkOrder->contact_email }}</a></td>
                                            </tr>
                                            @endif
                                            @if($blockWorkOrder->note_for_access)
                                            <tr>
                                                <td class="fw-medium">Access Note:</td>
                                                <td><small class="text-muted">{{ $blockWorkOrder->note_for_access }}</small></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Issue Details -->
                        @if($blockWorkOrder->blockIssue)
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    <i class="ph-exclamation-triangle me-2 text-danger"></i>
                                    Related Issue Details
                                </h6>
                                        <a href="{{ route('block-issues.show', $blockWorkOrder->blockIssue) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ph-eye me-1"></i> View Full Issue
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <td class="fw-medium" style="width: 150px;">Issue Reference:</td>
                                                        <td>
                                                        <a href="{{ route('block-issues.show', $blockWorkOrder->blockIssue) }}" class="text-decoration-none">
                                                            <span class="badge bg-primary">{{ $blockWorkOrder->blockIssue->ref_no }}</span>
                                                        </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Title:</td>
                                                        <td>{{ $blockWorkOrder->blockIssue->issue ?? 'N/A' }}</td>
                                                    </tr>
                                                    @if($blockWorkOrder->blockIssue->issueType || $blockWorkOrder->blockIssue->issue_type)
                                                    <tr>
                                                        <td class="fw-medium">Type:</td>
                                                        <td>
                                                            <span class="badge bg-secondary">
                                                                {{ $blockWorkOrder->blockIssue->issueType->name ?? $blockWorkOrder->blockIssue->issue_type ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                        @endif
                                                    @if($blockWorkOrder->blockIssue->priority)
                                                    <tr>
                                                        <td class="fw-medium">Priority:</td>
                                                        <td>
                                                        @php
                                                            $issuePriorityColors = [
                                                                1 => 'success',
                                                                2 => 'info',
                                                                3 => 'warning',
                                                                4 => 'danger',
                                                                5 => 'dark'
                                                            ];
                                                            $issuePriorityId = $blockWorkOrder->blockIssue->priority_id ?? null;
                                                            $issuePriorityColor = $issuePriorityColors[$issuePriorityId] ?? 'secondary';
                                                                $issuePriorityText = $blockWorkOrder->blockIssue->priority->label ?? $blockWorkOrder->blockIssue->priority_text ?? 'N/A';
                                                        @endphp
                                                        <span class="badge bg-{{ $issuePriorityColor }}">{{ $issuePriorityText }}</span>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    @if($blockWorkOrder->blockIssue->issueStatus || $blockWorkOrder->blockIssue->status)
                                                    <tr>
                                                        <td class="fw-medium">Status:</td>
                                                        <td>
                                                        @php
                                                            $issueStatusColors = [
                                                                1 => 'warning',
                                                                2 => 'info',
                                                                3 => 'success',
                                                                4 => 'secondary',
                                                                5 => 'danger'
                                                            ];
                                                            $issueStatusId = $blockWorkOrder->blockIssue->issue_status_id ?? $blockWorkOrder->blockIssue->status ?? null;
                                                            $issueStatusColor = $issueStatusColors[$issueStatusId] ?? 'secondary';
                                                                $issueStatusText = $blockWorkOrder->blockIssue->issueStatus->name ?? $blockWorkOrder->blockIssue->status_text ?? 'N/A';
                                                        @endphp
                                                        <span class="badge bg-{{ $issueStatusColor }}">{{ $issueStatusText }}</span>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    @if($blockWorkOrder->blockIssue->created_at)
                                                    <tr>
                                                        <td class="fw-medium" style="width: 150px;">Reported Date:</td>
                                                        <td>{{ $blockWorkOrder->blockIssue->created_at->format('M d, Y H:i') }}</td>
                                                    </tr>
                                                    @endif
                                                    @if($blockWorkOrder->blockIssue->reportedBy)
                                                    <tr>
                                                        <td class="fw-medium">Reported By:</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm me-2">
                                                                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                                                        <i class="ph-user"></i>
                                                                    </span>
                                                    </div>
                                                                <div>
                                                                    <div class="fw-medium">{{ $blockWorkOrder->blockIssue->reportedBy->name }}</div>
                                                                    @if($blockWorkOrder->blockIssue->reportedBy->email)
                                                                        <small class="text-muted">{{ $blockWorkOrder->blockIssue->reportedBy->email }}</small>
                                                                    @endif
                                                </div>
                                                    </div>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    @if($blockWorkOrder->blockIssue->assignedTo)
                                                    <tr>
                                                        <td class="fw-medium">Assigned To:</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm me-2">
                                                                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                                                        <i class="ph-user-circle"></i>
                                                                    </span>
                                                </div>
                                                                <div>
                                                                    <div class="fw-medium">{{ $blockWorkOrder->blockIssue->assignedTo->name }}</div>
                                                                    @if($blockWorkOrder->blockIssue->assignedTo->email)
                                                                        <small class="text-muted">{{ $blockWorkOrder->blockIssue->assignedTo->email }}</small>
                                                                    @endif
                                                    </div>
                                                </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($blockWorkOrder->blockIssue->issue_details || $blockWorkOrder->blockIssue->description)
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="border-top pt-3">
                                                    <strong>Issue Description:</strong>
                                                    <p class="mt-2 mb-0 text-muted">
                                                        {{ $blockWorkOrder->blockIssue->issue_details ?? $blockWorkOrder->blockIssue->description ?? 'No description available' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                <!-- Comments -->
                @if($blockWorkOrder->comment)
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="ph-note me-2 text-warning"></i>
                                    Comments
                                </h6>
                                <div class="text-muted" style="white-space: pre-wrap;">{{ $blockWorkOrder->comment }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                <!-- Notes -->
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    <i class="ph-notes me-2 text-info"></i>
                                    Notes @if($blockWorkOrder->notes) ({{ $blockWorkOrder->notes->count() }}) @endif
                                </h6>
                                @admin
                                    @if($blockWorkOrder->status == 3)
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                                            <i class="ph-plus me-1"></i>Add Note
                                        </button>
                                    @endif
                                @endadmin
                                    </div>
                                    <div class="card-body">
                                @if($blockWorkOrder->notes && $blockWorkOrder->notes->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($blockWorkOrder->notes as $note)
                                        <div class="list-group-item px-0 border-bottom" data-note-id="{{ $note->id }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="avatar-xs me-2">
                                                            <span class="avatar-title bg-info-subtle text-info rounded-circle">
                                                                <i class="ph-user"></i>
                                                            </span>
                                    </div>
                                                        <div>
                                                            <div class="fw-medium small">{{ $note->creator->name ?? 'System' }}</div>
                                                            <small class="text-muted">{{ $note->created_at->format('M d, Y H:i') }}</small>
                                                            @if($note->note_type && $note->note_type !== 'note')
                                                                <span class="badge bg-secondary ms-2">{{ $note->note_type }}</span>
                                                            @endif
                                </div>
                            </div>
                                                    <div class="note-content text-muted" style="white-space: pre-wrap;">{{ $note->note }}</div>
                                                </div>
                                                @admin
                                                    @if($blockWorkOrder->status == 3)
                                                        <div class="ms-2">
                                                            <button type="button" class="btn btn-sm btn-outline-primary edit-note-btn" 
                                                                    data-note-id="{{ $note->id }}" 
                                                                    data-note-content="{{ $note->note }}"
                                                                    title="Edit Note">
                                                                <i class="ph-pencil"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-note-btn" 
                                                                    data-note-id="{{ $note->id }}"
                                                                    title="Delete Note">
                                                                <i class="ph-trash"></i>
                                                            </button>
                        </div>
                        @endif
                                                @endadmin
                                    </div>
                                        </div>
                                        @endforeach
                                        </div>
                                                @else
                                    <div class="text-center text-muted py-4">
                                        <i class="ph-notes fs-1 mb-2"></i>
                                        <p class="mb-0">No notes added yet</p>
                                        @admin
                                            @if($blockWorkOrder->status == 3)
                                                <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                                                    <i class="ph-plus me-1"></i>Add Note
                                                </button>
                                                @endif
                                        @endadmin
                                            </div>
                                @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                <!-- Images -->
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    <i class="ph-images me-2 text-primary"></i>
                                    Images @if($blockWorkOrder->images) ({{ $blockWorkOrder->images->count() }}) @endif
                                </h6>
                                @admin
                                    @if($blockWorkOrder->status == 3)
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotosModal">
                                            <i class="ph-plus me-1"></i>Add Photos
                                        </button>
                                    @endif
                                @endadmin
                                    </div>
                                    <div class="card-body">
                                @if($blockWorkOrder->images && $blockWorkOrder->images->count() > 0)
                                <div class="row" id="workOrderImageGallery">
                                    @foreach($blockWorkOrder->images as $image)
                                    <div class="col-6 col-md-4 col-lg-3 mb-3 position-relative" data-image-id="{{ $image->id }}">
                                        <div class="work-order-thumbnail-container" onclick="openImageModal('{{ $image->image_url }}', '{{ $image->image_name }}')">
                                            <img src="{{ $image->image_url }}" class="work-order-thumbnail" alt="Work Order Image" loading="lazy">
                                        </div>
                                        @admin
                                            @if($blockWorkOrder->status == 3)
                                                <div class="position-absolute top-0 end-0 m-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-photo-btn-thumbnail" 
                                                            data-image-id="{{ $image->id }}" 
                                                            data-image-name="{{ $image->image_name }}"
                                                            title="Delete Image"
                                                            style="padding: 0.25rem 0.4rem; font-size: 0.7rem;">
                                                        <i class="ph-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        @endadmin
                                        </div>
                                    @endforeach
                                        </div>
                                                @else
                                <div class="text-center text-muted py-4">
                                    <i class="ph-images fs-1 mb-2"></i>
                                    <p class="mb-0">No images uploaded yet</p>
                                    @admin
                                        @if($blockWorkOrder->status == 3)
                                            <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#uploadPhotosModal">
                                                <i class="ph-plus me-1"></i>Add Photos
                                            </button>
                                                @endif
                                    @endadmin
                                </div>
                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

            <!-- Right Column - Sidebar -->
            <div class="col-lg-4">
                <!-- Work Docket Card (if completed) -->
                @if($blockWorkOrder->status == 3)
                    @if($blockWorkOrder->pdf_path && $blockWorkOrder->pdf_name)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="ph-file-pdf me-2 text-danger"></i>
                                Work Docket
                            </h6>
                            <div class="text-center mb-3">
                                <div class="bg-danger bg-opacity-10 p-3 rounded-circle d-inline-block mb-2">
                                    <i class="ph-file-pdf text-danger fs-1"></i>
                                    </div>
                                <p class="mb-2"><strong>{{ $blockWorkOrder->pdf_name }}</strong></p>
                                <small class="text-muted d-block mb-3">Generated when work order was completed</small>
                            </div>
                            <a href="{{ route('block-work-orders.download-work-docket', $blockWorkOrder) }}" 
                               class="btn btn-danger w-100" 
                               title="Download Work Docket">
                                <i class="ph-download me-2"></i>Download Work Docket
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                            <div class="alert alert-warning mb-0">
                                <i class="ph-warning me-2"></i>
                                <small>Work docket PDF is not available. The work order is completed but the PDF may not have been generated yet.</small>
                                        </div>
                                        </div>
                                        </div>
                    @endif
                @endif

                <!-- Contractor Information -->
                @if($blockWorkOrder->contractor)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ph-user-circle me-2 text-primary"></i>
                            Contractor
                        </h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-2">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                    <i class="ph-user"></i>
                                </span>
                                        </div>
                            <div>
                                <div class="fw-medium">{{ $blockWorkOrder->contractor->name ?? 'N/A' }}</div>
                                @if($blockWorkOrder->contractor->email)
                                    <small class="text-muted">{{ $blockWorkOrder->contractor->email }}</small>
                                @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- User Information -->
                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ph-info me-2 text-info"></i>
                            User Information
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    @if($blockWorkOrder->issuedBy)
                                    <tr>
                                        <td class="fw-medium" style="width: 100px;">Issued By:</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title bg-info-subtle text-info rounded-circle">
                                                        <i class="ph-user"></i>
                                                    </span>
                                    </div>
                                                <div>
                                                    <div class="fw-medium">{{ $blockWorkOrder->issuedBy->name }}</div>
                                                    @if($blockWorkOrder->issuedBy->email)
                                                        <small class="text-muted">{{ $blockWorkOrder->issuedBy->email }}</small>
                                                    @endif
                                </div>
                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($blockWorkOrder->creator)
                                    <tr>
                                        <td class="fw-medium">Created By:</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title bg-success-subtle text-success rounded-circle">
                                                        <i class="ph-user-plus"></i>
                                                    </span>
                        </div>
                                                <div>
                                                    <div class="fw-medium">{{ $blockWorkOrder->creator->name }}</div>
                                                    @if($blockWorkOrder->creator->email)
                                                        <small class="text-muted">{{ $blockWorkOrder->creator->email }}</small>
                        @endif
                                        </div>
                                            </div>
                                        </td>
                                    </tr>
                                        @endif
                                    @if($blockWorkOrder->created_at)
                                    <tr>
                                        <td class="fw-medium">Created At:</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $blockWorkOrder->created_at->format('M d, Y') }}<br>
                                                {{ $blockWorkOrder->created_at->format('h:i A') }}
                                            </small>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($blockWorkOrder->updater)
                                    <tr>
                                        <td class="fw-medium">Updated By:</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle">
                                                        <i class="ph-pencil"></i>
                                                    </span>
                                    </div>
                                                <div>
                                                    <div class="fw-medium">{{ $blockWorkOrder->updater->name }}</div>
                                                    @if($blockWorkOrder->updater->email)
                                                        <small class="text-muted">{{ $blockWorkOrder->updater->email }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($blockWorkOrder->updated_at)
                                    <tr>
                                        <td class="fw-medium">Updated At:</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $blockWorkOrder->updated_at->format('M d, Y') }}<br>
                                                {{ $blockWorkOrder->updated_at->format('h:i A') }}
                                            </small>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                                </div>
                            </div>
                        </div>

                <!-- Activity Timeline -->
                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="ph-clock-clockwise me-2 text-primary"></i>
                            Activity Timeline
                        </h6>
                                    </div>
                                    <div class="card-body">
                        @php
                            $timelineItems = [];
                            
                            // Collect all timeline items with timestamps
                            $items = [];
                            
                            // Created event
                            $items[] = [
                                'label' => 'Work Order Created',
                                'description' => 'Work order was created',
                                'type' => 'created',
                                'badge' => 'info',
                                'icon' => 'ph-plus-circle',
                                'time' => optional($blockWorkOrder->created_at)->format('M d, Y H:i'),
                                'timestamp' => $blockWorkOrder->created_at,
                                'actor' => optional($blockWorkOrder->creator)->name,
                                'actor_email' => optional($blockWorkOrder->creator)->email ?? null,
                            ];
                            
                            // Add logs from database if they exist
                            if ($blockWorkOrder->logs && $blockWorkOrder->logs->count() > 0) {
                                foreach ($blockWorkOrder->logs as $log) {
                                    $statusColors = [
                                        'created' => 'info',
                                        'updated' => 'secondary',
                                        'status_changed' => 'primary',
                                        'priority_changed' => 'warning',
                                        'started' => 'success',
                                        'paused' => 'warning',
                                        'resumed' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        'work_docket_generated' => 'danger',
                                        'work_docket_regenerated' => 'danger',
                                        'comment_added' => 'info',
                                        'comment_updated' => 'info',
                                        'attachment_added' => 'success',
                                        'attachment_deleted' => 'warning',
                                    ];
                                    
                                    $iconMap = [
                                        'created' => 'ph-plus-circle',
                                        'updated' => 'ph-pencil',
                                        'status_changed' => 'ph-arrow-right',
                                        'priority_changed' => 'ph-flag',
                                        'started' => 'ph-play',
                                        'paused' => 'ph-pause',
                                        'resumed' => 'ph-play',
                                        'completed' => 'ph-check-circle',
                                        'cancelled' => 'ph-x-circle',
                                        'accepted' => 'ph-check',
                                        'rejected' => 'ph-x',
                                        'work_docket_generated' => 'ph-file-pdf',
                                        'work_docket_regenerated' => 'ph-arrow-clockwise',
                                        'comment_added' => 'ph-note',
                                        'comment_updated' => 'ph-note-pencil',
                                        'attachment_added' => 'ph-image',
                                        'attachment_deleted' => 'ph-trash',
                                    ];
                                    
                                    $items[] = [
                                        'label' => ucfirst(str_replace('_', ' ', $log->log_type)),
                                        'description' => $log->description,
                                        'type' => $log->log_type,
                                        'badge' => $statusColors[$log->log_type] ?? 'secondary',
                                        'icon' => $iconMap[$log->log_type] ?? 'ph-circle',
                                        'time' => optional($log->created_at)->format('M d, Y H:i'),
                                        'timestamp' => $log->created_at,
                                        'actor' => optional($log->user)->name,
                                        'actor_email' => optional($log->user)->email ?? null,
                                        'old_value' => $log->old_value,
                                        'new_value' => $log->new_value,
                                        'field_name' => $log->field_name,
                                    ];
                                }
                            }
                            
                            // Sort by timestamp in descending order (newest first)
                            usort($items, function($a, $b) {
                                if (!$a['timestamp'] || !$b['timestamp']) {
                                    return 0;
                                }
                                return $b['timestamp'] <=> $a['timestamp'];
                            });
                            
                            $timelineItems = $items;
                        @endphp

                        @if(count($timelineItems) > 0)
                        <div class="timeline">
                            @foreach($timelineItems as $item)
                            <div class="timeline-item">
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-{{ $item['badge'] }}">
                                            <i class="{{ $item['icon'] }} me-1"></i>{{ $item['label'] }}
                                        </span>
                                        <span class="timeline-time">
                                            <i class="ph-clock me-1"></i>{{ $item['time'] }}
                                        </span>
                                        </div>
                                    
                                    @if(!empty($item['description']))
                                    <div class="text-muted small">
                                        {{ $item['description'] }}
                                        @if(!empty($item['old_value']) && !empty($item['new_value']))
                                            <span class="badge bg-light text-dark ms-2">
                                                {{ $item['old_value'] }} → {{ $item['new_value'] }}
                                            </span>
                                        @endif
                                        </div>
                                    @endif
                                    
                                    @if(!empty($item['actor']))
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-xxs">
                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                <i class="ph-user"></i>
                                            </span>
                                        </div>
                                        <div class="small text-muted">
                                            <span class="d-block fw-medium">{{ $item['actor'] }}</span>
                                            @if(!empty($item['actor_email']))
                                                <span>{{ $item['actor_email'] }}</span>
                                            @endif
                                        </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @else
                        <div class="text-center text-muted py-3">
                            <i class="ph-clock-clockwise fs-1 mb-2"></i>
                            <p class="mb-0">No activity timeline available</p>
                        </div>
                        @endif
                                    </div>
                        </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ph-lightning me-2 text-warning"></i>
                            Quick Actions
                        </h6>
                        <div class="d-grid gap-2">
                                @if($blockWorkOrder->status != 3)
                                    <a href="{{ route('block-work-orders.edit', $blockWorkOrder) }}" class="btn btn-primary btn-sm">
                                        <i class="ph-pencil me-2"></i>Edit Work Order
                                    </a>
                        @endif
                            @if($blockWorkOrder->blockIssue)
                                <a href="{{ route('block-issues.show', $blockWorkOrder->blockIssue) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="ph-eye me-2"></i>View Related Issue
                                </a>
                            @endif
                            @if($blockWorkOrder->block)
                                <a href="{{ route('blocks.show', $blockWorkOrder->block) }}" class="btn btn-outline-info btn-sm">
                                    <i class="ph-buildings me-2"></i>View Block
                                </a>
                            @endif
                            <a href="{{ route('block-work-orders.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ph-arrow-left me-2"></i>Back to List
                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                    </div>
                                        </div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Work Order Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Work Order Image" class="img-fluid" style="max-height: 70vh;">
                                </div>
                            </div>
                        </div>
                                        </div>

<!-- Upload Photos Modal (for completed work orders - admin only) -->
@admin
    @if($blockWorkOrder->status == 3)
    <div class="modal fade" id="uploadPhotosModal" tabindex="-1" aria-labelledby="uploadPhotosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header photo-upload-header border-0 py-3 px-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="photo-upload-icon">
                                <i class="ph-camera"></i>
                            </span>
                            <h5 class="modal-title text-white mb-0" id="uploadPhotosModalLabel">Upload Photos</h5>
                        </div>
                        <p class="text-white-50 mb-0 small">
                            Work Order: <span class="fw-semibold text-white">{{ $blockWorkOrder->ref_no ?? 'N/A' }}</span>
                        </p>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Message container -->
                    <div id="uploadPhotosMessage" class="alert d-none" role="alert">
                        <i class="ph-check-circle me-2"></i>
                        <span class="message-text"></span>
                    </div>
                    
                    <!-- Work Order Info Summary Card -->
                    <div class="row mb-3">
                            <div class="col-12">
                            <div class="card border-0 shadow-sm work-order-upload-summary">
                                <div class="card-body py-3 px-3 px-lg-4">
                                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                        <div>
                                            <p class="text-muted mb-1 small text-uppercase">Work Order</p>
                                            <h5 class="mb-1">{{ $blockWorkOrder->issue ?? $blockWorkOrder->ref_no ?? 'N/A' }}</h5>
                                            <div class="text-secondary small">
                                                @if($blockWorkOrder->blockUnit)
                                                    Unit: <span class="fw-semibold">{{ $blockWorkOrder->blockUnit->unit_name ?? $blockWorkOrder->blockUnit->unit_code ?? 'N/A' }}</span>
                                                @else
                                                    <span class="fw-semibold">No unit assigned</span>
                                                @endif
                                    </div>
                                                    </div>
                                        <div class="d-flex flex-wrap gap-2 work-order-upload-badges text-nowrap">
                                            @if($blockWorkOrder->priority)
                                                <span class="badge rounded-pill 
                                                    @if($blockWorkOrder->priority_id == 1) bg-success
                                                    @elseif($blockWorkOrder->priority_id == 2) bg-info
                                                    @elseif($blockWorkOrder->priority_id == 3) bg-warning
                                                    @elseif($blockWorkOrder->priority_id == 4) bg-danger
                                                    @elseif($blockWorkOrder->priority_id == 5) bg-dark
                                                    @else bg-secondary
                                                    @endif fw-semibold">
                                                    Priority: {{ $blockWorkOrder->priority->name ?? 'N/A' }}
                                                </span>
                                            @endif
                                            @if($blockWorkOrder->jobStatus)
                                                <span class="badge rounded-pill 
                                                    @if($blockWorkOrder->status == 3) bg-success
                                                    @else bg-secondary
                                                    @endif fw-semibold">
                                                    Status: {{ $blockWorkOrder->jobStatus->name ?? 'Completed' }}
                                                </span>
                                            @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                    <!-- Dropzone Container -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted text-uppercase small">Upload Images <span class="text-danger">*</span></label>
                        <div id="workOrderPhotoDropzone" class="dropzone">
                            <div class="dz-message text-center py-4">
                                <div class="mb-2 text-primary">
                                    <i class="ph-cloud-arrow-up fs-1"></i>
                        </div>
                                <h5 class="fw-semibold mb-1">Drag &amp; drop images here</h5>
                                <p class="text-muted mb-0 small">or click to browse your files</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upload Status -->
                    <div class="mb-3">
                        <div id="workOrderPhotoUploadStatus" class="mt-2"></div>
                    </div>
                    
                    <!-- Existing Photos Section (already shown in main view, but can display count here) -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i>Close
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="clearWorkOrderPhotosBtn">
                        <i class="ph-x me-1"></i>Clear All
                    </button>
                    <button type="button" class="btn btn-primary" id="uploadWorkOrderPhotosBtn">
                        <i class="ph-cloud-upload me-1"></i>Upload Photos
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Custom CSS for Work Order Photo Upload Modal -->
    <style>
        .photo-upload-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .photo-upload-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            font-size: 1.25rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .photo-upload-header .btn-close {
            opacity: 0.85;
        }
        
        .photo-upload-header .btn-close:hover {
            opacity: 1;
        }
        
        .work-order-upload-summary {
            background: linear-gradient(135deg, rgba(102,126,234,0.08), rgba(118,75,162,0.08));
            border-radius: 14px;
        }
        
        .work-order-upload-summary h5 {
            font-weight: 600;
            color: #111827;
        }
        
        .work-order-upload-summary .work-order-upload-badges .badge {
            font-size: 0.75rem;
            padding: 0.45rem 0.7rem;
            border-radius: 999px;
            letter-spacing: 0.02em;
        }
        
        #workOrderPhotoDropzone.dropzone {
            min-height: 96px !important;
            border: 2px dashed rgba(102, 126, 234, 0.45) !important;
            border-radius: 12px !important;
            background: #f8f9ff !important;
            transition: all 0.25s ease-in-out;
            padding: 15px 8px !important;
        }
        
        #workOrderPhotoDropzone.dropzone:hover,
        #workOrderPhotoDropzone.dropzone.dz-drag-hover {
            border-color: #667eea !important;
            background: #eef1ff !important;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15) !important;
        }
        
        #workOrderPhotoDropzone .dz-message {
            padding: 15px 8px !important;
            margin: 0 !important;
            text-align: center !important;
            color: #4b5563 !important;
        }
        
        #workOrderPhotoDropzone .dz-message h5 {
            margin: 6px 0 3px 0 !important;
            font-size: 0.9rem !important;
            color: #1f2937 !important;
        }
        
        #workOrderPhotoDropzone .dz-message p {
            margin: 0 !important;
            font-size: 0.75rem !important;
            line-height: 1.3 !important;
        }
        
        #workOrderPhotoDropzone .dz-preview {
            margin: 8px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        #workOrderPhotoDropzone .dz-preview .dz-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        #workOrderPhotoDropzone .dz-preview .dz-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        #workOrderPhotoDropzone .dz-preview .dz-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #workOrderPhotoDropzone .dz-preview .dz-remove:hover {
            background: rgba(220, 53, 69, 1);
        }
    </style>
                        @endif
@endadmin

<!-- Add/Edit Note Modal (for completed work orders - admin only) -->
@admin
    @if($blockWorkOrder->status == 3)
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNoteModalLabel">Add Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                <div class="modal-body">
                    <form id="addNoteForm">
                        @csrf
                        <input type="hidden" id="editNoteId" name="note_id">
                        <div class="mb-3">
                            <label for="noteContent" class="form-label">Note</label>
                            <textarea class="form-control" id="noteContent" name="note" rows="5" required></textarea>
                </div>
                        <div id="addNoteError" class="alert alert-danger d-none"></div>
                        <div id="addNoteSuccess" class="alert alert-success d-none"></div>
                    </form>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveNoteBtn">
                        <i class="ph-check me-1"></i>Save Note
                    </button>
        </div>
    </div>
</div>
    </div>
    @endif
@endadmin

<!-- Delete Photo Confirmation Modal -->
<div class="modal fade" id="deletePhotoModal" tabindex="-1" aria-labelledby="deletePhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePhotoModalLabel">
                    <i class="ph-warning text-warning me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Are you sure you want to delete this photo?</p>
                <p class="text-muted mb-0 small" id="deletePhotoName"></p>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="ph-warning me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeletePhotoBtn">
                    <i class="ph-trash me-1"></i>Yes, Delete Photo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Dropzone CSS -->
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
@endpush

@section('script')
<!-- Dropzone JS -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    // Verify Dropzone is loaded
    if (typeof Dropzone === 'undefined') {
        console.error('Dropzone failed to load from CDN');
        document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"><\/script>');
    }
</script>

<script>
    const workOrderId = {{ $blockWorkOrder->id }};
    const isCompleted = {{ $blockWorkOrder->status == 3 ? 'true' : 'false' }};
    const isAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
    const apiToken = '{{ csrf_token() }}';
    let workOrderPhotoDropzone = null;

    function openImageModal(imageUrl, imageName) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModalLabel').textContent = imageName || 'Work Order Image';
        var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }

    @admin
        @if($blockWorkOrder->status == 3)
        
        // Initialize Dropzone when photo upload modal is shown
        $('#uploadPhotosModal').on('shown.bs.modal', function() {
            // Destroy existing dropzone if it exists
            if (workOrderPhotoDropzone) {
                workOrderPhotoDropzone.destroy();
                workOrderPhotoDropzone = null;
            }
            initializeWorkOrderPhotoDropzone();
        });
        
        // Cleanup when modal is hidden
        $('#uploadPhotosModal').on('hidden.bs.modal', function() {
            if (workOrderPhotoDropzone) {
                workOrderPhotoDropzone.destroy();
                workOrderPhotoDropzone = null;
            }
        });
        
        // Initialize Work Order Photo Dropzone
        function initializeWorkOrderPhotoDropzone() {
            // Disable auto discover to prevent conflicts
            if (typeof Dropzone !== 'undefined') {
                Dropzone.autoDiscover = false;
            }
            
            // Ensure element is clean
            const dropzoneElement = document.getElementById('workOrderPhotoDropzone');
            if (dropzoneElement && dropzoneElement.dropzone) {
                dropzoneElement.dropzone.destroy();
            }
            
            if (typeof Dropzone === 'undefined') {
                console.error('Dropzone is not loaded');
                return;
            }
            
            workOrderPhotoDropzone = new Dropzone("#workOrderPhotoDropzone", {
                url: "#", // Disabled initially, set dynamically on upload
                paramName: "photos",
                uploadMultiple: true,
                parallelUploads: 6,
                maxFiles: 6, // Maximum 6 photos allowed
                maxFilesize: 5, // 5MB per file
                acceptedFiles: "image/*",
                addRemoveLinks: true,
                clickable: true,
                autoProcessQueue: false, // Manual upload via button
                dictDefaultMessage: "Drop images here or click to upload",
                dictRemoveFile: "Remove",
                dictCancelUpload: "Cancel",
                dictUploadCanceled: "Upload canceled",
                dictInvalidFileType: "You can't upload files of this type.",
                dictFileTooBig: "File is too big. Max filesize: 5MB.",
                dictMaxFilesExceeded: "You can not upload more than 6 files.",
                dictResponseError: "Server responded with an error.",
                headers: {
                    'X-CSRF-TOKEN': apiToken
                },
                init: function() {
                    const dz = this;
                    
                    // Handle file addition
                    this.on("addedfile", function(file) {
                        console.log('File added:', file.name);
                        showWorkOrderPhotoMessage('info', 'Photos added to preview. Click "Upload Photos" to save them.');
                    });
                    
                    // Handle sending (when upload starts) - for each file
                    this.on("sending", function(file, xhr, formData) {
                        console.log('Sending file:', file.name);
                        console.log('Upload URL:', dz.options.url);
                        console.log('CSRF Token:', apiToken);
                        // Ensure CSRF token is in formData for Laravel
                        formData.append('_token', apiToken);
                    });
                    
                    // Handle sending multiple files
                    this.on("sendingmultiple", function(files, xhr, formData) {
                        console.log('Sending multiple files:', files.length);
                        // Ensure CSRF token is in formData for Laravel
                        formData.append('_token', apiToken);
                    });
                    
                    // Handle successful upload
                    this.on("successmultiple", function(files, response) {
                        console.log('Upload success:', response);
                        if (response.success) {
                            showWorkOrderPhotoMessage('success', response.message || 'Photos uploaded successfully!');
                            
                            // Clear dropzone
                            dz.removeAllFiles(true);
                            
                            // Close modal and reload page after successful upload
                            setTimeout(() => {
                                $('#uploadPhotosModal').modal('hide');
                                location.reload();
                            }, 1500);
                        } else {
                            showWorkOrderPhotoMessage('danger', response.message || 'Upload failed. Please try again.');
                        }
                    });
                    
                    // Handle upload errors
                    this.on("errormultiple", function(files, response) {
                        console.error('Upload error (multiple):', response);
                        let errorMessage = 'Upload failed. Please try again.';
                        
                        if (response && response.message) {
                            errorMessage = response.message;
                        } else if (response && response.errors) {
                            const errors = [];
                            Object.values(response.errors).forEach(errorArray => {
                                errors.push(...errorArray);
                            });
                            errorMessage = errors.join('<br>');
                        }
                        
                        showWorkOrderPhotoMessage('danger', errorMessage);
                    });
                    
                    // Handle individual file errors
                    this.on("error", function(file, errorMessage, xhr) {
                        console.error('Upload error (single):', errorMessage, xhr);
                        showWorkOrderPhotoMessage('danger', errorMessage);
                    });
                }
            });
        }
        
        // Show message in photo upload modal
        function showWorkOrderPhotoMessage(type, message) {
            const messageDiv = document.getElementById('uploadPhotosMessage');
            const messageText = messageDiv.querySelector('.message-text');
            
            // Remove all alert classes and add the new one
            messageDiv.className = 'alert alert-' + type;
            messageDiv.classList.remove('d-none');
            
            // Set appropriate icon
            const icon = messageDiv.querySelector('i');
            icon.className = 'ph-me-2';
            switch(type) {
                case 'success':
                    icon.classList.add('ph-check-circle');
                    break;
                case 'danger':
                    icon.classList.add('ph-x-circle');
                    break;
                case 'warning':
                    icon.classList.add('ph-warning');
                    break;
                case 'info':
                    icon.classList.add('ph-info-circle');
                    break;
            }
            
            // Set message text (support HTML)
            messageText.innerHTML = message;
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(function() {
                    messageDiv.classList.add('d-none');
                }, 5000);
            }
        }
        
        // Clear all files
        document.getElementById('clearWorkOrderPhotosBtn').addEventListener('click', function() {
            if (workOrderPhotoDropzone) {
                workOrderPhotoDropzone.removeAllFiles(true);
            }
            // Clear any photo upload messages
            document.getElementById('uploadPhotosMessage').classList.add('d-none');
        });
        
        // Upload photos when submit button is clicked
        document.getElementById('uploadWorkOrderPhotosBtn').addEventListener('click', function() {
            console.log('Upload button clicked');
            console.log('Dropzone instance:', workOrderPhotoDropzone);
            console.log('Files in dropzone:', workOrderPhotoDropzone ? workOrderPhotoDropzone.files.length : 0);
            
            if (workOrderPhotoDropzone && workOrderPhotoDropzone.files.length > 0) {
                // Set the correct URL for upload
                const uploadUrl = '{{ route("block-work-orders.upload-photos", $blockWorkOrder) }}';
                console.log('Setting upload URL:', uploadUrl);
                workOrderPhotoDropzone.options.url = uploadUrl;
                
                // Disable button during upload
                this.disabled = true;
                const originalHTML = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Uploading...';
                
                // Process the queue
                console.log('Processing queue...');
                workOrderPhotoDropzone.processQueue();
                
                // Re-enable button after upload completes (handled in success/error events)
                workOrderPhotoDropzone.on("complete", function() {
                    console.log('Upload complete');
                    document.getElementById('uploadWorkOrderPhotosBtn').disabled = false;
                    document.getElementById('uploadWorkOrderPhotosBtn').innerHTML = originalHTML;
                });
            } else {
                console.log('No files to upload');
                showWorkOrderPhotoMessage('warning', 'Please select photos to upload.');
            }
        });

        // Remove Photo - Show Confirmation Modal
        let photoToDelete = { id: null, name: '' };
        
        document.querySelectorAll('.remove-photo-btn-thumbnail').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent opening image modal
                
                photoToDelete.id = this.getAttribute('data-image-id');
                photoToDelete.name = this.getAttribute('data-image-name');
                
                // Update modal with photo name
                document.getElementById('deletePhotoName').textContent = `Photo: ${photoToDelete.name}`;
                
                // Show modal
                const deleteModal = new bootstrap.Modal(document.getElementById('deletePhotoModal'));
                deleteModal.show();
            });
        });
        
        // Confirm Delete Photo
        document.getElementById('confirmDeletePhotoBtn').addEventListener('click', function() {
            const btn = this;
            const originalHTML = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';
            
            fetch('{{ route("block-work-orders.delete-photo", [$blockWorkOrder, "photo" => 0]) }}'.replace('0', photoToDelete.id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': apiToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide modal
                    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deletePhotoModal'));
                    deleteModal.hide();
                    
                    // Reload page to show updated images
                    location.reload();
                } else {
                    alert(data.message || 'Failed to remove photo');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            })
            .catch(error => {
                alert('An error occurred while removing photo');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            });
        });

        // Add Note
        document.getElementById('saveNoteBtn').addEventListener('click', function() {
            const noteContent = document.getElementById('noteContent').value.trim();
            const noteId = document.getElementById('editNoteId').value;
            const errorDiv = document.getElementById('addNoteError');
            const successDiv = document.getElementById('addNoteSuccess');
            
            if (!noteContent) {
                errorDiv.textContent = 'Please enter a note';
                errorDiv.classList.remove('d-none');
                return;
            }
            
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
            
            const url = noteId 
                ? '{{ route("block-work-orders.update-note", [$blockWorkOrder, "note" => 0]) }}'.replace('0', noteId)
                : '{{ route("block-work-orders.add-note", $blockWorkOrder) }}';
            const method = noteId ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': apiToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ note: noteContent })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successDiv.textContent = data.message || 'Note saved successfully';
                    successDiv.classList.remove('d-none');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    errorDiv.textContent = data.message || 'Failed to save note';
                    errorDiv.classList.remove('d-none');
                }
            })
            .catch(error => {
                errorDiv.textContent = 'An error occurred while saving note';
                errorDiv.classList.remove('d-none');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="ph-check me-1"></i>Save Note';
            });
        });

        // Edit Note
        document.querySelectorAll('.edit-note-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const noteId = this.getAttribute('data-note-id');
                const noteContent = this.getAttribute('data-note-content');
                
                document.getElementById('editNoteId').value = noteId;
                document.getElementById('noteContent').value = noteContent;
                document.getElementById('addNoteModalLabel').textContent = 'Edit Note';
                document.getElementById('addNoteError').classList.add('d-none');
                document.getElementById('addNoteSuccess').classList.add('d-none');
                
                var modal = new bootstrap.Modal(document.getElementById('addNoteModal'));
                modal.show();
            });
        });

        // Reset add note modal when closed
        document.getElementById('addNoteModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('editNoteId').value = '';
            document.getElementById('noteContent').value = '';
            document.getElementById('addNoteModalLabel').textContent = 'Add Note';
            document.getElementById('addNoteError').classList.add('d-none');
            document.getElementById('addNoteSuccess').classList.add('d-none');
        });

        // Remove Note
        document.querySelectorAll('.remove-note-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const noteId = this.getAttribute('data-note-id');
                
                if (!confirm('Are you sure you want to delete this note?')) {
                    return;
                }
                
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                
                fetch('{{ route("block-work-orders.delete-note", [$blockWorkOrder, "note" => 0]) }}'.replace('0', noteId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': apiToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to delete note');
                        this.disabled = false;
                        this.innerHTML = '<i class="ph-trash"></i>';
                    }
                })
                .catch(error => {
                    alert('An error occurred while deleting note');
                    this.disabled = false;
                    this.innerHTML = '<i class="ph-trash"></i>';
                });
            });
        });
        @endif
    @endadmin
</script>
@endsection 
