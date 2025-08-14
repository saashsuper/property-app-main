<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Building Information</h6>
            <button class="btn btn-sm btn-primary">
                <i class="ri-add-line align-bottom me-1"></i> Add Building
            </button>
        </div>
        
        @if($block->buildings && $block->buildings->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Building Name</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($block->buildings as $building)
                            <tr>
                                <td>{{ $building->name ?? 'N/A' }}</td>
                                <td>{{ $building->buildingType->name ?? 'N/A' }}</td>
                                <td>{{ $building->floor_no ?? 'N/A' }}</td>
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
                <i class="ri-building-line text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No buildings found for this block.</p>
                <button class="btn btn-primary">Add First Building</button>
            </div>
        @endif
    </div>
</div>
