<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Work Orders</h6>
            <div class="d-flex align-items-center gap-2">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('export.pdf', 'block-work-orders') }}?block_id={{ $block->id }}" 
                       id="exportPdfBtn"
                       class="btn btn-outline-danger btn-sm {{ (!$blockWorkOrders || $blockWorkOrders->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$blockWorkOrders || $blockWorkOrders->count() === 0) ? 'No data to export' : 'Export to PDF' }}"
                       {{ (!$blockWorkOrders || $blockWorkOrders->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-pdf"></i>
                    </a>
                    <a href="{{ route('export.excel', 'block-work-orders') }}?block_id={{ $block->id }}" 
                       id="exportExcelBtn"
                       class="btn btn-outline-success btn-sm {{ (!$blockWorkOrders || $blockWorkOrders->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$blockWorkOrders || $blockWorkOrders->count() === 0) ? 'No data to export' : 'Export to Excel' }}"
                       {{ (!$blockWorkOrders || $blockWorkOrders->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-file-xls"></i>
                    </a>
                    <a href="{{ route('export.print', 'block-work-orders') }}?block_id={{ $block->id }}" 
                       id="exportPrintBtn"
                       class="btn btn-outline-secondary btn-sm {{ (!$blockWorkOrders || $blockWorkOrders->count() === 0) ? 'disabled' : '' }}" 
                       title="{{ (!$blockWorkOrders || $blockWorkOrders->count() === 0) ? 'No data to export' : 'Print' }}" 
                       target="_blank"
                       {{ (!$blockWorkOrders || $blockWorkOrders->count() === 0) ? 'onclick="return false;"' : '' }}>
                        <i class="ph-printer"></i>
                    </a>
                </div>
                <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#createWorkOrderModal">
                    <i class="ph-plus align-bottom me-1"></i> Create Work Order
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover w-100" id="workOrdersTable">
                <thead class="table-light">
                    <tr>
                        <th class="min-width-sm">Work Order #</th>
                        <th class="min-width-md">Title</th>
                        <th class="min-width-sm">Priority</th>
                        <th class="min-width-sm">Status</th>
                        <th class="min-width-sm">Created Date</th>
                        <th class="text-center min-width-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($blockWorkOrders && $blockWorkOrders->count() > 0)
                        @foreach($blockWorkOrders as $workOrder)
                            <tr>
                                <td class="align-middle">#{{ $workOrder->id }}</td>
                                <td class="align-middle">{{ $workOrder->issue ?? 'N/A' }}</td>
                                <td class="align-middle text-nowrap">
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
                                <td class="align-middle text-nowrap">
                                    @if($workOrder->status == 1)
                                        <span class="badge bg-warning">Open</span>
                                    @elseif($workOrder->status == 2)
                                        <span class="badge bg-info">In Progress</span>
                                    @else
                                        <span class="badge bg-success">Completed</span>
                                    @endif
                                </td>
                                <td class="align-middle text-nowrap">{{ $workOrder->created_at ? $workOrder->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="align-middle text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewWorkOrder({{ $workOrder->id }})" title="View Work Order">
                                            <i class="ph-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="editWorkOrder({{ $workOrder->id }})" title="Edit Work Order">
                                            <i class="ph-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="workOrderShowDeleteConfirmation({{ $workOrder->id }}, {
                                            ref_no: '#{{ $workOrder->id }}',
                                            title: '{{ $workOrder->issue ?? 'N/A' }}',
                                            priority: '{{ $workOrder->priority_id == 1 ? 'Low' : ($workOrder->priority_id == 2 ? 'Normal' : ($workOrder->priority_id == 3 ? 'High' : ($workOrder->priority_id == 4 ? 'Urgent' : ($workOrder->priority_id == 5 ? 'Critical' : 'Unknown')))) }}',
                                            status: '{{ $workOrder->status == 1 ? 'Open' : ($workOrder->status == 2 ? 'In Progress' : 'Completed') }}',
                                            created_date: '{{ $workOrder->created_at ? $workOrder->created_at->format('M d, Y') : 'N/A' }}'
                                        })" title="Delete Work Order">
                                            <i class="ph-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('blocks.tabs.edit.work-orders.modals')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" />

<style>
    #workOrdersTable thead th {
        white-space: nowrap;
        font-size: 0.85rem;
    }

    #workOrdersTable tbody td {
        vertical-align: middle;
        font-size: 0.85rem;
    }

    #workOrdersTable .min-width-sm { min-width: 120px; }
    #workOrdersTable .min-width-md { min-width: 160px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
    window.blockId = {{ $block->id }};
    window.csrfToken = '{{ csrf_token() }}';
    window.routes = {
        workOrders: {
            store: '{{ route("block-work-orders.store") }}',
            update: '{{ route("block-work-orders.update", ":id") }}',
            show: '{{ route("block-work-orders.show", ":id") }}',
            destroy: '{{ route("block-work-orders.destroy", ":id") }}'
        }
    };
    window.workOrderShowDeleteConfirmation = window.workOrderShowDeleteConfirmation || function() {};
    window.workOrderAttachEditHandlers = window.workOrderAttachEditHandlers || function() {};
</script>
@include('blocks.tabs.edit.work-orders.scripts')
@endpush
