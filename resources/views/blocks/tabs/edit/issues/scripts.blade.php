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
    
    /** @var {Object} unitAutoComplete - Global AutoComplete.js instance for units dropdown */
    let unitAutoComplete = null;
    
    /** @var {boolean} isInitializingAutoComplete - Flag to prevent multiple simultaneous initializations */
    let isInitializingAutoComplete = false;
    
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
                    autoWidth: false,           // Disable automatic column width calculation
                    processing: true,           // Enable processing indicator
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>', // Length/filter on same line, info/pagination on same line
                    order: [[0, 'desc']],      // Default sort by first column (Issue ID) descending
                    columnDefs: [
                        { targets: [7], orderable: false }, // Actions column (last column) not sortable
                        { targets: '_all', className: 'text-nowrap' } // Prevent text wrapping for better layout
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
                    
                    data.data.forEach(function(issue) {
                        blockIssuesDataTable.row.add([
                            `<a href="/block-issues/${issue.id}" class="text-decoration-none"><b>#${issue.ref_no}</b></a>`,
                            issue.issue || 'N/A',
                            `<span class="badge bg-secondary">${issue.block_unit?.unit_name || 'N/A'}</span>`,
                            getIssueTypeBadge(issue.issue_type),
                            getPriorityBadge(issue.priority_id),
                            getStatusBadge(issue.issue_status_id),
                            formatDate(issue.created_at),
                            `<button class="btn btn-sm btn-outline-primary" onclick="editIssue(${issue.id})" title="Edit Issue">
                                <i class="ph-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="openPhotoUploadModal(${issue.id})" title="Upload Photos">
                                <i class="ph-camera"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="issuesShowDeleteConfirmation(${issue.id}, {
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
    // UNIT SELECTION HANDLER
    // ========================================
    
    /**
     * Load active issues for selected unit
     * 
     * @param {number} unitId - The ID of the selected unit
     */
    function loadActiveIssuesForUnit(unitId) {
        if (!unitId) {
            // Clear table if no unit selected
            $('#openIssuesTableBody').html(`
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        <i class="ph-info-circle"></i> Select a unit to view open issues
                    </td>
                </tr>
            `);
            return;
        }
        
        // Show loading state
        $('#openIssuesTableBody').html(`
            <tr>
                <td colspan="6" class="text-center text-muted py-3">
                    <i class="ph-spinner ph-spin"></i> Loading issues...
                </td>
            </tr>
        `);
        
        $.ajax({
            url: '/api/block-unit-active-issues',
            method: 'GET',
            data: { unit_id: unitId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayActiveIssues(response.data);
                } else {
                    showActiveIssuesError('Error loading issues');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading active issues:', error);
                showActiveIssuesError('Error loading issues');
            }
        });
    }
    
    /**
     * Display active issues in the table
     * 
     * @param {Array} issues - Array of issue objects
     */
    function displayActiveIssues(issues) {
        const tbody = $('#openIssuesTableBody');
        
        if (issues.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        <i class="ph-check-circle"></i> No open issues for this unit
                    </td>
                </tr>
            `);
            return;
        }
        
        let html = '';
        issues.forEach(function(issue) {
            const priorityBadge = getPriorityBadge(issue.priority_id);
            const statusBadge = getStatusBadge(issue.issue_status_id);
            const issueType = issue.issue_type ? issue.issue_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A';
            const reportedDate = formatDate(issue.created_at);
            
            html += `
                <tr>
                    <td>
                        <a href="/block-issues/${issue.id}" class="text-decoration-none" target="_blank">
                            <b>#${issue.ref_no}</b>
                        </a>
                    </td>
                    <td>${issue.issue || 'N/A'}</td>
                    <td>
                        <span class="badge bg-secondary">${issueType}</span>
                    </td>
                    <td>${priorityBadge}</td>
                    <td>${reportedDate}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editActiveIssue(${JSON.stringify(issue).replace(/"/g, '&quot;')})" title="Edit Issue">
                            <i class="ph-pencil"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.html(html);
    }
    
    /**
     * Show error message in the table
     * 
     * @param {string} message - Error message to display
     */
    function showActiveIssuesError(message) {
        $('#openIssuesTableBody').html(`
            <tr>
                <td colspan="6" class="text-center text-danger py-3">
                    <i class="ph-warning"></i> ${message}
                </td>
            </tr>
        `);
    }
    
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
            
            // Detect if this is an edit operation
            const isEdit = $form.find('input[name="_method"]').val() === 'PUT';
            const loadingText = isEdit ? 'Updating...' : 'Creating...';
            
            $submitBtn.html(`<i class="ph-spinner-gap ph-spin me-1"></i> ${loadingText}`).prop('disabled', true);
            
            const formData = new FormData(this);
            
            // Add images from Dropzone if in step 2 (create mode)
            if (!isEdit && issueDropzone && issueDropzone.files.length > 0) {
                issueDropzone.files.forEach(function(file, index) {
                    formData.append('images[]', file);
                });
            }
            
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
                        // Use appropriate success message based on operation
                        const finalSuccessMessage = isEdit ? 'Issue updated successfully!' : successMessage;
                        showMessage(messageId, 'success', finalSuccessMessage);
                        $form[0].reset();
                        
                        // Clear dropzone files
                        if (issueDropzone) {
                            issueDropzone.removeAllFiles();
                        }
                        
                        // Reset step form
                        resetStepForm();
                        
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
            // Add mode - show step form
            $modalLabel.text('Create Issue');
            $submitBtn.html('<i class="ph-check me-1"></i> Create Issue');
            $form.attr('action', window.routes?.blockIssues?.store || '/block-issues');
            $form.find('input[name="_method"]').remove(); // Remove PUT method for add
            $form[0].reset(); // Reset form
            
            // Show step form for add mode
            $('.step-wizard').show();
            resetStepForm();
            
            // Initialize modal before showing
            initializeModal('issueModal');
            
            // Show modal (units will be refreshed by the modal event handler)
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
                    
                    // Hide step form for edit mode - show all fields at once
                    $('.step-wizard').hide();
                    $('.step-content').removeClass('d-none');
                    $('#step1, #step2').removeClass('d-none');
                    // Reset form wizard state
                    resetStepForm();
                    $('#issueSubmitBtn').removeClass('d-none');
                    
                    // Initialize modal first
                    initializeModal('issueModal');
                    
                    // Show the modal first
                    $modal.modal('show');
                    
                    // Populate form fields after modal is shown
                    $modal.on('shown.bs.modal', function() {
                        // Refresh units dropdown with the current unit selected
                        refreshUnitsDropdownForEdit(issue.block_unit_id);
                        
                        $('#contact_method_id').val(issue.contact_method_id);
                        $('#assigned_to').val(issue.assigned_to.id).trigger('change');
                        $('#issue_type').val(issue.issue_type);
                        $('#priority_id').val(issue.priority_id);
                        $('#issue').val(issue.issue);
                        $('#contact_details').val(issue.contact_details);
                        $('#issue_details').val(issue.issue_details);
                        
                        // Remove the event listener to prevent multiple triggers
                        $modal.off('shown.bs.modal');
                    });
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
        
        // Reset step form to step 1
        resetStepForm();
    }
    
    // ========================================
    // STEP FORM NAVIGATION (Using fullkit wizard style)
    // ========================================
    
    /** @var {Dropzone} issueDropzone - Dropzone instance for image uploads */
    let issueDropzone = null;
    
    /**
     * Reset step form to initial state
     */
    function resetStepForm() {
        // Reset to first tab
        const firstTab = document.getElementById('pills-issue-details-tab');
        if (firstTab) {
            firstTab.click();
        }
        
        // Disable step 2 tab until issue is created
        const step2Tab = document.getElementById('pills-upload-images-tab');
        if (step2Tab) {
            step2Tab.disabled = true;
        }
        
        // Reset progress bar
        const progressBar = document.querySelector('#custom-progress-bar .progress-bar');
        if (progressBar) {
            progressBar.style.width = '0%';
        }
        
        // Reset footer buttons
        $('#step1Footer').removeClass('d-none');
        $('#step2Footer').addClass('d-none');
        
        // Clear created issue ID
        $('#created_issue_id').val('');
        
        // Destroy existing dropzone if it exists
        if (issueDropzone) {
            issueDropzone.destroy();
            issueDropzone = null;
        }
    }
    
    /**
     * Validate step 1 form fields
     * @returns {boolean} - True if valid, false otherwise
     */
    function validateStep1() {
        const requiredFields = [
            { id: 'issue_block_unit_id_hidden', name: 'Unit Selection' },
            { id: 'contact_method_id', name: 'Contact Method' },
            { id: 'assigned_to', name: 'Assigned To' },
            { id: 'issue_type', name: 'Issue Category' },
            { id: 'priority_id', name: 'Priority' },
            { id: 'issue', name: 'Problem Overview' },
            { id: 'contact_details', name: 'Reported By' }
        ];
        
        let isValid = true;
        const errors = [];
        
        requiredFields.forEach(function(field) {
            const $field = $('#' + field.id);
            const value = $field.val();
            
            if (!value || value.trim() === '') {
                isValid = false;
                errors.push(field.name);
                $field.addClass('is-invalid');
            } else {
                $field.removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            showMessage('issueMessage', 'danger', 'Please fill in all required fields: ' + errors.join(', '));
            // Scroll to first invalid field
            const firstInvalid = $('.is-invalid').first();
            if (firstInvalid.length) {
                $('html, body').animate({
                    scrollTop: firstInvalid.offset().top - 100
                }, 500);
            }
        } else {
            clearMessage('issueMessage');
        }
        
        return isValid;
    }
    
    /**
     * Validate step 1 before allowing next tab
     * This is called by the nexttab button
     */
    function validateAndProceedToNextTab(nextTabId) {
        if (validateStep1()) {
            // Validation passed, proceed to next tab
            document.getElementById(nextTabId).click();
        }
        // If validation fails, prevent tab change (handled by validateStep1)
    }
    
    /**
     * Initialize Dropzone for image uploads
     */
    function initializeIssueDropzone() {
        if (typeof Dropzone === 'undefined') {
            console.error('Dropzone is not loaded');
            return;
        }
        
        // Destroy existing instance if any
        if (issueDropzone) {
            issueDropzone.destroy();
        }
        
        // Initialize Dropzone
        issueDropzone = new Dropzone('#issueImageDropzone', {
            url: '#', // Will be handled by form submission
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 10,
            maxFiles: 20,
            maxFilesize: 10, // 10MB
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            dictDefaultMessage: '',
            dictRemoveFile: 'Remove',
            dictCancelUpload: 'Cancel',
            dictFileTooBig: function(file) {
                return 'File is too big (' + (file.size / 1024 / 1024).toFixed(2) + 'MB). Max filesize: 10MB.';
            },
            dictInvalidFileType: 'Invalid file type. Only images are allowed.',
            dictMaxFilesExceeded: function(maxFiles) {
                return 'You can only upload ' + maxFiles + ' files.';
            },
            previewsContainer: '#issueImagePreview',
            previewTemplate: `
                <div class="dz-preview dz-file-preview col-md-3 col-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="dz-image position-relative" style="height: 150px; overflow: hidden; border-radius: 8px 8px 0 0;">
                            <img data-dz-thumbnail class="w-100 h-100" style="object-fit: cover;" />
                            <div class="dz-remove position-absolute top-0 end-0 m-2" data-dz-remove style="background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;">
                                <i class="ph-x"></i>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="dz-filename small text-truncate" data-dz-name style="font-weight: 500;"></div>
                            <div class="dz-size text-muted" data-dz-size style="font-size: 0.75rem;"></div>
                        </div>
                        <div class="dz-progress position-absolute bottom-0 start-0 w-100" style="height: 4px; background: #e9ecef;">
                            <span class="dz-upload bg-primary" data-dz-uploadprogress style="display: block; height: 100%; width: 0%; transition: width 0.3s;"></span>
                        </div>
                        <div class="dz-error-message text-danger small p-2" data-dz-errormessage style="display: none;"></div>
                    </div>
                </div>
            `,
            init: function() {
                const dropzoneInstance = this;
                
                // Clear preview container on initialization
                $('#issueImagePreview').html('');
                
                // Handle file addition
                this.on('addedfile', function(file) {
                    // File added callback
                });
                
                // Handle file removal
                this.on('removedfile', function(file) {
                    // File removed callback
                });
                
                // Handle upload progress
                this.on('uploadprogress', function(file, progress, bytesSent) {
                    // Upload progress callback
                });
            }
        });
    }
    
    // Step navigation button handlers
    // Handle "Create Issue and Upload Images" button click
    $(document).on('click', '#createIssueAndUploadBtn', function(e) {
        e.preventDefault();
        
        // Validate step 1 first
        if (!validateStep1()) {
            return; // Validation failed, stop here
        }
        
        const $btn = $(this);
        const originalText = $btn.html();
        const $form = $('#issueForm');
        const messageId = 'issueMessage';
        
        // Disable button and show loading
        $btn.html('<i class="ph-spinner-gap ph-spin label-icon align-middle fs-lg me-2"></i>Creating Issue...').prop('disabled', true);
        
        // Create FormData with only step 1 fields (no images yet)
        const formData = new FormData($form[0]);
        
        // Submit to create the issue
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
                if (data.success && data.data && data.data.id) {
                    // Issue created successfully, store the issue ID
                    const issueId = data.data.id;
                    $('#created_issue_id').val(issueId);
                    
                    // Show success message
                    showMessage(messageId, 'success', 'Issue created successfully! Now you can upload images.');
                    
                    // Enable step 2 tab and navigate to it
                    const step2Tab = document.getElementById('pills-upload-images-tab');
                    if (step2Tab) {
                        step2Tab.disabled = false;
                        
                        // Switch footer buttons
                        $('#step1Footer').addClass('d-none');
                        $('#step2Footer').removeClass('d-none');
                        
                        setTimeout(function() {
                            step2Tab.click();
                        }, 500);
                    }
                } else {
                    showMessage(messageId, 'danger', 'Error creating issue. Please try again.');
                    $btn.html(originalText).prop('disabled', false);
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
                    showMessage(messageId, 'danger', 'Please correct the errors and try again.');
                } else {
                    showMessage(messageId, 'danger', 'Error creating issue. Please try again.');
                }
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });
    
    // Prevent direct navigation to step 2 before issue is created
    $(document).on('show.bs.tab', '#pills-upload-images-tab', function(e) {
        const issueId = $('#created_issue_id').val();
        if (!issueId) {
            e.preventDefault();
            showMessage('issueMessage', 'warning', 'Please create the issue first before uploading images.');
            return false;
        }
    });
    
    // Switch footer buttons based on active tab and issue creation status
    $(document).on('shown.bs.tab', 'button[data-bs-toggle="pill"]', function(e) {
        const issueId = $('#created_issue_id').val();
        const targetId = $(e.target).attr('data-bs-target');
        
        // Once issue is created, always show step 2 footer
        if (issueId) {
            $('#step1Footer').addClass('d-none');
            $('#step2Footer').removeClass('d-none');
        } else {
            // Issue not created yet - show step 1 footer
            if (targetId === '#pills-issue-details') {
                $('#step1Footer').removeClass('d-none');
                $('#step2Footer').addClass('d-none');
            } else {
                $('#step1Footer').addClass('d-none');
                $('#step2Footer').addClass('d-none');
            }
        }
    });
    
    // Initialize dropzone when upload images tab is shown
    $(document).on('shown.bs.tab', '#pills-upload-images-tab', function() {
        // Only initialize if issue has been created
        const issueId = $('#created_issue_id').val();
        if (!issueId) {
            // Issue not created yet, go back to step 1
            showMessage('issueMessage', 'warning', 'Please create the issue first.');
            document.getElementById('pills-issue-details-tab').click();
            return;
        }
        
        // Initialize dropzone if not already initialized
        if (!issueDropzone) {
            initializeIssueDropzone();
        }
    });
    
    // Handle "Upload Images" button click in step 2
    $(document).on('click', '#uploadImagesBtn', function(e) {
        e.preventDefault();
        
        const issueId = $('#created_issue_id').val();
        if (!issueId) {
            showMessage('issueMessage', 'danger', 'Issue ID not found. Please go back and create the issue first.');
            return;
        }
        
        if (!issueDropzone || issueDropzone.files.length === 0) {
            showMessage('issueMessage', 'warning', 'Please add at least one image to upload.');
            return;
        }
        
        const $btn = $(this);
        const originalText = $btn.html();
        const messageId = 'issueMessage';
        
        // Disable button and show loading
        $btn.html('<i class="ph-spinner-gap ph-spin me-1"></i>Uploading...').prop('disabled', true);
        
        // Create FormData with images
        const formData = new FormData();
        issueDropzone.files.forEach(function(file, index) {
            formData.append('images[]', file);
        });
        
        // Upload images to the created issue
        $.ajax({
            url: `/block-issues/${issueId}/photos`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    showMessage(messageId, 'success', 'Images uploaded successfully!');
                    
                    // Clear dropzone files
                    if (issueDropzone) {
                        issueDropzone.removeAllFiles();
                    }
                    
                    // Close modal and refresh table after a short delay
                    setTimeout(function() {
                        $('#issueModal').modal('hide');
                        refreshBlockIssuesTable();
                    }, 1000);
                } else {
                    showMessage(messageId, 'danger', 'Error uploading images. Please try again.');
                    $btn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Error uploading images. Please try again.';
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors && errors.images) {
                        errorMessage = errors.images[0];
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                }
                showMessage(messageId, 'danger', errorMessage);
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });
    
    // Handle "Skip & Close" button - just close the modal
    $(document).on('click', '#skipUploadBtn', function(e) {
        // Refresh table to show the newly created issue
        refreshBlockIssuesTable();
    });
    
    // Reset form when modal is closed
    $('#issueModal').on('hidden.bs.modal', function() {
        resetStepForm();
        if (issueDropzone) {
            issueDropzone.removeAllFiles();
        }
    });
    
    // ========================================
    // DELETE CONFIRMATION MODAL
    // ========================================
    
    /**
     * Shows the delete confirmation modal with issue details
     * 
     * @param {number} issueId - The ID of the issue to delete
     * @param {object} issueData - The issue data to display in confirmation
     */
    function issuesShowDeleteConfirmation(issueId, issueData) {
        
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
        $('#confirmDeleteIssueBtn').off('click').on('click', function() {
            issuesDeleteIssue(issueId);
        });
        
        // Show the modal
        $('#deleteIssueModal').modal('show');
    }
    
    /**
     * Deletes an issue via AJAX
     * 
     * @param {number} issueId - The ID of the issue to delete
     */
    function issuesDeleteIssue(issueId) {
        
        // Show loading state
        const $confirmBtn = $('#confirmDeleteIssueBtn');
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
    window.issuesShowDeleteConfirmation = issuesShowDeleteConfirmation;
    window.issuesConfirmDeletion = issuesShowDeleteConfirmation;
    window.issuesDeleteIssue = issuesDeleteIssue;
    
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
                    const issueTitle = issue.issue || 'N/A';
                    const issueRef = issue.ref_no || 'N/A';
                    const issueType = issue.issue_type ? issue.issue_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Not specified';
                    const issueUnit = issue.block_unit ? (issue.block_unit.unit_name || issue.block_unit.unit_code || `Unit #${issue.block_unit.id}`) : 'No unit linked';
                    
                    $('#photoUploadModalIssueTitle').text(issueTitle);
                    $('#photoUploadModalIssueRef').text(issueRef);
                    $('#photoUploadModalType').text(`Type: ${issueType}`);
                    $('#photoUploadModalUnit').text(issueUnit);
                    
                    const priorityBadge = getPriorityBadge(issue.priority_id) || '<span class="badge bg-secondary">Priority: N/A</span>';
                    const statusBadge = getStatusBadge(issue.issue_status_id) || '<span class="badge bg-secondary">Status: N/A</span>';
                    
                    $('#photoUploadModalPriority').html(priorityBadge);
                    $('#photoUploadModalStatus').html(statusBadge);
                    
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
                <div class="col-md-3 col-6 mb-2">
                    <div class="card">
                        <img src="/storage/${photo.image_path}/${photo.image_name}" 
                             class="card-img-top" 
                             style="height: 60px; object-fit: cover; cursor: pointer;"
                             alt="Photo ${index + 1}"
                             onclick="previewPhoto('${photo.image_path}/${photo.image_name}', '${photo.image_name}', ${index})">
                        <div class="card-body p-1">
                            <small class="text-muted d-block text-truncate" style="font-size: 10px;">${photo.image_name}</small>
                            <button type="button" class="btn btn-xs btn-outline-danger float-end" 
                                    onclick="confirmDeletePhoto(${photo.id}, '${photo.image_name}')" title="Delete Photo"
                                    style="padding: 2px 6px; font-size: 10px;">
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
     * Refreshes units autocomplete with fresh data from database
     * 
     * Fetches the latest units for the current block and updates
     * the autocomplete data before opening the modal
     */
    function refreshUnitsDropdown() {
        const blockId = window.blockId || $('input[name="block_id"]').val();
        
        console.log('refreshUnitsDropdown called for block ID:', blockId);
        
        // Show loading state for units input
        const $unitsInput = $('#issue_block_unit_id');
        const $unitsHidden = $('#issue_block_unit_id_hidden');
        
        // Store current value to preserve selection if modal is in edit mode
        const currentUnitValue = $unitsInput.val();
        const currentUnitId = $unitsHidden.val();
        
        // Set loading state
        $unitsInput.val('Loading units...').prop('disabled', true);
        
        // Fetch fresh units data
        $.ajax({
            url: `/block-units/block/${blockId}`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Units API response:', response);
                let unitsData = [];
                
                if (response.success && response.data && response.data.length > 0) {
                    unitsData = response.data.map(function(unit) {
                        const labelParts = [];
                        if (unit.unit_code) {
                            labelParts.push(unit.unit_code);
                        }
                        if (unit.unit_name && unit.unit_name !== unit.unit_code) {
                            labelParts.push(unit.unit_name);
                        }
                        const label = labelParts.length > 0 ? labelParts.join(' - ') : `Unit #${unit.id}`;
                        const searchTokens = [unit.unit_code, unit.unit_name]
                            .filter(Boolean)
                            .join(' ')
                            .toLowerCase();
                        
                        return {
                            value: unit.id,
                            label: label,
                            unit_code: unit.unit_code,
                            unit_name: unit.unit_name,
                            searchValue: searchTokens
                        };
                    });
                    console.log('Mapped units data:', unitsData);
                } else {
                    console.warn('No units found in response');
                }
                
                $unitsInput.val('').prop('disabled', false);
                
                // Initialize or refresh AutoComplete.js
                initializeUnitAutoComplete(unitsData);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching units:', error);
                console.error('XHR:', xhr);
                $unitsInput.val('Error loading units').prop('disabled', false);
            }
        });
    }
    
    /**
     * Refreshes units autocomplete with fresh data from database for edit mode
     * 
     * Fetches the latest units for the current block and updates
     * the autocomplete data, preserving the selected unit
     * 
     * @param {number} selectedUnitId - The ID of the unit to select
     */
    function refreshUnitsDropdownForEdit(selectedUnitId) {
        const blockId = window.blockId || $('input[name="block_id"]').val();
        
        // Show loading state for units input
        const $unitsInput = $('#issue_block_unit_id');
        const $unitsHidden = $('#issue_block_unit_id_hidden');
        
        // Set loading state
        $unitsInput.val('Loading units...').prop('disabled', true);
        
        // Fetch fresh units data
        $.ajax({
            url: `/block-units/block/${blockId}`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let unitsData = [];
                let selectedUnit = null;
                
                if (response.success && response.data && response.data.length > 0) {
                    unitsData = response.data.map(function(unit) {
                        const labelParts = [];
                        if (unit.unit_code) {
                            labelParts.push(unit.unit_code);
                        }
                        if (unit.unit_name && unit.unit_name !== unit.unit_code) {
                            labelParts.push(unit.unit_name);
                        }
                        const label = labelParts.length > 0 ? labelParts.join(' - ') : `Unit #${unit.id}`;
                        const searchTokens = [unit.unit_code, unit.unit_name]
                            .filter(Boolean)
                            .join(' ')
                            .toLowerCase();
                        
                        const unitObj = {
                            value: unit.id,
                            label: label,
                            unit_code: unit.unit_code,
                            unit_name: unit.unit_name,
                            searchValue: searchTokens
                        };
                        
                        // Check if this is the selected unit
                        if (unit.id == selectedUnitId) {
                            selectedUnit = unitObj;
                        }
                        
                        return unitObj;
                    });
                }
                
                $unitsInput.prop('disabled', false);
                
                // Initialize or refresh AutoComplete.js
                initializeUnitAutoComplete(unitsData);
                
                // Set the selected unit if found
                if (selectedUnit) {
                    $unitsInput.val(selectedUnit.label);
                    $unitsHidden.val(selectedUnit.value);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching units for edit:', error);
                $unitsInput.val('Error loading units').prop('disabled', false);
            }
        });
    }
    
    /**
     * Initialize AutoComplete.js for the units dropdown
     * 
     * Creates a searchable autocomplete with keyboard navigation
     * 
     * @param {Array} unitsData - Array of unit objects for autocomplete
     */
    function initializeUnitAutoComplete(unitsData) {
        // Prevent multiple simultaneous initializations
        if (isInitializingAutoComplete) {
            console.warn('AutoComplete initialization already in progress, skipping...');
            return;
        }
        
        isInitializingAutoComplete = true;
        
        const unitsInput = document.getElementById('issue_block_unit_id');
        const unitsHidden = document.getElementById('issue_block_unit_id_hidden');
        
        console.log('initializeUnitAutoComplete called with data:', unitsData);
        
        const escapeRegExp = (string) => string ? string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') : '';
        
        const uniqueUnits = Array.from(
            new Map(
                (Array.isArray(unitsData) ? unitsData : [])
                    .filter(unit => unit && typeof unit.value !== 'undefined' && unit.value !== null)
                    .map(unit => [unit.value, unit])
            ).values()
        );
        
        console.log('Unique units data for autocomplete:', uniqueUnits);
        console.log('unitsInput element:', unitsInput);
        
        if (!unitsInput) {
            console.error('Unit input element not found!');
            isInitializingAutoComplete = false;
            return;
        }
        
        // Destroy existing AutoComplete instance if it exists
        if (unitAutoComplete) {
            console.log('Destroying existing AutoComplete instance');
            try {
                unitAutoComplete.unInit();
            } catch (e) {
                console.warn('Error destroying autocomplete:', e);
            }
            unitAutoComplete = null;
        }
        
        // Remove any existing autocomplete list elements from DOM
        const existingLists = document.querySelectorAll('[id^="autoComplete_list"]');
        existingLists.forEach(list => {
            console.log('Removing existing autocomplete list:', list.id);
            list.remove();
        });
        
        // Check if AutoComplete is available
        if (typeof autoComplete === 'undefined') {
            console.error('AutoComplete library is not loaded!');
            isInitializingAutoComplete = false;
            return;
        }
        
        console.log('Initializing AutoComplete with', uniqueUnits.length, 'units');
        
        // Initialize new AutoComplete instance
        try {
            unitAutoComplete = new autoComplete({
                selector: "#issue_block_unit_id",
                placeHolder: "Search for units...",
                data: {
                    src: uniqueUnits,
                    keys: ["searchValue"]
                },
                resultItem: {
                    highlight: false,
                    element: (item, data) => {
                        const label = data.value.label || '';
                        const query = (data.query || '').trim();
                        
                        if (!query) {
                            item.innerHTML = label;
                            return;
                        }
                        
                        const regex = new RegExp(escapeRegExp(query), 'ig');
                        const highlighted = label.replace(
                            regex,
                            match => `<span class="text-danger fw-semibold">${match}</span>`
                        );
                        
                        item.innerHTML = highlighted;
                    }
                },
                events: {
                    input: {
                        selection: (event) => {
                            console.log('AutoComplete selection event:', event.detail);
                            const selection = event.detail.selection;
                            console.log('Selection object:', selection);
                            console.log('Selection value:', selection.value);
                            
                            // The selection.value contains the actual unit data
                            const selectedUnit = selection.value;
                            console.log('Selected unit:', selectedUnit);
                            
                            if (selectedUnit && selectedUnit.value) {
                                // Update the visible input with the selected label
                                unitsInput.value = selectedUnit.label;
                                // Update the hidden input with the selected value (unit ID)
                                unitsHidden.value = selectedUnit.value;
                                
                                // Populate contact details from unit
                                console.log('Calling getUnitContactDetails with unit ID:', selectedUnit.value);
                                getUnitContactDetails(selectedUnit.value);
                                
                                console.log('Calling fetchUnitIssues with unit ID:', selectedUnit.value);
                                // Fetch and display issues for the selected unit
                                fetchUnitIssues(selectedUnit.value);
                                
                                console.log('Calling loadActiveIssuesForUnit with unit ID:', selectedUnit.value);
                                // Load active issues for the modal table
                                loadActiveIssuesForUnit(selectedUnit.value);
                            } else {
                                console.log('No unit found for selection:', selection);
                            }
                        }
                    }
                },
                threshold: 1,
                debounce: 300,
                searchEngine: function (query, record) {
                    if (!record) return 0;
                    return record.toLowerCase().includes(query.toLowerCase()) ? 1 : 0;
                },
                maxResults: 10
            });
            
            console.log('AutoComplete successfully initialized');
            isInitializingAutoComplete = false;
            
        } catch (error) {
            console.error('Error initializing AutoComplete.js:', error);
            isInitializingAutoComplete = false;
        }
    }
    
    /**
     * Fetch and display issues for a specific unit
     * 
     * @param {number} unitId - The ID of the unit to fetch issues for
     */
    function fetchUnitIssues(unitId) {
        console.log('fetchUnitIssues called with unitId:', unitId);
        if (!unitId) {
            console.log('No unitId provided, returning');
            return;
        }
        
        console.log('Fetching issues for unit ID:', unitId);
        // Show loading state
        if (blockIssuesDataTable && typeof blockIssuesDataTable.processing === 'function') {
            blockIssuesDataTable.processing(true);
        } else {
            console.log('DataTable not ready or processing method not available');
        }
        
        // Fetch issues for the specific unit
        $.ajax({
            url: `/block-issues/unit/${unitId}`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('API response:', response);
                if (response.success && response.data) {
                    // Clear existing data and add new data
                    if (blockIssuesDataTable) {
                        blockIssuesDataTable.clear();
                        blockIssuesDataTable.rows.add(response.data);
                        blockIssuesDataTable.draw();
                    }
                } else {
                    // Clear table if no issues found
                    if (blockIssuesDataTable) {
                        blockIssuesDataTable.clear();
                        blockIssuesDataTable.draw();
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching unit issues:', error);
                console.error('XHR:', xhr);
                console.error('Status:', status);
                // Clear table on error
                if (blockIssuesDataTable) {
                    blockIssuesDataTable.clear();
                    blockIssuesDataTable.draw();
                }
            },
            complete: function() {
                // Hide loading state
                if (blockIssuesDataTable && typeof blockIssuesDataTable.processing === 'function') {
                    blockIssuesDataTable.processing(false);
                }
            }
        });
    }
    
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
     * Get category badge HTML
     */
    function getIssueTypeBadge(issueType) {
        if (issueType) {
            const formattedType = issueType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            return `<span class="badge bg-secondary">${formattedType}</span>`;
        }
        return '<span class="text-muted">N/A</span>';
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
    
    /**
     * Get unit contact details and populate default contact details
     */
    function getUnitContactDetails(unitId) {
        if (!unitId) return;
        
        // Only populate if use_default_contact is checked
        if (!$('#use_default_contact').is(':checked')) {
            return;
        }
        
        $.ajax({
            url: '/api/block-unit-contact-details',
            method: 'GET',
            data: {
                block_unit_id: unitId
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(data) {
                if (data.success && data.data) {
                    const unit = data.data;
                    
                    // Use the formatted contact_details string from the API
                    if (unit.contact_details) {
                        $('#default_contact_details').val(unit.contact_details);
                    } else {
                        // Fallback: build contact details string if API doesn't provide formatted string
                        let contactDetails = '';
                        if (unit.mobile_no) {
                            contactDetails += `Mobile: ${unit.mobile_no}`;
                        }
                        if (unit.phone_number) {
                            contactDetails += contactDetails ? `\nPhone: ${unit.phone_number}` : `Phone: ${unit.phone_number}`;
                        }
                        if (unit.email) {
                            contactDetails += contactDetails ? `\nEmail: ${unit.email}` : `Email: ${unit.email}`;
                        }
                        if (unit.owners_name) {
                            contactDetails += contactDetails ? `\nOwner: ${unit.owners_name}` : `Owner: ${unit.owners_name}`;
                        }
                        $('#default_contact_details').val(contactDetails);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading unit contact details:', error);
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
    
    // Clear messages and refresh units dropdown when issue modal is opened
    $('#issueModal').on('show.bs.modal', function() {
        // Use setTimeout to ensure DOM is ready before clearing message
        setTimeout(function() {
            clearMessage('issueMessage');
        }, 50);
        
        // Refresh units dropdown with latest data
        refreshUnitsDropdown();
    });
    
    
    // Cleanup AutoComplete.js instance when modal is hidden
    $('#issueModal').on('hidden.bs.modal', function() {
        console.log('Modal hidden - cleaning up autocomplete');
        if (unitAutoComplete) {
            try {
                unitAutoComplete.unInit();
            } catch (e) {
                console.warn('Error cleaning up autocomplete:', e);
            }
            unitAutoComplete = null;
        }
        
        // Reset initialization flag
        isInitializingAutoComplete = false;
        
        // Remove any remaining autocomplete list elements from DOM
        const existingLists = document.querySelectorAll('[id^="autoComplete_list"]');
        existingLists.forEach(list => {
            console.log('Cleanup: Removing autocomplete list:', list.id);
            list.remove();
        });
    });
    
    // Handle assigned_to change to populate default contact details
    // Note: Unit contact details take priority over property manager
    $('#assigned_to').on('change', function() {
        const userId = $(this).val();
        if (userId && $('#use_default_contact').is(':checked')) {
            // Only populate from property manager if no unit is selected
            const unitId = $('#issue_block_unit_id_hidden').val();
            if (!unitId) {
                getUserDetails(userId);
            }
        }
    });
    
    // Handle default contact checkbox change
    $('#use_default_contact').on('change', function() {
        if (this.checked) {
            $('#default_contact_details').prop('readonly', true).addClass('bg-light');
            // Prioritize unit contact details over property manager
            const unitId = $('#issue_block_unit_id_hidden').val();
            if (unitId) {
                getUnitContactDetails(unitId);
            } else {
                // Fallback to property manager if no unit selected
                const userId = $('#assigned_to').val();
                if (userId) {
                    getUserDetails(userId);
                }
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
        // Destroy existing dropzone if it exists
        if (photoDropzone) {
            photoDropzone.destroy();
            photoDropzone = null;
        }
        initializePhotoDropzone();
    });
    
    // Cleanup when modal is hidden
    $('#photoUploadModal').on('hidden.bs.modal', function() {
        if (photoDropzone) {
            photoDropzone.destroy();
            photoDropzone = null;
        }
    });
    
    // Initialize Photo Dropzone
    function initializePhotoDropzone() {
        // Disable auto discover to prevent conflicts
        Dropzone.autoDiscover = false;
        
        // Ensure element is clean
        const dropzoneElement = document.getElementById('photoDropzone');
        if (dropzoneElement.dropzone) {
            dropzoneElement.dropzone.destroy();
        }
        
        photoDropzone = new Dropzone("#photoDropzone", {
            url: "#", // Disable auto-upload
                paramName: "images",
                uploadMultiple: true,
                parallelUploads: 10,
                maxFiles: 10,
                maxFilesize: 2, // 2MB per file
                acceptedFiles: "image/*",
                addRemoveLinks: true,
                clickable: true, // Enable click to upload
                autoProcessQueue: false, // Don't auto-upload
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
                
                // Handle file addition (preview mode)
                this.on("addedfile", function(file) {
                    // Add custom styling to file preview
                    const preview = file.previewElement;
                    $(preview).addClass('dz-image-preview-custom');
                    
                    // Add file size info
                    const sizeInfo = $(preview).find('.dz-size');
                    if (sizeInfo.length === 0) {
                        $(preview).find('.dz-details').append('<div class="dz-size"><span data-dz-size></span></div>');
                    }
                    
                    // Show preview message
                    showPhotoMessage('info', 'Photos added to preview. Click "Upload Photos" to save them.');
                });
                
                // Handle successful upload
                this.on("successmultiple", function(files, response) {
                    showPhotoMessage('success', 'Photos uploaded successfully!');
                    
                    // Clear dropzone
                    dz.removeAllFiles(true);
                    
                    // Reload existing photos
                    loadExistingPhotos(currentIssueId);
                    
                    // Close modal after successful upload
                    setTimeout(() => {
                        $('#photoUploadModal').modal('hide');
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
        // Clear any photo upload messages
        $('#photoUploadMessage').addClass('d-none');
    });
    
    // Upload photos when submit button is clicked
    $('#uploadPhotosBtn').on('click', function() {
        if (photoDropzone && photoDropzone.files.length > 0) {
            // Set the correct URL for upload
            photoDropzone.options.url = `/block-issues/${currentIssueId}/photos`;
            
            // Process the queue
            photoDropzone.processQueue();
        } else {
            showPhotoMessage('warning', 'Please select photos to upload.');
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
    // ACTIVE ISSUES EDIT FUNCTIONALITY
    // ========================================
    
    /**
     * Edit an active issue from the table using existing data
     * 
     * @param {object} issue - The issue object with all data
     */
    window.editActiveIssue = function(issue) {
        // Parse issue if it's a string (from onclick attribute)
        if (typeof issue === 'string') {
            try {
                issue = JSON.parse(issue.replace(/&quot;/g, '"'));
            } catch (e) {
                console.error('Error parsing issue data:', e);
                showMessage('issueMessage', 'danger', 'Error loading issue data');
                return;
            }
        }
        
        // Set up modal for edit mode
        const $modal = $('#issueModal');
        const $modalLabel = $('#issueModalLabel');
        const $form = $('#issueForm');
        const $submitBtn = $('#issueSubmitBtn');
        
        // Configure modal for edit mode
        $modalLabel.text('Edit Issue');
        $submitBtn.html('<i class="ph-check me-1"></i> Update');
        $form.attr('action', window.routes?.blockIssues?.update?.replace(':id', issue.id) || `/block-issues/${issue.id}`);
        
        // Add PUT method for edit
        if ($form.find('input[name="_method"]').length === 0) {
            $form.append('<input type="hidden" name="_method" value="PUT">');
        }
        
        // Clear any previous messages
        clearMessage('issueMessage');
        
        // Refresh units dropdown with the current unit selected
        refreshUnitsDropdownForEdit(issue.block_unit_id);
        
        // Populate all form fields with existing data
        $('#contact_method_id').val(issue.contact_method_id || '');
        $('#assigned_to').val(issue.assigned_to?.id || issue.assigned_to || '').trigger('change');
        $('#issue_type').val(issue.issue_type || '');
        $('#priority_id').val(issue.priority_id || '');
        $('#issue').val(issue.issue || '');
        $('#contact_details').val(issue.contact_details || '');
        $('#issue_details').val(issue.issue_details || '');
        $('#default_contact_details').val(issue.default_contact_details || '');
        
        // Handle use_default_contact checkbox
        if (issue.default_contact_details && issue.default_contact_details.trim() !== '') {
            $('#use_default_contact').prop('checked', true);
        } else {
            $('#use_default_contact').prop('checked', false);
        }
        
        // Trigger change event to update dependent fields
        $('#use_default_contact').trigger('change');
    };
    
    // ========================================
    // EVENT HANDLERS
    // ========================================
    
    // Handle unit selection change to load active issues and populate contact details
    $('#issue_block_unit_id_hidden').on('change', function() {
        const unitId = $(this).val();
        if (unitId) {
            // Populate contact details from unit
            getUnitContactDetails(unitId);
            // Load active issues for the unit
            loadActiveIssuesForUnit(unitId);
        }
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
