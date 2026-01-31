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

        /* Card enhancements */
        .card.border.shadow-sm {
            transition: box-shadow 0.2s ease-in-out;
        }
        .card.border.shadow-sm:hover {
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1) !important;
        }

        /* Avatar enhancements */
        .avatar-xs {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .avatar-xs .avatar-title {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        /* Issue thumbnail images styling */
        .issue-thumbnail-container {
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

        .issue-thumbnail-container:hover {
            border-color: #0d6efd;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .issue-thumbnail {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }

        /* Ensure all thumbnail containers have same dimensions */
        #issueImageGallery .col-6 {
            margin-bottom: 0.5rem;
        }

        #issueImageGallery .col-6 .issue-thumbnail-container {
            min-height: 120px;
            max-height: 120px;
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
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createWorkOrderModal">
                                    <i class="ph-plus-circle me-1"></i> Raise Work Order
                                </button>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#createSiteVisitModal">
                                    <i class="ph-map-pin me-1"></i> Assign Site Visit
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#createActionModal">
                                    <i class="ph-activity me-1"></i> Add Action
                                </button>
                                <button type="button" class="btn btn-dark btn-sm" onclick="printIssueDetails()">
                                    <i class="ph-printer me-1"></i> Print
                                </button>
                                <a href="{{ route('block-issues.edit', $blockIssue) }}" class="btn btn-primary btn-sm">
                                    <i class="ph-pencil me-1"></i> Edit
                                </a>
                                @php
                                    $hasWorkOrders = $hasWorkOrders ?? $blockIssue->hasWorkOrders();
                                    $hasSiteVisits = $blockIssue->hasSiteVisits();
                                    $hasActions = $blockIssue->hasActions();
                                    $hasRelatedEntities = $hasWorkOrders || $hasSiteVisits || $hasActions;
                                    
                                    $workOrdersCount = $workOrdersCount ?? $blockIssue->workOrders()->count();
                                    $siteVisitsCount = $blockIssue->relatedSiteVisits()->count();
                                    $actionsCount = $blockIssue->actions()->count();
                                    
                                    $actionText = $hasRelatedEntities ? 'Archive' : 'Delete';
                                    $actionIcon = $hasRelatedEntities ? 'ph-archive' : 'ph-trash';
                                    $actionColor = $hasRelatedEntities ? 'warning' : 'danger';
                                @endphp
                                <button type="button" class="btn btn-{{ $actionColor }} btn-sm" onclick="showDeleteIssueModal({{ $blockIssue->id }}, {
                                    ref_no: '{{ addslashes($blockIssue->ref_no) }}',
                                    issue: '{{ addslashes($blockIssue->issue ?? 'N/A') }}',
                                    has_related_entities: {{ $hasRelatedEntities ? 'true' : 'false' }},
                                    has_work_orders: {{ $hasWorkOrders ? 'true' : 'false' }},
                                    has_site_visits: {{ $hasSiteVisits ? 'true' : 'false' }},
                                    has_actions: {{ $hasActions ? 'true' : 'false' }},
                                    work_orders_count: {{ $workOrdersCount }},
                                    site_visits_count: {{ $siteVisitsCount }},
                                    actions_count: {{ $actionsCount }}
                                })">
                                    <i class="{{ $actionIcon }} me-1"></i>{{ $actionText }}
                                </button>
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
                                                <td class="fw-medium">{{ __('translation.issue-category') }}:</td>
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
                            @if($blockIssue->contact_name || $blockIssue->salutation || $blockIssue->contact_mobile || $blockIssue->phone_number || $blockIssue->contact_email || $blockIssue->contactMethod || $blockIssue->contact_details || ($blockIssue->blockUnit && ($blockIssue->blockUnit->owners_name || $blockIssue->blockUnit->email || $blockIssue->blockUnit->mobile_no || $blockIssue->blockUnit->phone_number || $blockIssue->blockUnit->letting_agent)))
                            <div class="col-12">
                                <div class="card border shadow-sm mb-3">
                                    <div class="card-header bg-light border-bottom d-flex align-items-center">
                                        <i class="ph-address-book fs-5 me-2 text-primary"></i>
                                        <h5 class="mb-0 text-dark">Contact Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <!-- Issue Contact Details Section -->
                                            @if($blockIssue->contact_name || $blockIssue->salutation || $blockIssue->contact_mobile || $blockIssue->phone_number || $blockIssue->contact_email || $blockIssue->contactMethod || $blockIssue->contact_details)
                                            <div class="col-12">
                                                <h6 class="mb-3 text-muted border-bottom pb-2">
                                                    <i class="ph-user-circle me-2"></i>Issue Contact Details
                                                </h6>
                                            </div>
                                            
                                            @if($blockIssue->salutation)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-primary-subtle text-primary rounded">
                                                                <i class="ph-user"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Salutation</p>
                                                        <h6 class="mb-0">{{ $blockIssue->salutation }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->contact_name)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-primary-subtle text-primary rounded">
                                                                <i class="ph-user"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Contact Name</p>
                                                        <h6 class="mb-0">{{ $blockIssue->contact_name }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->contact_mobile)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-success-subtle text-success rounded">
                                                                <i class="ph-device-mobile"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Mobile</p>
                                                        <h6 class="mb-0"><a href="tel:{{ $blockIssue->contact_mobile }}" class="text-dark">{{ $blockIssue->contact_mobile }}</a></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->phone_number)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-info-subtle text-info rounded">
                                                                <i class="ph-phone"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Phone Number</p>
                                                        <h6 class="mb-0"><a href="tel:{{ $blockIssue->phone_number }}" class="text-dark">{{ $blockIssue->phone_number }}</a></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->contact_email)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-warning-subtle text-warning rounded">
                                                                <i class="ph-envelope"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Email</p>
                                                        <h6 class="mb-0"><a href="mailto:{{ $blockIssue->contact_email }}" class="text-dark text-truncate d-block">{{ $blockIssue->contact_email }}</a></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->contactMethod)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-info-subtle text-info rounded">
                                                                <i class="ph-phone-call"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Preferred Method</p>
                                                        <h6 class="mb-0">{{ $blockIssue->contactMethod->name }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->contact_details)
                                            <div class="col-md-12">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-secondary-subtle text-secondary rounded">
                                                                <i class="ph-note"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Additional Details</p>
                                                        <h6 class="mb-0">{{ $blockIssue->contact_details }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @endif

                                            <!-- Unit Contact Details Section -->
                                            @if($blockIssue->blockUnit && ($blockIssue->blockUnit->owners_name || $blockIssue->blockUnit->email || $blockIssue->blockUnit->mobile_no || $blockIssue->blockUnit->phone_number || $blockIssue->blockUnit->letting_agent))
                                            <div class="col-12 mt-4">
                                                <h6 class="mb-3 text-muted border-bottom pb-2">
                                                    <i class="ph-house me-2"></i>Unit Contact Details
                                                </h6>
                                            </div>

                                            @if($blockIssue->blockUnit->owners_name)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-primary-subtle text-primary rounded">
                                                                <i class="ph-user"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Owner's Name</p>
                                                        <h6 class="mb-0">{{ $blockIssue->blockUnit->owners_name }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->blockUnit->email)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-warning-subtle text-warning rounded">
                                                                <i class="ph-envelope"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Unit Email</p>
                                                        <h6 class="mb-0"><a href="mailto:{{ $blockIssue->blockUnit->email }}" class="text-dark text-truncate d-block">{{ $blockIssue->blockUnit->email }}</a></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->blockUnit->mobile_no)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-success-subtle text-success rounded">
                                                                <i class="ph-device-mobile"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Unit Mobile</p>
                                                        <h6 class="mb-0"><a href="tel:{{ $blockIssue->blockUnit->mobile_no }}" class="text-dark">{{ $blockIssue->blockUnit->mobile_no }}</a></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->blockUnit->phone_number)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-info-subtle text-info rounded">
                                                                <i class="ph-phone"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Unit Phone</p>
                                                        <h6 class="mb-0"><a href="tel:{{ $blockIssue->blockUnit->phone_number }}" class="text-dark">{{ $blockIssue->blockUnit->phone_number }}</a></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->blockUnit->letting_agent)
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-secondary-subtle text-secondary rounded">
                                                                <i class="ph-buildings"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Letting Agent</p>
                                                        <h6 class="mb-0">{{ $blockIssue->blockUnit->letting_agent }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Schedule Information -->
                            @if($blockIssue->preferred_start_date_time || $blockIssue->preferred_end_date_time)
                            <div class="col-12">
                                <div class="card border shadow-sm mb-3">
                                    <div class="card-header bg-light border-bottom d-flex align-items-center">
                                        <i class="ph-calendar-check fs-5 me-2 text-info"></i>
                                        <h5 class="mb-0 text-dark">Schedule Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            @if($blockIssue->preferred_start_date_time)
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-success-subtle text-success rounded">
                                                                <i class="ph-calendar-plus"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Preferred Start Date & Time</p>
                                                        <h6 class="mb-0">{{ $blockIssue->preferred_start_date_time->format('M d, Y') }}</h6>
                                                        <p class="text-muted mb-0 small">{{ $blockIssue->preferred_start_date_time->format('h:i A') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->preferred_end_date_time)
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-danger-subtle text-danger rounded">
                                                                <i class="ph-calendar-x"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Preferred End Date & Time</p>
                                                        <h6 class="mb-0">{{ $blockIssue->preferred_end_date_time->format('M d, Y') }}</h6>
                                                        <p class="text-muted mb-0 small">{{ $blockIssue->preferred_end_date_time->format('h:i A') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Additional Notes -->
                            @if($blockIssue->note_for_access || $blockIssue->comment)
                            <div class="col-12">
                                <div class="card border shadow-sm mb-3">
                                    <div class="card-header bg-light border-bottom d-flex align-items-center">
                                        <i class="ph-note-pencil fs-5 me-2 text-warning"></i>
                                        <h5 class="mb-0 text-dark">Additional Notes</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            @if($blockIssue->note_for_access)
                                            <div class="col-md-{{ $blockIssue->comment ? '6' : '12' }}">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-warning-subtle text-warning rounded">
                                                                <i class="ph-lock-key"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Note for Access</p>
                                                        <p class="mb-0">{{ $blockIssue->note_for_access }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($blockIssue->comment)
                                            <div class="col-md-{{ $blockIssue->note_for_access ? '6' : '12' }}">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title bg-info-subtle text-info rounded">
                                                                <i class="ph-chat-circle-text"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="text-muted mb-1 small">Comment</p>
                                                        <p class="mb-0">{{ $blockIssue->comment }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            </div> <!-- /.row (inner) -->
                            </div> <!-- /.col-lg-8 -->

                            <!-- Right Column - Photos -->
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
                                                        <div class="position-relative issue-thumbnail-container">
                                                            <img src="{{ $image->image_url }}" 
                                                                 class="img-fluid issue-thumbnail" 
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
                                            <th>Contractor Company</th>
                                            <th>Priority</th>
                                            <th>Start Date & Time</th>
                                            <th>End Date & Time</th>
                                            <th>Deadline Date</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($workOrders as $workOrder)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('block-work-orders.show', $workOrder->id) }}" class="text-decoration-none">
                                                        <span class="badge bg-primary">{{ $workOrder->ref_no }}</span>
                                                    </a>
                                                </td>
                                                <td>
                                                    @php
                                                        $contractorName = $workOrder->getContractorCompanyDisplayName();
                                                    @endphp
                                                    @if($contractorName)
                                                        <strong>{{ $contractorName }}</strong>
                                                    @else
                                                        <span class="text-muted">Not assigned</span>
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
                                                    @if($workOrder->preferred_start_date_time)
                                                        {{ $workOrder->preferred_start_date_time->format('M d, Y H:i') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($workOrder->preferred_end_date_time)
                                                        {{ $workOrder->preferred_end_date_time->format('M d, Y H:i') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($workOrder->deadline_date)
                                                        {{ $workOrder->deadline_date->format('M d, Y') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $workOrder->status_color ?? 'secondary' }}">{{ $workOrder->status_text ?? 'Unknown' }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title rounded-circle bg-primary">
                                                                {{ substr($workOrder->creator->name ?? 'N/A', 0, 1) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $workOrder->creator->name ?? 'N/A' }}</div>
                                                            <small class="text-muted">{{ $workOrder->creator->email ?? '' }}</small>
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
                                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-work-order-btn"
                                                            data-work-order-id="{{ $workOrder->id }}"
                                                            title="Edit Work Order">
                                                            <i class="ph-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-work-order-btn"
                                                            data-work-order-id="{{ $workOrder->id }}"
                                                            data-work-order-ref="{{ $workOrder->ref_no }}"
                                                            title="Delete Work Order">
                                                            <i class="ph-trash"></i>
                                                        </button>
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
                                            <th>Reference</th>
                                            <th>Visit Date</th>
                                            <th>User</th>
                                            <th>Status</th>
                                            <th>Notes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($relatedSiteVisits as $siteVisit)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('block-visits.show', $siteVisit->id) }}" class="text-primary fw-bold" style="text-decoration: none;">
                                                        {{ $siteVisit->ref_no ?? 'N/A' }}
                                                    </a>
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
                                                        {{ $siteVisit->team->first()->user->name ?? 'N/A' }}
                                                    @else
                                                        {{ $siteVisit->createdByUser->name ?? 'N/A' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($siteVisit->end_date_time)
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif($siteVisit->start_date_time)
                                                        <span class="badge bg-warning">In Progress</span>
                                                    @else
                                                        <span class="badge bg-info">Scheduled</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ \Illuminate\Support\Str::limit($siteVisit->notes ?? 'N/A', 50) }}
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('block-visits.show', $siteVisit->id) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="View Site Visit">
                                                            <i class="ph-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-site-visit-btn"
                                                            data-site-visit-id="{{ $siteVisit->id }}"
                                                            title="Edit Site Visit">
                                                            <i class="ph-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-site-visit-btn"
                                                            data-site-visit-id="{{ $siteVisit->id }}"
                                                            data-site-visit-ref="{{ $siteVisit->ref_no }}"
                                                            title="Delete Site Visit">
                                                            <i class="ph-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach ($siteVisits as $siteVisit)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('block-visits.show', $siteVisit->id) }}" class="text-primary fw-bold" style="text-decoration: none;">
                                                        {{ $siteVisit->ref_no ?? 'N/A' }}
                                                    </a>
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
                                                        {{ $siteVisit->team->first()->user->name ?? 'N/A' }}
                                                    @else
                                                        {{ $siteVisit->createdByUser->name ?? 'N/A' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($siteVisit->end_date_time)
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif($siteVisit->start_date_time)
                                                        <span class="badge bg-warning">In Progress</span>
                                                    @else
                                                        <span class="badge bg-info">Scheduled</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ \Illuminate\Support\Str::limit($siteVisit->notes ?? 'N/A', 50) }}
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('block-visits.show', $siteVisit->id) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="View Site Visit">
                                                            <i class="ph-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-site-visit-btn"
                                                            data-site-visit-id="{{ $siteVisit->id }}"
                                                            title="Edit Site Visit">
                                                            <i class="ph-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-site-visit-btn"
                                                            data-site-visit-id="{{ $siteVisit->id }}"
                                                            data-site-visit-ref="{{ $siteVisit->ref_no }}"
                                                            title="Delete Site Visit">
                                                            <i class="ph-trash"></i>
                                                        </button>
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
                                            <th>Actions</th>
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
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-action-btn"
                                                        data-action-id="{{ $action->id }}"
                                                        data-action-type="{{ $action->action_type }}"
                                                        title="Delete Action">
                                                        <i class="ph-trash"></i>
                                                    </button>
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
                
                // Ensure element is clean
                const dropzoneElement = document.getElementById('issuePhotoDropzone');
                if (dropzoneElement && dropzoneElement.dropzone) {
                    dropzoneElement.dropzone.destroy();
                }
                
                issuePhotoDropzone = new Dropzone("#issuePhotoDropzone", {
                    url: '{{ route("block-issues.upload-photos", $blockIssue->id) }}',
                    paramName: "images",
                    uploadMultiple: true,
                    parallelUploads: 10,
                    maxFiles: 10,
                    maxFilesize: 2, // 2MB per file
                    acceptedFiles: "image/*",
                    addRemoveLinks: true,
                    clickable: true,
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
                            
                            // Fix remove button tooltip to show file name instead of "object object"
                            const removeLink = $(preview).find('.dz-remove');
                            if (removeLink.length) {
                                const fileName = file.name || 'Remove file';
                                removeLink.attr('title', `Remove ${fileName}`);
                                removeLink.attr('aria-label', `Remove ${fileName}`);
                            }
                            
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
            
            // Clear dropzone when modal is closed
            $('#uploadPhotosModal').on('hidden.bs.modal', function() {
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

            // Site Visit Dropzone Handling
            let siteVisitDropzone = null;
            
            // Initialize Dropzone when modal is shown
            $('#createSiteVisitModal').on('shown.bs.modal', function() {
                if (!siteVisitDropzone) {
                    initializeSiteVisitDropzone();
                }
            });
            
            // Initialize Site Visit Dropzone
            function initializeSiteVisitDropzone() {
                // Disable auto discover to prevent conflicts
                Dropzone.autoDiscover = false;
                
                // Ensure element is clean
                const dropzoneElement = document.getElementById('siteVisitDropzone');
                if (dropzoneElement && dropzoneElement.dropzone) {
                    dropzoneElement.dropzone.destroy();
                }
                
                siteVisitDropzone = new Dropzone("#siteVisitDropzone", {
                    url: "#", // Placeholder, we'll handle upload manually
                    paramName: "files",
                    uploadMultiple: true,
                    parallelUploads: 10,
                    maxFiles: 10,
                    maxFilesize: 5, // 5MB per file
                    acceptedFiles: "image/*,.pdf,.doc,.docx",
                    addRemoveLinks: true,
                    clickable: true,
                    autoProcessQueue: false, // Don't auto-upload
                    dictDefaultMessage: "Drop files here or click to upload",
                    dictRemoveFile: "Remove",
                    dictCancelUpload: "Cancel",
                    dictUploadCanceled: "Upload canceled",
                    dictInvalidFileType: "You can't upload files of this type.",
                    dictFileTooBig: "File is too big. Max filesize: 5MB.",
                    dictMaxFilesExceeded: "You can not upload more than 10 files.",
                    dictResponseError: "Server responded with an error.",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    init: function() {
                        const dz = this;
                        
                        // Custom styling
                        this.on("addedfile", function(file) {
                            const preview = file.previewElement;
                            $(preview).addClass('dz-image-preview-custom');
                            
                            // Add file size info
                            const sizeInfo = $(preview).find('.dz-size');
                            if (sizeInfo.length === 0) {
                                $(preview).find('.dz-details').append('<div class="dz-size"><span data-dz-size></span></div>');
                            }
                        });
                        
                        // Handle individual file errors
                        this.on("error", function(file, errorMessage) {
                            showModalAlert('error', errorMessage);
                        });
                        
                        // Custom validation for total file size
                        this.on("addedfiles", function(files) {
                            let totalSize = 0;
                            const maxTotalSize = 50 * 1024 * 1024; // 50MB total
                            
                            files.forEach(file => {
                                totalSize += file.size;
                            });
                            
                            if (totalSize > maxTotalSize) {
                                showModalAlert('error', `Total size exceeds 50MB (${(totalSize / 1024 / 1024).toFixed(1)}MB)`);
                                files.forEach(file => {
                                    dz.removeFile(file);
                                });
                            }
                        });
                    }
                });
            }
            
            // Clear dropzone when modal is closed
            $('#createSiteVisitModal').on('hidden.bs.modal', function() {
                if (siteVisitDropzone) {
                    siteVisitDropzone.removeAllFiles(true);
                }
            });

            // Handle Assign Site Visit form submission
            $('#createSiteVisitForm').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();
                const isEdit = form.attr('action').includes('/block-visits/') && 
                              form.find('input[name="_method"]').length > 0;
                
                // Hide any existing alerts
                hideModalAlert();
                
                // Disable submit button and show loading state
                const loadingText = isEdit ? 
                    '<i class="ph-spinner-gap ph-spin me-1"></i> Updating...' : 
                    '<i class="ph-spinner-gap ph-spin me-1"></i> Creating...';
                submitBtn.prop('disabled', true).html(loadingText);
                
                // Create FormData to handle file uploads
                const formData = new FormData(form[0]);
                
                // Append files from Dropzone to FormData
                if (siteVisitDropzone && siteVisitDropzone.files.length > 0) {
                    console.log('Dropzone files count:', siteVisitDropzone.files.length);
                    
                    // Get accepted files from Dropzone (this is the proper way)
                    const acceptedFiles = siteVisitDropzone.getAcceptedFiles();
                    console.log('Accepted files count:', acceptedFiles.length);
                    
                    acceptedFiles.forEach((file, index) => {
                        console.log('Adding file:', file.name, file.size, file.type);
                        formData.append('files[]', file);
                    });
                } else {
                    console.log('No dropzone files to upload');
                }
                
                // Debug: Log FormData contents
                console.log('FormData contents:');
                for (let pair of formData.entries()) {
                    console.log(pair[0], ':', pair[1]);
                }
                
                // Submit form via AJAX
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Site visit response:', response);
                        if (response.success) {
                            const imagesCount = response.data && response.data.images ? response.data.images.length : 0;
                            console.log('Site visit created with', imagesCount, 'images');
                            showModalAlert('success', response.message);
                            
                            // Close modal after successful operation
                            setTimeout(() => {
                                $('#createSiteVisitModal').modal('hide');
                                location.reload();
                            }, 1500);
                        } else {
                            showModalAlert('error', response.message || 'An error occurred while processing the site visit.');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while processing the site visit.';
                        
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                // Handle validation errors
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join('<br>');
                            }
                        }
                        
                        showModalAlert('error', errorMessage);
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reset form when modal is hidden
            $('#createSiteVisitModal').on('hidden.bs.modal', function() {
                $('#createSiteVisitForm')[0].reset();
                resetModalToCreateMode();
            });

            // Hide alert when modal is shown (for create mode)
            $('#createSiteVisitModal').on('show.bs.modal', function() {
                hideModalAlert();
            });

            // Hide work order alert when modal is shown
            $('#createWorkOrderModal').on('show.bs.modal', function() {
                hideWorkOrderAlert();
                // Note: Don't reset work order type here - it's handled in resetWorkOrderModalToCreateMode()
                // and would override the correct type when editing
            });
            
            // Work Order Dropzone Handling
            let workOrderDropzone = null;
            
            // Initialize Dropzone when modal is shown
            $('#createWorkOrderModal').on('shown.bs.modal', function() {
                if (!workOrderDropzone) {
                    initializeWorkOrderDropzone();
                }
            });
            
            // Initialize Work Order Dropzone
            function initializeWorkOrderDropzone() {
                // Disable auto discover to prevent conflicts
                Dropzone.autoDiscover = false;
                
                // Ensure element is clean
                const dropzoneElement = document.getElementById('workOrderDropzone');
                if (dropzoneElement && dropzoneElement.dropzone) {
                    dropzoneElement.dropzone.destroy();
                }
                
                workOrderDropzone = new Dropzone("#workOrderDropzone", {
                    url: "#", // Placeholder, we'll handle upload manually
                    paramName: "images",
                    uploadMultiple: true,
                    parallelUploads: 10,
                    maxFiles: 10,
                    maxFilesize: 2, // 2MB per file
                    acceptedFiles: "image/jpeg,image/png,image/jpg,image/gif",
                    addRemoveLinks: true,
                    clickable: true,
                    autoProcessQueue: false, // Don't auto-upload
                    dictDefaultMessage: "Drop files here or click to upload",
                    dictRemoveFile: "Remove",
                    dictCancelUpload: "Cancel",
                    dictUploadCanceled: "Upload canceled",
                    dictInvalidFileType: "You can't upload files of this type.",
                    dictFileTooBig: "File is too big. Max filesize: 2MB.",
                    dictMaxFilesExceeded: "You can not upload more than 10 files.",
                    dictResponseError: "Server responded with an error.",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    init: function() {
                        const dz = this;
                        
                        // Custom styling
                        this.on("addedfile", function(file) {
                            const preview = file.previewElement;
                            $(preview).addClass('dz-image-preview-custom');
                        });
                        
                        // Handle individual file errors
                        this.on("error", function(file, errorMessage) {
                            showWorkOrderAlert('error', errorMessage);
                        });
                    }
                });
            }
            
            // Clear dropzone when modal is closed
            $('#createWorkOrderModal').on('hidden.bs.modal', function() {
                // Remove PUT method field if exists (for next open)
                $('#createWorkOrderForm').find('input[name="_method"]').remove();
                // Clear dropzone files
                if (workOrderDropzone) {
                    workOrderDropzone.removeAllFiles(true);
                }
            });
            
            // Handle "Raise Work Order" button click (for creating new work orders)
            $('button[data-bs-target="#createWorkOrderModal"]').on('click', function() {
                // Reset to create mode (default to Outsource)
                resetWorkOrderModalToCreateMode();
            });
            
            // Handle Work Order Type toggle
            $('#workOrderType').on('change', function() {
                const selectedType = $(this).val();
                
                if (selectedType === 'inhouse') {
                    // Show Property Manager, hide Contract Company
                    $('#propertyManagerFieldContainer').show();
                    $('#contractorFieldContainer').hide();
                    
                    // Enable Property Manager field and disable Contract Company field
                    $('#propertyManagerField').prop('required', true).prop('disabled', false);
                    $('#contractorField').prop('required', false).prop('disabled', true).val('');
                } else {
                    // Show Contract Company, hide Property Manager (Outsource)
                    $('#contractorFieldContainer').show();
                    $('#propertyManagerFieldContainer').hide();
                    
                    // Enable Contract Company field and disable Property Manager field
                    $('#contractorField').prop('required', true).prop('disabled', false);
                    $('#propertyManagerField').prop('required', false).prop('disabled', true).val('');
                }
            });

            // Hide action alert when modal is shown
            $('#createActionModal').on('show.bs.modal', function() {
                hideActionAlert();
            });

            // Handle Create/Edit Work Order form submission
            $('#createWorkOrderForm').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();
                const isEdit = form.attr('action').includes('/block-work-orders/') && 
                              form.find('input[name="_method"]').length > 0;
                
                // Hide any existing alerts
                hideWorkOrderAlert();
                
                // Disable submit button and show loading state
                const loadingText = isEdit ? 
                    '<i class="ph-spinner-gap ph-spin me-1"></i> Updating...' : 
                    '<i class="ph-spinner-gap ph-spin me-1"></i> Creating...';
                submitBtn.prop('disabled', true).html(loadingText);
                
                // Create FormData from form
                const formData = new FormData(form[0]);
                
                // Append files from Dropzone to FormData
                if (workOrderDropzone && workOrderDropzone.files.length > 0) {
                    const acceptedFiles = workOrderDropzone.getAcceptedFiles();
                    acceptedFiles.forEach(function(file, index) {
                        formData.append('images[]', file);
                    });
                }
                
                // Submit form via AJAX
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showWorkOrderAlert('success', response.message);
                            
                            // Close modal after successful operation
                            setTimeout(() => {
                                $('#createWorkOrderModal').modal('hide');
                                location.reload();
                            }, 1500);
                        } else {
                            showWorkOrderAlert('error', response.message || 'An error occurred while processing the work order.');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while processing the work order.';
                        
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                // Handle validation errors
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join('<br>');
                            }
                        }
                        
                        showWorkOrderAlert('error', errorMessage);
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reset work order form when modal is hidden
            $('#createWorkOrderModal').on('hidden.bs.modal', function() {
                $('#createWorkOrderForm')[0].reset();
                resetWorkOrderModalToCreateMode();
            });

            // Handle Add Action form submission
            $('#createActionForm').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();
                
                // Hide any existing alerts
                hideActionAlert();
                
                // Disable submit button and show loading state
                submitBtn.prop('disabled', true).html('<i class="ph-spinner-gap ph-spin me-1"></i> Creating...');
                
                // Submit form via AJAX
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showActionAlert('success', response.message);
                            
                            // Close modal after successful creation
                            setTimeout(() => {
                                $('#createActionModal').modal('hide');
                                location.reload();
                            }, 1500);
                        } else {
                            showActionAlert('error', response.message || 'An error occurred while creating the action.');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while creating the action.';
                        
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                // Handle validation errors
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join('<br>');
                            }
                        }
                        
                        showActionAlert('error', errorMessage);
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reset action form when modal is hidden
            $('#createActionModal').on('hidden.bs.modal', function() {
                $('#createActionForm')[0].reset();
                hideActionAlert();
            });

            // Hide action alert when clicked
            $(document).on('click', '#actionAlert', function() {
                hideActionAlert();
            });

            // Delete functionality variables
            let deleteType = '';
            let deleteId = '';
            let deleteRef = '';

            // Handle delete button clicks
            $('.delete-site-visit-btn').on('click', function() {
                deleteType = 'site-visit';
                deleteId = $(this).data('site-visit-id');
                deleteRef = $(this).data('site-visit-ref');
                showDeleteConfirmation(`Site Visit ${deleteRef}`);
            });

            $('.delete-work-order-btn').on('click', function() {
                deleteType = 'work-order';
                deleteId = $(this).data('work-order-id');
                deleteRef = $(this).data('work-order-ref');
                showDeleteConfirmation(`Work Order ${deleteRef}`);
            });

            $('.delete-action-btn').on('click', function() {
                deleteType = 'action';
                deleteId = $(this).data('action-id');
                const actionType = $(this).data('action-type');
                showDeleteConfirmation(`${actionType} Action`);
            });

            // Show delete confirmation modal
            function showDeleteConfirmation(itemName) {
                $('#deleteConfirmationMessage').text(`Are you sure you want to delete ${itemName}?`);
                $('#deleteConfirmationModal').modal('show');
            }

            // Handle delete confirmation
            $('#confirmDeleteBtn').on('click', function() {
                const btn = $(this);
                const originalText = btn.html();
                
                // Show loading state
                btn.prop('disabled', true).html('<i class="ph-spinner-gap ph-spin me-1"></i> Deleting...');
                
                // Determine delete URL based on type
                let deleteUrl = '';
                if (deleteType === 'site-visit') {
                    deleteUrl = `/block-visits/${deleteId}`;
                } else if (deleteType === 'work-order') {
                    deleteUrl = `/block-work-orders/${deleteId}`;
                } else if (deleteType === 'action') {
                    deleteUrl = `/block-issue-actions/${deleteId}`;
                }
                
                // Perform delete via AJAX
                $.ajax({
                    url: deleteUrl,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#deleteConfirmationModal').modal('hide');
                        showAlert('success', response.message || 'Item deleted successfully!');
                        
                        // Reload page after successful deletion
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to delete item.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        $('#deleteConfirmationModal').modal('hide');
                        showAlert('error', errorMessage);
                    },
                    complete: function() {
                        // Re-enable button
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Hide work order alert when clicked
            $(document).on('click', '#workOrderAlert', function() {
                hideWorkOrderAlert();
            });

            // Handle Edit Work Order button clicks
            $('.edit-work-order-btn').on('click', function() {
                const workOrderId = $(this).data('work-order-id');
                loadWorkOrderForEdit(workOrderId);
            });

            // Function to reset work order modal to create mode
            function resetWorkOrderModalToCreateMode() {
                $('#workOrderModalTitle').text('Raise Work Order for Issue: {{ $blockIssue->ref_no }}');
                $('#workOrderSubmitBtnText').text('Raise Work Order');
                $('#createWorkOrderForm').attr('action', '{{ route("block-work-orders.store") }}');
                $('#createWorkOrderForm').find('input[name="_method"]').remove();
                $('#createWorkOrderForm input[name="block_issue_id"]').val('{{ $blockIssue->id }}');
                // Reset work order type to default (Outsource)
                $('#workOrderType').val('outsource').trigger('change');
                // Clear form values
                $('#contractorField').val('');
                $('#propertyManagerField').val('');
                // Clear dropzone files
                if (workOrderDropzone) {
                    workOrderDropzone.removeAllFiles(true);
                }
                hideWorkOrderAlert();
            }

            // Function to load work order data for editing
            function loadWorkOrderForEdit(workOrderId) {
                // Show loading state
                const originalTitle = $('#workOrderModalTitle').text();
                $('#workOrderModalTitle').text('Loading...');
                
                // Fetch work order data
                $.ajax({
                    url: `/block-work-orders/${workOrderId}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            const workOrder = response.data;
                            
                            // Update modal title and form action
                            $('#workOrderModalTitle').text(`Edit Work Order #${workOrder.id}`);
                            $('#workOrderSubmitBtnText').text('Update Work Order');
                            $('#createWorkOrderForm').attr('action', `/block-work-orders/${workOrderId}`);
                            
                            // Add PUT method for update
                            if ($('#createWorkOrderForm').find('input[name="_method"]').length === 0) {
                                $('#createWorkOrderForm').append('<input type="hidden" name="_method" value="PUT">');
                            }
                            
                            // Set Work Order Type based on whether assigned user is property manager
                            if (workOrder.is_property_manager === true || workOrder.is_property_manager === 1) {
                                // Set to In House and trigger change to show property manager field
                                $('#workOrderType').val('inhouse').trigger('change');
                                // Populate property manager field after change event
                                setTimeout(() => {
                                    $('#propertyManagerField').val(workOrder.contractor_id || '');
                                }, 100);
                            } else {
                                // Set to Outsource and trigger change to show contract company field
                                $('#workOrderType').val('outsource').trigger('change');
                                // Populate contract company field after change event
                                setTimeout(() => {
                                    // Use contract_company_id if available, otherwise fall back to contractor_id for legacy data
                                    const companyId = workOrder.contract_company_id || workOrder.contractor_id || '';
                                    $('#contractorField').val(companyId);
                                }, 100);
                            }
                            
                            // Populate other form fields
                            $('#createWorkOrderForm select[name="priority_id"]').val(workOrder.priority_id || '');
                            
                            // Format datetime for inputs
                            if (workOrder.preferred_start_date_time) {
                                const startDateTime = new Date(workOrder.preferred_start_date_time);
                                const formattedStartDateTime = startDateTime.toISOString().slice(0, 16);
                                $('#createWorkOrderForm input[name="preferred_start_date_time"]').val(formattedStartDateTime);
                            }
                            
                            if (workOrder.preferred_end_date_time) {
                                const endDateTime = new Date(workOrder.preferred_end_date_time);
                                const formattedEndDateTime = endDateTime.toISOString().slice(0, 16);
                                $('#createWorkOrderForm input[name="preferred_end_date_time"]').val(formattedEndDateTime);
                            }
                            
                            if (workOrder.deadline_date) {
                                const deadlineDate = new Date(workOrder.deadline_date);
                                const formattedDeadlineDate = deadlineDate.toISOString().slice(0, 10);
                                $('#createWorkOrderForm input[name="deadline_date"]').val(formattedDeadlineDate);
                            }
                            
                            $('#createWorkOrderForm textarea[name="comment"]').val(workOrder.comment || '');
                            
                            // Clear dropzone files when editing (existing images are shown separately)
                            if (workOrderDropzone) {
                                workOrderDropzone.removeAllFiles(true);
                            }
                            
                            // Show modal
                            $('#createWorkOrderModal').modal('show');
                        } else {
                            showWorkOrderAlert('error', 'Failed to load work order data.');
                        }
                    },
                    error: function(xhr) {
                        $('#workOrderModalTitle').text(originalTitle);
                        showWorkOrderAlert('error', 'Failed to load work order data.');
                    }
                });
            }

            // Handle Edit Site Visit button clicks
            $('.edit-site-visit-btn').on('click', function() {
                const siteVisitId = $(this).data('site-visit-id');
                hideModalAlert(); // Hide any existing alerts
                loadSiteVisitForEdit(siteVisitId);
            });

            // Hide modal alert when clicked
            $(document).on('click', '#siteVisitAlert', function() {
                hideModalAlert();
            });

            // Function to show alert within the modal
            function showModalAlert(type, message) {
                const alertContainer = $('#siteVisitAlertContainer');
                const alert = $('#siteVisitAlert');
                const alertMessage = $('#siteVisitAlertMessage');
                
                // Remove existing alert classes
                alert.removeClass('alert-success alert-danger alert-warning alert-info');
                
                // Add appropriate class based on type
                if (type === 'success') {
                    alert.addClass('alert-success');
                } else if (type === 'error') {
                    alert.addClass('alert-danger');
                } else if (type === 'warning') {
                    alert.addClass('alert-warning');
                } else {
                    alert.addClass('alert-info');
                }
                
                // Set message and show
                alertMessage.html(message);
                alertContainer.show();
                
                // Auto hide after 5 seconds for success messages
                if (type === 'success') {
                    setTimeout(() => {
                        alertContainer.hide();
                    }, 5000);
                }
            }

            // Function to hide modal alert
            function hideModalAlert() {
                $('#siteVisitAlertContainer').hide();
            }

            // Function to show work order alert within the modal
            function showWorkOrderAlert(type, message) {
                const alertContainer = $('#workOrderAlertContainer');
                const alert = $('#workOrderAlert');
                const alertMessage = $('#workOrderAlertMessage');
                
                // Remove existing alert classes
                alert.removeClass('alert-success alert-danger alert-warning alert-info');
                
                // Add appropriate class based on type
                if (type === 'success') {
                    alert.addClass('alert-success');
                } else if (type === 'error') {
                    alert.addClass('alert-danger');
                } else if (type === 'warning') {
                    alert.addClass('alert-warning');
                } else {
                    alert.addClass('alert-info');
                }
                
                // Set message and show
                alertMessage.html(message);
                alertContainer.show();
                
                // Auto hide after 5 seconds for success messages
                if (type === 'success') {
                    setTimeout(() => {
                        alertContainer.hide();
                    }, 5000);
                }
            }

            // Function to hide work order alert
            function hideWorkOrderAlert() {
                $('#workOrderAlertContainer').hide();
            }

            // Function to show action alert within the modal
            function showActionAlert(type, message) {
                const alertContainer = $('#actionAlertContainer');
                const alert = $('#actionAlert');
                const alertMessage = $('#actionAlertMessage');
                
                // Remove existing alert classes
                alert.removeClass('alert-success alert-danger alert-warning alert-info');
                
                // Add appropriate class based on type
                if (type === 'success') {
                    alert.addClass('alert-success');
                } else if (type === 'error') {
                    alert.addClass('alert-danger');
                } else if (type === 'warning') {
                    alert.addClass('alert-warning');
                } else {
                    alert.addClass('alert-info');
                }
                
                // Set message and show
                alertMessage.html(message);
                alertContainer.show();
                
                // Auto hide after 5 seconds for success messages
                if (type === 'success') {
                    setTimeout(() => {
                        alertContainer.hide();
                    }, 5000);
                }
            }

            // Function to hide action alert
            function hideActionAlert() {
                $('#actionAlertContainer').hide();
            }

            // Function to reset modal to create mode
            function resetModalToCreateMode() {
                $('#modalTitle').text('Assign Site Visit for Issue: {{ $blockIssue->ref_no }}');
                $('#submitBtnText').text('Assign Site Visit');
                $('#createSiteVisitForm').attr('action', '{{ route("block-visits.store") }}');
                $('#createSiteVisitForm').find('input[name="_method"]').remove();
                $('#createSiteVisitForm input[name="block_id"]').val('{{ $blockIssue->block->id }}');
                $('#createSiteVisitForm input[name="block_issue_id"]').val('{{ $blockIssue->id }}');
                // Clear dropzone files
                if (siteVisitDropzone) {
                    siteVisitDropzone.removeAllFiles(true);
                }
                hideModalAlert();
            }

            // Function to load site visit data for editing
            function loadSiteVisitForEdit(siteVisitId) {
                // Show loading state
                const originalTitle = $('#modalTitle').text();
                $('#modalTitle').text('Loading...');
                
                // Fetch site visit data
                $.ajax({
                    url: `/block-visits/${siteVisitId}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            const siteVisit = response.data;
                            
                            // Update modal title and form action
                            $('#modalTitle').text(`Edit Site Visit #${siteVisit.id}`);
                            $('#submitBtnText').text('Update Site Visit');
                            $('#createSiteVisitForm').attr('action', `/block-visits/${siteVisitId}`);
                            
                            // Add PUT method for update
                            if ($('#createSiteVisitForm').find('input[name="_method"]').length === 0) {
                                $('#createSiteVisitForm').append('<input type="hidden" name="_method" value="PUT">');
                            }
                            
                            // Populate form fields
                            $('#createSiteVisitForm select[name="user_id"]').val(siteVisit.team && siteVisit.team[0] ? siteVisit.team[0].user_id : '');
                            
                            // Format datetime for input
                            if (siteVisit.scheduled_date_time) {
                                const dateTime = new Date(siteVisit.scheduled_date_time);
                                const formattedDateTime = dateTime.toISOString().slice(0, 16);
                                $('#createSiteVisitForm input[name="scheduled_date_time"]').val(formattedDateTime);
                            }
                            
                            $('#createSiteVisitForm textarea[name="notes"]').val(siteVisit.notes || '');
                            
                            // Show modal
                            $('#createSiteVisitModal').modal('show');
                        } else {
                            showModalAlert('error', 'Failed to load site visit data.');
                        }
                    },
                    error: function(xhr) {
                        $('#modalTitle').text(originalTitle);
                        showModalAlert('error', 'Failed to load site visit data.');
                    }
                });
            }
        });
        
        // Print Issue Details Function
        function printIssueDetails() {
            // Add print class to body
            document.body.classList.add('printing');
            
            // Set print date
            const now = new Date();
            const printDate = now.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            $('.page-content').attr('data-print-date', printDate);
            
            // Set issue reference number for header
            const refNo = '{{ $blockIssue->ref_no ?? "" }}';
            $('.card:first .card-header').attr('data-ref-no', refNo);
            
            // Hide empty sections before printing
            hideEmptySections();
            
            // Trigger print dialog
            window.print();
            
            // Remove print class and restore sections after printing
            setTimeout(function() {
                document.body.classList.remove('printing');
                // Remove the temporary hide class
                $('.print-hide-empty').removeClass('print-hide-empty');
            }, 100);
        }
        
        // Function to hide empty sections
        function hideEmptySections() {
            // Check each card for empty content
            $('.card').each(function() {
                const $card = $(this);
                const $tbody = $card.find('table tbody');
                
                // Check if this card has a table
                if ($tbody.length > 0) {
                    // Check if tbody is empty or only has a "no records" row
                    const rowCount = $tbody.find('tr').length;
                    const hasColspanRow = $tbody.find('tr td[colspan]').length > 0;
                    const hasNoRecordsText = $tbody.text().toLowerCase().includes('no') && 
                                            ($tbody.text().toLowerCase().includes('found') || 
                                             $tbody.text().toLowerCase().includes('available') ||
                                             $tbody.text().toLowerCase().includes('records'));
                    
                    // If empty or only has "no records" message, hide it for print
                    if (rowCount === 0 || (rowCount === 1 && (hasColspanRow || hasNoRecordsText))) {
                        $card.addClass('print-hide-empty');
                    }
                }
                
                // Also check for alert messages indicating no data
                const hasNoDataAlert = $card.find('.alert-info, .alert-warning').filter(function() {
                    const text = $(this).text().toLowerCase();
                    return text.includes('no ') && (text.includes('actions') || text.includes('work orders') || text.includes('visits'));
                }).length > 0;
                
                if (hasNoDataAlert) {
                    $card.addClass('print-hide-empty');
                }
            });
        }
    </script>
    
    <style>
        /* Print Styles */
        @media print {
            /* Hide only specific action buttons, not all buttons */
            .page-title-box,
            .breadcrumb,
            .card-header .btn,
            .card-header button,
            .modal,
            .modal-backdrop,
            .sidebar,
            .navbar,
            .footer,
            .app-menu,
            .navbar-menu,
            .topbar,
            #page-topbar,
            .vertical-overlay,
            .dropzone,
            #uploadPhotosModal,
            form[onsubmit*="confirm"],
            a.btn-outline-danger[title*="Delete"],
            .ph-pencil,
            .ph-trash {
                display: none !important;
            }
            
            /* Adjust page layout for printing */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            html, body {
                width: 210mm;
                height: auto;
                margin: 0 !important;
                padding: 0 !important;
                background: white;
                font-size: 11pt;
            }
            
            .page-content {
                margin: 0 !important;
                padding: 10mm !important;
                width: 210mm !important;
                max-width: 210mm !important;
            }
            
            .container-fluid {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .row {
                margin: 0 !important;
            }
            
            /* Card styling for print - ALLOW page breaks */
            .card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
                page-break-inside: auto !important;
                margin-bottom: 10px !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                break-inside: auto !important;
                display: block !important;
                width: 100% !important;
            }
            
            .card-header {
                background-color: #f5f5f5 !important;
                border-bottom: 1px solid #ccc !important;
                padding: 8px !important;
                display: block !important;
                page-break-after: avoid;
                margin: 0 !important;
            }
            
            .card-title {
                font-size: 13pt !important;
                font-weight: bold !important;
                color: #000 !important;
                margin: 0 !important;
            }
            
            .card-body {
                padding: 8px !important;
                display: block !important;
                page-break-inside: auto;
            }
            
            /* Simplify badge colors for print - use borders instead */
            .badge {
                background-color: white !important;
                color: #000 !important;
                border: 1px solid #333 !important;
                padding: 2px 6px !important;
                display: inline-block !important;
                font-size: 9pt !important;
            }
            
            /* Keep priority/status colors but lighter */
            .badge.bg-danger {
                border-color: #dc3545 !important;
                color: #dc3545 !important;
            }
            
            .badge.bg-warning {
                border-color: #ffc107 !important;
                color: #856404 !important;
            }
            
            .badge.bg-success {
                border-color: #28a745 !important;
                color: #28a745 !important;
            }
            
            .badge.bg-primary {
                border-color: #0d6efd !important;
                color: #0d6efd !important;
            }
            
            .badge.bg-info {
                border-color: #17a2b8 !important;
                color: #17a2b8 !important;
            }
            
            /* Ensure all text is visible */
            p, span, div, td, th, li {
                color: #000 !important;
                visibility: visible !important;
                display: inline !important;
            }
            
            div {
                display: block !important;
            }
            
            /* Ensure images print properly */
            img {
                max-width: 150px !important;
                max-height: 150px !important;
                page-break-inside: avoid;
                display: block !important;
                margin: 5px;
            }
            
            /* Hide the existing page title that's showing up */
            .row:first-child .card:first-child .card-header h4 {
                display: none !important;
            }
            
            /* Add clean header for print - ONLY on the very first card */
            .row:first-child .card:first-child .card-header::before {
                content: "Block Issue Report - " attr(data-ref-no);
                display: block;
                font-size: 16pt;
                font-weight: bold;
                color: #000;
                margin-bottom: 8px;
                padding-bottom: 5px;
                border-bottom: 2px solid #333;
            }
            
            /* Keep other card headers as they are */
            .card-header h5,
            .card-header .card-title {
                display: block !important;
            }
            
            /* Hide sections marked as empty by JavaScript */
            .print-hide-empty {
                display: none !important;
            }
            
            /* Also hide cards with empty tables using CSS (backup method) */
            .card:has(tbody tr td[colspan]:only-child),
            .card:has(tbody:empty) {
                display: none !important;
            }
            
            /* Better table printing */
            table {
                page-break-inside: auto !important;
                width: 100% !important;
                border-collapse: collapse !important;
                display: table !important;
                margin: 10px 0;
            }
            
            table thead {
                display: table-header-group !important;
            }
            
            table tbody {
                display: table-row-group !important;
            }
            
            table tr {
                display: table-row !important;
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            table th, 
            table td {
                display: table-cell !important;
                border: 1px solid #dee2e6 !important;
                padding: 6px !important;
                font-size: 10pt !important;
                visibility: visible !important;
                color: #000 !important;
            }
            
            table th {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
            }
            
            /* Table borderless variant */
            .table-borderless td,
            .table-borderless th {
                border: none !important;
            }
            
            /* Timeline adjustments for print */
            .timeline {
                page-break-inside: auto;
                display: block !important;
            }
            
            .timeline-item {
                page-break-inside: avoid;
                display: block !important;
                margin-bottom: 10px;
            }
            
            /* Allow page breaks between sections */
            .row {
                page-break-inside: auto !important;
                display: block !important;
                width: 100%;
            }
            
            /* Adjust column layout for print */
            .col-lg-8,
            .col-lg-4,
            .col-md-6,
            .col-12,
            [class*="col-"] {
                width: 100% !important;
                max-width: 100% !important;
                float: none !important;
                display: block !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin: 0 !important;
            }
            
            /* Hide only action column header and cells, not all content */
            table th:has(.ph-gear-six),
            table td:has(.btn-outline-danger),
            table td:has(form[method="POST"]) {
                display: none !important;
            }
            
            /* Avatar icons - keep visible but simplify */
            .avatar-sm,
            .avatar-xs {
                display: inline-block !important;
                width: 25px;
                height: 25px;
            }
            
            .avatar-title {
                display: flex !important;
            }
            
            /* Strong tags should be bold and visible */
            strong {
                font-weight: bold !important;
                color: #000 !important;
            }
            
            /* Ensure labels and content are visible */
            .fw-medium,
            .form-label,
            label {
                font-weight: 600 !important;
                color: #000 !important;
                display: inline-block !important;
            }
            
            /* Photo gallery */
            .gallery-item,
            .issue-photo-item {
                display: inline-block !important;
                width: 150px;
                margin: 5px;
                page-break-inside: avoid;
            }
            
            /* Section spacing */
            h5, h4, h3 {
                page-break-after: avoid;
                margin-top: 15px;
                margin-bottom: 10px;
                color: #000 !important;
                font-weight: bold !important;
            }
        }
    </style>
    
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

    <!-- Add Action Modal -->
    <div class="modal fade" id="createActionModal" tabindex="-1" aria-labelledby="createActionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createActionModalLabel">Add Action for Issue: {{ $blockIssue->ref_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createActionForm" method="POST" action="{{ route('block-issues.store-action', $blockIssue->id) }}">
                    @csrf
                    <div class="modal-body">
                        <!-- Alert Messages Container -->
                        <div id="actionAlertContainer" style="display: none;">
                            <div id="actionAlert" class="alert" role="alert">
                                <span id="actionAlertMessage"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="action_type" class="form-label">Action Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="action_type" name="action_type" required>
                                    <option value="">Select Action Type</option>
                                    @foreach (\App\Models\BlockIssueAction::ACTION_TYPES as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Hidden field for default Pending status -->
                        <input type="hidden" name="status" value="pending">
                        
                        <div class="row">
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
                            <i class="ph-plus me-1"></i> Add Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create/Edit Site Visit Modal -->
    <div class="modal fade" id="createSiteVisitModal" tabindex="-1" aria-labelledby="createSiteVisitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createSiteVisitModalLabel">
                        <i class="ph-map-pin me-2"></i><span id="modalTitle">Assign Site Visit for Issue: {{ $blockIssue->ref_no }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createSiteVisitForm" method="POST" action="{{ route('block-visits.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Alert Messages Container -->
                        <div id="siteVisitAlertContainer" style="display: none;">
                            <div id="siteVisitAlert" class="alert" role="alert">
                                <span id="siteVisitAlertMessage"></span>
                            </div>
                        </div>
                        
                        <!-- Block and Unit Row -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Block</label>
                                <div class="form-control-plaintext bg-light p-2 rounded">
                                    <strong>{{ $blockIssue->block->name }}</strong> - {{ $blockIssue->block->management_company }}
                                    @if($blockIssue->block->blockType)
                                        ({{ $blockIssue->block->blockType->name }})
                                    @endif
                                </div>
                                <input type="hidden" name="block_id" value="{{ $blockIssue->block->id }}">
                                <input type="hidden" name="block_issue_id" value="{{ $blockIssue->id }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit</label>
                                <div class="form-control-plaintext bg-light p-2 rounded">
                                    @if($blockIssue->blockUnit)
                                        <strong>{{ $blockIssue->blockUnit->unit_code }}</strong>
                                        @if($blockIssue->blockUnit->unit_name)
                                            - {{ $blockIssue->blockUnit->unit_name }}
                                        @endif
                                        @if($blockIssue->blockUnit->unitType)
                                            ({{ $blockIssue->blockUnit->unitType->name }})
                                        @endif
                                    @else
                                        <span class="text-muted">No unit specified</span>
                                    @endif
                                </div>
                                <input type="hidden" name="block_unit_id" value="{{ $blockIssue->block_unit_id }}">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Assigned User <span class="text-danger">*</span></label>
                                <select class="form-select" name="user_id" required>
                                    <option value="">Select a user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="scheduled_date_time" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Enter any additional notes for this site visit..."></textarea>
                        </div>
                        
                        <!-- File Upload Section with Dropzone -->
                        <div class="mb-3">
                            <label class="form-label">Upload Photos or Documents</label>
                            <small class="text-muted d-block mb-2">Optional - Upload multiple files</small>
                            <div id="siteVisitDropzone" class="dropzone">
                                <div class="dz-message">
                                    <div class="mb-2">
                                        <i class="ph-cloud-upload display-4 text-muted"></i>
                                    </div>
                                    <h5>Drop files here or click to upload</h5>
                                    <p class="text-muted font-size-14 mb-0">
                                        <strong>Requirements:</strong><br>
                                        • Maximum 10 files<br>
                                        • Each file max 5MB<br>
                                        • Formats: Images, PDF, DOC, DOCX
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dropzone Custom Styles -->
                        <style>
                            #siteVisitDropzone.dropzone {
                                min-height: 120px !important;
                                border: 2px dashed #ccc !important;
                                border-radius: 6px !important;
                                background: #fafafa;
                            }
                            
                            #siteVisitDropzone .dz-message {
                                padding: 20px !important;
                                margin: 0 !important;
                            }
                            
                            #siteVisitDropzone.dz-drag-hover {
                                border-color: #0d6efd !important;
                                background: #e7f3ff !important;
                            }
                            
                            #siteVisitDropzone .dz-preview {
                                margin: 10px !important;
                            }
                            
                            #siteVisitDropzone .dz-preview .dz-image {
                                border-radius: 4px !important;
                            }
                        </style>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="ph-map-pin me-1"></i> <span id="submitBtnText">Assign Site Visit</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create/Edit Work Order Modal -->
    <div class="modal fade" id="createWorkOrderModal" tabindex="-1" aria-labelledby="createWorkOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createWorkOrderModalLabel">
                        <i class="ph-plus-circle me-2"></i><span id="workOrderModalTitle">Raise Work Order for Issue: {{ $blockIssue->ref_no }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createWorkOrderForm" method="POST" action="{{ route('block-work-orders.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Alert Messages Container -->
                        <div id="workOrderAlertContainer" style="display: none;">
                            <div id="workOrderAlert" class="alert" role="alert">
                                <span id="workOrderAlertMessage"></span>
                            </div>
                        </div>
                        
                        <!-- Only pass the issue ID - backend will populate all related data -->
                        <input type="hidden" name="block_issue_id" value="{{ $blockIssue->id }}">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Work Order Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="workOrderType" required>
                                    <option value="outsource" selected>Outsource</option>
                                    <option value="inhouse">In House</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select" name="priority_id" required>
                                    <option value="">Select Priority</option>
                                    <option value="1">Low</option>
                                    <option value="2" selected>Normal</option>
                                    <option value="3">High</option>
                                    <option value="4">Urgent</option>
                                    <option value="5">Critical</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3" id="contractorFieldContainer">
                                <label class="form-label">Contract Company <span class="text-danger">*</span></label>
                                <select class="form-select" name="contract_company_id" id="contractorField" required>
                                    <option value="">Select Contract Company</option>
                                    @if(isset($contractCompanies) && $contractCompanies->count() > 0)
                                        @foreach($contractCompanies as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    @elseif(isset($contractors) && $contractors->count() > 0)
                                        @foreach($contractors as $contractor)
                                            <option value="{{ $contractor->id }}">
                                                {{ $contractor->name }}@if($contractor->code) ({{ $contractor->code }})@endif
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No contract companies available</option>
                                    @endif
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3" id="propertyManagerFieldContainer" style="display: none;">
                                <label class="form-label">Assign Property Manager <span class="text-danger">*</span></label>
                                <select class="form-select" name="property_manager_id" id="propertyManagerField">
                                    <option value="">Select Property Manager</option>
                                    @foreach($propertyManagers as $manager)
                                        <option value="{{ $manager->id }}">
                                            {{ $manager->name }} ({{ $manager->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Deadline Date</label>
                                <input type="date" class="form-control" name="deadline_date">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Preferred Start Date & Time</label>
                                <input type="datetime-local" class="form-control" name="preferred_start_date_time">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Preferred End Date & Time</label>
                                <input type="datetime-local" class="form-control" name="preferred_end_date_time">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Comments</label>
                                <textarea class="form-control" name="comment" rows="3" placeholder="Additional comments or notes..."></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Upload Photos (Optional)</label>
                                <small class="text-muted d-block mb-2">Upload multiple images</small>
                                <div id="workOrderDropzone" class="dropzone">
                                    <div class="dz-message">
                                        <div class="mb-2">
                                            <i class="ph-cloud-upload display-4 text-muted"></i>
                                        </div>
                                        <h5>Drop files here or click to upload</h5>
                                        <p class="text-muted font-size-14 mb-0">
                                            <strong>Requirements:</strong><br>
                                            • Maximum 10 files<br>
                                            • Each file max 2MB<br>
                                            • Formats: JPEG, PNG, JPG, GIF
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Work Order Dropzone Custom Styles -->
                        <style>
                            #workOrderDropzone.dropzone {
                                min-height: 120px !important;
                                border: 2px dashed #ccc !important;
                                border-radius: 6px !important;
                                background: #fafafa;
                            }
                            
                            #workOrderDropzone .dz-message {
                                padding: 20px !important;
                                margin: 0 !important;
                            }
                            
                            #workOrderDropzone.dz-drag-hover {
                                border-color: #198754 !important;
                                background: #d1e7dd !important;
                            }
                            
                            #workOrderDropzone .dz-preview {
                                margin: 10px !important;
                            }
                            
                            #workOrderDropzone .dz-preview .dz-image {
                                border-radius: 4px !important;
                            }
                        </style>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="workOrderSubmitBtn">
                            <i class="ph-plus-circle me-1"></i> <span id="workOrderSubmitBtnText">Raise Work Order</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete/Archive Confirmation Modal for Issue -->
    <div class="modal fade" id="deleteIssueModal" tabindex="-1" aria-labelledby="deleteIssueModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="deleteIssueModalHeader">
                    <h5 class="modal-title" id="deleteIssueModalLabel">
                        <i class="ph-warning me-2"></i>Confirm Action
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteIssueModalMessage">Are you sure you want to perform this action?</p>
                    <div class="alert" id="deleteIssueModalAlert">
                        <i class="ph-warning me-2"></i>
                        <span id="deleteIssueModalAlertMessage"></span>
                    </div>
                    <div class="alert alert-info">
                        <strong>Issue Details:</strong>
                        <div id="deleteIssueDetails" class="mt-2">
                            <!-- Issue details will be populated here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn" id="confirmDeleteIssueBtn">
                        <i class="ph-trash me-1"></i> Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Generic Delete Confirmation Modal (for other items) -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">
                        <i class="ph-warning text-warning me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteConfirmationMessage">Are you sure you want to delete this item?</p>
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="ph-trash me-1"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ========================================
        // ISSUE DELETE/ARCHIVE MODAL
        // ========================================
        
        // Global variable to store the issue ID for deletion
        let issueToDelete = null;
        
        /**
         * Shows the delete/archive confirmation modal for an issue
         * 
         * @param {number} issueId - The ID of the issue to delete/archive
         * @param {object} issueData - The issue data to display in confirmation
         */
        window.showDeleteIssueModal = function(issueId, issueData) {
            issueToDelete = issueId;
            
            const hasRelatedEntities = issueData.has_related_entities || false;
            const hasWorkOrders = issueData.has_work_orders || false;
            const hasSiteVisits = issueData.has_site_visits || false;
            const hasActions = issueData.has_actions || false;
            const workOrdersCount = issueData.work_orders_count || 0;
            const siteVisitsCount = issueData.site_visits_count || 0;
            const actionsCount = issueData.actions_count || 0;
            
            const actionText = hasRelatedEntities ? 'Archive' : 'Delete';
            const actionIcon = hasRelatedEntities ? 'ph-archive' : 'ph-trash';
            const actionColor = hasRelatedEntities ? 'warning' : 'danger';
            
            // Update modal header
            const $header = $('#deleteIssueModalHeader');
            $header.removeClass('bg-danger bg-warning text-white');
            $header.addClass(hasRelatedEntities ? 'bg-warning text-white' : 'bg-danger text-white');
            
            // Update modal title
            $('#deleteIssueModalLabel').html(`<i class="ph-warning me-2"></i>Confirm ${actionText} Issue`);
            
            // Update modal message
            const messageText = hasRelatedEntities 
                ? `Are you sure you want to archive <strong>${issueData.issue || issueData.ref_no || 'this issue'}</strong>?`
                : `Are you sure you want to permanently delete <strong>${issueData.issue || issueData.ref_no || 'this issue'}</strong>?`;
            $('#deleteIssueModalMessage').html(messageText);
            
            // Update alert message
            const $alert = $('#deleteIssueModalAlert');
            $alert.removeClass('alert-danger alert-warning');
            if (hasRelatedEntities) {
                $alert.addClass('alert-warning');
                
                // Build list of related entities
                const relatedEntities = [];
                if (hasWorkOrders) {
                    relatedEntities.push(`${workOrdersCount} ${workOrdersCount === 1 ? 'work order' : 'work orders'}`);
                }
                if (hasSiteVisits) {
                    relatedEntities.push(`${siteVisitsCount} ${siteVisitsCount === 1 ? 'site visit' : 'site visits'}`);
                }
                if (hasActions) {
                    relatedEntities.push(`${actionsCount} ${actionsCount === 1 ? 'action' : 'actions'}`);
                }
                
                const entitiesText = relatedEntities.join(', ');
                $('#deleteIssueModalAlertMessage').html(
                    `This issue contains <strong>${entitiesText}</strong>. ` +
                    `<strong>The issue will be archived</strong> and can be restored later. The associated entities will remain in the database.`
                );
            } else {
                $alert.addClass('alert-danger');
                $('#deleteIssueModalAlertMessage').html(
                    `This issue has no related entities (work orders, site visits, or actions). <strong>This action will permanently delete the issue</strong> and cannot be undone. All issue data and images will be permanently removed.`
                );
            }
            
            // Populate issue details
            let detailsHtml = `
                <div class="row">
                    <div class="col-6"><strong>Ref No:</strong></div>
                    <div class="col-6">${issueData.ref_no || 'N/A'}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Issue:</strong></div>
                    <div class="col-6">${issueData.issue || 'N/A'}</div>
                </div>
            `;
            
            if (hasRelatedEntities) {
                detailsHtml += '<div class="row mt-2"><div class="col-12"><strong>Related Entities:</strong></div></div>';
                if (hasWorkOrders) {
                    detailsHtml += `
                        <div class="row">
                            <div class="col-6"><strong>Work Orders:</strong></div>
                            <div class="col-6"><span class="badge bg-warning">${workOrdersCount} ${workOrdersCount === 1 ? 'Work Order' : 'Work Orders'}</span></div>
                        </div>
                    `;
                }
                if (hasSiteVisits) {
                    detailsHtml += `
                        <div class="row">
                            <div class="col-6"><strong>Site Visits:</strong></div>
                            <div class="col-6"><span class="badge bg-info">${siteVisitsCount} ${siteVisitsCount === 1 ? 'Site Visit' : 'Site Visits'}</span></div>
                        </div>
                    `;
                }
                if (hasActions) {
                    detailsHtml += `
                        <div class="row">
                            <div class="col-6"><strong>Actions:</strong></div>
                            <div class="col-6"><span class="badge bg-secondary">${actionsCount} ${actionsCount === 1 ? 'Action' : 'Actions'}</span></div>
                        </div>
                    `;
                }
            }
            
            $('#deleteIssueDetails').html(detailsHtml);
            
            // Update confirm button
            const $confirmBtn = $('#confirmDeleteIssueBtn');
            $confirmBtn.removeClass('btn-danger btn-warning');
            $confirmBtn.addClass(`btn-${actionColor}`);
            $confirmBtn.html(`<i class="${actionIcon} me-1"></i>Yes, ${actionText} Issue`);
            
            // Set up the confirm button to actually delete/archive
            $confirmBtn.off('click').on('click', function() {
                deleteIssue(issueId);
            });
            
            // Show the modal
            $('#deleteIssueModal').modal('show');
        };

        /**
         * Deletes/archives an issue via AJAX
         */
        function deleteIssue(issueId) {
            if (!issueId) {
                return;
            }
            
            // Show loading state
            const $confirmBtn = $('#confirmDeleteIssueBtn');
            const originalText = $confirmBtn.html();
            $confirmBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Processing...').prop('disabled', true);
            
            $.ajax({
                url: `/block-issues/${issueId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    // Hide the modal
                    $('#deleteIssueModal').modal('hide');
                    
                    if (response && response.success) {
                        // Show success alert with message from response
                        const message = response.message || 'Issue action completed successfully!';
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="ph-check-circle me-2"></i>
                                ${message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('.page-content').prepend(alertHtml);
                        
                        // Redirect after a short delay
                        setTimeout(function() {
                            window.location.href = '{{ route("block-issues.index") }}';
                        }, 1500);
                    } else {
                        // Show error and reset button
                        const alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ph-warning me-2"></i>
                                Error processing issue. Please try again.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('.page-content').prepend(alertHtml);
                        $confirmBtn.html(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    // Hide the modal
                    $('#deleteIssueModal').modal('hide');
                    
                    // Show error message
                    let errorMessage = 'Error processing issue. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    const alertHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ph-warning me-2"></i>
                            ${errorMessage}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    $('.page-content').prepend(alertHtml);
                    
                    // Reset button state
                    $confirmBtn.html(originalText).prop('disabled', false);
                }
            });
        }
    </script>
@endsection 