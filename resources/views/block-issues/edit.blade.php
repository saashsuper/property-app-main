@extends('layouts.master')
@section('title')
    Edit Block Issue - PROMAN
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
                    <h4 class="mb-sm-0">Edit Block Issue</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-issues.index') }}">Block Issues</a></li>
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
                        <h4 class="card-title">Edit Block Issue: {{ $blockIssue->ref_no }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('block-issues.update', $blockIssue) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Basic Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="ref_no" class="form-label">Reference Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('ref_no') is-invalid @enderror" 
                                               id="ref_no" name="ref_no" value="{{ old('ref_no', $blockIssue->ref_no) }}" required>
                                        @error('ref_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="block_id" class="form-label">Block <span class="text-danger">*</span></label>
                                        <select class="form-select @error('block_id') is-invalid @enderror" id="block_id" name="block_id" required>
                                            <option value="">Select Block</option>
                                            @foreach($blocks as $block)
                                                <option value="{{ $block->id }}" {{ old('block_id', $blockIssue->block_id) == $block->id ? 'selected' : '' }}>
                                                    {{ $block->name }} - {{ $block->blockType->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('block_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="title" class="form-label">Issue Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $blockIssue->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="4" required>{{ old('description', $blockIssue->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Issue Details -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Issue Details</h5>
                                    
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                            <option value="">Select Priority</option>
                                            <option value="1" {{ old('priority', $blockIssue->priority) == '1' ? 'selected' : '' }}>Low</option>
                                            <option value="2" {{ old('priority', $blockIssue->priority) == '2' ? 'selected' : '' }}>Normal</option>
                                            <option value="3" {{ old('priority', $blockIssue->priority) == '3' ? 'selected' : '' }}>High</option>
                                            <option value="4" {{ old('priority', $blockIssue->priority) == '4' ? 'selected' : '' }}>Urgent</option>
                                            <option value="5" {{ old('priority', $blockIssue->priority) == '5' ? 'selected' : '' }}>Critical</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="1" {{ old('status', $blockIssue->status) == '1' ? 'selected' : '' }}>Open</option>
                                            <option value="2" {{ old('status', $blockIssue->status) == '2' ? 'selected' : '' }}>In Progress</option>
                                            <option value="3" {{ old('status', $blockIssue->status) == '3' ? 'selected' : '' }}>Resolved</option>
                                            <option value="4" {{ old('status', $blockIssue->status) == '4' ? 'selected' : '' }}>Closed</option>
                                            <option value="5" {{ old('status', $blockIssue->status) == '5' ? 'selected' : '' }}>On Hold</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="assigned_to" class="form-label">Assign To</label>
                                        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                            <option value="">Select User</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('assigned_to', $blockIssue->assigned_to) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('assigned_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="reported_by" class="form-label">Reported By</label>
                                        <select class="form-select @error('reported_by') is-invalid @enderror" id="reported_by" name="reported_by">
                                            <option value="">Select User</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('reported_by', $blockIssue->reported_by) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('reported_by')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Additional Details -->
                                <div class="col-12">
                                    <h5 class="mb-3">Additional Details</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="contact_name" class="form-label">Contact Name</label>
                                                <input type="text" class="form-control @error('contact_name') is-invalid @enderror" 
                                                       id="contact_name" name="contact_name" value="{{ old('contact_name', $blockIssue->contact_name) }}">
                                                @error('contact_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="contact_mobile" class="form-label">Contact Mobile</label>
                                                <input type="text" class="form-control @error('contact_mobile') is-invalid @enderror" 
                                                       id="contact_mobile" name="contact_mobile" value="{{ old('contact_mobile', $blockIssue->contact_mobile) }}">
                                                @error('contact_mobile')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Contact Email</label>
                                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                               id="contact_email" name="contact_email" value="{{ old('contact_email', $blockIssue->contact_email) }}">
                                        @error('contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="preferred_start_date_time" class="form-label">Preferred Start Date/Time</label>
                                                <input type="datetime-local" class="form-control @error('preferred_start_date_time') is-invalid @enderror" 
                                                       id="preferred_start_date_time" name="preferred_start_date_time" 
                                                       value="{{ old('preferred_start_date_time', $blockIssue->preferred_start_date_time ? $blockIssue->preferred_start_date_time->format('Y-m-d\TH:i') : '') }}">
                                                @error('preferred_start_date_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="preferred_end_date_time" class="form-label">Preferred End Date/Time</label>
                                                <input type="datetime-local" class="form-control @error('preferred_end_date_time') is-invalid @enderror" 
                                                       id="preferred_end_date_time" name="preferred_end_date_time" 
                                                       value="{{ old('preferred_end_date_time', $blockIssue->preferred_end_date_time ? $blockIssue->preferred_end_date_time->format('Y-m-d\TH:i') : '') }}">
                                                @error('preferred_end_date_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="note_for_access" class="form-label">Note for Access</label>
                                        <textarea class="form-control @error('note_for_access') is-invalid @enderror" 
                                                  id="note_for_access" name="note_for_access" rows="3">{{ old('note_for_access', $blockIssue->note_for_access) }}</textarea>
                                        @error('note_for_access')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="images" class="form-label">Upload Additional Images</label>
                                        <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                               id="images" name="images[]" multiple accept="image/*">
                                        <small class="form-text text-muted">You can select multiple images. Maximum file size: 2MB each.</small>
                                        @error('images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('block-issues.index') }}" class="btn btn-secondary">
                                            <i class="ri-arrow-left-line me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line me-1"></i> Update Block Issue
                                        </button>
                                    </div>
                                </div>
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