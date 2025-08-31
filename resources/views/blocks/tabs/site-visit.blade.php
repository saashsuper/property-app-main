        @if($block->blockVisits && $block->blockVisits->count() > 0)
            <div class="table-responsive w-100">
                <table class="table table-bordered table-hover w-100" id="siteVisitsTable" style="width: 100% !important;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%;">Reference</th>
                        <th style="width: 20%;">Visit Date</th>
                        <th style="width: 20%;">User</th>
                        <th style="width: 20%;">Job Reason</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 30%;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($block->blockVisits as $visit)
                        <tr>
                            <td>{{ $visit->ref_no ?? 'N/A' }}</td>
                            <td>{{ $visit->scheduled_date_time ? \Carbon\Carbon::parse($visit->scheduled_date_time)->format('M d, Y H:i') : 'N/A' }}</td>
                            <td>{{ $visit->createdByUser->name ?? 'N/A' }}</td>
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="ph-map-pin text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No site visits scheduled for this block.</p>
            </div>
        @endif

<!-- Add Site Visit Modal -->
<div class="modal fade" id="addSiteVisitModal" tabindex="-1" aria-labelledby="addSiteVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSiteVisitModalLabel">ADD SITE VISIT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <i class="ph-check me-1"></i> Submit
                    </button>
                    <button type="button" class="btn btn-danger" onclick="resetForm()">
                        <i class="ph-x me-1"></i> Reset
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
            <div class="modal-header">
                <h5 class="modal-title" id="editSiteVisitModalLabel">EDIT SITE VISIT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
            { width: '20%', targets: 0 }, // Visit Date
            { width: '15%', targets: 1 }, // Reference
            { width: '20%', targets: 2 }, // User
            { width: '20%', targets: 3 }, // Job Reason
            { width: '15%', targets: 4 }, // Status
            { width: '30%', targets: 5 }  // Notes
        ],
        fixedHeader: true,
        scrollCollapse: true
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
                        localStorage.setItem('activeBlockTab', activeTab.getAttribute('href'));
                    }
                    location.reload();
                }, 1500);
            } else {
                showMessage('addSiteVisitMessage', 'danger', data.message || 'Error scheduling site visit');
            }
        })
        .catch(error => {
            showMessage('addSiteVisitMessage', 'danger', 'Error scheduling site visit');
        });
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

// Restore the active tab from localStorage
var lastTab = localStorage.getItem('activeBlockTab');
if (lastTab) {
    var triggerTab = document.querySelector('.nav-link[data-bs-toggle="tab"][href="' + lastTab + '"]');
    if (triggerTab) {
        var tab = new bootstrap.Tab(triggerTab);
        tab.show();
    }
    localStorage.removeItem('activeBlockTab');
}
</script>
