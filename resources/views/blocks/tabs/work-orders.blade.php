<!-- Work Orders Header -->
<div class="d-flex align-items-center mb-3 gap-3">
    <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Work Orders</h6>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <div class="text-muted">
                    <i class="ph-wrench font-size-24 mb-2"></i>
                    <p>No work orders created for this block.</p>
                </div>
            </div>
        @endif
