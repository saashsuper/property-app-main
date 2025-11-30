<script>
(function() {
    let workOrdersDT = null;

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
        initializeDataTable();
        bindFormHandlers();
        bindModalEvents();
        
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
            window.workOrderAttachEditHandlers();
        });
    }

    function bindFormHandlers() {
        const createForm = document.getElementById('createWorkOrderForm');
        const editForm = document.getElementById('editWorkOrderForm');

        if (createForm) {
            createForm.addEventListener('submit', handleCreateWorkOrderSubmit);
        }
        if (editForm) {
            editForm.addEventListener('submit', handleEditWorkOrderSubmit);
        }
        window.workOrderAttachEditHandlers = attachEditHandlers;
        attachEditHandlers();
    }

    function bindModalEvents() {
        const createModal = document.getElementById('createWorkOrderModal');
        const editModal = document.getElementById('editWorkOrderModal');

        if (createModal) {
            createModal.addEventListener('show.bs.modal', () => {
                const msg = document.getElementById('createWorkOrderMessage');
                if (msg) msg.innerHTML = '';
                setCurrentDateTime();
                // Set ref_no to show auto-generated placeholder
                const refNoField = document.getElementById('ref_no');
                if (refNoField) {
                    refNoField.value = 'Auto-generated';
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
        }

        if (editModal) {
            editModal.addEventListener('show.bs.modal', () => {
                const msg = document.getElementById('editWorkOrderMessage');
                if (msg) msg.innerHTML = '';
            });

            editModal.addEventListener('hide.bs.modal', (event) => {
                // Prevent backdrop from being removed if there are other modals
                const openModals = document.querySelectorAll('.modal.show');
                // Allow Bootstrap to handle backdrop normally
            });

            editModal.addEventListener('hidden.bs.modal', (event) => {
                const form = document.getElementById('editWorkOrderForm');
                if (form) {
                    form.reset();
                    form.classList.remove('was-validated');
                }
                const msg = document.getElementById('editWorkOrderMessage');
                if (msg) msg.innerHTML = '';
                
                // Ensure proper cleanup - check for other open modals
                setTimeout(() => {
                    const openModals = document.querySelectorAll('.modal.show');
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    
                    // If no modals are open, clean up any remaining backdrop
                    if (openModals.length === 0) {
                        backdrops.forEach(backdrop => backdrop.remove());
                        // Ensure body is properly restored
                        if (!document.querySelector('.modal.show')) {
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }
                    }
                }, 150);
            });
        }
    }

    function attachEditHandlers() {
        document.querySelectorAll('.btn-outline-warning').forEach(button => {
            if (button.title === 'Edit Work Order' && !button.dataset.bound) {
                button.dataset.bound = 'true';
                button.addEventListener('click', function() {
                    const workOrderId = this.getAttribute('onclick').match(/\d+/)[0];
                    editWorkOrder(workOrderId);
                });
            }
        });

        document.querySelectorAll('.btn-outline-danger').forEach(button => {
            if (button.title === 'Delete Work Order' && !button.dataset.bound) {
                button.dataset.bound = 'true';
                button.addEventListener('click', function() {
                    const onclickAttr = this.getAttribute('onclick');
                    const match = onclickAttr.match(/workOrderShowDeleteConfirmation\((\d+),/);
                    if (match) {
                        const workOrderId = match[1];
                        const row = this.closest('tr');
                        const refNo = row.querySelector('td:nth-child(1)').textContent.trim();
                        const issue = row.querySelector('td:nth-child(3)').textContent.trim();
                        const priority = row.querySelector('td:nth-child(4) .badge').textContent.trim();
                        const status = row.querySelector('td:nth-child(5) .badge').textContent.trim();
                        const issuedBy = row.querySelector('td:nth-child(8)').textContent.split('\n')[1]?.trim() || row.querySelector('td:nth-child(8)').textContent.trim();
                        
                        workOrderShowDeleteConfirmation(workOrderId, {
                            ref_no: refNo,
                            title: issue,
                            priority: priority,
                            status: status,
                            created_date: issuedBy
                        });
                    }
                });
            }
        });
    }

    function handleCreateWorkOrderSubmit(evt) {
        evt.preventDefault();
        const form = evt.currentTarget;

        const payload = buildPayload(form);

        setLoading(form.querySelector('button[type="submit"]'), true, 'Creating...');

        submitForm(form.action, 'POST', payload, {
            onSuccess: msg => {
                showToast('success', msg || 'Work order created successfully.');
                bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                refreshTable();
            },
            onError: msg => {
                const container = document.getElementById('createWorkOrderMessage');
                if (container) {
                    container.innerHTML = `<div class="alert alert-danger mb-0">${msg}</div>`;
                } else {
                    showToast('danger', msg);
                }
            },
            onComplete: () => setLoading(form.querySelector('button[type="submit"]'), false)
        });
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
                showToast('success', msg || 'Work order updated successfully.');
                bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                refreshTable();
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
        // Remove ref_no from create form since it's auto-generated
        if (form.id === 'createWorkOrderForm') {
            delete data.ref_no;
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
            .then(resp => resp.json())
            .then(payload => {
                if (payload.success) {
                    onSuccess && onSuccess(payload.message);
                } else {
                    onError && onError(payload.message || 'Request failed.');
                }
            })
            .catch(error => {
                console.error('Work order submit error', error);
                onError && onError('Unexpected error. Please try again.');
            })
            .finally(() => {
                onComplete && onComplete();
            });
    }

    function refreshTable() {
        if (workOrdersDT) {
            refreshWorkOrdersTable();
        } else {
            setTimeout(() => {
                location.reload();
            }, 1500);
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
        if (!workOrdersDT) {
            return;
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
                                <button class="btn btn-sm btn-outline-warning" onclick="editWorkOrder(${workOrder.id})" title="Edit Work Order">
                                    <i class="ph-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="workOrderShowDeleteConfirmation(${workOrder.id}, {
                                    ref_no: '#${workOrder.ref_no}',
                                    title: '${issueText.replace(/'/g, "\\'")}',
                                    priority: '${workOrder.priority_id == 1 ? 'Low' : (workOrder.priority_id == 2 ? 'Normal' : (workOrder.priority_id == 3 ? 'High' : (workOrder.priority_id == 4 ? 'Urgent' : (workOrder.priority_id == 5 ? 'Critical' : 'Unknown'))))}',
                                    status: '${workOrder.status == 1 ? 'Pending' : (workOrder.status == 2 ? 'In Progress' : (workOrder.status == 3 ? 'Completed' : (workOrder.status == 4 ? 'Cancelled' : 'On Hold')))}',
                                    created_date: '${createdDate}'
                                })" title="Delete Work Order">
                                    <i class="ph-trash"></i>
                                </button>
                            </div>`
                        ]);
                    });
                    
                    workOrdersDT.draw();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching work orders:', error);
            }
        });
    };

    function viewWorkOrder(workOrderId) {
        // Navigate to work order show page
        window.location.href = `/block-work-orders/${workOrderId}`;
    }

    function editWorkOrder(workOrderId) {
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

    function populateEditModal(workOrder) {
        if (!workOrder) return;

        document.getElementById('editWorkOrderForm').action = `/block-work-orders/${workOrder.id}`;
        setValue('edit_block_issue_id', workOrder.block_issue_id);
        setValue('edit_ref_no', workOrder.ref_no);
        setValue('edit_priority_id', workOrder.priority_id);
        setValue('edit_status', workOrder.status);
        setValue('edit_block_unit_id', workOrder.block_unit_id);
        setValue('edit_block_building_id', workOrder.block_building_id);
        setValue('edit_contractor_id', workOrder.contractor_id);
        setValue('edit_repair_category_id', workOrder.repair_category_id);
        setValue('edit_issue', workOrder.issue);
        setValue('edit_contact_name', workOrder.contact_name);
        setValue('edit_contact_mobile', workOrder.contact_mobile);
        setValue('edit_contact_email', workOrder.contact_email);
        setValue('edit_note_for_access', workOrder.note_for_access);
        setValue('edit_comment', workOrder.comment);

        // Handle datetime fields
        if (workOrder.issued_date_time) {
            setValue('edit_issued_date_time', formatDateTimeForInput(workOrder.issued_date_time));
        }
        if (workOrder.preferred_start_date_time) {
            setValue('edit_preferred_start_date_time', formatDateTimeForInput(workOrder.preferred_start_date_time));
        }
        if (workOrder.preferred_end_date_time) {
            setValue('edit_preferred_end_date_time', formatDateTimeForInput(workOrder.preferred_end_date_time));
        }
        if (workOrder.deadline_date) {
            setValue('edit_deadline_date', formatDateForInput(workOrder.deadline_date));
        }

        const modalElement = document.getElementById('editWorkOrderModal');
        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        modal.show();
    }

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

    // Auto-populate issue description when issue is selected
    const issueSelect = document.getElementById('block_issue_id');
    const issueDescription = document.getElementById('issue');

    if (issueSelect && issueDescription) {
        issueSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const optionText = selectedOption.text;
                const issueText = optionText.split(' - ')[1];
                if (issueText) {
                    issueDescription.value = issueText;
                }
            } else {
                issueDescription.value = '';
            }
        });
    }

    // Delete confirmation function
    function workOrderShowDeleteConfirmation(workOrderId, workOrderData) {
        const detailsList = document.getElementById('deleteWorkOrderDetails');
        if (detailsList) {
            detailsList.innerHTML = `
                <li><strong>Work Order #:</strong> ${workOrderData.ref_no}</li>
                <li><strong>Title:</strong> ${workOrderData.title}</li>
                <li><strong>Priority:</strong> ${workOrderData.priority}</li>
                <li><strong>Status:</strong> ${workOrderData.status}</li>
                <li><strong>Created Date:</strong> ${workOrderData.created_date}</li>
            `;
        }

        $('#confirmDeleteWorkOrderBtn').off('click').on('click', function() {
            deleteWorkOrder(workOrderId);
        });

        $('#deleteWorkOrderModal').modal('show');
    }

    function deleteWorkOrder(workOrderId) {
        const $confirmBtn = $('#confirmDeleteWorkOrderBtn');
        const originalText = $confirmBtn.html();
        $confirmBtn.html('<i class="ph-spinner-gap me-1 ph-spin"></i>Deleting...').prop('disabled', true);

        fetch(`/block-work-orders/${workOrderId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message || 'Work order deleted successfully.');
                    $('#deleteWorkOrderModal').modal('hide');
                    refreshTable();
                } else {
                    showToast('danger', data.message || 'Failed to delete work order.');
                }
            })
            .catch(error => {
                console.error('Delete work order error', error);
                showToast('danger', 'Error deleting work order.');
            })
            .finally(() => {
                $confirmBtn.html(originalText).prop('disabled', false);
            });
    }

    // Expose functions globally
    window.workOrderShowDeleteConfirmation = workOrderShowDeleteConfirmation;
    window.workOrderAttachEditHandlers = attachEditHandlers;
    window.deleteWorkOrder = deleteWorkOrder;
    window.viewWorkOrder = viewWorkOrder;
    window.editWorkOrder = editWorkOrder;

})();
</script>
