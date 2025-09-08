<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Inspection History</h6>
            <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#addInspectionModal">
                <i class="ph-plus align-bottom me-1"></i> Schedule Inspection
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="inspectionsTable">
                <thead class="table-light">
                    <tr>
                        <th>Inspection Date</th>
                        <th>Reference</th>
                        <th>Inspector</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($blockInspections) && $blockInspections->count() > 0)
                        @foreach($blockInspections as $inspection)
                            <tr>
                                <td>{{ $inspection->scheduled_date_time ? \Carbon\Carbon::parse($inspection->scheduled_date_time)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $inspection->ref_no ?? 'N/A' }}</td>
                                <td>{{ $inspection->creator->name ?? 'N/A' }}</td>
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
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editInspection({{ $inspection->id }})">
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
            <form id="editInspectionForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="inspection_id" id="edit_inspection_id">
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- User Selection -->
                        <div class="col-12 mb-3">
                            <label for="edit_user_id" class="form-label">User <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_user_id" name="user_id" required>
                                <option value="">Select User</option>
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
                        
                        <!-- Notes -->
                        <div class="col-12 mb-3">
                            <label for="edit_notes" class="form-label">Notes <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="4" placeholder="Enter inspection notes..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Update
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
    $('#inspectionsTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        ],
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
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
                _token: formData.get('_token'),
                _method: formData.get('_method')
            };
            
            // Submit form via AJAX (you'll need to create this route)
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
                    showAlert('success', 'Inspection updated successfully!');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editInspectionModal'));
                    modal.hide();
                    
                    // Reset form
                    editInspectionForm.reset();
                    
                    // DataTable will refresh automatically when modal closes
                } else {
                    showAlert('error', data.message || 'Failed to update inspection.');
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

// Function to edit inspection
function editInspection(inspectionId) {
    console.log('editInspection called with ID:', inspectionId);
    
    // Find the inspection data from the table row
    const row = event.target.closest('tr');
    console.log('Found row:', row);
    
    if (!row) {
        console.error('Could not find table row');
        return;
    }
    
    // Map the table columns correctly:
    // Column 0: Inspection Date (e.g., "Dec 15, 2024")
    // Column 1: Reference
    // Column 2: Inspector
    // Column 3: Status
    // Column 4: Actions (buttons)
    
    const inspectionDate = row.cells[0].textContent.trim();
    const reference = row.cells[1].textContent.trim();
    const inspector = row.cells[2].textContent.trim();
    const status = row.cells[3].textContent.trim();
    
    console.log('Raw cell data:', {
        cell0: row.cells[0].textContent,
        cell1: row.cells[1].textContent,
        cell2: row.cells[2].textContent,
        cell3: row.cells[3].textContent,
        cell4: row.cells[4].textContent
    });
    
    console.log('Extracted data:', { 
        inspectionId, 
        inspectionDate, 
        reference, 
        inspector, 
        status,
        rowCells: row.cells.length 
    });
    
    // Parse the date and time from the inspection date cell
    let scheduledDate = '';
    let scheduledTime = '';
    
    if (inspectionDate !== 'N/A') {
        try {
            // Handle "Sep 01, 2025" format - use a more reliable date parsing
            const months = {
                'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
                'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
                'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
            };
            
            // Parse "Sep 01, 2025" format manually
            const parts = inspectionDate.split(' ');
            if (parts.length === 3) {
                const month = months[parts[0]];
                const day = parts[1].replace(',', '').padStart(2, '0');
                const year = parts[2];
                
                if (month && day && year) {
                    scheduledDate = `${year}-${month}-${day}`;
                    scheduledTime = '09:00'; // Default time
                    console.log('Manually parsed date successfully:', { 
                        original: inspectionDate, 
                        scheduledDate, 
                        scheduledTime,
                        month, day, year 
                    });
                } else {
                    console.log('Could not parse date parts:', { month, day, year });
                }
            } else {
                console.log('Date format not recognized:', inspectionDate);
            }
        } catch (e) {
            console.log('Error parsing date:', inspectionDate, e);
        }
    }
    
    // Populate the edit modal fields
    try {
        const inspectionIdField = document.getElementById('edit_inspection_id');
        const userSelect = document.getElementById('edit_user_id');
        const dateField = document.getElementById('edit_scheduled_date');
        const timeField = document.getElementById('edit_scheduled_time');
        const notesField = document.getElementById('edit_notes');
        
        console.log('Found modal fields:', {
            inspectionIdField: !!inspectionIdField,
            userSelect: !!userSelect,
            dateField: !!dateField,
            timeField: !!timeField,
            notesField: !!notesField
        });
        
        console.log('Values to set:', {
            inspectionId,
            scheduledDate,
            scheduledTime,
            notes: `Inspection for ${reference} - ${inspector}`
        });
        
        if (inspectionIdField) {
            inspectionIdField.value = inspectionId;
            console.log('Set inspection ID to:', inspectionId);
        }
        if (dateField) {
            dateField.value = scheduledDate;
            console.log('Set date to:', scheduledDate);
        }
        if (timeField) {
            timeField.value = scheduledTime;
            console.log('Set time to:', scheduledTime);
        }
        if (notesField) {
            notesField.value = `Inspection for ${reference} - ${inspector}`;
            console.log('Set notes to:', `Inspection for ${reference} - ${inspector}`);
        }
        
        // Try to find and select the user based on inspector name
        if (userSelect) {
            console.log('Looking for user:', inspector);
            console.log('Available options:', Array.from(userSelect.options).map(opt => ({ value: opt.value, text: opt.text })));
            
            for (let option of userSelect.options) {
                if (option.text.includes(inspector) || option.text === inspector) {
                    userSelect.value = option.value;
                    console.log('Selected user:', option.text, 'with value:', option.value);
                    break;
                }
            }
        }
        
        console.log('Modal fields populated successfully');
        
        // Show the edit modal using jQuery (same as working modals)
        $('#editInspectionModal').modal('show');
        console.log('Edit modal shown');
        
        // Populate fields after modal is shown (more reliable)
        $('#editInspectionModal').on('shown.bs.modal', function() {
            console.log('Modal fully shown, populating fields...');
            
            // Use jQuery to set values (more reliable)
            $('#edit_inspection_id').val(inspectionId);
            $('#edit_scheduled_date').val(scheduledDate);
            $('#edit_scheduled_time').val(scheduledTime);
            $('#edit_notes').val(`Inspection for ${reference} - ${inspector}`);
            
            console.log('Set values using jQuery:');
            console.log('Inspection ID:', inspectionId);
            console.log('Date:', scheduledDate);
            console.log('Time:', scheduledTime);
            console.log('Notes:', `Inspection for ${reference} - ${inspector}`);
            
            // Try to find and select the user based on inspector name
            const userSelect = document.getElementById('edit_user_id');
            if (userSelect) {
                console.log('Looking for user:', inspector);
                console.log('Available options:', Array.from(userSelect.options).map(opt => ({ value: opt.value, text: opt.text })));
                
                for (let option of userSelect.options) {
                    if (option.text.includes(inspector) || option.text === inspector) {
                        userSelect.value = option.value;
                        console.log('Selected user:', option.text, 'with value:', option.value);
                        break;
                    }
                }
            }
            
            // Force a refresh of the form fields
            $('#editInspectionModal input, #editInspectionModal select, #editInspectionModal textarea').each(function() {
                console.log('Field:', this.id, 'Value:', $(this).val());
            });
            
            // Additional debugging - check if fields are actually set
            setTimeout(() => {
                console.log('=== FINAL FIELD VALUES ===');
                console.log('Inspection ID:', $('#edit_inspection_id').val());
                console.log('Date:', $('#edit_scheduled_date').val());
                console.log('Time:', $('#edit_scheduled_time').val());
                console.log('Notes:', $('#edit_notes').val());
                console.log('User:', $('#edit_user_id').val());
                console.log('=== END FIELD VALUES ===');
            }, 100);
            
            console.log('Fields populated after modal show');
        });
        
    } catch (error) {
        console.error('Error populating modal fields:', error);
    }
}

// Function to delete inspection
function deleteInspection(inspectionId) {
    if (confirm('Are you sure you want to delete this inspection?')) {
        // You can implement delete functionality here
        // For now, show an alert
        showAlert('info', `Delete inspection with ID: ${inspectionId}`);
        
        // TODO: Implement delete via AJAX
        // Example: 
        // fetch(`/inspections/${inspectionId}`, {
        //     method: 'DELETE',
        //     headers: {
        //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        //     }
        // })
        // .then(response => response.json())
        // .then(data => {
        //     if (data.success) {
        //         showAlert('success', 'Inspection deleted successfully!');
        //         setTimeout(() => window.location.reload(), 1500);
        //     }
        // });
    }
}
</script>
