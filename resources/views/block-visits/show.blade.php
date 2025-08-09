@extends('layouts.master')
@section('title') Site Visit Details @endsection
@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Visit: {{ $blockVisit->ref_no }}</h4>
        <a href="{{ route('block-visits.edit', $blockVisit) }}" class="btn btn-sm btn-secondary"><i class="ph-pencil me-1"></i>Edit</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-borderless">
            <tbody>
              <tr><td class="fw-medium" style="width:160px;">Block</td><td>{{ $blockVisit->block->name ?? 'N/A' }}</td></tr>
              <tr><td class="fw-medium">Scheduled</td><td>{{ optional($blockVisit->scheduled_date_time)->format('M d, Y H:i') }}</td></tr>
              <tr><td class="fw-medium">Start</td><td>{{ optional($blockVisit->start_date_time)->format('M d, Y H:i') }}</td></tr>
              <tr><td class="fw-medium">End</td><td>{{ optional($blockVisit->end_date_time)->format('M d, Y H:i') }}</td></tr>
              <tr><td class="fw-medium">Notes</td><td>{{ $blockVisit->notes ?? '—' }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Attachments</h5></div>
      <div class="card-body">
        @forelse($blockVisit->images as $img)
          <div class="mb-2"><i class="ph-image me-2"></i>{{ $img->image_path }}</div>
        @empty
          <div class="text-muted">No images</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection


