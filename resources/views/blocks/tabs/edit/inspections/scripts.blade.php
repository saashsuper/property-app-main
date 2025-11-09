<script>
(function() {
    let inspectionsDT = null;

    const STATUS_BADGES = {
        1: '<span class="badge bg-info-subtle text-info">Scheduled</span>',
        2: '<span class="badge bg-warning-subtle text-warning">In Progress</span>',
        3: '<span class="badge bg-success-subtle text-success">Completed</span>',
        4: '<span class="badge bg-danger-subtle text-danger">Cancelled</span>',
        5: '<span class="badge bg-secondary-subtle text-secondary">On Hold</span>',
        6: '<span class="badge bg-primary-subtle text-primary">Rescheduled</span>',
        default: '<span class="badge bg-secondary-subtle text-secondary">Unknown</span>'
    };

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

    document.addEventListener('DOMContentLoaded', () => {
        initializeDataTable();
        bindFormHandlers();
        bindModalEvents();
        
        // Debounced trigger to avoid duplicate refreshes
        let inspectionsRefreshTimer = null;
        function triggerInspectionsRefresh() {
            clearTimeout(inspectionsRefreshTimer);
            inspectionsRefreshTimer = setTimeout(function() {
                if (!$.fn.DataTable.isDataTable('#inspectionsTable')) {
                    initializeDataTable();
                }
                if (typeof window.refreshInspectionsTable === 'function') {
                    window.refreshInspectionsTable();
                }
            }, 50);
        }

        // Listen for Bootstrap tab shown event to refresh data when inspections tab becomes active
        $(document).on('shown.bs.tab', '#inspections-tab', function(e) {
            triggerInspectionsRefresh();
        });

        // If Inspections tab is already active on page load, refresh once to ensure data is loaded
        if ($('#inspections').hasClass('show') && $('#inspections').hasClass('active')) {
            triggerInspectionsRefresh();
        }
    });

    function initializeDataTable() {
        const $table = $('#inspectionsTable');
        if (!$table.length) {
            return;
        }

        if ($.fn.DataTable.isDataTable('#inspectionsTable')) {
            inspectionsDT = $table.DataTable();
            return;
        }

        inspectionsDT = $table.DataTable({
            responsive: true, 
            autoWidth: false,   
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
            order: [[0, 'desc']],       // Default sort by first column descending
            columnDefs: [
                { targets: 5, orderable: false, className: 'text-center text-nowrap' },
                { targets: [0, 1, 2, 3], className: 'text-nowrap' },
                { targets: 4, className: 'text-wrap' }
            ],
            pageLength: 10,            // Default page size
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Page size options
            language: {
                lengthMenu: "Show _MENU_ inspections per page",
                info: "Showing _START_ to _END_ of _TOTAL_ inspections",
                infoEmpty: "Showing 0 to 0 of 0 inspections",
                infoFiltered: "(filtered from _MAX_ total inspections)",
                search: "Search inspections:",
                searchPlaceholder: "Search by reference, inspector, notes...",
                paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
            },
            initComplete: function() {
                console.log('DataTable initialization completed');
                console.log('DataTable instance:', inspectionsDT);
                
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

        inspectionsDT.on('draw', function() {
            window.inspectionAttachEditHandlers();
        });
    }

    function bindFormHandlers() {
        const addForm = document.getElementById('addInspectionForm');
        const editForm = document.getElementById('editInspectionForm');

        if (addForm) {
            addForm.addEventListener('submit', handleAddInspectionSubmit);
        }
        if (editForm) {
            editForm.addEventListener('submit', handleEditInspectionSubmit);
        }
        window.inspectionAttachEditHandlers = attachEditHandlers;
        attachEditHandlers();
    }

    function bindModalEvents() {
        const editModal = document.getElementById('editInspectionModal');
        const addModal = document.getElementById('addInspectionModal');
        
        if (editModal) {
            editModal.addEventListener('show.bs.modal', () => {
                // Clear any previous messages
                clearMessage('editInspectionMessage');
            });

            editModal.addEventListener('hidden.bs.modal', () => {
                const form = document.getElementById('editInspectionForm');
                if (form) {
                    form.reset();
                    form.classList.remove('was-validated');
                }
                clearMessage('editInspectionMessage');
            });
        }
        
        if (addModal) {
            addModal.addEventListener('show.bs.modal', () => {
                // Clear any previous messages
                clearMessage('addInspectionMessage');
            });
        }
    }

    function attachEditHandlers() {
        document.querySelectorAll('.edit-inspection').forEach(button => {
            if (!button.dataset.bound) {
                button.dataset.bound = 'true';
                button.addEventListener('click', () => {
                    const id = button.getAttribute('data-inspection-id');
                    fetchInspection(id);
                });
            }
        });
    }

    function fetchInspection(inspectionId) {
        if (!inspectionId) {
            return;
        }

        fetch(`/block-inspections/${inspectionId}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(resp => resp.json())
            .then(data => {
                if (!data.success) {
                    showToast('danger', data.message || 'Failed to load inspection details.');
                    return;
                }
                populateEditModal(data.data);
            })
            .catch(error => {
                console.error('Inspection fetch error', error);
                showToast('danger', 'Error fetching inspection details.');
            });
    }

    function populateEditModal(inspection) {
        if (!inspection) return;

        document.getElementById('editInspectionForm').action = `/block-inspections/${inspection.id}`;
        setValue('edit_inspection_id', inspection.id);
        setValue('edit_ref_no', inspection.ref_no);
        setValue('edit_notes', inspection.notes);
        setValue('edit_job_status_id', inspection.job_status_id);

        const teams = inspection.inspection_teams || inspection.inspectionTeams || [];
        let lead = teams.find(t => t.is_lead) || teams[0];
        if (!lead && inspection.created_by) {
            lead = { user_id: inspection.created_by };
        }
        setValue('edit_user_id', lead ? lead.user_id : '');

        applyDateTime(inspection.scheduled_date_time, 'edit_scheduled_date', 'edit_scheduled_time');
        applyDateTime(inspection.start_date_time, 'edit_start_date', 'edit_start_time');
        applyDateTime(inspection.end_date_time, 'edit_end_date', 'edit_end_time');

        setText('edit_created_info', formatDateTime(inspection.created_at));
        setText('edit_updated_info', formatDateTime(inspection.updated_at));

        const modal = new bootstrap.Modal(document.getElementById('editInspectionModal'));
        modal.show();
    }

    function setValue(id, value) {
        const el = document.getElementById(id);
        if (el) el.value = value || '';
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value || '-';
    }

    function applyDateTime(value, dateId, timeId) {
        const dateEl = document.getElementById(dateId);
        const timeEl = document.getElementById(timeId);

        if (!value || !dateEl || !timeEl) {
            if (dateEl) dateEl.value = '';
            if (timeEl) timeEl.value = '';
            return;
        }

        const date = new Date(value);
        if (isNaN(date.getTime())) {
            return;
        }

        dateEl.value = date.toISOString().split('T')[0];
        timeEl.value = date.toTimeString().slice(0, 5);
    }

    function handleAddInspectionSubmit(evt) {
        evt.preventDefault();
        const form = evt.currentTarget;

        const payload = buildPayload(form, {
            scheduled_date_time: combineDateTime(form.scheduled_date.value, form.scheduled_time.value)
        });

        submitForm(form.action, 'POST', payload, {
            onSuccess: (message) => {
                showMessage('addInspectionMessage', 'success', message || 'Inspection scheduled successfully!');
                form.reset();
                
                setTimeout(function() {
                    bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                    // Add a small delay to ensure modal is fully closed
                    setTimeout(function() {
                        refreshInspectionsTable();
                    }, 100);
                }, 800);
            },
            onError: msg => showMessage('addInspectionMessage', 'danger', msg)
        });
    }

    function handleEditInspectionSubmit(evt) {
        evt.preventDefault();
        const form = evt.currentTarget;
        if (!form.checkValidity()) {
            evt.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        const id = form.inspection_id.value;
        const payload = buildPayload(form, {
            scheduled_date_time: combineDateTime(form.scheduled_date.value, form.scheduled_time.value),
            start_date_time: combineDateTime(form.start_date.value, form.start_time.value),
            end_date_time: combineDateTime(form.end_date.value, form.end_time.value)
        });

        setLoading(form.querySelector('button[type="submit"]'), true, 'Updating…');

        submitForm(`/block-inspections/${id}`, 'PUT', payload, {
            onSuccess: msg => {
                showMessage('editInspectionMessage', 'success', msg || 'Inspection updated successfully!');
                
                setTimeout(function() {
                    bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                    // Add a small delay to ensure modal is fully closed
                    setTimeout(function() {
                        refreshInspectionsTable();
                    }, 100);
                }, 800);
            },
            onError: msg => {
                showMessage('editInspectionMessage', 'danger', msg);
            },
            onComplete: () => setLoading(form.querySelector('button[type="submit"]'), false)
        });
    }

    function buildPayload(form, overrides = {}) {
        const data = Object.fromEntries(new FormData(form));
        return Object.assign({}, data, overrides);
    }

    function combineDateTime(date, time) {
        if (!date || !time) return null;
        return `${date} ${time}`;
    }

    function submitForm(url, method, data, { onSuccess, onError, onComplete }) {
        fetch(url, {
            method,
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
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
                console.error('Inspection submit error', error);
                onError && onError('Unexpected error. Please try again.');
            })
            .finally(() => {
                onComplete && onComplete();
            });
    }

    function refreshTable() {
        console.log('refreshTable called, inspectionsDT:', inspectionsDT);
        if (inspectionsDT) {
            console.log('Calling refreshInspectionsTable');
            refreshInspectionsTable();
        } else {
            console.log('DataTable not initialized, checking if table exists...');
            if ($.fn.DataTable.isDataTable('#inspectionsTable')) {
                inspectionsDT = $('#inspectionsTable').DataTable();
                console.log('DataTable found and assigned, calling refreshInspectionsTable');
                refreshInspectionsTable();
            } else {
                console.log('DataTable not found, reloading page in 1.5s');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        }
    }

    /**
     * Refreshes the DataTable with fresh data from the server
     * 
     * This function is exposed globally so it can be called from other parts of the application.
     * It fetches the latest inspection data for the current block and updates the DataTable.
     * 
     * @global
     */
    window.refreshInspectionsTable = function() {
        console.log('refreshInspectionsTable called, inspectionsDT:', inspectionsDT);
        
        // Try to get the DataTable if it's not available
        if (!inspectionsDT) {
            console.log('DataTable not available, trying to get it...');
            if ($.fn.DataTable.isDataTable('#inspectionsTable')) {
                inspectionsDT = $('#inspectionsTable').DataTable();
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
        
        console.log('Making AJAX request to:', `/api/blocks/${blockId}/inspections`);
        $.ajax({
            url: `/api/blocks/${blockId}/inspections`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('AJAX success, data:', data);
                if (data.success) {
                    console.log('Clearing DataTable and adding', data.data.length, 'rows');
                    // Clear and repopulate DataTable
                    inspectionsDT.clear();
                    
                    data.data.forEach(function(inspection) {
                        const teams = inspection.inspection_teams || [];
                        const leadMember = teams.find(t => t.is_lead) || teams[0];
                        const inspectorName = leadMember?.user?.name || inspection.creator?.name || 'N/A';

                        const scheduleDateAttr = inspection.scheduled_date_time ? new Date(inspection.scheduled_date_time).toISOString().split('T')[0] : '';
                        const scheduleTimeAttr = inspection.scheduled_date_time ? new Date(inspection.scheduled_date_time).toTimeString().slice(0, 5) : '';
                        const scheduleDisplay = inspection.scheduled_date_time ? new Date(inspection.scheduled_date_time).toLocaleString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        }) : 'N/A';

                        const notesDisplay = inspection.notes
                            ? (inspection.notes.length > 80 ? `${inspection.notes.slice(0, 77)}…` : inspection.notes)
                            : 'N/A';

                        inspectionsDT.row.add([
                            inspection.ref_no || 'N/A',
                            scheduleDisplay,
                            inspectorName,
                            inspection.status_text ? `<span class="badge bg-${inspection.status_color}-subtle text-${inspection.status_color}">${inspection.status_text}</span>` : (STATUS_BADGES[inspection.job_status_id] || STATUS_BADGES.default),
                            notesDisplay,
                            renderActions({
                                id: inspection.id,
                                ref_no: inspection.ref_no,
                                notes: inspection.notes,
                                job_status_id: inspection.job_status_id,
                                scheduled_date_attr: scheduleDateAttr,
                                scheduled_time_attr: scheduleTimeAttr,
                                scheduled_display: scheduleDisplay
                            }, leadMember ? leadMember.user_id : inspection.created_by, inspectorName)
                        ]);
                    });
                    
                    console.log('Drawing DataTable');
                    inspectionsDT.draw();
                    console.log('DataTable refresh completed');
                } else {
                    console.log('API returned success: false');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching inspections:', error);
                console.error('Response:', xhr.responseText);
                console.log('Falling back to page reload');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });
    };

    function renderActions(inspection, leadUserId, inspectorName) {
        const limitedNotes = (inspection.notes || '').replace(/"/g, '&quot;');
        const viewUrl = "{{ route('block-inspections.show', ':id') }}".replace(':id', inspection.id);
        const downloadUrl = "{{ route('block-inspections.download-pdf', ':id') }}".replace(':id', inspection.id);
        const editDetailsUrl = "{{ route('block-inspections.edit', ':id') }}".replace(':id', inspection.id);
        const canDownload = Number(inspection.job_status_id) === 3;

        return `
            <div class="d-flex justify-content-center gap-2">
                <a href="${viewUrl}"
                   class="btn btn-sm btn-outline-primary"
                   title="View Inspection">
                    <i class="ph-eye"></i>
                </a>
                ${canDownload ? `
                <a href="${downloadUrl}"
                   class="btn btn-sm btn-outline-danger"
                   title="Download PDF Report">
                    <i class="ph-file-pdf"></i>
                </a>` : ''}
                <button class="btn btn-sm btn-outline-primary edit-inspection"
                        data-inspection-id="${inspection.id}"
                        data-user-id="${leadUserId || ''}"
                        data-date="${inspection.scheduled_date_attr || ''}"
                        data-time="${inspection.scheduled_time_attr || ''}"
                        data-notes="${limitedNotes}"
                        title="Edit Inspection">
                    <i class="ph-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning"
                        onclick="window.location.href='${editDetailsUrl}'"
                        title="Update Inspection Details">
                    <i class="ph-note-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger"
                        onclick="inspectionShowDeleteConfirmation(${inspection.id}, {
                            ref_no: '${inspection.ref_no || 'N/A'}',
                            scheduled_date: '${inspection.scheduled_display || 'N/A'}',
                            inspector: '${inspectorName}'
                        })"
                        title="Delete Inspection">
                    <i class="ph-trash"></i>
                </button>
            </div>`;
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

    function formatDateTime(value) {
        if (!value) return '-';
        try {
            const date = new Date(value);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return '-';
        }
    }

    function showToast(type, message) {
        const containerId = 'inspectionToastContainer';
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.style.position = 'fixed';
            container.style.top = '20px';
            container.style.right = '20px';
            container.style.zIndex = '1080';
            container.style.display = 'flex';
            container.style.flexDirection = 'column';
            container.style.gap = '10px';
            document.body.appendChild(container);
        }

        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show shadow-sm`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        container.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    function inspectionDeleteInspection(inspectionId) {
        if (!inspectionId) return;

        const $btn = $('#confirmDeleteInspectionBtn');
        const original = $btn.html();
        $btn.html('<i class="ph-spinner-gap me-1 ph-spin"></i>Deleting…').prop('disabled', true);

        fetch(`/block-inspections/${inspectionId}`, {
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
                    showToast('success', data.message || 'Inspection deleted successfully.');
                    refreshTable();
                    bootstrap.Modal.getInstance(document.getElementById('deleteInspectionModal')).hide();
                } else {
                    showToast('danger', data.message || 'Failed to delete inspection.');
                }
            })
            .catch(error => {
                console.error('Inspection delete error', error);
                showToast('danger', 'Error deleting inspection.');
            })
            .finally(() => {
                $btn.html(original).prop('disabled', false);
            });
    }

    window.inspectionShowDeleteConfirmation = function(inspectionId, details) {
        const container = document.getElementById('deleteInspectionDetails');
        if (container) {
            container.innerHTML = `
                <div class="row">
                    <div class="col-5">Reference:</div>
                    <div class="col-7"><strong>${details.ref_no || 'N/A'}</strong></div>
                </div>
                <div class="row">
                    <div class="col-5">Scheduled:</div>
                    <div class="col-7">${details.scheduled_date || 'N/A'}</div>
                </div>
                <div class="row">
                    <div class="col-5">Inspector:</div>
                    <div class="col-7">${details.inspector || 'N/A'}</div>
                </div>`;
        }

        $('#confirmDeleteInspectionBtn').off('click').on('click', () => inspectionDeleteInspection(inspectionId));
        new bootstrap.Modal(document.getElementById('deleteInspectionModal')).show();
    };

    window.inspectionAttachEditHandlers = attachEditHandlers;
})();
</script>

