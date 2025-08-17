<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Site Visit History</h6>
            <button class="btn btn-sm btn-primary">
                <i class="ph-plus align-bottom me-1"></i> Schedule Visit
            </button>
        </div>
        
        @if($block->blockVisits && $block->blockVisits->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Visit Date</th>
                            <th>Reference</th>
                            <th>Job Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($block->blockVisits as $visit)
                            <tr>
                                <td>{{ $visit->scheduled_date_time ? \Carbon\Carbon::parse($visit->scheduled_date_time)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $visit->ref_no ?? 'N/A' }}</td>
                                <td>{{ $visit->job_reason_id ?? 'N/A' }}</td>
                                <td>
                                    @if($visit->end_date_time)
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($visit->start_date_time)
                                        <span class="badge bg-warning">In Progress</span>
                                    @else
                                        <span class="badge bg-info">Scheduled</span>
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
                                                    <i class="ph-calendar-check text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No site visits scheduled for this block.</p>
                <button class="btn btn-primary">Schedule First Visit</button>
            </div>
        @endif
    </div>
</div>
