<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Block Issues</h6>
            <button class="btn btn-sm btn-primary">
                <i class="ri-add-line align-bottom me-1"></i> Report Issue
            </button>
        </div>
        
        @if($block->issues && $block->issues->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Issue ID</th>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Reported Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($block->issues as $issue)
                            <tr>
                                <td>#{{ $issue->id }}</td>
                                <td>{{ $issue->issue ?? 'N/A' }}</td>
                                <td>
                                    @if($issue->priority_id == 1)
                                        <span class="badge bg-success">Low</span>
                                    @elseif($issue->priority_id == 2)
                                        <span class="badge bg-info">Normal</span>
                                    @elseif($issue->priority_id == 3)
                                        <span class="badge bg-warning">High</span>
                                    @elseif($issue->priority_id == 4)
                                        <span class="badge bg-danger">Urgent</span>
                                    @elseif($issue->priority_id == 5)
                                        <span class="badge bg-dark">Critical</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if($issue->issue_status_id == 1)
                                        <span class="badge bg-warning">Open</span>
                                    @elseif($issue->issue_status_id == 2)
                                        <span class="badge bg-info">In Progress</span>
                                    @elseif($issue->issue_status_id == 3)
                                        <span class="badge bg-success">Resolved</span>
                                    @elseif($issue->issue_status_id == 4)
                                        <span class="badge bg-secondary">Closed</span>
                                    @elseif($issue->issue_status_id == 5)
                                        <span class="badge bg-danger">On Hold</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $issue->created_at ? $issue->created_at->format('M d, Y') : 'N/A' }}</td>
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
                <i class="ri-error-warning-line text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No issues reported for this block.</p>
                <button class="btn btn-primary">Report First Issue</button>
            </div>
        @endif
    </div>
</div>
