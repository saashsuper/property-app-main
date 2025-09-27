<script>
/**
 * Contractors Management JavaScript Module
 * 
 * This module handles all contractor-related functionality including:
 * - Add/Edit contractor operations
 * - DataTable management with auto-refresh
 * - Form validation and AJAX submissions
 * - Delete confirmation with Bootstrap modal
 * - Export functionality with conditional button states
 */

$(document).ready(function() {
    // ========================================
    // GLOBAL VARIABLES
    // ========================================
    
    /** @var {DataTable} blockContractorsDataTable - Global DataTable instance for contractors table */
    let blockContractorsDataTable;
    
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
                    $btn.removeClass('disabled')
                        .removeAttr('onclick')
                        .attr('title', $btn.attr('id') === 'exportPdfBtn' ? 'Export to PDF' : 
                              $btn.attr('id') === 'exportExcelBtn' ? 'Export to Excel' : 'Print');
                } else {
                    $btn.addClass('disabled')
                        .attr('onclick', 'return false;')
                        .attr('title', 'No data to export');
                }
            }
        });
    }
    
    /**
     * Shows success/error messages in modals
     * 
     * @param {string} containerId - The ID of the message container
     * @param {string} type - The type of message (success, danger, warning, info)
     * @param {string} message - The message to display
     */
    function showMessage(containerId, type, message) {
        let $messageDiv = $('#' + containerId);
        
        // Create message structure if it doesn't exist
        if ($messageDiv.length === 0) {
            $messageDiv = $(`<div id="${containerId}" class="alert d-none" role="alert">
                <i class="ph-check-circle me-2"></i>
                <span class="message-text"></span>
            </div>`);
            $('.modal-body').prepend($messageDiv);
        }
        
        // Remove all alert classes and add the new one
        $messageDiv.removeClass('alert-success alert-danger alert-info alert-warning')
                  .addClass(`alert-${type}`)
                  .removeClass('d-none');
        
        // Set appropriate icon
        const $icon = $messageDiv.find('i');
        $icon.removeClass('ph-check-circle ph-warning ph-info-circle ph-x-circle');
        
        switch(type) {
            case 'success':
                $icon.addClass('ph-check-circle');
                break;
            case 'danger':
                $icon.addClass('ph-x-circle');
                break;
            case 'warning':
                $icon.addClass('ph-warning');
                break;
            case 'info':
                $icon.addClass('ph-info-circle');
                break;
        }
        
        // Set message text
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
    function clearMessage(containerId) {
        const $messageDiv = $('#' + containerId);
        if ($messageDiv.length) {
            $messageDiv.addClass('d-none');
        }
    }
    
    // ========================================
    // DATATABLE INITIALIZATION
    // ========================================
    
    /**
     * Initializes the DataTable for the contractors table
     * 
     * Sets up DataTable with responsive design, pagination, and custom language settings.
     * Prevents re-initialization if the table is already initialized.
     */
    function initializeDataTable() {
        if ($('#blockContractorsTable').length) {
            // Check if DataTable is already initialized to prevent conflicts
            if (!$.fn.DataTable.isDataTable('#blockContractorsTable')) {
                blockContractorsDataTable = $('#blockContractorsTable').DataTable({
                    responsive: true,           // Enable responsive design
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',             // Define table layout (l=length, f=filter/search, r=processing, t=table, i=info, p=pagination)
                    order: [[0, 'asc']],       // Default sort by first column (Contractor Name) ascending
                    columnDefs: [
                        { targets: [4], orderable: false } // Actions column (last column) not sortable
                    ],
                    pageLength: 10,            // Default page size
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Page size options
                    language: {
                        lengthMenu: "Show _MENU_ contractors per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ contractors",
                        infoEmpty: "Showing 0 to 0 of 0 contractors",
                        infoFiltered: "(filtered from _MAX_ total contractors)",
                        search: "Search contractors:",
                        searchPlaceholder: "Search by contractor name, email, type...",
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
                const initialRowCount = blockContractorsDataTable.rows().count();
                toggleExportButtons(initialRowCount > 0);
            }
        }
    }
    
    // ========================================
    // DATA REFRESH FUNCTIONALITY
    // ========================================
    
    /**
     * Refreshes the block contractors DataTable with fresh data from the server
     * 
     * This function is exposed globally so it can be called from other parts of the application.
     * It fetches the latest contractor data for the current block and updates the DataTable.
     * 
     * @global
     */
    window.refreshBlockContractorsTable = function() {
        if (!blockContractorsDataTable) {
            return;
        }
        
        const blockId = window.blockId || $('input[name="block_id"]').val();
        if (!blockId) {
            return;
        }
        
        $.ajax({
            url: `/block-contractors/block/${blockId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Clear and repopulate DataTable
                    blockContractorsDataTable.clear();
                    
                    data.data.forEach(function(contractor) {
                        const statusBadge = contractor.status == 1 ? 
                            '<span class="badge bg-success">Default</span>' : 
                            '<span class="badge bg-info">Active</span>';
                            
                        blockContractorsDataTable.row.add([
                            contractor.contractor_name || 'N/A',
                            contractor.contractor_email || 'N/A',
                            contractor.contractor_type_name || 'N/A',
                            statusBadge,
                            `<button class="btn btn-sm btn-outline-primary" onclick="editContractor(${contractor.id})" title="Edit Contractor">
                                <i class="ph-pencil"></i>
                            </button> 
                            <button class="btn btn-sm btn-outline-danger" onclick="contractorShowDeleteConfirmation(${contractor.id}, {
                                name: '${contractor.contractor_name || 'N/A'}',
                                email: '${contractor.contractor_email || 'N/A'}',
                                type: '${contractor.contractor_type_name || 'N/A'}',
                                status: '${contractor.status == 1 ? 'Default' : 'Active'}'
                            })" title="Delete Contractor">
                                <i class="ph-trash"></i>
                            </button>`
                        ]);
                    });
                    
                    blockContractorsDataTable.draw();
                    
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
     * Handles contractor form submission with AJAX
     * 
     * @param {string} modalId - The ID of the modal to close
     * @param {string} messageId - The ID of the message container
     * @param {string} successMessage - Success message to display
     * @param {string} errorMessage - Error message to display
     */
    function handleContractorFormSubmission(modalId, messageId, successMessage, errorMessage) {
        const $form = $('#contractorForm');
        
        $form.off('submit').on('submit', function(e) {
            e.preventDefault();
            
            const $submitBtn = $('#contractorSubmitBtn');
            const originalText = $submitBtn.html();
            $submitBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Saving...').prop('disabled', true);
            
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        showMessage(messageId, 'success', successMessage);
                        $form[0].reset();
                        
                        setTimeout(function() {
                            $('#' + modalId).modal('hide');
                            refreshBlockContractorsTable();
                        }, 800);
                    } else {
                        showMessage(messageId, 'danger', (data && data.message) || errorMessage);
                    }
                },
                error: function(xhr, status, error) {
                    showMessage(messageId, 'danger', errorMessage);
                },
                complete: function() {
                    $submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });
    }
    
    // ========================================
    // MODAL MANAGEMENT
    // ========================================
    
    /**
     * Opens the contractor modal for add or edit mode
     * 
     * @param {string} mode - The mode ('add' or 'edit')
     * @param {number} id - The contractor ID (for edit mode)
     */
    window.openContractorModal = function(mode, id = null) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            return;
        }
        
        const $modal = $('#contractorModal');
        const $modalLabel = $('#contractorModalLabel');
        const $form = $('#contractorForm');
        const $submitBtn = $('#contractorSubmitBtn');
        
        if (mode === 'add') {
            // Add mode
            $modalLabel.text('Add Contractor');
            $submitBtn.html('<i class="ph-check me-1"></i> Save');
            $form.attr('action', window.routes?.blockContractors?.store || '/block-contractors');
            $form.find('input[name="_method"]').remove(); // Remove PUT method for add
            $form[0].reset(); // Reset form
            
            // Initialize modal before showing
            initializeModal('contractorModal', 'contractor_type_id');
            
            // Modal is ready to show
            $modal.modal('show');
        } else if (mode === 'edit' && id) {
            // Edit mode - load contractor data
            loadContractorForEdit(id);
        }
    };
    
    /**
     * Loads contractor data and populates the modal for editing
     * 
     * @param {number} id - The ID of the contractor to edit
     */
    function loadContractorForEdit(id) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            return;
        }
        
        // Show loading state - try multiple selectors to find the edit button
        let $editBtn = $(`button[onclick="editContractor(${id})"]`);
        if ($editBtn.length === 0) {
            // Try alternative selector
            $editBtn = $(`button:contains("Edit")`).filter(function() {
                return $(this).attr('onclick') && $(this).attr('onclick').includes(`editContractor(${id})`);
            });
        }
        
        const originalText = $editBtn.length > 0 ? $editBtn.html() : 'Edit';
        if ($editBtn.length > 0) {
            $editBtn.html('<i class="ph-spinner ph-spin me-1"></i>Loading...').prop('disabled', true);
        }
        
        $.ajax({
            url: `/block-contractors/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const contractor = data.contractor;
                    const $modal = $('#contractorModal');
                    const $form = $('#contractorForm');
                    const $modalLabel = $('#contractorModalLabel');
                    const $submitBtn = $('#contractorSubmitBtn');
                    
                    // Update modal for edit mode
                    $modalLabel.text('Edit Contractor');
                    $submitBtn.html('<i class="ph-check me-1"></i> Update');
                    $form.attr('action', window.routes?.blockContractors?.update?.replace(':id', id) || `/block-contractors/${id}`);
                    
                    // Add PUT method for edit
                    if ($form.find('input[name="_method"]').length === 0) {
                        $form.append('<input type="hidden" name="_method" value="PUT">');
                    }
                    
                    // Initialize modal first
                    initializeModal('contractorModal', 'contractor_type_id');
                    
                    // Populate form fields
                    $('#contractor_type_id').val(contractor.contractor_type_id);
                    $('#contractor_id').val(contractor.contractor_id);
                    $('#default_contractor').prop('checked', contractor.status == 1);
                    
                    // Show the modal
                    $modal.modal('show');
                } else {
                    showMessage('contractorMessage', 'danger', 'Error loading contractor data');
                }
            },
            error: function(xhr, status, error) {
                showMessage('contractorMessage', 'danger', 'Error loading contractor data');
            },
            complete: function() {
                // Reset button state
                if ($editBtn.length > 0) {
                    $editBtn.html(originalText).prop('disabled', false);
                }
            }
        });
    }
    
    /**
     * Initialize modal with default state
     * 
     * @param {string} modalId - The ID of the modal
     * @param {string} selectId - The ID of the select dropdown
     */
    function initializeModal(modalId, selectId) {
        // Clear any previous messages
        clearMessage('contractorMessage');
    }
    
    // ========================================
    // DELETE CONFIRMATION MODAL
    // ========================================
    
    /**
     * Shows the delete confirmation modal with contractor details
     * 
     * @param {number} contractorId - The ID of the contractor to delete
     * @param {object} contractorData - The contractor data to display in confirmation
     */
    function contractorShowDeleteConfirmation(contractorId, contractorData) {
        
        // Populate contractor details in the modal
        const detailsHtml = `
            <div class="row">
                <div class="col-6"><strong>Contractor Name:</strong></div>
                <div class="col-6">${contractorData.name || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Email:</strong></div>
                <div class="col-6">${contractorData.email || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Type:</strong></div>
                <div class="col-6">${contractorData.type || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Status:</strong></div>
                <div class="col-6">${contractorData.status || 'N/A'}</div>
            </div>
        `;
        
        $('#deleteContractorDetails').html(detailsHtml);
        
        // Set up the confirm button to actually delete
        $('#confirmDeleteContractorBtn').off('click').on('click', function() {
            contractorDeleteContractor(contractorId);
        });
        
        // Show the modal
        $('#deleteContractorModal').modal('show');
    }
    
    /**
     * Deletes a contractor via AJAX
     * 
     * @param {number} contractorId - The ID of the contractor to delete
     */
    function contractorDeleteContractor(contractorId) {
        
        // Show loading state
        const $confirmBtn = $('#confirmDeleteContractorBtn');
        const originalText = $confirmBtn.html();
        $confirmBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Deleting...').prop('disabled', true);
        
        $.ajax({
            url: `/block-contractors/${contractorId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                
                // Hide the modal
                $('#deleteContractorModal').modal('hide');
                
                // Show success message
                showMessage('contractorMessage', 'success', 'Contractor deleted successfully');
                
                // Refresh the table
                refreshBlockContractorsTable();
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                
                // Show error message
                showMessage('contractorMessage', 'danger', 'Error deleting contractor. Please try again.');
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            }
        });
    }
    
    // Expose delete functions to global scope
    window.contractorShowDeleteConfirmation = contractorShowDeleteConfirmation;
    window.contractorConfirmDeletion = contractorShowDeleteConfirmation;
    window.contractorDeleteContractor = contractorDeleteContractor;
    
    // ========================================
    // TRIGGER FUNCTIONS
    // ========================================
    
    /**
     * Triggers a refresh of the contractors table
     * 
     * This function is debounced to prevent multiple simultaneous calls
     */
    function triggerContractorsRefresh() {
        if (!$.fn.DataTable.isDataTable('#blockContractorsTable')) {
            initializeDataTable();
        }
        if (typeof window.refreshBlockContractorsTable === 'function') {
            window.refreshBlockContractorsTable();
        } else {
            // If refresh function is not available, check current table data
            if (blockContractorsDataTable) {
                const currentRowCount = blockContractorsDataTable.rows().count();
                toggleExportButtons(currentRowCount > 0);
            }
        }
    }
    
    // Debounced trigger to avoid duplicate refreshes
    let contractorsRefreshTimer = null;
    function triggerContractorsRefresh() {
        clearTimeout(contractorsRefreshTimer);
        contractorsRefreshTimer = setTimeout(function() {
            if (!$.fn.DataTable.isDataTable('#blockContractorsTable')) {
                initializeDataTable();
            }
            if (typeof window.refreshBlockContractorsTable === 'function') {
                window.refreshBlockContractorsTable();
            } else {
                // If refresh function is not available, check current table data
                if (blockContractorsDataTable) {
                    const currentRowCount = blockContractorsDataTable.rows().count();
                    toggleExportButtons(currentRowCount > 0);
                }
            }
        }, 50);
    }
    
    // ========================================
    // EVENT LISTENERS AND INITIALIZATION
    // ========================================
    
    // Listen for Bootstrap tab shown event to refresh data when contractors tab becomes active
    $(document).on('shown.bs.tab', '#contractors-tab', function(e) {
        triggerContractorsRefresh();
    });

    // If Contractors tab is already active on page load, refresh once to ensure data is loaded
    if ($('#contractors').hasClass('show') && $('#contractors').hasClass('active')) {
        triggerContractorsRefresh();
    }
    
    // ========================================
    // MODAL EVENT HANDLERS
    // ========================================
    
    // Clear messages when contractor modal is opened
    $('#contractorModal').on('show.bs.modal', function() {
        // Use setTimeout to ensure DOM is ready before clearing message
        setTimeout(function() {
            clearMessage('contractorMessage');
        }, 50);
    });
    
    // ========================================
    // FORM SUBMISSION HANDLERS
    // ========================================
    
    // Handle contractor form submission
    handleContractorFormSubmission('contractorModal', 'contractorMessage', 'Contractor saved successfully!', 'Error saving contractor. Please try again.');
    
    // ========================================
    // INITIALIZATION
    // ========================================
    
    // Initialize DataTable on page load
    initializeDataTable();
});

// ========================================
// GLOBAL FUNCTIONS
// ========================================

/**
 * Edit contractor function (for backward compatibility)
 * 
 * @param {number} id - The ID of the contractor to edit
 */
function editContractor(id) {
    window.openContractorModal('edit', id);
}

// Expose editContractor to global scope for DataTable onclick handlers
window.editContractor = editContractor;

</script>
