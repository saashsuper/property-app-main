@extends('layouts.master')
@section('title') Site Visits @endsection
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Site Visits</h4>
        <a href="{{ route('block-visits.create') }}" class="btn btn-primary"><i class="ph-plus me-1"></i> New Visit</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>Ref</th>
                <th>Block</th>
                <th>Scheduled</th>
                <th>Start</th>
                <th>End</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($visits as $visit)
                <tr>
                  <td><strong>{{ $visit->ref_no }}</strong></td>
                  <td>{{ $visit->block->name ?? 'N/A' }}</td>
                  <td>{{ optional($visit->scheduled_date_time)->format('M d, Y H:i') }}</td>
                  <td>{{ optional($visit->start_date_time)->format('M d, Y H:i') }}</td>
                  <td>{{ optional($visit->end_date_time)->format('M d, Y H:i') }}</td>
                  <td>
                    <a href="{{ route('block-visits.show', $visit) }}" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="{{ route('block-visits.edit', $visit) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center">No visits found</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        {{ $visits->links('vendor.pagination.datatables') }}
      </div>
    </div>
  </div>
</div>
@endsection


