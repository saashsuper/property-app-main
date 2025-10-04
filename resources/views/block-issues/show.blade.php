@extends('layouts.master')
@section('title')
    Block Issue Details - PROMAN
@endsection
@section('css')
    <!-- add your css here -->
    <!-- Dropzone CSS -->
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <style>
        /* Vertical timeline */
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
    </style>
@endsection
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Block Issue Details</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-issues.index') }}">Block Issues</a></li>
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
                            <h4 class="card-title mb-0">Block Issue: {{ $blockIssue->ref_no }}</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('block-work-orders.create', ['block_issue_id' => $blockIssue->id]) }}" class="btn btn-success btn-sm">
                                    <i class="ph-plus-circle me-1"></i> Create Work Order
                                </a>
                                <a href="{{ route('block-visits.create', ['block_issue_id' => $blockIssue->id]) }}" class="btn btn-info btn-sm">
                                    <i class="ph-map-pin me-1"></i> Create Site Visit
                                </a>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#createActionModal">
                                    <i class="ph-activity me-1"></i> Create Action
                                </button>
                                <a href="{{ route('block-issues.edit', $blockIssue) }}" class="btn btn-primary btn-sm">
                                    <i class="ph-pencil me-1"></i> Edit
                                </a>
                                <a href="{{ route('block-issues.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ph-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Left content wrapper -->
                            <div class="col-lg-8 order-lg-1">
                                <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Basic Information</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td class="fw-medium" style="width: 150px;">Reference:</td>
                                                <td><span class="badge bg-primary">{{ $blockIssue->ref_no }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Block:</td>
                                                <td>
                                                    @if($blockIssue->block)
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-2">
                                                                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                                                    <i class="ph-buildings"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $blockIssue->block->name }}</div>
                                                                <small class="text-muted">{{ $blockIssue->block->blockType->name ?? 'N/A' }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Title:</td>
                                                <td>{{ $blockIssue->issue ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Description:</td>
                                                <td>{{ $blockIssue->issue_details ?? $blockIssue->issue_details ?? 'No description available' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Priority:</td>
                                                <td>
                                                    @if($blockIssue->priority)
                                                        <span class="badge bg-{{ $blockIssue->priority->btn_class ?? 'secondary' }}">
                                                            {{ $blockIssue->priority->label ?? 'Unknown' }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-{{ $blockIssue->priority_color }}">
                                                            {{ $blockIssue->priority_text }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Status:</td>
                                                <td><span class="badge bg-{{ $blockIssue->status_color }}">{{ $blockIssue->status_text }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Issue Type:</td>
                                                <td>
                                                    @if($blockIssue->issue_type)
                                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $blockIssue->issue_type)) }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Unit:</td>
                                                <td>
                                                    @if($blockIssue->blockUnit)
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-2">
                                                                <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                                                    <i class="ph-house-line"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $blockIssue->blockUnit->name ?? 'Unit #' . $blockIssue->blockUnit->id }}</div>
                                                                <small class="text-muted">{{ $blockIssue->blockUnit->unitType->name ?? 'N/A' }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Assignment Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Assignment Information</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td class="fw-medium" style="width: 150px;">Assigned To:</td>
                                                <td>
                                                    @if($blockIssue->assignedTo)
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-2">
                                                                <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                                                    <i class="ph-user"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $blockIssue->assignedTo->name }}</div>
                                                                <small class="text-muted">{{ $blockIssue->assignedTo->email }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Unassigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Reported By:</td>
                                                <td>
                                                    @if($blockIssue->reportedBy)
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-2">
                                                                <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                                                    <i class="ph-user"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $blockIssue->reportedBy->name }}</div>
                                                                <small class="text-muted">{{ $blockIssue->reportedBy->email }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Created By:</td>
                                                <td>
                                                    @if($blockIssue->creator)
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-2">
                                                                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                                                    <i class="ph-user"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $blockIssue->creator->name }}</div>
                                                                <small class="text-muted">{{ $blockIssue->creator->email }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Created:</td>
                                                <td>{{ $blockIssue->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Last Updated:</td>
                                                <td>{{ $blockIssue->updated_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Issued By:</td>
                                                <td>
                                                    @if($blockIssue->issuedBy)
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-2">
                                                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                                                    <i class="ph-user"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $blockIssue->issuedBy->name }}</div>
                                                                <small class="text-muted">{{ $blockIssue->issuedBy->email }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Issued Date:</td>
                                                <td>
                                                    @if($blockIssue->issued_date_time)
                                                        {{ $blockIssue->issued_date_time->format('M d, Y H:i') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Mobile Issue:</td>
                                                <td>
                                                    @if($blockIssue->is_mobile)
                                                        <span class="badge bg-info">
                                                            <i class="ph-device-mobile me-1"></i>Mobile
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="ph-desktop me-1"></i>Desktop
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            @if($blockIssue->contact_name || $blockIssue->contact_mobile || $blockIssue->contact_email || $blockIssue->contactMethod || $blockIssue->contact_details)
                            <div class="col-12">
                                <h5 class="mb-3">Contact Information</h5>
                                
                                <div class="row">
                                    @if($blockIssue->contactMethod)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                                            <i class="ph-phone-call"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Contact Method</h6>
                                                        <p class="mb-0 text-muted">{{ $blockIssue->contactMethod->name }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($blockIssue->contact_details)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                                            <i class="ph-note"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Contact Details</h6>
                                                        <p class="mb-0 text-muted">{{ $blockIssue->contact_details }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if($blockIssue->contact_name)
                                    <div class="col-md-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                                            <i class="ph-user"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Contact Name</h6>
                                                        <p class="mb-0 text-muted">{{ $blockIssue->contact_name }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($blockIssue->contact_mobile)
                                    <div class="col-md-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                                            <i class="ph-phone"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Contact Mobile</h6>
                                                        <p class="mb-0 text-muted">{{ $blockIssue->contact_mobile }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($blockIssue->contact_email)
                                    <div class="col-md-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                                            <i class="ph-envelope"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Contact Email</h6>
                                                        <p class="mb-0 text-muted">{{ $blockIssue->contact_email }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Schedule Information -->
                            @if($blockIssue->preferred_start_date_time || $blockIssue->preferred_end_date_time)
                            <div class="col-12">
                                <h5 class="mb-3">Schedule Information</h5>
                                
                                <div class="row">
                                    @if($blockIssue->preferred_start_date_time)
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                                            <i class="ph-calendar"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Preferred Start</h6>
                                                        <p class="mb-0 text-muted">{{ $blockIssue->preferred_start_date_time->format('M d, Y H:i') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($blockIssue->preferred_end_date_time)
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                                            <i class="ph-calendar"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Preferred End</h6>
                                                        <p class="mb-0 text-muted">{{ $blockIssue->preferred_end_date_time->format('M d, Y H:i') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            </div> <!-- /.row (inner) -->
                            </div> <!-- /.col-lg-8 -->

                            <!-- Right Column - Status Timeline and Photos -->
                            <div class="col-lg-4 order-lg-2 mt-3 mt-lg-0">
                                <!-- Status Timeline -->
                                <div class="mb-4">
                                    <h5 class="mb-3">Status Timeline</h5>
                                    <div class="card border">
                                        <div class="card-body position-sticky" style="top: 1rem;">
                                        @php
                                            $timelineItems = [];
                                            
                                            // Collect all timeline items with timestamps
                                            $items = [];
                                            
                                            // Created
                                            $items[] = [
                                                'label' => 'Created',
                                                'status' => $blockIssue->status_text,
                                                'badge' => $blockIssue->status_color,
                                                'time' => optional($blockIssue->created_at)->format('M d, Y H:i'),
                                                'timestamp' => $blockIssue->created_at,
                                                'actor' => optional($blockIssue->reportedBy ?? $blockIssue->creator)->name,
                                                'actor_email' => optional($blockIssue->reportedBy ?? $blockIssue->creator)->email
                                            ];
                                            
                                            // Issued (if available)
                                            if (!empty($blockIssue->issued_date_time)) {
                                                $items[] = [
                                                    'label' => 'Issued',
                                                    'status' => $blockIssue->status_text,
                                                    'badge' => $blockIssue->status_color,
                                                    'time' => optional($blockIssue->issued_date_time)->format('M d, Y H:i'),
                                                    'timestamp' => $blockIssue->issued_date_time,
                                                    'actor' => optional($blockIssue->issuedBy)->name,
                                                    'actor_email' => optional($blockIssue->issuedBy)->email
                                                ];
                                            }
                                            
                                            // Preferred window (optional informational)
                                            if (!empty($blockIssue->preferred_start_date_time)) {
                                                $items[] = [
                                                    'label' => 'Preferred Start',
                                                    'status' => 'Scheduled',
                                                    'badge' => 'info',
                                                    'time' => optional($blockIssue->preferred_start_date_time)->format('M d, Y H:i'),
                                                    'timestamp' => $blockIssue->preferred_start_date_time,
                                                    'actor' => optional($blockIssue->reportedBy)->name,
                                                    'actor_email' => optional($blockIssue->reportedBy)->email
                                                ];
                                            }
                                            
                                            if (!empty($blockIssue->preferred_end_date_time)) {
                                                $items[] = [
                                                    'label' => 'Preferred End',
                                                    'status' => 'Scheduled',
                                                    'badge' => 'secondary',
                                                    'time' => optional($blockIssue->preferred_end_date_time)->format('M d, Y H:i'),
                                                    'timestamp' => $blockIssue->preferred_end_date_time,
                                                    'actor' => optional($blockIssue->reportedBy)->name,
                                                    'actor_email' => optional($blockIssue->reportedBy)->email
                                                ];
                                            }
                                            
                                            // Last update (current status)
                                            $items[] = [
                                                'label' => 'Last Update',
                                                'status' => $blockIssue->status_text,
                                                'badge' => $blockIssue->status_color,
                                                'time' => optional($blockIssue->updated_at)->format('M d, Y H:i'),
                                                'timestamp' => $blockIssue->updated_at,
                                                'actor' => optional($blockIssue->updater)->name,
                                                'actor_email' => optional($blockIssue->updater)->email
                                            ];
                                            
                                            // Sort by timestamp in descending order (newest first)
                                            usort($items, function($a, $b) {
                                                return $b['timestamp'] <=> $a['timestamp'];
                                            });
                                            
                                            $timelineItems = $items;
                                        @endphp

                                        <div class="timeline">
                                            @foreach($timelineItems as $item)
                                                <div class="timeline-item">
                                                    <div class="d-flex flex-column gap-1">
                                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                                            <span class="badge bg-{{ $item['badge'] }}">{{ $item['status'] }}</span>
                                                            <strong>{{ $item['label'] }}</strong>
                                                            <span class="timeline-time">{{ $item['time'] }}</span>
                                                        </div>
                                                        @if(!empty($item['actor']))
                                                        <div class="d-flex align-items-center gap-2 ms-0">
                                                            <div class="avatar-xxs">
                                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                    <i class="ph-user"></i>
                                                                </span>
                                                            </div>
                                                            <div class="small text-muted">
                                                                <span class="d-block">{{ $item['actor'] }}</span>
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
                                    </div>
                                </div>
                                
                                <!-- Issue Photos Gallery -->
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">
                                                <i class="ph-images me-2 text-primary"></i>Issue Photos
                                            </h5>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadPhotosModal">
                                                <i class="ph-plus me-1"></i>Upload Photos
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                        <div id="issueImageGallery" class="row g-2">
                                            @if($blockIssue->images && $blockIssue->images->count() > 0)
                                                @foreach($blockIssue->images as $image)
                                                    <div class="col-6" data-image-id="{{ $image->id }}">
                                                        <div class="position-relative">
                                                            <img src="{{ $image->image_url }}" 
                                                                 class="img-fluid rounded" 
                                                                 style="height: 120px; width: 100%; object-fit: cover; cursor: pointer;"
                                                                 alt="{{ $image->display_name }}"
                                                                 data-bs-toggle="modal" 
                                                                 data-bs-target="#imagePreviewModal"
                                                                 data-image-url="{{ $image->image_url }}"
                                                                 data-image-name="{{ $image->display_name }}"
                                                                 data-image-id="{{ $image->id }}">
                                                            
                                                            <div class="position-absolute top-0 end-0 m-1">
                                                                <button type="button" class="btn btn-danger btn-sm delete-issue-image-btn"
                                                                        data-image-id="{{ $image->id }}" 
                                                                        title="Delete Image"
                                                                        style="padding: 0.25rem 0.4rem; font-size: 0.7rem;">
                                                                    <i class="ph-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="col-12 text-center py-4">
                                                    <i class="ph-image text-muted" style="font-size: 3rem;"></i>
                                                    <p class="text-muted mt-2 mb-0">No photos uploaded yet</p>
                                                    <small class="text-muted">Click "Upload Photos" to add photos</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            @if($blockIssue->note_for_access || $blockIssue->comment)
                            <div class="col-12">
                                <h5 class="mb-3">Additional Notes</h5>
                                
                                <div class="row">
                                    @if($blockIssue->note_for_access)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-secondary-subtle text-secondary rounded-circle fs-3">
                                                            <i class="ph-file-text"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Note for Access</h6>
                                                        <p class="mb-0 text-muted">{{ $blockIssue->note_for_access }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($blockIssue->comment)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                                            <i class="ph-chat-circle"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Comment</h6>
                                                        <p class="mb-0 text-muted">{{ $blockIssue->comment }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Related Entities -->
                            @if($blockIssue->siteVisit || $blockIssue->block_visit_id || $blockIssue->block_inspection_id)
                            <div class="col-12">
                                <h5 class="mb-3">Related Entities</h5>
                                
                                <div class="row">
                                    @if($blockIssue->siteVisit)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                                            <i class="ph-map-pin"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Related Site Visit</h6>
                                                        <p class="mb-0 text-muted">
                                                            <a href="{{ route('block-visits.show', $blockIssue->siteVisit) }}" class="text-decoration-none">
                                                                Visit #{{ $blockIssue->siteVisit->id }}
                                                            </a>
                                                        </p>
                                                        @if($blockIssue->siteVisit->scheduled_date_time)
                                                            <small class="text-muted">{{ $blockIssue->siteVisit->scheduled_date_time->format('M d, Y H:i') }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($blockIssue->block_inspection_id)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                                            <i class="ph-clipboard-text"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Related Inspection</h6>
                                                        <p class="mb-0 text-muted">
                                                            <a href="{{ route('block-inspections.show', $blockIssue->block_inspection_id) }}" class="text-decoration-none">
                                                                Inspection #{{ $blockIssue->block_inspection_id }}
                                                            </a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Orders Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-wrench me-2"></i>Work Orders for this Issue
                            <span class="badge bg-primary ms-2">{{ $workOrders->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($workOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Work Order #</th>
                                            <th>Title</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Issued By</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($workOrders as $workOrder)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ $workOrder->ref_no }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $workOrder->title }}</strong>
                                                    @if ($workOrder->description)
                                                        <br><small class="text-muted">{{ Str::limit($workOrder->description, 100) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($workOrder->priority)
                                                        <span class="badge bg-{{ $workOrder->priority->btn_class ?? 'secondary' }}">
                                                            {{ $workOrder->priority->label ?? 'Unknown' }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $workOrder->status_color ?? 'secondary' }}">{{ $workOrder->status_text ?? 'Unknown' }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title rounded-circle bg-primary">
                                                                {{ substr($workOrder->issuedBy->name ?? 'N/A', 0, 1) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $workOrder->issuedBy->name ?? 'N/A' }}</div>
                                                            <small class="text-muted">{{ $workOrder->issuedBy->email ?? '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $workOrder->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('block-work-orders.show', $workOrder->id) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="View Work Order">
                                                            <i class="ph-eye"></i>
                                                        </a>
                                                        <a href="{{ route('block-work-orders.edit', $workOrder->id) }}"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            title="Edit Work Order">
                                                            <i class="ph-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="ph-wrench display-4 text-muted"></i>
                                </div>
                                <h6 class="text-muted">No work orders found for this issue</h6>
                                <p class="text-muted mb-0">Create a work order to get started with resolving this issue.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Site Visits Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-map-pin me-2"></i>Site Visits for this Block
                            <span class="badge bg-info ms-2">{{ $siteVisits->count() + $relatedSiteVisits->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($siteVisits->count() > 0 || $relatedSiteVisits->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Visit #</th>
                                            <th>Job Reason</th>
                                            <th>Status</th>
                                            <th>Scheduled Date</th>
                                            <th>Team Members</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($relatedSiteVisits as $siteVisit)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-info">{{ $siteVisit->id }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $siteVisit->jobReason->name ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $siteVisit->jobStatus->btn_class ?? 'secondary' }}">
                                                        {{ $siteVisit->jobStatus->name ?? 'Unknown' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($siteVisit->scheduled_date_time)
                                                        {{ $siteVisit->scheduled_date_time->format('M d, Y H:i') }}
                                                    @else
                                                        <span class="text-muted">Not scheduled</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($siteVisit->team && $siteVisit->team->count() > 0)
                                                        <div class="d-flex">
                                                            @foreach($siteVisit->team->take(3) as $member)
                                                                <div class="avatar-xs me-1" title="{{ $member->user->name ?? 'N/A' }}">
                                                                    <div class="avatar-title rounded-circle bg-primary">
                                                                        {{ substr($member->user->name ?? 'N', 0, 1) }}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @if($siteVisit->team->count() > 3)
                                                                <div class="avatar-xs">
                                                                    <div class="avatar-title rounded-circle bg-secondary">
                                                                        +{{ $siteVisit->team->count() - 3 }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No team assigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title rounded-circle bg-success">
                                                                {{ substr($siteVisit->createdByUser->name ?? 'N/A', 0, 1) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $siteVisit->createdByUser->name ?? 'N/A' }}</div>
                                                            <small class="text-muted">{{ $siteVisit->createdByUser->email ?? '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('block-visits.show', $siteVisit->id) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="View Site Visit">
                                                            <i class="ph-eye"></i>
                                                        </a>
                                                        <a href="{{ route('block-visits.edit', $siteVisit->id) }}"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            title="Edit Site Visit">
                                                            <i class="ph-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach ($siteVisits as $siteVisit)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $siteVisit->id }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $siteVisit->jobReason->name ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $siteVisit->jobStatus->btn_class ?? 'secondary' }}">
                                                        {{ $siteVisit->jobStatus->name ?? 'Unknown' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($siteVisit->scheduled_date_time)
                                                        {{ $siteVisit->scheduled_date_time->format('M d, Y H:i') }}
                                                    @else
                                                        <span class="text-muted">Not scheduled</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($siteVisit->team && $siteVisit->team->count() > 0)
                                                        <div class="d-flex">
                                                            @foreach($siteVisit->team->take(3) as $member)
                                                                <div class="avatar-xs me-1" title="{{ $member->user->name ?? 'N/A' }}">
                                                                    <div class="avatar-title rounded-circle bg-primary">
                                                                        {{ substr($member->user->name ?? 'N', 0, 1) }}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @if($siteVisit->team->count() > 3)
                                                                <div class="avatar-xs">
                                                                    <div class="avatar-title rounded-circle bg-secondary">
                                                                        +{{ $siteVisit->team->count() - 3 }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No team assigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title rounded-circle bg-success">
                                                                {{ substr($siteVisit->createdByUser->name ?? 'N/A', 0, 1) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $siteVisit->createdByUser->name ?? 'N/A' }}</div>
                                                            <small class="text-muted">{{ $siteVisit->createdByUser->email ?? '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('block-visits.show', $siteVisit->id) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="View Site Visit">
                                                            <i class="ph-eye"></i>
                                                        </a>
                                                        <a href="{{ route('block-visits.edit', $siteVisit->id) }}"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            title="Edit Site Visit">
                                                            <i class="ph-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="ph-map-pin display-4 text-muted"></i>
                                </div>
                                <h6 class="text-muted">No site visits found for this block</h6>
                                <p class="text-muted mb-0">Create a site visit to schedule inspections or maintenance activities.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-activity me-2"></i>Actions for this Issue
                            <span class="badge bg-primary ms-2">{{ $actions->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($actions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Action Type</th>
                                            <th>Description</th>
                                            <th>Performed By</th>
                                            <th>Action Date</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($actions as $action)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $action->action_type)) }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $action->description }}</strong>
                                                    @if ($action->notes)
                                                        <br><small class="text-muted">{{ Str::limit($action->notes, 100) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title rounded-circle bg-primary">
                                                                {{ substr($action->performedBy->name ?? 'N/A', 0, 1) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $action->performedBy->name ?? 'N/A' }}</div>
                                                            <small class="text-muted">{{ $action->performedBy->email ?? '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($action->action_date)
                                                        {{ $action->action_date->format('M d, Y H:i') }}
                                                    @else
                                                        <span class="text-muted">Not set</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $action->status_color ?? 'secondary' }}">{{ ucfirst($action->status) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $action->priority_color ?? 'secondary' }}">{{ ucfirst($action->priority) }}</span>
                                                </td>
                                                <td>
                                                    @if($action->cost)
                                                        ${{ number_format($action->cost, 2) }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="ph-activity display-4 text-muted"></i>
                                </div>
                                <h6 class="text-muted">No actions found for this issue</h6>
                                <p class="text-muted mb-0">Create an action to track progress on resolving this issue.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- Upload Photos Modal -->
<div class="modal fade" id="uploadPhotosModal" tabindex="-1" aria-labelledby="uploadPhotosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPhotosModalLabel">
                    <i class="ph-upload me-2"></i>Upload Issue Photos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Dropzone Container -->
                <div class="mb-3">
                    <label class="form-label">Upload Images <span class="text-danger">*</span></label>
                    <div id="issuePhotoDropzone" class="dropzone">
                        <div class="dz-message">
                            <div class="mb-3">
                                <i class="ph-cloud-upload display-4 text-muted"></i>
                            </div>
                            <h4>Drop images here or click to upload</h4>
                            <p class="text-muted font-size-16">
                                <strong>Requirements:</strong><br>
                                • Maximum 10 images<br>
                                • Each image max 2MB<br>
                                • Total size max 10MB<br>
                                • Formats: JPEG, PNG, JPG, GIF
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div id="uploadStatus" class="mt-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary" id="clearIssuePhotosBtn">
                    <i class="ph-x me-1"></i>Clear All
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Image Confirmation Modal -->
<div class="modal fade" id="deleteImageModal" tabindex="-1" aria-labelledby="deleteImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteImageModalLabel">
                    <i class="ph-warning text-warning me-2"></i>Delete Image
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this image?</p>
                <p class="text-muted mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteImageBtn">
                    <i class="ph-trash me-1"></i>Yes, Delete Image
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal with Carousel -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewModalLabel">
                    <span id="currentImageInfo">Image Preview</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Image Carousel -->
                <div id="imageCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner" id="carouselInner">
                        <!-- Images will be dynamically added here -->
                    </div>
                    
                    <!-- Navigation Arrows -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                
                <!-- Image Info -->
                <div class="p-3 text-center bg-light">
                    <h6 id="previewImageName" class="mb-1"></h6>
                    <small class="text-muted" id="imageCounter"></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <!-- add your js here -->
    <!-- Dropzone JS -->
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        $(document).ready(function() {
            let issuePhotoDropzone;
            
            // Initialize Dropzone when modal is shown
            $('#uploadPhotosModal').on('shown.bs.modal', function() {
                if (!issuePhotoDropzone) {
                    initializeIssuePhotoDropzone();
                }
            });
            
            // Initialize Issue Photo Dropzone
            function initializeIssuePhotoDropzone() {
                // Disable auto discover to prevent conflicts
                Dropzone.autoDiscover = false;
                
                issuePhotoDropzone = new Dropzone("#issuePhotoDropzone", {
                    url: '{{ route("block-issues.upload-photos", $blockIssue->id) }}',
                    paramName: "images",
                    uploadMultiple: true,
                    parallelUploads: 10,
                    maxFiles: 10,
                    maxFilesize: 2, // 2MB per file
                    acceptedFiles: "image/*",
                    addRemoveLinks: true,
                    dictDefaultMessage: "Drop images here or click to upload",
                    dictRemoveFile: "Remove",
                    dictCancelUpload: "Cancel",
                    dictUploadCanceled: "Upload canceled",
                    dictInvalidFileType: "You can't upload files of this type.",
                    dictFileTooBig: "File is too big. Max filesize: 2MB.",
                    dictMaxFilesExceeded: "You can not upload more than 10 files.",
                    dictResponseError: "Server responded with an error.",
                    dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
                    dictRemoveFileConfirmation: "Are you sure you want to remove this file?",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    init: function() {
                        const dz = this;
                        
                        // Custom styling
                        this.on("addedfile", function(file) {
                            // Add custom styling to file preview
                            const preview = file.previewElement;
                            $(preview).addClass('dz-image-preview-custom');
                            
                            // Add file size info
                            const sizeInfo = $(preview).find('.dz-size');
                            if (sizeInfo.length === 0) {
                                $(preview).find('.dz-details').append('<div class="dz-size"><span data-dz-size></span></div>');
                            }
                        });
                        
                        // Handle successful upload
                        this.on("successmultiple", function(files, response) {
                            showAlert('success', response.message);
                            
                            // Close modal after successful upload
                            setTimeout(() => {
                                $('#uploadPhotosModal').modal('hide');
                                location.reload();
                            }, 1500);
                        });
                        
                        // Handle upload errors
                        this.on("errormultiple", function(files, response) {
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
                            
                            showAlert('error', errorMessage);
                        });
                        
                        // Handle individual file errors
                        this.on("error", function(file, errorMessage) {
                            showAlert('error', errorMessage);
                        });
                        
                        // Custom validation for total file size
                        this.on("addedfiles", function(files) {
                            let totalSize = 0;
                            const maxTotalSize = 10 * 1024 * 1024; // 10MB
                            
                            files.forEach(file => {
                                totalSize += file.size;
                            });
                            
                            if (totalSize > maxTotalSize) {
                                showAlert('error', `Total size exceeds 10MB (${(totalSize / 1024 / 1024).toFixed(1)}MB)`);
                                files.forEach(file => {
                                    this.removeFile(file);
                                });
                            }
                        });
                    }
                });
            }
            
            // Clear all files
            $('#clearIssuePhotosBtn').on('click', function() {
                if (issuePhotoDropzone) {
                    issuePhotoDropzone.removeAllFiles(true);
                }
            });
            
            // Handle image click to open carousel
            $(document).on('click', '#issueImageGallery img', function() {
                const clickedImageId = $(this).data('image-id');
                const allImages = [];
                
                // Collect all images
                $('#issueImageGallery .col-6').each(function() {
                    const img = $(this).find('img');
                    allImages.push({
                        id: $(this).data('image-id'),
                        url: img.attr('src'),
                        name: img.attr('alt')
                    });
                });
                
                if (allImages.length === 0) return;
                
                // Build carousel items
                const carouselInner = $('#carouselInner');
                carouselInner.empty();
                
                allImages.forEach((image, index) => {
                    const isActive = image.id == clickedImageId ? 'active' : '';
                    const carouselItem = $(`
                        <div class="carousel-item ${isActive}" data-image-id="${image.id}">
                            <img src="${image.url}" class="d-block w-100" style="max-height: 70vh; object-fit: contain;" alt="${image.name}">
                        </div>
                    `);
                    carouselInner.append(carouselItem);
                });
                
                // Update image info
                const currentImage = allImages.find(img => img.id == clickedImageId);
                if (currentImage) {
                    $('#previewImageName').text(currentImage.name);
                    const currentIndex = allImages.findIndex(img => img.id == clickedImageId) + 1;
                    $('#imageCounter').text(`${currentIndex} of ${allImages.length}`);
                }
                
                // Initialize carousel
                const carousel = new bootstrap.Carousel('#imageCarousel', {
                    interval: false, // Disable auto-slide
                    wrap: true // Enable infinite loop
                });
                
                // Update info when slide changes
                $('#imageCarousel').on('slid.bs.carousel', function (event) {
                    const activeItem = $(event.target).find('.carousel-item.active');
                    const imageId = activeItem.data('image-id');
                    const currentImage = allImages.find(img => img.id == imageId);
                    
                    if (currentImage) {
                        $('#previewImageName').text(currentImage.name);
                        const currentIndex = allImages.findIndex(img => img.id == imageId) + 1;
                        $('#imageCounter').text(`${currentIndex} of ${allImages.length}`);
                    }
                });
                
                // Add keyboard navigation
                $(document).on('keydown', function(e) {
                    if ($('#imagePreviewModal').hasClass('show')) {
                        if (e.key === 'ArrowLeft') {
                            e.preventDefault();
                            $('#imageCarousel').carousel('prev');
                        } else if (e.key === 'ArrowRight') {
                            e.preventDefault();
                            $('#imageCarousel').carousel('next');
                        } else if (e.key === 'Escape') {
                            e.preventDefault();
                            $('#imagePreviewModal').modal('hide');
                        }
                    }
                });
            });
            
            // Delete issue image
            $(document).on('click', '.delete-issue-image-btn', function() {
                const imageId = $(this).data('image-id');
                const imageCard = $(this).closest('.col-6');
                
                // Store the data for the confirmation modal
                $('#confirmDeleteImageBtn').data('image-id', imageId);
                $('#confirmDeleteImageBtn').data('image-card', imageCard);
                
                // Show the confirmation modal
                $('#deleteImageModal').modal('show');
            });
            
            // Handle confirmation button click
            $('#confirmDeleteImageBtn').on('click', function() {
                const imageId = $(this).data('image-id');
                const imageCard = $(this).data('image-card');
                
                // Close the modal
                $('#deleteImageModal').modal('hide');
                
                // Delete the image
                deleteIssueImage(imageId, imageCard);
            });
            
            // Delete image function
            function deleteIssueImage(imageId, imageCard) {
                $.ajax({
                    url: '{{ route("block-issues.delete-image", ":id") }}'.replace(':id', imageId),
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showAlert('success', response.message);
                        imageCard.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if no images left
                            if ($('#issueImageGallery .col-6').length === 0) {
                                $('#issueImageGallery').html(`
                                    <div class="col-12 text-center py-4">
                                        <i class="ph-image text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2 mb-0">No photos uploaded yet</p>
                                        <small class="text-muted">Click "Upload Photos" to add photos</small>
                                    </div>
                                `);
                            }
                        });
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        const errorMessage = response && response.message ? response.message : 'Delete failed. Please try again.';
                        showAlert('error', errorMessage);
                    }
                });
            }
            
            // Show alert function
            function showAlert(type, message) {
                // Remove any existing alerts first
                $('.page-title-box').siblings('.alert').remove();
                
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const alert = $(`
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        <i class="ph-${type === 'success' ? 'check-circle' : 'warning'} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                
                // Insert alert after the page title row
                $('.page-title-box').parent().parent().after(alert);
                
                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    alert.alert('close');
                }, 5000);
            }
        });
    </script>
    
    <style>
        /* Custom Dropzone Styling */
        .dropzone {
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            background: #f8f9fa;
            min-height: 200px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .dropzone:hover {
            border-color: #667eea;
            background: #f0f2ff;
        }

        .dropzone.dz-drag-hover {
            border-color: #667eea;
            background: #e8f0fe;
        }

        .dropzone .dz-message {
            margin: 0;
            color: #6c757d;
        }

        .dropzone .dz-message h4 {
            color: #495057;
            margin-bottom: 10px;
        }

        .dropzone .dz-message p {
            margin-bottom: 0;
            font-size: 14px;
        }

        /* File preview styling */
        .dz-image-preview-custom {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin: 5px;
            padding: 10px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dz-image-preview-custom .dz-image {
            border-radius: 0.25rem;
            overflow: hidden;
        }

        .dz-image-preview-custom .dz-details {
            padding: 5px 0;
            font-size: 12px;
        }

        .dz-image-preview-custom .dz-filename {
            font-weight: 500;
            color: #495057;
        }

        .dz-image-preview-custom .dz-size {
            color: #6c757d;
        }

        .dz-image-preview-custom .dz-progress {
            margin-top: 5px;
        }

        .dz-image-preview-custom .dz-remove {
            color: #dc3545;
            font-weight: bold;
            text-decoration: none;
        }

        .dz-image-preview-custom .dz-remove:hover {
            color: #c82333;
            text-decoration: underline;
        }

        /* Progress bar styling */
        .dz-image-preview-custom .dz-progress .dz-upload {
            background: #667eea;
            border-radius: 2px;
        }

        /* Image Carousel Styling */
        #imageCarousel {
            position: relative;
        }

        #imageCarousel .carousel-item img {
            background: #f8f9fa;
            border-radius: 0.375rem;
        }

        #imageCarousel .carousel-control-prev,
        #imageCarousel .carousel-control-next {
            width: 50px;
            height: 50px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        #imageCarousel .carousel-control-prev:hover,
        #imageCarousel .carousel-control-next:hover {
            opacity: 1;
        }

        #imageCarousel .carousel-control-prev {
            left: 20px;
        }

        #imageCarousel .carousel-control-next {
            right: 20px;
        }

        #imageCarousel .carousel-control-prev-icon,
        #imageCarousel .carousel-control-next-icon {
            width: 20px;
            height: 20px;
        }

        /* Modal styling */
        #imagePreviewModal .modal-dialog {
            max-width: 90vw;
            max-height: 90vh;
        }

        #imagePreviewModal .modal-content {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        #imagePreviewModal .modal-body {
            padding: 0;
        }
    </style>

    <!-- Create Action Modal -->
    <div class="modal fade" id="createActionModal" tabindex="-1" aria-labelledby="createActionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createActionModalLabel">Create Action for Issue: {{ $blockIssue->ref_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createActionForm" method="POST" action="{{ route('block-issues.store-action', $blockIssue->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="action_type" class="form-label">Action Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="action_type" name="action_type" required>
                                    <option value="">Select Action Type</option>
                                    <option value="inspection">Inspection</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="repair">Repair</option>
                                    <option value="replacement">Replacement</option>
                                    <option value="cleaning">Cleaning</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="action_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="action_status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed" selected>Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="action_description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="action_description" name="description" rows="3" required placeholder="Describe the action taken..."></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="action_date" class="form-label">Action Date</label>
                                <input type="datetime-local" class="form-control" id="action_date" name="action_date" value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="action_priority" class="form-label">Priority</label>
                                <select class="form-select" id="action_priority" name="priority">
                                    <option value="low">Low</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="action_cost" class="form-label">Cost</label>
                                <input type="number" class="form-control" id="action_cost" name="cost" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="action_performed_by" class="form-label">Performed By</label>
                                <select class="form-select" id="action_performed_by" name="performed_by">
                                    <option value="">Select User</option>
                                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                        <option value="{{ $user->id }}" {{ $user->id == auth()->id() ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="action_notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="action_notes" name="notes" rows="2" placeholder="Additional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-plus me-1"></i> Create Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 