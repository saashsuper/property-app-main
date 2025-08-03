@extends('layouts.master')
@section('title')
    Issue Details - PROMAN
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
                    <h4 class="mb-sm-0">Issue Details</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('issues.index') }}">Issues</a></li>
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
                            <h4 class="card-title mb-0">Issue: {{ $issue->ref_no }}</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('issues.edit', $issue) }}" class="btn btn-primary btn-sm">
                                    <i class="ri-edit-line me-1"></i> Edit
                                </a>
                                <a href="{{ route('issues.index') }}" class="btn btn-secondary btn-sm">
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
                                                <td><span class="badge bg-primary">{{ $issue->ref_no }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Title:</td>
                                                <td>{{ $issue->title }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Description:</td>
                                                <td>{{ $issue->description }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Category:</td>
                                                <td><span class="badge bg-info">{{ $issue->category }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Priority:</td>
                                                <td><span class="badge bg-{{ $issue->priority_color }}">{{ $issue->priority_text }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Status:</td>
                                                <td><span class="badge bg-{{ $issue->status_color }}">{{ $issue->status_text }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Location:</td>
                                                <td>{{ $issue->location ?? 'N/A' }}</td>
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
                                                    @if($issue->assignedTo)
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-2">
                                                                <span class="avatar-title bg-soft-success rounded-3">
                                                                    <i class="ri-user-line font-size-16 text-success"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $issue->assignedTo->name }}</div>
                                                                <small class="text-muted">{{ $issue->assignedTo->email }}</small>
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
                                                    @if($issue->reportedBy)
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-2">
                                                                <span class="avatar-title bg-soft-warning rounded-3">
                                                                    <i class="ri-user-line font-size-16 text-warning"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $issue->reportedBy->name }}</div>
                                                                <small class="text-muted">{{ $issue->reportedBy->email }}</small>
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
                                                    @if($issue->creator)
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-2">
                                                                <span class="avatar-title bg-soft-info rounded-3">
                                                                    <i class="ri-user-line font-size-16 text-info"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $issue->creator->name }}</div>
                                                                <small class="text-muted">{{ $issue->creator->email }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Created:</td>
                                                <td>{{ $issue->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Last Updated:</td>
                                                <td>{{ $issue->updated_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Issue Summary Cards -->
                            <div class="col-12">
                                <h5 class="mb-3">Issue Summary</h5>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-soft-primary rounded-3">
                                                            <i class="ri-error-warning-line font-size-16 text-primary"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Issue Type</h6>
                                                        <p class="mb-0 text-muted">{{ $issue->category }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-soft-{{ $issue->priority_color }} rounded-3">
                                                            <i class="ri-flag-line font-size-16 text-{{ $issue->priority_color }}"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Priority</h6>
                                                        <p class="mb-0 text-muted">{{ $issue->priority_text }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-soft-{{ $issue->status_color }} rounded-3">
                                                            <i class="ri-checkbox-circle-line font-size-16 text-{{ $issue->status_color }}"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Status</h6>
                                                        <p class="mb-0 text-muted">{{ $issue->status_text }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-soft-info rounded-3">
                                                            <i class="ri-map-pin-line font-size-16 text-info"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Location</h6>
                                                        <p class="mb-0 text-muted">{{ $issue->location ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Information -->
                            <div class="col-12">
                                <h5 class="mb-3">Timeline</h5>
                                
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-soft-success rounded-3">
                                                            <i class="ri-calendar-line font-size-16 text-success"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Created</h6>
                                                        <p class="mb-0 text-muted">{{ $issue->created_at->format('M d, Y H:i') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-soft-info rounded-3">
                                                            <i class="ri-time-line font-size-16 text-info"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Last Updated</h6>
                                                        <p class="mb-0 text-muted">{{ $issue->updated_at->format('M d, Y H:i') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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