<!-- Block Issues Header -->
<div class="d-flex align-items-center mb-3 gap-3">
    <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Block Issues</h6>
    <a href="{{ route('block-issues.create', ['block_id' => $block->id]) }}" class="btn btn-primary">
        <i class="ph-plus align-bottom me-1"></i> Add Issue
    </a>
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
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Issue" 
                                                onclick="deleteIssue({{ $issue->id }}, '{{ $issue->ref_no }}')">
                                            <i class="ph-trash"></i>
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
                <i class="ph-warning text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No issues reported for this block.</p>
            </div>
        @endif
    </div>
</div>

<script>
// Global functions that need to be accessible from HTML onclick attributes
function deleteIssue(issueId, issueRef) {
    // Show confirmation dialog
    if (confirm(`Are you sure you want to delete issue ${issueRef}? This action cannot be undone.`)) {
        // Show loading state
        const deleteBtn = event.target.closest('button');
        const originalHTML = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="ph-spinner ph-spin"></i>';
        deleteBtn.disabled = true;

        // Send delete request
        fetch(`/block-issues/${issueId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                // Remove the row from the table
                const row = deleteBtn.closest('tr');
                row.remove();
                
                // Show success message
                showNotification('Issue deleted successfully!', 'success');
                
                // Check if table is empty and show message
                const tbody = document.querySelector('#issuesTable tbody');
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
                throw new Error('Failed to delete issue');
            }
        })
        .catch(error => {
            console.error('Error deleting issue:', error);
            showNotification('Failed to delete issue. Please try again.', 'error');
        })
        .finally(() => {
            // Reset button state
            deleteBtn.innerHTML = originalHTML;
            deleteBtn.disabled = false;
        });
    }
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
