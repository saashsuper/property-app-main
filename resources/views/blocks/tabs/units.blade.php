<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Unit Information</h6>
            <button class="btn btn-sm btn-primary">
                <i class="ri-add-line align-bottom me-1"></i> Add Unit
            </button>
        </div>
        
        @if($block->units && $block->units->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Unit Code</th>
                            <th>Unit Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($block->units as $unit)
                            <tr>
                                <td>{{ $unit->unit_code ?? 'N/A' }}</td>
                                <td>{{ $unit->unit_name ?? 'N/A' }}</td>
                                <td>{{ $unit->unitType->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-success">Active</span></td>
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
                <i class="ri-home-line text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No units found for this block.</p>
                <button class="btn btn-primary">Add First Unit</button>
            </div>
        @endif
    </div>
</div>
