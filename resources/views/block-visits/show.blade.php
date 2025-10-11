@extends('layouts.master')
@section('title') Site Visit Details @endsection
@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Visit: {{ $blockVisit->ref_no }}</h4>
        <div>
          <a href="{{ route('block-visits.edit', $blockVisit) }}" class="btn btn-sm btn-secondary"><i class="ph-pencil me-1"></i>Edit</a>
          <a href="{{ route('block-visits.index') }}" class="btn btn-sm btn-outline-secondary"><i class="ph-arrow-left me-1"></i>Back</a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-borderless">
            <tbody>
              <tr>
                <td class="fw-medium" style="width:160px;">Block</td>
                <td>
                  @if($blockVisit->block)
                    <a href="{{ route('blocks.show', $blockVisit->block) }}" class="text-decoration-none">
                      <strong>{{ $blockVisit->block->name }}</strong>
                      <br>
                      <small class="text-muted">{{ $blockVisit->block->management_company }}</small>
                      @if($blockVisit->block->blockType)
                        <br>
                        <span class="badge bg-primary">{{ $blockVisit->block->blockType->name }}</span>
                      @endif
                    </a>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td class="fw-medium">Scheduled</td>
                <td>
                  @if($blockVisit->scheduled_date_time)
                    <span class="badge bg-info">{{ $blockVisit->scheduled_date_time->format('M d, Y H:i') }}</span>
                  @else
                    <span class="text-muted">Not scheduled</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td class="fw-medium">Start Time</td>
                <td>
                  @if($blockVisit->start_date_time)
                    <span class="badge bg-warning">{{ $blockVisit->start_date_time->format('M d, Y H:i') }}</span>
                  @else
                    <span class="text-muted">Not started</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td class="fw-medium">End Time</td>
                <td>
                  @if($blockVisit->end_date_time)
                    <span class="badge bg-success">{{ $blockVisit->end_date_time->format('M d, Y H:i') }}</span>
                  @else
                    <span class="text-muted">Not completed</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td class="fw-medium">Status</td>
                <td>
                  @if($blockVisit->start_date_time && $blockVisit->end_date_time)
                    <span class="badge bg-success">Completed</span>
                  @elseif($blockVisit->start_date_time)
                    <span class="badge bg-warning">In Progress</span>
                  @else
                    <span class="badge bg-info">Scheduled</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td class="fw-medium">Notes</td>
                <td>{{ $blockVisit->notes ?? '—' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Block Information</h5>
      </div>
      <div class="card-body">
        @if($blockVisit->block)
          <div class="d-flex align-items-center mb-3">
            <div class="avatar-sm me-3">
              <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                <i class="ph-buildings"></i>
              </span>
            </div>
            <div>
              <h6 class="mb-1">{{ $blockVisit->block->name }}</h6>
              <p class="mb-0 text-muted">{{ $blockVisit->block->management_company }}</p>
            </div>
          </div>
          <div class="mb-3">
            <strong>Address:</strong><br>
            <small class="text-muted">{{ $blockVisit->block->full_address }}</small>
          </div>
          <div class="mb-3">
            <strong>Units:</strong> {{ $blockVisit->block->no_of_units ?? 0 }}<br>
            <strong>Car Spaces:</strong> {{ $blockVisit->block->car_spaces }}
          </div>
          <a href="{{ route('blocks.show', $blockVisit->block) }}" class="btn btn-outline-primary btn-sm w-100">
            <i class="ph-eye me-1"></i>View Block Details
          </a>
        @else
          <div class="text-muted">No block information available</div>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Attachments ({{ $blockVisit->images->count() }})</h5>
      </div>
      <div class="card-body">
        @forelse($blockVisit->images as $image)
          <div class="mb-3">
            @if($image->image_url)
              @php
                $extension = pathinfo($image->display_name, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
              @endphp
              
              @if($isImage)
                <!-- Image Preview -->
                <a href="{{ $image->image_url }}" target="_blank" class="text-decoration-none">
                  <img src="{{ $image->image_url }}" 
                       class="img-thumbnail rounded" 
                       style="max-width: 100%; height: auto; max-height: 200px; object-fit: cover;"
                       alt="{{ $image->display_name }}">
                </a>
              @else
                <!-- Document Icon -->
                <a href="{{ $image->image_url }}" target="_blank" class="d-flex align-items-center text-decoration-none p-2 bg-light rounded">
                  @if(strtolower($extension) == 'pdf')
                    <i class="ph-file-pdf fs-2 text-danger me-2"></i>
                  @elseif(in_array(strtolower($extension), ['doc', 'docx']))
                    <i class="ph-file-doc fs-2 text-primary me-2"></i>
                  @else
                    <i class="ph-file fs-2 text-secondary me-2"></i>
                  @endif
                  <span class="text-dark">{{ $image->display_name }}</span>
                </a>
              @endif
              <small class="d-block mt-1 text-muted">{{ $image->display_name }}</small>
            @else
              <div class="text-muted"><i class="ph-warning me-1"></i>File not found</div>
            @endif
          </div>
        @empty
          <div class="text-center text-muted py-3">
            <i class="ph-image fs-1 d-block mb-2"></i>
            <p class="mb-0">No images attached</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection


