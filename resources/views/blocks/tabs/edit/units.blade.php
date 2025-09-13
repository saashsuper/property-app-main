<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Unit Information</h6>
            <div class="d-flex align-items-center gap-2">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('export.pdf', 'block-units') }}?block_id={{ $block->id }}" 
                       class="btn btn-outline-danger btn-sm" title="Export to PDF">
                        <i class="ph-file-pdf"></i>
                    </a>
                    <a href="{{ route('export.excel', 'block-units') }}?block_id={{ $block->id }}" 
                       class="btn btn-outline-success btn-sm" title="Export to Excel">
                        <i class="ph-file-xls"></i>
                    </a>
                    <a href="{{ route('export.print', 'block-units') }}?block_id={{ $block->id }}" 
                       class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
                        <i class="ph-printer"></i>
                    </a>
                </div>
                <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#unitModal" onclick="openUnitModal('add')">
                    <i class="ph-plus align-bottom me-1"></i> Add Unit
                </button>
                <button class="btn btn-success custom-toggle" data-bs-toggle="modal" data-bs-target="#uploadUnitModal">
                    <i class="ph-upload align-bottom me-1"></i> Upload Unit
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="blockUnitsTable" class="table table-bordered table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Unit Code</th>
                        <th>Unit Name</th>
                        <th>Type</th>
                        <th>Owner's Name</th>
                        <th>Salutation</th>
                        <th>Email</th>
                        <th>Resident</th>
                        <th>Mobile</th>
                        <th>Phone</th>
                        <th>Letting Agent</th>
                        <th>Misc Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($block->units && $block->units->count() > 0)
                        @foreach($block->units as $unit)
                            <tr>
                                <td>{{ $unit->unit_code ?? 'N/A' }}</td>
                                <td>{{ $unit->unit_name ?? 'N/A' }}</td>
                                <td>{{ $unit->unitType->name ?? 'N/A' }}</td>
                                <td>{{ $unit->owners_name ?? 'N/A' }}</td>
                                <td>{{ $unit->salutation ?? 'N/A' }}</td>
                                <td>{{ $unit->email ?? 'N/A' }}</td>
                                <td>{{ $unit->resident ? 'Yes' : 'No' }}</td>
                                <td>{{ $unit->mobile_no ?? 'N/A' }}</td>
                                <td>{{ $unit->phone_number ?? 'N/A' }}</td>
                                <td>{{ $unit->letting_agent ?? 'N/A' }}</td>
                                <td>{{ $unit->misc_info ?? 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editUnit({{ $unit->id }})">Edit</button>
                                    <form action="{{ route('block-units.destroy', $unit->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this unit?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Unit Modal (Add/Edit) -->
<div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="unitModalLabel" style="color: white !important; padding-bottom: 15px;">Add Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="unitForm" method="POST">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <!-- Message container -->
                    <div id="unitMessage"></div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="block_building_id" class="form-label">Building/Core <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_building_id" name="block_building_id" required>
                                <option value="">Select Building/Core</option>
                                @foreach($block->buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="block_unit_type_id" class="form-label">Unit Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_unit_type_id" name="block_unit_type_id" required>
                                <option value="">Select Unit Type</option>
                                @foreach(\App\Models\BlockUnitType::orderBy('name')->get() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit_code" class="form-label">Unit Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit_code" name="unit_code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit_name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit_name" name="unit_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="owners_name" class="form-label">Owner's Name</label>
                            <input type="text" class="form-control" id="owners_name" name="owners_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salutation" class="form-label">Salutation</label>
                            <select class="form-select" id="salutation" name="salutation">
                                <option value="">Select Salutation</option>
                                @foreach(\App\Models\Salutation::orderBy('name')->get() as $salutation)
                                    <option value="{{ $salutation->name }}">{{ $salutation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="resident" class="form-label">Resident</label>
                            <select class="form-select" id="resident" name="resident">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mobile_no" class="form-label">Mobile Number</label>
                            <input type="number" class="form-control" id="mobile_no" name="mobile_no" min="0" max="99999999999999999999">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="number" class="form-control" id="phone_number" name="phone_number" min="0" max="99999999999999999999">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="letting_agent" class="form-label">Letting Agent</label>
                            <input type="text" class="form-control" id="letting_agent" name="letting_agent">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="misc_info" class="form-label">Miscellaneous Info</label>
                            <textarea class="form-control" id="misc_info" name="misc_info" rows="2"></textarea>
                        </div>
                        
                        <!-- Address Fields - Shown when Resident = No -->
                        <div class="col-md-6 mb-3" id="address1_field" style="display: none;">
                            <label for="address1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address1" name="address1">
                        </div>
                        <div class="col-md-6 mb-3" id="address2_field" style="display: none;">
                            <label for="address2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="address2" name="address2">
                        </div>
                        <div class="col-md-6 mb-3" id="address3_field" style="display: none;">
                            <label for="address3" class="form-label">Address Line 3</label>
                            <input type="text" class="form-control" id="address3" name="address3">
                        </div>
                        <div class="col-md-6 mb-3" id="country_field" style="display: none;">
                            <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-select" id="country_id" name="country_id">
                                <option value="">Select Country</option>
                                @foreach(\App\Models\Country::orderBy('country_name')->get() as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="state_field" style="display: none;">
                            <label for="state_id" class="form-label">County / State <span class="text-danger">*</span></label>
                            <select class="form-select" id="state_id" name="state_id">
                                <option value="">Select County / State</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="zip_field" style="display: none;">
                            <label for="zip" class="form-label">Zip / Eircode</label>
                            <input type="text" class="form-control" id="zip" name="zip">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="unitSubmitBtn">
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


<!-- Upload Unit Modal -->
<div class="modal fade" id="uploadUnitModal" tabindex="-1" aria-labelledby="uploadUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="uploadUnitModalLabel" style="color: white !important; padding-bottom: 15px;">Upload Units</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            
            <!-- Message Container -->
            <div id="uploadMessageContainer" style="display: none;">
                <div id="uploadSuccess" class="alert alert-success mx-3 mt-3" style="display: none;">
                    <h6 class="alert-heading"><i class="ph-check-circle me-2"></i>Upload Successful</h6>
                    <div id="successMessage"></div>
                </div>
            </div>
            
            <form id="uploadUnitForm" method="POST" action="{{ route('block-units.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="unit_file" class="form-label">Upload File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="unit_file" name="unit_file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">
                                <strong>Supported formats:</strong> Excel (.xlsx, .xls) or CSV (.csv)<br>
                                <strong>Maximum file size:</strong> 5MB<br>
                                <strong>Required columns:</strong> Unit Code, Unit Name, Owner's Name, Salutation, Email, Resident, Mobile Number, Phone Number, Letting Agent, Miscellaneous Info
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="ph-info me-2"></i>Upload Instructions</h6>
                                <ul class="mb-0">
                                    <li>Download the template file to see the required format</li>
                                    <li>Ensure all required fields are filled</li>
                                    <li>Unit codes must be unique within the block</li>
                                    <li>Resident field should be "Yes" or "No"</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <a href="{{ asset('storage/templates/unit-upload-template.xlsx') }}" 
                               download="unit_upload_template_{{ str_replace(' ', '_', $block->name) }}_{{ date('Y-m-d') }}.xlsx"
                               class="btn btn-outline-primary">
                                <i class="ph-download me-1"></i> Download Template
                            </a>
                        </div>
                        <div class="col-12 mb-3" id="uploadErrors" style="display: none;">
                            <div class="alert alert-danger">
                                <h6 class="alert-heading"><i class="ph-warning me-2"></i>Import Errors</h6>
                                <div id="errorList"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Upload
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
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
@endpush

<script>
/**
 * Units Management jQuery Implementation
 * 
 * This script handles all unit-related functionality including:
 * - Add/Edit/Upload unit operations
 * - DataTable management with auto-refresh
 * - Country-state dropdown dependency
 * - Address field visibility toggling
 * - Form validation and AJAX submissions
 */

$(document).ready(function() {
    // ========================================
    // GLOBAL VARIABLES
    // ========================================
    
    /** @var {DataTable} blockUnitsDataTable - Global DataTable instance for units table */
    let blockUnitsDataTable;
    
    // ========================================
    // UTILITY FUNCTIONS
    // ========================================
    
    /**
     * Loads states/provinces based on selected country
     * 
     * @param {string|number} countryId - The ID of the selected country
     * @param {string} stateSelectId - The ID of the state dropdown element
     * 
     * This function makes an AJAX call to fetch states for the given country
     * and populates the state dropdown with the response data.
     */
    
    
    /**
     * Shows a message in a modal form
     * 
     * @param {string} containerId - The ID of the message container
     * @param {string} type - The type of message ('success' or 'danger')
     * @param {string} message - The message text to display
     * 
     * Creates or updates a message div in the specified form and shows it.
     * Automatically removes existing alert classes and applies the new type.
     */
    function showMessage(containerId, type, message) {
        let $messageDiv = $('#' + containerId);
        
        // Create message div if it doesn't exist
        if (!$messageDiv.length) {
            $messageDiv = $(`<div id="${containerId}" class="alert d-none"></div>`);
            $('#' + containerId.replace('Message', 'Form')).prepend($messageDiv);
        }
        
        // Update message content and styling
        $messageDiv.removeClass('alert-success alert-danger')
                  .addClass(`alert-${type}`)
                  .text(message)
                  .removeClass('d-none');
    }
    
    // ========================================
    // DATATABLE INITIALIZATION
    // ========================================
    
    /**
     * Initializes the DataTable for the units table
     * 
     * Sets up DataTable with responsive design, pagination, and custom language settings.
     * Prevents re-initialization if the table is already initialized.
     */
    function initializeDataTable() {
        if ($('#blockUnitsTable').length) {
            // Check if DataTable is already initialized to prevent conflicts
            if (!$.fn.DataTable.isDataTable('#blockUnitsTable')) {
                blockUnitsDataTable = $('#blockUnitsTable').DataTable({
                    responsive: true,           // Enable responsive design
                    dom: 'lfrtip',             // Define table layout (l=length, f=filter/search, r=processing, t=table, i=info, p=pagination)
                    order: [[0, 'asc']],       // Default sort by first column (Unit Code) ascending
                    columnDefs: [
                        { targets: [11], orderable: false } // Actions column (last column) not sortable
                    ],
                    pageLength: 10,            // Default page size
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Page size options
                    language: {
                        lengthMenu: "Show _MENU_ units per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ units",
                        infoEmpty: "Showing 0 to 0 of 0 units",
                        infoFiltered: "(filtered from _MAX_ total units)",
                        search: "Search units:",
                        searchPlaceholder: "Search by unit code, name, owner...",
                        paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
                    }
                });
            } else {
                // Get existing DataTable instance if already initialized
                blockUnitsDataTable = $('#blockUnitsTable').DataTable();
            }
        }
    }
    
    /**
     * Refreshes the DataTable with fresh data from the server
     * 
     * This function is exposed globally so it can be called from other parts of the application.
     * It fetches the latest unit data for the current block and updates the DataTable.
     * 
     * @global
     */
    window.refreshBlockUnitsTable = function() {
        console.log('refreshBlockUnitsTable called');
        console.log('blockUnitsDataTable:', blockUnitsDataTable);
        
        if (blockUnitsDataTable) {
            // Get the current block ID from the form
            const blockId = $('input[name="block_id"]').val();
            console.log('Block ID:', blockId);
            
            // Fetch fresh data from the server
            $.ajax({
                url: `/block-units/block/${blockId}`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('API response data:', data);
                    if (data.success) {
                        // Clear existing data from DataTable
                        blockUnitsDataTable.clear();
                        console.log('DataTable cleared');
                        
                        // Add new data rows to DataTable
                        data.data.forEach(function(unit) {
                            console.log('Adding unit:', unit);
                            blockUnitsDataTable.row.add([
                                unit.unit_code || 'N/A',
                                unit.unit_name || 'N/A',
                                unit.block_unit_type?.name || 'N/A',
                                unit.owners_name || 'N/A',
                                unit.salutation || 'N/A',
                                unit.email || 'N/A',
                                unit.resident ? 'Yes' : 'No',
                                unit.mobile_no || 'N/A',
                                unit.phone_number || 'N/A',
                                unit.letting_agent || 'N/A',
                                unit.misc_info || 'N/A',
                                // Action buttons with inline event handlers
                                '<button class="btn btn-sm btn-outline-primary" onclick="editUnit(' + unit.id + ')">' +
                                    '<i class="ph-pencil"></i> Edit' +
                                '</button> ' +
                                '<form action="/block-units/' + unit.id + '" method="POST" class="d-inline-block" onsubmit="return confirm(\'Are you sure you want to delete this unit?\');">' +
                                    '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                                    '<input type="hidden" name="_method" value="DELETE">' +
                                    '<button type="submit" class="btn btn-sm btn-outline-danger">' +
                                        '<i class="ph-trash"></i> Delete' +
                                    '</button>' +
                                '</form>'
                            ]);
                        });
                        
                        // Redraw the table to show new data
                        blockUnitsDataTable.draw();
                        console.log('Units table refreshed with', data.data.length, 'units');
                    } else {
                        console.error('Error refreshing units table:', data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching units data:', error);
                }
            });
            } else {
            console.error('blockUnitsDataTable is not initialized');
        }
    };
    
    // ========================================
    // FORM HANDLERS
    // ========================================
    
    /**
     * Handles unit form submission (both Add and Edit)
     * 
     * @param {string} formId - The ID of the form
     * @param {string} modalId - The ID of the modal
     * @param {string} messageId - The ID for the message container
     * @param {string} successMessage - Success message to display
     * @param {string} errorMessage - Error message to display
     * @param {boolean} shouldReset - Whether to reset the form after success
     */
    function handleUnitFormSubmission(formId, modalId, messageId, successMessage, errorMessage, shouldReset = false) {
        $('#' + formId).on('submit', function(e) {
        e.preventDefault();
            const $form = $(this);
            const formData = new FormData(this);
            
            $.ajax({
                url: $form.attr('action'),
            method: 'POST',
                data: formData,
                processData: false,        // Don't process data (for file uploads)
                contentType: false,       // Don't set content type (let browser set it)
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
                },
                success: function(data) {
            if (data && data.success) {
                        // Show success message
                        showMessage(messageId, 'success', successMessage);
                        // Reset form if needed
                        if (shouldReset) {
                            $form[0].reset();
                        }
                        // Close modal and refresh table after delay
                        setTimeout(function() {
                            $('#' + modalId).modal('hide');
                            refreshBlockUnitsTable();
                }, 800);
            } else {
                        // Show error message
                        showMessage(messageId, 'danger', (data && data.message) || errorMessage);
                    }
                },
                error: function() {
                    // Show generic error message
                    showMessage(messageId, 'danger', errorMessage);
                }
        });
    });
    }
    
    // Initialize unified form handler
    handleUnitFormSubmission('unitForm', 'unitModal', 'unitMessage', 'Unit saved successfully!', 'Error saving unit', true);
    
    /**
     * Handles Upload Unit form submission
     * 
     * Validates file selection, shows loading state, and uploads file via AJAX.
     * Displays success/error messages and refreshes the DataTable on success.
     */
    $('#uploadUnitForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $fileInput = $('#unit_file');
        
        // Validate file selection
        if (!$fileInput[0].files[0]) {
            $('#errorList').html('Please select a file to upload.');
            $('#uploadErrors').show();
            return;
        }
        
        // Hide any previous messages
        $('#uploadMessageContainer, #uploadSuccess, #uploadErrors').hide();
        
        // Show loading state on submit button
        const $submitBtn = $form.find('button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.html('<i class="ph-spinner ph-spin me-1"></i> Uploading...').prop('disabled', true);
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: new FormData(this),
            processData: false,        // Don't process data (for file uploads)
            contentType: false,       // Don't set content type (let browser set it)
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    $('#uploadErrors').hide();
                    
                    // Show success message
                    $('#successMessage').text(data.message);
                    $('#uploadMessageContainer, #uploadSuccess').show();
                    
                    // Show validation errors if any
                    if (data.errors && data.errors.length > 0) {
                        const errorList = '<ul class="mb-0">' + data.errors.map(function(error) {
                            return '<li>' + error + '</li>';
                        }).join('') + '</ul>';
                        $('#errorList').html(errorList);
                        $('#uploadErrors').show();
                    }
                    
                    // Reset form
                    $form[0].reset();
                    
                    // Close modal and refresh table after 3 seconds
                    setTimeout(function() {
                        $('#uploadUnitModal').modal('hide');
                        setTimeout(function() {
                            refreshBlockUnitsTable();
                        }, 300);
                    }, 3000);
            } else {
                    // Show error message
                    $('#errorList').html(data.message || 'Error uploading units.');
                    $('#uploadErrors').show();
                }
            },
            error: function() {
                // Show generic error message
                $('#errorList').html('An error occurred while uploading. Please try again.');
                $('#uploadErrors').show();
            },
            complete: function() {
                // Reset button state regardless of success/failure
                $submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    /**
     * Clear messages when upload modal is opened
     * 
     * Hides all previous success/error messages when the upload modal is shown.
     */
    $('#uploadUnitModal').on('show.bs.modal', function() {
        $('#uploadMessageContainer, #uploadSuccess, #uploadErrors').hide();
    });
    
    // ========================================
    // INITIALIZATION
    // ========================================
    
    
    
    // Initialize DataTable for units listing
    initializeDataTable();
});

// ========================================
// MODAL EVENT LISTENERS (Outside document ready)
// ========================================

// Initialize unified modal when opened
$(document).on('shown.bs.modal', '#unitModal', function() {
    console.log('Modal shown event triggered');
    initializeModal('unitModal', 'resident');
});

// ========================================
// GLOBAL FUNCTIONS
// ========================================

/**
 * Loads states/provinces based on selected country
 * 
 * @param {string} countryId - The ID of the selected country
 * @param {string} stateSelectId - The ID of the state dropdown element
 * 
 * This function makes an AJAX call to fetch states for the given country
 * and populates the state dropdown with the response data.
 */
function loadStates(countryId, stateSelectId) {
    console.log('loadStates called with countryId:', countryId, 'stateSelectId:', stateSelectId);
    const $stateSelect = $('#' + stateSelectId);
    console.log('State select found:', $stateSelect.length);
    
    // Validate inputs and element existence
    if (!countryId || !$stateSelect.length) {
        console.warn('Missing countryId or stateSelect element', 'countryId:', countryId, 'stateSelect length:', $stateSelect.length);
        if ($stateSelect.length) {
            $stateSelect.html('<option value="">Select County / State</option>');
        }
        return;
    }
    
    console.log('Making API call to /api/states/' + countryId);
    // Show loading state and disable dropdown
    $stateSelect.html('<option value="">Loading states...</option>').prop('disabled', true);
    
    // Make AJAX call to fetch states
    $.ajax({
        url: `/api/states/${countryId}`,
        method: 'GET',
        dataType: 'json',
        success: function(states) {
            console.log('Received states:', states);
            // Build options HTML string
            let options = '<option value="">Select County / State</option>';
            states.forEach(function(state) {
                options += `<option value="${state.id}">${state.name}</option>`;
            });
            // Update dropdown and re-enable it
            $stateSelect.html(options).prop('disabled', false);
            console.log('States loaded successfully');
        },
        error: function(xhr, status, error) {
            console.error('Error loading states:', error);
            // Show error state and re-enable dropdown
            $stateSelect.html('<option value="">Error loading states</option>').prop('disabled', false);
        }
    });
}

/**
 * Toggles address fields visibility based on resident selection
 * 
 * @param {string} residentSelectId - The ID of the resident dropdown
 * 
 * When resident is set to "No" (value="0"), address fields become visible and required.
 * When resident is set to "Yes" (value="1"), address fields are hidden and not required.
 */
function toggleAddressFields(residentSelectId) {
    console.log('toggleAddressFields called with:', residentSelectId);
    const $residentSelect = $('#' + residentSelectId);
    console.log('Resident select found:', $residentSelect.length);
    
    if ($residentSelect.length) {
        // Remove any existing event listeners to prevent duplicates
        $residentSelect.off('change.toggleAddress');
        console.log('Removed existing event listeners');
        
                const addressFields = [
            'address1_field',
            'address2_field', 
            'address3_field',
            'country_field',
            'state_field',
            'zip_field'
        ];
        
        // Attach change event listener to resident dropdown with namespace
        $residentSelect.on('change.toggleAddress', function() {
            console.log('Resident changed to:', this.value);
            
            if (this.value === '0') {
                // Resident = No: Show address fields
                console.log('Showing address fields:', addressFields);
                
                // Show all address fields
                addressFields.forEach(function(fieldId) {
                    const $field = $('#' + fieldId);
                    console.log('Looking for field:', fieldId, 'Found:', $field.length);
                    $field.show();
                });
                
                // Set required attributes for required fields
                $('#address1').attr('required', 'required');
                $('#country_id').attr('required', 'required');
                $('#state_id').attr('required', 'required');
                
                // Attach country change listener now that country dropdown is visible
                const $countrySelect = $('#country_id');
                console.log('Attaching country change listener, country select found:', $countrySelect.length);
                if ($countrySelect.length) {
                    $countrySelect.off('change.loadStates').on('change.loadStates', function() {
                        console.log('Country changed in Unit modal:', this.value);
                        loadStates(this.value, 'state_id');
                    });
                    console.log('Country change listener attached');
                }
                } else {
                // Resident = Yes: Hide address fields
                addressFields.forEach(function(fieldId) {
                    $('#' + fieldId).hide();
                });
                
                // Remove required attributes from all address fields
                addressFields.forEach(function(fieldId) {
                    $('#' + fieldId).find('input, select').removeAttr('required');
                });
            }
        });
    }
}

/**
 * Initialize modal with default state (resident = Yes, address fields hidden)
 * 
 * @param {string} modalId - The ID of the modal
 * @param {string} residentId - The ID of the resident dropdown
 */
function initializeModal(modalId, residentId) {
    console.log('initializeModal called with modalId:', modalId, 'residentId:', residentId);
    
    const addressFields = [
        'address1_field',
        'address2_field',
        'address3_field',
        'country_field',
        'state_field',
        'zip_field'
    ];
    
    // Hide all address fields
    addressFields.forEach(function(fieldId) {
        const $field = $('#' + fieldId);
        console.log('Hiding field:', fieldId, 'Found:', $field.length);
        $field.hide();
    });
    
    // Set resident dropdown to Yes
    const $residentSelect = $('#' + residentId);
    console.log('Setting resident dropdown:', residentId, 'Found:', $residentSelect.length);
    $residentSelect.val('1');
    
    // Attach toggle functionality to resident dropdown
    console.log('Attaching toggle functionality to:', residentId);
    toggleAddressFields(residentId);
}

/**
 * Opens the unit modal in Add or Edit mode
 * 
 * @param {string} mode - 'add' or 'edit'
 * @param {number} id - Unit ID (only needed for edit mode)
 */
function openUnitModal(mode, id = null) {
    const $modal = $('#unitModal');
    const $modalLabel = $('#unitModalLabel');
    const $form = $('#unitForm');
    const $submitBtn = $('#unitSubmitBtn');
    
    if (mode === 'add') {
        // Add mode
        $modalLabel.text('Add Unit');
        $submitBtn.html('<i class="ph-check me-1"></i> Save');
        $form.attr('action', '{{ route("block-units.store") }}');
        $form.find('input[name="_method"]').remove(); // Remove PUT method for add
        $form[0].reset(); // Reset form
        
        // Initialize modal before showing
        initializeModal('unitModal', 'resident');
        
        $modal.modal('show');
    } else if (mode === 'edit' && id) {
        // Edit mode - load unit data
        loadUnitForEdit(id);
    }
}

/**
 * Loads unit data and populates the modal for editing
 * 
 * @param {number} id - The ID of the unit to edit
 */
function loadUnitForEdit(id) {
            // Show loading state
    const $editBtn = $(`button[onclick="editUnit(${id})"]`);
    const originalText = $editBtn.html();
    $editBtn.html('<i class="ph-spinner ph-spin me-1"></i>Loading...').prop('disabled', true);
    
    $.ajax({
        url: `/block-units/${id}`,
        method: 'GET',
                headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data && data.success) {
                const unit = data.data;
                const $modal = $('#unitModal');
                const $modalLabel = $('#unitModalLabel');
                const $form = $('#unitForm');
                const $submitBtn = $('#unitSubmitBtn');
                
                // Set modal to edit mode
                $modalLabel.text('Edit Unit');
                $submitBtn.html('<i class="ph-check me-1"></i> Update');
                $form.attr('action', `/block-units/${id}`);
                
                // Add PUT method for edit
                if ($form.find('input[name="_method"]').length === 0) {
                    $form.append('<input type="hidden" name="_method" value="PUT">');
                }
                
                // Populate form fields
                $('#block_building_id').val(unit.block_building_id);
                $('#block_unit_type_id').val(unit.block_unit_type_id);
                $('#unit_code').val(unit.unit_code);
                $('#unit_name').val(unit.unit_name);
                $('#owners_name').val(unit.owners_name);
                $('#salutation').val(unit.salutation);
                $('#email').val(unit.email);
                $('#mobile_no').val(unit.mobile_no);
                $('#phone_number').val(unit.phone_number);
                $('#letting_agent').val(unit.letting_agent);
                $('#misc_info').val(unit.misc_info);
                $('#resident').val(unit.resident);
                
                // Handle address fields based on resident status
                if (unit.resident == 0) {
                    // Non-resident: Show address fields and populate them
                    const addressFields = ['address1_field', 'address2_field', 'address3_field', 'country_field', 'state_field', 'zip_field'];
                    addressFields.forEach(function(fieldId) {
                        $('#' + fieldId).show();
                    });
                    
                    $('#address1').val(unit.address1);
                    $('#address2').val(unit.address2);
                    $('#address3').val(unit.address3);
                    $('#zip').val(unit.zip);
                    
                    // Set country and load states
                    if (unit.country_id) {
                        $('#country_id').val(unit.country_id);
                        loadStates(unit.country_id, 'state_id').then(function() {
                            if (unit.state_id) {
                                $('#state_id').val(unit.state_id);
                            }
                        });
                    }
                } else {
                    // Resident: Hide address fields
                    const addressFields = ['address1_field', 'address2_field', 'address3_field', 'country_field', 'state_field', 'zip_field'];
                    addressFields.forEach(function(fieldId) {
                        $('#' + fieldId).hide();
                    });
                }
                
                // Initialize modal before showing
                initializeModal('unitModal', 'resident');
                
                // Show the modal
                $modal.modal('show');
            } else {
                showMessage('unitMessage', 'danger', 'Error loading unit data');
            }
        },
        error: function() {
            showMessage('unitMessage', 'danger', 'Error loading unit data');
        },
        complete: function() {
            // Restore button state
            $editBtn.html(originalText).prop('disabled', false);
            }
        });
    }

/**
 * Edit unit function (for backward compatibility)
 * 
 * @param {number} id - The ID of the unit to edit
 */
function editUnit(id) {
    openUnitModal('edit', id);
}
</script>