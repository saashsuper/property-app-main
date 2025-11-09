<!-- Block Inspections Header -->
<div class="d-flex align-items-center mb-3 gap-3">
    <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Block Inspections</h6>
</div>

<style>
/* Status badge colors for inspections */
.badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
.badge.bg-primary { background-color: #0d6efd !important; color: #fff !important; }
.badge.bg-secondary { background-color: #6c757d !important; color: #fff !important; }
.badge.bg-success { background-color: #198754 !important; color: #fff !important; }
.badge.bg-light { background-color: #f8f9fa !important; color: #000 !important; }
.badge.bg-info { background-color: #0dcaf0 !important; color: #000 !important; }
.badge.bg-danger { background-color: #dc3545 !important; color: #fff !important; }
</style>

@if(isset($blockInspections) && $blockInspections->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Inspection Date</th>
                            <th>Inspector</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blockInspections as $inspection)
                            <tr>
                                <td>{{ $inspection->ref_no ?? 'N/A' }}</td>
                                <td>{{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('M d, Y H:i') : 'N/A' }}</td>
                                <td>{{ $inspection->creator->name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $status = $issueStatuses->where('value', $inspection->job_status_id)->first();
                                    @endphp
                                    @if($status)
                                        <span class="badge {{ $status->btn_class ?: 'bg-secondary' }}">{{ $status->label }}</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown (ID: {{ $inspection->job_status_id }})</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('block-inspections.show', $inspection->id) }}" class="btn btn-sm btn-outline-primary" title="View Inspection">
                                            <i class="ph-eye"></i>
                                        </a>
                                        @if($inspection->job_status_id == 3)
                                        <a href="{{ route('block-inspections.download-pdf', $inspection->id) }}" class="btn btn-sm btn-outline-danger" title="Download PDF Report">
                                            <i class="ph-file-pdf"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                                                    <i class="ph-magnifying-glass text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No inspections scheduled for this block.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInspectionModal">Schedule First Inspection</button>
            </div>
        @endif

<!-- Add Inspection Modal -->
<div class="modal fade" id="addInspectionModal" tabindex="-1" aria-labelledby="addInspectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addInspectionModalLabel">ADD INSPECTION</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addInspectionForm" method="POST" action="{{ route('block-inspections.store-from-modal') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- User Selection -->
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Lead Inspector <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select Lead Inspector</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Only Property Manager users can be assigned as Lead Inspector</div>
                        </div>
                        
                        <!-- Status Selection -->
                        <div class="col-md-6 mb-3">
                            <label for="job_status_id" class="form-label">Status</label>
                            <select class="form-select" id="job_status_id" name="job_status_id">
                                <option value="">Select Status</option>
                                @foreach($issueStatuses as $status)
                                    <option value="{{ $status->value }}">{{ $status->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Scheduled Date & Time -->
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_date" class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_time" class="form-label">&nbsp;</label>
                            <input type="time" class="form-control" id="scheduled_time" name="scheduled_time" required>
                        </div>
                        
                        <!-- Notes -->
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">Notes <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Enter inspection notes..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="resetForm()">
                        <i class="ph-x align-bottom me-1"></i> RESET
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check align-bottom me-1"></i> SUBMIT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addInspectionForm = document.getElementById('addInspectionForm');
    
    if (addInspectionForm) {
        addInspectionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(addInspectionForm);
            
            // Combine date and time
            const scheduledDate = formData.get('scheduled_date');
            const scheduledTime = formData.get('scheduled_time');
            const scheduledDateTime = scheduledDate + ' ' + scheduledTime;
            
            // Create the data object
            const data = {
                block_id: formData.get('block_id'),
                user_id: formData.get('user_id'),
                scheduled_date_time: scheduledDateTime,
                notes: formData.get('notes'),
                job_status_id: formData.get('job_status_id'),
                _token: formData.get('_token')
            };
            
            // Submit form via AJAX
            fetch(addInspectionForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', 'Inspection scheduled successfully!');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addInspectionModal'));
                    modal.hide();
                    
                    // Reset form
                    addInspectionForm.reset();
                    
                    // DataTable will refresh automatically when modal closes
                } else {
                    showAlert('error', data.message || 'Failed to schedule inspection.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred. Please try again.');
            });
        });
    }
});

// Function to reset form
function resetForm() {
    const form = document.getElementById('addInspectionForm');
    if (form) {
        form.reset();
    }
}

// Function to show alerts
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
