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
                    <form action="{{ route('block-inspections.update', $blockInspection->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
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
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_date_time" class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('scheduled_date_time') is-invalid @enderror" 
                                           name="scheduled_date_time" id="scheduled_date_time" 
                                           value="{{ old('scheduled_date_time', $blockInspection->scheduled_date_time->format('Y-m-d H:i')) }}" required>
                                    @error('scheduled_date_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
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
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lead_inspector" class="form-label">Lead Inspector <span class="text-danger">*</span></label>
                                    <select class="form-select @error('lead_inspector') is-invalid @enderror" name="lead_inspector" required>
                                        <option value="">Select Lead Inspector</option>
                                        @foreach($users as $user)
                                            @php
                                                $isLead = $blockInspection->inspectionTeams->where('user_id', $user->id)->where('is_lead', true)->first();
                                            @endphp
                                            <option value="{{ $user->id }}" {{ old('lead_inspector', $isLead ? $user->id : '') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lead_inspector')
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
                                              name="notes" rows="3" placeholder="Enter any additional notes...">{{ old('notes', $blockInspection->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

@section('script')
    <script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            flatpickr("#scheduled_date_time", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                minuteIncrement: 15
            });
        });
    </script>
@endsection
