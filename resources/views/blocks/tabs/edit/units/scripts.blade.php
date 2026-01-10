<script>
/**
 * Units Management JavaScript Module
 * 
 * This module handles all unit-related functionality including:
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
     * Toggles export buttons based on table data availability
     * 
     * @param {boolean} hasData - Whether the table has data
     */
    function toggleExportButtons(hasData) {
        const exportButtons = ['#exportPdfBtn', '#exportExcelBtn', '#exportPrintBtn'];
        
        exportButtons.forEach(function(buttonId) {
            const $btn = $(buttonId);
            if ($btn.length) {
                if (hasData) {
                    // Enable button
                    $btn.removeClass('disabled')
                        .removeAttr('onclick')
                        .attr('title', $btn.attr('id') === 'exportPdfBtn' ? 'Export to PDF' : 
                              $btn.attr('id') === 'exportExcelBtn' ? 'Export to Excel' : 'Print');
                } else {
                    // Disable button
                    $btn.addClass('disabled')
                        .attr('onclick', 'return false;')
                        .attr('title', 'No data to export');
                }
            }
        });
    }
    
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
    window.showMessage = function(containerId, type, message) {
        let $messageDiv = $('#' + containerId);
        
        // If message div doesn't exist, create it
        if (!$messageDiv.length) {
            $messageDiv = $(`
                <div id="${containerId}" class="alert d-none" role="alert">
                    <i class="ph-check-circle me-2"></i>
                    <span class="message-text"></span>
                </div>
            `);
            $('#unitForm .modal-body').prepend($messageDiv);
        }
        
        // Update message content and styling
        $messageDiv.removeClass('alert-success alert-danger alert-info alert-warning')
                  .addClass(`alert-${type}`)
                  .removeClass('d-none');
        
        // Update icon based on message type
        const $icon = $messageDiv.find('i');
        $icon.removeClass('ph-check-circle ph-warning ph-info-circle ph-x-circle');
        
        switch(type) {
            case 'success':
                $icon.addClass('ph-check-circle');
                break;
            case 'danger':
            case 'error':
                $icon.addClass('ph-x-circle');
                break;
            case 'warning':
                $icon.addClass('ph-warning');
                break;
            case 'info':
                $icon.addClass('ph-info-circle');
                break;
            default:
                $icon.addClass('ph-info-circle');
        }
        
        // Update message text
        $messageDiv.find('.message-text').text(message);
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                $messageDiv.addClass('d-none');
            }, 5000);
        }
    }
    
    /**
     * Clears/hides message in the modal
     * 
     * @param {string} containerId - The ID of the message container
     */
    window.clearMessage = function(containerId) {
        const $messageDiv = $('#' + containerId);
        if ($messageDiv.length) {
            $messageDiv.addClass('d-none');
        }
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
                    responsive: true, 
                    autoWidth: false,   
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
                    order: [[0, 'asc']],       // Default sort by first column (Unit Code) ascending
                    columnDefs: [
                        { targets: [8], orderable: false } // Actions column (last column) not sortable
                    ],
                    pageLength: 10,            // Default page size
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Page size options
                    language: {
                        lengthMenu: "Show _MENU_ units per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ units",
                        infoEmpty: "Showing 0 to 0 of 0 units",
                        infoFiltered: "(filtered from _MAX_ total units)",
                        search: "Search units:",
                        searchPlaceholder: "Search by unit code, name, owner, email...",
                        paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
                    },
                    initComplete: function() {
                        // Style the search box
                        $('.dataTables_filter input')
                            .addClass('form-control')
                            .removeClass('mb-3')
                            .css({
                                'width': '300px',
                                'height': '38px',
                                'font-size': '14px',
                                'margin-left': '10px',
                                'margin-bottom': '0 !important'
                            });
                        
                        // Style the page length dropdown
                        $('.dataTables_length select')
                            .addClass('form-select')
                            .css({
                                'width': 'auto',
                                'height': '38px',
                                'font-size': '14px',
                                'margin': '0 10px'
                            });
                        
                        // Ensure labels and inputs are on the same line
                        $('.dataTables_length label').css({
                            'display': 'flex',
                            'align-items': 'center',
                            'margin-bottom': '0'
                        });
                        
                        $('.dataTables_filter label').css({
                            'display': 'flex',
                            'align-items': 'center',
                            'margin-bottom': '0'
                        });
                    }
                });
                
                // Check initial data and toggle export buttons
                const initialRowCount = blockUnitsDataTable.rows().count();
                toggleExportButtons(initialRowCount > 0);
            } else {
                // Get existing DataTable instance if already initialized
                blockUnitsDataTable = $('#blockUnitsTable').DataTable();
                
                // Check data and toggle export buttons for existing table
                const existingRowCount = blockUnitsDataTable.rows().count();
                toggleExportButtons(existingRowCount > 0);
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
        if (!blockUnitsDataTable) {
            if ($.fn.DataTable.isDataTable('#blockUnitsTable')) {
                blockUnitsDataTable = $('#blockUnitsTable').DataTable();
            } else {
                return;
            }
        }
        
        const blockId = window.blockId || $('input[name="block_id"]').val();
        if (!blockId) {
            return;
        }
        
        $.ajax({
            url: `/block-units/block/${blockId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Clear and repopulate DataTable
                    blockUnitsDataTable.clear();
                    
                    data.data.forEach(function(unit) {
                        // Generate the show URL
                        const showBase = window.routes?.blockUnits?.showBase || '/block-units';
                        const showUrl = `${showBase}/${unit.id}`;
                        const unitCodeLink = `<a href="${showUrl}" class="text-primary text-decoration-none fw-semibold" title="View Unit Details">${unit.unit_code || 'N/A'}</a>`;
                        blockUnitsDataTable.row.add([
                            unitCodeLink,
                            unit.unit_name || 'N/A',
                            unit.unit_type?.name || 'N/A',
                            unit.owners_name || 'N/A',
                            unit.email || 'N/A',
                            unit.resident ? 'Yes' : 'No',
                            unit.mobile_no || 'N/A',
                            unit.letting_agent || 'N/A',
                            `<a href="${showUrl}" class="btn btn-sm btn-outline-info" title="View Unit Details">
                                <i class="ph-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-primary" onclick="editUnit(${unit.id})" title="Edit Unit">
                                <i class="ph-pencil"></i>
                            </button> 
                            ${unit.has_issues ? 
                                `<button class="btn btn-sm btn-outline-warning" onclick="unitShowDeleteConfirmation(${unit.id}, {
                                    unit_code: '${(unit.unit_code || 'N/A').replace(/'/g, "\\'")}',
                                    unit_name: '${(unit.unit_name || 'N/A').replace(/'/g, "\\'")}',
                                    owners_name: '${(unit.owners_name || 'N/A').replace(/'/g, "\\'")}',
                                    unit_type: { name: '${(unit.unit_type?.name || 'N/A').replace(/'/g, "\\'")}' },
                                    has_issues: true,
                                    issues_count: ${unit.issues_count || 0}
                                })" title="Archive Unit">
                                    <i class="ph-archive"></i>
                                </button>` :
                                `<button class="btn btn-sm btn-outline-danger" onclick="unitShowDeleteConfirmation(${unit.id}, {
                                    unit_code: '${(unit.unit_code || 'N/A').replace(/'/g, "\\'")}',
                                    unit_name: '${(unit.unit_name || 'N/A').replace(/'/g, "\\'")}',
                                    owners_name: '${(unit.owners_name || 'N/A').replace(/'/g, "\\'")}',
                                    unit_type: { name: '${(unit.unit_type?.name || 'N/A').replace(/'/g, "\\'")}' },
                                    has_issues: false,
                                    issues_count: 0
                                })" title="Delete Unit">
                                    <i class="ph-trash"></i>
                                </button>`
                            }`
                        ]);
                    });
                    
                    blockUnitsDataTable.draw();
                    
                    // Toggle export buttons based on data availability
                    toggleExportButtons(data.data.length > 0);
                } else {
                    // Disable export buttons on error
                    toggleExportButtons(false);
                }
            },
            error: function(xhr, status, error) {
                // Disable export buttons on error
                toggleExportButtons(false);
            }
        });
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
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    console.log('Form submission success response:', data);
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
                            refreshBlockUnitsTable(); // Direct refresh since we're staying on Units tab
                        }, 800);
                    } else {
                        // Show error message
                        showMessage(messageId, 'danger', (data && data.message) || errorMessage);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Form submission error:', xhr, status, error);
                    console.log('Response text:', xhr.responseText);
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
        
        // Debug: Log form data before sending
        const formData = new FormData(this);
        console.log('Form data being sent:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        
        // Check if block_id is present
        const blockId = $form.find('input[name="block_id"]').val();
        console.log('Block ID from form:', blockId);
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: formData,
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
                    
                    // Close modal and refresh after 3 seconds
                    setTimeout(function() {
                        $('#uploadUnitModal').modal('hide');
                        refreshBlockUnitsTable(); // Direct refresh since we're staying on Units tab
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
     */
    $('#uploadUnitModal').on('show.bs.modal', function() {
        $('#uploadMessageContainer, #uploadSuccess, #uploadErrors').hide();
    });
    
    // ========================================
    // INITIALIZATION
    // ========================================
    
    // Initialize DataTable for units listing
    initializeDataTable();

    // Debounced trigger to avoid duplicate refreshes
    let unitsRefreshTimer = null;
    function triggerUnitsRefresh() {
        clearTimeout(unitsRefreshTimer);
        unitsRefreshTimer = setTimeout(function() {
            if (!$.fn.DataTable.isDataTable('#blockUnitsTable')) {
                initializeDataTable();
            }
            if (typeof window.refreshBlockUnitsTable === 'function') {
                window.refreshBlockUnitsTable();
            } else {
                // If refresh function is not available, check current table data
                if (blockUnitsDataTable) {
                    const currentRowCount = blockUnitsDataTable.rows().count();
                    toggleExportButtons(currentRowCount > 0);
                }
            }
        }, 50);
    }

    // ========================================
    // EVENT LISTENERS AND INITIALIZATION
    // ========================================
    
    // Listen for Bootstrap tab shown event to refresh data when units tab becomes active
    $(document).on('shown.bs.tab', '#units-tab', function(e) {
        triggerUnitsRefresh();
    });

    // If Units tab is already active on page load, refresh once to ensure data is loaded
    if ($('#units').hasClass('show') && $('#units').hasClass('active')) {
        triggerUnitsRefresh();
    }
    
    // ========================================
    // MODAL EVENT HANDLERS
    // ========================================
    
    // Clear messages when unit modal is opened and re-initialize toggle functionality
    $('#unitModal').on('show.bs.modal', function() {
        // Use setTimeout to ensure DOM is ready before clearing message
        setTimeout(function() {
            clearMessage('unitMessage');
        }, 50);
        // Re-initialize the resident toggle functionality
        setTimeout(function() {
            toggleAddressFields('resident');
        }, 100);
    });
    
    $('#uploadUnitModal').on('show.bs.modal', function() {
        $('#uploadMessageContainer, #uploadSuccess, #uploadErrors').hide();
        
        // Debug: Check if block_id is present when modal opens
        const blockId = $('#uploadUnitForm input[name="block_id"]').val();
        console.log('Modal opened - Block ID:', blockId);
        
        if (!blockId) {
            console.error('Block ID is missing from the upload form!');
        }
    });
});

// ========================================
// GLOBAL FUNCTIONS (accessible from HTML onclick)
// ========================================

/**
 * Opens the unit modal in Add or Edit mode
 * 
 * @param {string} mode - 'add' or 'edit'
 * @param {number} id - Unit ID (only needed for edit mode)
 */
function openUnitModal(mode, id = null) {
    // Ensure jQuery is available
    if (typeof $ === 'undefined') {
        return;
    }
    
    const $modal = $('#unitModal');
    const $modalLabel = $('#unitModalLabel');
    const $form = $('#unitForm');
    const $submitBtn = $('#unitSubmitBtn');
    
    // Fetch fresh building/core and unit type data before opening modal
    refreshBuildingCoreDropdowns();
    
    if (mode === 'add') {
        // Add mode
        $modalLabel.text('Add Unit');
        $submitBtn.html('<i class="ph-check me-1"></i> Save');
        $form.attr('action', window.routes?.blockUnits?.store || '/block-units');
        $form.find('input[name="_method"]').remove(); // Remove PUT method for add
        $form[0].reset(); // Reset form
        
        // Initialize modal before showing
        initializeModal('unitModal', 'resident');
        
        // Also ensure toggle is set up after modal is shown
        $modal.on('shown.bs.modal', function() {
            toggleAddressFields('resident');
        });
        
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
    // Ensure jQuery is available
    if (typeof $ === 'undefined') {
        return;
    }
    
    // Show loading state - try multiple selectors to find the edit button
    let $editBtn = $(`button[onclick="editUnit(${id})"]`);
    if ($editBtn.length === 0) {
        // Try alternative selector
        $editBtn = $(`button:contains("Edit")`).filter(function() {
            return $(this).attr('onclick') && $(this).attr('onclick').includes(`editUnit(${id})`);
        });
    }
    
    const originalText = $editBtn.length > 0 ? $editBtn.html() : 'Edit';
    if ($editBtn.length > 0) {
        $editBtn.html('<i class="ph-spinner ph-spin me-1"></i>Loading...').prop('disabled', true);
    }
        
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
                
                // Initialize modal first (this sets up the toggle functionality and hides address fields)
                initializeModal('unitModal', 'resident');
                
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
                
                // Convert boolean to string for dropdown (API returns boolean, dropdown expects string)
                const residentValue = unit.resident ? '1' : '0';
                $('#resident').val(residentValue);
                
                // Handle address fields based on resident status AFTER setting resident value
                if (!unit.resident) { // Check boolean directly
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
                        $('#country_id').val(unit.country_id).trigger('change');
                        
                        loadStates(unit.country_id, 'state_id').then(function(states) {
                            if (unit.state_id) {
                                $('#state_id').val(unit.state_id).trigger('change');
                            }
                            
                        // Show the modal after states are loaded
                        $modal.on('shown.bs.modal', function() {
                            toggleAddressFields('resident');
                        });
                        $modal.modal('show');
                        }).catch(function(error) {
                            // Show modal even if states fail to load
                            $modal.on('shown.bs.modal', function() {
                                toggleAddressFields('resident');
                            });
                            $modal.modal('show');
                        });
                    } else {
                        $modal.on('shown.bs.modal', function() {
                            toggleAddressFields('resident');
                        });
                        $modal.modal('show');
                    }
                } else {
                    // If resident is true, address fields remain hidden (as set by initializeModal)
                    // Show the modal for resident units
                    $modal.on('shown.bs.modal', function() {
                        toggleAddressFields('resident');
                    });
                    $modal.modal('show');
                }
                
            } else {
                showMessage('unitMessage', 'danger', 'Error loading unit data');
            }
        },
        error: function(xhr, status, error) {
            showMessage('unitMessage', 'danger', 'Error loading unit data');
        },
        complete: function() {
            // Restore button state
            if ($editBtn.length > 0) {
                $editBtn.html(originalText).prop('disabled', false);
            }
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

// Expose editUnit to global scope for DataTable onclick handlers
window.editUnit = editUnit;

// ========================================
// DELETE CONFIRMATION MODAL
// ========================================

/**
 * Shows the delete confirmation modal with unit details
 * 
 * @param {number} unitId - The ID of the unit to delete
 * @param {object} unitData - The unit data to display in confirmation
 */
function unitShowDeleteConfirmation(unitId, unitData) {
    
    // Populate unit details in the modal
    const detailsHtml = `
        <div class="row">
            <div class="col-6"><strong>Unit Code:</strong></div>
            <div class="col-6">${unitData.unit_code || 'N/A'}</div>
        </div>
        <div class="row">
            <div class="col-6"><strong>Unit Name:</strong></div>
            <div class="col-6">${unitData.unit_name || 'N/A'}</div>
        </div>
        <div class="row">
            <div class="col-6"><strong>Owner:</strong></div>
            <div class="col-6">${unitData.owners_name || 'N/A'}</div>
        </div>
        <div class="row">
            <div class="col-6"><strong>Type:</strong></div>
            <div class="col-6">${unitData.unit_type?.name || 'N/A'}</div>
        </div>
    `;
    
    $('#deleteUnitDetails').html(detailsHtml);
    
    // Set up the confirm button to actually delete
    $('#confirmDeleteUnitBtn').off('click').on('click', function() {
        unitDeleteUnit(unitId);
    });
    
    // Show the modal
    $('#deleteUnitModal').modal('show');
}

/**
 * Deletes a unit via AJAX
 * 
 * @param {number} unitId - The ID of the unit to delete
 */
function unitDeleteUnit(unitId) {
    
    // Show loading state
    const $confirmBtn = $('#confirmDeleteUnitBtn');
    const originalText = $confirmBtn.html();
    $confirmBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Processing...').prop('disabled', true);
    
    $.ajax({
        url: `/block-units/${unitId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            
            // Hide the modal
            $('#deleteUnitModal').modal('hide');
            
            // Show success message from response
            const message = response.message || 'Unit action completed successfully';
            showMessage('unitMessage', 'success', message);
            
            // Close modal and refresh table after delay (same as add/edit)
            setTimeout(function() {
                refreshBlockUnitsTable(); // Direct refresh since we're staying on Units tab
            }, 800);
            
            // Reset button state
            $confirmBtn.html(originalText).prop('disabled', false);
        },
        error: function(xhr, status, error) {
            
            // Show error message
            showMessage('unitMessage', 'danger', 'Error deleting unit. Please try again.');
            
            // Reset button state
            $confirmBtn.html(originalText).prop('disabled', false);
        }
    });
}

// Expose delete functions to global scope
window.unitShowDeleteConfirmation = unitShowDeleteConfirmation;
window.unitConfirmDeletion = unitShowDeleteConfirmation;
window.unitDeleteUnit = unitDeleteUnit;

/**
 * View unit details in a read-only modal
 * 
 * @param {number} id - The ID of the unit to view
 */
function viewUnit(id) {
    // Find and set loading state on the clicked button if present
    let $viewBtn = $(`button[onclick="viewUnit(${id})"]`);
    const originalText = $viewBtn.length > 0 ? $viewBtn.html() : 'View';
    if ($viewBtn.length > 0) {
        $viewBtn.html('<i class="ph-spinner ph-spin me-1"></i>Loading...').prop('disabled', true);
    }
    
    $.ajax({
        url: `/block-units/${id}`,
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(resp) {
            if (!resp || !resp.success || !resp.data) {
                showMessage('unitMessage', 'danger', 'Error loading unit details');
                return;
            }
            const u = resp.data || {};
            // Populate fields
            $('#v_unit_code').text(u.unit_code || 'N/A');
            $('#v_unit_name').text(u.unit_name || 'N/A');
            $('#v_unit_type').text((u.unit_type && u.unit_type.name) ? u.unit_type.name : (u.unit_type_name || 'N/A'));
            $('#v_building').text((u.building && u.building.name) ? u.building.name : (u.building_name || 'N/A'));
            $('#v_resident').text(u.resident ? 'Yes' : 'No');
            
            $('#v_owners_name').text(u.owners_name || 'N/A');
            $('#v_salutation').text(u.salutation || 'N/A');
            $('#v_email').text(u.email || 'N/A');
            $('#v_mobile_no').text(u.mobile_no || 'N/A');
            $('#v_phone_number').text(u.phone_number || 'N/A');
            $('#v_letting_agent').text(u.letting_agent || 'N/A');
            
            $('#v_address1').text(u.address1 || 'N/A');
            $('#v_address2').text(u.address2 || 'N/A');
            $('#v_address3').text(u.address3 || 'N/A');
            $('#v_country').text(u.country_name || (u.country_id || 'N/A'));
            $('#v_state').text(u.state_name || (u.state_id || 'N/A'));
            $('#v_zip').text(u.zip || 'N/A');
            
            $('#v_misc_info').text(u.misc_info || 'N/A');
            
            // Show modal
            $('#viewUnitModal').modal('show');
        },
        error: function() {
            showMessage('unitMessage', 'danger', 'Error loading unit details');
        },
        complete: function() {
            if ($viewBtn.length > 0) {
                $viewBtn.html(originalText).prop('disabled', false);
            }
        }
    });
}
window.viewUnit = viewUnit;

// ========================================
// SIMPLE ONCHANGE HANDLERS
// ========================================

/**
 * Handles country dropdown change event
 * 
 * @param {string} countryId - The selected country ID
 */
function handleCountryChange(countryId) {
    const $container = $('#unitModal');
    if (countryId) {
        loadStates(countryId, 'state_id');
    } else {
        const $stateSelect = $container.find('#state_id');
        if ($stateSelect.length) {
            $stateSelect.html('<option value="">Select County / State</option>');
        }
    }
}

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
    const $container = $('#unitModal');
    const $stateSelect = $container.find('#' + stateSelectId);

    // Validate inputs and element existence
    if (!countryId || !$stateSelect.length) {
        if ($stateSelect.length) {
            $stateSelect.html('<option value="">Select County / State</option>');
        }
        // Return a resolved promise so callers using .then do not break
        return $.Deferred().resolve([]).promise();
    }

    // Show loading state and disable dropdown
    $stateSelect.html('<option value="">Loading states...</option>').prop('disabled', true);

    // Make AJAX call to fetch states and return the jqXHR (thenable)
    return $.ajax({
        url: `/api/states/${countryId}`,
        method: 'GET',
        dataType: 'json'
    }).then(function(response) {
        // Accept either an array directly or { data: [...] }
        const states = Array.isArray(response) ? response : (response && Array.isArray(response.data) ? response.data : []);

        // Build options HTML string
        let options = '<option value="">Select County / State</option>';
        states.forEach(function(state, index) {
            const id = state && (state.id ?? state.value);
            const name = state && (state.name ?? state.text);
            if (id != null && name != null) {
                options += `<option value="${id}">${name}</option>`;
            }
        });

        // Update dropdown, ensure it is enabled and visible
        $stateSelect.html(options).prop('disabled', false);
        $container.find('#state_field').show();
        return states;
    }).catch(function(error) {
        $stateSelect.html('<option value="">Error loading states</option>').prop('disabled', false);
        return [];
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
    const $residentSelect = $('#' + residentSelectId);
    
    if ($residentSelect.length) {
        // Remove any existing event listeners to prevent duplicates
        $residentSelect.off('change.toggleAddress');
        
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
            if (this.value === '0') {
                // Resident = No: Show address fields
                // Show all address fields
                addressFields.forEach(function(fieldId) {
                    const $field = $('#' + fieldId);
                    $field.show();
                });
                
                // Set required attributes for required fields
                $('#address1').attr('required', 'required');
                $('#country_id').attr('required', 'required');
                $('#state_id').attr('required', 'required');
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
        
        // Set initial state based on current value
        const currentValue = $residentSelect.val();
        if (currentValue === '0') {
            // Show address fields if resident is currently "No"
            addressFields.forEach(function(fieldId) {
                const $field = $('#' + fieldId);
                $field.show();
            });
            $('#address1').attr('required', 'required');
            $('#country_id').attr('required', 'required');
            $('#state_id').attr('required', 'required');
        } else {
            // Hide address fields if resident is "Yes" or not set
            addressFields.forEach(function(fieldId) {
                $('#' + fieldId).hide();
            });
            addressFields.forEach(function(fieldId) {
                $('#' + fieldId).find('input, select').removeAttr('required');
            });
        }
    }
}

/**
 * Initialize modal with default state (resident = Yes, address fields hidden)
 * 
 * @param {string} modalId - The ID of the modal
 * @param {string} residentId - The ID of the resident dropdown
 */
function initializeModal(modalId, residentId) {
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
        $field.hide();
    });
    
    // Set resident dropdown to Yes
    const $residentSelect = $('#' + residentId);
    $residentSelect.val('1');
    
    // Attach toggle functionality to resident dropdown
    toggleAddressFields(residentId);
}

/**
 * Refreshes building/core dropdown with fresh data from database
 * 
 * Fetches the latest buildings for the current block and updates
 * the dropdown options before opening the modal
 */
function refreshBuildingCoreDropdowns() {
    const blockId = window.blockId || $('input[name="block_id"]').val();
    
    // Show loading state for building dropdown only
    const $buildingSelect = $('#block_building_id');
    
    // Store current value to preserve selection if modal is in edit mode
    const currentBuildingValue = $buildingSelect.val();
    
    // Set loading state
    $buildingSelect.html('<option value="">Loading buildings...</option>').prop('disabled', true);
    
    // Fetch fresh building data
    $.ajax({
        url: '/api/blocks/' + blockId + '/buildings',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let buildingOptions = '<option value="">Select Building/Core</option>';
            
            if (response && response.length > 0) {
                response.forEach(function(building) {
                    const selected = building.id == (refreshBuildingCoreDropdowns._selectedId || currentBuildingValue) ? 'selected' : '';
                    buildingOptions += `<option value="${building.id}" ${selected}>${building.name}</option>`;
                });
            }
            
            $buildingSelect.html(buildingOptions).prop('disabled', false);
            console.log('Buildings refreshed:', response.length, 'buildings loaded');
        },
        error: function(xhr, status, error) {
            console.error('Error fetching buildings:', error);
            $buildingSelect.html('<option value="">Error loading buildings</option>').prop('disabled', false);
        }
    });
}

// Overload to allow preselecting a building ID
const _originalRefreshBuildings = refreshBuildingCoreDropdowns;
refreshBuildingCoreDropdowns = function(selectedBuildingId = null) {
    try {
        refreshBuildingCoreDropdowns._selectedId = selectedBuildingId;
        _originalRefreshBuildings();
    } finally {
        // Clear after use to avoid leaking selection across calls
        refreshBuildingCoreDropdowns._selectedId = null;
    }
}
</script>
