@extends('layouts.master')
@section('title')
    Block Issue Details - PROMAN
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
                                    <i class="ri-edit-line me-1"></i> Edit
                                </a>
                                <a href="{{ route('block-issues.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ri-arrow-left-line me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
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
                                                                <span class="avatar-title bg-soft-info rounded-3">
                                                                    <i class="ri-building-line font-size-16 text-info"></i>
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
                                                                <span class="avatar-title bg-soft-success rounded-3">
                                                                    <i class="ri-user-line font-size-16 text-success"></i>
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
                                                                <span class="avatar-title bg-soft-warning rounded-3">
                                                                    <i class="ri-user-line font-size-16 text-warning"></i>
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
                                                                <span class="avatar-title bg-soft-info rounded-3">
                                                                    <i class="ri-user-line font-size-16 text-info"></i>
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
                                                        <span class="avatar-title bg-soft-primary rounded-3">
                                                            <i class="ri-user-line font-size-16 text-primary"></i>
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
                                                        <span class="avatar-title bg-soft-success rounded-3">
                                                            <i class="ri-phone-line font-size-16 text-success"></i>
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
                                                        <span class="avatar-title bg-soft-warning rounded-3">
                                                            <i class="ri-mail-line font-size-16 text-warning"></i>
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
                                                        <span class="avatar-title bg-soft-info rounded-3">
                                                            <i class="ri-calendar-line font-size-16 text-info"></i>
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
                                                        <span class="avatar-title bg-soft-danger rounded-3">
                                                            <i class="ri-calendar-line font-size-16 text-danger"></i>
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

                            <!-- Additional Notes -->
                            @if($blockIssue->note_for_access)
                            <div class="col-12">
                                <h5 class="mb-3">Additional Notes</h5>
                                
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-soft-secondary rounded-3">
                                                    <i class="ri-file-text-line font-size-16 text-secondary"></i>
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