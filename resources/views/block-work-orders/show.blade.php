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
                                    <i class="ri-edit-line me-1"></i> Edit
                                </a>
                                <a href="{{ route('block-work-orders.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ri-arrow-left-line me-1"></i> Back
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
                                                            <i class="ri-building-line font-size-16 text-primary"></i>
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
                                            <div class="col-sm-8">{{ $blockWorkOrder->blockIssue->ref_no ?? 'N/A' }}</div>
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

                        <!-- Issue Description -->
                        @if($blockWorkOrder->issue)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Issue Description</h5>
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
                                            <i class="ri-file-pdf-line me-2"></i>
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