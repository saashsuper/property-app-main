<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Inspection History</h6>
            <button class="btn btn-sm btn-primary">
                <i class="ph-plus align-bottom me-1"></i> Schedule Inspection
            </button>
        </div>
        
        @if(isset($blockInspections) && $blockInspections->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Inspection Date</th>
                            <th>Reference</th>
                            <th>Inspector</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blockInspections as $inspection)
                            <tr>
                                <td>{{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $inspection->ref_no ?? 'N/A' }}</td>
                                <td>{{ $inspection->creator->name ?? 'N/A' }}</td>
                                <td>
                                    @if($inspection->job_status_id == 1)
                                        <span class="badge bg-info">Scheduled</span>
                                    @elseif($inspection->job_status_id == 2)
                                        <span class="badge bg-warning">In Progress</span>
                                    @elseif($inspection->job_status_id == 3)
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($inspection->job_status_id == 4)
                                        <span class="badge bg-danger">Cancelled</span>
                                    @elseif($inspection->job_status_id == 5)
                                        <span class="badge bg-secondary">On Hold</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                                                    <i class="ph-magnifying-glass text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No inspections scheduled for this block.</p>
                <button class="btn btn-primary">Schedule First Inspection</button>
            </div>
        @endif
    </div>
</div>
