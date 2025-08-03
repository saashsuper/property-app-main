@extends('layouts.master')
@section('title')
    Edit Block Work Order - PROMAN
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
                    <h4 class="mb-sm-0">Edit Block Work Order</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-work-orders.index') }}">Block Work Orders</a></li>
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
                        <h4 class="card-title">Edit Block Work Order: {{ $blockWorkOrder->ref_no }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('block-work-orders.update', $blockWorkOrder) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Basic Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="block_id" class="form-label">Block <span class="text-danger">*</span></label>
                                        <select class="form-select @error('block_id') is-invalid @enderror" id="block_id" name="block_id" required>
                                            <option value="">Select Block</option>
                                            @foreach($blocks as $block)
                                                <option value="{{ $block->id }}" {{ old('block_id', $blockWorkOrder->block_id) == $block->id ? 'selected' : '' }}>
                                                    {{ $block->name }} - {{ $block->blockType->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('block_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="block_issue_id" class="form-label">Block Issue <span class="text-danger">*</span></label>
                                        <select class="form-select @error('block_issue_id') is-invalid @enderror" id="block_issue_id" name="block_issue_id" required>
                                            <option value="">Select Block Issue</option>
                                            @foreach($blockIssues as $issue)
                                                <option value="{{ $issue->id }}" {{ old('block_issue_id', $blockWorkOrder->block_issue_id) == $issue->id ? 'selected' : '' }}>
                                                    {{ $issue->ref_no }} - {{ $issue->block->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('block_issue_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="ref_no" class="form-label">Reference Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('ref_no') is-invalid @enderror" 
                                               id="ref_no" name="ref_no" value="{{ old('ref_no', $blockWorkOrder->ref_no) }}" required>
                                        @error('ref_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                        <select class="form-select @error('priority_id') is-invalid @enderror" id="priority_id" name="priority_id" required>
                                            <option value="">Select Priority</option>
                                            <option value="1" {{ old('priority_id', $blockWorkOrder->priority_id) == '1' ? 'selected' : '' }}>Low</option>
                                            <option value="2" {{ old('priority_id', $blockWorkOrder->priority_id) == '2' ? 'selected' : '' }}>Normal</option>
                                            <option value="3" {{ old('priority_id', $blockWorkOrder->priority_id) == '3' ? 'selected' : '' }}>High</option>
                                            <option value="4" {{ old('priority_id', $blockWorkOrder->priority_id) == '4' ? 'selected' : '' }}>Urgent</option>
                                            <option value="5" {{ old('priority_id', $blockWorkOrder->priority_id) == '5' ? 'selected' : '' }}>Critical</option>
                                        </select>
                                        @error('priority_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="1" {{ old('status', $blockWorkOrder->status) == '1' ? 'selected' : '' }}>Pending</option>
                                            <option value="2" {{ old('status', $blockWorkOrder->status) == '2' ? 'selected' : '' }}>In Progress</option>
                                            <option value="3" {{ old('status', $blockWorkOrder->status) == '3' ? 'selected' : '' }}>Completed</option>
                                            <option value="4" {{ old('status', $blockWorkOrder->status) == '4' ? 'selected' : '' }}>Cancelled</option>
                                            <option value="5" {{ old('status', $blockWorkOrder->status) == '5' ? 'selected' : '' }}>On Hold</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Location Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Location Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="block_unit_id" class="form-label">Block Unit</label>
                                        <select class="form-select @error('block_unit_id') is-invalid @enderror" id="block_unit_id" name="block_unit_id">
                                            <option value="">Select Unit (Optional)</option>
                                            @foreach($blockUnits as $unit)
                                                <option value="{{ $unit->id }}" {{ old('block_unit_id', $blockWorkOrder->block_unit_id) == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->unit_name }} - {{ $unit->block->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('block_unit_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="block_building_id" class="form-label">Block Building</label>
                                        <select class="form-select @error('block_building_id') is-invalid @enderror" id="block_building_id" name="block_building_id">
                                            <option value="">Select Building (Optional)</option>
                                            @foreach($blockBuildings as $building)
                                                <option value="{{ $building->id }}" {{ old('block_building_id', $blockWorkOrder->block_building_id) == $building->id ? 'selected' : '' }}>
                                                    {{ $building->name }} - {{ $building->block->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('block_building_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="contractor_id" class="form-label">Contractor ID</label>
                                        <input type="number" class="form-control @error('contractor_id') is-invalid @enderror" 
                                               id="contractor_id" name="contractor_id" value="{{ old('contractor_id', $blockWorkOrder->contractor_id) }}">
                                        @error('contractor_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="repair_category_id" class="form-label">Repair Category ID</label>
                                        <input type="number" class="form-control @error('repair_category_id') is-invalid @enderror" 
                                               id="repair_category_id" name="repair_category_id" value="{{ old('repair_category_id', $blockWorkOrder->repair_category_id) }}">
                                        @error('repair_category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Issue Description -->
                            <div class="mb-3">
                                <label for="issue" class="form-label">Issue Description</label>
                                <textarea class="form-control @error('issue') is-invalid @enderror" 
                                          id="issue" name="issue" rows="3">{{ old('issue', $blockWorkOrder->issue) }}</textarea>
                                @error('issue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Schedule Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Schedule</h5>
                                    
                                    <div class="mb-3">
                                        <label for="issued_date_time" class="form-label">Issued Date & Time</label>
                                        <input type="datetime-local" class="form-control @error('issued_date_time') is-invalid @enderror" 
                                               id="issued_date_time" name="issued_date_time" 
                                               value="{{ old('issued_date_time', $blockWorkOrder->issued_date_time ? $blockWorkOrder->issued_date_time->format('Y-m-d\TH:i') : '') }}">
                                        @error('issued_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="preferred_start_date_time" class="form-label">Preferred Start Date & Time</label>
                                        <input type="datetime-local" class="form-control @error('preferred_start_date_time') is-invalid @enderror" 
                                               id="preferred_start_date_time" name="preferred_start_date_time" 
                                               value="{{ old('preferred_start_date_time', $blockWorkOrder->preferred_start_date_time ? $blockWorkOrder->preferred_start_date_time->format('Y-m-d\TH:i') : '') }}">
                                        @error('preferred_start_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="preferred_end_date_time" class="form-label">Preferred End Date & Time</label>
                                        <input type="datetime-local" class="form-control @error('preferred_end_date_time') is-invalid @enderror" 
                                               id="preferred_end_date_time" name="preferred_end_date_time" 
                                               value="{{ old('preferred_end_date_time', $blockWorkOrder->preferred_end_date_time ? $blockWorkOrder->preferred_end_date_time->format('Y-m-d\TH:i') : '') }}">
                                        @error('preferred_end_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="deadline_date" class="form-label">Deadline Date</label>
                                        <input type="date" class="form-control @error('deadline_date') is-invalid @enderror" 
                                               id="deadline_date" name="deadline_date" 
                                               value="{{ old('deadline_date', $blockWorkOrder->deadline_date ? $blockWorkOrder->deadline_date->format('Y-m-d') : '') }}">
                                        @error('deadline_date')
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
                                               id="contact_name" name="contact_name" value="{{ old('contact_name', $blockWorkOrder->contact_name) }}">
                                        @error('contact_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_mobile" class="form-label">Contact Mobile</label>
                                        <input type="text" class="form-control @error('contact_mobile') is-invalid @enderror" 
                                               id="contact_mobile" name="contact_mobile" value="{{ old('contact_mobile', $blockWorkOrder->contact_mobile) }}">
                                        @error('contact_mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Contact Email</label>
                                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                               id="contact_email" name="contact_email" value="{{ old('contact_email', $blockWorkOrder->contact_email) }}">
                                        @error('contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="note_for_access" class="form-label">Note for Access</label>
                                        <input type="text" class="form-control @error('note_for_access') is-invalid @enderror" 
                                               id="note_for_access" name="note_for_access" value="{{ old('note_for_access', $blockWorkOrder->note_for_access) }}">
                                        @error('note_for_access')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comments</label>
                                <textarea class="form-control @error('comment') is-invalid @enderror" 
                                          id="comment" name="comment" rows="4">{{ old('comment', $blockWorkOrder->comment) }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current Files -->
                            @if($blockWorkOrder->pdf_name)
                            <div class="mb-3">
                                <h5 class="mb-3">Current PDF</h5>
                                <div class="alert alert-info">
                                    <i class="ri-file-pdf-line me-2"></i>
                                    <a href="{{ $blockWorkOrder->pdf_url }}" target="_blank">{{ $blockWorkOrder->pdf_name }}</a>
                                </div>
                            </div>
                            @endif

                            @if($blockWorkOrder->images->count() > 0)
                            <div class="mb-3">
                                <h5 class="mb-3">Current Images ({{ $blockWorkOrder->images->count() }})</h5>
                                <div class="row">
                                    @foreach($blockWorkOrder->images as $image)
                                    <div class="col-md-3 mb-2">
                                        <div class="card">
                                            <img src="{{ $image->image_url }}" class="card-img-top" alt="Work Order Image" style="height: 150px; object-fit: cover;">
                                            <div class="card-body p-2">
                                                <small class="text-muted">{{ $image->image_name }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- File Uploads -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="mb-3">PDF Document</h5>
                                        <label for="pdf" class="form-label">Upload New PDF</label>
                                        <input type="file" class="form-control @error('pdf') is-invalid @enderror" 
                                               id="pdf" name="pdf" accept=".pdf">
                                        <div class="form-text">Maximum file size: 10MB</div>
                                        @error('pdf')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="mb-3">Images</h5>
                                        <label for="images" class="form-label">Upload Additional Images</label>
                                        <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                               id="images" name="images[]" multiple accept="image/*">
                                        <div class="form-text">You can select multiple images. Maximum file size: 2MB each.</div>
                                        @error('images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('block-work-orders.index') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> Update Block Work Order
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