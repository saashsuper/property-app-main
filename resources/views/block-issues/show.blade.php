@extends('layouts.master')
@section('title')
    Block Issue Details - PROMAN
@endsection
@section('css')
    <!-- add your css here -->
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
                                                <td>{{ $blockIssue->title ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Description:</td>
                                                <td>{{ $blockIssue->description ?? $blockIssue->issue ?? 'No description available' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Priority:</td>
                                                <td><span class="badge bg-{{ $blockIssue->priority_color }}">{{ $blockIssue->priority_text }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Status:</td>
                                                <td><span class="badge bg-{{ $blockIssue->status_color }}">{{ $blockIssue->status_text }}</span></td>
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
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            @if($blockIssue->contact_name || $blockIssue->contact_mobile || $blockIssue->contact_email)
                            <div class="col-12">
                                <h5 class="mb-3">Contact Information</h5>
                                
                                <div class="row">
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

                            <!-- Status Timeline (Right side) -->
                            <div class="col-lg-4 order-lg-2 mt-3 mt-lg-0">
                                <h5 class="mb-3">Status Timeline</h5>
                                <div class="card border">
                                    <div class="card-body position-sticky" style="top: 1rem;">
                                        @php
                                            $timelineItems = [];
                                            // Created
                                            $timelineItems[] = [
                                                'label' => 'Created',
                                                'status' => $blockIssue->status_text,
                                                'badge' => $blockIssue->status_color,
                                                'time' => optional($blockIssue->created_at)->format('M d, Y H:i'),
                                                'actor' => optional($blockIssue->reportedBy ?? $blockIssue->creator)->name,
                                                'actor_email' => optional($blockIssue->reportedBy ?? $blockIssue->creator)->email
                                            ];
                                            // Issued (if available)
                                            if (!empty($blockIssue->issued_date_time)) {
                                                $timelineItems[] = [
                                                    'label' => 'Issued',
                                                    'status' => $blockIssue->status_text,
                                                    'badge' => $blockIssue->status_color,
                                                    'time' => optional($blockIssue->issued_date_time)->format('M d, Y H:i'),
                                                    'actor' => optional($blockIssue->issuedBy)->name,
                                                    'actor_email' => optional($blockIssue->issuedBy)->email
                                                ];
                                            }
                                            // Preferred window (optional informational)
                                            if (!empty($blockIssue->preferred_start_date_time)) {
                                                $timelineItems[] = [
                                                    'label' => 'Preferred Start',
                                                    'status' => 'Scheduled',
                                                    'badge' => 'info',
                                                    'time' => optional($blockIssue->preferred_start_date_time)->format('M d, Y H:i'),
                                                    'actor' => optional($blockIssue->reportedBy)->name,
                                                    'actor_email' => optional($blockIssue->reportedBy)->email
                                                ];
                                            }
                                            if (!empty($blockIssue->preferred_end_date_time)) {
                                                $timelineItems[] = [
                                                    'label' => 'Preferred End',
                                                    'status' => 'Scheduled',
                                                    'badge' => 'secondary',
                                                    'time' => optional($blockIssue->preferred_end_date_time)->format('M d, Y H:i'),
                                                    'actor' => optional($blockIssue->reportedBy)->name,
                                                    'actor_email' => optional($blockIssue->reportedBy)->email
                                                ];
                                            }
                                            // Last update (current status)
                                            $timelineItems[] = [
                                                'label' => 'Last Update',
                                                'status' => $blockIssue->status_text,
                                                'badge' => $blockIssue->status_color,
                                                'time' => optional($blockIssue->updated_at)->format('M d, Y H:i'),
                                                'actor' => optional($blockIssue->updater)->name,
                                                'actor_email' => optional($blockIssue->updater)->email
                                            ];
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
                            </div>

                            <!-- Additional Notes -->
                            @if($blockIssue->note_for_access)
                            <div class="col-12">
                                <h5 class="mb-3">Additional Notes</h5>
                                
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