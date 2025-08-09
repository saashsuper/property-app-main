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
            <label class="form-label">Block</label>
            <select class="form-select" name="block_id" required>
              <option value="">Select block</option>
              @foreach($blocks as $block)
                <option value="{{ $block->id }}">{{ $block->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Reference No</label>
            <input type="text" class="form-control" name="ref_no" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Scheduled Date & Time</label>
            <input type="datetime-local" class="form-control" name="scheduled_date_time" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <input type="text" class="form-control" name="notes">
          </div>
          <div>
            <button class="btn btn-primary" type="submit"><i class="ph-check me-1"></i> Save</button>
            <a href="{{ route('block-visits.index') }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


