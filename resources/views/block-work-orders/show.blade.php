@extends('layouts.master')
@section('title')
    Block Work Order Details - PROMAN
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
                    <h4 class="mb-sm-0">Block Work Order Details</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-work-orders.index') }}">Block Work Orders</a></li>
                            <li class="breadcrumb-item active">Details</li>
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
                            <h4 class="card-title mb-0">Block Work Order: {{ $blockWorkOrder->ref_no }}</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('block-work-orders.edit', $blockWorkOrder) }}" class="btn btn-primary btn-sm">
                                    <i class="ph-pencil me-1"></i> Edit
                                </a>
                                <a href="{{ route('block-work-orders.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ph-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Reference No:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->ref_no }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Block:</strong></div>
                                            <div class="col-sm-8">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <span class="avatar-title bg-soft-primary rounded-3">
                                                            <i class="ph-buildings font-size-16 text-primary"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $blockWorkOrder->block->name }}</div>
                                                        <small class="text-muted">{{ $blockWorkOrder->block->blockType->name ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Block Issue:</strong></div>
                                            <div class="col-sm-8">
                                                @if($blockWorkOrder->blockIssue)
                                                    <a href="{{ route('block-issues.show', $blockWorkOrder->blockIssue) }}" class="text-decoration-none">
                                                        <span class="badge bg-primary">{{ $blockWorkOrder->blockIssue->ref_no }}</span>
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Priority:</strong></div>
                                            <div class="col-sm-8">
                                                @php
                                                    $priorityColors = [
                                                        1 => 'success',
                                                        2 => 'info',
                                                        3 => 'warning',
                                                        4 => 'danger',
                                                        5 => 'dark'
                                                    ];
                                                    $color = $priorityColors[$blockWorkOrder->priority_id] ?? 'info';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">{{ $blockWorkOrder->priority_text }}</span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Status:</strong></div>
                                            <div class="col-sm-8">
                                                @php
                                                    $statusColors = [
                                                        1 => 'warning',
                                                        2 => 'info',
                                                        3 => 'success',
                                                        4 => 'secondary',
                                                        5 => 'danger'
                                                    ];
                                                    $color = $statusColors[$blockWorkOrder->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">{{ $blockWorkOrder->status_text }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Location Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Block Unit:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->blockUnit->unit_name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Block Building:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->blockBuilding->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Contractor ID:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->contractor_id ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Repair Category:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->repair_category_id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Issue Details -->
                        @if($blockWorkOrder->blockIssue)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="ph-file-text me-2"></i>Related Issue Details
                                        </h5>
                                        <a href="{{ route('block-issues.show', $blockWorkOrder->blockIssue) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ph-eye me-1"></i> View Full Issue
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row mb-3">
                                                    <div class="col-sm-4"><strong>Issue Reference:</strong></div>
                                                    <div class="col-sm-8">
                                                        <a href="{{ route('block-issues.show', $blockWorkOrder->blockIssue) }}" class="text-decoration-none">
                                                            <span class="badge bg-primary">{{ $blockWorkOrder->blockIssue->ref_no }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4"><strong>Issue Title:</strong></div>
                                                    <div class="col-sm-8">{{ $blockWorkOrder->blockIssue->issue ?? 'N/A' }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4"><strong>Issue Type:</strong></div>
                                                    <div class="col-sm-8">
                                                        @if($blockWorkOrder->blockIssue->issueType)
                                                            <span class="badge bg-info">{{ $blockWorkOrder->blockIssue->issueType->name }}</span>
                                                        @elseif($blockWorkOrder->blockIssue->issue_type)
                                                            <span class="badge bg-info">{{ $blockWorkOrder->blockIssue->issue_type }}</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4"><strong>Priority:</strong></div>
                                                    <div class="col-sm-8">
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
                                                            $issuePriorityText = $blockWorkOrder->blockIssue->priority_text ?? 'N/A';
                                                        @endphp
                                                        <span class="badge bg-{{ $issuePriorityColor }}">{{ $issuePriorityText }}</span>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4"><strong>Status:</strong></div>
                                                    <div class="col-sm-8">
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
                                                            $issueStatusText = $blockWorkOrder->blockIssue->status_text ?? 'N/A';
                                                        @endphp
                                                        <span class="badge bg-{{ $issueStatusColor }}">{{ $issueStatusText }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row mb-3">
                                                    <div class="col-sm-4"><strong>Reported Date:</strong></div>
                                                    <div class="col-sm-8">
                                                        {{ $blockWorkOrder->blockIssue->created_at ? $blockWorkOrder->blockIssue->created_at->format('M d, Y H:i') : 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4"><strong>Reported By:</strong></div>
                                                    <div class="col-sm-8">
                                                        {{ $blockWorkOrder->blockIssue->reportedBy->name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4"><strong>Assigned To:</strong></div>
                                                    <div class="col-sm-8">
                                                        {{ $blockWorkOrder->blockIssue->assignedTo->name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                @if($blockWorkOrder->blockIssue->blockUnit)
                                                <div class="row mb-3">
                                                    <div class="col-sm-4"><strong>Issue Unit:</strong></div>
                                                    <div class="col-sm-8">
                                                        <span class="badge bg-secondary">{{ $blockWorkOrder->blockIssue->blockUnit->unit_name }}</span>
                                                    </div>
                                                </div>
                                                @endif
                                                @if($blockWorkOrder->blockIssue->blockBuilding)
                                                <div class="row mb-3">
                                                    <div class="col-sm-4"><strong>Issue Building:</strong></div>
                                                    <div class="col-sm-8">{{ $blockWorkOrder->blockIssue->blockBuilding->name }}</div>
                                                </div>
                                                @endif
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
                                        
                                        @if($blockWorkOrder->blockIssue->contact_name || $blockWorkOrder->blockIssue->contact_email || $blockWorkOrder->blockIssue->contact_mobile)
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="border-top pt-3">
                                                    <strong>Issue Contact Information:</strong>
                                                    <div class="mt-2">
                                                        @if($blockWorkOrder->blockIssue->contact_name)
                                                            <div><strong>Name:</strong> {{ $blockWorkOrder->blockIssue->contact_name }}</div>
                                                        @endif
                                                        @if($blockWorkOrder->blockIssue->contact_email)
                                                            <div><strong>Email:</strong> {{ $blockWorkOrder->blockIssue->contact_email }}</div>
                                                        @endif
                                                        @if($blockWorkOrder->blockIssue->contact_mobile)
                                                            <div><strong>Mobile:</strong> {{ $blockWorkOrder->blockIssue->contact_mobile }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Work Order Issue Description (if different from related issue) -->
                        @if($blockWorkOrder->issue && (!$blockWorkOrder->blockIssue || $blockWorkOrder->issue !== $blockWorkOrder->blockIssue->issue))
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Work Order Issue Description</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $blockWorkOrder->issue }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row mt-3">
                            <!-- Schedule Information -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Schedule</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Issued Date:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->issued_date_time ? $blockWorkOrder->issued_date_time->format('M d, Y H:i') : 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Preferred Start:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->preferred_start_date_time ? $blockWorkOrder->preferred_start_date_time->format('M d, Y H:i') : 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Preferred End:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->preferred_end_date_time ? $blockWorkOrder->preferred_end_date_time->format('M d, Y H:i') : 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Deadline:</strong></div>
                                            <div class="col-sm-8">
                                                @if($blockWorkOrder->deadline_date)
                                                    <span class="badge bg-info">{{ $blockWorkOrder->deadline_date->format('M d, Y') }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Contact Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Name:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->contact_name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Mobile:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->contact_mobile ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Email:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->contact_email ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Access Note:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->note_for_access ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comments -->
                        @if($blockWorkOrder->comment)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Comments</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $blockWorkOrder->comment }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- User Information -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">User Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Issued By:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->issuedBy->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Created By:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->creator->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Created At:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->created_at->format('M d, Y H:i') }}</div>
                                        </div>
                                        @if($blockWorkOrder->updated_by)
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Updated By:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->updater->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Updated At:</strong></div>
                                            <div class="col-sm-8">{{ $blockWorkOrder->updated_at->format('M d, Y H:i') }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Document -->
                        @if($blockWorkOrder->pdf_name)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">PDF Document</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info mb-0">
                                                                                            <i class="ph-file-pdf me-2"></i>
                                            <a href="{{ $blockWorkOrder->pdf_url }}" target="_blank" class="alert-link">
                                                {{ $blockWorkOrder->pdf_name }}
                                            </a>
                                            <small class="text-muted ms-2">(Click to view)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Images -->
                        @if($blockWorkOrder->images->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Images ({{ $blockWorkOrder->images->count() }})</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($blockWorkOrder->images as $image)
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <img src="{{ $image->image_url }}" class="card-img-top" alt="Work Order Image" style="height: 200px; object-fit: cover;">
                                                    <div class="card-body p-2">
                                                        <small class="text-muted">{{ $image->image_name }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
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