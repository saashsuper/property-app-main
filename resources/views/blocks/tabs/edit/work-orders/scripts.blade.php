<script>
(function() {
    let workOrdersDT = null;

    // Expose refresh functions immediately so they're available for inline scripts
    // These will be properly defined later, but we create placeholders now
    window.refreshTable = window.refreshTable || function() {
        setTimeout(() => {
            if (typeof window.refreshTable === 'function' && window.refreshTable.toString().includes('workOrdersDT')) {
                window.refreshTable();
            } else if (typeof window.refreshWorkOrdersTable === 'function') {
                window.refreshWorkOrdersTable();
            }
        }, 100);
    };
    
    window.refreshWorkOrdersTable = window.refreshWorkOrdersTable || function() {
    };
    
    // Fetch work order function (similar to fetchInspection in inspection modal)
    function fetchWorkOrder(workOrderId) {
        
        if (!workOrderId) {
            return;
        }

        
        fetch(`/block-work-orders/${workOrderId}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
            .then(resp => {
                if (!resp.ok) {
                    throw new Error(`HTTP error! status: ${resp.status}`);
                }
                return resp.json();
            })
            .then(data => {
                if (!data.success) {
                    showToast('danger', data.message || 'Failed to load work order details.');
                    return;
                }
                if (!data.data) {
                    showToast('danger', 'No work order data received.');
                    return;
                }
                populateEditModal(data.data);
            })
            .catch(error => {
                showToast('danger', 'Error fetching work order details: ' + error.message);
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
        
        try {
        initializeDataTable();
        bindFormHandlers();
        bindModalEvents();
            
            // Verify modal exists
            const workOrderModal = document.getElementById('workOrderModal');
            if (workOrderModal) {
            } else {
            }
            
            const deleteModal = document.getElementById('deleteWorkOrderModal');
            if (deleteModal) {
            } else {
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
        const workOrderForm = document.getElementById('workOrderForm');

        if (workOrderForm) {
            workOrderForm.addEventListener('submit', handleWorkOrderSubmit);
        } else {
        }
        
        window.workOrderAttachEditHandlers = attachEditHandlers;
        attachEditHandlers();
    }

    // Handle Work Order Type toggle
    function setupWorkOrderTypeToggle() {
        let workOrderTypeSelect = document.getElementById('work_order_type');
        const contractCompanyContainer = document.getElementById('contractCompanyFieldContainer');
        const propertyManagerContainer = document.getElementById('propertyManagerFieldContainer');
        const contractCompanyField = document.getElementById('work_order_contract_company_id');
        const propertyManagerField = document.getElementById('work_order_property_manager_id');
        
        if (!workOrderTypeSelect) {
            return;
        }
        
        // Remove any existing event listeners by cloning the element
        const newSelect = workOrderTypeSelect.cloneNode(true);
        workOrderTypeSelect.parentNode.replaceChild(newSelect, workOrderTypeSelect);
        workOrderTypeSelect = document.getElementById('work_order_type');
        
        function toggleFields() {
            const selectedType = workOrderTypeSelect.value;
            
            if (selectedType === 'inhouse') {
                // Show Property Manager dropdown, hide Contract Company dropdown
                if (propertyManagerContainer) {
                    propertyManagerContainer.removeAttribute('style');
                    propertyManagerContainer.style.setProperty('display', 'block', 'important');
                    propertyManagerContainer.classList.remove('d-none');
                } else {
                }
                if (contractCompanyContainer) {
                    contractCompanyContainer.style.setProperty('display', 'none', 'important');
                    contractCompanyContainer.classList.add('d-none');
                } else {
                }
                
                // Enable Property Manager field (make it required) and disable Contract Company field
                if (propertyManagerField) {
                    propertyManagerField.required = true;
                    propertyManagerField.disabled = false;
                    propertyManagerField.setAttribute('required', 'required');
                    propertyManagerField.removeAttribute('disabled');
                }
                if (contractCompanyField) {
                    contractCompanyField.required = false;
                    contractCompanyField.disabled = true;
                    contractCompanyField.value = '';
                    contractCompanyField.removeAttribute('required');
                }
            } else {
                // Show Contract Company dropdown, hide Property Manager dropdown
                if (contractCompanyContainer) {
                    contractCompanyContainer.removeAttribute('style');
                    contractCompanyContainer.style.setProperty('display', 'block', 'important');
                    contractCompanyContainer.classList.remove('d-none');
                } else {
                }
                if (propertyManagerContainer) {
                    propertyManagerContainer.style.setProperty('display', 'none', 'important');
                    propertyManagerContainer.classList.add('d-none');
                } else {
                }
                
                // Enable Contract Company field (make it required) and disable Property Manager field
                if (contractCompanyField) {
                    contractCompanyField.required = true;
                    contractCompanyField.disabled = false;
                    contractCompanyField.setAttribute('required', 'required');
                    contractCompanyField.removeAttribute('disabled');
                }
                if (propertyManagerField) {
                    propertyManagerField.required = false;
                    propertyManagerField.disabled = true;
                    propertyManagerField.value = '';
                    propertyManagerField.removeAttribute('required');
                }
            }
        }
        
        // Set initial state based on current selection
        toggleFields();
        
        // Listen for changes
        workOrderTypeSelect.addEventListener('change', function() {
            toggleFields();
        });
    }

    function bindModalEvents() {
        const workOrderModal = document.getElementById('workOrderModal');

        if (workOrderModal) {
            
            // Track if modal is in edit mode
            let isEditMode = false;
            
            workOrderModal.addEventListener('show.bs.modal', () => {
                const msg = document.getElementById('workOrderMessage');
                if (msg) {
                    msg.innerHTML = '';
                    msg.classList.add('d-none');
                }
                
                // Check if we're in edit mode by checking if form action contains an ID
                const form = document.getElementById('workOrderForm');
                isEditMode = form && form.action.includes('/block-work-orders/') && form.action.match(/\d+$/);
                
                if (!isEditMode) {
                    // Reset to create mode
                    const workOrderType = document.getElementById('work_order_type');
                    if (workOrderType) {
                        workOrderType.value = 'outsource';
                    }
                    
                    // Setup work order type toggle - this will set initial state
                    setupWorkOrderTypeToggle();
                    
                    // Reset form
                if (form) {
                        form.action = '{{ route("block-work-orders.store") }}';
                        const methodInput = document.getElementById('work_order_method');
                        if (methodInput) {
                            methodInput.value = 'POST';
                        }
                        
                        // Update modal title and submit button
                        const modalLabel = document.getElementById('workOrderModalLabel');
                        const submitBtn = document.getElementById('workOrderSubmitBtn');
                        if (modalLabel) {
                            modalLabel.textContent = 'Create Work Order';
                        }
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="ph-check me-1"></i> Create Work Order';
                        }
                    }
                    
                    // Reset unit and issue selects
                    const unitSelect = document.getElementById('work_order_unit_id');
                    const issueSelect = document.getElementById('block_issue_id_select');
                    
                    if (unitSelect) {
                        unitSelect.value = '';
                    }
                    if (issueSelect) {
                        issueSelect.innerHTML = '<option value="">Select a unit first to see issues</option>';
                        issueSelect.disabled = true;
                    }
                } else {
                    // Edit mode - setup toggle in case it's needed
                    setupWorkOrderTypeToggle();
                }
            });

            workOrderModal.addEventListener('hidden.bs.modal', () => {
                const form = document.getElementById('workOrderForm');
                if (form) {
                    form.reset();
                    form.classList.remove('was-validated');
                    
                    // Reset form action to store route
                    if (window.routes && window.routes.workOrders && window.routes.workOrders.store) {
                        form.action = window.routes.workOrders.store;
                    } else {
                        form.action = '/block-work-orders';
                    }
                    
                    const methodInput = document.getElementById('work_order_method');
                    if (methodInput) {
                        methodInput.value = 'POST';
                    }
                }
                const msg = document.getElementById('workOrderMessage');
                if (msg) {
                    msg.innerHTML = '';
                    msg.classList.add('d-none');
                }
                
                // Reset modal title and submit button
                const modalLabel = document.getElementById('workOrderModalLabel');
                const submitBtn = document.getElementById('workOrderSubmitBtn');
                if (modalLabel) {
                    modalLabel.textContent = 'Create Work Order';
                }
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="ph-check me-1"></i> Create Work Order';
                }
                
                isEditMode = false;
            });
            
            // Handle unit selection change
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
                    
                    // Load issues for the selected unit
                    if (unitId) {
                        loadIssuesForUnit(unitId);
                    } else {
                        const issueSelect = document.getElementById('block_issue_id_select');
                        const blockIssueIdHidden = document.getElementById('work_order_block_issue_id');
                        if (issueSelect) {
                            issueSelect.innerHTML = '<option value="">Select a unit first to see issues</option>';
                            issueSelect.disabled = true;
                        }
                        if (blockIssueIdHidden) {
                            blockIssueIdHidden.value = '';
                        }
                    }
                });
            }
            
            // Handle issue selection change - update hidden field
            const issueSelect = document.getElementById('block_issue_id_select');
            if (issueSelect) {
                issueSelect.addEventListener('change', function() {
                    const issueId = this.value;
                    const blockIssueIdHidden = document.getElementById('work_order_block_issue_id');
                    if (blockIssueIdHidden) {
                        blockIssueIdHidden.value = issueId || '';
                    }
                });
            }
        }
    }
    
    function loadIssuesForUnit(unitId, callback) {
        const issueSelect = document.getElementById('block_issue_id_select');
        if (!issueSelect) {
            if (callback && typeof callback === 'function') {
                callback();
            }
            return;
        }
        
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
            
            // Execute callback if provided
            if (callback && typeof callback === 'function') {
                callback();
            }
        })
        .catch(error => {
            issueSelect.innerHTML = '<option value="">Error loading issues</option>';
            issueSelect.disabled = true;
            
            if (callback && typeof callback === 'function') {
                callback();
            }
        });
    }

    function attachEditHandlers() {
        // Use event delegation for edit buttons (like inspection modal)
        const editButtons = document.querySelectorAll('.edit-work-order');
        
        editButtons.forEach(button => {
            if (!button.dataset.bound) {
                button.dataset.bound = 'true';
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const workOrderId = this.getAttribute('data-work-order-id');
                    
                    if (workOrderId) {
                        fetchWorkOrder(workOrderId);
                } else {
                        showToast('danger', 'Work order ID not found.');
                }
                });
            }
        });
    }


    function handleWorkOrderSubmit(evt) {
        evt.preventDefault();
        evt.stopPropagation();
        const form = evt.currentTarget;
        
        if (!form.checkValidity()) {
            evt.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        // Determine if this is create or edit mode
        const methodInput = document.getElementById('work_order_method');
        const isEditMode = methodInput && methodInput.value === 'PUT';
        const method = isEditMode ? 'PUT' : 'POST';
        const action = isEditMode ? form.action : form.action;
        

        // Get required fields directly from DOM BEFORE building payload
        const blockIssueIdHidden = document.getElementById('work_order_block_issue_id');
        const prioritySelect = document.getElementById('work_order_priority_id');
        
:', blockIssueIdHidden?.value);
:', prioritySelect?.value);
        
        if (!blockIssueIdHidden?.value) {
        }
        if (!prioritySelect?.value) {
        }
        
        const payload = buildPayload(form);
        
        // Log the payload for debugging
        
        // FORCE include required fields if missing (safety net)
        if (!payload.block_issue_id) {
            if (blockIssueIdHidden?.value) {
                payload.block_issue_id = blockIssueIdHidden.value;
            } else {
            }
        }
        if (!payload.priority_id) {
            if (prioritySelect?.value) {
                payload.priority_id = prioritySelect.value;
            } else {
            }
        }
        
        // Final validation before submission
        if (!payload.block_issue_id || !payload.priority_id) {
            showToast('danger', 'Required fields are missing. Please refresh and try again.');
            setLoading(submitBtn, false);
            return false;
        }
        
        
        const submitBtn = form.querySelector('button[type="submit"]');
        setLoading(submitBtn, true, isEditMode ? 'Updating...' : 'Creating...');

        submitForm(action, method, payload, {
            onSuccess: msg => {
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
                const msgContainer = document.getElementById('workOrderMessage');
                if (msgContainer) {
                    msgContainer.innerHTML = '';
                    msgContainer.classList.add('d-none');
                }
                // Refresh table and stay on same tab (matches inspection pattern)
                setTimeout(() => {
                refreshTable();
                }, 300);
                // No success message - just silently refresh
            },
            onError: msg => {
                const container = document.getElementById('workOrderMessage');
                if (container) {
                    container.innerHTML = `<div class="alert alert-danger mb-0">${msg}</div>`;
                    container.classList.remove('d-none');
                } else {
                    showToast('danger', msg);
                }
            },
            onComplete: () => setLoading(submitBtn, false)
        });
        
        return false; // Prevent any default form submission
    }

    function buildPayload(form) {
        // Start with empty object - don't rely on FormData which may miss disabled/empty fields
        const data = {};
        
        // REQUIRED: Get block_issue_id - ALWAYS get directly from DOM
        const blockIssueIdHidden = document.getElementById('work_order_block_issue_id');
        const blockIssueIdSelect = document.getElementById('block_issue_id_select');
        
        // Always try to get from hidden field first (more reliable)
        if (blockIssueIdHidden && blockIssueIdHidden.value) {
            data.block_issue_id = blockIssueIdHidden.value;
        } else if (blockIssueIdSelect && blockIssueIdSelect.value) {
            data.block_issue_id = blockIssueIdSelect.value;
            // Also update hidden field for consistency
            if (blockIssueIdHidden) {
                blockIssueIdHidden.value = blockIssueIdSelect.value;
            }
        }
        
        // REQUIRED: Get priority_id - ALWAYS get directly from DOM
        const prioritySelect = document.getElementById('work_order_priority_id');
        if (prioritySelect && prioritySelect.value) {
            data.priority_id = prioritySelect.value;
        }
        
        // Get block_id from window or hidden field
        const blockId = window.blockId || document.getElementById('work_order_block_id')?.value;
        if (blockId) {
            data.block_id = blockId;
        }
        
        // Get status
        const statusSelect = document.getElementById('work_order_status');
        if (statusSelect && statusSelect.value) {
            data.status = statusSelect.value;
        }
        
        // Get other fields from FormData for everything else
        const formData = Object.fromEntries(new FormData(form));
        
        // Merge formData into data, but don't overwrite our DOM values
        Object.keys(formData).forEach(key => {
            if (key !== 'block_issue_id' && key !== 'priority_id') {
                // Only add non-empty values (except for required fields we already set)
                if (formData[key] !== null && formData[key] !== undefined && formData[key] !== '') {
                    data[key] = formData[key];
                }
            }
        });
        
        // Remove ref_no since it's auto-generated at backend
            delete data.ref_no;
        
        // Validate required fields before submission and log for debugging
        if (!data.block_issue_id) {
        } else {
        }
        if (!data.priority_id) {
        } else {
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
        
        // Handle work_order_type to determine contract_company_id vs property_manager_id
        const workOrderType = document.getElementById('work_order_type')?.value;
        if (workOrderType === 'inhouse') {
            // For inhouse, use property_manager_id
            const propertyManagerSelect = document.getElementById('work_order_property_manager_id');
            if (propertyManagerSelect && propertyManagerSelect.value) {
                data.property_manager_id = propertyManagerSelect.value;
            }
            delete data.contract_company_id;
        } else {
            // For outsource, use contract_company_id
            const contractCompanySelect = document.getElementById('work_order_contract_company_id');
            if (contractCompanySelect && contractCompanySelect.value) {
                data.contract_company_id = contractCompanySelect.value;
            }
            delete data.property_manager_id;
        }
        
        return data;
    }

    function submitForm(url, method, data, { onSuccess, onError, onComplete }) {
        const formData = new FormData();
        
        // Log what we're about to send
);
        
        // CRITICAL: Ensure required fields are ALWAYS in FormData, even if missing from data object
        const blockIssueIdHidden = document.getElementById('work_order_block_issue_id');
        const blockIssueIdSelect = document.getElementById('block_issue_id_select');
        const prioritySelect = document.getElementById('work_order_priority_id');
        
        // Get block_issue_id from DOM if not in data
        if (!data.block_issue_id) {
            if (blockIssueIdHidden && blockIssueIdHidden.value) {
                data.block_issue_id = blockIssueIdHidden.value;
            } else if (blockIssueIdSelect && blockIssueIdSelect.value) {
                data.block_issue_id = blockIssueIdSelect.value;
            }
        }
        
        // Get priority_id from DOM if not in data
        if (!data.priority_id) {
            if (prioritySelect && prioritySelect.value) {
                data.priority_id = prioritySelect.value;
            }
        }
        
        // Now build FormData
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
        
        // CRITICAL: Explicitly add required fields - read directly from DOM as final fallback
        let finalBlockIssueId = data.block_issue_id;
        let finalPriorityId = data.priority_id;
        
        // Final DOM read as absolute fallback
        if (!finalBlockIssueId) {
            const hiddenField = document.getElementById('work_order_block_issue_id');
            const selectField = document.getElementById('block_issue_id_select');
            if (hiddenField && hiddenField.value) {
                finalBlockIssueId = hiddenField.value;
            } else if (selectField && selectField.value) {
                finalBlockIssueId = selectField.value;
            }
        }
        
        if (!finalPriorityId) {
            const priorityField = document.getElementById('work_order_priority_id');
            if (priorityField && priorityField.value) {
                finalPriorityId = priorityField.value;
            }
        }
        
        // CRITICAL: Only set these fields if we have actual values
        if (finalBlockIssueId) {
            formData.set('block_issue_id', finalBlockIssueId);
        } else {
        }
        
        if (finalPriorityId) {
            formData.set('priority_id', finalPriorityId);
        } else {
        }
        
        // Final verification before sending
        const finalCheckBlockIssueId = formData.get('block_issue_id');
        const finalCheckPriorityId = formData.get('priority_id');
        
        if (!finalCheckBlockIssueId || !finalCheckPriorityId) {
            onError && onError('Required fields are missing. Please check the console for details.');
            onComplete && onComplete();
            return;
        }
        
        // Log FormData contents for debugging
        for (let pair of formData.entries()) {
        }
);
);

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
);
                
                // Check if response is JSON
                const contentType = resp.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return resp.json();
                } else if (resp.status >= 300 && resp.status < 400) {
                    // This is a redirect response - we should not follow it
                    throw new Error('Server attempted to redirect. This should not happen for AJAX requests.');
                } else {
                    // If not JSON, it might be HTML or something else
                    return resp.text().then(text => {
);
                        throw new Error('Server returned non-JSON response');
                    });
                }
            })
            .then(payload => {
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
                onError && onError(error.message || 'Unexpected error. Please try again.');
            })
            .finally(() => {
                onComplete && onComplete();
            });
    }

    function refreshTable() {
        if (workOrdersDT) {
            refreshWorkOrdersTable();
        } else {
            if ($.fn.DataTable.isDataTable('#workOrdersTable')) {
                workOrdersDT = $('#workOrdersTable').DataTable();
                refreshWorkOrdersTable();
            } else {
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
        
        // Try to get the DataTable if it's not available
        if (!workOrdersDT) {
            if ($.fn.DataTable.isDataTable('#workOrdersTable')) {
                workOrdersDT = $('#workOrdersTable').DataTable();
            } else {
            return;
            }
        }
        
        const blockId = window.blockId || $('input[name="block_id"]').val();
        if (!blockId) {
            return;
        }
        
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
                                <button type="button" class="btn btn-sm btn-outline-warning edit-work-order" data-work-order-id="${workOrder.id}" title="Edit Work Order">
                                    <i class="ph-pencil"></i>
                                </button>
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
                    
                    workOrdersDT.draw();
                } else {
                }
            },
            error: function(xhr, status, error) {
            }
        });
    };

    function viewWorkOrder(workOrderId) {
        // Navigate to work order show page
        window.location.href = `/block-work-orders/${workOrderId}`;
    }

    function populateEditModal(workOrder) {
        
        if (!workOrder) {
            return;
        }

        const form = document.getElementById('workOrderForm');
        if (!form) {
                    return;
                }

        
        // Set form to edit mode
        form.action = `/block-work-orders/${workOrder.id}`;
        const methodInput = document.getElementById('work_order_method');
        if (methodInput) {
            methodInput.value = 'PUT';
        }
        
        // Update modal title and submit button
        const modalLabel = document.getElementById('workOrderModalLabel');
        const submitBtn = document.getElementById('workOrderSubmitBtn');
        if (modalLabel) {
            modalLabel.textContent = 'Edit Work Order';
        }
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="ph-check me-1"></i> Update Work Order';
        }
        
        // Set block ID
        setValue('work_order_block_id', workOrder.block_id);
        
        // Set priority and status FIRST - these are required fields
        if (workOrder.priority_id) {
            setValue('work_order_priority_id', workOrder.priority_id);
            
            // Verify it was set
            const priorityCheck = document.getElementById('work_order_priority_id');
        } else {
        }
        if (workOrder.status) {
            setValue('work_order_status', workOrder.status);
        }
        
        // Set block_issue_id in hidden field IMMEDIATELY (required field)
        const blockIssueIdField = document.getElementById('work_order_block_issue_id');
        if (!blockIssueIdField) {
        } else if (!workOrder.block_issue_id) {
        } else {
            blockIssueIdField.value = workOrder.block_issue_id;
to:', workOrder.block_issue_id);
);
            
            // Double-check it's set
            setTimeout(() => {
                const verifyValue = document.getElementById('work_order_block_issue_id')?.value;
            }, 100);
        }
        
        // Set work order type and contract company/property manager
        if (workOrder.is_property_manager === true || workOrder.is_property_manager === 1) {
            setValue('work_order_type', 'inhouse');
            setTimeout(() => {
                setupWorkOrderTypeToggle();
                setValue('work_order_property_manager_id', workOrder.property_manager_id || workOrder.contractor_id || '');
            }, 100);
        } else {
            setValue('work_order_type', 'outsource');
            setTimeout(() => {
                setupWorkOrderTypeToggle();
                setValue('work_order_contract_company_id', workOrder.contract_company_id || '');
            }, 100);
        }
        
        // Set unit - this will trigger the change event which loads issues
        const unitSelect = document.getElementById('work_order_unit_id');
        if (unitSelect && workOrder.block_unit_id) {
            unitSelect.value = workOrder.block_unit_id;
            
            // Update hidden fields for unit and building
            const blockUnitIdField = document.getElementById('work_order_block_unit_id');
            const blockBuildingIdField = document.getElementById('work_order_block_building_id');
            
            if (blockUnitIdField) {
                blockUnitIdField.value = workOrder.block_unit_id;
            }
            
            // Get building ID from the selected unit option
            const selectedOption = unitSelect.options[unitSelect.selectedIndex];
            if (selectedOption && blockBuildingIdField) {
                const buildingId = selectedOption.getAttribute('data-building-id') || workOrder.block_building_id || '';
                blockBuildingIdField.value = buildingId;
            }
            
            // Load issues for the selected unit and set the issue after loading
            loadIssuesForUnit(workOrder.block_unit_id, () => {
                // After issues are loaded, set the selected issue
                setTimeout(() => {
                    const issueSelect = document.getElementById('block_issue_id_select');
                    const blockIssueIdHidden = document.getElementById('work_order_block_issue_id');
                    
                    if (issueSelect && workOrder.block_issue_id) {
                        issueSelect.value = workOrder.block_issue_id;
                        
                        // Ensure the select is enabled
                        issueSelect.disabled = false;
                        
                        // Ensure hidden field is still set (refresh it)
                        if (blockIssueIdHidden) {
                            blockIssueIdHidden.value = workOrder.block_issue_id;
is set to:', workOrder.block_issue_id);
                        }
                    }
                }, 200); // Increased timeout to ensure issues are fully loaded
            });
        } else {
            // If no unit, the hidden field is already set above
        }
        
        // Handle datetime fields
        if (workOrder.preferred_start_date_time) {
            setValue('work_order_preferred_start', formatDateTimeForInput(workOrder.preferred_start_date_time));
        }
        if (workOrder.preferred_end_date_time) {
            setValue('work_order_preferred_end', formatDateTimeForInput(workOrder.preferred_end_date_time));
        }
        if (workOrder.deadline_date) {
            setValue('work_order_deadline', formatDateForInput(workOrder.deadline_date));
        }

        // Set comments
        setValue('work_order_comment', workOrder.comment || '');

        // Clear any error messages
        const messageContainer = document.getElementById('workOrderMessage');
        if (messageContainer) {
            messageContainer.innerHTML = '';
            messageContainer.classList.add('d-none');
        }

        // Show modal
        const modalElement = document.getElementById('workOrderModal');
        if (!modalElement) {
            showToast('danger', 'Work order modal not found. Please refresh the page.');
            return;
        }
        
        try {
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            
            // Verify values are set before showing modal
            const blockIssueCheck = document.getElementById('work_order_block_issue_id')?.value;
            const priorityCheck = document.getElementById('work_order_priority_id')?.value;
            
            modal.show();
            
            // Verify values are still set after modal is shown (in case event handlers cleared them)
            setTimeout(() => {
                const blockIssueAfter = document.getElementById('work_order_block_issue_id')?.value;
                const priorityAfter = document.getElementById('work_order_priority_id')?.value;
- block_issue_id:', blockIssueAfter, 'priority_id:', priorityAfter);
                
                // If values were cleared, restore them
                if (!blockIssueAfter && workOrder.block_issue_id) {
                    const blockIssueField = document.getElementById('work_order_block_issue_id');
                    if (blockIssueField) {
                        blockIssueField.value = workOrder.block_issue_id;
                    }
                }
                if (!priorityAfter && workOrder.priority_id) {
                    setValue('work_order_priority_id', workOrder.priority_id);
                }
            }, 500);
            
        } catch (error) {
            showToast('danger', 'Error opening modal. Please refresh the page.');
        }
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
called', workOrderId, details);
        
        const container = document.getElementById('deleteWorkOrderDetails');
        if (!container) {
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
            return;
        }
        
        // Remove existing handlers and add new one (matches inspection pattern)
        $('#confirmDeleteWorkOrderBtn').off('click').on('click', () => deleteWorkOrder(workOrderId));
        
        const modalElement = document.getElementById('deleteWorkOrderModal');
        if (!modalElement) {
            return;
        }
        
        // Show modal using Bootstrap 5 (matches inspection pattern)
        try {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } catch (error) {
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
    
    
    // Verify the deleteWorkOrder function has access to refreshTable
    if (!window.deleteWorkOrder.toString().includes('refreshTable')) {
    }
    window.workOrderAttachEditHandlers = attachEditHandlers;
    window.viewWorkOrder = viewWorkOrder;

})();
</script>
