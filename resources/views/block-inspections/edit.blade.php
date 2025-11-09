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
                                                    <div id="dropzone_{{ $asset->id }}" class="dropzone" style="min-height: 110px; border: 2px dashed #d1d5db; border-radius: 0.375rem; background-color: white;">
                                                        <div class="dz-message" data-dz-message>
                                                            <i class="ph-cloud-arrow-up text-primary mb-2" style="font-size: 1.5rem;"></i>
                                                            <div class="text-muted small">Drag & drop images or click to browse</div>
                                                            <div class="text-muted" style="font-size: 0.75rem;">Max 5MB per image</div>
                                                        </div>
                                                    </div>
                                                    <!-- Existing Images Preview -->
                                                    @if($existingData && $existingData->images->count() > 0)
                                                    <div class="existing-images-preview mt-2">
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($existingData->images as $image)
                                                            <div class="position-relative" style="width: 80px; height: 80px;">
                                                                <img src="{{ $image->image_url }}" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover;" alt="Existing image">
                                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-existing-image" data-image-id="{{ $image->id }}" style="padding: 0.1rem 0.3rem; font-size: 0.7rem;">
                                                                    <i class="ph-x"></i>
                                                                </button>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @endif
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

                            <!-- Building-Specific Assets Tabs (Dynamic) -->
                            @foreach($buildings as $building)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="building-{{ $building->id }}-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#building-{{ $building->id }}" aria-expanded="false" aria-controls="building-{{ $building->id }}">
                                        <i class="ph-building me-3 text-info"></i>
                                        <span class="fw-bold">{{ strtoupper($building->name) }}</span>
                                        @if($building->buildingType)
                                            <span class="badge bg-info ms-2">{{ $building->buildingType->name }}</span>
                                        @endif
                                        <i class="ph-caret-down ms-auto"></i>
                                    </button>
                                </h2>
                                <div id="building-{{ $building->id }}" class="accordion-collapse collapse" aria-labelledby="building-{{ $building->id }}-header" data-bs-parent="#inspectionAccordion">
                                    <div class="accordion-body">
                                        @php
                                            // Get assets for this specific building based on its type
                                            $buildingAssets = $buildingAssetsMap[$building->id] ?? collect();
                                        @endphp
                                        
                                        @if($buildingAssets->isEmpty())
                                            @php
                                                // Get existing observations/comments for this building (if any)
                                                // For types 3 and 4, we store data with building_asset_id = NULL
                                                $existingObservationsData = \App\Models\BlockInspectionAsset::where('block_inspection_id', $blockInspection->id)
                                                    ->where('block_building_id', $building->id)
                                                    ->whereNull('building_asset_id')
                                                    ->whereNull('block_general_asset_id')
                                                    ->first();
                                                $existingObservations = $existingObservationsData ? $existingObservationsData->comments : '';
                                                $existingComments = $existingObservationsData ? $existingObservationsData->additional_comments : '';
                                            @endphp
                                            
                                            <!-- For Commercial Business Park (Type 4) and Houses (Type 3) -->
                                            @if($building->buildingType && in_array($building->buildingType->id, [3, 4]))
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="building_{{ $building->id }}_observations" class="form-label fw-bold">Other Observations:</label>
                                                            <textarea class="form-control" 
                                                                      id="building_{{ $building->id }}_observations" 
                                                                      name="building_{{ $building->id }}_observations" 
                                                                      rows="5" 
                                                                      placeholder="" 
                                                                      maxlength="500">{{ old('building_' . $building->id . '_observations', $existingObservations) }}</textarea>
                                                            <div class="form-text text-muted">Maximum allowable characters are 500.</div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="building_{{ $building->id }}_comments" class="form-label fw-bold">Comments:</label>
                                                            <textarea class="form-control" 
                                                                      id="building_{{ $building->id }}_comments" 
                                                                      name="building_{{ $building->id }}_comments" 
                                                                      rows="5" 
                                                                      placeholder="" 
                                                                      maxlength="500">{{ old('building_' . $building->id . '_comments', $existingComments) }}</textarea>
                                                            <div class="form-text text-muted">Maximum allowable characters are 500.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-info">
                                                    <i class="ph-info me-2"></i>
                                                    No specific assets configured for this building type ({{ $building->buildingType->name ?? 'Unknown' }}).
                                                </div>
                                            @endif
                                        @else
                                            @foreach($buildingAssets as $asset)
                                            @php
                                                // Get existing data for this building asset (if any)
                                                $existingDataKey = $building->id . '_' . $asset->id;
                                                $existingDataCollection = $existingBuildingInspectionAssets->get($existingDataKey);
                                                $existingData = $existingDataCollection ? $existingDataCollection->first() : null;
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
                                                        @if($asset->valueType && $asset->valueType->inspectionValues)
                                                            @foreach($asset->valueType->inspectionValues as $index => $value)
                                                                @php
                                                                    $isFirst = $index === 0;
                                                                    $isLast = $index === count($asset->valueType->inspectionValues) - 1;
                                                                    $isSelected = $existingData && $existingData->block_inspection_value_id == $value->id;
                                                                    
                                                                    // Border radius classes
                                                                    $radiusClass = '';
                                                                    if ($isFirst) {
                                                                        $radiusClass = 'rounded-start';
                                                                        $radiusStyle = 'border-radius: 0.375rem 0 0 0.375rem !important;';
                                                                    } elseif ($isLast) {
                                                                        $radiusClass = 'rounded-end';
                                                                        $radiusStyle = 'border-radius: 0 0.375rem 0.375rem 0 !important; border-left: 0 !important;';
                                                                    } else {
                                                                        $radiusStyle = 'border-radius: 0 !important; border-left: 0 !important; border-right: 0 !important;';
                                                                    }
                                                                    
                                                                    // Color class based on value name
                                                                    $colorClass = 'building-value-' . strtolower(str_replace([' ', '/'], ['_', '_'], $value->name));
                                                                @endphp
                                                                <input type="radio" class="btn-check" 
                                                                       name="building_{{ $building->id }}_asset_value_{{ $asset->id }}" 
                                                                       id="building_{{ $building->id }}_value_{{ $value->id }}_{{ $asset->id }}" 
                                                                       value="{{ $value->id }}" 
                                                                       autocomplete="off" 
                                                                       {{ $isSelected ? 'checked' : '' }}>
                                                                <label class="btn btn-outline-secondary btn-sm {{ $radiusClass }} {{ $colorClass }}" 
                                                                       for="building_{{ $building->id }}_value_{{ $value->id }}_{{ $asset->id }}" 
                                                                       style="{{ $radiusStyle }} color: black; border: 1px solid #dee2e6;">
                                                                    {{ $value->name }}
                                                                </label>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">No values configured</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Photo Upload Area -->
                                                <div class="col-md-4">
                                                    <label class="form-label small mb-2 fw-bold text-black">Photos:</label>
                                                    <div id="building_{{ $building->id }}_dropzone_{{ $asset->id }}" class="dropzone" style="min-height: 110px; border: 2px dashed #d1d5db; border-radius: 0.375rem; background-color: white;">
                                                        <div class="dz-message" data-dz-message>
                                                            <i class="ph-cloud-arrow-up text-primary mb-2" style="font-size: 1.5rem;"></i>
                                                            <div class="text-muted small">Drag & drop images or click to browse</div>
                                                            <div class="text-muted" style="font-size: 0.75rem;">Max 5MB per image</div>
                                                        </div>
                                                    </div>
                                                    <!-- Existing Images Preview -->
                                                    @if($existingData && $existingData->images->count() > 0)
                                                    <div class="existing-images-preview mt-2">
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($existingData->images as $image)
                                                            <div class="position-relative" style="width: 80px; height: 80px;">
                                                                <img src="{{ $image->image_url }}" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover;" alt="Existing image">
                                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-existing-image" data-image-id="{{ $image->id }}" style="padding: 0.1rem 0.3rem; font-size: 0.7rem;">
                                                                    <i class="ph-x"></i>
                                                                </button>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Notes -->
                                                <div class="col-md-4">
                                                    <label for="building_{{ $building->id }}_notes_{{ $asset->id }}" class="form-label small fw-bold text-black">Note:</label>
                                                    <textarea class="form-control form-control-sm" 
                                                              id="building_{{ $building->id }}_notes_{{ $asset->id }}" 
                                                              name="building_{{ $building->id }}_notes_{{ $asset->id }}" 
                                                              rows="3" 
                                                              placeholder="Maximum allowable characters are 500." 
                                                              maxlength="500">{{ $existingNotes }}</textarea>
                                                    <div class="form-text small text-muted">Maximum allowable characters are 500.</div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach

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
/* Dropzone Custom Styling */
.dropzone {
    border: 2px dashed #d1d5db !important;
    border-radius: 0.375rem;
    background-color: white;
    padding: 10px;
}

.dropzone .dz-message {
    margin: 1em 0;
    text-align: center;
}

.dropzone .dz-preview {
    display: inline-block;
    width: 120px;
    margin: 10px;
    vertical-align: top;
}

.dropzone .dz-preview .dz-image {
    width: 120px;
    height: 120px;
    border-radius: 0.375rem;
    overflow: hidden;
    background: #f3f4f6;
    position: relative;
}

.dropzone .dz-preview .dz-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.dropzone .dz-preview .dz-details {
    padding: 5px;
    font-size: 0.75rem;
}

.dropzone .dz-preview .dz-filename {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.dropzone .dz-preview .dz-size {
    font-size: 0.65rem;
    color: #6b7280;
}

.dropzone .dz-preview .dz-progress {
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
    margin: 5px 0;
}

.dropzone .dz-preview .dz-progress .dz-upload {
    display: block;
    height: 100%;
    background: #3b82f6;
    transition: width 0.3s ease;
}

.dropzone .dz-preview .dz-remove {
    display: block;
    text-align: center;
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 5px;
    cursor: pointer;
    text-decoration: none;
}

.dropzone .dz-preview .dz-remove:hover {
    text-decoration: underline;
}

.dropzone .dz-preview .dz-success-mark,
.dropzone .dz-preview .dz-error-mark {
    display: none;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 2rem;
}

.dropzone .dz-preview.dz-success .dz-success-mark {
    display: block;
    color: #10b981;
}

.dropzone .dz-preview.dz-error .dz-error-mark {
    display: block;
    color: #ef4444;
}

.dropzone .dz-preview .dz-error-message {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #ef4444;
    color: white;
    padding: 5px;
    font-size: 0.7rem;
    border-radius: 0.25rem;
    margin-top: 5px;
    z-index: 10;
}

.dropzone .dz-preview.dz-error .dz-error-message {
    display: block;
}

/* Existing images preview styling */
.existing-images-preview .position-relative {
    position: relative;
}

.existing-images-preview .delete-existing-image {
    opacity: 0;
    transition: opacity 0.2s;
}

.existing-images-preview .position-relative:hover .delete-existing-image {
    opacity: 1;
}

/* Force override Bootstrap's button styles with maximum specificity */
.btn-group .btn-outline-secondary:not([class*="building-value-"]) {
    background-color: white !important;
    color: black !important;
    border-color: #dee2e6 !important;
}

/* Override Bootstrap's default checked state with maximum specificity */
.btn-group .btn-check:checked + .btn-outline-secondary:not([class*="building-value-"]) {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: white !important;
}

.btn-group .btn-check:checked + .btn-outline-secondary:not([class*="building-value-"]):hover {
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
.btn-group .btn-check:checked + .btn-outline-secondary.btn-sm:not([class*="building-value-"]) {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: white !important;
}

.btn-group .btn-check:checked + .btn-outline-secondary.btn-sm:not([class*="building-value-"]):hover {
    background-color: #157347 !important;
    border-color: #146c43 !important;
    color: white !important;
}

/* Building asset value-specific colors */
/* Green values: Yes, Clean, Good, Working, No faults, No lights */
/* Base (unchecked) color for Fire Alarm buttons */
.btn-group .building-value-no_faults {
    background-color: #eaf6ef !important;
    border-color: #198754 !important;
    color: #0f5132 !important;
}

.btn-group .building-value-faults {
    background-color: #fdecee !important;
    border-color: #dc3545 !important;
    color: #842029 !important;
}

.btn-group .building-value-needs_attention {
    background-color: #fff2e5 !important;
    border-color: #e67e22 !important;
    color: #7f3d00 !important;
}

.btn-group .building-value-no_faults:hover,
.btn-group .building-value-faults:hover,
.btn-group .building-value-needs_attention:hover {
    filter: brightness(0.95);
}

.btn-group .btn-check:checked + .building-value-yes,
.btn-group .btn-check:checked + .building-value-clean,
.btn-group .btn-check:checked + .building-value-good,
.btn-group .btn-check:checked + .building-value-working,
.btn-group .btn-check:checked + .building-value-no_faults,
.btn-group .btn-check:checked + .building-value-no_lights,
.btn-group .btn-check:checked + .building-value-n_a {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: white !important;
}

/* Orange/Warning values: Average, Needs Attention, Not checked, Partially Working */
.btn-group .btn-check:checked + .building-value-average,
.btn-group .btn-check:checked + .building-value-needs_attention,
.btn-group .btn-check:checked + .building-value-not_checked,
.btn-group .btn-check:checked + .building-value-partially_working {
    background-color: #e67e22 !important;
    border-color: #e67e22 !important;
    color: white !important;
}

/* Red values: No, Poor, Not Working, Faults */
.btn-group .btn-check:checked + .building-value-no,
.btn-group .btn-check:checked + .building-value-poor,
.btn-group .btn-check:checked + .building-value-not_working,
.btn-group .btn-check:checked + .building-value-faults {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

/* Hover states */
.btn-group .btn-check:checked + .building-value-yes:hover,
.btn-group .btn-check:checked + .building-value-clean:hover,
.btn-group .btn-check:checked + .building-value-good:hover,
.btn-group .btn-check:checked + .building-value-working:hover,
.btn-group .btn-check:checked + .building-value-no_faults:hover,
.btn-group .btn-check:checked + .building-value-no_lights:hover,
.btn-group .btn-check:checked + .building-value-n_a:hover {
    background-color: #157347 !important;
    border-color: #146c43 !important;
    color: white !important;
}

.btn-group .btn-check:checked + .building-value-average:hover,
.btn-group .btn-check:checked + .building-value-needs_attention:hover,
.btn-group .btn-check:checked + .building-value-not_checked:hover,
.btn-group .btn-check:checked + .building-value-partially_working:hover {
    background-color: #d35400 !important;
    border-color: #d35400 !important;
    color: white !important;
}

.btn-group .btn-check:checked + .building-value-no:hover,
.btn-group .btn-check:checked + .building-value-poor:hover,
.btn-group .btn-check:checked + .building-value-not_working:hover,
.btn-group .btn-check:checked + .building-value-faults:hover {
    background-color: #c82333 !important;
    border-color: #bd2130 !important;
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
                
                console.log('Form elements initialized successfully');
            } else {
                console.error('ERROR: Form not found! Check if ID is correct.');
            }


            // Initialize Dropzone for each general asset
            Dropzone.autoDiscover = false;
            
            const dropzones = {};
            const dropzoneFiles = {}; // Store files for each asset
            
            @foreach($generalAssets as $asset)
            (function() {
                const assetId = {{ $asset->id }};
                const dropzoneElement = document.getElementById('dropzone_' + assetId);
                
                if (dropzoneElement) {
                    dropzoneFiles[assetId] = [];
                    
                    const myDropzone = new Dropzone('#dropzone_' + assetId, {
                        url: '#', // Dummy URL since we're handling submission manually
                        paramName: 'photos_' + assetId,
                        autoProcessQueue: false,
                        uploadMultiple: true,
                        parallelUploads: 10,
                        maxFiles: 10,
                        maxFilesize: 5, // 5MB per file
                        acceptedFiles: 'image/*',
                        addRemoveLinks: true,
                        dictDefaultMessage: '',
                        dictRemoveFile: 'Remove',
                        dictCancelUpload: 'Cancel',
                        dictMaxFilesExceeded: 'Maximum 10 files allowed',
                        previewTemplate: `
                            <div class="dz-preview dz-file-preview">
                                <div class="dz-image">
                                    <img data-dz-thumbnail />
                                </div>
                                <div class="dz-details">
                                    <div class="dz-filename"><span data-dz-name></span></div>
                                    <div class="dz-size" data-dz-size></div>
                                </div>
                                <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                                <div class="dz-success-mark"><i class="ph-check-circle"></i></div>
                                <div class="dz-error-mark"><i class="ph-x-circle"></i></div>
                                <div class="dz-error-message"><span data-dz-errormessage></span></div>
                            </div>
                        `,
                        init: function() {
                            const dz = this;
                            
                            // Store reference to dropzone
                            dropzones[assetId] = dz;
                            
                            // Track files when added
                            dz.on('addedfile', function(file) {
                                console.log('File added to dropzone_' + assetId + ':', file.name);
                                dropzoneFiles[assetId].push(file);
                            });
                            
                            // Remove from tracking when removed
                            dz.on('removedfile', function(file) {
                                console.log('File removed from dropzone_' + assetId + ':', file.name);
                                const index = dropzoneFiles[assetId].indexOf(file);
                                if (index > -1) {
                                    dropzoneFiles[assetId].splice(index, 1);
                                }
                            });
                        }
                    });
                }
            })();
            @endforeach
            
            // Initialize Dropzone for each building asset
            @foreach($buildings as $building)
            @php
                $buildingAssets = $buildingAssetsMap[$building->id] ?? collect();
            @endphp
            @foreach($buildingAssets as $asset)
            (function() {
                const buildingId = {{ $building->id }};
                const assetId = {{ $asset->id }};
                const dropzoneKey = 'building_' + buildingId + '_' + assetId;
                const dropzoneElement = document.getElementById('building_' + buildingId + '_dropzone_' + assetId);
                
                if (dropzoneElement) {
                    dropzoneFiles[dropzoneKey] = [];
                    
                    const myDropzone = new Dropzone('#building_' + buildingId + '_dropzone_' + assetId, {
                        url: '#', // Dummy URL since we're handling submission manually
                        paramName: 'building_' + buildingId + '_photos_' + assetId,
                        autoProcessQueue: false,
                        uploadMultiple: true,
                        parallelUploads: 10,
                        maxFiles: 10,
                        maxFilesize: 5, // 5MB per file
                        acceptedFiles: 'image/*',
                        addRemoveLinks: true,
                        dictDefaultMessage: '',
                        dictRemoveFile: 'Remove',
                        dictCancelUpload: 'Cancel',
                        dictMaxFilesExceeded: 'Maximum 10 files allowed',
                        previewTemplate: `
                            <div class="dz-preview dz-file-preview">
                                <div class="dz-image">
                                    <img data-dz-thumbnail />
                                </div>
                                <div class="dz-details">
                                    <div class="dz-filename"><span data-dz-name></span></div>
                                    <div class="dz-size" data-dz-size></div>
                                </div>
                                <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                                <div class="dz-success-mark"><i class="ph-check-circle"></i></div>
                                <div class="dz-error-mark"><i class="ph-x-circle"></i></div>
                                <div class="dz-error-message"><span data-dz-errormessage></span></div>
                            </div>
                        `,
                        init: function() {
                            const dz = this;
                            
                            // Store reference to dropzone
                            dropzones[dropzoneKey] = dz;
                            
                            // Track files when added
                            dz.on('addedfile', function(file) {
                                console.log('File added to building_' + buildingId + '_dropzone_' + assetId + ':', file.name);
                                dropzoneFiles[dropzoneKey].push(file);
                            });
                            
                            // Remove from tracking when removed
                            dz.on('removedfile', function(file) {
                                console.log('File removed from building_' + buildingId + '_dropzone_' + assetId + ':', file.name);
                                const index = dropzoneFiles[dropzoneKey].indexOf(file);
                                if (index > -1) {
                                    dropzoneFiles[dropzoneKey].splice(index, 1);
                                }
                            });
                        }
                    });
                }
            })();
            @endforeach
            @endforeach
            
            // Modify form submission to include dropzone files
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('=== FORM SUBMIT EVENT ===');
                
                // Check form validity first
                if (!form.checkValidity()) {
                    console.error('Form validation failed!');
                    
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
                
                console.log('Form is valid, processing form with Dropzone files...');
                
                // Create FormData from the form
                const formData = new FormData(form);
                
                // Add files from each dropzone
                @foreach($generalAssets as $asset)
                if (dropzoneFiles[{{ $asset->id }}] && dropzoneFiles[{{ $asset->id }}].length > 0) {
                    console.log('Adding ' + dropzoneFiles[{{ $asset->id }}].length + ' files for asset {{ $asset->id }}');
                    dropzoneFiles[{{ $asset->id }}].forEach(function(file, index) {
                        formData.append('photos_{{ $asset->id }}[]', file);
                    });
                }
                @endforeach
                
                // Add files from each building asset dropzone
                @foreach($buildings as $building)
                @php
                    $buildingAssets = $buildingAssetsMap[$building->id] ?? collect();
                @endphp
                @foreach($buildingAssets as $asset)
                const buildingKey_{{ $building->id }}_{{ $asset->id }} = 'building_{{ $building->id }}_{{ $asset->id }}';
                if (dropzoneFiles[buildingKey_{{ $building->id }}_{{ $asset->id }}] && dropzoneFiles[buildingKey_{{ $building->id }}_{{ $asset->id }}].length > 0) {
                    console.log('Adding ' + dropzoneFiles[buildingKey_{{ $building->id }}_{{ $asset->id }}].length + ' files for building {{ $building->id }} asset {{ $asset->id }}');
                    dropzoneFiles[buildingKey_{{ $building->id }}_{{ $asset->id }}].forEach(function(file, index) {
                        formData.append('building_{{ $building->id }}_photos_{{ $asset->id }}[]', file);
                    });
                }
                @endforeach
                @endforeach
                
                // Show loading state
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="ph-spinner-gap me-2 ph-spin"></i>Updating...';
                    submitBtn.disabled = true;
                }
                
                // Submit via AJAX
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success message
                        if (typeof Toastify !== 'undefined') {
                            Toastify({
                                text: "Inspection updated successfully!",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                            }).showToast();
                        }
                        
                        // Redirect after a short delay
                        setTimeout(function() {
                            window.location.href = '{{ route("block-inspections.index") }}';
                        }, 1000);
                    } else {
                        alert('Error: ' + (data.message || 'Failed to update inspection'));
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="ph-check me-2"></i>Update Inspection';
                            submitBtn.disabled = false;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Handle validation errors
                    if (error.errors) {
                        let errorMessage = 'Validation errors:\n';
                        Object.keys(error.errors).forEach(key => {
                            errorMessage += '- ' + error.errors[key].join('\n- ') + '\n';
                        });
                        alert(errorMessage);
                    } else if (error.message) {
                        alert('Error: ' + error.message);
                    } else {
                        alert('An error occurred while updating the inspection. Please try again.');
                    }
                    
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="ph-check me-2"></i>Update Inspection';
                        submitBtn.disabled = false;
                    }
                });
                
                return false;
            });
            
            // Handle deletion of existing images
            document.querySelectorAll('.delete-existing-image').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const imageId = this.getAttribute('data-image-id');
                    
                    if (confirm('Are you sure you want to delete this image?')) {
                        // Send AJAX request to delete the image
                        fetch('/block-inspection-images/' + imageId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove the image element from DOM
                                this.closest('.position-relative').remove();
                                
                                // Show success message
                                alert('Image deleted successfully!');
                            } else {
                                alert('Failed to delete image: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the image.');
                        });
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
