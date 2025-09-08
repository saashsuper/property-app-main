<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Site Visit History</h6>
            <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#addSiteVisitModal">
                <i class="ph-plus align-bottom me-1"></i> Schedule Visit
            </button>
        </div>
        
        <div class="table-responsive w-100">
            <table class="table table-bordered table-hover w-100" id="siteVisitsTable" style="width: 100% !important;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 12%;">Reference</th>
                        <th style="width: 15%;">Visit Date</th>
                        <th style="width: 15%;">User</th>
                        <th style="width: 15%;">Job Reason</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 25%;">Notes</th>
                        <th style="width: 8%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($block->blockVisits as $visit)
                        <tr>
                            <td>
                                <a href="#" class="text-primary fw-bold view-site-visit-details" data-visit-id="{{ $visit->id }}" style="text-decoration: none;">
                                    {{ $visit->ref_no ?? 'N/A' }}
                                </a>
                            </td>
                            <td>{{ $visit->scheduled_date_time ? \Carbon\Carbon::parse($visit->scheduled_date_time)->format('M d, Y H:i') : 'N/A' }}</td>
                            <td>
                                @if($visit->team && $visit->team->count() > 0)
                                    {{ $visit->team->first()->user->name ?? 'N/A' }}
                                @else
                                    {{ $visit->createdByUser->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td>{{ $visit->jobReason->name ?? 'N/A' }}</td>
                            <td>
                                @if($visit->end_date_time)
                                    <span class="badge bg-success">Completed</span>
                                @elseif($visit->start_date_time)
                                    <span class="badge bg-warning">In Progress</span>
                                @else
                                    <span class="badge bg-info">Scheduled</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($visit->notes, 50) ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary edit-site-visit" data-visit-id="{{ $visit->id }}">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-site-visit" data-visit-id="{{ $visit->id }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Site Visit Modal -->
<div class="modal fade" id="addSiteVisitModal" tabindex="-1" aria-labelledby="addSiteVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="addSiteVisitModalLabel" style="color: white !important; padding-bottom: 15px;">Add Site Visit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="addSiteVisitForm">
                <div class="modal-body">
                    <div id="addSiteVisitMessage"></div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User *</label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->userType->name ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scheduled_date" class="form-label">Scheduled Date *</label>
                                <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scheduled_time" class="form-label">Scheduled Time *</label>
                                <input type="time" class="form-control" id="scheduled_time" name="scheduled_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="job_reason_id" class="form-label">Reason *</label>
                                <select class="form-select" id="job_reason_id" name="job_reason_id" required>
                                    <option value="">Select Reason</option>
                                    @foreach($jobReasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes *</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Save
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Site Visit Modal -->
<div class="modal fade" id="editSiteVisitModal" tabindex="-1" aria-labelledby="editSiteVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="editSiteVisitModalLabel" style="color: white !important; padding-bottom: 15px;">Edit Site Visit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="editSiteVisitForm">
                <div class="modal-body">
                    <div id="editSiteVisitMessage"></div>
                    <input type="hidden" id="edit_visit_id" name="visit_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_user_id" class="form-label">User *</label>
                                <select class="form-select" id="edit_user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->userType->name ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_scheduled_date" class="form-label">Scheduled Date *</label>
                                <input type="date" class="form-control" id="edit_scheduled_date" name="scheduled_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_scheduled_time" class="form-label">Scheduled Time *</label>
                                <input type="time" class="form-control" id="edit_scheduled_time" name="scheduled_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_job_reason_id" class="form-label">Reason *</label>
                                <select class="form-select" id="edit_job_reason_id" name="job_reason_id" required>
                                    <option value="">Select Reason</option>
                                    @foreach($jobReasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes *</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Update
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Site Visit Details Modal -->
<div class="modal fade" id="siteVisitDetailsModal" tabindex="-1" aria-labelledby="siteVisitDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="siteVisitDetailsModalLabel">Site Visit Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Reference Number:</label>
                            <p class="form-control-plaintext" id="detail_ref_no">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <p class="form-control-plaintext" id="detail_status">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Scheduled Date & Time:</label>
                            <p class="form-control-plaintext" id="detail_scheduled_date_time">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Assigned User:</label>
                            <p class="form-control-plaintext" id="detail_user">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Job Reason:</label>
                            <p class="form-control-plaintext" id="detail_job_reason">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created By:</label>
                            <p class="form-control-plaintext" id="detail_created_by">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Start Date & Time:</label>
                            <p class="form-control-plaintext" id="detail_start_date_time">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">End Date & Time:</label>
                            <p class="form-control-plaintext" id="detail_end_date_time">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Notes:</label>
                    <p class="form-control-plaintext" id="detail_notes">-</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Comments:</label>
                    <p class="form-control-plaintext" id="detail_comments">-</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.dataTables_wrapper {
    width: 100% !important;
}
.dataTables_wrapper .dataTables_scroll {
    width: 100% !important;
}
#siteVisitsTable {
    width: 100% !important;
}
.dataTables_wrapper .dataTables_scrollBody {
    width: 100% !important;
}
.dataTables_wrapper .dataTables_scrollHead {
    width: 100% !important;
}
.dataTables_wrapper .dataTables_scrollHeadInner {
    width: 100% !important;
}
.dataTables_wrapper .dataTables_scrollHeadInner table {
    width: 100% !important;
}
.dataTables_wrapper .dataTables_scrollBody table {
    width: 100% !important;
}
/* Ensure column headers take full width */
#siteVisitsTable thead th {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 100px;
}
/* Notes column should have more space */
#siteVisitsTable th:nth-child(6),
#siteVisitsTable td:nth-child(6) {
    min-width: 200px;
    max-width: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    const siteVisitsTable = $('#siteVisitsTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        pageLength: 10,
        order: [[0, 'desc']],
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        columnDefs: [
            { width: '15%', targets: 0 }, // Visit Date
            { width: '12%', targets: 1 }, // Reference
            { width: '15%', targets: 2 }, // User
            { width: '15%', targets: 3 }, // Job Reason
            { width: '10%', targets: 4 }, // Status
            { width: '25%', targets: 5 }, // Notes - Give more width
            { width: '8%', targets: 6 }   // Actions
        ],
        fixedHeader: true,
        scrollCollapse: true,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No Site Visit Informations found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        initComplete: function() {
            // Increase search box size
            $('.dataTables_filter input').addClass('form-control').css({
                'width': '300px',
                'height': '38px',
                'font-size': '14px'
            });
        }
    });

    // Add Site Visit Form Submission
    document.getElementById('addSiteVisitForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const scheduledDateTime = formData.get('scheduled_date') + ' ' + formData.get('scheduled_time');
        
        fetch(`/block-visits`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                block_id: {{ $block->id }},
                user_id: formData.get('user_id'),
                scheduled_date_time: scheduledDateTime,
                job_reason_id: formData.get('job_reason_id'),
                notes: formData.get('notes')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('addSiteVisitMessage', 'success', data.message);
                setTimeout(() => {
                    $('#addSiteVisitModal').modal('hide');
                    // Store current tab and reload page
                    var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                    if (activeTab) {
                        // Tab switching code removed
                    }
                    // DataTable will refresh automatically when modal closes
                }, 1500);
            } else {
                showMessage('addSiteVisitMessage', 'danger', data.message || 'Error scheduling site visit');
            }
        })
        .catch(error => {
            showMessage('addSiteVisitMessage', 'danger', 'Error scheduling site visit');
        });
    });

    // Edit Site Visit
    document.querySelectorAll('.edit-site-visit').forEach(button => {
        button.addEventListener('click', function() {
            const visitId = this.getAttribute('data-visit-id');
            
            fetch(`/block-visits/${visitId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const visit = data.data;
                        const scheduledDateTime = new Date(visit.scheduled_date_time);
                        
                        // Get the user from the team relationship
                        // Handle both old records (no team) and new records (with team)
                        let assignedUser = null;
                        if (visit.team && visit.team.length > 0) {
                            assignedUser = visit.team[0].user_id;
                        } else if (visit.created_by) {
                            // Fallback to created_by for old records that don't have team members
                            assignedUser = visit.created_by;
                        }
                        
                        document.getElementById('edit_visit_id').value = visit.id;
                        document.getElementById('edit_user_id').value = assignedUser || '';
                        document.getElementById('edit_scheduled_date').value = scheduledDateTime.toISOString().split('T')[0];
                        document.getElementById('edit_scheduled_time').value = scheduledDateTime.toTimeString().slice(0, 5);
                        document.getElementById('edit_job_reason_id').value = visit.job_reason_id;
                        document.getElementById('edit_notes').value = visit.notes;
                        
                        $('#editSiteVisitModal').modal('show');
                    } else {
                        alert('Could not fetch site visit details: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error fetching site visit details:', error);
                    alert('Error fetching site visit details: ' + error.message);
                });
        });
    });

    // Edit Site Visit Form Submission
    document.getElementById('editSiteVisitForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const visitId = formData.get('visit_id');
        const scheduledDateTime = formData.get('scheduled_date') + ' ' + formData.get('scheduled_time');
        
        fetch(`/block-visits/${visitId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                user_id: formData.get('user_id'),
                scheduled_date_time: scheduledDateTime,
                job_reason_id: formData.get('job_reason_id'),
                notes: formData.get('notes')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('editSiteVisitMessage', 'success', data.message);
                setTimeout(() => {
                    $('#editSiteVisitModal').modal('hide');
                    // Store current tab and reload page
                    var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                    if (activeTab) {
                        // Tab switching code removed
                    }
                    // DataTable will refresh automatically when modal closes
                }, 1500);
            } else {
                showMessage('editSiteVisitMessage', 'danger', data.message || 'Error updating site visit');
            }
        })
        .catch(error => {
            showMessage('editSiteVisitMessage', 'danger', 'Error updating site visit');
        });
    });

    // Delete Site Visit
    document.querySelectorAll('.delete-site-visit').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this site visit?')) {
                const visitId = this.getAttribute('data-visit-id');
                
                fetch(`/block-visits/${visitId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Store current tab and reload page
                        var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                        if (activeTab) {
                            // Tab switching code removed
                        }
                        // DataTable will refresh automatically when modal closes
                    } else {
                        alert('Error deleting site visit');
                    }
                })
                .catch(error => {
                    alert('Error deleting site visit');
                });
            }
        });
    });

    // View Site Visit Details
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-site-visit-details')) {
            e.preventDefault();
            const visitId = e.target.getAttribute('data-visit-id');
            
            fetch(`/block-visits/${visitId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const visit = data.data;
                        
                        // Populate modal with visit details
                        document.getElementById('detail_ref_no').textContent = visit.ref_no || 'N/A';
                        
                        // Determine status
                        let status = 'Scheduled';
                        if (visit.end_date_time) {
                            status = 'Completed';
                        } else if (visit.start_date_time) {
                            status = 'In Progress';
                        }
                        document.getElementById('detail_status').innerHTML = `<span class="badge bg-${status === 'Completed' ? 'success' : status === 'In Progress' ? 'warning' : 'info'}">${status}</span>`;
                        
                        // Format scheduled date time
                        const scheduledDateTime = visit.scheduled_date_time ? new Date(visit.scheduled_date_time).toLocaleString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : 'N/A';
                        document.getElementById('detail_scheduled_date_time').textContent = scheduledDateTime;
                        
                        // Get assigned user
                        let assignedUser = 'N/A';
                        if (visit.team && visit.team.length > 0) {
                            assignedUser = visit.team[0].user ? visit.team[0].user.name : 'N/A';
                        } else if (visit.createdByUser) {
                            assignedUser = visit.createdByUser.name;
                        }
                        document.getElementById('detail_user').textContent = assignedUser;
                        
                        // Job reason
                        document.getElementById('detail_job_reason').textContent = visit.jobReason ? visit.jobReason.name : 'N/A';
                        
                        // Created by
                        document.getElementById('detail_created_by').textContent = visit.createdByUser ? visit.createdByUser.name : 'N/A';
                        
                        // Start date time
                        const startDateTime = visit.start_date_time ? new Date(visit.start_date_time).toLocaleString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : 'N/A';
                        document.getElementById('detail_start_date_time').textContent = startDateTime;
                        
                        // End date time
                        const endDateTime = visit.end_date_time ? new Date(visit.end_date_time).toLocaleString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : 'N/A';
                        document.getElementById('detail_end_date_time').textContent = endDateTime;
                        
                        // Notes
                        document.getElementById('detail_notes').textContent = visit.notes || 'N/A';
                        
                        // Comments
                        document.getElementById('detail_comments').textContent = visit.comment || 'N/A';
                        
                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('siteVisitDetailsModal'));
                        modal.show();
                    } else {
                        alert('Could not fetch site visit details: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error fetching site visit details:', error);
                    alert('Error fetching site visit details: ' + error.message);
                });
        }
    });
});

function showMessage(elementId, type, message) {
    const element = document.getElementById(elementId);
    element.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
}

function resetForm() {
    document.getElementById('addSiteVisitForm').reset();
    document.getElementById('addSiteVisitMessage').innerHTML = '';
}

// Tab switching code removed
</script>
