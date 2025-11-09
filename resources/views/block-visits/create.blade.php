@extends('layouts.master')

@section('title') Create Site Visit @endsection

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <style>
        .card-header-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            color: #5469d4;
            border-radius: 12px;
            font-size: 1.25rem;
        }
        .form-section-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .form-section-title .section-pill {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.15);
        }
        .info-summary-card {
            background: linear-gradient(145deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.06));
            border: none;
        }
        .info-summary-card .icon-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(102, 126, 234, 0.18);
            color: #5469d4;
            font-size: 1.1rem;
        }
        #siteVisitDropzone.dropzone {
            min-height: 96px !important;
            border: 2px dashed rgba(102, 126, 234, 0.45) !important;
            border-radius: 12px !important;
            background: #f8f9ff !important;
            transition: all 0.25s ease-in-out;
        }
        #siteVisitDropzone.dropzone:hover,
        #siteVisitDropzone.dropzone.dz-drag-hover {
            border-color: #667eea !important;
            background: #eef1ff !important;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15) !important;
        }
        #siteVisitDropzone .dz-message {
            padding: 15px 8px !important;
            margin: 0 !important;
            text-align: center !important;
            color: #4b5563 !important;
        }
        #siteVisitDropzone .dz-message h5 {
            margin: 6px 0 3px 0 !important;
            font-size: 0.9rem !important;
            color: #1f2937 !important;
        }
        #siteVisitDropzone .dz-message p {
            margin: 0 !important;
            font-size: 0.75rem !important;
            line-height: 1.3 !important;
        }
        .summary-prompt {
            background: #ffffff;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
        }
        .summary-prompt h6 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .summary-prompt p {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 0.75rem;
        }
        .summary-prompt .badge {
            font-size: 0.7rem;
            background: rgba(102, 126, 234, 0.15);
            color: #5469d4;
            border-radius: 999px;
        }
    </style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        @component('components.breadcrumb')
            @slot('li_1') Dashboard @endslot
            @slot('title') Create Site Visit @endslot
        @endcomponent

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <div class="d-flex align-items-center gap-3">
                            <span class="card-header-icon">
                                <i class="ph-map-pin-line"></i>
                            </span>
                            <div>
                                <h4 class="mb-1">Schedule New Site Visit</h4>
                                <p class="text-muted mb-0 small">Plan inspections, assign your field team, and keep track of every visit in PROMAN.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-3">
                        <div id="formAlert" class="alert d-none" role="alert"></div>

                        <form id="siteVisitCreateForm" action="{{ route('block-visits.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                            @csrf

                            <div class="form-section mb-4">
                                <div class="form-section-title">
                                    <span class="section-pill">1</span>
                                    Visit Context
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="block_id" class="form-label">Block <span class="text-danger">*</span></label>
                                        <select class="form-select" id="block_id" name="block_id" required data-selected="{{ old('block_id') }}">
                                            <option value="">Select a block</option>
                                            @foreach ($blocks as $block)
                                                <option 
                                                    value="{{ $block->id }}"
                                                    data-management="{{ $block->management_company }}"
                                                    data-type="{{ $block->blockType->name ?? 'N/A' }}"
                                                    data-total-units="{{ $block->units->count() }}"
                                                    data-address="{{ $block->block_address ?? '' }}"
                                                    {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                                    {{ $block->name }} @if($block->blockType) ({{ $block->blockType->name }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select the block for this visit.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="block_issue_id" class="form-label">Related Issue</label>
                                        <select class="form-select" id="block_issue_id" name="block_issue_id" data-selected="{{ old('block_issue_id') }}">
                                            <option value="">No linked issue</option>
                                        </select>
                                        <div class="form-text">Only open issues for the selected block are shown.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="block_unit_id" class="form-label">Unit (Optional)</label>
                                        <select class="form-select" id="block_unit_id" name="block_unit_id" data-selected="{{ old('block_unit_id') }}">
                                            <option value="">Select unit (optional)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="user_id" class="form-label">Assign to <span class="text-danger">*</span></label>
                                        <select class="form-select" id="user_id" name="user_id" required data-selected="{{ old('user_id') }}">
                                            <option value="">Select a team member</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Assign the visit to someone on your team.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mb-4">
                                <div class="form-section-title">
                                    <span class="section-pill">2</span>
                                    Scheduling & Tracking
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="scheduled_date_time" class="form-label">Scheduled Date &amp; Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="scheduled_date_time" name="scheduled_date_time" value="{{ old('scheduled_date_time') }}" required>
                                        <div class="invalid-feedback">Please choose when the visit should occur.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="job_reason_id" class="form-label">Job Reason</label>
                                        <select class="form-select" id="job_reason_id" name="job_reason_id" data-selected="{{ old('job_reason_id') }}">
                                            <option value="">Select a reason</option>
                                            @foreach ($jobReasons as $reason)
                                                <option value="{{ $reason->id }}" {{ old('job_reason_id') == $reason->id ? 'selected' : '' }}>
                                                    {{ $reason->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="job_status_id" class="form-label">Job Status</label>
                                        <select class="form-select" id="job_status_id" name="job_status_id" data-selected="{{ old('job_status_id') }}">
                                            <option value="">Scheduled</option>
                                            @foreach ($jobStatuses as $status)
                                                <option value="{{ $status->id }}" {{ old('job_status_id') == $status->id ? 'selected' : '' }}>
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mb-4">
                                <div class="form-section-title">
                                    <span class="section-pill">3</span>
                                    Notes & Attachments
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Visit Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter visit objectives, access instructions, or any important details...">{{ old('notes') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Attachments</label>
                                    <small class="text-muted d-block mb-2">Optional – drag and drop images or documents that will be useful for your field team.</small>
                                    <div id="siteVisitDropzone" class="dropzone">
                                        <div class="dz-message text-center">
                                            <div class="mb-2 text-primary">
                                                <i class="ph-cloud-arrow-up fs-1"></i>
                                            </div>
                                            <h5 class="fw-semibold mb-1">Drop files here or click to upload</h5>
                                            <p class="text-muted mb-0 small">Supports images, PDF, Word documents • up to 5MB each (max 10 files)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('block-visits.index') }}" class="btn btn-outline-secondary">
                                    <i class="ph-arrow-left me-1"></i> Back to Site Visits
                                </a>
                                <button type="submit" class="btn btn-primary" id="siteVisitSubmitBtn">
                                    <i class="ph-map-pin me-1"></i> Create Site Visit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card info-summary-card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="icon-circle">
                                <i class="ph-compass"></i>
                            </span>
                            <div>
                                <h6 class="mb-1 text-dark">Site Visit Checklist</h6>
                                <p class="text-muted mb-0 small">Assign a lead, confirm reasons, and add context so every visit is successful.</p>
                            </div>
                        </div>
                        <div class="summary-prompt mb-3" id="selectedBlockSummary">
                            <h6>Select a block</h6>
                            <p>Pick a block to view management details, unit counts, and helpful context for your field team.</p>
                            <span class="badge">Tip</span>
                        </div>
                        <div>
                            <h6 class="text-uppercase text-muted small mb-3">Quick Counts</h6>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Total Blocks</span>
                                    <span class="fw-semibold">{{ $blocks->count() }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Job Reasons</span>
                                    <span class="fw-semibold">{{ $jobReasons->count() }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Job Statuses</span>
                                    <span class="fw-semibold">{{ $jobStatuses->count() }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Assignable Users</span>
                                    <span class="fw-semibold">{{ $users->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const blockSelect = document.getElementById('block_id');
            const unitSelect = document.getElementById('block_unit_id');
            const issueSelect = document.getElementById('block_issue_id');
            const form = document.getElementById('siteVisitCreateForm');
            const alertBox = document.getElementById('formAlert');
            const submitBtn = document.getElementById('siteVisitSubmitBtn');
            const summaryCard = document.getElementById('selectedBlockSummary');
            const showVisitBaseUrl = @json(url('block-visits'));
            let cachedIssues = [];

            Dropzone.autoDiscover = false;
            const siteVisitDropzone = new Dropzone('#siteVisitDropzone', {
                url: form.getAttribute('action'),
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 10,
                addRemoveLinks: true,
                maxFiles: 10,
                maxFilesize: 5,
                acceptedFiles: '.jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx',
            });

            const setAlert = (type = 'success', message = '') => {
                alertBox.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning', 'alert-info');
                alertBox.classList.add(`alert-${type}`);
                alertBox.innerHTML = message;
                alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            };

            const clearAlert = () => {
                alertBox.classList.add('d-none');
                alertBox.innerHTML = '';
            };

            const resetValidation = () => {
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                form.querySelectorAll('.invalid-feedback.dynamic').forEach(el => el.remove());
            };

            const addValidationErrors = (errors = {}) => {
                Object.entries(errors).forEach(([field, messages]) => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        let feedback = input.nextElementSibling;
                        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                            feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback dynamic';
                            input.parentNode.appendChild(feedback);
                        }
                        feedback.textContent = Array.isArray(messages) ? messages[0] : messages;
                    }
                });
            };

            const renderBlockSummary = (option) => {
                if (!option || !option.value) {
                    summaryCard.innerHTML = `
                        <h6>Select a block</h6>
                        <p>Pick a block to view management details, unit counts, and helpful context for your field team.</p>
                        <span class="badge">Tip</span>
                    `;
                    return;
                }

                const management = option.dataset.management || 'Not specified';
                const type = option.dataset.type || 'Not specified';
                const totalUnits = option.dataset.totalUnits || '0';
                const address = option.dataset.address || 'No address available';

                summaryCard.innerHTML = `
                    <h6 class="mb-2">${option.textContent.trim()}</h6>
                    <p class="mb-2"><strong>Management:</strong> ${management}</p>
                    <p class="mb-2"><strong>Block Type:</strong> ${type}</p>
                    <p class="mb-2"><strong>Total Units:</strong> ${totalUnits}</p>
                    <p class="mb-0 text-muted small"><i class="ph-map-pin me-1"></i>${address}</p>
                `;
            };

            const setSelectState = (selectEl, { disabled = false, placeholder = null } = {}) => {
                if (!selectEl) return;
                selectEl.disabled = disabled;
                if (placeholder !== null) {
                    selectEl.innerHTML = `<option value="">${placeholder}</option>`;
                }
            };

            const fetchJson = async (url) => {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                if (!response.ok) {
                    throw new Error(`Request failed with status ${response.status}`);
                }
                return await response.json();
            };

            const populateUnits = async (blockId) => {
                setSelectState(unitSelect, { disabled: true, placeholder: 'Loading units...' });
                if (!blockId) {
                    setSelectState(unitSelect, { disabled: true, placeholder: 'Select unit (optional)' });
                    return;
                }

                try {
                    const data = await fetchJson(`/block-units/block/${blockId}`);
                    setSelectState(unitSelect, { disabled: false, placeholder: 'Select unit (optional)' });

                    if (data.success && Array.isArray(data.data)) {
                        const preselected = unitSelect.dataset.selected;
                        data.data.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id;
                            const parts = [];
                            if (unit.unit_code) parts.push(unit.unit_code);
                            if (unit.unit_name && unit.unit_name !== unit.unit_code) parts.push(unit.unit_name);
                            if (unit.unit_type?.name) parts.push(`(${unit.unit_type.name})`);
                            option.textContent = parts.join(' ');
                            option.dataset.code = unit.unit_code || '';
                            if (preselected && String(unit.id) === String(preselected)) {
                                option.selected = true;
                                unitSelect.dataset.selected = '';
                            }
                            unitSelect.appendChild(option);
                        });
                    } else {
                        setSelectState(unitSelect, { disabled: true, placeholder: 'No units found for this block' });
                    }
                } catch (error) {
                    console.error('Unable to load units', error);
                    setSelectState(unitSelect, { disabled: true, placeholder: 'Unable to load units' });
                    console.error(error);
                }
            };

            const populateIssues = async (blockId) => {
                setSelectState(issueSelect, { disabled: true, placeholder: 'Loading issues...' });
                cachedIssues = [];

                if (!blockId) {
                    setSelectState(issueSelect, { disabled: true, placeholder: 'No linked issue' });
                    return;
                }

                try {
                    const data = await fetchJson(`{{ route('api.block-issues') }}?block_id=${blockId}`);
                    setSelectState(issueSelect, { disabled: false, placeholder: 'No linked issue' });

                    if (data.success && Array.isArray(data.data)) {
                        cachedIssues = data.data;
                        const preselected = issueSelect.dataset.selected;
                        cachedIssues.forEach(issue => {
                            const option = document.createElement('option');
                            option.value = issue.id;
                            const statusBadge = issue.issue_status?.label || (issue.issue_status_id === 2 ? 'In Progress' : 'Open');
                            option.textContent = `${issue.ref_no || 'N/A'} — ${issue.issue || 'Issue'} (${statusBadge})`;
                            option.dataset.unitId = issue.block_unit_id || '';
                            option.dataset.priority = issue.priority?.label || '';
                            if (preselected && String(issue.id) === String(preselected)) {
                                option.selected = true;
                                issueSelect.dataset.selected = '';
                            }
                            issueSelect.appendChild(option);
                        });
                    } else {
                        setSelectState(issueSelect, { disabled: true, placeholder: 'No linked issue' });
                    }
                } catch (error) {
                    console.error('Unable to load issues', error);
                    setSelectState(issueSelect, { disabled: true, placeholder: 'Unable to load issues' });
                    console.error(error);
                }
            };

            const resetDependentSelects = () => {
                setSelectState(unitSelect, { disabled: true, placeholder: 'Select unit (optional)' });
                unitSelect.dataset.selected = '';
                setSelectState(issueSelect, { disabled: true, placeholder: 'No linked issue' });
                issueSelect.dataset.selected = '';
            };

            const handleBlockChange = () => {
                if (!blockSelect) {
                    resetDependentSelects();
                    return;
                }

                const blockId = blockSelect.value;
                const selectedOption = blockSelect.options[blockSelect.selectedIndex];

                renderBlockSummary(selectedOption);

                if (!blockId) {
                    resetDependentSelects();
                    return;
                }

                unitSelect.dataset.selected = '';
                issueSelect.dataset.selected = '';
                populateUnits(blockId);
                populateIssues(blockId);
            };

            if (blockSelect) {
                blockSelect.addEventListener('change', handleBlockChange);
            }

            issueSelect.addEventListener('change', () => {
                const selectedOption = issueSelect.options[issueSelect.selectedIndex];
                if (selectedOption && selectedOption.dataset.unitId) {
                    unitSelect.value = selectedOption.dataset.unitId;
                }
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                clearAlert();
                resetValidation();

                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }

                submitBtn.disabled = true;
                const originalBtnHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="ph-spinner-gap ph-spin me-1"></i> Saving...';

                try {
                    const formData = new FormData(form);

                    if (siteVisitDropzone && siteVisitDropzone.getAcceptedFiles().length > 0) {
                        siteVisitDropzone.getAcceptedFiles().forEach((file) => {
                            formData.append('files[]', file);
                        });
                    }

                    const response = await fetch(form.getAttribute('action'), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });

                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        if (data.errors) {
                            addValidationErrors(data.errors);
                            setAlert('danger', 'Please review the highlighted fields and try again.');
                        } else {
                            throw new Error(data.message || 'Unable to create the site visit at the moment.');
                        }
                        return;
                    }

                    setAlert('success', data.message || 'Site visit created successfully!');
                    form.reset();
                    if (siteVisitDropzone) {
                        siteVisitDropzone.removeAllFiles(true);
                    }
                    setTimeout(() => {
                        window.location.href = `${showVisitBaseUrl}/${data.data?.id || ''}`;
                    }, 900);
                } catch (error) {
                    console.error(error);
                    setAlert('danger', error.message || 'Something went wrong while creating the site visit.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            });

            renderBlockSummary(blockSelect && blockSelect.value ? blockSelect.options[blockSelect.selectedIndex] : null);
            if (blockSelect && blockSelect.value) {
                Promise.allSettled([
                    populateUnits(blockSelect.value),
                    populateIssues(blockSelect.value)
                ]).then(() => {
                    const issueOption = issueSelect.options[issueSelect.selectedIndex];
                    if (issueOption && issueOption.dataset.unitId) {
                        unitSelect.value = issueOption.dataset.unitId;
                    }
                });
            } else {
                resetDependentSelects();
            }
        });
    </script>
@endpush
