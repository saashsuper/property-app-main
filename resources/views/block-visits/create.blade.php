@extends('layouts.master')
@section('title') New Site Visit @endsection
@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header"><h4 class="mb-0">Create Site Visit</h4></div>
      <div class="card-body">
        <form method="POST" action="{{ route('block-visits.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Block <span class="text-danger">*</span></label>
            <select class="form-select @error('block_id') is-invalid @enderror" name="block_id" required>
              <option value="">Select a block</option>
              @foreach($blocks as $block)
                <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                  {{ $block->name }} - {{ $block->management_company }}
                  @if($block->blockType)
                    ({{ $block->blockType->name }})
                  @endif
                </option>
              @endforeach
            </select>
            @error('block_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Reference No <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('ref_no') is-invalid @enderror" name="ref_no" value="{{ old('ref_no') }}" required>
            @error('ref_no')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control @error('scheduled_date_time') is-invalid @enderror" name="scheduled_date_time" value="{{ old('scheduled_date_time') }}" required>
            @error('scheduled_date_time')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3" placeholder="Enter any additional notes for this site visit...">{{ old('notes') }}</textarea>
            @error('notes')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div>
            <button class="btn btn-primary" type="submit"><i class="ph-check me-1"></i> Create Site Visit</button>
            <a href="{{ route('block-visits.index') }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Quick Info</h5>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="avatar-sm me-3">
            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
              <i class="ph-buildings"></i>
            </span>
          </div>
          <div>
            <h6 class="mb-1">Total Blocks</h6>
            <p class="mb-0 text-muted">{{ $blocks->count() }} available</p>
          </div>
        </div>
        <div class="d-flex align-items-center mb-3">
          <div class="avatar-sm me-3">
            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
              <i class="ph-map-pin"></i>
            </span>
          </div>
          <div>
            <h6 class="mb-1">Site Visits</h6>
            <p class="mb-0 text-muted">Create new visits</p>
          </div>
        </div>
        <hr>
        <div class="text-muted small">
          <p class="mb-1"><i class="ph-info me-1"></i> Site visits help track property inspections and maintenance activities.</p>
          <p class="mb-0"><i class="ph-clock me-1"></i> Scheduled visits can be updated with start/end times.</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


