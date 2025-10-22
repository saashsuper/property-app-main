@extends('layouts.master')
@section('title') Site Visit Details @endsection

@section('css')
<style>
.days-badge {
    font-size: 1rem;
    padding: 0.5rem 1rem;
    font-weight: 600;
    border-radius: 0.375rem;
}
.days-overdue {
    background-color: #dc3545;
    color: white;
}
.days-urgent {
    background-color: #fd7e14;
    color: white;
}
.days-soon {
    background-color: #ffc107;
    color: #000;
}
.days-upcoming {
    background-color: #0dcaf0;
    color: #000;
}
.days-future {
    background-color: #6c757d;
    color: white;
}

.visit-image-thumbnail {
    cursor: pointer;
}

.visit-image-thumbnail:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.container-fluid {
    padding-bottom: 15px;
}
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
<div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ph-calendar-check me-2 text-primary"></i>
                        SITE VISIT DETAILS
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-visits.index') }}">Site Visits</a></li>
                            <li class="breadcrumb-item active">Visit Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visit Overview Header with Gradient -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body bg-gradient-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="mb-1 text-white">{{ $blockVisit->ref_no }}</h2>
                                <p class="mb-0 text-white-50">Site Visit Reference</p>
                                <div class="mt-2">
                                    @if($blockVisit->start_date_time && $blockVisit->end_date_time)
                                        <span class="badge bg-success bg-opacity-75 text-white me-2">
                                            <i class="ph-check-circle me-1"></i>Completed
                                        </span>
                                    @elseif($blockVisit->start_date_time)
                                        <span class="badge bg-warning bg-opacity-75 text-white me-2">
                                            <i class="ph-clock me-1"></i>In Progress
                                        </span>
                                    @else
                                        <span class="badge bg-info bg-opacity-75 text-white me-2">
                                            <i class="ph-calendar me-1"></i>Scheduled
                                        </span>
                                    @endif
                                    @if($blockVisit->block)
                                        <span class="badge bg-white bg-opacity-25 text-white">
                                            <i class="ph-buildings me-1"></i>{{ $blockVisit->block->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="d-flex flex-column align-items-md-end">
                                    @if($blockVisit->scheduled_date_time)
                                    <div class="mb-2">
                                        <i class="ph-calendar text-white-50 me-2"></i>
                                        <span class="text-white-50">Scheduled: {{ $blockVisit->scheduled_date_time->format('d M, Y') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <i class="ph-clock text-white-50 me-2"></i>
                                        <span class="text-white-50">{{ $blockVisit->scheduled_date_time->format('h:i A') }}</span>
      </div>
                      @endif
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editSiteVisitModal">
                                            <i class="ph-pencil me-2"></i>Edit Visit
                                        </button>
                                        <a href="{{ route('block-visits.index') }}" class="btn btn-outline-light btn-sm">
                                            <i class="ph-list me-2"></i>Visit List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Layout -->
        <div class="row">
            <!-- Left Column - Visit Details -->
            <div class="col-lg-8">
                <!-- Visit Schedule & Timing -->
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Schedule Details</h6>
                                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                                        <i class="ph-calendar-check text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="mb-2">
                                        <strong><i class="ph-calendar me-2 text-primary"></i>Scheduled:</strong><br>
                  @if($blockVisit->scheduled_date_time)
                    <span class="badge bg-info">{{ $blockVisit->scheduled_date_time->format('M d, Y H:i') }}</span>
                  @else
                    <span class="text-muted">Not scheduled</span>
                  @endif
                                    </p>
                                    <p class="mb-2">
                                        <strong><i class="ph-play me-2 text-warning"></i>Start Time:</strong><br>
                  @if($blockVisit->start_date_time)
                    <span class="badge bg-warning">{{ $blockVisit->start_date_time->format('M d, Y H:i') }}</span>
                  @else
                    <span class="text-muted">Not started</span>
                  @endif
                                    </p>
                                    <p class="mb-0">
                                        <strong><i class="ph-check-circle me-2 text-success"></i>End Time:</strong><br>
                  @if($blockVisit->end_date_time)
                    <span class="badge bg-success">{{ $blockVisit->end_date_time->format('M d, Y H:i') }}</span>
                  @else
                    <span class="text-muted">Not completed</span>
                  @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Status Information</h6>
                                    <div class="bg-success bg-opacity-10 p-2 rounded">
                                        <i class="ph-info text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="mb-2">
                                        <strong><i class="ph-flag me-2 text-info"></i>Status:</strong><br>
                  @if($blockVisit->start_date_time && $blockVisit->end_date_time)
                    <span class="badge bg-success">Completed</span>
                  @elseif($blockVisit->start_date_time)
                    <span class="badge bg-warning">In Progress</span>
                  @else
                    <span class="badge bg-info">Scheduled</span>
                  @endif
                                    </p>
                                    @if($blockVisit->scheduled_date_time && !$blockVisit->start_date_time)
                                        @php
                                            $today = \Carbon\Carbon::now()->startOfDay();
                                            $scheduledDate = \Carbon\Carbon::parse($blockVisit->scheduled_date_time)->startOfDay();
                                            $daysUntil = $today->diffInDays($scheduledDate, false);
                                            
                                            if ($daysUntil < 0) {
                                                $badgeClass = 'days-overdue';
                                                $label = abs($daysUntil) . ' day' . (abs($daysUntil) > 1 ? 's' : '') . ' overdue';
                                                $icon = 'ph-warning-circle';
                                            } elseif ($daysUntil == 0) {
                                                $badgeClass = 'days-urgent';
                                                $label = 'Today';
                                                $icon = 'ph-clock';
                                            } elseif ($daysUntil == 1) {
                                                $badgeClass = 'days-urgent';
                                                $label = 'Tomorrow';
                                                $icon = 'ph-clock';
                                            } elseif ($daysUntil <= 3) {
                                                $badgeClass = 'days-urgent';
                                                $label = $daysUntil . ' days';
                                                $icon = 'ph-alarm';
                                            } elseif ($daysUntil <= 7) {
                                                $badgeClass = 'days-soon';
                                                $label = $daysUntil . ' days';
                                                $icon = 'ph-calendar-check';
                                            } elseif ($daysUntil <= 14) {
                                                $badgeClass = 'days-upcoming';
                                                $label = $daysUntil . ' days';
                                                $icon = 'ph-calendar';
                                            } else {
                                                $badgeClass = 'days-future';
                                                $label = $daysUntil . ' days';
                                                $icon = 'ph-calendar-blank';
                                            }
                                        @endphp
                                        <p class="mb-0">
                                            <strong><i class="ph-timer me-2 text-warning"></i>Days Until Visit:</strong><br>
                                            <span class="badge days-badge {{ $badgeClass }}">
                                                <i class="{{ $icon }} me-1"></i>{{ $label }}
                                            </span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unit & Issue Information -->
                <div class="row mt-3">
                    @if($blockVisit->blockUnit)
                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Unit Information</h6>
                                    <div class="bg-info bg-opacity-10 p-2 rounded">
                                        <i class="ph-house text-info fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="mb-2">
                                        <strong><i class="ph-hash me-2 text-info"></i>Unit Number:</strong><br>
                                        <span class="badge bg-info">{{ $blockVisit->blockUnit->unit_no }}</span>
                                    </p>
                                    @if($blockVisit->blockUnit->blockUnitType)
                                    <p class="mb-2">
                                        <strong><i class="ph-tag me-2 text-primary"></i>Unit Type:</strong><br>
                                        <span class="badge bg-primary">{{ $blockVisit->blockUnit->blockUnitType->name }}</span>
                                    </p>
                                    @endif
                                    @if($blockVisit->blockUnit->contact_person)
                                    <p class="mb-2">
                                        <strong><i class="ph-user me-2 text-secondary"></i>Contact Person:</strong><br>
                                        {{ $blockVisit->blockUnit->contact_person }}
                                    </p>
                                    @endif
                                    @if($blockVisit->blockUnit->phone || $blockVisit->blockUnit->email)
                                    <p class="mb-0">
                                        <strong><i class="ph-phone me-2 text-success"></i>Contact:</strong><br>
                                        @if($blockVisit->blockUnit->phone)
                                            <small>{{ $blockVisit->blockUnit->phone }}</small><br>
                                        @endif
                                        @if($blockVisit->blockUnit->email)
                                            <small>{{ $blockVisit->blockUnit->email }}</small>
                                        @endif
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($blockVisit->blockIssue)
                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Related Issue</h6>
                                    <div class="bg-danger bg-opacity-10 p-2 rounded">
                                        <i class="ph-warning text-danger fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="mb-2">
                                        <strong><i class="ph-hash me-2 text-danger"></i>Reference:</strong><br>
                                        <a href="{{ route('block-issues.show', $blockVisit->blockIssue) }}" class="text-decoration-none">
                                            <span class="badge bg-danger">{{ $blockVisit->blockIssue->ref_no }}</span>
                                        </a>
                                    </p>
                                    @if($blockVisit->blockIssue->issueType)
                                    <p class="mb-2">
                                        <strong><i class="ph-tag me-2 text-primary"></i>Issue Type:</strong><br>
                                        <span class="badge bg-primary">{{ $blockVisit->blockIssue->issueType->name }}</span>
                                    </p>
                                    @endif
                                    @if($blockVisit->blockIssue->issueStatus)
                                    <p class="mb-2">
                                        <strong><i class="ph-flag me-2"></i>Status:</strong><br>
                                        <span class="badge" style="background-color: {{ $blockVisit->blockIssue->issueStatus->badge_class }}">
                                            {{ $blockVisit->blockIssue->issueStatus->name }}
                                        </span>
                                    </p>
                                    @endif
                                    @if($blockVisit->blockIssue->priority)
                                    <p class="mb-0">
                                        <strong><i class="ph-star me-2 text-warning"></i>Priority:</strong><br>
                                        <span class="badge bg-warning text-dark">{{ $blockVisit->blockIssue->priority->name }}</span>
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Job Details & Team -->
                <div class="row mt-3">
                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Job Details</h6>
                                    <div class="bg-success bg-opacity-10 p-2 rounded">
                                        <i class="ph-briefcase text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    @if($blockVisit->jobReason)
                                    <p class="mb-2">
                                        <strong><i class="ph-list-bullets me-2 text-primary"></i>Job Reason:</strong><br>
                                        <span class="badge bg-primary">{{ $blockVisit->jobReason->name }}</span>
                                    </p>
                                    @endif
                                    @if($blockVisit->jobStatus)
                                    <p class="mb-2">
                                        <strong><i class="ph-flag me-2 text-success"></i>Job Status:</strong><br>
                                        <span class="badge bg-success">{{ $blockVisit->jobStatus->name }}</span>
                                    </p>
                                    @endif
                                    @if($blockVisit->createdByUser)
                                    <p class="mb-2">
                                        <strong><i class="ph-user-plus me-2 text-secondary"></i>Created By:</strong><br>
                                        {{ $blockVisit->createdByUser->name }}
                                        <br><small class="text-muted">{{ $blockVisit->created_at->format('d M, Y h:i A') }}</small>
                                    </p>
                                    @endif
                                    @if($blockVisit->updatedByUser)
                                    <p class="mb-0">
                                        <strong><i class="ph-user-check me-2 text-info"></i>Updated By:</strong><br>
                                        {{ $blockVisit->updatedByUser->name }}
                                        <br><small class="text-muted">{{ $blockVisit->updated_at->format('d M, Y h:i A') }}</small>
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($blockVisit->team->count() > 0)
                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Team Members ({{ $blockVisit->team->count() }})</h6>
                                    <div class="bg-purple bg-opacity-10 p-2 rounded">
                                        <i class="ph-users text-purple fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    @foreach($blockVisit->team as $teamMember)
                                        <div class="d-flex align-items-center mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                    <i class="ph-user"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <strong>{{ $teamMember->user->name ?? 'Unknown' }}</strong>
                                                @if($teamMember->user && $teamMember->user->email)
                                                    <br><small class="text-muted">{{ $teamMember->user->email }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Visit Results -->
                @if($blockVisit->results->count() > 0)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Visit Results ({{ $blockVisit->results->count() }})</h6>
                                    <div class="bg-success bg-opacity-10 p-2 rounded">
                                        <i class="ph-clipboard-text text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Description</th>
                                                <th>Notes</th>
                                                <th>Date</th>
              </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($blockVisit->results as $result)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $result->description ?? '-' }}</td>
                                                <td>{{ $result->notes ?? '-' }}</td>
                                                <td>{{ $result->created_at ? $result->created_at->format('d M, Y') : '-' }}</td>
              </tr>
                                            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
      </div>
                @endif

                <!-- Visit Notes -->
                @if($blockVisit->notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
      <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Visit Notes</h6>
                                    <div class="bg-warning bg-opacity-10 p-2 rounded">
                                        <i class="ph-note-pencil text-warning fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="mb-0">{{ $blockVisit->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <!-- Right Column - Block Information & Images -->
            <div class="col-lg-4">
                <!-- Block Information Card -->
        @if($blockVisit->block)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Block Information</h6>
                            <div class="bg-primary bg-opacity-10 p-2 rounded">
                                <i class="ph-buildings text-primary fs-4"></i>
                            </div>
                        </div>
          <div class="d-flex align-items-center mb-3">
            <div class="avatar-sm me-3">
              <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                <i class="ph-buildings"></i>
              </span>
            </div>
            <div>
              <h6 class="mb-1">{{ $blockVisit->block->name }}</h6>
                                <p class="mb-0 text-muted small">{{ $blockVisit->block->management_company }}</p>
            </div>
          </div>
                        <div class="mb-3">
                            <p class="mb-1"><strong>Block Type:</strong><br>
                                @if($blockVisit->block->blockType)
                                    <span class="badge bg-primary">{{ $blockVisit->block->blockType->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
          </div>
          <div class="mb-3">
            <strong>Address:</strong><br>
            <small class="text-muted">{{ $blockVisit->block->full_address }}</small>
          </div>
          <div class="mb-3">
            <strong>Units:</strong> {{ $blockVisit->block->no_of_units ?? 0 }}<br>
                            <strong>Car Spaces:</strong> {{ $blockVisit->block->car_spaces ?? 0 }}
          </div>
          <a href="{{ route('blocks.show', $blockVisit->block) }}" class="btn btn-outline-primary btn-sm w-100">
            <i class="ph-eye me-1"></i>View Block Details
          </a>
      </div>
    </div>
                @endif

                <!-- Visit Images Gallery -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="ph-images me-2 text-primary"></i>Visit Images
                            </h6>
                            <span class="badge bg-primary">{{ $blockVisit->images->count() }}</span>
                        </div>
      </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        @if($blockVisit->images->count() > 0)
                            <div class="row g-2">
                                @foreach($blockVisit->images as $image)
            @if($image->image_url)
              @php
                $extension = pathinfo($image->display_name, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
              @endphp
              
              @if($isImage)
                                        <div class="col-6">
                                            <div class="position-relative">
                  <img src="{{ $image->image_url }}" 
                                                     class="img-fluid rounded visit-image-thumbnail" 
                                                     style="height: 120px; width: 100%; object-fit: cover; cursor: pointer;"
                                                     alt="{{ $image->display_name }}"
                                                     data-image-id="{{ $image->id }}"
                                                     data-image-url="{{ $image->image_url }}"
                                                     data-image-name="{{ $image->display_name }}"
                                                     onclick="openImageCarousel({{ $image->id }})">
                                            </div>
                                        </div>
              @else
                                        <div class="col-12">
                                            <a href="{{ $image->image_url }}" target="_blank" class="text-decoration-none">
                                                <div class="d-flex align-items-center p-2 bg-light rounded border">
                  @if(strtolower($extension) == 'pdf')
                                                        <i class="ph-file-pdf fs-4 text-danger me-2"></i>
                  @elseif(in_array(strtolower($extension), ['doc', 'docx']))
                                                        <i class="ph-file-doc fs-4 text-primary me-2"></i>
                  @else
                                                        <i class="ph-file fs-4 text-secondary me-2"></i>
                  @endif
                                                    <div class="flex-grow-1">
                                                        <small class="d-block text-dark text-truncate">{{ $image->display_name }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="ph-image fs-1 d-block mb-2 opacity-50"></i>
                                <p class="mb-0 small">No images attached</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end container-fluid -->
</div><!-- end page-content -->
@endsection

<!-- Edit Site Visit Modal -->
<div class="modal fade" id="editSiteVisitModal" tabindex="-1" aria-labelledby="editSiteVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSiteVisitModalLabel">
                    <i class="ph-pencil me-2"></i>Edit Site Visit: {{ $blockVisit->ref_no }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSiteVisitForm" method="POST" action="{{ route('block-visits.update', $blockVisit) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Alert Messages Container -->
                    <div id="editVisitAlertContainer" style="display: none;">
                        <div id="editVisitAlert" class="alert" role="alert">
                            <span id="editVisitAlertMessage"></span>
                        </div>
                    </div>
                    
                    <!-- Block and Unit Row -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Block</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                @if($blockVisit->block)
                                    <strong>{{ $blockVisit->block->name }}</strong> - {{ $blockVisit->block->management_company }}
                                    @if($blockVisit->block->blockType)
                                        ({{ $blockVisit->block->blockType->name }})
                                    @endif
                                @else
                                    <span class="text-muted">No block specified</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unit</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                @if($blockVisit->blockUnit)
                                    <strong>{{ $blockVisit->blockUnit->unit_no }}</strong>
                                    @if($blockVisit->blockUnit->blockUnitType)
                                        ({{ $blockVisit->blockUnit->blockUnitType->name }})
              @endif
            @else
                                    <span class="text-muted">No unit specified</span>
            @endif
          </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned Team Member</label>
                            <select class="form-select" name="user_id">
                                <option value="">Select a team member</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $blockVisit->team->first() && $blockVisit->team->first()->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" name="scheduled_date_time" 
                                   value="{{ $blockVisit->scheduled_date_time ? $blockVisit->scheduled_date_time->format('Y-m-d\TH:i') : '' }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date & Time</label>
                            <input type="datetime-local" class="form-control" name="start_date_time" 
                                   value="{{ $blockVisit->start_date_time ? $blockVisit->start_date_time->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date & Time</label>
                            <input type="datetime-local" class="form-control" name="end_date_time" 
                                   value="{{ $blockVisit->end_date_time ? $blockVisit->end_date_time->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Reason</label>
                            <select class="form-select" name="job_reason_id">
                                <option value="">Select job reason</option>
                                @foreach($jobReasons as $reason)
                                    <option value="{{ $reason->id }}" {{ $blockVisit->job_reason_id == $reason->id ? 'selected' : '' }}>
                                        {{ $reason->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Status</label>
                            <select class="form-select" name="job_status_id">
                                <option value="">Select job status</option>
                                @foreach($jobStatuses as $status)
                                    <option value="{{ $status->id }}" {{ $blockVisit->job_status_id == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Enter any additional notes for this site visit...">{{ $blockVisit->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Update Visit
                    </button>
          </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewModalLabel">
                    <span id="currentImageInfo">Image Preview</span>
                    <span id="imageCounter" class="ms-3 text-muted small"></span>
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
      </div>
    </div>
  </div>
</div>

@section('script')
<script>
let allImages = [];

$(document).ready(function() {
    // Collect all images on page load
    $('.visit-image-thumbnail').each(function() {
        allImages.push({
            id: $(this).data('image-id'),
            url: $(this).data('image-url'),
            name: $(this).data('image-name')
        });
    });

    // Handle edit site visit form submission
    $('#editSiteVisitForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        
        // Disable submit button
        submitBtn.prop('disabled', true).html('<i class="ph-spinner ph-spin me-1"></i> Updating...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    showEditVisitAlert('success', response.message || 'Site visit updated successfully!');
                    
                    // Close modal and reload page after delay
                    setTimeout(() => {
                        $('#editSiteVisitModal').modal('hide');
                        location.reload();
                    }, 1500);
                } else {
                    showEditVisitAlert('error', response.message || 'An error occurred.');
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating the site visit.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('<br>');
                }
                
                showEditVisitAlert('error', errorMessage);
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
});

function showEditVisitAlert(type, message) {
    const alertContainer = $('#editVisitAlertContainer');
    const alert = $('#editVisitAlert');
    const alertMessage = $('#editVisitAlertMessage');
    
    alertContainer.show();
    alert.removeClass('alert-success alert-danger').addClass('alert-' + (type === 'success' ? 'success' : 'danger'));
    alertMessage.html(message);
    
    // Auto hide after 5 seconds if success
    if (type === 'success') {
        setTimeout(() => {
            alertContainer.fadeOut();
        }, 5000);
    }
}

function hideEditVisitAlert() {
    $('#editVisitAlertContainer').hide();
}

function openImageCarousel(clickedImageId) {
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
    updateImageInfo();
    
    // Initialize carousel
    const carousel = new bootstrap.Carousel('#imageCarousel', {
        interval: false, // Disable auto-slide
        wrap: true // Enable infinite loop
    });
    
    // Update info when slide changes
    $('#imageCarousel').on('slid.bs.carousel', function (event) {
        updateImageInfo();
    });
    
    // Show modal
    $('#imagePreviewModal').modal('show');
}

function updateImageInfo() {
    const activeItem = $('#imageCarousel .carousel-item.active');
    if (activeItem.length) {
        const imageId = activeItem.data('image-id');
        const currentImage = allImages.find(img => img.id == imageId);
        const currentIndex = allImages.findIndex(img => img.id == imageId) + 1;
        
        if (currentImage) {
            $('#currentImageInfo').text(currentImage.name || 'Image');
            $('#imageCounter').text(`${currentIndex} of ${allImages.length}`);
        }
    }
}

// Keyboard navigation
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
</script>
@endsection
