<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Work Orders</h6>
            <button class="btn btn-sm btn-primary">
                <i class="ri-add-line align-bottom me-1"></i> Create Work Order
            </button>
        </div>
        
        @if(isset($blockWorkOrders) && $blockWorkOrders->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Work Order #</th>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blockWorkOrders as $workOrder)
                            <tr>
                                <td>#{{ $workOrder->id }}</td>
                                <td>{{ $workOrder->issue ?? 'N/A' }}</td>
                                <td>
                                    @if($workOrder->priority_id == 1)
                                        <span class="badge bg-success">Low</span>
                                    @elseif($workOrder->priority_id == 2)
                                        <span class="badge bg-info">Normal</span>
                                    @elseif($workOrder->priority_id == 3)
                                        <span class="badge bg-warning">High</span>
                                    @elseif($workOrder->priority_id == 4)
                                        <span class="badge bg-danger">Urgent</span>
                                    @elseif($workOrder->priority_id == 5)
                                        <span class="badge bg-dark">Critical</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if($workOrder->status == 1)
                                        <span class="badge bg-warning">Open</span>
                                    @elseif($workOrder->status == 2)
                                        <span class="badge bg-info">In Progress</span>
                                    @else
                                        <span class="badge bg-success">Completed</span>
                                    @endif
                                </td>
                                <td>{{ $workOrder->created_at ? $workOrder->created_at->format('M d, Y') : 'N/A' }}</td>
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
                <i class="ri-file-list-line text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No work orders created for this block.</p>
                <button class="btn btn-primary">Create First Work Order</button>
            </div>
        @endif
    </div>
</div>
