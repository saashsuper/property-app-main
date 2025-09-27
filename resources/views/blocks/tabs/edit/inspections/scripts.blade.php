<script>
(function() {
    let inspectionsDT = null;

    const STATUS_BADGES = {
        1: '<span class="badge badge-inspection-created">Created</span>',
        2: '<span class="badge badge-inspection-progress">In Progress</span>',
        3: '<span class="badge badge-inspection-workorder">Work Order</span>',
        4: '<span class="badge badge-inspection-completed">Completed</span>',
        5: '<span class="badge badge-inspection-invoiced">Invoiced</span>',
        default: '<span class="badge badge-inspection-default">Unknown</span>'
    };

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
        if (!editModal) {
            return;
        }

        editModal.addEventListener('show.bs.modal', () => {
            const msg = document.getElementById('editInspectionMessage');
            if (msg) msg.innerHTML = '';
        });

        editModal.addEventListener('hidden.bs.modal', () => {
            const form = document.getElementById('editInspectionForm');
            if (form) {
                form.reset();
                form.classList.remove('was-validated');
            }
            const msg = document.getElementById('editInspectionMessage');
            if (msg) msg.innerHTML = '';
            document.getElementById('edit_end_date_group').style.display = 'none';
            document.getElementById('edit_end_time_group').style.display = 'none';
        });
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
        applyDateTime(inspection.end_date_time, 'edit_end_date', 'edit_end_time', true);

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

    function applyDateTime(value, dateId, timeId, toggleVisibility = false) {
        const dateEl = document.getElementById(dateId);
        const timeEl = document.getElementById(timeId);
        const groupDate = document.getElementById(`${dateId}_group`);
        const groupTime = document.getElementById(`${timeId}_group`);

        if (!value || !dateEl || !timeEl) {
            if (toggleVisibility) {
                if (groupDate) groupDate.style.display = 'none';
                if (groupTime) groupTime.style.display = 'none';
            }
            return;
        }

        const date = dayjs(value);
        if (!date.isValid()) {
            return;
        }

        dateEl.value = date.format('YYYY-MM-DD');
        timeEl.value = date.format('HH:mm');

        if (toggleVisibility) {
            if (groupDate) groupDate.style.display = 'block';
            if (groupTime) groupTime.style.display = 'block';
        }
    }

    function handleAddInspectionSubmit(evt) {
        evt.preventDefault();
        const form = evt.currentTarget;

        const payload = buildPayload(form, {
            scheduled_date_time: combineDateTime(form.scheduled_date.value, form.scheduled_time.value)
        });

        submitForm(form.action, 'POST', payload, {
            onSuccess: () => {
                form.reset();
                bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                showToast('success', 'Inspection scheduled successfully.');
                refreshTable();
            },
            onError: msg => showToast('danger', msg)
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
            scheduled_date_time: combineDateTime(form.scheduled_date.value, form.scheduled_time.value)
        });

        setLoading(form.querySelector('button[type="submit"]'), true, 'Updating…');

        submitForm(`/block-inspections/${id}`, 'PUT', payload, {
            onSuccess: msg => {
                showToast('success', msg || 'Inspection updated successfully.');
                bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                refreshTable();
            },
            onError: msg => {
                const container = document.getElementById('editInspectionMessage');
                if (container) {
                    container.innerHTML = `<div class="alert alert-danger mb-0">${msg}</div>`;
                } else {
                    showToast('danger', msg);
                }
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
        if (inspectionsDT) {
            refreshInspectionsTable();
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
     * It fetches the latest inspection data for the current block and updates the DataTable.
     * 
     * @global
     */
    window.refreshInspectionsTable = function() {
        if (!inspectionsDT) {
            return;
        }
        
        const blockId = window.blockId || $('input[name="block_id"]').val();
        if (!blockId) {
            return;
        }
        
        $.ajax({
            url: `/api/blocks/${blockId}/inspections`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Clear and repopulate DataTable
                    inspectionsDT.clear();
                    
                    data.data.forEach(function(inspection) {
                        const teams = inspection.inspection_teams || [];
                        const leadMember = teams.find(t => t.is_lead) || teams[0];
                        const inspectorName = leadMember?.user?.name || inspection.creator?.name || 'N/A';

                        const scheduleDateAttr = inspection.scheduled_date_time ? dayjs(inspection.scheduled_date_time).format('YYYY-MM-DD') : '';
                        const scheduleTimeAttr = inspection.scheduled_date_time ? dayjs(inspection.scheduled_date_time).format('HH:mm') : '';
                        const scheduleDisplay = inspection.scheduled_date_time ? dayjs(inspection.scheduled_date_time).format('MMM DD, YYYY') : 'N/A';

                        const notesDisplay = inspection.notes
                            ? (inspection.notes.length > 80 ? `${inspection.notes.slice(0, 77)}…` : inspection.notes)
                            : 'N/A';

                        inspectionsDT.row.add([
                            inspection.ref_no || 'N/A',
                            scheduleDisplay,
                            inspectorName,
                            STATUS_BADGES[inspection.job_status_id] || STATUS_BADGES.default,
                            notesDisplay,
                            renderActions({
                                id: inspection.id,
                                ref_no: inspection.ref_no,
                                notes: inspection.notes,
                                scheduled_date_attr: scheduleDateAttr,
                                scheduled_time_attr: scheduleTimeAttr
                            }, leadMember ? leadMember.user_id : inspection.created_by, inspectorName)
                        ]);
                    });
                    
                    inspectionsDT.draw();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching inspections:', error);
            }
        });
    };

    function renderActions(inspection, leadUserId, inspectorName) {
        const limitedNotes = (inspection.notes || '').replace(/"/g, '&quot;');

        return `
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-outline-primary edit-inspection"
                        data-inspection-id="${inspection.id}"
                        data-user-id="${leadUserId || ''}"
                        data-date="${inspection.scheduled_date_attr || ''}"
                        data-time="${inspection.scheduled_time_attr || ''}"
                        data-notes="${limitedNotes}"
                        title="Edit Inspection">
                    <i class="ph-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger"
                        onclick="inspectionShowDeleteConfirmation(${inspection.id}, {
                            ref_no: '${inspection.ref_no || 'N/A'}',
                            scheduled_date: '${inspection.scheduled_date_attr ? dayjs(inspection.scheduled_date_attr).format('MMM DD, YYYY') : 'N/A'}',
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
        const date = dayjs(value);
        return date.isValid() ? date.format('MMM DD, YYYY HH:mm') : '-';
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

