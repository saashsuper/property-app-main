<script>
/**
 * Issues Management JavaScript Module
 * 
 * This module handles all issue-related functionality including:
 * - Add/Edit issue operations
 * - DataTable management with auto-refresh
 * - Advanced search and filtering
 * - Form validation and AJAX submissions
 * - Delete confirmation with Bootstrap modal
 * - Export functionality with conditional button states
 */

$(document).ready(function() {
    // ========================================
    // GLOBAL VARIABLES
    // ========================================
    
    /** @var {DataTable} blockIssuesDataTable - Global DataTable instance for issues table */
    let blockIssuesDataTable;
    
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
     * Initializes the DataTable for the issues table
     * 
     * Sets up DataTable with responsive design, pagination, and custom language settings.
     * Prevents re-initialization if the table is already initialized.
     */
    function initializeDataTable() {
        if ($('#blockIssuesTable').length) {
            // Check if DataTable is already initialized to prevent conflicts
            if (!$.fn.DataTable.isDataTable('#blockIssuesTable')) {
                blockIssuesDataTable = $('#blockIssuesTable').DataTable({
                    responsive: true,           // Enable responsive design
                    dom: 'lfrtip',             // Define table layout (l=length, f=filter/search, r=processing, t=table, i=info, p=pagination)
                    order: [[0, 'desc']],      // Default sort by first column (Issue ID) descending
                    columnDefs: [
                        { targets: [5], orderable: false } // Actions column (last column) not sortable
                    ],
                    pageLength: 10,            // Default page size
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Page size options
                    language: {
                        lengthMenu: "Show _MENU_ issues per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ issues",
                        infoEmpty: "Showing 0 to 0 of 0 issues",
                        infoFiltered: "(filtered from _MAX_ total issues)",
                        search: "Search issues:",
                        searchPlaceholder: "Search by issue ID, title, priority, status...",
                        paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
                    },
                    initComplete: function() {
                        // Style the search box to match other tabs
                        $('.dataTables_filter input')
                            .addClass('form-control custom-search-input mb-3')
                            .css({
                                'width': '300px',
                                'height': '38px',
                                'font-size': '14px'
                            });
                        
                        // Style the page length dropdown
                        $('.dataTables_length select')
                            .addClass('form-select custom-page-length-select')
                            .css({
                                'width': 'auto',
                                'height': '38px',
                                'font-size': '14px'
                            });
                    }
                });
                
                // Check initial data and toggle export buttons
                const initialRowCount = blockIssuesDataTable.rows().count();
                toggleExportButtons(initialRowCount > 0);
            }
        }
    }
    
    // ========================================
    // DATA REFRESH FUNCTIONALITY
    // ========================================
    
    /**
     * Refreshes the block issues DataTable with fresh data from the server
     * 
     * This function is exposed globally so it can be called from other parts of the application.
     * It fetches the latest issue data for the current block and updates the DataTable.
     * 
     * @global
     */
    window.refreshBlockIssuesTable = function() {
        if (!blockIssuesDataTable) {
            return;
        }
        
        const blockId = window.blockId || $('input[name="block_id"]').val();
        if (!blockId) {
            return;
        }
        
        $.ajax({
            url: `/block-issues?block_id=${blockId}&type=api`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Clear and repopulate DataTable
                    blockIssuesDataTable.clear();
                    
                    data.data.data.forEach(function(issue) {
                        blockIssuesDataTable.row.add([
                            `<a href="/block-issues/${issue.id}" class="text-decoration-none"><b>#${issue.ref_no}</b></a>`,
                            issue.issue || 'N/A',
                            getPriorityBadge(issue.priority_id),
                            getStatusBadge(issue.issue_status_id),
                            formatDate(issue.created_at),
                            `<button class="btn btn-sm btn-outline-primary" onclick="editIssue(${issue.id})" title="Edit Issue">
                                <i class="ph-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="openPhotoUploadModal(${issue.id})" title="Upload Photos">
                                <i class="ph-camera"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="showDeleteConfirmation(${issue.id}, {
                                ref_no: '${issue.ref_no || 'N/A'}',
                                issue: '${issue.issue || 'N/A'}',
                                priority: '${issue.priority_id || 'N/A'}',
                                status: '${issue.issue_status_id || 'N/A'}'
                            })" title="Delete Issue">
                                <i class="ph-trash"></i>
                            </button>`
                        ]);
                    });
                    
                    blockIssuesDataTable.draw();
                    
                    // Toggle export buttons based on data availability
                    toggleExportButtons(data.data.data.length > 0);
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
     * Handles issue form submission with AJAX
     * 
     * @param {string} modalId - The ID of the modal to close
     * @param {string} messageId - The ID of the message container
     * @param {string} successMessage - Success message to display
     * @param {string} errorMessage - Error message to display
     */
    function handleIssueFormSubmission(modalId, messageId, successMessage, errorMessage) {
        const $form = $('#issueForm');
        
        $form.off('submit').on('submit', function(e) {
            e.preventDefault();
            
            const $submitBtn = $('#issueSubmitBtn');
            const originalText = $submitBtn.html();
            $submitBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Creating...').prop('disabled', true);
            
            const formData = new FormData(this);
            
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        showMessage(messageId, 'success', successMessage);
                        $form[0].reset();
                        
                        setTimeout(function() {
                            $('#' + modalId).modal('hide');
                            refreshBlockIssuesTable();
                        }, 800);
                    } else {
                        showMessage(messageId, 'danger', (data && data.message) || errorMessage);
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        if (errors) {
                            // Clear previous error messages
                            $form.find('.is-invalid').removeClass('is-invalid');
                            $form.find('.invalid-feedback').remove();
                            
                            // Show validation errors
                            Object.keys(errors).forEach(field => {
                                const $input = $form.find(`[name="${field}"]`);
                                if ($input.length) {
                                    $input.addClass('is-invalid');
                                    const errorDiv = $('<div class="invalid-feedback"></div>').text(errors[field][0]);
                                    $input.after(errorDiv);
                                }
                            });
                        }
                    } else {
                        showMessage(messageId, 'danger', errorMessage);
                    }
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
     * Opens the issue modal for add or edit mode
     * 
     * @param {string} mode - The mode ('add' or 'edit')
     * @param {number} id - The issue ID (for edit mode)
     */
    window.openIssueModal = function(mode, id = null) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            return;
        }
        
        const $modal = $('#issueModal');
        const $modalLabel = $('#issueModalLabel');
        const $form = $('#issueForm');
        const $submitBtn = $('#issueSubmitBtn');
        
        if (mode === 'add') {
            // Add mode
            $modalLabel.text('Create Issue');
            $submitBtn.html('<i class="ph-check me-1"></i> Create');
            $form.attr('action', window.routes?.blockIssues?.store || '/block-issues');
            $form.find('input[name="_method"]').remove(); // Remove PUT method for add
            $form[0].reset(); // Reset form
            
            // Initialize modal before showing
            initializeModal('issueModal');
            
            $modal.modal('show');
        } else if (mode === 'edit' && id) {
            // Edit mode - load issue data
            loadIssueForEdit(id);
        }
    }
    
    /**
     * Loads issue data and populates the modal for editing
     * 
     * @param {number} id - The ID of the issue to edit
     */
    function loadIssueForEdit(id) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            return;
        }
        
        // Show loading state
        let $editBtn = $(`button[onclick="editIssue(${id})"]`);
        const originalText = $editBtn.length > 0 ? $editBtn.html() : 'Edit';
        if ($editBtn.length > 0) {
            $editBtn.html('<i class="ph-spinner ph-spin me-1"></i>Loading...').prop('disabled', true);
        }
        
        $.ajax({
            url: `/block-issues/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const issue = data.data;
                    const $modal = $('#issueModal');
                    const $form = $('#issueForm');
                    const $modalLabel = $('#issueModalLabel');
                    const $submitBtn = $('#issueSubmitBtn');
                    
                    // Update modal for edit mode
                    $modalLabel.text('Edit Issue');
                    $submitBtn.html('<i class="ph-check me-1"></i> Update');
                    $form.attr('action', window.routes?.blockIssues?.update?.replace(':id', id) || `/block-issues/${id}`);
                    
                    // Add PUT method for edit
                    if ($form.find('input[name="_method"]').length === 0) {
                        $form.append('<input type="hidden" name="_method" value="PUT">');
                    }
                    
                    // Initialize modal first
                    initializeModal('issueModal');
                    
                    // Populate form fields
                    $('#contact_method_id').val(issue.contact_method_id);
                    $('#block_unit_id').val(issue.block_unit_id);
                    $('#assigned_to').val(issue.assigned_to);
                    $('#issue_type').val(issue.issue_type);
                    $('#priority_id').val(issue.priority_id);
                    $('#issue').val(issue.issue);
                    $('#contact_details').val(issue.contact_details);
                    $('#fault_details').val(issue.fault_details);
                    
                    // Show the modal
                    $modal.modal('show');
                } else {
                    showMessage('issueMessage', 'danger', 'Error loading issue data');
                }
            },
            error: function(xhr, status, error) {
                showMessage('issueMessage', 'danger', 'Error loading issue data');
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
        clearMessage('issueMessage');
    }
    
    // ========================================
    // DELETE CONFIRMATION MODAL
    // ========================================
    
    /**
     * Shows the delete confirmation modal with issue details
     * 
     * @param {number} issueId - The ID of the issue to delete
     * @param {object} issueData - The issue data to display in confirmation
     */
    function showDeleteConfirmation(issueId, issueData) {
        
        // Populate issue details in the modal
        const detailsHtml = `
            <div class="row">
                <div class="col-6"><strong>Issue ID:</strong></div>
                <div class="col-6">#${issueData.ref_no || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Title:</strong></div>
                <div class="col-6">${issueData.issue || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Priority:</strong></div>
                <div class="col-6">${getPriorityText(issueData.priority)}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Status:</strong></div>
                <div class="col-6">${getStatusText(issueData.status)}</div>
            </div>
        `;
        
        $('#deleteIssueDetails').html(detailsHtml);
        
        // Set up the confirm button to actually delete
        $('#confirmDeleteBtn').off('click').on('click', function() {
            deleteIssue(issueId);
        });
        
        // Show the modal
        $('#deleteIssueModal').modal('show');
    }
    
    /**
     * Deletes an issue via AJAX
     * 
     * @param {number} issueId - The ID of the issue to delete
     */
    function deleteIssue(issueId) {
        
        // Show loading state
        const $confirmBtn = $('#confirmDeleteBtn');
        const originalText = $confirmBtn.html();
        $confirmBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Deleting...').prop('disabled', true);
        
        $.ajax({
            url: `/block-issues/${issueId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                
                // Hide the modal
                $('#deleteIssueModal').modal('hide');
                
                // Show success message
                showMessage('issueMessage', 'success', 'Issue deleted successfully');
                
                // Refresh the table
                refreshBlockIssuesTable();
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                
                // Show error message
                showMessage('issueMessage', 'danger', 'Error deleting issue. Please try again.');
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            }
        });
    }
    
    // Expose delete functions to global scope
    window.showDeleteConfirmation = showDeleteConfirmation;
    window.deleteIssue = deleteIssue;
    
    // ========================================
    // PHOTO UPLOAD FUNCTIONALITY (DROPZONE)
    // ========================================
    
    let photoDropzone;
    let currentIssueId = null;
    let photosToDelete = [];
    
    /**
     * Opens the photo upload modal for a specific issue
     * 
     * @param {number} issueId - The ID of the issue to upload photos for
     */
    window.openPhotoUploadModal = function(issueId) {
        console.log('openPhotoUploadModal called with ID:', issueId);
        currentIssueId = issueId;
        
        // Show loading state
        const uploadBtn = document.querySelector(`button[onclick="openPhotoUploadModal(${issueId})"]`);
        if (uploadBtn) {
            const originalText = uploadBtn.innerHTML;
            uploadBtn.innerHTML = '<i class="ph-spinner ph-spin me-1"></i>Loading...';
            uploadBtn.disabled = true;
        }
        
        // Load issue data and existing photos
        loadIssueForPhotoUpload(issueId);
    };
    
    /**
     * Loads issue data and existing photos for the upload modal
     * 
     * @param {number} issueId - The ID of the issue
     */
    function loadIssueForPhotoUpload(issueId) {
        $.ajax({
            url: `/block-issues/${issueId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const issue = data.data;
                    
                    // Update modal with issue info
                    $('#photoUploadIssueTitle').text(`Issue: ${issue.issue || 'N/A'}`);
                    $('#photoUploadIssueRef').text(`Reference: ${issue.ref_no || 'N/A'}`);
                    
                    // Load existing photos
                    loadExistingPhotos(issueId);
                    
                    // Show the modal
                    $('#photoUploadModal').modal('show');
                } else {
                    showPhotoMessage('danger', 'Error loading issue data');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading issue:', error);
                showPhotoMessage('danger', 'Error loading issue data');
            },
            complete: function() {
                // Reset button state
                const uploadBtn = document.querySelector(`button[onclick="openPhotoUploadModal(${issueId})"]`);
                if (uploadBtn) {
                    uploadBtn.innerHTML = '<i class="ph-camera"></i>';
                    uploadBtn.disabled = false;
                }
            }
        });
    }
    
    /**
     * Loads existing photos for the issue
     * 
     * @param {number} issueId - The ID of the issue
     */
    function loadExistingPhotos(issueId) {
        $.ajax({
            url: `/api/block-issues/${issueId}/photos`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success && data.data.length > 0) {
                    displayExistingPhotos(data.data);
                    $('#existingPhotosSection').show();
                } else {
                    $('#existingPhotosSection').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading existing photos:', error);
                $('#existingPhotosSection').hide();
            }
        });
    }
    
    /**
     * Displays existing photos in the modal
     * 
     * @param {Array} photos - Array of photo objects
     */
    function displayExistingPhotos(photos) {
        const container = $('#existingPhotos');
        container.empty();
        
        photos.forEach(function(photo, index) {
            const photoHtml = `
                <div class="col-md-4 mb-2">
                    <div class="card">
                        <img src="/storage/${photo.image_path}/${photo.image_name}" 
                             class="card-img-top" 
                             style="height: 120px; object-fit: cover; cursor: pointer;"
                             alt="Photo ${index + 1}"
                             onclick="previewPhoto('${photo.image_path}/${photo.image_name}', '${photo.image_name}', ${index})">
                        <div class="card-body p-2">
                            <small class="text-muted">${photo.image_name}</small>
                            <button type="button" class="btn btn-sm btn-outline-danger float-end" 
                                    onclick="confirmDeletePhoto(${photo.id}, '${photo.image_name}')" title="Delete Photo">
                                <i class="ph-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.append(photoHtml);
        });
    }
    
    /**
     * Shows message in photo upload modal
     * 
     * @param {string} type - The type of message (success, danger, warning, info)
     * @param {string} message - The message to display
     */
    function showPhotoMessage(type, message) {
        const $messageDiv = $('#photoUploadMessage');
        
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
     * Confirms photo deletion
     * 
     * @param {number} photoId - The ID of the photo to delete
     * @param {string} photoName - The name of the photo
     */
    window.confirmDeletePhoto = function(photoId, photoName) {
        $('#deletePhotoModal').modal('show');
        $('#confirmDeletePhotoBtn').off('click').on('click', function() {
            deletePhoto(photoId);
            $('#deletePhotoModal').modal('hide');
        });
    };
    
    /**
     * Deletes a photo
     * 
     * @param {number} photoId - The ID of the photo to delete
     */
    function deletePhoto(photoId) {
        $.ajax({
            url: `/api/block-issue-photos/${photoId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    showPhotoMessage('success', 'Photo deleted successfully');
                    // Reload existing photos
                    loadExistingPhotos(currentIssueId);
                } else {
                    showPhotoMessage('danger', 'Error deleting photo');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error deleting photo:', error);
                showPhotoMessage('danger', 'Error deleting photo');
            }
        });
    }
    
    /**
     * Previews a photo in modal
     * 
     * @param {string} imagePath - The path to the image
     * @param {string} imageName - The name of the image
     * @param {number} index - The index of the image
     */
    window.previewPhoto = function(imagePath, imageName, index) {
        // This will be implemented to show photo in preview modal
        console.log('Preview photo:', imagePath, imageName, index);
    };
    
    // ========================================
    // SEARCH FUNCTIONALITY
    // ========================================
    
    /**
     * Toggle search panel visibility
     */
    $('#toggleSearchBtn').on('click', function() {
        const $panel = $('#searchIssuesPanel');
        if ($panel.is(':visible')) {
            $panel.hide();
        } else {
            $panel.show();
        }
    });
    
    /**
     * Close search panel
     */
    $('#closeSearchHeaderBtn, #closeSearchBtn').on('click', function() {
        $('#searchIssuesPanel').hide();
    });
    
    /**
     * Perform search
     */
    $('#searchIssuesBtn').on('click', function() {
        performSearch();
    });
    
    /**
     * Clear search
     */
    $('#clearSearchBtn').on('click', function() {
        clearSearch();
    });
    
    /**
     * Show all issues
     */
    $('#showAllBtn').on('click', function() {
        // Refresh the table to show all issues
        refreshBlockIssuesTable();
    });
    
    /**
     * Allow Enter key to trigger search
     */
    $('#searchIssuesForm').on('submit', function(e) {
        e.preventDefault();
        performSearch();
    });
    
    /**
     * Perform search function
     */
    function performSearch() {
        const formData = new FormData($('#searchIssuesForm')[0]);
        const searchParams = new URLSearchParams();
        
        // Add search parameters
        for (let [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                searchParams.append(key, value);
            }
        }
        
        // Show loading state on main table
        const $mainTableBody = $('#blockIssuesTable tbody');
        if ($mainTableBody.length) {
            $mainTableBody.html(`
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        <i class="ph-spinner ph-spin"></i> Filtering issues...
                    </td>
                </tr>
            `);
        }
        
        // Perform AJAX search
        $.ajax({
            url: `/block-issues?${searchParams.toString()}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if ($mainTableBody.length) {
                    if (data.success && data.data && data.data.total > 0) {
                        // Display filtered results in main table
                        const issues = data.data.data;
                        $mainTableBody.html(issues.map(issue => `
                            <tr>
                                <td>
                                    <a href="/block-issues/${issue.id}" class="text-decoration-none">
                                        <b>#${issue.ref_no}</b>
                                    </a>
                                </td>
                                <td>${issue.issue || 'N/A'}</td>
                                <td>${getPriorityBadge(issue.priority_id)}</td>
                                <td>${getStatusBadge(issue.issue_status_id)}</td>
                                <td>${formatDate(issue.created_at)}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editIssue(${issue.id})" title="Edit Issue">
                                        <i class="ph-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="showDeleteConfirmation(${issue.id}, {
                                        ref_no: '${issue.ref_no || 'N/A'}',
                                        issue: '${issue.issue || 'N/A'}',
                                        priority: '${issue.priority_id || 'N/A'}',
                                        status: '${issue.issue_status_id || 'N/A'}'
                                    })" title="Delete Issue">
                                        <i class="ph-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join(''));
                    } else {
                        // No results found
                        $mainTableBody.html(`
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="ph-magnifying-glass"></i> No issues found matching your search criteria
                                </td>
                            </tr>
                        `);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                if ($mainTableBody.length) {
                    $mainTableBody.html(`
                        <tr>
                            <td colspan="6" class="text-center text-danger py-3">
                                <i class="ph-warning"></i> Error occurred while filtering. Please try again.
                            </td>
                        </tr>
                    `);
                }
            }
        });
    }
    
    /**
     * Clear search function
     */
    function clearSearch() {
        $('#searchIssuesForm')[0].reset();
        $('#searchIssuesPanel').hide();
        
        // Restore original issue list
        refreshBlockIssuesTable();
    }
    
    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    
    /**
     * Get priority badge HTML
     */
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
    
    /**
     * Get status badge HTML
     */
    function getStatusBadge(statusId) {
        const statuses = {
            1: '<span class="badge bg-warning">Open</span>',
            2: '<span class="badge bg-info">In Progress</span>',
            3: '<span class="badge bg-success">Resolved</span>',
            4: '<span class="badge bg-secondary">Closed</span>',
            5: '<span class="badge bg-danger">On Hold</span>'
        };
        return statuses[statusId] || '<span class="badge bg-secondary">Unknown</span>';
    }
    
    /**
     * Get priority text
     */
    function getPriorityText(priorityId) {
        const priorities = {
            1: 'Low',
            2: 'Normal',
            3: 'High',
            4: 'Urgent',
            5: 'Critical'
        };
        return priorities[priorityId] || 'Unknown';
    }
    
    /**
     * Get status text
     */
    function getStatusText(statusId) {
        const statuses = {
            1: 'Open',
            2: 'In Progress',
            3: 'Resolved',
            4: 'Closed',
            5: 'On Hold'
        };
        return statuses[statusId] || 'Unknown';
    }
    
    /**
     * Format date
     */
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }
    
    /**
     * Get user details and populate default contact details
     */
    function getUserDetails(userId) {
        if (!userId) return;
        
        $.ajax({
            url: `/api/users/${userId}`,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(data) {
                if (data.success && data.data) {
                    const user = data.data;
                    let contactDetails = '';
                    
                    // Build contact details string
                    if (user.phone) {
                        contactDetails += `Phone: ${user.phone}`;
                    }
                    if (user.email) {
                        contactDetails += contactDetails ? `\nEmail: ${user.email}` : `Email: ${user.email}`;
                    }
                    if (user.address) {
                        contactDetails += contactDetails ? `\nAddress: ${user.address}` : `Address: ${user.address}`;
                    }
                    
                    // Update default contact details field
                    $('#default_contact_details').val(contactDetails);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading user details:', error);
            }
        });
    }
    
    // ========================================
    // TRIGGER FUNCTIONS
    // ========================================
    
    /**
     * Triggers a refresh of the issues table
     * 
     * This function is debounced to prevent multiple simultaneous calls
     */
    function triggerIssuesRefresh() {
        if (!$.fn.DataTable.isDataTable('#blockIssuesTable')) {
            initializeDataTable();
        }
        if (typeof window.refreshBlockIssuesTable === 'function') {
            window.refreshBlockIssuesTable();
        } else {
            // If refresh function is not available, check current table data
            if (blockIssuesDataTable) {
                const currentRowCount = blockIssuesDataTable.rows().count();
                toggleExportButtons(currentRowCount > 0);
            }
        }
    }
    
    // Debounced trigger to avoid duplicate refreshes
    let issuesRefreshTimer = null;
    function triggerIssuesRefresh() {
        clearTimeout(issuesRefreshTimer);
        issuesRefreshTimer = setTimeout(function() {
            if (!$.fn.DataTable.isDataTable('#blockIssuesTable')) {
                initializeDataTable();
            }
            if (typeof window.refreshBlockIssuesTable === 'function') {
                window.refreshBlockIssuesTable();
            } else {
                // If refresh function is not available, check current table data
                if (blockIssuesDataTable) {
                    const currentRowCount = blockIssuesDataTable.rows().count();
                    toggleExportButtons(currentRowCount > 0);
                }
            }
        }, 50);
    }
    
    // ========================================
    // EVENT LISTENERS AND INITIALIZATION
    // ========================================
    
    // Listen for Bootstrap tab shown event to refresh data when issues tab becomes active
    $(document).on('shown.bs.tab', '#issues-tab', function(e) {
        triggerIssuesRefresh();
    });

    // If Issues tab is already active on page load, refresh once to ensure data is loaded
    if ($('#issues').hasClass('show') && $('#issues').hasClass('active')) {
        triggerIssuesRefresh();
    }
    
    // ========================================
    // MODAL EVENT HANDLERS
    // ========================================
    
    // Clear messages when issue modal is opened
    $('#issueModal').on('show.bs.modal', function() {
        // Use setTimeout to ensure DOM is ready before clearing message
        setTimeout(function() {
            clearMessage('issueMessage');
        }, 50);
    });
    
    // Handle assigned_to change to populate default contact details
    $('#assigned_to').on('change', function() {
        const userId = $(this).val();
        if (userId && $('#use_default_contact').is(':checked')) {
            getUserDetails(userId);
        }
    });
    
    // Handle default contact checkbox change
    $('#use_default_contact').on('change', function() {
        if (this.checked) {
            $('#default_contact_details').prop('readonly', true).addClass('bg-light');
            const userId = $('#assigned_to').val();
            if (userId) {
                getUserDetails(userId);
            }
        } else {
            $('#default_contact_details').prop('readonly', false).removeClass('bg-light');
        }
    });
    
    // ========================================
    // FORM SUBMISSION HANDLERS
    // ========================================
    
    // Handle issue form submission
    handleIssueFormSubmission('issueModal', 'issueMessage', 'Issue created successfully!', 'Error creating issue. Please try again.');
    
    // ========================================
    // PHOTO UPLOAD FORM HANDLERS
    // ========================================
    
    // ========================================
    // DROPZONE INITIALIZATION
    // ========================================
    
    // Initialize Dropzone when photo upload modal is shown
    $('#photoUploadModal').on('shown.bs.modal', function() {
        if (!photoDropzone) {
            initializePhotoDropzone();
        }
    });
    
    // Initialize Photo Dropzone
    function initializePhotoDropzone() {
        // Disable auto discover to prevent conflicts
        Dropzone.autoDiscover = false;
        
        photoDropzone = new Dropzone("#photoDropzone", {
            url: `/block-issues/${currentIssueId}/photos`,
                paramName: "images",
                uploadMultiple: true,
                parallelUploads: 10,
                maxFiles: 10,
                maxFilesize: 2, // 2MB per file
                acceptedFiles: "image/*",
                addRemoveLinks: true,
                clickable: true, // Enable click to upload
                dictDefaultMessage: "Drop images here or click to upload",
                dictRemoveFile: "Remove",
                dictCancelUpload: "Cancel",
                dictUploadCanceled: "Upload canceled",
                dictInvalidFileType: "You can't upload files of this type.",
                dictFileTooBig: "File is too big. Max filesize: 2MB.",
                dictMaxFilesExceeded: "You can not upload more than 10 files.",
                dictResponseError: "Server responded with an error.",
                dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
                dictRemoveFileConfirmation: "Are you sure you want to remove this file?",
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
                },
            init: function() {
                const dz = this;
                
                // Custom styling
                this.on("addedfile", function(file) {
                    // Add custom styling to file preview
                    const preview = file.previewElement;
                    $(preview).addClass('dz-image-preview-custom');
                    
                    // Add file size info
                    const sizeInfo = $(preview).find('.dz-size');
                    if (sizeInfo.length === 0) {
                        $(preview).find('.dz-details').append('<div class="dz-size"><span data-dz-size></span></div>');
                    }
                });
                
                // Handle successful upload
                this.on("successmultiple", function(files, response) {
                    showPhotoMessage('success', 'Photos uploaded successfully!');
                    
                    // Close modal after successful upload
                    setTimeout(() => {
                        $('#photoUploadModal').modal('hide');
                        // Reload the page to show new photos
                        location.reload();
                    }, 1500);
                });
                
                // Handle upload errors
                this.on("errormultiple", function(files, response) {
                    let errorMessage = 'Upload failed. Please try again.';
                    
                    if (response && response.message) {
                        errorMessage = response.message;
                    } else if (response && response.errors) {
                        const errors = [];
                        Object.values(response.errors).forEach(errorArray => {
                            errors.push(...errorArray);
                        });
                        errorMessage = errors.join('<br>');
                    }
                    
                    showPhotoMessage('danger', errorMessage);
                });
                
                // Handle individual file errors
                this.on("error", function(file, errorMessage) {
                    showPhotoMessage('danger', errorMessage);
                });
                
                // Custom validation for total file size
                this.on("addedfiles", function(files) {
                    let totalSize = 0;
                    const maxTotalSize = 10 * 1024 * 1024; // 10MB
                    
                    files.forEach(file => {
                        totalSize += file.size;
                    });
                    
                    if (totalSize > maxTotalSize) {
                        showPhotoMessage('warning', 'Total file size exceeds 10MB limit. Please reduce the number of files or their size.');
                        files.forEach(file => {
                            this.removeFile(file);
                        });
                    }
                });
            }
        });
    }
    
    // Clear all files
    $('#clearPhotosBtn').on('click', function() {
        if (photoDropzone) {
            photoDropzone.removeAllFiles(true);
        }
    });
    
    // Debug function to test dropzone
    window.testDropzone = function() {
        if (photoDropzone) {
            showPhotoMessage('success', 'Dropzone is working correctly!');
        } else {
            showPhotoMessage('warning', 'Dropzone not initialized. Try opening the upload modal first.');
        }
    };
    
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
 * Edit issue function (for backward compatibility)
 * 
 * @param {number} id - The ID of the issue to edit
 */
function editIssue(id) {
    window.openIssueModal('edit', id);
}

// Expose editIssue to global scope for DataTable onclick handlers
window.editIssue = editIssue;
</script>
