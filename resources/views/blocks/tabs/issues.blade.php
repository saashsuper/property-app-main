<!-- Block Issues Header -->
<div class="d-flex align-items-center mb-3 gap-3">
    <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Block Issues</h6>
</div>

<div class="row">
    <div class="col-12">
        
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
                                    @if($issue->priority)
                                        <span class="badge bg-{{ $issue->priority->btn_class }}">{{ $issue->priority->label }}</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if($issue->issueStatus)
                                        <span class="badge bg-{{ $issue->issueStatus->btn_class }}">{{ $issue->issueStatus->label }}</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $issue->created_at ? $issue->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('block-issues.show', $issue) }}" class="btn btn-sm btn-outline-primary" title="View Issue">
                                            <i class="ph-eye"></i>
                                        </a>
                                        <a href="{{ route('block-issues.edit', $issue) }}" class="btn btn-sm btn-outline-warning" title="Edit Issue">
                                            <i class="ph-pencil"></i>
                                        </a>
                                        @php
                                            $hasWorkOrders = $issue->workOrders()->count() > 0;
                                            $workOrdersCount = $issue->workOrders()->count();
                                            $actionText = $hasWorkOrders ? 'Archive' : 'Delete';
                                            $actionIcon = $hasWorkOrders ? 'ph-archive' : 'ph-trash';
                                            $actionColor = $hasWorkOrders ? 'warning' : 'danger';
                                        @endphp
                                        <button type="button" class="btn btn-sm btn-outline-{{ $actionColor }}" title="{{ $actionText }} Issue" 
                                                onclick="showDeleteIssueModal({{ $issue->id }}, {
                                                    ref_no: '{{ addslashes($issue->ref_no) }}',
                                                    issue: '{{ addslashes($issue->issue ?? 'N/A') }}',
                                                    has_work_orders: {{ $hasWorkOrders ? 'true' : 'false' }},
                                                    work_orders_count: {{ $workOrdersCount }}
                                                })">
                                            <i class="{{ $actionIcon }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <div class="text-muted">
                    <i class="ph-warning font-size-24 mb-2"></i>
                    <p>No issues reported for this block.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete/Archive Confirmation Modal -->
<div class="modal fade" id="deleteIssueModal" tabindex="-1" aria-labelledby="deleteIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="deleteIssueModalHeader">
                <h5 class="modal-title" id="deleteIssueModalLabel">
                    <i class="ph-warning me-2"></i>Confirm Action
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteIssueModalMessage">Are you sure you want to perform this action?</p>
                <div class="alert" id="deleteIssueModalAlert">
                    <i class="ph-warning me-2"></i>
                    <span id="deleteIssueModalAlertMessage"></span>
                </div>
                <div class="alert alert-info">
                    <strong>Issue Details:</strong>
                    <div id="deleteIssueDetails" class="mt-2">
                        <!-- Issue details will be populated here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn" id="confirmDeleteIssueBtn">
                    <i class="ph-trash me-1"></i> Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variable to store the issue ID for deletion
let issueToDelete = null;
let issueDeleteBtn = null;

// Global functions that need to be accessible from HTML onclick attributes
function showDeleteIssueModal(issueId, issueData) {
    issueToDelete = issueId;
    issueDeleteBtn = event.target.closest('button');
    
    const hasWorkOrders = issueData.has_work_orders || false;
    const workOrdersCount = issueData.work_orders_count || 0;
    const actionText = hasWorkOrders ? 'Archive' : 'Delete';
    const actionIcon = hasWorkOrders ? 'ph-archive' : 'ph-trash';
    const actionColor = hasWorkOrders ? 'warning' : 'danger';
    
    // Update modal header
    const header = document.getElementById('deleteIssueModalHeader');
    header.className = 'modal-header bg-' + actionColor + ' text-white';
    
    // Update modal title
    document.getElementById('deleteIssueModalLabel').innerHTML = '<i class="ph-warning me-2"></i>Confirm ' + actionText + ' Issue';
    
    // Update modal message
    const messageText = hasWorkOrders 
        ? 'Are you sure you want to archive <strong>' + (issueData.issue || issueData.ref_no || 'this issue') + '</strong>?'
        : 'Are you sure you want to permanently delete <strong>' + (issueData.issue || issueData.ref_no || 'this issue') + '</strong>?';
    document.getElementById('deleteIssueModalMessage').innerHTML = messageText;
    
    // Update alert message
    const alert = document.getElementById('deleteIssueModalAlert');
    alert.className = 'alert alert-' + actionColor;
    const alertMessage = hasWorkOrders
        ? 'This issue contains <strong>' + workOrdersCount + '</strong> related ' + (workOrdersCount === 1 ? 'work order' : 'work orders') + '. ' +
          '<strong>The issue will be archived</strong> and can be restored later. The associated work orders will remain in the database.'
        : 'This issue has no related work orders. <strong>This action will permanently delete the issue</strong> and cannot be undone. All issue data will be permanently removed.';
    document.getElementById('deleteIssueModalAlertMessage').innerHTML = alertMessage;
    
    // Populate issue details
    const detailsHtml = `
        <div class="row">
            <div class="col-6"><strong>Ref No:</strong></div>
            <div class="col-6">${issueData.ref_no || 'N/A'}</div>
        </div>
        <div class="row">
            <div class="col-6"><strong>Issue:</strong></div>
            <div class="col-6">${issueData.issue || 'N/A'}</div>
        </div>
        ${hasWorkOrders ? `
        <div class="row">
            <div class="col-6"><strong>Related Work Orders:</strong></div>
            <div class="col-6"><span class="badge bg-warning">${workOrdersCount} ${workOrdersCount === 1 ? 'Work Order' : 'Work Orders'}</span></div>
        </div>
        ` : ''}
    `;
    document.getElementById('deleteIssueDetails').innerHTML = detailsHtml;
    
    // Update confirm button
    const confirmBtn = document.getElementById('confirmDeleteIssueBtn');
    confirmBtn.className = 'btn btn-' + actionColor;
    confirmBtn.innerHTML = '<i class="' + actionIcon + ' me-1"></i>Yes, ' + actionText + ' Issue';
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('deleteIssueModal'));
    modal.show();
    
    // Set up the confirm button to actually delete/archive
    confirmBtn.onclick = function() {
        deleteIssue(issueId);
    };
}

function deleteIssue(issueId) {
    if (!issueId) {
        return;
    }
    
    // Show loading state
    const confirmBtn = document.getElementById('confirmDeleteIssueBtn');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<i class="ph-spinner-gap ph-spin me-1"></i> Processing...';
    confirmBtn.disabled = true;

    // Send delete request
    fetch(`/block-issues/${issueId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Hide the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteIssueModal'));
        if (modal) {
            modal.hide();
        }
        
        if (data.success) {
            // Remove the row from the table
            if (issueDeleteBtn) {
                const row = issueDeleteBtn.closest('tr');
                if (row) {
                    row.remove();
                }
            }
            
            // Show success message from response
            const message = data.message || 'Issue action completed successfully!';
            showNotification(message, 'success');
            
            // Check if table is empty and show message
            const tbody = document.querySelector('table tbody');
            if (tbody && tbody.children.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            <i class="ph-info-circle"></i> No issues reported for this block.
                        </td>
                    </tr>
                `;
            }
        } else {
            throw new Error(data.message || 'Failed to delete issue');
        }
    })
    .catch(error => {
        console.error('Error deleting issue:', error);
        showNotification(error.message || 'Failed to delete issue. Please try again.', 'error');
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    });
}

// Notification function
function showNotification(message, type = 'info') {
    // Check if Toastify is available
    if (typeof Toastify !== 'undefined') {
        const backgroundColor = type === 'success' ? '#28a745' : 
                             type === 'error' ? '#dc3545' : 
                             type === 'warning' ? '#ffc107' : '#17a2b8';
        
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: backgroundColor,
            stopOnFocus: true
        }).showToast();
    } else {
        // Fallback to alert if Toastify is not available
        alert(message);
    }
}
</script>
