<script>
/**
 * Block Information Management JavaScript Module
 * 
 * This module handles all block information-related functionality including:
 * - Add/Edit block information operations
 * - DataTable management with auto-refresh
 * - Form validation and AJAX submissions
 * - Delete confirmation with Bootstrap modal
 * - Export functionality with conditional button states
 * - Block information details viewing
 */

$(document).ready(function() {
    // ========================================
    // GLOBAL VARIABLES
    // ========================================
    
    /** @var {DataTable} blockInformationDataTable - Global DataTable instance for block information table */
    let blockInformationDataTable;
    
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
     * Initializes the DataTable for the block information table
     * 
     * Sets up DataTable with responsive design, pagination, and custom language settings.
     * Prevents re-initialization if the table is already initialized.
     */
    function initializeDataTable() {
        if ($('#blockInformationTable').length) {
            // Check if DataTable is already initialized to prevent conflicts
            if (!$.fn.DataTable.isDataTable('#blockInformationTable')) {
                blockInformationDataTable = $('#blockInformationTable').DataTable({
                    responsive: true,           // Enable responsive design
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
                    order: [[2, 'desc']],      // Default sort by added date descending
                    columnDefs: [
                        { targets: [4], orderable: false } // Actions column (last column) not sortable
                    ],
                    pageLength: 25,            // Default page size
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Page size options
                    language: {
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No entries found",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        search: "",
                        searchPlaceholder: "Search information...",
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
                const initialRowCount = blockInformationDataTable.rows().count();
                toggleExportButtons(initialRowCount > 0);
            }
        }
    }
    
    // ========================================
    // DATA REFRESH FUNCTIONALITY
    // ========================================
    
    /**
     * Refreshes the block information DataTable with fresh data from the server
     * 
     * This function is exposed globally so it can be called from other parts of the application.
     * It fetches the latest block information data for the current block and updates the DataTable.
     * 
     * @global
     */
    window.refreshBlockInformationTable = function() {
        if (!blockInformationDataTable) {
            return;
        }
        
        const blockId = window.blockId || $('input[name="block_id"]').val();
        if (!blockId) {
            return;
        }
        
        $.ajax({
            url: window.routes?.blockInformation?.getByBlock || `/block-information/block/${blockId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Clear and repopulate DataTable
                    blockInformationDataTable.clear();
                    
                    data.data.forEach(function(info) {
                        const addedDate = info.created_at 
                            ? new Date(info.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: '2-digit'
                            })
                            : 'N/A';
                        
                        const description = info.description && info.description.length > 50 
                            ? info.description.substring(0, 50) + '...' 
                            : info.description || 'No description provided';
                        
                        blockInformationDataTable.row.add([
                            `<span class="fw-semibold">${info.information_type?.name || 'N/A'}</span>`,
                            description,
                            addedDate,
                            info.creator?.name || 'N/A',
                            `<button class="btn btn-sm btn-outline-primary" onclick="editBlockInformation(${info.id})" title="Edit Block Information">
                                <i class="ph-pencil"></i>
                            </button> 
                            <button class="btn btn-sm btn-outline-info" onclick="viewBlockInformationDetails(${info.id})" title="View Details">
                                <i class="ph-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="blockInformationShowDeleteConfirmation(${info.id}, {
                                type: '${info.information_type?.name || 'N/A'}',
                                description: '${description}',
                                added_date: '${addedDate}',
                                added_by: '${info.creator?.name || 'N/A'}'
                            })" title="Delete Block Information">
                                <i class="ph-trash"></i>
                            </button>`
                        ]);
                    });
                    
                    blockInformationDataTable.draw();
                    
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
     * Handles block information form submission with AJAX
     * 
     * @param {string} modalId - The ID of the modal to close
     * @param {string} messageId - The ID of the message container
     * @param {string} successMessage - Success message to display
     * @param {string} errorMessage - Error message to display
     */
    function handleBlockInformationFormSubmission(modalId, messageId, successMessage, errorMessage) {
        const $form = $('#blockInformationForm');
        
        $form.off('submit').on('submit', function(e) {
            e.preventDefault();
            
            const $submitBtn = $('#blockInformationSubmitBtn');
            const originalText = $submitBtn.html();
            $submitBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Saving...').prop('disabled', true);
            
            // Determine if this is an edit operation
            const isEdit = $form.find('input[name="_method"]').length > 0;
            const method = isEdit ? 'PUT' : 'POST';
            
            const formData = {
                block_id: window.blockId,
                information_type_id: $('#information_type_id').val(),
                description: $('#description').val(),
                _token: window.csrfToken
            };
            
            // Add _method field for PUT requests
            if (isEdit) {
                formData._method = 'PUT';
            }
            
            console.log('Form data being sent:', formData);
            console.log('Form action URL:', $form.attr('action'));
            console.log('Is edit mode:', isEdit);
            
            $.ajax({
                url: $form.attr('action'),
                method: 'POST', // Always use POST for Laravel form spoofing
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    console.log('Success response:', data);
                    if (data.success) {
                        showMessage(messageId, 'success', successMessage);
                        $form[0].reset();
                        
                        setTimeout(function() {
                            $('#' + modalId).modal('hide');
                            refreshBlockInformationTable();
                        }, 800);
                    } else {
                        console.error('Success but data.success is false:', data);
                        showMessage(messageId, 'danger', (data && data.message) || errorMessage);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    console.error('Status:', status);
                    console.error('Error:', error);
                    
                    let errorMessage = 'Error saving block information. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('Could not parse error response:', e);
                        }
                    }
                    
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
     * Opens the block information modal for add or edit mode
     * 
     * @param {string} mode - The mode ('add' or 'edit')
     * @param {number} id - The block information ID (for edit mode)
     */
    window.openBlockInformationModal = function(mode, id = null) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            return;
        }
        
        const $modal = $('#blockInformationModal');
        const $modalLabel = $('#blockInformationModalLabel');
        const $form = $('#blockInformationForm');
        const $submitBtn = $('#blockInformationSubmitBtn');
        
        if (mode === 'add') {
            // Add mode
            $modalLabel.text('Add Block Information');
            $submitBtn.html('<i class="ph-check me-1"></i> Save');
            $form.attr('action', window.routes?.blockInformation?.store || '/block-information');
            $form.find('input[name="_method"]').remove(); // Remove PUT method for add
            $form[0].reset(); // Reset form
            
            // Initialize modal before showing
            initializeModal('blockInformationModal');
            
            // Modal is ready to show
            $modal.modal('show');
        } else if (mode === 'edit' && id) {
            // Edit mode - load block information data
            loadBlockInformationForEdit(id);
        }
    };
    
    /**
     * Loads block information data and populates the modal for editing
     * 
     * @param {number} id - The ID of the block information to edit
     */
    function loadBlockInformationForEdit(id) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            return;
        }
        
        // Show loading state
        let $editBtn = $(`button[onclick="editBlockInformation(${id})"]`);
        if ($editBtn.length === 0) {
            $editBtn = $(`button:contains("Edit")`).filter(function() {
                return $(this).attr('onclick') && $(this).attr('onclick').includes(`editBlockInformation(${id})`);
            });
        }
        
        const originalText = $editBtn.length > 0 ? $editBtn.html() : 'Edit';
        if ($editBtn.length > 0) {
            $editBtn.html('<i class="ph-spinner ph-spin me-1"></i>Loading...').prop('disabled', true);
        }
        
        $.ajax({
            url: window.routes?.blockInformation?.show?.replace(':id', id) || `/block-information/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const info = data.data;
                    const $modal = $('#blockInformationModal');
                    const $form = $('#blockInformationForm');
                    const $modalLabel = $('#blockInformationModalLabel');
                    const $submitBtn = $('#blockInformationSubmitBtn');
                    
                    // Update modal for edit mode
                    $modalLabel.text('Edit Block Information');
                    $submitBtn.html('<i class="ph-check me-1"></i> Update');
                    $form.attr('action', window.routes?.blockInformation?.update?.replace(':id', id) || `/block-information/${id}`);
                    
                    // Add PUT method for edit
                    if ($form.find('input[name="_method"]').length === 0) {
                        $form.append('<input type="hidden" name="_method" value="PUT">');
                    }
                    
                    // Initialize modal first
                    initializeModal('blockInformationModal');
                    
                    // Populate form fields
                    $('#information_type_id').val(info.information_type_id || '');
                    $('#description').val(info.description || '');
                    
                    // Show the modal
                    $modal.modal('show');
                } else {
                    showMessage('blockInformationMessage', 'danger', 'Error loading block information data');
                }
            },
            error: function(xhr, status, error) {
                showMessage('blockInformationMessage', 'danger', 'Error loading block information data');
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
     */
    function initializeModal(modalId) {
        // Clear any previous messages
        clearMessage('blockInformationMessage');
    }
    
    // ========================================
    // DELETE CONFIRMATION MODAL
    // ========================================
    
    /**
     * Shows the delete confirmation modal with block information details
     * 
     * @param {number} infoId - The ID of the block information to delete
     * @param {object} infoData - The block information data to display in confirmation
     */
    function blockInformationShowDeleteConfirmation(infoId, infoData) {
        
        // Populate block information details in the modal
        const detailsHtml = `
            <div class="row">
                <div class="col-6"><strong>Type:</strong></div>
                <div class="col-6">${infoData.type || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Description:</strong></div>
                <div class="col-6">${infoData.description || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Added Date:</strong></div>
                <div class="col-6">${infoData.added_date || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Added By:</strong></div>
                <div class="col-6">${infoData.added_by || 'N/A'}</div>
            </div>
        `;
        
        $('#deleteBlockInformationDetails').html(detailsHtml);
        
        // Set up the confirm button to actually delete
        $('#confirmDeleteBlockInformationBtn').off('click').on('click', function() {
            deleteBlockInformation(infoId);
        });
        
        // Show the modal
        $('#deleteBlockInformationModal').modal('show');
    }
    
    /**
     * Deletes a block information via AJAX
     * 
     * @param {number} infoId - The ID of the block information to delete
     */
    function deleteBlockInformation(infoId) {
        
        // Show loading state
        const $confirmBtn = $('#confirmDeleteBlockInformationBtn');
        const originalText = $confirmBtn.html();
        $confirmBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Deleting...').prop('disabled', true);
        
        $.ajax({
            url: window.routes?.blockInformation?.destroy?.replace(':id', infoId) || `/block-information/${infoId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                
                // Hide the modal
                $('#deleteBlockInformationModal').modal('hide');
                
                // Show success message
                showMessage('blockInformationMessage', 'success', 'Block information deleted successfully');
                
                // Refresh the table
                refreshBlockInformationTable();
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                
                // Show error message
                showMessage('blockInformationMessage', 'danger', 'Error deleting block information. Please try again.');
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            }
        });
    }
    
    // Expose delete functions to global scope
    window.blockInformationShowDeleteConfirmation = blockInformationShowDeleteConfirmation;
    window.blockInformationConfirmDeletion = blockInformationShowDeleteConfirmation;
    window.deleteBlockInformation = deleteBlockInformation;
    
    // ========================================
    // BLOCK INFORMATION DETAILS MODAL
    // ========================================
    
    /**
     * Shows block information details in a modal
     * 
     * @param {number} infoId - The ID of the block information to show details for
     */
    function viewBlockInformationDetails(infoId) {
        $.ajax({
            url: window.routes?.blockInformation?.show?.replace(':id', infoId) || `/block-information/${infoId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const info = data.data;
                    
                    // Populate modal with information details
                    $('#detail_information_type').text(info.information_type?.name || 'N/A');
                    
                    // Format added date
                    const addedDate = info.created_at ? new Date(info.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    }) : 'N/A';
                    $('#detail_added_date').text(addedDate);
                    
                    // Added by
                    $('#detail_added_by').text(info.creator?.name || 'N/A');
                    
                    // Last updated
                    const updatedAt = info.updated_at ? new Date(info.updated_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : 'N/A';
                    $('#detail_updated_at').text(updatedAt);
                    
                    // Description
                    $('#detail_description').text(info.description || 'N/A');
                    
                    // Show modal
                    $('#blockInformationDetailsModal').modal('show');
                } else {
                    showMessage('blockInformationMessage', 'danger', 'Error loading block information details');
                }
            },
            error: function(xhr, status, error) {
                showMessage('blockInformationMessage', 'danger', 'Error loading block information details');
            }
        });
    }
    
    // Expose view details function to global scope
    window.viewBlockInformationDetails = viewBlockInformationDetails;
    
    // ========================================
    // TRIGGER FUNCTIONS
    // ========================================
    
    /**
     * Triggers a refresh of the block information table
     * 
     * This function is debounced to prevent multiple simultaneous calls
     */
    function triggerBlockInformationRefresh() {
        if (!$.fn.DataTable.isDataTable('#blockInformationTable')) {
            initializeDataTable();
        }
        if (typeof window.refreshBlockInformationTable === 'function') {
            window.refreshBlockInformationTable();
        } else {
            // If refresh function is not available, check current table data
            if (blockInformationDataTable) {
                const currentRowCount = blockInformationDataTable.rows().count();
                toggleExportButtons(currentRowCount > 0);
            }
        }
    }
    
    // Debounced trigger to avoid duplicate refreshes
    let blockInformationRefreshTimer = null;
    function triggerBlockInformationRefresh() {
        clearTimeout(blockInformationRefreshTimer);
        blockInformationRefreshTimer = setTimeout(function() {
            if (!$.fn.DataTable.isDataTable('#blockInformationTable')) {
                initializeDataTable();
            }
            if (typeof window.refreshBlockInformationTable === 'function') {
                window.refreshBlockInformationTable();
            } else {
                // If refresh function is not available, check current table data
                if (blockInformationDataTable) {
                    const currentRowCount = blockInformationDataTable.rows().count();
                    toggleExportButtons(currentRowCount > 0);
                }
            }
        }, 50);
    }
    
    // ========================================
    // EVENT LISTENERS AND INITIALIZATION
    // ========================================
    
    // Listen for Bootstrap tab shown event to refresh data when block information tab becomes active
    $(document).on('shown.bs.tab', '#block-info-tab', function(e) {
        triggerBlockInformationRefresh();
    });

    // If Block Information tab is already active on page load, refresh once to ensure data is loaded
    if ($('#block-info').hasClass('show') && $('#block-info').hasClass('active')) {
        triggerBlockInformationRefresh();
    }
    
    // ========================================
    // MODAL EVENT HANDLERS
    // ========================================
    
    // Clear messages when block information modal is opened
    $('#blockInformationModal').on('show.bs.modal', function() {
        // Use setTimeout to ensure DOM is ready before clearing message
        setTimeout(function() {
            clearMessage('blockInformationMessage');
        }, 50);
    });
    
    // ========================================
    // FORM SUBMISSION HANDLERS
    // ========================================
    
    // Handle block information form submission
    handleBlockInformationFormSubmission('blockInformationModal', 'blockInformationMessage', 'Block information saved successfully!', 'Error saving block information. Please try again.');
    
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
 * Edit block information function (for backward compatibility)
 * 
 * @param {number} id - The ID of the block information to edit
 */
function editBlockInformation(id) {
    window.openBlockInformationModal('edit', id);
}

// Expose editBlockInformation to global scope for DataTable onclick handlers
window.editBlockInformation = editBlockInformation;

</script>

