<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Block Issues</h6>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createIssueModal">
                <i class="ph-plus align-bottom me-1"></i> Report Issue
            </button>
        </div>
        
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
                                    @if($issue->priority_id == 1)
                                        <span class="badge bg-success">Low</span>
                                    @elseif($issue->priority_id == 2)
                                        <span class="badge bg-info">Normal</span>
                                    @elseif($issue->priority_id == 3)
                                        <span class="badge bg-warning">High</span>
                                    @elseif($issue->priority_id == 4)
                                        <span class="badge bg-danger">Urgent</span>
                                    @elseif($issue->priority_id == 5)
                                        <span class="badge bg-dark">Critical</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if($issue->issue_status_id == 1)
                                        <span class="badge bg-warning">Open</span>
                                    @elseif($issue->issue_status_id == 2)
                                        <span class="badge bg-info">In Progress</span>
                                    @elseif($issue->issue_status_id == 3)
                                        <span class="badge bg-success">Resolved</span>
                                    @elseif($issue->issue_status_id == 4)
                                        <span class="badge bg-secondary">Closed</span>
                                    @elseif($issue->issue_status_id == 5)
                                        <span class="badge bg-danger">On Hold</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $issue->created_at ? $issue->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">View</button>
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createIssueModal">Report First Issue</button>
            </div>
        @endif
    </div>
</div>

<!-- Create Issue Modal -->
<div class="modal fade" id="createIssueModal" tabindex="-1" aria-labelledby="createIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createIssueModalLabel">Create Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createIssueForm" method="POST" action="{{ route('block-issues.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- Full Width Form Fields -->
                        <div class="col-12">
                            <div class="row">
                                <!-- Row 1: Contact Method, Unit Selection, Issue Code -->
                                <div class="col-md-4 mb-3">
                                    <label for="contact_method_id" class="form-label">Contact Method <span class="text-danger">*</span></label>
                                    <select class="form-select" id="contact_method_id" name="contact_method_id" required>
                                        <option value="">Select Contact Method</option>
                                        @foreach($contactMethods as $contactMethod)
                                            <option value="{{ $contactMethod->id }}">{{ $contactMethod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="block_unit_id" class="form-label">Unit Selection <span class="text-danger">*</span></label>
                                    <select class="form-select" id="block_unit_id" name="block_unit_id" required>
                                        <option value="">Select Unit</option>
                                        @foreach($block->units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->unit_code }} - {{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="assigned_to" class="form-label">Assigned To <span class="text-danger">*</span></label>
                                    <select class="form-select" id="assigned_to" name="assigned_to" required>
                                        <option value="">Select Property Manager</option>
                                        @foreach($users as $user)
                                            @if($user->userType && $user->userType->name === 'Property manager')
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Row 2: Issue Type, Priority, Issue Title -->
                                <div class="col-md-4 mb-3">
                                    <label for="issue_type" class="form-label">Issue Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="issue_type" name="issue_type" required>
                                        <option value="">Select Issue Type</option>
                                        <option value="plumbing">Plumbing</option>
                                        <option value="electrical">Electrical</option>
                                        <option value="hvac">HVAC</option>
                                        <option value="structural">Structural</option>
                                        <option value="security">Security</option>
                                        <option value="fire_safety">Fire Safety</option>
                                        <option value="water_leakage">Water Leakage</option>
                                        <option value="noise">Noise Complaint</option>
                                        <option value="parking">Parking Issue</option>
                                        <option value="landscaping">Landscaping</option>
                                        <option value="elevator">Elevator</option>
                                        <option value="internet">Internet</option>
                                        <option value="trash">Trash Collection</option>
                                        <option value="lighting">Lighting</option>
                                        <option value="access_control">Access Control</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
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
                                <div class="col-md-4 mb-3">
                                    <label for="issue" class="form-label">Issue Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="issue" name="issue" required>
                                </div>
                                

                                
                                <!-- Row 3: Dynamic Contact Details based on Contact Method -->
                                <div class="col-md-6 mb-3" id="contact_details_container">
                                    <label for="contact_details" class="form-label">Contact Details <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="contact_details" name="contact_details" placeholder="Enter contact details..." required>
                                    <div class="form-text">Please provide relevant contact information</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="fault_details" class="form-label">Issue Details</label>
                                    <textarea class="form-control" id="fault_details" name="fault_details" rows="2" placeholder="Describe the issue in detail..."></textarea>
                                </div>
                                
                                <!-- Row 4: Default Contact Details -->
                                <div class="col-12 mb-2">
                                    <label for="default_contact_details" class="form-label">Default Contact Details</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="use_default_contact" checked>
                                        <label class="form-check-label" for="use_default_contact">
                                            Use default contact details
                                        </label>
                                    </div>
                                    <textarea class="form-control" id="default_contact_details" name="default_contact_details" rows="2" placeholder="Enter default contact information..." readonly></textarea>
                                </div>
                                
                                <!-- Row 5: File Upload -->
                                <div class="col-12 mb-3">
                                    <label for="images" class="form-label">Upload Images</label>
                                    <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                                    <small class="form-text text-muted">You can select multiple images. Maximum file size: 2MB each.</small>
                                </div>
                                
                                <!-- Row 6: Open Issues in Same Unit -->
                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                                            <h6 class="mb-0">Open Issues in Same Unit</h6>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="refreshIssuesBtn">
                                                <i class="ph-arrow-clockwise"></i> Refresh
                                            </button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive" style="max-height: 200px;">
                                                <table class="table table-sm table-hover mb-0" id="openIssuesTable">
                                                    <thead class="table-light sticky-top">
                                                        <tr>
                                                            <th>Ref #</th>
                                                            <th>Issue</th>
                                                            <th>Type</th>
                                                            <th>Priority</th>
                                                            <th>Reported</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="openIssuesTableBody">
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted py-3">
                                                                <i class="ph-info-circle"></i> Select a unit to view open issues
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Issue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #contact_details_container {
        transition: all 0.3s ease;
    }
    
    #contact_details_container input:focus,
    #contact_details_container textarea:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    #contact_details_container .form-text {
        font-size: 0.875em;
        margin-top: 0.25rem;
    }
    
    .is-valid {
        border-color: #198754 !important;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25) !important;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
    }

/* Compact modal styling */
#createIssueModal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

#createIssueModal .form-label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

#createIssueModal .form-control,
#createIssueModal .form-select {
    font-size: 0.875rem;
    padding: 0.375rem 0.5rem;
}



/* Custom styling for autocomplete dropdowns to match Bootstrap form design */
.autoComplete_wrapper {
    position: relative;
    display: block;
    width: 100%;
}

.autoComplete_wrapper > input {
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.autoComplete_wrapper > input:focus {
    color: #495057;
    background-color: #fff;
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.autoComplete_wrapper > ul {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    margin: 0;
    padding: 0;
    list-style: none;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.25rem 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    max-height: 200px;
    overflow-y: auto;
}

.autoComplete_wrapper > ul > li {
    padding: 0.375rem 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
}

.autoComplete_wrapper > ul > li:last-child {
    border-bottom: none;
}

.autoComplete_wrapper > ul > li:hover,
.autoComplete_wrapper > ul > li[aria-selected="true"] {
    background-color: #e9ecef;
    color: #495057;
}

.autoComplete_wrapper > ul > li mark {
    background-color: #fff3cd;
    color: #856404;
    padding: 0;
    font-weight: bold;
}

/* Hide the default autocomplete styling */
.autoComplete_wrapper > ul::-webkit-scrollbar {
    width: 6px;
}

.autoComplete_wrapper > ul::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.autoComplete_wrapper > ul::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.autoComplete_wrapper > ul::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Ensure the wrapper doesn't interfere with form layout */
.autoComplete_wrapper {
    margin-bottom: 0;
}

/* Match Bootstrap form-control focus state */
.autoComplete_wrapper > input:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .autoComplete_wrapper > ul {
        max-height: 150px;
    }
}

/* Hide the red lens/search icon in autocomplete */
.autoComplete_wrapper::before,
.autoComplete_wrapper::after {
    display: none !important;
}

.autoComplete_wrapper > input::-webkit-search-cancel-button,
.autoComplete_wrapper > input::-webkit-search-decoration,
.autoComplete_wrapper > input::-webkit-search-results-button,
.autoComplete_wrapper > input::-webkit-search-results-decoration {
    display: none !important;
}

/* Hide any search icons that might appear */
.autoComplete_wrapper .search-icon,
.autoComplete_wrapper .search-lens,
.autoComplete_wrapper .search-button {
    display: none !important;
}

/* Additional rules to hide lens/search icons */
.autoComplete_wrapper input[type="search"]::-webkit-search-cancel-button,
.autoComplete_wrapper input[type="search"]::-webkit-search-decoration,
.autoComplete_wrapper input[type="search"]::-webkit-search-results-button,
.autoComplete_wrapper input[type="search"]::-webkit-search-results-decoration {
    display: none !important;
}

/* Hide any background images that might be search icons */
.autoComplete_wrapper > input {
    background-image: none !important;
    background-repeat: no-repeat !important;
    background-position: right center !important;
}

/* Hide any pseudo-elements that might contain search icons */
.autoComplete_wrapper > input::before,
.autoComplete_wrapper > input::after {
    display: none !important;
}

/* Ensure no search-related icons appear */
.autoComplete_wrapper *[class*="search"],
.autoComplete_wrapper *[class*="lens"],
.autoComplete_wrapper *[class*="magnify"],
.autoComplete_wrapper *[class*="icon"] {
    display: none !important;
}

/* Open Issues Table Styling */
#openIssuesTable {
    font-size: 0.875rem;
}

#openIssuesTable th {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 0.5rem;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

#openIssuesTable td {
    padding: 0.5rem;
    vertical-align: middle;
}

#openIssuesTable tbody tr:hover {
    background-color: #f8f9fa;
}

#openIssuesTable .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

#refreshIssuesBtn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

#refreshIssuesBtn:hover {
    transform: rotate(180deg);
    transition: transform 0.3s ease;
}

/* Sticky header for table */
#openIssuesTable thead.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const createIssueForm = document.getElementById('createIssueForm');
    const createIssueModal = document.getElementById('createIssueModal');
    

    
    if (createIssueForm) {
        createIssueForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = createIssueForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
            submitBtn.disabled = true;
            
            // Get form data
            const formData = new FormData(createIssueForm);
            

            
            // Submit form via AJAX
            fetch(createIssueForm.action, {
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
                    showAlert('success', 'Issue created successfully!');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(createIssueModal);
                    modal.hide();
                    
                    // Reset form
                    createIssueForm.reset();
                    

                    
                    // Reload page to show new issue
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    showAlert('error', data.message || 'Failed to create issue. Please try again.');
                }
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    // Validation errors
                    error.response.json().then(data => {
                        if (data.errors) {
                            // Clear previous error messages
                            createIssueForm.querySelectorAll('.is-invalid').forEach(el => {
                                el.classList.remove('is-invalid');
                            });
                            createIssueForm.querySelectorAll('.invalid-feedback').forEach(el => {
                                el.remove();
                            });
                            
                            // Show validation errors
                            Object.keys(data.errors).forEach(field => {
                                const input = createIssueForm.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'invalid-feedback';
                                    errorDiv.textContent = data.errors[field][0];
                                    input.parentNode.appendChild(errorDiv);
                                }
                            });
                        }
                    });
                } else {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred. Please try again.');
                }
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
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
    
    // Initialize autocomplete when modal opens
    createIssueModal.addEventListener('show.bs.modal', function() {
        console.log('Modal opened - initializing autocomplete...');
        
        // Initialize autocomplete for property manager assignment
        const assignedToInput = document.getElementById('assigned_to');
        const assignedToHiddenInput = document.getElementById('assigned_to_hidden');
        
        if (assignedToInput) {
            let propertyManagersData = [];
            let autoCompletePropertyManagers;
            
            // Function to initialize property manager autocomplete
            function initPropertyManagerAutocomplete(data) {
                propertyManagersData = data;
                
                autoCompletePropertyManagers = new autoComplete({
                    selector: "#assigned_to",
                    placeHolder: "Search property managers...",
                    data: {
                        src: data.map(manager => manager.text),
                        cache: true
                    },
                    resultsList: {
                        element: function element(list, data) {
                            if (!data.results.length) {
                                var message = document.createElement("div");
                                message.setAttribute("class", "no_result");
                                message.innerHTML = "<span>Found No Results for \"" + data.query + "\"</span>";
                                list.prepend(message);
                            }
                        },
                        noResults: true
                    },
                    resultItem: {
                        highlight: true
                    },
                    events: {
                        input: {
                            selection: function selection(event) {
                                const selection = event.detail.selection.value;
                                assignedToInput.value = selection;
                                
                                const selectedManager = propertyManagersData.find(manager => manager.text === selection);
                                if (selectedManager) {
                                    assignedToHiddenInput.value = selectedManager.id;
                                }
                            }
                        }
                    }
                });
            }
            
            // Load property managers data and initialize
            console.log('Loading property managers data...');
            fetch(`{{ route('api.property-managers-autocomplete') }}?query=`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Property managers data loaded:', data);
                    initPropertyManagerAutocomplete(data);
                })
                .catch(error => {
                    console.error('Error loading property managers:', error);
                    initPropertyManagerAutocomplete([]);
                });
        }
        
        // Initialize autocomplete for unit selection
        const unitInput = document.getElementById('block_unit_id');
        const unitHiddenInput = document.getElementById('block_unit_id_hidden');
        
        if (unitInput) {
            let unitsData = [];
            let autoCompleteUnits;
            
            // Function to initialize autocomplete
            function initUnitAutocomplete(data) {
                unitsData = data;
                
                autoCompleteUnits = new autoComplete({
                    selector: "#block_unit_id",
                    placeHolder: "Search for units...",
                    data: {
                        src: data.map(unit => unit.text),
                        cache: true
                    },
                    resultsList: {
                        element: function element(list, data) {
                            if (!data.results.length) {
                                var message = document.createElement("div");
                                message.setAttribute("class", "no_result");
                                message.innerHTML = "<span>Found No Results for \"" + data.query + "\"</span>";
                                list.prepend(message);
                            }
                        },
                        noResults: true
                    },
                    resultItem: {
                        highlight: true
                    },
                    events: {
                        input: {
                            selection: function selection(event) {
                                const selection = event.detail.selection.value;
                                unitInput.value = selection;
                                
                                // Find the unit ID and set it in hidden input
                                const selectedUnit = unitsData.find(unit => unit.text === selection);
                                if (selectedUnit) {
                                    unitHiddenInput.value = selectedUnit.id;
                                    
                                    // Auto-fill default contact details only if checkbox is checked
                                    const defaultContactDetailsField = document.getElementById('default_contact_details');
                                    const useDefaultContactCheckbox = document.getElementById('use_default_contact');
                                    if (defaultContactDetailsField && useDefaultContactCheckbox && useDefaultContactCheckbox.checked) {
                                        defaultContactDetailsField.value = selectedUnit.contact_details;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Load initial data and initialize
            console.log('Loading units data...');
            fetch(`{{ route('api.blocks.units-autocomplete', $block) }}?query=`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Units data loaded:', data);
                    initUnitAutocomplete(data);
                })
                .catch(error => {
                    console.error('Error loading initial units:', error);
                    initUnitAutocomplete([]);
                });
        }
        
        // Initialize autocomplete for contact method selection
        const contactMethodInput = document.getElementById('contact_method_id');
        const contactMethodHiddenInput = document.getElementById('contact_method_id_hidden');
        
        if (contactMethodInput) {
            let contactMethodsData = [];
            let autoCompleteContactMethods;
            
            // Function to initialize autocomplete
            function initContactMethodAutocomplete(data) {
                contactMethodsData = data;
                
                // Set default value (Email)
                const emailMethod = data.find(method => method.text === 'Email');
                if (emailMethod) {
                    contactMethodInput.value = 'Email';
                    contactMethodHiddenInput.value = emailMethod.id;
                    
                    // Initialize contact details field for Email
                    updateContactDetailsField('Email');
                }
                
                autoCompleteContactMethods = new autoComplete({
                    selector: "#contact_method_id",
                    placeHolder: "Search contact methods...",
                    data: {
                        src: data.map(method => method.text),
                        cache: true
                    },
                    resultsList: {
                        element: function element(list, data) {
                            if (!data.results.length) {
                                var message = document.createElement("div");
                                message.setAttribute("class", "no_result");
                                message.innerHTML = "<span>Found No Results for \"" + data.query + "\"</span>";
                                list.prepend(message);
                            }
                        },
                        noResults: true
                    },
                    resultItem: {
                        highlight: true
                    },
                    events: {
                        input: {
                            selection: function selection(event) {
                                const selection = event.detail.selection.value;
                                contactMethodInput.value = selection;
                                
                                // Find the contact method ID and set it in hidden input
                                const selectedMethod = contactMethodsData.find(method => method.text === selection);
                                if (selectedMethod) {
                                    contactMethodHiddenInput.value = selectedMethod.id;
                                    
                                    // Update contact details field based on contact method
                                    updateContactDetailsField(selection);
                                }
                            }
                        }
                    }
                });
            }
            
            // Load initial data and initialize
            console.log('Loading contact methods data...');
            fetch(`{{ route('api.contact-methods-autocomplete') }}?query=`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Contact methods data loaded:', data);
                    initContactMethodAutocomplete(data);
                })
                .catch(error => {
                    console.error('Error loading initial contact methods:', error);
                    initContactMethodAutocomplete([]);
                });
        }
    });
    
    // Handle checkbox toggle for contact details
    const useDefaultContactCheckbox = document.getElementById('use_default_contact');
    const defaultContactDetailsField = document.getElementById('default_contact_details');
    
    if (useDefaultContactCheckbox && defaultContactDetailsField) {
        useDefaultContactCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Enable readonly mode
                defaultContactDetailsField.readOnly = true;
                defaultContactDetailsField.classList.add('bg-light');
            } else {
                // Disable readonly mode for manual editing
                defaultContactDetailsField.readOnly = false;
                defaultContactDetailsField.classList.remove('bg-light');
                defaultContactDetailsField.focus();
            }
        });
        
        // Initialize readonly state
        defaultContactDetailsField.readOnly = true;
        defaultContactDetailsField.classList.add('bg-light');
    }

    // Function to update contact details field based on selected contact method
    function updateContactDetailsField(selectedMethod) {
        const contactDetailsContainer = document.getElementById('contact_details_container');
        
        // Clear previous content
        contactDetailsContainer.innerHTML = '';
        
        // Determine which field to show based on contact method
        if (selectedMethod === 'Email') {
            contactDetailsContainer.innerHTML = `
                <label for="contact_details" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="contact_details" name="contact_details" placeholder="Enter email address..." required>
                <div class="form-text">Please enter a valid email address</div>
            `;
        } else if (selectedMethod.includes('Phone')) {
            contactDetailsContainer.innerHTML = `
                <label for="contact_details" class="form-label">Phone Number <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="contact_details" name="contact_details" placeholder="Enter phone number..." required>
                <div class="form-text">Please enter a valid phone number</div>
            `;
        } else if (selectedMethod === 'In Person' || selectedMethod === 'Site Visit') {
            contactDetailsContainer.innerHTML = `
                <label for="contact_details" class="form-label">Location/Address <span class="text-danger">*</span></label>
                <textarea class="form-control" id="contact_details" name="contact_details" rows="2" placeholder="Enter location or address details..." required></textarea>
                <div class="form-text">Please provide specific location details</div>
            `;
        } else {
            // Default to text input for other methods (Block Inspection, Meetings, etc.)
            contactDetailsContainer.innerHTML = `
                <label for="contact_details" class="form-label">Contact Details <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="contact_details" name="contact_details" placeholder="Enter contact details..." required>
                <div class="form-text">Please provide relevant contact information</div>
            `;
        }
        
        // Add event listener for validation
        const newInput = contactDetailsContainer.querySelector('input, textarea');
        if (newInput) {
            newInput.addEventListener('input', function() {
                validateContactDetails(this);
            });
        }
    }
    
    // Function to validate contact details based on type
    function validateContactDetails(input) {
        const value = input.value.trim();
        const type = input.type;
        
        // Remove previous validation classes
        input.classList.remove('is-valid', 'is-invalid');
        
        if (type === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailRegex.test(value)) {
                input.classList.add('is-valid');
            } else if (value.length > 0) {
                input.classList.add('is-invalid');
            }
        } else if (type === 'tel') {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            if (phoneRegex.test(value.replace(/[\s\-\(\)]/g, ''))) {
                input.classList.add('is-valid');
            } else if (value.length > 0) {
                input.classList.add('is-invalid');
            }
        } else {
            if (value.length >= 3) {
                input.classList.add('is-valid');
            } else if (value.length > 0) {
                input.classList.add('is-invalid');
            }
        }
    }
    
    // Function to load open issues for the selected unit
    function loadOpenIssues(blockId, blockUnitId, issueType = null) {
        const tableBody = document.getElementById('openIssuesTableBody');
        
        // Show loading state
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-3">
                    <i class="ph-spinner ph-spin"></i> Loading open issues...
                </td>
            </tr>
        `;
        
        // Build API URL with filters
        let apiUrl = `/api/block-issues?block_id=${blockId}&block_unit_id=${blockUnitId}`;
        if (issueType) {
            apiUrl += `&issue_type=${encodeURIComponent(issueType)}`;
        }
        
        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data.length > 0) {
                    // Render issues table
                    tableBody.innerHTML = data.data.map(issue => `
                        <tr>
                            <td><span class="badge bg-secondary">${issue.ref_no}</span></td>
                            <td>${issue.issue}</td>
                            <td><span class="badge bg-info">${issue.issue_type}</span></td>
                            <td>
                                ${getPriorityBadge(issue.priority_id)}
                            </td>
                            <td>${formatDate(issue.created_at)}</td>
                        </tr>
                    `).join('');
                } else {
                    // Show no issues message
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                <i class="ph-check-circle"></i> No open issues found for this unit
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading open issues:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-danger py-3">
                            <i class="ph-warning"></i> Failed to load issues. Please try again.
                        </td>
                    </tr>
                `;
            });
    }
    
    // Function to get priority badge HTML
    function getPriorityBadge(priorityId) {
        const priorities = {
            1: '<span class="badge bg-success">Low</span>',
            2: '<span class="badge bg-info">Normal</span>',
            3: '<span class="badge bg-warning">High</span>',
            4: '<span class="badge bg-danger">Urgent</span>',
            5: '<span class="badge bg-dark">Critical</span>'
        };
        return priorities[priorityId] || '<span class="badge bg-secondary">Unknown</span>';
    }
    
    // Function to format date
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric' 
        });
    }
    
    // Event listeners for issue type and unit selection changes
    const issueTypeSelect = document.getElementById('issue_type');
    const blockUnitSelect = document.getElementById('block_unit_id');
    const refreshIssuesBtn = document.getElementById('refreshIssuesBtn');
    
    if (issueTypeSelect) {
        issueTypeSelect.addEventListener('change', function() {
            const blockId = document.querySelector('input[name="block_id"]').value;
            const blockUnitId = blockUnitSelect ? blockUnitSelect.value : null;
            
            if (blockId && blockUnitId) {
                loadOpenIssues(blockId, blockUnitId, this.value);
            }
        });
    }
    
    if (blockUnitSelect) {
        blockUnitSelect.addEventListener('change', function() {
            const blockId = document.querySelector('input[name="block_id"]').value;
            const issueType = issueTypeSelect ? issueTypeSelect.value : null;
            
            if (blockId && this.value) {
                loadOpenIssues(blockId, this.value, issueType);
            }
        });
    }
    
    if (refreshIssuesBtn) {
        refreshIssuesBtn.addEventListener('click', function() {
            const blockId = document.querySelector('input[name="block_id"]').value;
            const blockUnitId = blockUnitSelect ? blockUnitSelect.value : null;
            const issueType = issueTypeSelect ? issueTypeSelect.value : null;
            
            if (blockId && blockUnitId) {
                loadOpenIssues(blockId, blockUnitId, issueType);
            }
        });
    }
    
    // Load initial issues when modal opens (if unit is already selected)
    createIssueModal.addEventListener('shown.bs.modal', function() {
        const blockId = document.querySelector('input[name="block_id"]').value;
        const blockUnitId = blockUnitSelect ? blockUnitSelect.value : null;
        
        if (blockId && blockUnitId) {
            loadOpenIssues(blockId, blockUnitId);
        }
    });
});
</script>
