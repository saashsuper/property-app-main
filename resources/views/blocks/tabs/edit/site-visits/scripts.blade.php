<script>
/**
 * Site Visits Management JavaScript Module
 * 
 * This module handles all site visit-related functionality including:
 * - Add/Edit site visit operations
 * - DataTable management with auto-refresh
 * - Form validation and AJAX submissions
 * - Delete confirmation with Bootstrap modal
 * - Export functionality with conditional button states
 * - Site visit details viewing
 */

$(document).ready(function() {
    // ========================================
    // GLOBAL VARIABLES
    // ========================================
    
    /** @var {DataTable} blockSiteVisitsDataTable - Global DataTable instance for site visits table */
    let blockSiteVisitsDataTable;
    
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
     * Initializes the DataTable for the site visits table
     * 
     * Sets up DataTable with responsive design, pagination, and custom language settings.
     * Prevents re-initialization if the table is already initialized.
     */
    function initializeDataTable() {
        if ($('#blockSiteVisitsTable').length) {
            // Check if DataTable is already initialized to prevent conflicts
            if (!$.fn.DataTable.isDataTable('#blockSiteVisitsTable')) {
                blockSiteVisitsDataTable = $('#blockSiteVisitsTable').DataTable({
                    responsive: true,           // Enable responsive design
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',             // Define table layout (l=length, f=filter/search, r=processing, t=table, i=info, p=pagination)
                    order: [[1, 'desc']],      // Default sort by visit date descending
                    columnDefs: [
                        { targets: [6], orderable: false } // Actions column (last column) not sortable
                    ],
                    pageLength: 10,            // Default page size
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Page size options
                    language: {
                        lengthMenu: "Show _MENU_ site visits per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ site visits",
                        infoEmpty: "Showing 0 to 0 of 0 site visits",
                        infoFiltered: "(filtered from _MAX_ total site visits)",
                        search: "Search site visits:",
                        searchPlaceholder: "Search by reference, user, reason...",
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
                const initialRowCount = blockSiteVisitsDataTable.rows().count();
                toggleExportButtons(initialRowCount > 0);
            }
        }
    }
    
    // ========================================
    // DATA REFRESH FUNCTIONALITY
    // ========================================
    
    /**
     * Refreshes the block site visits DataTable with fresh data from the server
     * 
     * This function is exposed globally so it can be called from other parts of the application.
     * It fetches the latest site visit data for the current block and updates the DataTable.
     * 
     * @global
     */
    window.refreshBlockSiteVisitsTable = function() {
        if (!blockSiteVisitsDataTable) {
            return;
        }
        
        const blockId = window.blockId || $('input[name="block_id"]').val();
        if (!blockId) {
            return;
        }
        
        $.ajax({
            url: `/block-visits/block/${blockId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Clear and repopulate DataTable
                    blockSiteVisitsDataTable.clear();
                    
                    data.data.forEach(function(visit) {
                        // Determine status
                        let status = 'Scheduled';
                        if (visit.end_date_time) {
                            status = 'Completed';
                        } else if (visit.start_date_time) {
                            status = 'In Progress';
                        }
                        
                        const statusBadge = status === 'Completed' 
                            ? '<span class="badge bg-success">Completed</span>'
                            : status === 'In Progress' 
                            ? '<span class="badge bg-warning">In Progress</span>'
                            : '<span class="badge bg-info">Scheduled</span>';
                        
                        const scheduledDateTime = visit.scheduled_date_time 
                            ? new Date(visit.scheduled_date_time).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                            : 'N/A';
                        
                        const notes = visit.notes && visit.notes.length > 50 
                            ? visit.notes.substring(0, 50) + '...' 
                            : visit.notes || 'N/A';
                        
                        blockSiteVisitsDataTable.row.add([
                            `<a href="#" class="text-primary fw-bold view-site-visit-details" data-visit-id="${visit.id}" style="text-decoration: none;">${visit.ref_no || 'N/A'}</a>`,
                            scheduledDateTime,
                            visit.user_name || 'N/A',
                            visit.job_reason_name || 'N/A',
                            statusBadge,
                            notes,
                            `<button class="btn btn-sm btn-outline-primary" onclick="editSiteVisit(${visit.id})" title="Edit Site Visit">
                                <i class="ph-pencil"></i>
                            </button> 
                            <button class="btn btn-sm btn-outline-danger" onclick="blockSiteVisitShowDeleteConfirmation(${visit.id}, {
                                ref_no: '${visit.ref_no || 'N/A'}',
                                scheduled_date: '${scheduledDateTime}',
                                user: '${visit.user_name || 'N/A'}',
                                reason: '${visit.job_reason_name || 'N/A'}',
                                status: '${status}'
                            })" title="Delete Site Visit">
                                <i class="ph-trash"></i>
                            </button>`
                        ]);
                    });
                    
                    blockSiteVisitsDataTable.draw();
                    
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
     * Handles site visit form submission with AJAX
     * 
     * @param {string} modalId - The ID of the modal to close
     * @param {string} messageId - The ID of the message container
     * @param {string} successMessage - Success message to display
     * @param {string} errorMessage - Error message to display
     */
    function handleSiteVisitFormSubmission(modalId, messageId, successMessage, errorMessage) {
        const $form = $('#siteVisitForm');
        
        $form.off('submit').on('submit', function(e) {
            e.preventDefault();
            
            const $submitBtn = $('#siteVisitSubmitBtn');
            const originalText = $submitBtn.html();
            $submitBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Saving...').prop('disabled', true);
            
            // Combine date and time for scheduled_date_time
            const scheduledDate = $('#scheduled_date').val();
            const scheduledTime = $('#scheduled_time').val();
            const scheduledDateTime = scheduledDate + ' ' + scheduledTime;
            
            // Determine if this is an edit operation
            const isEdit = $form.find('input[name="_method"]').length > 0;
            const method = isEdit ? 'PUT' : 'POST';
            
            const formData = {
                block_id: window.blockId,
                user_id: $('#user_id').val(),
                scheduled_date_time: scheduledDateTime,
                job_reason_id: $('#job_reason_id').val(),
                notes: $('#notes').val(),
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
                            refreshBlockSiteVisitsTable();
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
                    
                    let errorMessage = 'Error saving site visit. Please try again.';
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
     * Opens the site visit modal for add or edit mode
     * 
     * @param {string} mode - The mode ('add' or 'edit')
     * @param {number} id - The site visit ID (for edit mode)
     */
    window.openSiteVisitModal = function(mode, id = null) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            return;
        }
        
        const $modal = $('#siteVisitModal');
        const $modalLabel = $('#siteVisitModalLabel');
        const $form = $('#siteVisitForm');
        const $submitBtn = $('#siteVisitSubmitBtn');
        
        if (mode === 'add') {
            // Add mode
            $modalLabel.text('Add Site Visit');
            $submitBtn.html('<i class="ph-check me-1"></i> Save');
            $form.attr('action', window.routes?.blockSiteVisits?.store || '/block-visits');
            $form.find('input[name="_method"]').remove(); // Remove PUT method for add
            $form[0].reset(); // Reset form
            
            // Initialize modal before showing
            initializeModal('siteVisitModal', 'user_id');
            
            // Modal is ready to show
            $modal.modal('show');
        } else if (mode === 'edit' && id) {
            // Edit mode - load site visit data
            loadSiteVisitForEdit(id);
        }
    };
    
    /**
     * Loads site visit data and populates the modal for editing
     * 
     * @param {number} id - The ID of the site visit to edit
     */
    function loadSiteVisitForEdit(id) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            return;
        }
        
        // Show loading state
        let $editBtn = $(`button[onclick="editSiteVisit(${id})"]`);
        if ($editBtn.length === 0) {
            $editBtn = $(`button:contains("Edit")`).filter(function() {
                return $(this).attr('onclick') && $(this).attr('onclick').includes(`editSiteVisit(${id})`);
            });
        }
        
        const originalText = $editBtn.length > 0 ? $editBtn.html() : 'Edit';
        if ($editBtn.length > 0) {
            $editBtn.html('<i class="ph-spinner ph-spin me-1"></i>Loading...').prop('disabled', true);
        }
        
        $.ajax({
            url: `/block-visits/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const visit = data.data;
                    const $modal = $('#siteVisitModal');
                    const $form = $('#siteVisitForm');
                    const $modalLabel = $('#siteVisitModalLabel');
                    const $submitBtn = $('#siteVisitSubmitBtn');
                    
                    // Update modal for edit mode
                    $modalLabel.text('Edit Site Visit');
                    $submitBtn.html('<i class="ph-check me-1"></i> Update');
                    $form.attr('action', window.routes?.blockSiteVisits?.update?.replace(':id', id) || `/block-visits/${id}`);
                    
                    // Add PUT method for edit
                    if ($form.find('input[name="_method"]').length === 0) {
                        $form.append('<input type="hidden" name="_method" value="PUT">');
                    }
                    
                    // Initialize modal first
                    initializeModal('siteVisitModal', 'user_id');
                    
                    // Get assigned user
                    let assignedUser = null;
                    if (visit.team && visit.team.length > 0) {
                        assignedUser = visit.team[0].user_id;
                    } else if (visit.created_by) {
                        assignedUser = visit.created_by;
                    }
                    
                    // Populate form fields
                    $('#user_id').val(assignedUser || '');
                    
                    // Parse scheduled date time
                    if (visit.scheduled_date_time) {
                        const scheduledDateTime = new Date(visit.scheduled_date_time);
                        $('#scheduled_date').val(scheduledDateTime.toISOString().split('T')[0]);
                        $('#scheduled_time').val(scheduledDateTime.toTimeString().slice(0, 5));
                    }
                    
                    $('#job_reason_id').val(visit.job_reason_id);
                    $('#notes').val(visit.notes);
                    
                    // Show the modal
                    $modal.modal('show');
                } else {
                    showMessage('siteVisitMessage', 'danger', 'Error loading site visit data');
                }
            },
            error: function(xhr, status, error) {
                showMessage('siteVisitMessage', 'danger', 'Error loading site visit data');
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
        clearMessage('siteVisitMessage');
    }
    
    // ========================================
    // DELETE CONFIRMATION MODAL
    // ========================================
    
    /**
     * Shows the delete confirmation modal with site visit details
     * 
     * @param {number} visitId - The ID of the site visit to delete
     * @param {object} visitData - The site visit data to display in confirmation
     */
    function blockSiteVisitShowDeleteConfirmation(visitId, visitData) {
        
        // Populate site visit details in the modal
        const detailsHtml = `
            <div class="row">
                <div class="col-6"><strong>Reference:</strong></div>
                <div class="col-6">${visitData.ref_no || visitData.reference || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Scheduled Date:</strong></div>
                <div class="col-6">${visitData.visit_date || visitData.scheduled_date || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Visit Time:</strong></div>
                <div class="col-6">${visitData.visit_time || visitData.scheduled_time || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Visit Type:</strong></div>
                <div class="col-6">${visitData.visit_type || visitData.reason || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>User:</strong></div>
                <div class="col-6">${visitData.user || visitData.assigned_user || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Status:</strong></div>
                <div class="col-6">${visitData.status || 'N/A'}</div>
            </div>
        `;
        
        $('#deleteSiteVisitDetails').html(detailsHtml);
        
        // Set up the confirm button to actually delete
        $('#confirmDeleteSiteVisitBtn').off('click').on('click', function() {
            blockSiteVisitDeleteSiteVisit(visitId);
        });
        
        // Show the modal
        $('#deleteSiteVisitModal').modal('show');
    }
    
    /**
     * Deletes a site visit via AJAX
     * 
     * @param {number} visitId - The ID of the site visit to delete
     */
    function blockSiteVisitDeleteSiteVisit(visitId) {
        
        // Show loading state
        const $confirmBtn = $('#confirmDeleteSiteVisitBtn');
        const originalText = $confirmBtn.html();
        $confirmBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Deleting...').prop('disabled', true);
        
        $.ajax({
            url: `/block-visits/${visitId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                
                // Hide the modal
                $('#deleteSiteVisitModal').modal('hide');
                
                // Show success message
                showMessage('siteVisitMessage', 'success', 'Site visit deleted successfully');
                
                // Refresh the table
                refreshBlockSiteVisitsTable();
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                
                // Show error message
                showMessage('siteVisitMessage', 'danger', 'Error deleting site visit. Please try again.');
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            }
        });
    }
    
    // Expose delete functions to global scope
    window.blockSiteVisitShowDeleteConfirmation = blockSiteVisitShowDeleteConfirmation;
    window.blockSiteVisitConfirmDeletion = blockSiteVisitShowDeleteConfirmation;
    window.blockSiteVisitDeleteSiteVisit = blockSiteVisitDeleteSiteVisit;
    
    // ========================================
    // SITE VISIT DETAILS MODAL
    // ========================================
    
    /**
     * Shows site visit details in a modal
     * 
     * @param {number} visitId - The ID of the site visit to show details for
     */
    function showSiteVisitDetails(visitId) {
        $.ajax({
            url: `/block-visits/${visitId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const visit = data.data;
                    
                    // Populate modal with visit details
                    $('#detail_ref_no').text(visit.ref_no || 'N/A');
                    
                    // Determine status
                    let status = 'Scheduled';
                    if (visit.end_date_time) {
                        status = 'Completed';
                    } else if (visit.start_date_time) {
                        status = 'In Progress';
                    }
                    $('#detail_status').html(`<span class="badge bg-${status === 'Completed' ? 'success' : status === 'In Progress' ? 'warning' : 'info'}">${status}</span>`);
                    
                    // Format scheduled date time
                    const scheduledDateTime = visit.scheduled_date_time ? new Date(visit.scheduled_date_time).toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : 'N/A';
                    $('#detail_scheduled_date_time').text(scheduledDateTime);
                    
                    // Get assigned user
                    let assignedUser = 'N/A';
                    if (visit.team && visit.team.length > 0) {
                        assignedUser = visit.team[0].user ? visit.team[0].user.name : 'N/A';
                    } else if (visit.createdByUser) {
                        assignedUser = visit.createdByUser.name;
                    }
                    $('#detail_user').text(assignedUser);
                    
                    // Job reason
                    $('#detail_job_reason').text(visit.jobReason ? visit.jobReason.name : 'N/A');
                    
                    // Created by
                    $('#detail_created_by').text(visit.createdByUser ? visit.createdByUser.name : 'N/A');
                    
                    // Start date time
                    const startDateTime = visit.start_date_time ? new Date(visit.start_date_time).toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : 'N/A';
                    $('#detail_start_date_time').text(startDateTime);
                    
                    // End date time
                    const endDateTime = visit.end_date_time ? new Date(visit.end_date_time).toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : 'N/A';
                    $('#detail_end_date_time').text(endDateTime);
                    
                    // Notes
                    $('#detail_notes').text(visit.notes || 'N/A');
                    
                    // Comments
                    $('#detail_comments').text(visit.comment || 'N/A');
                    
                    // Show modal
                    $('#siteVisitDetailsModal').modal('show');
                } else {
                    showMessage('siteVisitMessage', 'danger', 'Error loading site visit details');
                }
            },
            error: function(xhr, status, error) {
                showMessage('siteVisitMessage', 'danger', 'Error loading site visit details');
            }
        });
    }
    
    // ========================================
    // TRIGGER FUNCTIONS
    // ========================================
    
    /**
     * Triggers a refresh of the site visits table
     * 
     * This function is debounced to prevent multiple simultaneous calls
     */
    function triggerSiteVisitsRefresh() {
        if (!$.fn.DataTable.isDataTable('#blockSiteVisitsTable')) {
            initializeDataTable();
        }
        if (typeof window.refreshBlockSiteVisitsTable === 'function') {
            window.refreshBlockSiteVisitsTable();
        } else {
            // If refresh function is not available, check current table data
            if (blockSiteVisitsDataTable) {
                const currentRowCount = blockSiteVisitsDataTable.rows().count();
                toggleExportButtons(currentRowCount > 0);
            }
        }
    }
    
    // Debounced trigger to avoid duplicate refreshes
    let siteVisitsRefreshTimer = null;
    function triggerSiteVisitsRefresh() {
        clearTimeout(siteVisitsRefreshTimer);
        siteVisitsRefreshTimer = setTimeout(function() {
            if (!$.fn.DataTable.isDataTable('#blockSiteVisitsTable')) {
                initializeDataTable();
            }
            if (typeof window.refreshBlockSiteVisitsTable === 'function') {
                window.refreshBlockSiteVisitsTable();
            } else {
                // If refresh function is not available, check current table data
                if (blockSiteVisitsDataTable) {
                    const currentRowCount = blockSiteVisitsDataTable.rows().count();
                    toggleExportButtons(currentRowCount > 0);
                }
            }
        }, 50);
    }
    
    // ========================================
    // EVENT LISTENERS AND INITIALIZATION
    // ========================================
    
    // Listen for Bootstrap tab shown event to refresh data when site visits tab becomes active
    $(document).on('shown.bs.tab', '#site-visit-tab', function(e) {
        triggerSiteVisitsRefresh();
    });

    // If Site Visits tab is already active on page load, refresh once to ensure data is loaded
    if ($('#site-visit').hasClass('show') && $('#site-visit').hasClass('active')) {
        triggerSiteVisitsRefresh();
    }
    
    // ========================================
    // MODAL EVENT HANDLERS
    // ========================================
    
    // Clear messages when site visit modal is opened
    $('#siteVisitModal').on('show.bs.modal', function() {
        // Use setTimeout to ensure DOM is ready before clearing message
        setTimeout(function() {
            clearMessage('siteVisitMessage');
        }, 50);
    });
    
    // ========================================
    // FORM SUBMISSION HANDLERS
    // ========================================
    
    // Handle site visit form submission
    handleSiteVisitFormSubmission('siteVisitModal', 'siteVisitMessage', 'Site visit saved successfully!', 'Error saving site visit. Please try again.');
    
    // ========================================
    // CLICK HANDLERS
    // ========================================
    
    // Handle view site visit details clicks
    $(document).on('click', '.view-site-visit-details', function(e) {
        e.preventDefault();
        const visitId = $(this).data('visit-id');
        showSiteVisitDetails(visitId);
    });
    
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
 * Edit site visit function (for backward compatibility)
 * 
 * @param {number} id - The ID of the site visit to edit
 */
function editSiteVisit(id) {
    window.openSiteVisitModal('edit', id);
}

// Expose editSiteVisit to global scope for DataTable onclick handlers
window.editSiteVisit = editSiteVisit;

</script>
