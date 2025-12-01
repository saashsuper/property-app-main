<script>
(function() {
    let workOrdersDT = null;
    
    // Expose refresh functions immediately so they're available for inline scripts
    // These will be properly defined later, but we create placeholders now
    window.refreshTable = window.refreshTable || function() {
        console.warn('refreshTable not yet initialized, will retry...');
        setTimeout(() => {
            if (typeof window.refreshTable === 'function' && window.refreshTable.toString().includes('workOrdersDT')) {
                window.refreshTable();
            } else if (typeof window.refreshWorkOrdersTable === 'function') {
                window.refreshWorkOrdersTable();
            }
        }, 100);
    };
    
    window.refreshWorkOrdersTable = window.refreshWorkOrdersTable || function() {
        console.warn('refreshWorkOrdersTable not yet initialized');
    };
    
    // Fetch work order function (similar to fetchInspection in inspection modal)
    function fetchWorkOrder(workOrderId) {
        if (!workOrderId) {
            return;
        }

        fetch(`/block-work-orders/${workOrderId}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(resp => resp.json())
            .then(data => {
                if (!data.success) {
                    showToast('danger', data.message || 'Failed to load work order details.');
                    return;
                }
                populateEditModal(data.data);
            })
            .catch(error => {
                console.error('Work order fetch error', error);
                showToast('danger', 'Error fetching work order details.');
            });
    }
    
    // Expose fetchWorkOrder globally for backwards compatibility
    window.editWorkOrder = fetchWorkOrder;

    const PRIORITY_BADGES = {
        1: '<span class="badge bg-success">Low</span>',
        2: '<span class="badge bg-info">Normal</span>',
        3: '<span class="badge bg-warning">High</span>',
        4: '<span class="badge bg-danger">Urgent</span>',
        5: '<span class="badge bg-dark">Critical</span>',
        default: '<span class="badge bg-secondary">Unknown</span>'
    };

    const STATUS_BADGES = {
        1: '<span class="badge bg-warning">Open</span>',
        2: '<span class="badge bg-info">In Progress</span>',
        3: '<span class="badge bg-success">Completed</span>',
        4: '<span class="badge bg-danger">Cancelled</span>',
        5: '<span class="badge bg-secondary">On Hold</span>',
        default: '<span class="badge bg-secondary">Unknown</span>'
    };

    document.addEventListener('DOMContentLoaded', () => {
        console.log('Work orders scripts: DOMContentLoaded');
        
        try {
            initializeDataTable();
            bindFormHandlers();
            bindModalEvents();
            
            // Verify modal exists
            const createModal = document.getElementById('createWorkOrderModal');
            if (createModal) {
                console.log('Create work order modal found in DOM');
            } else {
                console.error('Create work order modal NOT found in DOM');
            }
            
            const deleteModal = document.getElementById('deleteWorkOrderModal');
            if (deleteModal) {
                console.log('Delete work order modal found in DOM');
            } else {
                console.error('Delete work order modal NOT found in DOM');
            }
            
            // Debounced trigger to avoid duplicate refreshes
            let workOrdersRefreshTimer = null;
            function triggerWorkOrdersRefresh() {
                clearTimeout(workOrdersRefreshTimer);
                workOrdersRefreshTimer = setTimeout(function() {
                    if (!$.fn.DataTable.isDataTable('#workOrdersTable')) {
                        initializeDataTable();
                    }
                    if (typeof window.refreshWorkOrdersTable === 'function') {
                        window.refreshWorkOrdersTable();
                    }
                }, 50);
            }

            // Listen for Bootstrap tab shown event to refresh data when work-orders tab becomes active
            $(document).on('shown.bs.tab', '#work-orders-tab', function(e) {
                triggerWorkOrdersRefresh();
            });

            // If Work Orders tab is already active on page load, refresh once to ensure data is loaded
            if ($('#work-orders').hasClass('show') && $('#work-orders').hasClass('active')) {
                triggerWorkOrdersRefresh();
            }
        } catch (error) {
            console.error('Error initializing work orders:', error);
        }
    });

    function initializeDataTable() {
        const $table = $('#workOrdersTable');
        if (!$table.length) {
            return;
        }

        // Check if DataTable is already initialized to prevent conflicts
        if (!$.fn.DataTable.isDataTable('#workOrdersTable')) {
            workOrdersDT = $table.DataTable({
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                order: [[0, 'desc']], // default sort by Ref No descending
                columnDefs: [
                    { targets: [8], orderable: false }, // Actions column
                    { targets: [0], width: '8%' },   // Ref No
                    { targets: [1], width: '10%' },  // Unit
                    { targets: [2], width: '18%' },   // Issue
                    { targets: [3], width: '8%' },    // Priority
                    { targets: [4], width: '8%' },     // Status
                    { targets: [5], width: '10%' },   // Contact
                    { targets: [6], width: '8%' },   // Deadline
                    { targets: [7], width: '12%' },  // Issued By
                    { targets: [8], width: '6%' }     // Actions
                ],
                language: {
                    lengthMenu: 'Show _MENU_ work orders per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ work orders',
                    infoEmpty: 'Showing 0 to 0 of 0 work orders',
                    infoFiltered: '(filtered from _MAX_ total work orders)',
                    search: 'Search work orders:',
                    searchPlaceholder: 'Search by work order number, title, priority, status...',
                    zeroRecords: 'No work orders found',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
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
        }

        workOrdersDT.on('draw', function() {
            // Re-attach edit handlers after DataTable redraws
            attachEditHandlers();
            // Delete handlers use event delegation, so no need to re-attach
        });
    }

    function bindFormHandlers() {
        const createForm = document.getElementById('createWorkOrderForm');
        const editForm = document.getElementById('editWorkOrderForm');

        if (createForm) {
            createForm.addEventListener('submit', handleCreateWorkOrderSubmit);
            console.log('Create work order form handler attached');
        } else {
            console.error('Create work order form not found');
        }
        
        if (editForm) {
            editForm.addEventListener('submit', handleEditWorkOrderSubmit);
            console.log('Edit work order form handler attached');
        } else {
            console.warn('Edit work order form not found (may not be loaded yet)');
        }
        
        window.workOrderAttachEditHandlers = attachEditHandlers;
        attachEditHandlers();
    }

    function bindModalEvents() {
        const createModal = document.getElementById('createWorkOrderModal');
        const editModal = document.getElementById('editWorkOrderModal');

        if (createModal) {
            console.log('Create work order modal found, attaching events');
            
            createModal.addEventListener('show.bs.modal', () => {
                console.log('Create work order modal opening');
                const msg = document.getElementById('createWorkOrderMessage');
                if (msg) msg.innerHTML = '';
                
                // Reset unit and issue selects
                const unitSelect = document.getElementById('work_order_unit_id');
                const issueSelect = document.getElementById('block_issue_id_select');
                
                if (unitSelect) {
                    unitSelect.value = '';
                    console.log('Unit select reset');
                } else {
                    console.error('Unit select not found');
                }
                
                if (issueSelect) {
                    issueSelect.value = '';
                    console.log('Issue select reset');
                } else {
                    console.error('Issue select not found');
                }
            });

            createModal.addEventListener('hidden.bs.modal', () => {
                const form = document.getElementById('createWorkOrderForm');
                if (form) {
                    form.reset();
                    form.classList.remove('was-validated');
                }
                const msg = document.getElementById('createWorkOrderMessage');
                if (msg) msg.innerHTML = '';
            });
            
            // Handle unit selection change for create modal
            const unitSelect = document.getElementById('work_order_unit_id');
            if (unitSelect) {
                unitSelect.addEventListener('change', function() {
                    const unitId = this.value;
                    const selectedOption = this.options[this.selectedIndex];
                    const buildingId = selectedOption ? selectedOption.getAttribute('data-building-id') : '';
                    
                    // Update hidden fields
                    const blockUnitIdField = document.getElementById('work_order_block_unit_id');
                    const blockBuildingIdField = document.getElementById('work_order_block_building_id');
                    
                    if (blockUnitIdField) {
                        blockUnitIdField.value = unitId;
                    }
                    if (blockBuildingIdField && buildingId) {
                        blockBuildingIdField.value = buildingId;
                    }
                });
            }
            
            // Handle unit selection change for edit modal
            const editUnitSelect = document.getElementById('edit_work_order_unit_id');
            if (editUnitSelect) {
                editUnitSelect.addEventListener('change', function() {
                    const unitId = this.value;
                    const selectedOption = this.options[this.selectedIndex];
                    const buildingId = selectedOption ? selectedOption.getAttribute('data-building-id') : '';
                    
                    // Update hidden fields
                    const blockUnitIdField = document.getElementById('edit_work_order_block_unit_id');
                    const blockBuildingIdField = document.getElementById('edit_work_order_block_building_id');
                    
                    if (blockUnitIdField) {
                        blockUnitIdField.value = unitId;
                    }
                    if (blockBuildingIdField && buildingId) {
                        blockBuildingIdField.value = buildingId;
                    }
                });
            }
        }
    }
    
    function loadIssuesForUnit(unitId) {
        const issueSelect = document.getElementById('block_issue_id_select');
        if (!issueSelect) return;
        
        // Show loading state
        issueSelect.disabled = true;
        issueSelect.innerHTML = '<option value="">Loading issues...</option>';
        
        // Fetch issues for the unit
        fetch(`/api/block-unit-active-issues?unit_id=${unitId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                issueSelect.innerHTML = '<option value="">Select Issue</option>';
                data.data.forEach(issue => {
                    const option = document.createElement('option');
                    option.value = issue.id;
                    option.textContent = `${issue.ref_no} - ${issue.issue}`;
                    issueSelect.appendChild(option);
                });
                issueSelect.disabled = false;
            } else {
                issueSelect.innerHTML = '<option value="">No issues found for this unit</option>';
                issueSelect.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error loading issues:', error);
            issueSelect.innerHTML = '<option value="">Error loading issues</option>';
            issueSelect.disabled = true;
        });
    }

        if (editModal) {
            editModal.addEventListener('show.bs.modal', () => {
                // Clear any previous messages
                const msg = document.getElementById('editWorkOrderMessage');
                if (msg) {
                    msg.innerHTML = '';
                    msg.classList.add('d-none');
                }
            });

            editModal.addEventListener('hidden.bs.modal', () => {
                const form = document.getElementById('editWorkOrderForm');
                if (form) {
                    form.reset();
                    form.classList.remove('was-validated');
                }
                const msg = document.getElementById('editWorkOrderMessage');
                if (msg) {
                    msg.innerHTML = '';
                    msg.classList.add('d-none');
                }
            });
        }
    }
    
    function loadIssuesForUnit(unitId) {
        const issueSelect = document.getElementById('block_issue_id_select');
        if (!issueSelect) return;
        
        // Show loading state
        issueSelect.disabled = true;
        issueSelect.innerHTML = '<option value="">Loading issues...</option>';
        
        // Fetch issues for the unit
        fetch(`/api/block-unit-active-issues?unit_id=${unitId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                issueSelect.innerHTML = '<option value="">Select Issue</option>';
                data.data.forEach(issue => {
                    const option = document.createElement('option');
                    option.value = issue.id;
                    option.textContent = `${issue.ref_no} - ${issue.issue}`;
                    issueSelect.appendChild(option);
                });
                issueSelect.disabled = false;
            } else {
                issueSelect.innerHTML = '<option value="">No issues found for this unit</option>';
                issueSelect.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error loading issues:', error);
            issueSelect.innerHTML = '<option value="">Error loading issues</option>';
            issueSelect.disabled = true;
        });
    }

    function attachEditHandlers() {
        // Use event delegation for edit buttons (like inspection modal)
        document.querySelectorAll('.edit-work-order').forEach(button => {
            if (!button.dataset.bound) {
                button.dataset.bound = 'true';
                button.addEventListener('click', function() {
                    const workOrderId = this.getAttribute('data-work-order-id');
                    if (workOrderId) {
                        fetchWorkOrder(workOrderId);
                    }
                });
            }
        });
    }
    

    function handleCreateWorkOrderSubmit(evt) {
        evt.preventDefault();
        evt.stopPropagation();
        const form = evt.currentTarget;
        
        console.log('Create work order form submitted via AJAX', form.action);

        const payload = buildPayload(form);

        setLoading(form.querySelector('button[type="submit"]'), true, 'Creating...');

        submitForm(form.action, 'POST', payload, {
            onSuccess: msg => {
                console.log('Work order created successfully, refreshing table');
                // Hide modal first
                const modalElement = form.closest('.modal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
                // Reset form
                form.reset();
                // Clear any error messages
                const msgContainer = document.getElementById('createWorkOrderMessage');
                if (msgContainer) {
                    msgContainer.innerHTML = '';
                }
                // Refresh table and stay on same tab (matches inspection pattern)
                setTimeout(() => {
                    refreshTable();
                }, 300);
                // No success message - just silently refresh
            },
            onError: msg => {
                console.error('Work order creation error:', msg);
                const container = document.getElementById('createWorkOrderMessage');
                if (container) {
                    container.innerHTML = `<div class="alert alert-danger mb-0">${msg}</div>`;
                } else {
                    showToast('danger', msg);
                }
            },
            onComplete: () => setLoading(form.querySelector('button[type="submit"]'), false)
        });
        
        return false; // Prevent any default form submission
    }

    function handleEditWorkOrderSubmit(evt) {
        evt.preventDefault();
        const form = evt.currentTarget;
        if (!form.checkValidity()) {
            evt.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        const id = form.action.split('/').pop();
        const payload = buildPayload(form);

        setLoading(form.querySelector('button[type="submit"]'), true, 'Updating...');

        submitForm(form.action, 'PUT', payload, {
            onSuccess: msg => {
                // Hide modal first
                bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                // Refresh table and stay on same tab (matches inspection pattern)
                refreshTable();
                // No success message - just silently refresh
            },
            onError: msg => {
                const container = document.getElementById('editWorkOrderMessage');
                if (container) {
                    container.innerHTML = `<div class="alert alert-danger mb-0">${msg}</div>`;
                } else {
                    showToast('danger', msg);
                }
            },
            onComplete: () => setLoading(form.querySelector('button[type="submit"]'), false)
        });
    }

    function buildPayload(form) {
        const data = Object.fromEntries(new FormData(form));
        // Remove ref_no from create form since it's auto-generated at backend
        if (form.id === 'createWorkOrderForm') {
            delete data.ref_no;
            
            // Get block_id from window or hidden field
            const blockId = window.blockId || document.getElementById('work_order_block_id')?.value;
            if (blockId) {
                data.block_id = blockId;
            }
            
            // Get block_issue_id from the select dropdown
            const blockIssueIdSelect = document.getElementById('block_issue_id_select');
            if (blockIssueIdSelect && blockIssueIdSelect.value) {
                data.block_issue_id = blockIssueIdSelect.value;
            }
            
            // Get block_unit_id from the select dropdown or hidden field
            const blockUnitIdSelect = document.getElementById('work_order_unit_id');
            const blockUnitIdHidden = document.getElementById('work_order_block_unit_id');
            if (blockUnitIdSelect && blockUnitIdSelect.value) {
                data.block_unit_id = blockUnitIdSelect.value;
            } else if (blockUnitIdHidden && blockUnitIdHidden.value) {
                data.block_unit_id = blockUnitIdHidden.value;
            }
            
            // Get block_building_id from hidden field
            const blockBuildingIdField = document.getElementById('work_order_block_building_id');
            if (blockBuildingIdField && blockBuildingIdField.value) {
                data.block_building_id = blockBuildingIdField.value;
            }
        }
        return data;
    }

    function submitForm(url, method, data, { onSuccess, onError, onComplete }) {
        const formData = new FormData();
        Object.keys(data).forEach(key => {
            if (data[key] !== null && data[key] !== undefined) {
                if (key === 'images' && Array.isArray(data[key])) {
                    data[key].forEach(file => {
                        if (file instanceof File) {
                            formData.append('images[]', file);
                        }
                    });
                } else if (data[key] instanceof File) {
                    formData.append(key, data[key]);
                } else {
                    formData.append(key, data[key]);
                }
            }
        });

        fetch(url, {
            method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData,
            redirect: 'manual' // Prevent automatic redirect following
        })
            .then(resp => {
                console.log('Response status:', resp.status, 'Content-Type:', resp.headers.get('content-type'));
                
                // Check if response is JSON
                const contentType = resp.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return resp.json();
                } else if (resp.status >= 300 && resp.status < 400) {
                    // This is a redirect response - we should not follow it
                    console.warn('Redirect response received, ignoring it');
                    throw new Error('Server attempted to redirect. This should not happen for AJAX requests.');
                } else {
                    // If not JSON, it might be HTML or something else
                    console.warn('Non-JSON response received:', contentType);
                    return resp.text().then(text => {
                        console.error('Response body:', text.substring(0, 200));
                        throw new Error('Server returned non-JSON response');
                    });
                }
            })
            .then(payload => {
                console.log('Response payload:', payload);
                if (payload.success) {
                    onSuccess && onSuccess(payload.message);
                } else {
                    // Handle validation errors
                    if (payload.errors) {
                        const errorMessages = Object.values(payload.errors).flat().join(', ');
                        onError && onError(errorMessages || payload.message || 'Validation failed.');
                    } else {
                        onError && onError(payload.message || 'Request failed.');
                    }
                }
            })
            .catch(error => {
                console.error('Work order submit error', error);
                onError && onError(error.message || 'Unexpected error. Please try again.');
            })
            .finally(() => {
                onComplete && onComplete();
            });
    }

    function refreshTable() {
        console.log('refreshTable called, workOrdersDT:', workOrdersDT);
        if (workOrdersDT) {
            console.log('Calling refreshWorkOrdersTable');
            refreshWorkOrdersTable();
        } else {
            console.log('DataTable not initialized, checking if table exists...');
            if ($.fn.DataTable.isDataTable('#workOrdersTable')) {
                workOrdersDT = $('#workOrdersTable').DataTable();
                console.log('DataTable found and assigned, calling refreshWorkOrdersTable');
                refreshWorkOrdersTable();
            } else {
                console.log('DataTable not found, returning');
                return;
            }
        }
    }

    /**
     * Refreshes the DataTable with fresh data from the server
     * 
     * This function is exposed globally so it can be called from other parts of the application.
     * It fetches the latest work order data for the current block and updates the DataTable.
     * 
     * @global
     */
    window.refreshWorkOrdersTable = function() {
        console.log('refreshWorkOrdersTable called, workOrdersDT:', workOrdersDT);
        
        // Try to get the DataTable if it's not available
        if (!workOrdersDT) {
            console.log('DataTable not available, trying to get it...');
            if ($.fn.DataTable.isDataTable('#workOrdersTable')) {
                workOrdersDT = $('#workOrdersTable').DataTable();
                console.log('DataTable found and assigned');
            } else {
                console.log('DataTable not found, returning');
                return;
            }
        }
        
        const blockId = window.blockId || $('input[name="block_id"]').val();
        console.log('Block ID:', blockId);
        if (!blockId) {
            console.log('No block ID found, returning');
            return;
        }
        
        console.log('Making AJAX request to:', `/block-work-orders/block/${blockId}`);
        $.ajax({
            url: `/block-work-orders/block/${blockId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Clear and repopulate DataTable
                    workOrdersDT.clear();
                    
                    data.data.forEach(function(workOrder) {
                        const priorityBadge = PRIORITY_BADGES[workOrder.priority_id] || PRIORITY_BADGES.default;
                        const statusBadge = STATUS_BADGES[workOrder.status] || STATUS_BADGES.default;
                        
                        // Format dates
                        const deadlineDate = workOrder.deadline_date ? new Date(workOrder.deadline_date).toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'short', 
                            day: '2-digit' 
                        }) : '<span class="text-muted">N/A</span>';
                        
                        const createdDate = workOrder.created_at ? new Date(workOrder.created_at).toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'short', 
                            day: '2-digit' 
                        }) : 'N/A';
                        
                        // Unit display - handle both camelCase and snake_case
                        const blockUnit = workOrder.block_unit || workOrder.blockUnit;
                        const unitDisplay = blockUnit ? 
                            `<span class="badge bg-secondary">${blockUnit.unit_name}</span>` : 
                            '<span class="text-muted">N/A</span>';
                        
                        // Contact display
                        let contactDisplay = '<span class="text-muted">N/A</span>';
                        if (workOrder.contact_name) {
                            contactDisplay = `<div>${workOrder.contact_name}</div>`;
                            if (workOrder.contact_email) {
                                contactDisplay += `<small class="text-muted">${workOrder.contact_email}</small>`;
                            }
                        }
                        
                        // Issued By display - handle both camelCase and snake_case
                        const issuedBy = workOrder.issued_by || workOrder.issuedBy;
                        const issuedByDisplay = issuedBy ? 
                            `<div>${issuedBy.name || 'N/A'}</div><small class="text-muted">${createdDate}</small>` : 
                            '<div>N/A</div>';
                        
                        // Issue text (limit to 50 chars) - get from blockIssue if available
                        const blockIssue = workOrder.block_issue || workOrder.blockIssue;
                        const issueTextRaw = (blockIssue && blockIssue.issue) ? blockIssue.issue : (workOrder.issue || null);
                        const issueText = issueTextRaw ? (issueTextRaw.length > 50 ? issueTextRaw.substring(0, 50) + '...' : issueTextRaw) : '<span class="text-muted">N/A</span>';
                        const issueTextForData = issueTextRaw ? (issueTextRaw.length > 50 ? issueTextRaw.substring(0, 50) : issueTextRaw) : 'N/A';
                        
                        // Get priority and status text
                        const priorityText = workOrder.priority_id == 1 ? 'Low' : (workOrder.priority_id == 2 ? 'Normal' : (workOrder.priority_id == 3 ? 'High' : (workOrder.priority_id == 4 ? 'Urgent' : (workOrder.priority_id == 5 ? 'Critical' : 'Unknown'))));
                        const statusText = workOrder.status == 1 ? 'Pending' : (workOrder.status == 2 ? 'In Progress' : (workOrder.status == 3 ? 'Completed' : (workOrder.status == 4 ? 'Cancelled' : 'On Hold')));
                        
                        // Ref No link
                        const refNoLink = `<a href="/block-work-orders/${workOrder.id}" class="text-decoration-none"><strong>#${workOrder.ref_no}</strong></a>`;
                        
                        workOrdersDT.row.add([
                            refNoLink,
                            unitDisplay,
                            issueText,
                            priorityBadge,
                            statusBadge,
                            contactDisplay,
                            deadlineDate,
                            issuedByDisplay,
                            `<div class="d-flex justify-content-center gap-1">
                                <a href="/block-work-orders/${workOrder.id}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="ph-eye"></i>
                                </a>
                                <a href="/block-work-orders/${workOrder.id}/edit" class="btn btn-sm btn-outline-warning" title="Edit Work Order">
                                    <i class="ph-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="event.preventDefault(); event.stopPropagation(); workOrderShowDeleteConfirmation(${workOrder.id}, {
                                            ref_no: '${workOrder.ref_no}',
                                            title: ${JSON.stringify(issueTextForData)},
                                            priority: ${JSON.stringify(priorityText)},
                                            status: ${JSON.stringify(statusText)},
                                            created_date: ${JSON.stringify(createdDate)}
                                        }); return false;"
                                        title="Delete Work Order">
                                    <i class="ph-trash"></i>
                                </button>
                            </div>`
                        ]);
                    });
                    
                    console.log('Drawing DataTable with', data.data.length, 'rows');
                    workOrdersDT.draw();
                    console.log('DataTable refresh completed');
                } else {
                    console.log('API returned success: false');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching work orders:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    };

    function viewWorkOrder(workOrderId) {
        // Navigate to work order show page
        window.location.href = `/block-work-orders/${workOrderId}`;
    }

    function populateEditModal(workOrder) {
        if (!workOrder) return;

        const form = document.getElementById('editWorkOrderForm');
        if (!form) {
            console.error('Edit work order form not found');
            return;
        }

        form.action = `/block-work-orders/${workOrder.id}`;
        
        // Set unit and issue
        setValue('edit_work_order_unit_id', workOrder.block_unit_id);
        setValue('edit_block_issue_id_select', workOrder.block_issue_id);
        
        // Update hidden fields for unit and building
        const blockUnitIdField = document.getElementById('edit_work_order_block_unit_id');
        const blockBuildingIdField = document.getElementById('edit_work_order_block_building_id');
        const unitSelect = document.getElementById('edit_work_order_unit_id');
        
        if (blockUnitIdField) {
            blockUnitIdField.value = workOrder.block_unit_id || '';
        }
        
        // Get building ID from the selected unit option
        if (unitSelect && workOrder.block_unit_id) {
            // Wait a moment for the select to update, then get the building ID
            setTimeout(() => {
                const selectedOption = unitSelect.options[unitSelect.selectedIndex];
                if (selectedOption && blockBuildingIdField) {
                    const buildingId = selectedOption.getAttribute('data-building-id') || '';
                    blockBuildingIdField.value = buildingId;
                }
            }, 50);
        }
        
        // Set priority and status
        setValue('edit_priority_id', workOrder.priority_id);
        setValue('edit_status', workOrder.status);
        
        // Set contact information
        setValue('edit_contact_name', workOrder.contact_name);
        setValue('edit_contact_mobile', workOrder.contact_mobile);
        setValue('edit_contact_email', workOrder.contact_email);
        
        // Handle datetime fields
        if (workOrder.preferred_start_date_time) {
            setValue('edit_preferred_start_date_time', formatDateTimeForInput(workOrder.preferred_start_date_time));
        }
        if (workOrder.preferred_end_date_time) {
            setValue('edit_preferred_end_date_time', formatDateTimeForInput(workOrder.preferred_end_date_time));
        }
        if (workOrder.deadline_date) {
            setValue('edit_deadline_date', formatDateForInput(workOrder.deadline_date));
        }
        
        // Set comments
        setValue('edit_comment', workOrder.comment);

        // Show modal (like inspection modal)
        const modalElement = document.getElementById('editWorkOrderModal');
        if (!modalElement) {
            console.error('Edit work order modal not found');
            return;
        }
        
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
    
    // Expose populateEditModal to global scope
    window.populateEditModal = populateEditModal;

    function setValue(id, value) {
        const el = document.getElementById(id);
        if (el) el.value = value || '';
    }

    function formatDateTimeForInput(dateTimeString) {
        if (!dateTimeString) return '';
        const date = new Date(dateTimeString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    function formatDateForInput(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function setCurrentDateTime() {
        const issuedDateTime = document.getElementById('issued_date_time');
        if (issuedDateTime) {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            issuedDateTime.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        }
    }

    function setLoading(button, state, text = 'Processing…') {
        if (!button) return;
        if (state) {
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = `<i class="ph-spinner-gap me-1 ph-spin"></i>${text}`;
            button.disabled = true;
        } else {
            button.innerHTML = button.dataset.originalText || button.innerHTML;
            button.disabled = false;
        }
    }

    function showToast(type, message) {
        const containerId = 'workOrderToastContainer';
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.style.position = 'fixed';
            container.style.top = '20px';
            container.style.right = '20px';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        container.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Populate work order form when issue is selected
    document.addEventListener('DOMContentLoaded', function() {
        const issueSelect = document.getElementById('block_issue_id_select');
        
        if (issueSelect) {
            issueSelect.addEventListener('change', function() {
                const issueId = this.value;
                
                if (!issueId) {
                    return;
                }
                
                // Fetch issue details and populate form
                fetch(`/api/block-issues/${issueId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        populateWorkOrderFormFromIssue(data.data);
                    } else {
                        showToast('danger', 'Failed to load issue details. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching issue details:', error);
                    showToast('danger', 'Error loading issue details. Please try again.');
                });
            });
        }
    });
    
    function populateWorkOrderFormFromIssue(issue) {
        // Set hidden fields
        const blockIdField = document.getElementById('work_order_block_id');
        const blockIssueIdField = document.getElementById('work_order_block_issue_id');
        const blockUnitIdField = document.getElementById('work_order_block_unit_id');
        const blockBuildingIdField = document.getElementById('work_order_block_building_id');
        
        if (blockIdField) {
            blockIdField.value = issue.block_id || '';
        }
        if (blockIssueIdField) {
            blockIssueIdField.value = issue.id || '';
        }
        // block_unit_id is already set when unit is selected, but update if different
        if (blockUnitIdField && issue.block_unit_id) {
            blockUnitIdField.value = issue.block_unit_id;
        }
        if (blockBuildingIdField) {
            blockBuildingIdField.value = issue.block_building_id || '';
        }
        
        // Populate priority (use issue priority if available)
        const priorityField = document.getElementById('work_order_priority_id');
        if (priorityField && issue.priority_id) {
            priorityField.value = issue.priority_id;
        }
        
        // Issue description field removed - using single comments field instead
        
        // Populate contact information
        const contactNameField = document.getElementById('work_order_contact_name');
        if (contactNameField && issue.contact_name) {
            contactNameField.value = issue.contact_name;
        }
        
        const contactMobileField = document.getElementById('work_order_contact_mobile');
        if (contactMobileField && issue.contact_mobile) {
            contactMobileField.value = issue.contact_mobile;
        }
        
        const contactEmailField = document.getElementById('work_order_contact_email');
        if (contactEmailField && issue.contact_email) {
            contactEmailField.value = issue.contact_email;
        }
        
        // Populate preferred start/end dates
        const preferredStartField = document.getElementById('work_order_preferred_start');
        if (preferredStartField && issue.preferred_start_date_time) {
            const startDate = new Date(issue.preferred_start_date_time);
            preferredStartField.value = formatDateTimeForInput(issue.preferred_start_date_time);
        }
        
        const preferredEndField = document.getElementById('work_order_preferred_end');
        if (preferredEndField && issue.preferred_end_date_time) {
            preferredEndField.value = formatDateTimeForInput(issue.preferred_end_date_time);
        }
        
        // Populate deadline date (default to 7 days from now if not set)
        const deadlineField = document.getElementById('work_order_deadline');
        if (deadlineField) {
            if (issue.deadline_date) {
                deadlineField.value = formatDateForInput(issue.deadline_date);
            } else {
                // Default to 7 days from now
                const defaultDeadline = new Date();
                defaultDeadline.setDate(defaultDeadline.getDate() + 7);
                deadlineField.value = formatDateForInput(defaultDeadline.toISOString());
            }
        }
        
        // Note for access field removed - using single comments field instead
    }

    function deleteWorkOrder(workOrderId) {
        if (!workOrderId) return;

        const $btn = $('#confirmDeleteWorkOrderBtn');
        const original = $btn.html();
        $btn.html('<i class="ph-spinner-gap me-1 ph-spin"></i>Deleting…').prop('disabled', true);

        fetch(`/block-work-orders/${workOrderId}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) {
                    // Hide modal first (matches inspection pattern)
                    bootstrap.Modal.getInstance(document.getElementById('deleteWorkOrderModal')).hide();
                    // Refresh table (matches inspection pattern exactly)
                    refreshTable();
                } else {
                    // Only show error toast for failures
                    if (typeof showToast === 'function') {
                        showToast('danger', data.message || 'Failed to delete work order.');
                    }
                }
            })
            .catch(error => {
                console.error('Work order delete error', error);
                // Only show error toast if needed
                if (typeof showToast === 'function') {
                    showToast('danger', 'Error deleting work order.');
                }
            })
            .finally(() => {
                $btn.html(original).prop('disabled', false);
            });
    }

    // Full implementation - override the inline version (matches inspection pattern)
    window.workOrderShowDeleteConfirmation = function(workOrderId, details) {
        console.log('workOrderShowDeleteConfirmation (IIFE) called', workOrderId, details);
        
        const container = document.getElementById('deleteWorkOrderDetails');
        if (!container) {
            console.error('deleteWorkOrderDetails container not found');
            return;
        }
        
        container.innerHTML = `
            <div class="row">
                <div class="col-5">Reference:</div>
                <div class="col-7"><strong>#${details.ref_no || 'N/A'}</strong></div>
            </div>
            <div class="row">
                <div class="col-5">Title:</div>
                <div class="col-7">${details.title || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-5">Priority:</div>
                <div class="col-7">${details.priority || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-5">Status:</div>
                <div class="col-7">${details.status || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-5">Created Date:</div>
                <div class="col-7">${details.created_date || 'N/A'}</div>
            </div>`;

        const confirmBtn = document.getElementById('confirmDeleteWorkOrderBtn');
        if (!confirmBtn) {
            console.error('confirmDeleteWorkOrderBtn not found');
            return;
        }
        
        // Remove existing handlers and add new one (matches inspection pattern)
        $('#confirmDeleteWorkOrderBtn').off('click').on('click', () => deleteWorkOrder(workOrderId));
        
        const modalElement = document.getElementById('deleteWorkOrderModal');
        if (!modalElement) {
            console.error('deleteWorkOrderModal not found');
            return;
        }
        
        // Show modal using Bootstrap 5 (matches inspection pattern)
        try {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Delete modal shown');
        } catch (error) {
            console.error('Error showing modal:', error);
        }
    };

    // Expose functions globally - editWorkOrder is already exposed above
    // deleteWorkOrder and workOrderShowDeleteConfirmation are already defined in inline script
    // but we override them here to ensure they have access to refreshTable and showToast
    // IMPORTANT: This must override the inline version to ensure refreshTable is available
    window.deleteWorkOrder = deleteWorkOrder;
    // Override the placeholder functions with the real implementations
    window.refreshTable = refreshTable;
    window.refreshWorkOrdersTable = refreshWorkOrdersTable;
    
    // Log to confirm functions are exposed
    console.log('Work orders IIFE functions exposed:', {
        deleteWorkOrder: typeof window.deleteWorkOrder,
        refreshTable: typeof window.refreshTable,
        refreshWorkOrdersTable: typeof window.refreshWorkOrdersTable,
        hasRefreshTable: window.deleteWorkOrder.toString().includes('refreshTable')
    });
    
    // Verify the deleteWorkOrder function has access to refreshTable
    if (!window.deleteWorkOrder.toString().includes('refreshTable')) {
        console.error('WARNING: deleteWorkOrder does not have access to refreshTable!');
    }
    window.workOrderAttachEditHandlers = attachEditHandlers;
    window.viewWorkOrder = viewWorkOrder;

})();
</script>
