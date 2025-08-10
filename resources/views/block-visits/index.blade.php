@extends('layouts.master')
@section('title') Site Visits @endsection
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Site Visits</h4>
        <div class="d-flex align-items-center">
          <!-- Export Buttons -->
          <div class="btn-group me-3" role="group">
            <a href="{{ route('export.pdf', 'block-visits') }}?{{ http_build_query(request()->query()) }}" 
               class="btn btn-outline-danger btn-sm" title="Export to PDF">
              <i class="ph-file-pdf"></i>
            </a>
            <a href="{{ route('export.excel', 'block-visits') }}?{{ http_build_query(request()->query()) }}" 
               class="btn btn-outline-success btn-sm" title="Export to Excel">
              <i class="ph-file-xls"></i>
            </a>
            <a href="{{ route('export.print', 'block-visits') }}?{{ http_build_query(request()->query()) }}" 
               class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
              <i class="ph-printer"></i>
            </a>
          </div>

          <a href="{{ route('block-visits.create') }}" class="btn btn-primary"><i class="ph-plus me-1"></i> New Visit</a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>Ref</th>
                <th>Block</th>
                <th>Block Type</th>
                <th>Scheduled</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($visits as $visit)
                <tr>
                  <td><strong>{{ $visit->ref_no }}</strong></td>
                  <td>
                    @if($visit->block)
                      <a href="{{ route('blocks.show', $visit->block) }}" class="text-decoration-none">
                        <strong>{{ $visit->block->name }}</strong>
                        <br>
                        <small class="text-muted">{{ $visit->block->management_company }}</small>
                      </a>
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </td>
                  <td>
                    @if($visit->block && $visit->block->blockType)
                      <span class="badge bg-primary">{{ $visit->block->blockType->name }}</span>
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </td>
                  <td>{{ optional($visit->scheduled_date_time)->format('M d, Y H:i') }}</td>
                  <td>{{ optional($visit->start_date_time)->format('M d, Y H:i') }}</td>
                  <td>{{ optional($visit->end_date_time)->format('M d, Y H:i') }}</td>
                  <td>
                    @if($visit->start_date_time && $visit->end_date_time)
                      <span class="badge bg-success">Completed</span>
                    @elseif($visit->start_date_time)
                      <span class="badge bg-warning">In Progress</span>
                    @else
                      <span class="badge bg-info">Scheduled</span>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <a href="{{ route('block-visits.show', $visit) }}" class="btn btn-sm btn-outline-primary">
                        <i class="ph-eye"></i>
                      </a>
                      <a href="{{ route('block-visits.edit', $visit) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ph-pencil"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center">No visits found</td></tr>
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


