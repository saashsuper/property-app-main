@extends('layouts.master')
@section('title')
    Edit Work Order - PROMAN
@endsection
@section('css')
    <!-- add your css here -->
@endsection
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Work Order</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">Work Orders</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Work Order: {{ $workOrder->code }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('work-orders.update', $workOrder) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Basic Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Work Order Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                               id="code" name="code" value="{{ old('code', $workOrder->code) }}" required>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="property_id" class="form-label">Property ID <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('property_id') is-invalid @enderror" 
                                               id="property_id" name="property_id" value="{{ old('property_id', $workOrder->property_id) }}" required>
                                        @error('property_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="contractor_id" class="form-label">Contractor ID <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('contractor_id') is-invalid @enderror" 
                                               id="contractor_id" name="contractor_id" value="{{ old('contractor_id', $workOrder->contractor_id) }}" required>
                                        @error('contractor_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                            <option value="">Select Priority</option>
                                            <option value="1" {{ old('priority', $workOrder->priority) == '1' ? 'selected' : '' }}>Low</option>
                                            <option value="2" {{ old('priority', $workOrder->priority) == '2' ? 'selected' : '' }}>Normal</option>
                                            <option value="3" {{ old('priority', $workOrder->priority) == '3' ? 'selected' : '' }}>High</option>
                                            <option value="4" {{ old('priority', $workOrder->priority) == '4' ? 'selected' : '' }}>Urgent</option>
                                            <option value="5" {{ old('priority', $workOrder->priority) == '5' ? 'selected' : '' }}>Critical</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="priority_label" class="form-label">Priority Label <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('priority_label') is-invalid @enderror" 
                                               id="priority_label" name="priority_label" value="{{ old('priority_label', $workOrder->priority_label) }}" required>
                                        @error('priority_label')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Issue Details -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Issue Details</h5>
                                    
                                    <div class="mb-3">
                                        <label for="issue_category" class="form-label">Issue Category <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('issue_category') is-invalid @enderror" 
                                               id="issue_category" name="issue_category" value="{{ old('issue_category', $workOrder->issue_category) }}" required>
                                        @error('issue_category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="issue_type" class="form-label">Issue Type <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('issue_type') is-invalid @enderror" 
                                               id="issue_type" name="issue_type" value="{{ old('issue_type', $workOrder->issue_type) }}" required>
                                        @error('issue_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('type') is-invalid @enderror" 
                                               id="type" name="type" value="{{ old('type', $workOrder->type) }}" required>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="type_id" class="form-label">Type ID <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('type_id') is-invalid @enderror" 
                                               id="type_id" name="type_id" value="{{ old('type_id', $workOrder->type_id) }}" required>
                                        @error('type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Fault Description -->
                            <div class="mb-3">
                                <label for="fault_description" class="form-label">Fault Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('fault_description') is-invalid @enderror" 
                                          id="fault_description" name="fault_description" rows="4" required>{{ old('fault_description', $workOrder->fault_description) }}</textarea>
                                @error('fault_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Dates -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Schedule</h5>
                                    
                                    <div class="mb-3">
                                        <label for="issued_date" class="form-label">Issued Date <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('issued_date') is-invalid @enderror" 
                                               id="issued_date" name="issued_date" value="{{ old('issued_date', $workOrder->issued_date) }}" required>
                                        @error('issued_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="deadline" class="form-label">Deadline <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('deadline') is-invalid @enderror" 
                                               id="deadline" name="deadline" value="{{ old('deadline', $workOrder->deadline) }}" required>
                                        @error('deadline')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="pricing" class="form-label">Pricing <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('pricing') is-invalid @enderror" 
                                               id="pricing" name="pricing" value="{{ old('pricing', $workOrder->pricing) }}" required>
                                        @error('pricing')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Contact Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="contact_name" class="form-label">Contact Name</label>
                                        <input type="text" class="form-control @error('contact_name') is-invalid @enderror" 
                                               id="contact_name" name="contact_name" value="{{ old('contact_name', $workOrder->contact_name) }}">
                                        @error('contact_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                                               id="contact_number" name="contact_number" value="{{ old('contact_number', $workOrder->contact_number) }}">
                                        @error('contact_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Contact Email</label>
                                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                               id="contact_email" name="contact_email" value="{{ old('contact_email', $workOrder->contact_email) }}">
                                        @error('contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Preferred Schedule -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Preferred Schedule</h5>
                                    
                                    <div class="mb-3">
                                        <label for="preferred_day" class="form-label">Preferred Day</label>
                                        <input type="text" class="form-control @error('preferred_day') is-invalid @enderror" 
                                               id="preferred_day" name="preferred_day" value="{{ old('preferred_day', $workOrder->preferred_day) }}">
                                        @error('preferred_day')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="time_from" class="form-label">Time From</label>
                                        <input type="text" class="form-control @error('time_from') is-invalid @enderror" 
                                               id="time_from" name="time_from" value="{{ old('time_from', $workOrder->time_from) }}">
                                        @error('time_from')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="time_to" class="form-label">Time To</label>
                                        <input type="text" class="form-control @error('time_to') is-invalid @enderror" 
                                               id="time_to" name="time_to" value="{{ old('time_to', $workOrder->time_to) }}">
                                        @error('time_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Additional Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="note" class="form-label">Note</label>
                                        <input type="text" class="form-control @error('note') is-invalid @enderror" 
                                               id="note" name="note" value="{{ old('note', $workOrder->note) }}">
                                        @error('note')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="report" class="form-label">Report</label>
                                        <input type="text" class="form-control @error('report') is-invalid @enderror" 
                                               id="report" name="report" value="{{ old('report', $workOrder->report) }}">
                                        @error('report')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Current Images -->
                            @if($workOrder->images->count() > 0)
                            <div class="mb-3">
                                <h5 class="mb-3">Current Images</h5>
                                <div class="row">
                                    @foreach($workOrder->images as $image)
                                    <div class="col-md-3 mb-2">
                                        <div class="card">
                                            <img src="{{ $image->image_url }}" class="card-img-top" alt="Work Order Image" style="height: 150px; object-fit: cover;">
                                            <div class="card-body p-2">
                                                <small class="text-muted">{{ $image->image }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Images Upload -->
                            <div class="mb-3">
                                <h5 class="mb-3">Add More Images</h5>
                                <label for="images" class="form-label">Upload Additional Images</label>
                                <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                       id="images" name="images[]" multiple accept="image/*">
                                <div class="form-text">You can select multiple images. Maximum file size: 2MB each.</div>
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('work-orders.index') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> Update Work Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <!-- add your js here -->
@endsection 