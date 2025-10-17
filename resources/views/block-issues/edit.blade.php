@extends('layouts.master')
@section('title')
    Edit Block Issue - PROMAN
@endsection
@section('css')
    <style>
        .modal-xl {
            max-width: 90%;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-title {
            color: #495057;
            font-weight: 600;
        }

        .work-order-section {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .work-order-section h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
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
                        <h4 class="mb-sm-0">Edit Block Issue</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('block-issues.index') }}">Block Issues</a>
                                </li>
                                <li class="breadcrumb-item active">Edit</li>
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
                            <h4 class="card-title">Edit Block Issue: {{ $blockIssue->ref_no }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('block-issues.update', $blockIssue) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Basic Information -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Basic Information</h5>

                                        <div class="mb-3">
                                            <label for="ref_no" class="form-label">Reference Number <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('ref_no') is-invalid @enderror"
                                                id="ref_no" name="ref_no"
                                                value="{{ old('ref_no', $blockIssue->ref_no) }}" required>
                                            @error('ref_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="block_id" class="form-label">Block <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('block_id') is-invalid @enderror"
                                                id="block_id" name="block_id" required>
                                                <option value="">Select Block</option>
                                                @foreach ($blocks as $block)
                                                    <option value="{{ $block->id }}"
                                                        {{ old('block_id', $blockIssue->block_id) == $block->id ? 'selected' : '' }}>
                                                        {{ $block->name }} - {{ $block->blockType->name ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('block_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="block_unit_id" class="form-label">Unit <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('block_unit_id') is-invalid @enderror"
                                                id="block_unit_id" name="block_unit_id" required>
                                                <option value="">Select Unit</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}"
                                                        {{ old('block_unit_id', $blockIssue->block_unit_id) == $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->unit_code }}@if($unit->unit_name) - {{ $unit->unit_name }}@endif @if($unit->blockUnitType) - {{ $unit->blockUnitType->name }} @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('block_unit_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="issue" class="form-label">Issue <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('issue') is-invalid @enderror"
                                                id="issue" name="issue"
                                                value="{{ old('issue', $blockIssue->issue) }}" required>
                                            @error('issue')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="issue_details" class="form-label">Description <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control @error('issue_details') is-invalid @enderror" id="issue_details" name="issue_details"
                                                rows="4" required>{{ old('issue_details', $blockIssue->issue_details) }}</textarea>
                                            @error('issue_details')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Issue Details -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Issue Details</h5>

                                        <div class="mb-3">
                                            <label for="priority_id" class="form-label">Priority <span
                                                    class="text-danger">*</span>{{ $blockIssue->priority_id }}</label>
                                            <select class="form-select @error('priority_id') is-invalid @enderror"
                                                id="priority_id" name="priority_id" required>
                                                <option value="">Select Priority</option>
                                                @foreach ($priorities as $priority)
                                                    <option value="{{ $priority->id }}"
                                                        {{ old('priority_id', $blockIssue->priority_id) == $priority->id ? 'selected' : '' }}>
                                                        {{ $priority->label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('priority_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="issue_status_id" class="form-label">Status <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('issue_status_id') is-invalid @enderror"
                                                id="issue_status_id" name="issue_status_id" required>
                                                <option value="">Select Status</option>
                                                @foreach ($issue_status as $issue_state)
                                                    <option value="{{ $issue_state->id }}"
                                                        {{ old('issue_status_id', $blockIssue->issue_status_id) == $issue_state->id ? 'selected' : '' }}>
                                                        {{ $issue_state->label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="assigned_to" class="form-label">Assign To</label>
                                            <select class="form-select @error('assigned_to') is-invalid @enderror"
                                                id="assigned_to" name="assigned_to">
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('assigned_to', $blockIssue->assigned_to) == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('assigned_to')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="reported_by" class="form-label">Reported By</label>
                                            <select class="form-select @error('reported_by') is-invalid @enderror"
                                                id="reported_by" name="reported_by">
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('reported_by', $blockIssue->reported_by) == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('reported_by')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Additional Details -->
                                    <div class="col-12">
                                        <h5 class="mb-3">Additional Details</h5>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="contact_name" class="form-label">Contact Name</label>
                                                    <input type="text"
                                                        class="form-control @error('contact_name') is-invalid @enderror"
                                                        id="contact_name" name="contact_name"
                                                        value="{{ old('contact_name', $blockIssue->contact_name) }}">
                                                    @error('contact_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="contact_mobile" class="form-label">Contact Mobile</label>
                                                    <input type="text"
                                                        class="form-control @error('contact_mobile') is-invalid @enderror"
                                                        id="contact_mobile" name="contact_mobile"
                                                        value="{{ old('contact_mobile', $blockIssue->contact_mobile) }}">
                                                    @error('contact_mobile')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="contact_email" class="form-label">Contact Email</label>
                                            <input type="email"
                                                class="form-control @error('contact_email') is-invalid @enderror"
                                                id="contact_email" name="contact_email"
                                                value="{{ old('contact_email', $blockIssue->contact_email) }}">
                                            @error('contact_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="preferred_start_date_time" class="form-label">Preferred
                                                        Start Date/Time</label>
                                                    <input type="datetime-local"
                                                        class="form-control @error('preferred_start_date_time') is-invalid @enderror"
                                                        id="preferred_start_date_time" name="preferred_start_date_time"
                                                        value="{{ old('preferred_start_date_time', $blockIssue->preferred_start_date_time ? $blockIssue->preferred_start_date_time->format('Y-m-d\TH:i') : '') }}">
                                                    @error('preferred_start_date_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="preferred_end_date_time" class="form-label">Preferred End
                                                        Date/Time</label>
                                                    <input type="datetime-local"
                                                        class="form-control @error('preferred_end_date_time') is-invalid @enderror"
                                                        id="preferred_end_date_time" name="preferred_end_date_time"
                                                        value="{{ old('preferred_end_date_time', $blockIssue->preferred_end_date_time ? $blockIssue->preferred_end_date_time->format('Y-m-d\TH:i') : '') }}">
                                                    @error('preferred_end_date_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="note_for_access" class="form-label">Note for Access</label>
                                            <textarea class="form-control @error('note_for_access') is-invalid @enderror" id="note_for_access"
                                                name="note_for_access" rows="3">{{ old('note_for_access', $blockIssue->note_for_access) }}</textarea>
                                            @error('note_for_access')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="images" class="form-label">Upload Additional Images</label>
                                            <input type="file"
                                                class="form-control @error('images.*') is-invalid @enderror"
                                                id="images" name="images[]" multiple accept="image/*">
                                            <small class="form-text text-muted">You can select multiple images. Maximum
                                                file size: 2MB each.</small>
                                            @error('images.*')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Existing Images -->
                                        @if ($blockIssue->images && $blockIssue->images->count() > 0)
                                            <div class="mb-3">
                                                <label class="form-label">Existing Images</label>
                                                <div class="row" id="existingImages">
                                                    @foreach ($blockIssue->images as $image)
                                                        <div class="col-md-4 col-lg-3 mb-3"
                                                            id="imageContainer{{ $image->id }}">
                                                            <div class="card border h-100">
                                                                <div class="card-img-top position-relative">
                                                                    <img src="{{ $image->image_url }}"
                                                                        alt="{{ $image->display_name }}"
                                                                        class="img-fluid"
                                                                        style="height: 150px; object-fit: cover; width: 100%;"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editImageModal{{ $image->id }}"
                                                                        style="cursor: pointer;">
                                                                    <div class="position-absolute top-0 end-0 m-2">
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm"
                                                                            onclick="deleteImage({{ $image->id }}, '{{ $image->display_name }}')"
                                                                            title="Delete Image">
                                                                            <i class="ph-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                    <div class="position-absolute top-0 start-0 m-2">
                                                                        <span
                                                                            class="badge bg-secondary">{{ $image->file_size }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block text-truncate"
                                                                        title="{{ $image->display_name }}">
                                                                        {{ $image->display_name }}
                                                                    </small>
                                                                    <small class="text-muted d-block">
                                                                        Uploaded:
                                                                        {{ $image->created_at->format('M d, Y') }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Edit Image Modal -->
                                                        <div class="modal fade" id="editImageModal{{ $image->id }}"
                                                            tabindex="-1"
                                                            aria-labelledby="editImageModalLabel{{ $image->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="editImageModalLabel{{ $image->id }}">
                                                                            {{ $image->display_name }}
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <img src="{{ $image->image_url }}"
                                                                            alt="{{ $image->display_name }}"
                                                                            class="img-fluid" style="max-height: 70vh;">
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <div class="me-auto">
                                                                            <small class="text-muted">
                                                                                Size: {{ $image->file_size }} |
                                                                                Uploaded:
                                                                                {{ $image->created_at->format('M d, Y H:i') }}
                                                                            </small>
                                                                        </div>
                                                                        <a href="{{ $image->image_url }}"
                                                                            class="btn btn-primary btn-sm"
                                                                            download="{{ $image->display_name }}">
                                                                            <i class="ph-download me-1"></i> Download
                                                                        </a>
                                                                        <button type="button"
                                                                            class="btn btn-secondary btn-sm"
                                                                            data-bs-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
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
                                                        <table class="table table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>Reference</th>
                                                                    <th>Status</th>
                                                                    <th>Priority</th>
                                                                    <th>Issued By</th>
                                                                    <th>Created</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($workOrders as $workOrder)
                                                                    <tr>
                                                                        <td>
                                                                            <strong>{{ $workOrder->ref_no }}</strong>
                                                                        </td>
                                                                        <td>
                                                                            @if ($workOrder->status == 1)
                                                                                <span
                                                                                    class="badge bg-warning">Pending</span>
                                                                            @elseif($workOrder->status == 2)
                                                                                <span class="badge bg-info">In
                                                                                    Progress</span>
                                                                            @elseif($workOrder->status == 3)
                                                                                <span
                                                                                    class="badge bg-success">Completed</span>
                                                                            @elseif($workOrder->status == 4)
                                                                                <span
                                                                                    class="badge bg-danger">Cancelled</span>
                                                                            @elseif($workOrder->status == 5)
                                                                                <span class="badge bg-secondary">On
                                                                                    Hold</span>
                                                                            @else
                                                                                <span
                                                                                    class="badge bg-secondary">Unknown</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($workOrder->priority_id == 1)
                                                                                <span class="badge bg-success">Low</span>
                                                                            @elseif($workOrder->priority_id == 2)
                                                                                <span class="badge bg-info">Normal</span>
                                                                            @elseif($workOrder->priority_id == 3)
                                                                                <span class="badge bg-warning">High</span>
                                                                            @elseif($workOrder->priority_id == 4)
                                                                                <span class="badge bg-danger">Urgent</span>
                                                                            @elseif($workOrder->priority_id == 5)
                                                                                <span class="badge bg-dark">Critical</span>
                                                                            @else
                                                                                <span
                                                                                    class="badge bg-secondary">Unknown</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{ $workOrder->issuedBy->name ?? 'N/A' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $workOrder->created_at ? $workOrder->created_at->format('M d, Y H:i') : 'N/A' }}
                                                                        </td>
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
                                                        <p class="text-muted mb-0">Create a work order to get started with
                                                            resolving this issue.</p>
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
                                                    <span class="badge bg-info ms-2">{{ $siteVisits->count() }}</span>
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                @if ($siteVisits->count() > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>Reference</th>
                                                                    <th>Scheduled Date</th>
                                                                    <th>Job Reason</th>
                                                                    <th>Status</th>
                                                                    <th>Team</th>
                                                                    <th>Created By</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($siteVisits as $siteVisit)
                                                                    <tr
                                                                        class="{{ $siteVisit->block_issue_id == $blockIssue->id ? 'table-success' : '' }}">
                                                                        <td>
                                                                            <strong>{{ $siteVisit->ref_no }}</strong>
                                                                            @if ($siteVisit->block_issue_id == $blockIssue->id)
                                                                                <span class="badge bg-success ms-1"
                                                                                    title="Created for this specific issue">
                                                                                    <i class="ph-check"></i>
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{ $siteVisit->scheduled_date_time ? $siteVisit->scheduled_date_time->format('M d, Y H:i') : 'N/A' }}
                                                                        </td>
                                                                        <td>
                                                                            <span
                                                                                class="badge bg-secondary">{{ $siteVisit->jobReason->name ?? 'N/A' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            @if ($siteVisit->jobStatus)
                                                                                @if ($siteVisit->jobStatus->id == 1)
                                                                                    <span
                                                                                        class="badge bg-warning">{{ $siteVisit->jobStatus->name }}</span>
                                                                                @elseif($siteVisit->jobStatus->id == 2)
                                                                                    <span
                                                                                        class="badge bg-info">{{ $siteVisit->jobStatus->name }}</span>
                                                                                @elseif($siteVisit->jobStatus->id == 3)
                                                                                    <span
                                                                                        class="badge bg-success">{{ $siteVisit->jobStatus->name }}</span>
                                                                                @elseif($siteVisit->jobStatus->id == 4)
                                                                                    <span
                                                                                        class="badge bg-danger">{{ $siteVisit->jobStatus->name }}</span>
                                                                                @else
                                                                                    <span
                                                                                        class="badge bg-secondary">{{ $siteVisit->jobStatus->name }}</span>
                                                                                @endif
                                                                            @else
                                                                                <span
                                                                                    class="badge bg-light text-dark">Pending</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($siteVisit->team->count() > 0)
                                                                                @foreach ($siteVisit->team->take(2) as $teamMember)
                                                                                    <span
                                                                                        class="badge bg-light text-dark me-1">{{ $teamMember->user->name ?? 'N/A' }}</span>
                                                                                @endforeach
                                                                                @if ($siteVisit->team->count() > 2)
                                                                                    <span
                                                                                        class="badge bg-light text-dark">+{{ $siteVisit->team->count() - 2 }}
                                                                                        more</span>
                                                                                @endif
                                                                            @else
                                                                                <span class="text-muted">No team
                                                                                    assigned</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{ $siteVisit->createdByUser->name ?? 'N/A' }}
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
                                                        <p class="text-muted mb-0">Create a site visit to schedule
                                                            inspections or maintenance activities.</p>
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
                                                                            <span
                                                                                class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $action->action_type)) }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>{{ $action->description }}</strong>
                                                                            @if ($action->notes)
                                                                                <br><small
                                                                                    class="text-muted">{{ Str::limit($action->notes, 100) }}</small>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="avatar-xs me-2">
                                                                                    <div
                                                                                        class="avatar-title rounded-circle bg-primary">
                                                                                        {{ substr($action->performedBy->name ?? 'N/A', 0, 1) }}
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="mb-0 font-size-14">
                                                                                        {{ $action->performedBy->name ?? 'N/A' }}
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            {{ $action->action_date ? $action->action_date->format('M d, Y H:i') : 'N/A' }}
                                                                        </td>
                                                                        <td>
                                                                            @if ($action->status == 'completed')
                                                                                <span
                                                                                    class="badge bg-success">{{ ucfirst($action->status) }}</span>
                                                                            @elseif($action->status == 'in_progress')
                                                                                <span
                                                                                    class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $action->status)) }}</span>
                                                                            @elseif($action->status == 'pending')
                                                                                <span
                                                                                    class="badge bg-warning">{{ ucfirst($action->status) }}</span>
                                                                            @else
                                                                                <span
                                                                                    class="badge bg-danger">{{ ucfirst($action->status) }}</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($action->priority)
                                                                                @if ($action->priority == 'critical')
                                                                                    <span
                                                                                        class="badge bg-danger">{{ ucfirst($action->priority) }}</span>
                                                                                @elseif($action->priority == 'urgent')
                                                                                    <span
                                                                                        class="badge bg-warning">{{ ucfirst($action->priority) }}</span>
                                                                                @elseif($action->priority == 'high')
                                                                                    <span
                                                                                        class="badge bg-info">{{ ucfirst($action->priority) }}</span>
                                                                                @else
                                                                                    <span
                                                                                        class="badge bg-secondary">{{ ucfirst($action->priority) }}</span>
                                                                                @endif
                                                                            @else
                                                                                <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($action->cost)
                                                                                <span
                                                                                    class="text-success fw-semibold">${{ number_format($action->cost, 2) }}</span>
                                                                            @else
                                                                                <span class="text-muted">-</span>
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
                                                        <h6 class="text-muted">No actions recorded for this issue</h6>
                                                        <p class="text-muted mb-0">Create an action to track progress and
                                                            activities for this issue.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#createWorkOrderModal">
                                                    <i class="ph-plus-circle me-1"></i> Create Work Order
                                                </button>
                                                <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#createSiteVisitModal">
                                                    <i class="ph-map-pin me-1"></i> Create Site Visit
                                                </button>
                                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#createActionModal">
                                                    <i class="ph-activity me-1"></i> Create Action
                                                </button>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <a href="{{ route('block-issues.index') }}" class="btn btn-secondary">
                                                    <i class="ph-arrow-left me-1"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ph-floppy-disk me-1"></i> Update Block Issue
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Work Order Modal -->
    <div class="modal fade" id="createWorkOrderModal" tabindex="-1" aria-labelledby="createWorkOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createWorkOrderModalLabel">Create Work Order for Issue:
                        {{ $blockIssue->ref_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createWorkOrderForm">
                    @csrf

                    <!-- Hidden fields pre-populated from the issue -->
                    <input type="hidden" name="block_id" value="{{ $blockIssue->block_id }}">
                    <input type="hidden" name="block_issue_id" value="{{ $blockIssue->id }}">
                    <input type="hidden" name="issued_by" value="{{ auth()->id() }}">
                    <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                    <input type="hidden" name="updated_by" value="{{ auth()->id() }}">

                    <div class="modal-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="work-order-section">
                                    <h6>Basic Information</h6>


                                    <div class="mb-3">
                                        <label for="work_order_priority_id" class="form-label">Priority <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="work_order_priority_id" name="priority_id"
                                            required>
                                            <option value="">Select Priority</option>
                                            @foreach ($priorities as $priority)
                                                <option value="{{ $priority->value }}"
                                                    {{ $priority->value == $blockIssue->priority_id ? 'selected' : '' }}>
                                                    {{ $priority->label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_status" class="form-label">Status <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="work_order_status" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="1" selected>Pending</option>
                                            <option value="2">In Progress</option>
                                            <option value="3">Completed</option>
                                            <option value="4">Cancelled</option>
                                            <option value="5">On Hold</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_issue" class="form-label">Issue Description</label>
                                        <textarea class="form-control" id="work_order_issue" name="issue" rows="3"
                                            placeholder="Describe the work order issue...">{{ $blockIssue->issue }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact & Scheduling -->
                            <div class="col-md-6">
                                <div class="work-order-section">
                                    <h6>Contact & Scheduling</h6>

                                    <div class="mb-3">
                                        <label for="work_order_contact_name" class="form-label">Contact Name</label>
                                        <input type="text" class="form-control" id="work_order_contact_name"
                                            name="contact_name" value="{{ $blockIssue->contact_name }}"
                                            placeholder="Enter contact name">
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_contact_mobile" class="form-label">Contact Mobile</label>
                                        <input type="text" class="form-control" id="work_order_contact_mobile"
                                            name="contact_mobile" value="{{ $blockIssue->contact_mobile }}"
                                            placeholder="Enter contact mobile">
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_contact_email" class="form-label">Contact Email</label>
                                        <input type="email" class="form-control" id="work_order_contact_email"
                                            name="contact_email" value="{{ $blockIssue->contact_email }}"
                                            placeholder="Enter contact email">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="work_order_preferred_start" class="form-label">Preferred Start
                                                    Date/Time</label>
                                                <input type="datetime-local" class="form-control"
                                                    id="work_order_preferred_start" name="preferred_start_date_time"
                                                    value="{{ $blockIssue->preferred_start_date_time ? $blockIssue->preferred_start_date_time->format('Y-m-d\TH:i') : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="work_order_preferred_end" class="form-label">Preferred End
                                                    Date/Time</label>
                                                <input type="datetime-local" class="form-control"
                                                    id="work_order_preferred_end" name="preferred_end_date_time"
                                                    value="{{ $blockIssue->preferred_end_date_time ? $blockIssue->preferred_end_date_time->format('Y-m-d\TH:i') : '' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_deadline" class="form-label">Deadline Date</label>
                                        <input type="date" class="form-control" id="work_order_deadline"
                                            name="deadline_date" value="{{ now()->addDays(7)->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="work-order-section">
                                    <h6>Additional Information</h6>

                                    <div class="mb-3">
                                        <label for="work_order_note_access" class="form-label">Note for Access</label>
                                        <textarea class="form-control" id="work_order_note_access" name="note_for_access" rows="3"
                                            placeholder="Enter access notes...">{{ $blockIssue->note_for_access }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="work_order_comment" class="form-label">Additional Comments</label>
                                        <textarea class="form-control" id="work_order_comment" name="comment" rows="3"
                                            placeholder="Enter additional comments..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ph-plus-circle me-1"></i> Create Work Order
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Site Visit Modal -->
    <div class="modal fade" id="createSiteVisitModal" tabindex="-1" aria-labelledby="createSiteVisitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createSiteVisitModalLabel">Create Site Visit for Issue:
                        {{ $blockIssue->ref_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createSiteVisitForm">
                    @csrf
                    <input type="hidden" name="block_issue_id" value="{{ $blockIssue->id }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_visit_block_id" class="form-label">Block <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="site_visit_block_id" name="block_id" required>
                                        <option value="">Select a block</option>
                                        @foreach ($blocks as $block)
                                            <option value="{{ $block->id }}"
                                                {{ $blockIssue->block_id == $block->id ? 'selected' : '' }}>
                                                {{ $block->name }} - {{ $block->management_company }}
                                                @if ($block->blockType)
                                                    ({{ $block->blockType->name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_visit_ref_no" class="form-label">Reference No <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="site_visit_ref_no" name="ref_no"
                                        required readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_date_time" class="form-label">Scheduled Date & Time <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="scheduled_date_time"
                                        name="scheduled_date_time" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_visit_user_id" class="form-label">Assigned User <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="site_visit_user_id" name="user_id" required>
                                        <option value="">Select a user</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="job_reason_id" class="form-label">Job Reason <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="job_reason_id" name="job_reason_id" required>
                                        <option value="">Select a reason</option>
                                        @foreach ($jobReasons as $reason)
                                            <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="job_status_id" class="form-label">Job Status</label>
                                    <select class="form-select" id="job_status_id" name="job_status_id">
                                        <option value="">Select a status</option>
                                        @foreach ($jobStatuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="site_visit_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="site_visit_notes" name="notes" rows="3"
                                placeholder="Enter any additional notes for this site visit...">{{ $blockIssue->issue_details }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="site_visit_comment" class="form-label">Comment</label>
                            <textarea class="form-control" id="site_visit_comment" name="comment" rows="3"
                                placeholder="Enter any additional comments...">{{ $blockIssue->comment }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info">
                            <i class="ph-map-pin me-1"></i> Create Site Visit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Action Modal -->
    <div class="modal fade" id="createActionModal" tabindex="-1" aria-labelledby="createActionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createActionModalLabel">Create Action for Issue:
                        {{ $blockIssue->ref_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createActionForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Action Type and Status -->
                            <div class="col-md-6 mb-3">
                                <label for="action_type" class="form-label">Action Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="action_type" name="action_type" required>
                                    <option value="">Select Action Type</option>
                                    @foreach (\App\Models\BlockIssueAction::ACTION_TYPES as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="action_status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="action_status" name="status" required>
                                    @foreach (\App\Models\BlockIssueAction::STATUSES as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ $key == 'completed' ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Performed By and Action Date -->
                            <div class="col-md-6 mb-3">
                                <label for="performed_by" class="form-label">Performed By <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="performed_by" name="performed_by" required>
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $user->id == Auth::id() ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="action_date" class="form-label">Action Date <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="action_date" name="action_date"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Priority and Cost -->
                            <div class="col-md-6 mb-3">
                                <label for="action_priority" class="form-label">Priority</label>
                                <select class="form-select" id="action_priority" name="priority">
                                    <option value="">Select Priority</option>
                                    @foreach (\App\Models\BlockIssueAction::PRIORITIES as $key => $value)
                                        <option value="{{ $key }}" {{ $key == 'normal' ? 'selected' : '' }}>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="action_cost" class="form-label">Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="action_cost" name="cost"
                                        min="0" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="action_description" class="form-label">Description <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="action_description" name="description" rows="3"
                                placeholder="Enter action description..." required></textarea>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="action_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="action_notes" name="notes" rows="3"
                                placeholder="Enter any additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="ph-activity me-1"></i> Create Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Handle block change to load units dynamically
            $('#block_id').on('change', function() {
                const blockId = $(this).val();
                const $unitSelect = $('#block_unit_id');
                
                // Clear existing units
                $unitSelect.html('<option value="">Select Unit</option>');
                
                if (blockId) {
                    // Show loading state
                    $unitSelect.prop('disabled', true);
                    $unitSelect.html('<option value="">Loading units...</option>');
                    
                    // Fetch units for selected block
                    $.ajax({
                        url: `/blocks/${blockId}/units`,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $unitSelect.html('<option value="">Select Unit</option>');
                            
                            if (response.success && response.data && response.data.length > 0) {
                                response.data.forEach(function(unit) {
                                    const unitType = unit.block_unit_type ? ` - ${unit.block_unit_type.name}` : '';
                                    const unitDisplay = unit.unit_code + (unit.unit_name ? ` - ${unit.unit_name}` : '');
                                    $unitSelect.append(
                                        `<option value="${unit.id}">${unitDisplay}${unitType}</option>`
                                    );
                                });
                            } else {
                                $unitSelect.append('<option value="">No units available</option>');
                            }
                            $unitSelect.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading units:', error);
                            $unitSelect.html('<option value="">Error loading units</option>');
                            $unitSelect.prop('disabled', false);
                            
                            Toastify({
                                text: 'Failed to load units. Please try again.',
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545",
                                stopOnFocus: true
                            }).showToast();
                        }
                    });
                } else {
                    $unitSelect.prop('disabled', false);
                }
            });
            
            // Handle work order form submission using jQuery AJAX
            $('#createWorkOrderForm').on('submit', function(e) {
                e.preventDefault();
                // Show loading state
                const $submitBtn = $(this).find('button[type="submit"]');
                const originalText = $submitBtn.html();
                $submitBtn.html('<i class="ph-spinner ph-spin me-1"></i> Creating...').prop('disabled',
                    true);

                // Prepare form data
                const formData = new FormData(this);

                // Submit form via jQuery AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.success) {
                            // Show success message
                            Toastify({
                                text: data.message ||
                                    'Work order has been created successfully!',
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#28a745",
                                stopOnFocus: true
                            }).showToast();

                            // Close modal immediately
                            closeWorkOrderModal();

                            // Also try direct modal close as backup
                            setTimeout(() => {
                                const $modalElement = $('#createWorkOrderModal');
                                if ($modalElement.length && !$modalElement.hasClass(
                                        'show')) {
                                    // Modal is already closed, proceed with redirect
                                    if (data.work_order_id) {
                                        window.location.href =
                                            `{{ route('block-work-orders.index') }}?highlight=${data.work_order_id}`;
                                    }
                                } else {
                                    // Force close and then redirect
                                    closeWorkOrderModal();
                                    setTimeout(() => {
                                        if (data.work_order_id) {
                                            window.location.href =
                                                `{{ route('block-work-orders.index') }}?highlight=${data.work_order_id}`;
                                        }
                                    }, 500);
                                }
                            }, 100);
                        } else {
                            // Show error message
                            Toastify({
                                text: data.message ||
                                    'Failed to create work order. Please try again.',
                                duration: 5000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545",
                                stopOnFocus: true
                            }).showToast();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        let errorMessage = 'An unexpected error occurred. Please try again.';

                        // Try to get error message from response
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                // Keep default error message
                            }
                        }

                        Toastify({
                            text: errorMessage,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545",
                            stopOnFocus: true
                        }).showToast();
                    },
                    complete: function() {
                        // Reset button state
                        $submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });


            // Copy issue details to work order form using jQuery
            $('#createWorkOrderModal').on('show.bs.modal', function() {
                // Copy issue details
                const issueDetails = '{{ $blockIssue->issue_details }}';
                if (issueDetails) {
                    $('#work_order_issue').val(issueDetails);
                }
            });

            // Function to delete images
            function deleteImage(imageId, imageName) {
                if (confirm(
                        `Are you sure you want to delete the image "${imageName}"? This action cannot be undone.`
                        )) {
                    // Show loading state
                    const deleteBtn = event.target.closest('button');
                    const originalHTML = deleteBtn.innerHTML;
                    deleteBtn.innerHTML = '<i class="ph-spinner ph-spin"></i>';
                    deleteBtn.disabled = true;

                    // Send delete request
                    fetch(`/block-issues/images/${imageId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove the image container from the DOM
                                const imageContainer = document.getElementById(`imageContainer${imageId}`);
                                if (imageContainer) {
                                    imageContainer.remove();
                                }

                                // Show success message
                                if (typeof Toastify !== 'undefined') {
                                    Toastify({
                                        text: data.message || 'Image deleted successfully!',
                                        duration: 3000,
                                        gravity: "top",
                                        position: "right",
                                        backgroundColor: "#28a745",
                                        stopOnFocus: true
                                    }).showToast();
                                } else {
                                    alert(data.message || 'Image deleted successfully!');
                                }

                                // Check if no images remain
                                const existingImages = document.getElementById('existingImages');
                                if (existingImages && existingImages.children.length === 0) {
                                    existingImages.innerHTML = `
                                <div class="col-12">
                                    <div class="text-center text-muted py-3">
                                        <i class="ph-images"></i> No images uploaded for this issue.
                                    </div>
                                </div>
                            `;
                                }
                            } else {
                                throw new Error(data.message || 'Failed to delete image');
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting image:', error);
                            const message = error.message || 'Failed to delete image. Please try again.';

                            if (typeof Toastify !== 'undefined') {
                                Toastify({
                                    text: message,
                                    duration: 5000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#dc3545",
                                    stopOnFocus: true
                                }).showToast();
                            } else {
                                alert(message);
                            }
                        })
                        .finally(() => {
                            // Reset button state
                            deleteBtn.innerHTML = originalHTML;
                            deleteBtn.disabled = false;
                        });
                }
            }

            // Handle site visit form submission using jQuery AJAX
            $('#createSiteVisitForm').on('submit', function(e) {
                e.preventDefault();

                // Show loading state
                const $submitBtn = $(this).find('button[type="submit"]');
                const originalText = $submitBtn.html();
                $submitBtn.html('<i class="ph-spinner ph-spin me-1"></i> Creating...').prop('disabled',
                    true);

                // Prepare form data
                const formData = new FormData(this);

                // Submit form via jQuery AJAX
                $.ajax({
                    url: '{{ route('block-visits.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.success) {
                            // Show success message
                            Toastify({
                                text: data.message ||
                                    'Site visit created successfully!',
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#28a745",
                                stopOnFocus: true
                            }).showToast();

                            // Close modal immediately
                            closeSiteVisitModal();

                            // Also try direct modal close as backup
                            setTimeout(() => {
                                const $modalElement = $('#createSiteVisitModal');
                                if ($modalElement.length && !$modalElement.hasClass(
                                        'show')) {
                                    // Modal is already closed, proceed with redirect
                                    window.location.href =
                                        '{{ route('block-visits.index') }}';
                                } else {
                                    // Force close and then redirect
                                    closeSiteVisitModal();
                                    setTimeout(() => {
                                        window.location.href =
                                            '{{ route('block-visits.index') }}';
                                    }, 500);
                                }
                            }, 100);
                        } else {
                            // Show error message
                            Toastify({
                                text: data.message ||
                                    'Failed to create site visit. Please try again.',
                                duration: 5000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545",
                                stopOnFocus: true
                            }).showToast();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        let errorMessage = 'An unexpected error occurred. Please try again.';

                        // Try to get error message from response
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                // Keep default error message
                            }
                        }

                        Toastify({
                            text: errorMessage,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545",
                            stopOnFocus: true
                        }).showToast();
                    },
                    complete: function() {
                        // Reset button state
                        $submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Generate reference number for site visit using jQuery
            $('#createSiteVisitModal').on('show.bs.modal', function() {
                const currentDate = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                const issueId = '{{ $blockIssue->id }}';
                $('#site_visit_ref_no').val(`SV-${currentDate}-${issueId.padStart(4, '0')}`);
            });

            // Copy issue details to site visit form using jQuery
            $('#createSiteVisitModal').on('show.bs.modal', function() {
                // Copy issue details to notes
                const issueDetails = '{{ $blockIssue->issue_details }}';
                const $notesField = $('#site_visit_notes');
                if (issueDetails && !$notesField.val()) {
                    $notesField.val(issueDetails);
                }
            });

            // Handle action form submission using jQuery AJAX
            $('#createActionForm').on('submit', function(e) {
                e.preventDefault();
                // Show loading state
                const $submitBtn = $(this).find('button[type="submit"]');
                const originalText = $submitBtn.html();
                $submitBtn.html('<i class="ph-spinner ph-spin me-1"></i> Creating...').prop('disabled',
                    true);

                // Prepare form data
                const formData = new FormData(this);

                // Submit form via jQuery AJAX
                $.ajax({
                    url: '{{ route('block-issues.store-action', $blockIssue) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.success) {
                            Toastify({
                                text: data.message,
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#28a745",
                                stopOnFocus: true
                            }).showToast();

                            // Close modal and reload page to show new action
                            closeActionModal();
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Unknown error occurred');
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'An error occurred while creating the action.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                // Validation errors
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join('\n');
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        }

                        Toastify({
                            text: errorMessage,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545",
                            stopOnFocus: true
                        }).showToast();
                    },
                    complete: function() {
                        // Reset button state
                        $submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Set current date/time when action modal opens
            $('#createActionModal').on('show.bs.modal', function() {
                const now = new Date();
                const formattedDateTime = now.toISOString().slice(0, 16);
                $('#action_date').val(formattedDateTime);
            });

            // Function to close action modal
            function closeActionModal() {
                const modalElement = document.getElementById('createActionModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                } else {
                    // Trigger the close button click as fallback
                    modalElement.querySelector('.btn-close').click();
                }
            }

            // Add modal event listener for action modal
            $('#createActionModal').on('hidden.bs.modal', function() {
                // Reset form when modal is closed
                $('#createActionForm')[0].reset();
            });

            // Function to close work order modal
            function closeWorkOrderModal() {
                const modalElement = document.getElementById('createWorkOrderModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                } else {
                    // Trigger the close button click as fallback
                    const closeBtn = modalElement.querySelector('.btn-close');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                }
            }

            // Function to close site visit modal
            function closeSiteVisitModal() {
                const modalElement = document.getElementById('createSiteVisitModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                } else {
                    // Trigger the close button click as fallback
                    const closeBtn = modalElement.querySelector('.btn-close');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                }
            }

            // Add modal event listeners for better control using jQuery
            $('#createWorkOrderModal').on('hidden.bs.modal', function() {
                // Reset form when modal is closed
                $('#createWorkOrderForm')[0].reset();
            });

            $('#createSiteVisitModal').on('hidden.bs.modal', function() {
                // Reset form when modal is closed
                $('#createSiteVisitForm')[0].reset();
            });
        }); // End document.ready
    </script>
@endsection
