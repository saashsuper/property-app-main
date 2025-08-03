@extends('layouts.master')
@section('title')
    Edit Issue - PROMAN
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
                    <h4 class="mb-sm-0">Edit Issue</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('issues.index') }}">Issues</a></li>
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
                        <h4 class="card-title">Edit Issue: {{ $issue->ref_no }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('issues.update', $issue) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Basic Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="ref_no" class="form-label">Reference Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('ref_no') is-invalid @enderror" 
                                               id="ref_no" name="ref_no" value="{{ old('ref_no', $issue->ref_no) }}" required>
                                        @error('ref_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="title" class="form-label">Issue Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $issue->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="4" required>{{ old('description', $issue->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Maintenance" {{ old('category', $issue->category) == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                            <option value="Repair" {{ old('category', $issue->category) == 'Repair' ? 'selected' : '' }}>Repair</option>
                                            <option value="Security" {{ old('category', $issue->category) == 'Security' ? 'selected' : '' }}>Security</option>
                                            <option value="Cleaning" {{ old('category', $issue->category) == 'Cleaning' ? 'selected' : '' }}>Cleaning</option>
                                            <option value="Other" {{ old('category', $issue->category) == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('category')
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
                                            <option value="1" {{ old('priority', $issue->priority) == '1' ? 'selected' : '' }}>Low</option>
                                            <option value="2" {{ old('priority', $issue->priority) == '2' ? 'selected' : '' }}>Normal</option>
                                            <option value="3" {{ old('priority', $issue->priority) == '3' ? 'selected' : '' }}>High</option>
                                            <option value="4" {{ old('priority', $issue->priority) == '4' ? 'selected' : '' }}>Urgent</option>
                                            <option value="5" {{ old('priority', $issue->priority) == '5' ? 'selected' : '' }}>Critical</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="1" {{ old('status', $issue->status) == '1' ? 'selected' : '' }}>Open</option>
                                            <option value="2" {{ old('status', $issue->status) == '2' ? 'selected' : '' }}>In Progress</option>
                                            <option value="3" {{ old('status', $issue->status) == '3' ? 'selected' : '' }}>Resolved</option>
                                            <option value="4" {{ old('status', $issue->status) == '4' ? 'selected' : '' }}>Closed</option>
                                            <option value="5" {{ old('status', $issue->status) == '5' ? 'selected' : '' }}>On Hold</option>
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
                                                <option value="{{ $user->id }}" {{ old('assigned_to', $issue->assigned_to) == $user->id ? 'selected' : '' }}>
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
                                                <option value="{{ $user->id }}" {{ old('reported_by', $issue->reported_by) == $user->id ? 'selected' : '' }}>
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
                                    
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                               id="location" name="location" value="{{ old('location', $issue->location) }}" placeholder="e.g., Building A, Floor 3, Room 301">
                                        @error('location')
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
                                        <a href="{{ route('issues.index') }}" class="btn btn-secondary">
                                            <i class="ri-arrow-left-line me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line me-1"></i> Update Issue
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