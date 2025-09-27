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
     * @param {string} message - Message to display
     * @param {string} type - Message type ('success' or 'error')
     */
    function showMessage(message, type = 'success') {
        const $messageDiv = $('#blockInformationMessage');
        $messageDiv.removeClass('d-none alert-success alert-danger')
                  .addClass(`alert-${type}`)
                  .find('.message-text')
                  .text(message);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            $messageDiv.addClass('d-none');
        }, 3000);
    }
    
    /**
     * Clears form and resets modal state
     */
    function clearForm() {
        $('#blockInformationForm')[0].reset();
        $('#blockInformationMessage').addClass('d-none');
        $('#blockInformationForm input[name="id"]').remove();
        $('#blockInformationModalLabel').text('Add Block Information');
    }
    
    /**
     * Initializes DataTable with proper configuration
     */
    function initializeDataTable() {
        if ($('#blockInformationTable').length) {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#blockInformationTable')) {
                $('#blockInformationTable').DataTable().destroy();
                blockInformationDataTable = null;
            }

            // Verify table structure before initialization
            const $table = $('#blockInformationTable');
            const headerCols = $table.find('thead tr th').length;
            const bodyRows = $table.find('tbody tr');

            console.log('Initializing DataTable - Headers:', headerCols, 'Body rows:', bodyRows.length);

            // Initialize DataTable regardless of data - it will handle empty tables
            if (headerCols === 5) { // Ensure we have exactly 5 columns
                    blockInformationDataTable = $table.DataTable({
                        responsive: true,
                        dom: 'lfrtip',
                        order: [[2, 'desc']],
                        columnDefs: [
                            { targets: [4], orderable: false }
                        ],
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                        language: {
                            lengthMenu: "Show _MENU_ information entries per page",
                            info: "Showing _START_ to _END_ of _TOTAL_ information entries",
                            infoEmpty: "Showing 0 to 0 of 0 information entries",
                            infoFiltered: "(filtered from _MAX_ total information entries)",
                            search: "Search information:",
                            searchPlaceholder: "Search by type, description...",
                            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" },
                            emptyTable: "No block information available. Click 'Add Block Information' to get started."
                        },
                        initComplete: function() {
                            $('.dataTables_filter input')
                                .addClass('form-control custom-search-input mb-3')
                                .css({'width': '300px', 'height': '38px', 'font-size': '14px'});
                            $('.dataTables_length select')
                                .addClass('form-select custom-page-length-select')
                                .css({'width': 'auto', 'height': '38px', 'font-size': '14px'});
                        }
                    });
                    
                    const initialRowCount = blockInformationDataTable.rows().count();
                    toggleExportButtons(initialRowCount > 0);
                    console.log('DataTable initialized successfully with', initialRowCount, 'rows');
                } else {
                    console.warn('Block Information Table: Expected 5 columns but found', headerCols);
                    console.warn('Skipping DataTable initialization to prevent errors');
                }
        }
    }
    
    /**
     * Refreshes the block information DataTable with fresh data from the server
     * 
     * This function is exposed globally so it can be called from other parts of the application.
     * It fetches the latest block information data for the current block and updates the DataTable.
     * 
     * @global
     */
    window.refreshBlockInformationTable = function() {
        const blockId = window.blockId || $('input[name="block_id"]').val() || {{ $block->id }};
        if (!blockId) {
            console.warn('No block ID found for refresh');
            return;
        }
        
        console.log('Refreshing block information table for block ID:', blockId);
        
        $.ajax({
            url: `/block-information/get-by-block/${blockId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    console.log('Received data for refresh:', data.data.length, 'items');
                    
                    // If DataTable is not initialized, initialize it first
                    if (!blockInformationDataTable || !$.fn.DataTable.isDataTable('#blockInformationTable')) {
                        console.log('DataTable not initialized, initializing now...');
                        initializeDataTable();
                    }
                    
                    // If still no DataTable, fallback to page reload
                    if (!blockInformationDataTable) {
                        console.warn('DataTable initialization failed, reloading page');
                        location.reload();
                        return;
                    }
                    
                    // Clear and repopulate DataTable
                    blockInformationDataTable.clear();
                    
                    data.data.forEach(function(info) {
                        const informationType = info.information_type_name || 'N/A';
                        const description = info.description && info.description.length > 50 
                            ? info.description.substring(0, 50) + '...' 
                            : info.description || 'No description provided';
                        
                        const addedDate = info.created_at 
                            ? new Date(info.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: '2-digit'
                            })
                            : 'N/A';
                        
                        blockInformationDataTable.row.add([
                            `<span class="fw-semibold">${informationType}</span>`,
                            description,
                            addedDate,
                            info.creator_name || 'N/A',
                            `<button class="btn btn-sm btn-outline-primary" onclick="editBlockInformation(${info.id})" title="Edit Block Information">
                                <i class="ph-pencil"></i>
                            </button> 
                            <button class="btn btn-sm btn-outline-info" onclick="viewBlockInformationDetails(${info.id})" title="View Details">
                                <i class="ph-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="showDeleteConfirmation(${info.id}, {
                                type: '${informationType}',
                                description: '${description.replace(/'/g, "\\'")}',
                                added_date: '${addedDate}',
                                added_by: '${(info.creator_name || 'N/A').replace(/'/g, "\\'")}'
                            })" title="Delete Block Information">
                                <i class="ph-trash"></i>
                            </button>`
                        ]);
                    });
                    
                    blockInformationDataTable.draw();
                    
                    // Toggle export buttons based on data availability
                    toggleExportButtons(data.data.length > 0);
                    console.log('Block information table refreshed successfully');
                } else {
                    console.error('Failed to refresh data:', data.message);
                    // Disable export buttons on error
                    toggleExportButtons(false);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error refreshing block information data:', xhr);
                // Disable export buttons on error
                toggleExportButtons(false);
            }
        });
    };
    
    /**
     * Refreshes the block information data via AJAX (legacy function for backward compatibility)
     */
    function refreshBlockInformationData() {
        if (!$.fn.DataTable.isDataTable('#blockInformationTable')) {
            initializeDataTable();
        }
        if (typeof window.refreshBlockInformationTable === 'function') {
            window.refreshBlockInformationTable();
        } else {
            // If refresh function is not available, reload the page
            location.reload();
        }
    }
    
    // ========================================
    // MODAL FUNCTIONS
    // ========================================
    
    /**
     * Opens the block information modal for add/edit
     * 
     * @param {string} mode - 'add' or 'edit'
     * @param {number} id - Block information ID (for edit mode)
     */
    window.openBlockInformationModal = function(mode, id = null) {
        // Only clear form for add mode
        if (mode === 'add') {
            clearForm();
        }
        
        if (mode === 'edit' && id) {
            $('#blockInformationModalLabel').text('Edit Block Information');
            
            // Clear any existing hidden ID field first
            $('#blockInformationForm input[name="id"]').remove();
            
            // Load existing data for editing
            $.ajax({
                url: `/block-information/${id}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Store data globally for use when modal is shown
                        window.editBlockInformationData = data;
                    } else {
                        showMessage('Error loading block information data', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading block information:', xhr.responseText);
                    showMessage('Error loading block information data', 'error');
                }
            });
        }
        
        $('#blockInformationModal').modal('show');
    };
    
    /**
     * Edits a block information entry
     * 
     * @param {number} id - Block information ID
     */
    window.editBlockInformation = function(id) {
        openBlockInformationModal('edit', id);
    };
    
    /**
     * Views block information details
     * 
     * @param {number} id - Block information ID
     */
    window.viewBlockInformationDetails = function(id) {
        $.ajax({
            url: `/block-information/${id}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#viewInformationType').text(data.information_type_name || 'N/A');
                    $('#viewDescription').text(data.description || 'No description provided');
                    $('#viewAddedDate').text(data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A');
                    $('#viewAddedBy').text(data.creator_name || 'N/A');
                    
                    $('#viewBlockInformationModal').modal('show');
                }
            },
            error: function(xhr) {
                showMessage('Error loading block information details', 'error');
            }
        });
    };
    
    /**
     * Shows delete confirmation modal
     * 
     * @param {number} id - Block information ID
     * @param {Object} data - Block information data for display
     */
    window.showDeleteConfirmation = function(id, data) {
        $('#deleteInformationType').text(data.type || 'N/A');
        $('#deleteDescription').text(data.description || 'No description');
        $('#deleteAddedDate').text(data.added_date || 'N/A');
        $('#deleteAddedBy').text(data.added_by || 'N/A');
        $('#deleteBlockInformationId').val(id);
        
        $('#deleteBlockInformationModal').modal('show');
    };
    
    // ========================================
    // FORM HANDLERS
    // ========================================
    
    /**
     * Handles form submission for add/edit
     */
    $('#blockInformationForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = formData.get('id') ? 
            `/block-information/${formData.get('id')}` : 
            '{{ route("block-information.store") }}';
        const method = formData.get('id') ? 'PUT' : 'POST';
        
        // Add method override for PUT request
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        // Add CSRF token to form data
        formData.append('_token', '{{ csrf_token() }}');
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    setTimeout(() => {
                        $('#blockInformationModal').modal('hide');
                        window.refreshBlockInformationTable();
                    }, 800);
                } else {
                    showMessage(response.message || 'An error occurred', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while saving block information';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join(', ');
                } else if (xhr.status === 422) {
                    errorMessage = 'Validation failed. Please check your input.';
                } else if (xhr.status === 419) {
                    errorMessage = 'CSRF token mismatch. Please refresh the page and try again.';
                }
                
                showMessage(errorMessage, 'error');
                console.error('Block Information Save Error:', xhr.responseJSON || xhr);
            }
        });
    });
    
    /**
     * Handles delete confirmation
     */
    $('#confirmDeleteBlockInformation').on('click', function() {
        const id = $('#deleteBlockInformationId').val();
        
        if (!id) {
            showMessage('Invalid block information ID', 'error');
            return;
        }
        
        $.ajax({
            url: `/block-information/${id}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteBlockInformationModal').modal('hide');
                    showMessage(response.message || 'Block information deleted successfully', 'success');
                    setTimeout(() => {
                        window.refreshBlockInformationTable();
                    }, 800);
                } else {
                    showMessage(response.message || 'Error deleting block information', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error deleting block information';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showMessage(errorMessage, 'error');
            }
        });
    });
    
    // ========================================
    // REFRESH TRIGGER FUNCTIONS
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
    function triggerBlockInformationRefreshDebounced() {
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
        }, 300);
    }
    
    // ========================================
    // EVENT LISTENERS
    // ========================================
    
    // Listen for Bootstrap tab shown event to refresh data when block information tab becomes active
    $(document).on('shown.bs.tab', '#block-info-tab', function(e) {
        triggerBlockInformationRefreshDebounced();
    });

    // If Block Information tab is already active on page load, refresh once to ensure data is loaded
    if ($('#block-info').hasClass('show') && $('#block-info').hasClass('active')) {
        triggerBlockInformationRefreshDebounced();
    }
    
    // Handle modal events
    $('#blockInformationModal').on('hidden.bs.modal', function() {
        clearForm();
        // Clear global edit data
        window.editBlockInformationData = null;
    });
    
    // Handle modal shown event for edit mode
    $('#blockInformationModal').on('shown.bs.modal', function() {
        if (window.editBlockInformationData) {
            const data = window.editBlockInformationData;
            
            // Populate form fields
            $('#information_type_id').val(data.information_type_id);
            $('#description').val(data.description);
            
            // Add hidden ID field for update
            $('#blockInformationForm').append(`<input type="hidden" name="id" value="${data.id}">`);
            
            // Clear the global data
            window.editBlockInformationData = null;
        }
    });
    
    // Initialize DataTable on page load
    initializeDataTable();
});
</script>