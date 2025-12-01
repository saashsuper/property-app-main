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
                        <th>Ref No</th>
                        <th>Unit</th>
                        <th>Issue</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Deadline</th>
                        <th>Issued By</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($blockWorkOrders && $blockWorkOrders->count() > 0)
                        @foreach($blockWorkOrders as $workOrder)
                            <tr>
                                <td class="align-middle">
                                    <a href="{{ route('block-work-orders.show', $workOrder) }}" class="text-decoration-none">
                                        <strong>#{{ $workOrder->ref_no }}</strong>
                                    </a>
                                </td>
                                <td class="align-middle">
                                    @if($workOrder->blockUnit)
                                        <span class="badge bg-secondary">{{ $workOrder->blockUnit->unit_name }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @php
                                        $issueText = $workOrder->blockIssue->issue ?? $workOrder->issue ?? null;
                                    @endphp
                                    @if($issueText)
                                        {{ Str::limit($issueText, 50) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="align-middle text-nowrap">
                                    @php
                                        $priorityColors = [
                                            1 => 'success',
                                            2 => 'info',
                                            3 => 'warning',
                                            4 => 'danger',
                                            5 => 'dark'
                                        ];
                                        $color = $priorityColors[$workOrder->priority_id] ?? 'info';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $workOrder->priority_text }}</span>
                                </td>
                                <td class="align-middle text-nowrap">
                                    @php
                                        $statusColors = [
                                            1 => 'warning',
                                            2 => 'info',
                                            3 => 'success',
                                            4 => 'secondary',
                                            5 => 'danger'
                                        ];
                                        $color = $statusColors[$workOrder->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $workOrder->status_text }}</span>
                                </td>
                                <td class="align-middle">
                                    @if($workOrder->contact_name)
                                        <div>{{ $workOrder->contact_name }}</div>
                                        @if($workOrder->contact_email)
                                            <small class="text-muted">{{ $workOrder->contact_email }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="align-middle text-nowrap">
                                    @if($workOrder->deadline_date)
                                        {{ $workOrder->deadline_date->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div>{{ $workOrder->issuedBy->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $workOrder->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('block-work-orders.show', $workOrder) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="ph-eye"></i>
                                        </a>
                                        <a href="{{ route('block-work-orders.edit', $workOrder) }}" class="btn btn-sm btn-outline-warning" title="Edit Work Order">
                                            <i class="ph-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" onclick="workOrderShowDeleteConfirmation({{ $workOrder->id }}, {
                                            ref_no: '#{{ $workOrder->ref_no }}',
                                            title: '{{ Str::limit($workOrder->issue ?? 'N/A', 50) }}',
                                            priority: '{{ $workOrder->priority_text }}',
                                            status: '{{ $workOrder->status_text }}',
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
