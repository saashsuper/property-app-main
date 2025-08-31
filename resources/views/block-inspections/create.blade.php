@extends('layouts.master')

@section('title') Schedule Block Inspection @endsection

@section('css')
    <!-- Select2 css -->
    <link href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Flatpickr css -->
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.blocks') @endslot
        @slot('li_2') @lang('translation.block-inspections') @endslot
        @slot('title') Schedule Inspection @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Schedule New Block Inspection</h4>
                    <p class="card-title-desc">Fill in the details below to schedule a new block inspection.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('block-inspections.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="block_id" class="form-label">Block <span class="text-danger">*</span></label>
                                    <select class="form-select @error('block_id') is-invalid @enderror" name="block_id" required>
                                        <option value="">Select Block</option>
                                        @foreach($blocks as $block)
                                            <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                                {{ $block->name }} - {{ $block->address1 }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('block_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_date_time" class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('scheduled_date_time') is-invalid @enderror" 
                                           name="scheduled_date_time" id="scheduled_date_time" 
                                           value="{{ old('scheduled_date_time') }}" required>
                                    @error('scheduled_date_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              name="notes" rows="3" placeholder="Enter any additional notes...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lead_inspector" class="form-label">Lead Inspector <span class="text-danger">*</span></label>
                                    <select class="form-select @error('lead_inspector') is-invalid @enderror" name="lead_inspector" required>
                                        <option value="">Select Lead Inspector</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('lead_inspector') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lead_inspector')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="team_members" class="form-label">Team Members <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('team_members') is-invalid @enderror" 
                                            name="team_members[]" multiple required>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ in_array($user->id, old('team_members', [])) ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('team_members')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Select additional team members (lead inspector will be automatically included)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('block-inspections.index') }}" class="btn btn-secondary">
                                        <i class="ph-arrow-left me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ph-calendar-plus me-2"></i>Schedule Inspection
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

@section('script')
    <!-- Select2 js -->
    <script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
    <!-- Flatpickr js -->
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: 'Select team members',
                allowClear: true
            });

            // Initialize Flatpickr for date and time
            flatpickr("#scheduled_date_time", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                time_24hr: true,
                minuteIncrement: 15
            });

            // Auto-select lead inspector in team members
            $('select[name="lead_inspector"]').on('change', function() {
                var leadInspectorId = $(this).val();
                var teamMembersSelect = $('select[name="team_members[]"]');
                
                if (leadInspectorId) {
                    // Check if lead inspector is already selected in team members
                    var isSelected = false;
                    teamMembersSelect.find('option:selected').each(function() {
                        if ($(this).val() == leadInspectorId) {
                            isSelected = true;
                            return false;
                        }
                    });
                    
                    if (!isSelected) {
                        teamMembersSelect.find('option[value="' + leadInspectorId + '"]').prop('selected', true);
                        teamMembersSelect.trigger('change');
                    }
                }
            });
        });
    </script>
@endsection
