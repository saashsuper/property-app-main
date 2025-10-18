@extends('layouts.master')

@section('title') Edit Inspection - {{ $blockInspection->ref_no }} @endsection

@section('css')
    <link href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.blocks') @endslot
        @slot('li_2') @lang('translation.block-inspections') @endslot
        @slot('li_3') {{ $blockInspection->ref_no }} @endslot
        @slot('title') Edit Inspection @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Block Inspection</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ph-x-circle me-2"></i>
                            <strong>Validation Error!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ph-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('block-inspections.update', $blockInspection->id) }}" method="POST" enctype="multipart/form-data" id="updateInspectionForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Accordion-style Tabs -->
                        <div class="accordion" id="inspectionAccordion">
                            <!-- General Information Tab -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="general-info-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#general-info" aria-expanded="true" aria-controls="general-info">
                                        <i class="ph-buildings me-3 text-primary"></i>
                                        <span class="fw-bold">GENERAL INFORMATION</span>
                                        <i class="ph-caret-down ms-auto"></i>
                                    </button>
                                </h2>
                                <div id="general-info" class="accordion-collapse collapse show" aria-labelledby="general-info-header" data-bs-parent="#inspectionAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="block_id" class="form-label">Block <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('block_id') is-invalid @enderror" name="block_id" required>
                                                        <option value="">Select Block</option>
                                                        @foreach($blocks as $block)
                                                            <option value="{{ $block->id }}" {{ old('block_id', $blockInspection->block_id) == $block->id ? 'selected' : '' }}>
                                                                {{ $block->name }} - {{ $block->address1 }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('block_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="lead_inspector" class="form-label">Lead Inspector <span class="text-danger">*</span></label>
                                                    @php
                                                        // Find the current lead inspector
                                                        $currentLead = $blockInspection->inspectionTeams->where('is_lead', true)->first();
                                                        $currentLeadId = $currentLead ? $currentLead->user_id : null;
                                                    @endphp
                                                    <select class="form-select @error('lead_inspector') is-invalid @enderror" name="lead_inspector" required>
                                                        <option value="">Select Lead Inspector</option>
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}" {{ old('lead_inspector', $currentLeadId) == $user->id ? 'selected' : '' }}>
                                                                {{ $user->name }} ({{ $user->email }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('lead_inspector')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="job_status_id" class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('job_status_id') is-invalid @enderror" name="job_status_id" required>
                                                        <option value="1" {{ old('job_status_id', $blockInspection->job_status_id) == 1 ? 'selected' : '' }}>Scheduled</option>
                                                        <option value="2" {{ old('job_status_id', $blockInspection->job_status_id) == 2 ? 'selected' : '' }}>In Progress</option>
                                                        <option value="3" {{ old('job_status_id', $blockInspection->job_status_id) == 3 ? 'selected' : '' }}>Completed</option>
                                                        <option value="4" {{ old('job_status_id', $blockInspection->job_status_id) == 4 ? 'selected' : '' }}>Cancelled</option>
                                                        <option value="5" {{ old('job_status_id', $blockInspection->job_status_id) == 5 ? 'selected' : '' }}>On Hold</option>
                                                    </select>
                                                    @error('job_status_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Scheduled Date & Time -->
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="scheduled_date" class="form-label">Scheduled Date <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control @error('scheduled_date') is-invalid @enderror" 
                                                               name="scheduled_date" id="scheduled_date" 
                                                               value="{{ old('scheduled_date', $blockInspection->scheduled_date_time ? $blockInspection->scheduled_date_time->format('Y-m-d') : '') }}" 
                                                               placeholder="dd/mm/yyyy" required>
                                                        <span class="input-group-text">
                                                            <i class="ph-calendar"></i>
                                                        </span>
                                                    </div>
                                                    @error('scheduled_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="scheduled_time" class="form-label">Scheduled Time <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="time" class="form-control @error('scheduled_time') is-invalid @enderror" 
                                                               name="scheduled_time" id="scheduled_time" 
                                                               value="{{ old('scheduled_time', $blockInspection->scheduled_date_time ? $blockInspection->scheduled_date_time->format('H:i') : '') }}" 
                                                               required>
                                                        <span class="input-group-text">
                                                            <i class="ph-clock"></i>
                                                        </span>
                                                    </div>
                                                    @error('scheduled_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="start_date" class="form-label">Start Date</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control @error('start_date') is-invalid @enderror" 
                                                               name="start_date" id="start_date" 
                                                               value="{{ old('start_date', $blockInspection->start_date_time ? $blockInspection->start_date_time->format('Y-m-d') : '') }}" 
                                                               placeholder="dd/mm/yyyy">
                                                        <span class="input-group-text">
                                                            <i class="ph-calendar"></i>
                                                        </span>
                                                    </div>
                                                    @error('start_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="start_time" class="form-label">Start Time</label>
                                                    <div class="input-group">
                                                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                                               name="start_time" id="start_time" 
                                                               value="{{ old('start_time', $blockInspection->start_date_time ? $blockInspection->start_date_time->format('H:i') : '') }}" 
                                                               placeholder="--:--">
                                                        <span class="input-group-text">
                                                            <i class="ph-clock"></i>
                                                        </span>
                                                    </div>
                                                    @error('start_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- End Date & Time -->
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="end_date" class="form-label">End Date</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control @error('end_date') is-invalid @enderror" 
                                                               name="end_date" id="end_date" 
                                                               value="{{ old('end_date', $blockInspection->end_date_time ? $blockInspection->end_date_time->format('Y-m-d') : '') }}" 
                                                               placeholder="dd/mm/yyyy">
                                                        <span class="input-group-text">
                                                            <i class="ph-calendar"></i>
                                                        </span>
                                                    </div>
                                                    @error('end_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="end_time" class="form-label">End Time</label>
                                                    <div class="input-group">
                                                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                                               name="end_time" id="end_time" 
                                                               value="{{ old('end_time', $blockInspection->end_date_time ? $blockInspection->end_date_time->format('H:i') : '') }}" 
                                                               placeholder="--:--">
                                                        <span class="input-group-text">
                                                            <i class="ph-clock"></i>
                                                        </span>
                                                    </div>
                                                    @error('end_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="notes" class="form-label">Note</label>
                                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                              name="notes" rows="3" placeholder="Enter any additional notes...">{{ old('notes', $blockInspection->notes) }}</textarea>
                                                    @error('notes')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- General Assets Tab -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="general-assets-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#general-assets" aria-expanded="false" aria-controls="general-assets">
                                        <i class="ph-buildings me-3 text-primary"></i>
                                        <span class="fw-bold">GENERAL ASSETS</span>
                                        <i class="ph-caret-down ms-auto"></i>
                                    </button>
                                </h2>
                                <div id="general-assets" class="accordion-collapse collapse" aria-labelledby="general-assets-header" data-bs-parent="#inspectionAccordion">
                                    <div class="accordion-body">
                                        @foreach($generalAssets as $asset)
                                            @php
                                                // Get existing data for this asset (if any)
                                                $existingData = $existingInspectionAssets->get($asset->id);
                                                $selectedStatus = $existingData ? \App\Http\Controllers\BlockInspectionController::getValueToStatusMap($existingData->block_inspection_value_id) : null;
                                                $existingNotes = $existingData ? $existingData->comments : '';
                                            @endphp
                                            
                                            <!-- Asset Name Heading -->
                                            <div class="row mb-2">
                                                <div class="col-12">
                                                    <h6 class="fw-bold text-start mb-0">{{ $asset->name }}</h6>
                                                </div>
                                            </div>
                                            
                                            <!-- Asset Details Row -->
                                            <div class="row mb-4 align-items-center">
                                                <!-- Status/Condition Options -->
                                                <div class="col-md-4">
                                                    <div class="btn-group w-100" role="group" aria-label="Status options for {{ $asset->name }}">
                                                        <input type="radio" class="btn-check" name="asset_status_{{ $asset->id }}" id="working_{{ $asset->id }}" value="working" autocomplete="off" {{ $selectedStatus === 'working' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-secondary btn-sm rounded-start" for="working_{{ $asset->id }}" style="border-radius: 0.375rem 0 0 0.375rem !important; color: black; border: 1px solid #dee2e6;">
                                                            @if($asset->name == 'Gates' || $asset->name == 'Street Lights')
                                                                Working
                                                            @elseif($asset->name == 'Landscape')
                                                                Clean
                                                            @elseif($asset->name == 'Building Externals')
                                                                Good
                                                            @else
                                                                Working
                                                            @endif
                                                        </label>

                                                        <input type="radio" class="btn-check" name="asset_status_{{ $asset->id }}" id="not_working_{{ $asset->id }}" value="not_working" autocomplete="off" {{ $selectedStatus === 'not_working' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-secondary btn-sm {{ $asset->name == 'Landscape' || $asset->name == 'Building Externals' ? 'average-option' : 'not-working-option' }}" for="not_working_{{ $asset->id }}" style="border-radius: 0 !important; border-left: 0 !important; border-right: 0 !important; color: black; border: 1px solid #dee2e6;">
                                                            @if($asset->name == 'Gates' || $asset->name == 'Street Lights')
                                                                Not Working
                                                            @elseif($asset->name == 'Landscape')
                                                                Average
                                                            @elseif($asset->name == 'Building Externals')
                                                                Average
                                                            @else
                                                                Not Working
                                                            @endif
                                                        </label>

                                                        <input type="radio" class="btn-check" name="asset_status_{{ $asset->id }}" id="na_{{ $asset->id }}" value="na" autocomplete="off" {{ $selectedStatus === 'na' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-secondary btn-sm rounded-end {{ strtolower(str_replace(' ', '_', $asset->name)) }}-na" for="na_{{ $asset->id }}" style="border-radius: 0 0.375rem 0.375rem 0 !important; border-left: 0 !important; color: black; border: 1px solid #dee2e6;">
                                                            @if($asset->name == 'Street Lights')
                                                                Not checked
                                                            @elseif($asset->name == 'Landscape')
                                                                Poor
                                                            @elseif($asset->name == 'Building Externals')
                                                                Poor
                                                            @else
                                                                N/A
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <!-- Photo Upload Area -->
                                                <div class="col-md-4">
                                                    <label class="form-label small mb-2 fw-bold text-black">Photos:</label>
                                                    <div class="upload-area border border-dashed rounded p-3 text-center" style="min-height: 100px; background-color: white;">
                                                        <i class="ph-cloud-arrow-up text-primary mb-2" style="font-size: 1.5rem;"></i>
                                                        <div class="text-muted small">DRAG & DROP HERE OR CLICK</div>
                                                        <input type="file" class="d-none" id="photos_{{ $asset->id }}" name="photos_{{ $asset->id }}[]" multiple accept="image/*">
                                                    </div>
                                                </div>
                                                
                                                <!-- Notes -->
                                                <div class="col-md-4">
                                                    <label for="notes_{{ $asset->id }}" class="form-label small fw-bold text-black">Note:</label>
                                                    <textarea class="form-control form-control-sm" 
                                                              id="notes_{{ $asset->id }}" 
                                                              name="notes_{{ $asset->id }}" 
                                                              rows="3" 
                                                              placeholder="Maximum allowable characters are 500." 
                                                              maxlength="500">{{ $existingNotes }}</textarea>
                                                    <div class="form-text small text-muted">Maximum allowable characters are 500.</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('block-inspections.show', $blockInspection->id) }}" class="btn btn-secondary">
                                        <i class="ph-arrow-left me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ph-check me-2"></i>Update Inspection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
/* Force override Bootstrap's button styles with maximum specificity */
.btn-group .btn-outline-secondary {
    background-color: white !important;
    color: black !important;
    border-color: #dee2e6 !important;
}

/* Override Bootstrap's default checked state with maximum specificity */
.btn-group .btn-check:checked + .btn-outline-secondary {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: white !important;
}

.btn-group .btn-check:checked + .btn-outline-secondary:hover {
    background-color: #157347 !important;
    border-color: #146c43 !important;
    color: white !important;
}

/* Specific overrides for different asset types and statuses with maximum specificity */

/* Gates - N/A should be green */
.btn-group .btn-check:checked + .gates-na {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: white !important;
}

.btn-group .btn-check:checked + .gates-na:hover {
    background-color: #157347 !important;
    border-color: #146c43 !important;
    color: white !important;
}

/* Landscape - Poor should be red */
.btn-group .btn-check:checked + .landscape-na {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

.btn-group .btn-check:checked + .landscape-na:hover {
    background-color: #c82333 !important;
    border-color: #bd2130 !important;
    color: white !important;
}

/* Street Lights - Not checked should be orange */
.btn-group .btn-check:checked + .street_lights-na {
    background-color: #e67e22 !important;
    border-color: #e67e22 !important;
    color: white !important;
}

.btn-group .btn-check:checked + .street_lights-na:hover {
    background-color: #d35400 !important;
    border-color: #d35400 !important;
    color: white !important;
}

/* Building Externals - Poor should be red */
.btn-group .btn-check:checked + .building_externals-na {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

.btn-group .btn-check:checked + .building_externals-na:hover {
    background-color: #c82333 !important;
    border-color: #bd2130 !important;
    color: white !important;
}

/* Additional override for any remaining Bootstrap styles */
.btn-group .btn-check:checked + .btn-outline-secondary.btn-sm {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: white !important;
}

.btn-group .btn-check:checked + .btn-outline-secondary.btn-sm:hover {
    background-color: #157347 !important;
    border-color: #146c43 !important;
    color: white !important;
}
</style>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/choices.js/choices.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date pickers for all date fields
            const dateFields = ['scheduled_date', 'start_date', 'end_date'];
            
            dateFields.forEach(function(fieldId) {
                const dateInput = document.getElementById(fieldId);
                if (dateInput && dateInput.value) {
                    console.log(`Initializing ${fieldId} with value: ${dateInput.value}`);
                    
                    flatpickr(dateInput, {
                        dateFormat: "Y-m-d",  // Internal format for Laravel compatibility
                        altInput: true,
                        altFormat: "d/m/Y",   // Display format for user (dd/mm/yyyy)
                        allowInput: true,
                        defaultDate: dateInput.value, // Set the default date from the input value
                        onReady: function(selectedDates, dateStr, instance) {
                            console.log(`${fieldId} ready with date: ${dateStr}`);
                        }
                    });
                } else if (dateInput) {
                    // Initialize empty date picker
                    flatpickr(dateInput, {
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d/m/Y",
                        allowInput: true
                    });
                }
            });

            // Handle form submission with loading state
            const form = document.getElementById('updateInspectionForm');
            const submitBtn = form ? form.querySelector('button[type="submit"]') : null;
            
            console.log('=== FORM DEBUG INFO ===');
            console.log('Form element:', form);
            console.log('Submit button:', submitBtn);
            
            if (form) {
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                console.log('Form enctype:', form.enctype);
                
                // Add click listener to submit button for debugging
                if (submitBtn) {
                    submitBtn.addEventListener('click', function(e) {
                        console.log('Submit button clicked!');
                        console.log('Button type:', this.type);
                        console.log('Form valid:', form.checkValidity());
                        
                        // Check for HTML5 validation errors
                        if (!form.checkValidity()) {
                            console.error('Form validation failed!');
                            const invalidFields = form.querySelectorAll(':invalid');
                            console.log('Invalid fields:', invalidFields);
                            invalidFields.forEach(field => {
                                console.log(`- ${field.name}: ${field.validationMessage}`);
                            });
                        }
                    });
                }
                
                form.addEventListener('submit', function(e) {
                    console.log('=== FORM SUBMIT EVENT ===');
                    
                    // Check form validity
                    if (!form.checkValidity()) {
                        console.error('Form validation failed!');
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Find invalid fields and expand their accordions
                        const invalidFields = form.querySelectorAll(':invalid');
                        console.log('Invalid fields found:', invalidFields.length);
                        
                        invalidFields.forEach(field => {
                            console.log(`Invalid: ${field.name} - ${field.validationMessage}`);
                            
                            // Find the accordion item containing this field
                            const accordionItem = field.closest('.accordion-collapse');
                            if (accordionItem) {
                                console.log('Expanding accordion for:', field.name);
                                // Show the accordion
                                const bsCollapse = new bootstrap.Collapse(accordionItem, {
                                    show: true
                                });
                                
                                // Also expand the accordion button
                                const accordionButton = accordionItem.previousElementSibling?.querySelector('.accordion-button');
                                if (accordionButton && accordionButton.classList.contains('collapsed')) {
                                    accordionButton.classList.remove('collapsed');
                                    accordionButton.setAttribute('aria-expanded', 'true');
                                }
                            }
                        });
                        
                        // Add Bootstrap validation classes
                        form.classList.add('was-validated');
                        
                        // Focus on the first invalid field after a short delay
                        setTimeout(function() {
                            const firstInvalid = form.querySelector(':invalid');
                            if (firstInvalid) {
                                firstInvalid.focus();
                                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }, 300);
                        
                        return false;
                    }
                    
                    console.log('Form is valid, proceeding with submission');
                    
                    // Log all form fields
                    const formData = new FormData(form);
                    console.log('Form data entries:');
                    for (let [key, value] of formData.entries()) {
                        console.log(`  ${key}: ${value}`);
                    }
                    
                    // Show loading state
                    if (submitBtn) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="ph-spinner-gap me-2 ph-spin"></i>Updating...';
                        submitBtn.disabled = true;
                        
                        // Re-enable button after 10 seconds as fallback
                        setTimeout(function() {
                            console.log('Re-enabling button after timeout');
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }, 10000);
                    }
                    
                    // Let the form submit normally
                    console.log('Allowing form to submit normally');
                });
                
                console.log('Form submit listener attached successfully');
            } else {
                console.error('ERROR: Form not found! Check if ID is correct.');
            }


            // Handle file upload areas
            document.querySelectorAll('.upload-area').forEach(function(uploadArea) {
                uploadArea.addEventListener('click', function() {
                    const fileInput = this.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.click();
                    }
                });

                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.style.backgroundColor = '#e9ecef';
                });

                uploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.style.backgroundColor = 'white';
                });

                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.style.backgroundColor = 'white';
                    const fileInput = this.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.files = e.dataTransfer.files;
                        // You can add file preview functionality here
                    }
                });
            });

            // Function to apply color to a radio button label based on its status
            function applyButtonColor(radio) {
                const label = radio.nextElementSibling;
                const assetName = label.className.match(/(gates|landscape|street_lights|building_externals)/);
                const buttonText = label.textContent.trim();
                
                if (radio.checked) {
                    // Apply specific color to selected button
                    if (assetName && assetName[1] === 'gates' && label.classList.contains('gates-na')) {
                        // Gates N/A - Green
                        label.style.backgroundColor = '#198754';
                        label.style.borderColor = '#198754';
                        label.style.color = 'white';
                    } else if (buttonText === 'Poor') {
                        // Poor - Red (for Landscape and Building Externals)
                        label.style.backgroundColor = '#dc3545';
                        label.style.borderColor = '#dc3545';
                        label.style.color = 'white';
                    } else if (buttonText === 'Not checked') {
                        // Not checked - Orange (for Street Lights)
                        label.style.backgroundColor = '#e67e22';
                        label.style.borderColor = '#e67e22';
                        label.style.color = 'white';
                    } else if (buttonText === 'Average') {
                        // Average - Orange (for Landscape and Building Externals)
                        label.style.backgroundColor = '#e67e22';
                        label.style.borderColor = '#e67e22';
                        label.style.color = 'white';
                    } else if (buttonText === 'Not Working') {
                        // Not Working - Red (for Gates and Street Lights)
                        label.style.backgroundColor = '#dc3545';
                        label.style.borderColor = '#dc3545';
                        label.style.color = 'white';
                    } else {
                        // Default - Green for all other selections (Working, Clean, Good, N/A for Gates)
                        label.style.backgroundColor = '#198754';
                        label.style.borderColor = '#198754';
                        label.style.color = 'white';
                    }
                } else {
                    // Reset to default white
                    label.style.backgroundColor = 'white';
                    label.style.color = 'black';
                    label.style.borderColor = '#dee2e6';
                }
            }

            // Apply colors to pre-selected buttons on page load
            document.querySelectorAll('.btn-check').forEach(function(radio) {
                if (radio.checked) {
                    applyButtonColor(radio);
                }
            });

            // Handle status button color changes on click
            document.querySelectorAll('.btn-check').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    // Reset all labels in the same group to white
                    const groupName = this.name;
                    document.querySelectorAll(`input[name="${groupName}"]`).forEach(function(input) {
                        applyButtonColor(input);
                    });
                });
            });
        });
    </script>
@endsection
