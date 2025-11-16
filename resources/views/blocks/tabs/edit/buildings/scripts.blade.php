<script>
/**
 * Buildings Management JavaScript Module
 * 
 * This module handles all building-related functionality including:
 * - Add/Edit building operations
 * - DataTable management with auto-refresh
 * - Form validation and AJAX submissions
 * - Delete confirmation with Bootstrap modal
 * - Export functionality with conditional button states
 */

$(document).ready(function() {
    // ========================================
    // GLOBAL VARIABLES
    // ========================================
    
    /** @var {DataTable} blockBuildingsDataTable - Global DataTable instance for buildings table */
    let blockBuildingsDataTable;
    
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
     * Initializes the DataTable for the buildings table
     * 
     * Sets up DataTable with responsive design, pagination, and custom language settings.
     * Prevents re-initialization if the table is already initialized.
     */
    function initializeDataTable() {
        if ($('#blockBuildingsTable').length) {
            // Check if DataTable is already initialized to prevent conflicts
            if (!$.fn.DataTable.isDataTable('#blockBuildingsTable')) {
                blockBuildingsDataTable = $('#blockBuildingsTable').DataTable({
                    responsive: true,           // Enable responsive design
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',             // Define table layout (l=length, f=filter/search, r=processing, t=table, i=info, p=pagination)
                    order: [[0, 'asc']],       // Default sort by first column (Building Name) ascending
                    columnDefs: [
                        { targets: [6], orderable: false } // Actions column (last column) not sortable
                    ],
                    pageLength: 10,            // Default page size
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Page size options
                    language: {
                        lengthMenu: "Show _MENU_ buildings per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ buildings",
                        infoEmpty: "Showing 0 to 0 of 0 buildings",
                        infoFiltered: "(filtered from _MAX_ total buildings)",
                        search: "Search buildings:",
                        searchPlaceholder: "Search by building name, type, floors...",
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
                const initialRowCount = blockBuildingsDataTable.rows().count();
                toggleExportButtons(initialRowCount > 0);
            }
        }
    }
    
    // ========================================
    // DATA REFRESH FUNCTIONALITY
    // ========================================
    
    /**
     * Refreshes the block buildings DataTable with fresh data from the server
     * 
     * This function is exposed globally so it can be called from other parts of the application.
     * It fetches the latest building data for the current block and updates the DataTable.
     * 
     * @global
     */
    window.refreshBlockBuildingsTable = function() {
        if (!blockBuildingsDataTable) {
            return;
        }
        
        const blockId = window.blockId || $('input[name="block_id"]').val();
        if (!blockId) {
            return;
        }
        
        $.ajax({
            url: `/block-buildings/block/${blockId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Clear and repopulate DataTable
                    blockBuildingsDataTable.clear();
                    
                    data.data.forEach(function(building) {
                        blockBuildingsDataTable.row.add([
                            building.name || 'N/A',
                            building.building_type_name || 'N/A',
                            building.floor_no || 'N/A',
                            building.roof_type || 'N/A',
                            building.no_lift || 'N/A',
                            '<span class="badge bg-success">Active</span>',
                        `<button class="btn btn-sm btn-outline-primary" onclick="editBuilding(${building.id})" title="Edit Building">
                                <i class="ph-pencil"></i>
                            </button> 
                            <button class="btn btn-sm btn-outline-info" onclick="viewBuildingDetails(${building.id})" title="View Details">
                                <i class="ph-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="buildingShowDeleteConfirmation(${building.id}, {
                                name: '${building.name || 'N/A'}',
                                type: '${building.building_type_name || 'N/A'}',
                                floors: '${building.floor_no || 'N/A'}',
                                roof_type: '${building.roof_type || 'N/A'}'
                            })" title="Delete Building">
                                <i class="ph-trash"></i>
                            </button>`
                        ]);
                    });
                    
                    blockBuildingsDataTable.draw();
                    
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
     * Handles building form submission with AJAX
     * 
     * @param {string} modalId - The ID of the modal to close
     * @param {string} messageId - The ID of the message container
     * @param {string} successMessage - Success message to display
     * @param {string} errorMessage - Error message to display
     */
    function handleBuildingFormSubmission(modalId, messageId, successMessage, errorMessage) {
        const $form = $('#buildingForm');
        
        $form.off('submit').on('submit', function(e) {
            e.preventDefault();
            
            const $submitBtn = $('#buildingSubmitBtn');
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
                            refreshBlockBuildingsTable();
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
     * Opens the building modal for add or edit mode
     * 
     * @param {string} mode - The mode ('add' or 'edit')
     * @param {number} id - The building ID (for edit mode)
     */
    window.openBuildingModal = function(mode, id = null) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            return;
        }
        
        const $modal = $('#buildingModal');
        const $modalLabel = $('#buildingModalLabel');
        const $form = $('#buildingForm');
        const $submitBtn = $('#buildingSubmitBtn');
        
        if (mode === 'add') {
            // Add mode
            $modalLabel.text('Add Building');
            $submitBtn.html('<i class="ph-check me-1"></i> Save');
            $form.attr('action', window.routes?.blockBuildings?.store || '/block-buildings');
            $form.find('input[name="_method"]').remove(); // Remove PUT method for add
            $form[0].reset(); // Reset form
            
            // Initialize modal before showing
            initializeModal('buildingModal', 'building_type_id');
            
            // Modal is ready to show
            
            $modal.modal('show');
        } else if (mode === 'edit' && id) {
            // Edit mode - load building data
            loadBuildingForEdit(id);
        }
    };
    
    /**
     * Loads building data and populates the modal for editing
     * 
     * @param {number} id - The ID of the building to edit
     */
    function loadBuildingForEdit(id) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            return;
        }
        
        // Show loading state - try multiple selectors to find the edit button
        let $editBtn = $(`button[onclick="editBuilding(${id})"]`);
        if ($editBtn.length === 0) {
            // Try alternative selector
            $editBtn = $(`button:contains("Edit")`).filter(function() {
                return $(this).attr('onclick') && $(this).attr('onclick').includes(`editBuilding(${id})`);
            });
        }
        
        const originalText = $editBtn.length > 0 ? $editBtn.html() : 'Edit';
        if ($editBtn.length > 0) {
            $editBtn.html('<i class="ph-spinner ph-spin me-1"></i>Loading...').prop('disabled', true);
        }
        
        $.ajax({
            url: `/block-buildings/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const building = data.data;
                    const $modal = $('#buildingModal');
                    const $form = $('#buildingForm');
                    const $modalLabel = $('#buildingModalLabel');
                    const $submitBtn = $('#buildingSubmitBtn');
                    
                    // Update modal for edit mode
                    $modalLabel.text('Edit Building');
                    $submitBtn.html('<i class="ph-check me-1"></i> Update');
                    $form.attr('action', window.routes?.blockBuildings?.update?.replace(':id', id) || `/block-buildings/${id}`);
                    
                    // Add PUT method for edit
                    if ($form.find('input[name="_method"]').length === 0) {
                        $form.append('<input type="hidden" name="_method" value="PUT">');
                    }
                    
                    // Initialize modal first
                    initializeModal('buildingModal', 'building_type_id');
                    
                    // Populate form fields
                    $('#building_type_id').val(building.building_type_id);
                    $('#building_name').val(building.name);
                    $('#no_of_floors').val(building.floor_no);
                    $('#roof_type').val(building.roof_type);
                    $('#no_lift').val(building.no_lift);
                    
                    // Show the modal
                    $modal.modal('show');
                } else {
                    showMessage('buildingMessage', 'danger', 'Error loading building data');
                }
            },
            error: function(xhr, status, error) {
                showMessage('buildingMessage', 'danger', 'Error loading building data');
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
        clearMessage('buildingMessage');
    }
    
    // ========================================
    // DELETE CONFIRMATION MODAL
    // ========================================
    
    /**
     * Shows the delete confirmation modal with building details
     * 
     * @param {number} buildingId - The ID of the building to delete
     * @param {object} buildingData - The building data to display in confirmation
     */
    function buildingShowDeleteConfirmation(buildingId, buildingData) {
        
        // Populate building details in the modal
        const detailsHtml = `
            <div class="row">
                <div class="col-6"><strong>Building Name:</strong></div>
                <div class="col-6">${buildingData.name || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Type:</strong></div>
                <div class="col-6">${buildingData.type || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Floors:</strong></div>
                <div class="col-6">${buildingData.floors || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Roof Type:</strong></div>
                <div class="col-6">${buildingData.roof_type || 'N/A'}</div>
            </div>
        `;
        
        $('#deleteBuildingDetails').html(detailsHtml);
        
        // Set up the confirm button to actually delete
        $('#confirmDeleteBuildingBtn').off('click').on('click', function() {
            buildingDeleteBuilding(buildingId);
        });
        
        // Show the modal
        $('#deleteBuildingModal').modal('show');
    }
    
    /**
     * Deletes a building via AJAX
     * 
     * @param {number} buildingId - The ID of the building to delete
     */
    function buildingDeleteBuilding(buildingId) {
        
        // Show loading state
        const $confirmBtn = $('#confirmDeleteBuildingBtn');
        const originalText = $confirmBtn.html();
        $confirmBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Deleting...').prop('disabled', true);
        
        $.ajax({
            url: `/block-buildings/${buildingId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                
                // Hide the modal
                $('#deleteBuildingModal').modal('hide');
                
                // Show success message
                showMessage('buildingMessage', 'success', 'Building deleted successfully');
                
                // Refresh the table
                refreshBlockBuildingsTable();
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                
                // Show error message
                showMessage('buildingMessage', 'danger', 'Error deleting building. Please try again.');
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            }
        });
    }
    
    // Expose delete functions to global scope
    window.buildingShowDeleteConfirmation = buildingShowDeleteConfirmation;
    window.buildingConfirmDeletion = buildingShowDeleteConfirmation;
    window.buildingDeleteBuilding = buildingDeleteBuilding;
    
    /**
     * View building details in a modal
     * @param {number} id
     */
    window.viewBuildingDetails = function(id) {
        $.ajax({
            url: `/block-buildings/${id}`,
            method: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(resp) {
                if (resp && resp.success && resp.data) {
                    const b = resp.data;
                    $('#detail_building_name').text(b.name || 'N/A');
                    $('#detail_building_type').text((b.building_type && (b.building_type.name || b.building_type)) || b.building_type_name || 'N/A');
                    $('#detail_building_floors').text(b.floor_no ?? 'N/A');
                    $('#detail_building_lifts').text(b.no_lift ?? 'N/A');
                    $('#detail_building_roof').text(b.roof_type || 'N/A');
                    $('#buildingDetailsModal').modal('show');
                }
            }
        });
    }
    
    // ========================================
    // SIMPLE ONCHANGE HANDLERS
    // ========================================
    
    /**
     * Handles input validation for 3-digit limit
     * 
     * @param {HTMLElement} input - The input element to validate
     */
    function validateThreeDigits(input) {
        const value = input.value;
        if (value.length > 3) {
            input.value = value.slice(0, 3);
        }
        if (parseInt(value) > 999) {
            input.value = '999';
        }
    }
    
    // Add event listeners for input validation
    $('#no_of_floors, #no_lift').on('input', function() {
        validateThreeDigits(this);
    });
    
    // ========================================
    // TRIGGER FUNCTIONS
    // ========================================
    
    /**
     * Triggers a refresh of the buildings table
     * 
     * This function is debounced to prevent multiple simultaneous calls
     */
    function triggerBuildingsRefresh() {
        if (!$.fn.DataTable.isDataTable('#blockBuildingsTable')) {
            initializeDataTable();
        }
        if (typeof window.refreshBlockBuildingsTable === 'function') {
            window.refreshBlockBuildingsTable();
        } else {
            // If refresh function is not available, check current table data
            if (blockBuildingsDataTable) {
                const currentRowCount = blockBuildingsDataTable.rows().count();
                toggleExportButtons(currentRowCount > 0);
            }
        }
    }
    
    // Debounced trigger to avoid duplicate refreshes
    let buildingsRefreshTimer = null;
    function triggerBuildingsRefresh() {
        clearTimeout(buildingsRefreshTimer);
        buildingsRefreshTimer = setTimeout(function() {
            if (!$.fn.DataTable.isDataTable('#blockBuildingsTable')) {
                initializeDataTable();
            }
            if (typeof window.refreshBlockBuildingsTable === 'function') {
                window.refreshBlockBuildingsTable();
            } else {
                // If refresh function is not available, check current table data
                if (blockBuildingsDataTable) {
                    const currentRowCount = blockBuildingsDataTable.rows().count();
                    toggleExportButtons(currentRowCount > 0);
                }
            }
        }, 50);
    }
    
    // ========================================
    // EVENT LISTENERS AND INITIALIZATION
    // ========================================
    
    // Listen for Bootstrap tab shown event to refresh data when buildings tab becomes active
    $(document).on('shown.bs.tab', '#building-core-tab', function(e) {
        triggerBuildingsRefresh();
    });

    // If Buildings tab is already active on page load, refresh once to ensure data is loaded
    if ($('#building-core').hasClass('show') && $('#building-core').hasClass('active')) {
        triggerBuildingsRefresh();
    }
    
    // ========================================
    // MODAL EVENT HANDLERS
    // ========================================
    
    // Clear messages when building modal is opened
    $('#buildingModal').on('show.bs.modal', function() {
        // Use setTimeout to ensure DOM is ready before clearing message
        setTimeout(function() {
            clearMessage('buildingMessage');
        }, 50);
    });
    
    // ========================================
    // FORM SUBMISSION HANDLERS
    // ========================================
    
    // Handle building form submission
    handleBuildingFormSubmission('buildingModal', 'buildingMessage', 'Building saved successfully!', 'Error saving building. Please try again.');
    
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
 * Edit building function (for backward compatibility)
 * 
 * @param {number} id - The ID of the building to edit
 */
function editBuilding(id) {
    window.openBuildingModal('edit', id);
}

// Expose editBuilding to global scope for DataTable onclick handlers
window.editBuilding = editBuilding;

</script>
