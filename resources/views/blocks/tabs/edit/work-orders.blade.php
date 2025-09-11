<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Work Orders</h6>
            <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#createWorkOrderModal">
                <i class="ph-plus align-bottom me-1"></i> Create Work Order
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="workOrdersTable">
                <thead class="table-light">
                    <tr>
                        <th>Work Order #</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blockWorkOrders ?? [] as $workOrder)
                        <tr>
                            <td>#{{ $workOrder->id }}</td>
                            <td>{{ $workOrder->issue ?? 'N/A' }}</td>
                            <td>
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
                            <td>
                                @if($workOrder->status == 1)
                                    <span class="badge bg-warning">Open</span>
                                @elseif($workOrder->status == 2)
                                    <span class="badge bg-info">In Progress</span>
                                @else
                                    <span class="badge bg-success">Completed</span>
                                @endif
                            </td>
                            <td>{{ $workOrder->created_at ? $workOrder->created_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">View</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">                    
                                <div class="text-muted">
                                    <i class="ph-wrench font-size-24 mb-2"></i>
                                    <p>No work orders created for this block.</p>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createWorkOrderModal">
                                        <i class="ph-plus me-1"></i> Create First Work Order
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Work Order Modal -->
<div class="modal fade" id="createWorkOrderModal" tabindex="-1" aria-labelledby="createWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 95vw;">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="createWorkOrderModalLabel">Create Work Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createWorkOrderForm" method="POST" action="{{ route('block-work-orders.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div id="createWorkOrderMessage" class="alert d-none" role="alert"></div>
                    
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            
                            <div class="mb-3">
                                <label for="block_issue_id" class="form-label">Related Issue <span class="text-danger">*</span></label>
                                <select class="form-select" id="block_issue_id" name="block_issue_id" required>
                                    <option value="">Select Issue</option>
                                    @foreach($block->issues as $issue)
                                        <option value="{{ $issue->id }}">
                                            {{ $issue->ref_no }} - {{ $issue->issue }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ref_no" class="form-label">Reference Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ref_no" name="ref_no" required>
                            </div>

                            <div class="mb-3">
                                <label for="priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select" id="priority_id" name="priority_id" required>
                                    <option value="">Select Priority</option>
                                    <option value="1">Low</option>
                                    <option value="2" selected>Normal</option>
                                    <option value="3">High</option>
                                    <option value="4">Urgent</option>
                                    <option value="5">Critical</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="1" selected>Pending</option>
                                    <option value="2">In Progress</option>
                                    <option value="3">Completed</option>
                                    <option value="4">Cancelled</option>
                                    <option value="5">On Hold</option>
                                </select>
                            </div>
                        </div>

                        <!-- Location & Assignment -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Location & Assignment</h5>
                            
                            <div class="mb-3">
                                <label for="block_unit_id" class="form-label">Block Unit</label>
                                <select class="form-select" id="block_unit_id" name="block_unit_id">
                                    <option value="">Select Unit (Optional)</option>
                                    @foreach($block->units as $unit)
                                        <option value="{{ $unit->id }}">
                                            {{ $unit->unit_name }} - {{ $unit->unit_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="block_building_id" class="form-label">Block Building</label>
                                <select class="form-select" id="block_building_id" name="block_building_id">
                                    <option value="">Select Building (Optional)</option>
                                    @foreach($block->buildings as $building)
                                        <option value="{{ $building->id }}">
                                            {{ $building->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="contractor_id" class="form-label">Assign to Contractor Admin</label>
                                <select class="form-select" id="contractor_id" name="contractor_id">
                                    <option value="">Select Contractor Admin (Optional)</option>
                                    @foreach($contractors as $contractor)
                                        @if($contractor->userType && $contractor->userType->name === 'Contractor Admin')
                                            <option value="{{ $contractor->id }}">
                                                {{ $contractor->name }} ({{ $contractor->email }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="repair_category_id" class="form-label">Repair Category ID</label>
                                <input type="number" class="form-control" id="repair_category_id" name="repair_category_id">
                            </div>
                        </div>
                    </div>

                    <!-- Issue Description -->
                    <div class="mb-3">
                        <label for="issue" class="form-label">Issue Description</label>
                        <textarea class="form-control" id="issue" name="issue" rows="3" placeholder="Describe the work order issue..."></textarea>
                    </div>

                    <div class="row">
                        <!-- Schedule Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Schedule</h5>
                            
                            <div class="mb-3">
                                <label for="issued_date_time" class="form-label">Issued Date & Time</label>
                                <input type="datetime-local" class="form-control" id="issued_date_time" name="issued_date_time">
                            </div>

                            <div class="mb-3">
                                <label for="preferred_start_date_time" class="form-label">Preferred Start Date & Time</label>
                                <input type="datetime-local" class="form-control" id="preferred_start_date_time" name="preferred_start_date_time">
                            </div>

                            <div class="mb-3">
                                <label for="preferred_end_date_time" class="form-label">Preferred End Date & Time</label>
                                <input type="datetime-local" class="form-control" id="preferred_end_date_time" name="preferred_end_date_time">
                            </div>

                            <div class="mb-3">
                                <label for="deadline_date" class="form-label">Deadline Date</label>
                                <input type="date" class="form-control" id="deadline_date" name="deadline_date">
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Contact Information</h5>
                            
                            <div class="mb-3">
                                <label for="contact_name" class="form-label">Contact Name</label>
                                <input type="text" class="form-control" id="contact_name" name="contact_name">
                            </div>

                            <div class="mb-3">
                                <label for="contact_mobile" class="form-label">Contact Mobile</label>
                                <input type="text" class="form-control" id="contact_mobile" name="contact_mobile">
                            </div>

                            <div class="mb-3">
                                <label for="contact_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email">
                            </div>

                            <div class="mb-3">
                                <label for="note_for_access" class="form-label">Note for Access</label>
                                <input type="text" class="form-control" id="note_for_access" name="note_for_access" placeholder="Access instructions for contractor...">
                            </div>
                        </div>
                    </div>

                    <!-- Comments -->
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comments</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="Additional comments or instructions..."></textarea>
                    </div>

                    <!-- File Uploads -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="mb-3">PDF Document</h5>
                                <label for="pdf" class="form-label">Upload PDF</label>
                                <input type="file" class="form-control" id="pdf" name="pdf" accept=".pdf">
                                <div class="form-text">Maximum file size: 10MB</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="mb-3">Images</h5>
                                <label for="images" class="form-label">Upload Images</label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                                <div class="form-text">You can select multiple images. Maximum file size: 2MB each.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Create Work Order
                    </button>
                </div>
            </form>
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

<script>
$(document).ready(function() {
    // Initialize DataTable with better error handling
    try {
        // Check if table exists and has proper structure
        const table = $('#workOrdersTable');
        if (table.length && table.find('thead tr th').length === 6) {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#workOrdersTable')) {
                table.DataTable().destroy();
            }
            
            // Initialize DataTable
            table.DataTable({
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
                    infoEmpty: "No work orders found",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    zeroRecords: "No work orders found",
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
        } else {
            console.warn('Work orders table not found or has incorrect structure');
        }
    } catch (error) {
        console.error('Error initializing DataTable:', error);
    }

    // Work Order Creation Form Handling
    const createWorkOrderForm = document.getElementById('createWorkOrderForm');
    const createWorkOrderModal = document.getElementById('createWorkOrderModal');

    if (createWorkOrderForm) {
        createWorkOrderForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = createWorkOrderForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ph-spinner ph-spin me-2"></i>Creating...';
            submitBtn.disabled = true;

            // Get form data
            const formData = new FormData(createWorkOrderForm);

            // Submit form via AJAX
            fetch(createWorkOrderForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showWorkOrderAlert('success', 'Work order created successfully!');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(createWorkOrderModal);
                    modal.hide();

                    // Reset form
                    createWorkOrderForm.reset();

                    // Refresh the work orders table
                    setTimeout(() => {
                        // Reload the page to show the new work order
                        location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    showWorkOrderAlert('error', data.message || 'Failed to create work order. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showWorkOrderAlert('error', 'An error occurred. Please try again.');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Function to show alerts
    function showWorkOrderAlert(type, message) {
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

    // Auto-populate issue description when issue is selected
    const issueSelect = document.getElementById('block_issue_id');
    const issueDescription = document.getElementById('issue');

    if (issueSelect && issueDescription) {
        issueSelect.addEventListener('change', function() {
            if (this.value) {
                // Get the selected option text and extract the issue description
                const selectedOption = this.options[this.selectedIndex];
                const optionText = selectedOption.text;
                const issueText = optionText.split(' - ')[1]; // Get part after the dash
                if (issueText) {
                    issueDescription.value = issueText;
                }
            } else {
                issueDescription.value = '';
            }
        });
    }

    // Set current date/time for issued date
    const issuedDateTime = document.getElementById('issued_date_time');
    if (issuedDateTime) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        issuedDateTime.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
});
</script>
