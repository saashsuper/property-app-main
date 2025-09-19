<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Inspection History</h6>
            <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#addInspectionModal">
                <i class="ph-plus align-bottom me-1"></i> Add Inspection
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="inspectionsTable">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Inspection Date</th>
                        <th>Inspector</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($blockInspections) && $blockInspections->count() > 0)
                        @foreach($blockInspections as $inspection)
                            <tr>
                                <td>{{ $inspection->ref_no ?? 'N/A' }}</td>
                                <td>{{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    @php
                                        $leadInspector = $inspection->inspectionTeams->where('is_lead', true)->first();
                                    @endphp
                                    {{ $leadInspector ? ($leadInspector->user->name ?? 'N/A') : ($inspection->creator->name ?? 'N/A') }}
                                </td>
                                <td>
                                    @if($inspection->job_status_id == 1)
                                        <span class="badge bg-info">Scheduled</span>
                                    @elseif($inspection->job_status_id == 2)
                                        <span class="badge bg-warning">In Progress</span>
                                    @elseif($inspection->job_status_id == 3)
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($inspection->job_status_id == 4)
                                        <span class="badge bg-danger">Cancelled</span>
                                    @elseif($inspection->job_status_id == 5)
                                        <span class="badge bg-secondary">On Hold</span>
                                    @elseif($inspection->job_status_id == 6)
                                        <span class="badge bg-primary">Rescheduled</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($inspection->notes, 50) ?? 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1 edit-inspection"
                                            data-inspection-id="{{ $inspection->id }}"
                                            data-user-id="{{ isset($leadInspector) && $leadInspector ? $leadInspector->user_id : '' }}"
                                            data-date="{{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('Y-m-d') : '' }}"
                                            data-time="{{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('H:i') : '' }}"
                                            data-notes="{{ e($inspection->notes) }}">
                                        <i class="ph-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteInspection({{ $inspection->id }})">
                                        <i class="ph-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

<!-- Add Inspection Modal -->
<div class="modal fade" id="addInspectionModal" tabindex="-1" aria-labelledby="addInspectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="addInspectionModalLabel" style="color: white !important; padding-bottom: 15px;">Add Inspection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="addInspectionForm" method="POST" action="{{ route('block-inspections.store-from-modal') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- User Selection -->
                        <div class="col-12 mb-3">
                            <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
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

<!-- Edit Inspection Modal -->
<div class="modal fade" id="editInspectionModal" tabindex="-1" aria-labelledby="editInspectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="editInspectionModalLabel" style="color: white !important; padding-bottom: 15px;">Edit Inspection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="editInspectionForm" method="POST" action="{{ route('block-inspections.update', 0) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="inspection_id" id="edit_inspection_id">
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div id="editInspectionMessage"></div>
                    <div class="row">
                        <!-- Reference Number (Read-only) -->
                        <div class="col-md-6 mb-3">
                            <label for="edit_ref_no" class="form-label">Reference Number</label>
                            <input type="text" class="form-control" id="edit_ref_no" name="ref_no" readonly style="background-color: #f8f9fa;">
                            <div class="form-text">Reference number cannot be changed</div>
                        </div>
                        
                        <!-- Status Selection -->
                        <div class="col-md-6 mb-3">
                            <label for="edit_job_status_id" class="form-label">Status</label>
                            <select class="form-select" id="edit_job_status_id" name="job_status_id">
                                <option value="">Select Status</option>
                                @php
                                    $jobStatuses = \App\Models\JobStatus::where('is_updated', true)->get();
                                @endphp
                                @foreach($jobStatuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Only updatable statuses are shown</div>
                        </div>
                        
                        <!-- User Selection -->
                        <div class="col-12 mb-3">
                            <label for="edit_user_id" class="form-label">Lead Inspector <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_user_id" name="user_id" required>
                                <option value="">Select Lead Inspector</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Scheduled Date & Time -->
                        <div class="col-md-6 mb-3">
                            <label for="edit_scheduled_date" class="form-label">Scheduled Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_scheduled_date" name="scheduled_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_scheduled_time" class="form-label">Scheduled Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="edit_scheduled_time" name="scheduled_time" required>
                        </div>
                        
                        
                        <!-- End Date & Time (if inspection is completed) -->
                        <div class="col-md-6 mb-3" id="edit_end_date_group" style="display: none;">
                            <label for="edit_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-6 mb-3" id="edit_end_time_group" style="display: none;">
                            <label for="edit_end_time" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="edit_end_time" name="end_time" readonly style="background-color: #f8f9fa;">
                        </div>
                        
                        <!-- Notes -->
                        <div class="col-12 mb-3">
                            <label for="edit_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="4" placeholder="Enter inspection notes..."></textarea>
                        </div>
                        
                        <!-- Created/Updated Info -->
                        <div class="col-12 mb-3">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <small class="text-muted">
                                        <strong>Created:</strong> <span id="edit_created_info">-</span><br>
                                        <strong>Last Updated:</strong> <span id="edit_updated_info">-</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Update Inspection
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    var inspectionsDT = $('#inspectionsTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        ],
        pageLength: 10,
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        columnDefs: [
            { width: '15%', targets: 0 }, // Reference
            { width: '15%', targets: 1 }, // Inspection Date
            { width: '20%', targets: 2 }, // Inspector
            { width: '12%', targets: 3 }, // Status
            { width: '28%', targets: 4 }, // Notes
            { width: '10%', targets: 5 }  // Actions
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No Inspection Informations found",
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

            // Fix header/body alignment after render
            setTimeout(function() {
                inspectionsDT.columns.adjust();
            }, 0);
        }
    });

    // Adjust columns on window resize to keep header/body aligned
    window.addEventListener('resize', function() {
        if (inspectionsDT) {
            inspectionsDT.columns.adjust();
        }
    });

    const addInspectionForm = document.getElementById('addInspectionForm');
    const editInspectionForm = document.getElementById('editInspectionForm');
    
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
    
    if (editInspectionForm) {
        editInspectionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(editInspectionForm);
            
            // Combine date and time
            const scheduledDate = formData.get('scheduled_date');
            const scheduledTime = formData.get('scheduled_time');
            const scheduledDateTime = scheduledDate + ' ' + scheduledTime;
            
            // Create the data object
            const data = {
                inspection_id: formData.get('inspection_id'),
                block_id: formData.get('block_id'),
                user_id: formData.get('user_id'),
                scheduled_date_time: scheduledDateTime,
                notes: formData.get('notes'),
                job_status_id: formData.get('job_status_id') || null,
                _token: formData.get('_token'),
                _method: formData.get('_method')
            };
            
            // Show loading state on submit button
            const submitBtn = editInspectionForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ph-spinner ph-spin me-1"></i>Updating...';
            submitBtn.disabled = true;
            
            // Submit form via AJAX
            const inspectionId = formData.get('inspection_id');
            fetch(`/block-inspections/${inspectionId}`, {
                method: 'PUT',
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
                    showAlert('success', data.message || 'Inspection updated successfully!');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editInspectionModal'));
                    modal.hide();
                    
                    // Reset form
                    editInspectionForm.reset();
                    
                    // Refresh the DataTable
                    if (typeof refreshInspectionsTable === 'function') {
                        refreshInspectionsTable();
                    } else {
                        // Fallback: reload the page
                        setTimeout(() => window.location.reload(), 1500);
                    }
                } else {
                    // Show error message in modal
                    const messageDiv = document.getElementById('editInspectionMessage');
                    if (messageDiv) {
                        messageDiv.innerHTML = `<div class="alert alert-danger">${data.message || 'Failed to update inspection.'}</div>`;
                    } else {
                        showAlert('error', data.message || 'Failed to update inspection.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('editInspectionMessage');
                if (messageDiv) {
                    messageDiv.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again.</div>`;
                } else {
                    showAlert('error', 'An error occurred. Please try again.');
                }
            })
            .finally(() => {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Edit Inspection Event Listener (enhanced version)
    function attachInspectionEventListeners() {
        document.querySelectorAll('.edit-inspection').forEach(button => {
            button.addEventListener('click', function() {
                const inspectionId = this.getAttribute('data-inspection-id');
                const scheduledDate = this.getAttribute('data-date');
                const scheduledTime = this.getAttribute('data-time');
                const notes = this.getAttribute('data-notes');
                const userId = this.getAttribute('data-user-id');
                
                // Clear any previous success/error message before loading new data
                const editMsgEl = document.getElementById('editInspectionMessage');
                if (editMsgEl) {
                    editMsgEl.innerHTML = '';
                }
                
                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="ph-spinner ph-spin me-1"></i>Loading...';
                this.disabled = true;
                
                // Clear form first
                document.getElementById('editInspectionForm').reset();
                
                // Set basic data
                document.getElementById('edit_inspection_id').value = inspectionId;
                
                // Debug logging to see what data we have
                console.log('Edit button clicked - Data attributes:', {
                    inspectionId: inspectionId,
                    scheduledDate: scheduledDate,
                    scheduledTime: scheduledTime,
                    notes: notes,
                    userId: userId
                });
                
                // Show modal first
                $('#editInspectionModal').modal('show');
                
                // Then fetch detailed data from server
                fetch(`/block-inspections/${inspectionId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const inspection = data.data;
                        const scheduledDateTime = new Date(inspection.scheduled_date_time);
                        
                        // Get the lead inspector from the inspection teams
                        let assignedUser = null;
                        if (inspection.inspectionTeams && inspection.inspectionTeams.length > 0) {
                            const leadMember = inspection.inspectionTeams.find(team => team.is_lead) || inspection.inspectionTeams[0];
                            assignedUser = leadMember.user_id;
                        } else if (inspection.inspection_teams && inspection.inspection_teams.length > 0) {
                            const leadMember = inspection.inspection_teams.find(team => team.is_lead) || inspection.inspection_teams[0];
                            assignedUser = leadMember.user_id;
                        } else if (inspection.created_by) {
                            assignedUser = inspection.created_by;
                        }
                        
                        // Update form fields with fresh data from server
                        document.getElementById('edit_inspection_id').value = inspection.id;
                        document.getElementById('edit_ref_no').value = inspection.ref_no || '';
                        document.getElementById('edit_user_id').value = assignedUser || '';
                        
                        // Ensure scheduled date and time are properly formatted
                        if (inspection.scheduled_date_time) {
                            const scheduledDateTime = new Date(inspection.scheduled_date_time);
                            const formattedDate = scheduledDateTime.toISOString().split('T')[0];
                            const formattedTime = scheduledDateTime.toTimeString().slice(0, 5);
                            
                            console.log('Server data - scheduled_date_time:', inspection.scheduled_date_time);
                            console.log('Server data - formatted date:', formattedDate);
                            console.log('Server data - formatted time:', formattedTime);
                            
                            // Force set the values with a small delay to ensure DOM is ready
                            setTimeout(() => {
                                const dateField = document.getElementById('edit_scheduled_date');
                                const timeField = document.getElementById('edit_scheduled_time');
                                
                                if (dateField) {
                                    dateField.value = formattedDate;
                                    console.log('Set scheduled date from server to:', formattedDate, 'Field value:', dateField.value);
                                } else {
                                    console.error('Date field not found!');
                                }
                                
                                if (timeField) {
                                    timeField.value = formattedTime;
                                    console.log('Set scheduled time from server to:', formattedTime, 'Field value:', timeField.value);
                                } else {
                                    console.error('Time field not found!');
                                }
                            }, 100);
                        } else {
                            console.log('No scheduled_date_time found in server data');
                        }
                        
                        document.getElementById('edit_notes').value = inspection.notes || '';
                        document.getElementById('edit_job_status_id').value = inspection.job_status_id || '';
                        
                        
                        // Handle end date/time
                        if (inspection.end_date_time) {
                            const endDateTime = new Date(inspection.end_date_time);
                            document.getElementById('edit_end_date').value = endDateTime.toISOString().split('T')[0];
                            document.getElementById('edit_end_time').value = endDateTime.toTimeString().slice(0, 5);
                            document.getElementById('edit_end_date_group').style.display = 'block';
                            document.getElementById('edit_end_time_group').style.display = 'block';
                        } else {
                            document.getElementById('edit_end_date_group').style.display = 'none';
                            document.getElementById('edit_end_time_group').style.display = 'none';
                        }
                        
                        // Update created/updated info
                        document.getElementById('edit_created_info').textContent = 
                            inspection.created_at ? new Date(inspection.created_at).toLocaleString() : '-';
                        document.getElementById('edit_updated_info').textContent = 
                            inspection.updated_at ? new Date(inspection.updated_at).toLocaleString() : '-';
                        
                        // Update form action URL
                        document.getElementById('editInspectionForm').action = `/block-inspections/${inspection.id}`;
                    } else {
                        showAlert('error', 'Could not fetch inspection details: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error fetching inspection details:', error);
                    showAlert('error', 'Error fetching inspection details: ' + error.message);
                })
                .finally(() => {
                    // Restore button state
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
            });
        });
    }
    
    // Attach event listeners on page load
    attachInspectionEventListeners();

    // Clear messages when edit inspection modal is shown/hidden
    const editInspectionModal = document.getElementById('editInspectionModal');
    if (editInspectionModal) {
        editInspectionModal.addEventListener('show.bs.modal', function() {
            const editMsgEl = document.getElementById('editInspectionMessage');
            if (editMsgEl) {
                editMsgEl.innerHTML = '';
            }
            // Reset form validation states
            const form = document.getElementById('editInspectionForm');
            if (form) {
                form.classList.remove('was-validated');
                const invalidElements = form.querySelectorAll('.is-invalid');
                invalidElements.forEach(el => el.classList.remove('is-invalid'));
            }
        });
        
        editInspectionModal.addEventListener('hide.bs.modal', function() {
            const editMsgEl = document.getElementById('editInspectionMessage');
            if (editMsgEl) {
                editMsgEl.innerHTML = '';
            }
            // Reset form
            const form = document.getElementById('editInspectionForm');
            if (form) {
                form.reset();
                form.classList.remove('was-validated');
                const invalidElements = form.querySelectorAll('.is-invalid');
                invalidElements.forEach(el => el.classList.remove('is-invalid'));
            }
        });
    }
    
    // Add form validation
    const editForm = document.getElementById('editInspectionForm');
    if (editForm) {
        editForm.addEventListener('submit', function(event) {
            if (!editForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            editForm.classList.add('was-validated');
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


// Function to refresh inspections table
function refreshInspectionsTable() {
    if (typeof inspectionsDT !== 'undefined' && inspectionsDT) {
        // Get the current block ID
        const blockId = document.querySelector('input[name="block_id"]').value;
        
        // Fetch fresh data
        fetch(`/api/blocks/${blockId}/inspections`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear existing data
                    inspectionsDT.clear();
                    
                    // Add new data
                    data.data.forEach(function(inspection) {
                        const leadInspector = inspection.inspection_teams?.find(team => team.is_lead) || 
                                           inspection.inspectionTeams?.find(team => team.is_lead);
                        const inspectorName = leadInspector?.user?.name || inspection.creator?.name || 'N/A';
                        
                        const scheduledDate = inspection.scheduled_date_time ? 
                            new Date(inspection.scheduled_date_time).toLocaleDateString('en-US', { 
                                year: 'numeric', month: 'short', day: '2-digit' 
                            }) : 'N/A';
                        
                        const statusBadge = getStatusBadge(inspection.job_status_id);
                        
                        // Get scheduled date and time for data attributes
                        const scheduledDateTime = inspection.scheduled_date_time ? new Date(inspection.scheduled_date_time) : null;
                        const scheduledDateAttr = scheduledDateTime ? scheduledDateTime.toISOString().split('T')[0] : '';
                        const scheduledTimeAttr = scheduledDateTime ? scheduledDateTime.toTimeString().slice(0, 5) : '';
                        
                        inspectionsDT.row.add([
                            inspection.ref_no || 'N/A',
                            scheduledDate,
                            inspectorName,
                            statusBadge,
                            inspection.notes ? (inspection.notes.length > 50 ? inspection.notes.substring(0, 50) + '...' : inspection.notes) : 'N/A',
                            '<button class="btn btn-sm btn-outline-primary me-1 edit-inspection" ' +
                                'data-inspection-id="' + inspection.id + '" ' +
                                'data-user-id="' + (leadInspector?.user_id || '') + '" ' +
                                'data-date="' + scheduledDateAttr + '" ' +
                                'data-time="' + scheduledTimeAttr + '" ' +
                                'data-notes="' + (inspection.notes || '').replace(/"/g, '&quot;') + '">' +
                                '<i class="ph-pencil"></i> Edit' +
                            '</button> ' +
                            '<button class="btn btn-sm btn-outline-danger" onclick="deleteInspection(' + inspection.id + ')">' +
                                '<i class="ph-trash"></i> Delete' +
                            '</button>'
                        ]);
                    });
                    
                    // Redraw the table
                    inspectionsDT.draw();
                    
                    // Reattach event listeners for new edit buttons
                    attachInspectionEventListeners();
                }
            })
            .catch(error => {
                console.error('Error refreshing inspections table:', error);
            });
    }
}

// Function to get status badge HTML
function getStatusBadge(statusId) {
    const statuses = {
        1: '<span class="badge bg-info">Scheduled</span>',
        2: '<span class="badge bg-warning">In Progress</span>',
        3: '<span class="badge bg-success">Completed</span>',
        4: '<span class="badge bg-danger">Cancelled</span>',
        5: '<span class="badge bg-secondary">On Hold</span>',
        6: '<span class="badge bg-primary">Rescheduled</span>'
    };
    return statuses[statusId] || '<span class="badge bg-secondary">Unknown</span>';
}

// Function to delete inspection
function deleteInspection(inspectionId) {
    if (confirm('Are you sure you want to delete this inspection? This action cannot be undone.')) {
        // Show loading state
        const deleteBtn = document.querySelector(`button[onclick="deleteInspection(${inspectionId})"]`);
        if (deleteBtn) {
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="ph-spinner ph-spin me-1"></i>Deleting...';
            deleteBtn.disabled = true;
        }
        
        fetch(`/block-inspections/${inspectionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message || 'Inspection deleted successfully!');
                // Refresh the table
                refreshInspectionsTable();
            } else {
                showAlert('error', data.message || 'Failed to delete inspection.');
            }
        })
        .catch(error => {
            console.error('Error deleting inspection:', error);
            showAlert('error', 'An error occurred while deleting the inspection.');
        })
        .finally(() => {
            // Restore button state
            if (deleteBtn) {
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
            }
        });
    }
}
</script>
