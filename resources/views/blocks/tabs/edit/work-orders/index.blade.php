<!-- Define delete functions immediately so they're available for onclick handlers -->
<script>
    // Define minimal placeholder functions that will be overridden by the IIFE version
    // The IIFE version (in scripts.blade.php) will completely replace these when it loads
    // These are just to prevent "function not defined" errors before the IIFE loads
    window.deleteWorkOrder = window.deleteWorkOrder || function(workOrderId) {
        console.warn('deleteWorkOrder placeholder called - waiting for IIFE version...');
        // Wait for IIFE to load and override this function, then call it
        let attempts = 0;
        const checkForIIFE = setInterval(() => {
            attempts++;
            const currentVersion = window.deleteWorkOrder;
            // Check if IIFE version is loaded (it will have 'refreshTable' in its code)
            if (currentVersion && currentVersion.toString().includes('refreshTable') && 
                currentVersion.toString().includes('workOrdersDT')) {
                console.log('IIFE version detected, calling it');
                clearInterval(checkForIIFE);
                currentVersion(workOrderId);
            } else if (attempts > 20) {
                console.error('IIFE deleteWorkOrder not available after 20 attempts');
                clearInterval(checkForIIFE);
            }
        }, 100);
    };
    
    // Define delete confirmation function immediately (before HTML renders)
    window.workOrderShowDeleteConfirmation = function(workOrderId, details) {
        try {
            console.log('workOrderShowDeleteConfirmation called', workOrderId, details);
            
            // Prevent default behavior if event is available
            if (typeof event !== 'undefined' && event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            const container = document.getElementById('deleteWorkOrderDetails');
            if (!container) {
                console.error('deleteWorkOrderDetails container not found');
                return false;
            }
            
            container.innerHTML = `
                <div class="row">
                    <div class="col-5">Reference:</div>
                    <div class="col-7"><strong>#${details.ref_no || 'N/A'}</strong></div>
                </div>
                <div class="row">
                    <div class="col-5">Title:</div>
                    <div class="col-7">${details.title || 'N/A'}</div>
                </div>
                <div class="row">
                    <div class="col-5">Priority:</div>
                    <div class="col-7">${details.priority || 'N/A'}</div>
                </div>
                <div class="row">
                    <div class="col-5">Status:</div>
                    <div class="col-7">${details.status || 'N/A'}</div>
                </div>
                <div class="row">
                    <div class="col-5">Created Date:</div>
                    <div class="col-7">${details.created_date || 'N/A'}</div>
                </div>`;

            const confirmBtn = document.getElementById('confirmDeleteWorkOrderBtn');
            if (!confirmBtn) {
                console.error('confirmDeleteWorkOrderBtn not found');
                return false;
            }
            
            // Remove existing handlers and add new one
            if (typeof $ !== 'undefined') {
                $(confirmBtn).off('click').on('click', function() {
                    if (window.deleteWorkOrder && typeof window.deleteWorkOrder === 'function') {
                        window.deleteWorkOrder(workOrderId);
                    } else {
                        console.error('deleteWorkOrder function not available');
                    }
                });
            } else {
                // Fallback if jQuery not available
                confirmBtn.onclick = function() {
                    if (window.deleteWorkOrder && typeof window.deleteWorkOrder === 'function') {
                        window.deleteWorkOrder(workOrderId);
                    }
                };
            }
            
            const modalElement = document.getElementById('deleteWorkOrderModal');
            if (!modalElement) {
                console.error('deleteWorkOrderModal not found');
                return false;
            }
            
            // Show modal using Bootstrap 5
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                    console.log('Delete modal shown successfully');
                } catch (error) {
                    console.error('Error showing modal:', error);
                }
            } else {
                console.error('Bootstrap Modal not available');
                return false;
            }
            
            return false; // Prevent any default behavior
        } catch (error) {
            console.error('Error in workOrderShowDeleteConfirmation:', error);
            return false;
        }
    };
</script>

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
                <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#workOrderModal">
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
                                        <button type="button" class="btn btn-sm btn-outline-warning edit-work-order" data-work-order-id="{{ $workOrder->id }}" title="Edit Work Order">
                                            <i class="ph-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="event.preventDefault(); event.stopPropagation(); workOrderShowDeleteConfirmation({{ $workOrder->id }}, {
                                                    ref_no: '{{ $workOrder->ref_no }}',
                                                    title: {{ json_encode(Str::limit($workOrder->blockIssue->issue ?? $workOrder->issue ?? 'N/A', 50)) }},
                                                    priority: {{ json_encode($workOrder->priority_text) }},
                                                    status: {{ json_encode($workOrder->status_text) }},
                                                    created_date: {{ json_encode($workOrder->created_at ? $workOrder->created_at->format('M d, Y') : 'N/A') }}
                                                }); return false;"
                                                title="Delete Work Order">
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
    
    // Define delete function immediately so it's available
    window.deleteWorkOrder = function(workOrderId) {
        if (!workOrderId) return;

        const $btn = $('#confirmDeleteWorkOrderBtn');
        const original = $btn.html();
        $btn.html('<i class="ph-spinner-gap me-1 ph-spin"></i>Deleting…').prop('disabled', true);

        const deleteUrl = '{{ route("block-work-orders.destroy", ":id") }}'.replace(':id', workOrderId);
        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) {
                    // Hide modal first
                    const modalElement = document.getElementById('deleteWorkOrderModal');
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                    }
                    
                    // Refresh table - matches inspection pattern exactly
                    // Use window.refreshTable which is exposed by the IIFE in scripts.blade.php
                    console.log('Delete successful, attempting to refresh table...');
                    
                    // Retry mechanism to wait for IIFE functions to be available
                    let retryCount = 0;
                    const maxRetries = 10;
                    const attemptRefresh = () => {
                        retryCount++;
                        if (typeof window.refreshTable === 'function') {
                            console.log('Calling window.refreshTable');
                            window.refreshTable();
                        } else if (typeof window.refreshWorkOrdersTable === 'function') {
                            console.log('Calling window.refreshWorkOrdersTable directly');
                            window.refreshWorkOrdersTable();
                        } else if (retryCount < maxRetries) {
                            console.log(`Refresh functions not available yet (attempt ${retryCount}/${maxRetries}), retrying...`);
                            setTimeout(attemptRefresh, 100);
                        } else {
                            console.error('Refresh functions not available after', maxRetries, 'attempts');
                        }
                    };
                    
                    attemptRefresh();
                } else {
                    // Only show error toast, no alerts
                    if (typeof showToast === 'function') {
                        showToast('danger', data.message || 'Failed to delete work order.');
                    }
                }
            })
            .catch(error => {
                console.error('Work order delete error', error);
                // Only show error toast, no alerts
                if (typeof showToast === 'function') {
                    showToast('danger', 'Error deleting work order.');
                }
            })
            .finally(() => {
                $btn.html(original).prop('disabled', false);
            });
    };
    
    // Define delete confirmation function immediately so it's available for onclick handlers
    window.workOrderShowDeleteConfirmation = function(workOrderId, details) {
        try {
            console.log('workOrderShowDeleteConfirmation called', workOrderId, details);
            
            // Prevent default behavior if event is available
            if (typeof event !== 'undefined' && event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            const container = document.getElementById('deleteWorkOrderDetails');
            if (!container) {
                console.error('deleteWorkOrderDetails container not found');
                alert('Error: Delete modal container not found. Please refresh the page.');
                return false;
            }
            
            container.innerHTML = `
                <div class="row">
                    <div class="col-5">Reference:</div>
                    <div class="col-7"><strong>#${details.ref_no || 'N/A'}</strong></div>
                </div>
                <div class="row">
                    <div class="col-5">Title:</div>
                    <div class="col-7">${details.title || 'N/A'}</div>
                </div>
                <div class="row">
                    <div class="col-5">Priority:</div>
                    <div class="col-7">${details.priority || 'N/A'}</div>
                </div>
                <div class="row">
                    <div class="col-5">Status:</div>
                    <div class="col-7">${details.status || 'N/A'}</div>
                </div>
                <div class="row">
                    <div class="col-5">Created Date:</div>
                    <div class="col-7">${details.created_date || 'N/A'}</div>
                </div>`;

            const confirmBtn = document.getElementById('confirmDeleteWorkOrderBtn');
            if (!confirmBtn) {
                console.error('confirmDeleteWorkOrderBtn not found');
                alert('Error: Delete confirmation button not found. Please refresh the page.');
                return false;
            }
            
            // Remove existing handlers and add new one
            if (typeof $ !== 'undefined') {
                $(confirmBtn).off('click').on('click', function() {
                    if (window.deleteWorkOrder && typeof window.deleteWorkOrder === 'function') {
                        window.deleteWorkOrder(workOrderId);
                    } else {
                        console.error('deleteWorkOrder function not available');
                        alert('Error: Delete function not available. Please refresh the page.');
                    }
                });
            } else {
                // Fallback if jQuery not available
                confirmBtn.onclick = function() {
                    if (window.deleteWorkOrder && typeof window.deleteWorkOrder === 'function') {
                        window.deleteWorkOrder(workOrderId);
                    } else {
                        alert('Error: Delete function not available. Please refresh the page.');
                    }
                };
            }
            
            const modalElement = document.getElementById('deleteWorkOrderModal');
            if (!modalElement) {
                console.error('deleteWorkOrderModal not found');
                alert('Error: Delete modal not found. Please refresh the page.');
                return false;
            }
            
            // Show modal using Bootstrap 5
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                    console.log('Delete modal shown successfully');
                } catch (error) {
                    console.error('Error showing modal:', error);
                    alert('Error showing delete modal: ' + error.message);
                }
            } else {
                console.error('Bootstrap Modal not available');
                alert('Error: Bootstrap Modal library not loaded. Please refresh the page.');
                return false;
            }
            
            return false; // Prevent any default behavior
        } catch (error) {
            console.error('Error in workOrderShowDeleteConfirmation:', error);
            alert('Error: ' + error.message);
            return false;
        }
    };
</script>
@include('blocks.tabs.edit.work-orders.scripts')
@endpush
