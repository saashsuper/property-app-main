<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Contractor Information</h6>
            <button class="btn btn-sm btn-primary">
                <i class="ri-add-line align-bottom me-1"></i> Add Contractor
            </button>
        </div>
        
        @if($block->contractors && $block->contractors->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Contractor ID</th>
                            <th>Type ID</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($block->contractors as $contractor)
                            <tr>
                                <td>#{{ $contractor->contractor_id ?? 'N/A' }}</td>
                                <td>#{{ $contractor->contractor_type_id ?? 'N/A' }}</td>
                                <td>
                                    @if($contractor->status == 1)
                                        <span class="badge bg-success">Default</span>
                                    @else
                                        <span class="badge bg-info">Active</span>
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
                <i class="ri-user-settings-line text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No contractors assigned to this block.</p>
                <button class="btn btn-primary">Assign Contractor</button>
            </div>
        @endif
    </div>
</div>
