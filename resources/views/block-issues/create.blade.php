@extends('layouts.master')
@section('title')
    Create Block Issue - PROMAN
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
                    <h4 class="mb-sm-0">Create Block Issue</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-issues.index') }}">Block Issues</a></li>
                            <li class="breadcrumb-item active">Create</li>
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
                        <h4 class="card-title">Create New Block Issue</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('block-issues.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <!-- Row 1: Contact Method, Unit Selection, Assigned To -->
                                <div class="col-md-4 mb-3">
                                    <label for="contact_method_id" class="form-label">Contact Method <span class="text-danger">*</span></label>
                                    <select class="form-select @error('contact_method_id') is-invalid @enderror" id="contact_method_id" name="contact_method_id" required>
                                        <option value="">Select Contact Method</option>
                                        <option value="1" {{ old('contact_method_id') == '1' ? 'selected' : '' }}>Phone</option>
                                        <option value="2" {{ old('contact_method_id') == '2' ? 'selected' : '' }}>Email</option>
                                        <option value="3" {{ old('contact_method_id') == '3' ? 'selected' : '' }}>SMS</option>
                                        <option value="4" {{ old('contact_method_id') == '4' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="5" {{ old('contact_method_id') == '5' ? 'selected' : '' }}>In Person</option>
                                    </select>
                                    @error('contact_method_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="block_unit_id" class="form-label">Unit Selection <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('block_unit_id') is-invalid @enderror" 
                                           id="block_unit_id" name="block_unit_id" placeholder="Search for units..." autocomplete="off" required>
                                    <input type="hidden" id="block_unit_id_hidden" name="block_unit_id_hidden">
                                    @error('block_unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="assigned_to" class="form-label">Assigned To <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('assigned_to') is-invalid @enderror" 
                                           id="assigned_to" name="assigned_to" placeholder="Search property managers..." autocomplete="off" required>
                                    <input type="hidden" id="assigned_to_hidden" name="assigned_to_hidden">
                                    @error('assigned_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Row 2: Issue Type, Priority, Issue Title -->
                                <div class="col-md-4 mb-3">
                                    <label for="issue_type" class="form-label">Issue Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('issue_type') is-invalid @enderror" id="issue_type" name="issue_type" required>
                                        <option value="">Select Issue Type</option>
                                        <option value="plumbing" {{ old('issue_type') == 'plumbing' ? 'selected' : '' }}>Plumbing</option>
                                        <option value="electrical" {{ old('issue_type') == 'electrical' ? 'selected' : '' }}>Electrical</option>
                                        <option value="hvac" {{ old('issue_type') == 'hvac' ? 'selected' : '' }}>HVAC</option>
                                        <option value="structural" {{ old('issue_type') == 'structural' ? 'selected' : '' }}>Structural</option>
                                        <option value="security" {{ old('issue_type') == 'security' ? 'selected' : '' }}>Security</option>
                                        <option value="fire_safety" {{ old('issue_type') == 'fire_safety' ? 'selected' : '' }}>Fire Safety</option>
                                        <option value="water_leakage" {{ old('issue_type') == 'water_leakage' ? 'selected' : '' }}>Water Leakage</option>
                                        <option value="noise" {{ old('issue_type') == 'noise' ? 'selected' : '' }}>Noise Complaint</option>
                                        <option value="parking" {{ old('issue_type') == 'parking' ? 'selected' : '' }}>Parking Issue</option>
                                        <option value="landscaping" {{ old('landscaping') == 'landscaping' ? 'selected' : '' }}>Landscaping</option>
                                        <option value="elevator" {{ old('elevator') == 'elevator' ? 'selected' : '' }}>Elevator</option>
                                        <option value="internet" {{ old('internet') == 'internet' ? 'selected' : '' }}>Internet</option>
                                        <option value="trash" {{ old('trash') == 'trash' ? 'selected' : '' }}>Trash Collection</option>
                                        <option value="lighting" {{ old('lighting') == 'lighting' ? 'selected' : '' }}>Lighting</option>
                                        <option value="access_control" {{ old('access_control') == 'access_control' ? 'selected' : '' }}>Access Control</option>
                                    </select>
                                    @error('issue_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority_id') is-invalid @enderror" id="priority_id" name="priority_id" required>
                                        <option value="">Select Priority</option>
                                        <option value="1" {{ old('priority_id') == '1' ? 'selected' : '' }}>Low</option>
                                        <option value="2" {{ old('priority_id') == '2' ? 'selected' : '' }} selected>Normal</option>
                                        <option value="3" {{ old('priority_id') == '3' ? 'selected' : '' }}>High</option>
                                        <option value="4" {{ old('priority_id') == '4' ? 'selected' : '' }}>Urgent</option>
                                        <option value="5" {{ old('priority_id') == '5' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('priority_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="issue" class="form-label">Issue Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('issue') is-invalid @enderror" 
                                           id="issue" name="issue" value="{{ old('issue') }}" required>
                                    @error('issue')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Row 3: Email, Fault Details -->
                                <div class="col-md-6 mb-3">
                                    <label for="contact_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                           id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required>
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="issue_details" class="form-label">Issue Details</label>
                                    <textarea class="form-control @error('issue_details') is-invalid @enderror" 
                                              id="issue_details" name="issue_details" rows="2" placeholder="Describe the issue in detail...">{{ old('issue_details') }}</textarea>
                                    @error('issue_details')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Row 4: Default Contact Details -->
                                <div class="col-12 mb-3">
                                    <label for="default_contact_details" class="form-label">Default Contact Details</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="use_default_contact" checked>
                                        <label class="form-check-label" for="use_default_contact">
                                            Use default contact details
                                        </label>
                                    </div>
                                    <textarea class="form-control @error('default_contact_details') is-invalid @enderror" 
                                              id="default_contact_details" name="default_contact_details" rows="2" placeholder="Enter default contact information..." readonly>{{ old('default_contact_details') }}</textarea>
                                    @error('default_contact_details')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Row 5: File Upload -->
                                <div class="col-12 mb-3">
                                    <label for="images" class="form-label">Upload Images</label>
                                    <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                           id="images" name="images[]" multiple accept="image/*">
                                    <small class="form-text text-muted">You can select multiple images. Maximum file size: 2MB each.</small>
                                    @error('images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Additional Details -->
                                <div class="col-12">
                                    <h5 class="mb-3">Additional Details</h5>
                                    


                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                                                <a href="{{ route('block-issues.index') }}" class="btn btn-secondary">
                            <i class="ph-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-floppy-disk me-1"></i> Create Block Issue
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
<style>
/* Custom styling for autocomplete dropdowns to match Bootstrap form design */
.autoComplete_wrapper {
    position: relative;
    display: block;
    width: 100%;
}

.autoComplete_wrapper > input {
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.autoComplete_wrapper > input:focus {
    color: #495057;
    background-color: #fff;
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.autoComplete_wrapper > ul {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    margin: 0;
    padding: 0;
    list-style: none;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.25rem 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    max-height: 200px;
    overflow-y: auto;
}

.autoComplete_wrapper > ul > li {
    padding: 0.375rem 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
}

.autoComplete_wrapper > ul > li:last-child {
    border-bottom: none;
}

.autoComplete_wrapper > ul > li:hover,
.autoComplete_wrapper > ul > li[aria-selected="true"] {
    background-color: #e9ecef;
    color: #495057;
}

.autoComplete_wrapper > ul > li mark {
    background-color: #fff3cd;
    color: #495057;
    padding: 0;
}

/* Hide the lens icon in autocomplete */
.autoComplete_wrapper > input::before {
    display: none !important;
}

.autoComplete_wrapper > input::-webkit-search-cancel-button,
.autoComplete_wrapper > input::-webkit-search-decoration,
.autoComplete_wrapper > input::-webkit-search-results-button,
.autoComplete_wrapper > input::-webkit-search-results-decoration {
    display: none !important;
}
</style>

<script>

// AutoComplete for Units
const unitAutoComplete = new autoComplete({
    selector: () => document.getElementById("block_unit_id"),
    placeHolder: "Search for units...",
    data: {
        src: async (query) => {
            try {
                const source = await fetch(`/api/units/search?q=${encodeURIComponent(query)}`);
                const data = await source.json();
                return data;
            } catch (error) {
                return [];
            }
        },
        keys: ["unit_code", "unit_name"]
    },
    resultItem: {
        highlight: true
    },
    events: {
        input: {
            selection: (event) => {
                const selection = event.detail.selection.value;
                document.getElementById("block_unit_id_hidden").value = selection.id;
            }
        }
    }
});

// AutoComplete for Property Managers
const assignedToAutoComplete = new autoComplete({
    selector: () => document.getElementById("assigned_to"),
    placeHolder: "Search property managers...",
    data: {
        src: async (query) => {
            try {
                const source = await fetch(`/api/users/property-managers?q=${encodeURIComponent(query)}`);
                const data = await source.json();
                return data;
            } catch (error) {
                return [];
            }
        },
        keys: ["name", "email"]
    },
    resultItem: {
        highlight: true
    },
    events: {
        input: {
            selection: (event) => {
                const selection = event.detail.selection.value;
                document.getElementById("assigned_to_hidden").value = selection.id;
            }
        }
    }
});



// Default contact details checkbox
document.getElementById('use_default_contact').addEventListener('change', function() {
    const textarea = document.getElementById('default_contact_details');
    textarea.readOnly = this.checked;
    if (this.checked) {
        textarea.value = 'Default contact information will be used';
    } else {
        textarea.value = '';
    }
});
</script>
@endsection 