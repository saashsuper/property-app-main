@extends('layouts.master')
@section('title')
    Edit Block Issue - PROMAN
@endsection
@section('css')
    <style>
        .modal-xl {
            max-width: 90%;
        }
        
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        
        .modal-title {
            color: #495057;
            font-weight: 600;
        }
        
        .work-order-section {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .work-order-section h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>
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
                                <li class="breadcrumb-item"><a href="{{ route('block-issues.index') }}">Block Issues</a>
                                </li>
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
                            <form action="{{ route('block-issues.update', $blockIssue) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Basic Information -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Basic Information</h5>

                                        <div class="mb-3">
                                            <label for="ref_no" class="form-label">Reference Number <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('ref_no') is-invalid @enderror"
                                                id="ref_no" name="ref_no"
                                                value="{{ old('ref_no', $blockIssue->ref_no) }}" required>
                                            @error('ref_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="block_id" class="form-label">Block <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('block_id') is-invalid @enderror"
                                                id="block_id" name="block_id" required>
                                                <option value="">Select Block</option>
                                                @foreach ($blocks as $block)
                                                    <option value="{{ $block->id }}"
                                                        {{ old('block_id', $blockIssue->block_id) == $block->id ? 'selected' : '' }}>
                                                        {{ $block->name }} - {{ $block->blockType->name ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('block_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="issue" class="form-label">Issue <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('issue') is-invalid @enderror"
                                                id="issue" name="issue"
                                                value="{{ old('issue', $blockIssue->issue) }}" required>
                                            @error('issue')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="issue_details" class="form-label">Description <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control @error('issue_details') is-invalid @enderror" id="issue_details" name="issue_details"
                                                rows="4" required>{{ old('issue_details', $blockIssue->issue_details) }}</textarea>
                                            @error('issue_details')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Issue Details -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Issue Details</h5>

                                        <div class="mb-3">
                                            <label for="priority_id" class="form-label">Priority <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('priority_id') is-invalid @enderror"
                                                id="priority_id" name="priority_id" required>
                                                <option value="">Select Priority</option>
                                                @foreach ($priorities as $priority)
                                                    <option value="{{ $priority->id }}"
                                                        {{ old('priority_id', $blockIssue->priority_id) == $priority->id ? 'selected' : '' }}>
                                                        {{ $priority->label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('priority_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="issue_status_id" class="form-label">Status <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('issue_status_id') is-invalid @enderror" id="issue_status_id"
                                                name="issue_status_id" required>
                                                <option value="">Select Status</option>
                                                @foreach ($issue_status as $issue_state)
                                                    <option value="{{ $issue_state->id }}"
                                                        {{ old('issue_status_id', $blockIssue->issue_status_id) == $issue_state->id ? 'selected' : '' }}>
                                                        {{ $issue_state->label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="assigned_to" class="form-label">Assign To</label>
                                            <select class="form-select @error('assigned_to') is-invalid @enderror"
                                                id="assigned_to" name="assigned_to">
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('assigned_to', $blockIssue->assigned_to) == $user->id ? 'selected' : '' }}>
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
                                            <select class="form-select @error('reported_by') is-invalid @enderror"
                                                id="reported_by" name="reported_by">
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('reported_by', $blockIssue->reported_by) == $user->id ? 'selected' : '' }}>
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
                                                    <input type="text"
                                                        class="form-control @error('contact_name') is-invalid @enderror"
                                                        id="contact_name" name="contact_name"
                                                        value="{{ old('contact_name', $blockIssue->contact_name) }}">
                                                    @error('contact_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="contact_mobile" class="form-label">Contact Mobile</label>
                                                    <input type="text"
                                                        class="form-control @error('contact_mobile') is-invalid @enderror"
                                                        id="contact_mobile" name="contact_mobile"
                                                        value="{{ old('contact_mobile', $blockIssue->contact_mobile) }}">
                                                    @error('contact_mobile')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="contact_email" class="form-label">Contact Email</label>
                                            <input type="email"
                                                class="form-control @error('contact_email') is-invalid @enderror"
                                                id="contact_email" name="contact_email"
                                                value="{{ old('contact_email', $blockIssue->contact_email) }}">
                                            @error('contact_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="preferred_start_date_time" class="form-label">Preferred
                                                        Start Date/Time</label>
                                                    <input type="datetime-local"
                                                        class="form-control @error('preferred_start_date_time') is-invalid @enderror"
                                                        id="preferred_start_date_time" name="preferred_start_date_time"
                                                        value="{{ old('preferred_start_date_time', $blockIssue->preferred_start_date_time ? $blockIssue->preferred_start_date_time->format('Y-m-d\TH:i') : '') }}">
                                                    @error('preferred_start_date_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="preferred_end_date_time" class="form-label">Preferred End
                                                        Date/Time</label>
                                                    <input type="datetime-local"
                                                        class="form-control @error('preferred_end_date_time') is-invalid @enderror"
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
                                            <textarea class="form-control @error('note_for_access') is-invalid @enderror" id="note_for_access"
                                                name="note_for_access" rows="3">{{ old('note_for_access', $blockIssue->note_for_access) }}</textarea>
                                            @error('note_for_access')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="images" class="form-label">Upload Additional Images</label>
                                            <input type="file"
                                                class="form-control @error('images.*') is-invalid @enderror"
                                                id="images" name="images[]" multiple accept="image/*">
                                            <small class="form-text text-muted">You can select multiple images. Maximum
                                                file size: 2MB each.</small>
                                            @error('images.*')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createWorkOrderModal">
                                                <i class="ph-plus-circle me-1"></i> Create Work Order
                                            </button>
                                            
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('block-issues.index') }}" class="btn btn-secondary">
                                                    <i class="ph-arrow-left me-1"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ph-floppy-disk me-1"></i> Update Block Issue
                                                </button>
                                            </div>
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

    <!-- Create Work Order Modal -->
    <div class="modal fade" id="createWorkOrderModal" tabindex="-1" aria-labelledby="createWorkOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createWorkOrderModalLabel">Create Work Order for Issue: {{ $blockIssue->ref_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createWorkOrderForm" action="{{ route('block-work-orders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Hidden fields pre-populated from the issue -->
                    <input type="hidden" name="block_id" value="{{ $blockIssue->block_id }}">
                    <input type="hidden" name="block_issue_id" value="{{ $blockIssue->id }}">
                    <input type="hidden" name="issued_by" value="{{ auth()->id() }}">
                    <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                    <input type="hidden" name="updated_by" value="{{ auth()->id() }}">
                    
                    <div class="modal-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="work-order-section">
                                    <h6>Basic Information</h6>
                                
                                <div class="mb-3">
                                    <label for="work_order_ref_no" class="form-label">Reference Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="work_order_ref_no" name="ref_no" 
                                           value="WO-{{ date('Ymd') }}-{{ str_pad($blockIssue->id, 4, '0', STR_PAD_LEFT) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="work_order_priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select" id="work_order_priority_id" name="priority_id" required>
                                        <option value="">Select Priority</option>
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority->value }}" {{ $priority->value == $blockIssue->priority_id ? 'selected' : '' }}>
                                                {{ $priority->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="work_order_status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="work_order_status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="1" selected>Pending</option>
                                        <option value="2">In Progress</option>
                                        <option value="3">Completed</option>
                                        <option value="4">Cancelled</option>
                                        <option value="5">On Hold</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="work_order_issue" class="form-label">Issue Description</label>
                                    <textarea class="form-control" id="work_order_issue" name="issue" rows="3" 
                                              placeholder="Describe the work order issue...">{{ $blockIssue->issue }}</textarea>
                                </div>
                                </div>
                            </div>

                            <!-- Contact & Scheduling -->
                            <div class="col-md-6">
                                <div class="work-order-section">
                                    <h6>Contact & Scheduling</h6>
                                
                                <div class="mb-3">
                                    <label for="work_order_contact_name" class="form-label">Contact Name</label>
                                    <input type="text" class="form-control" id="work_order_contact_name" name="contact_name" 
                                           value="{{ $blockIssue->contact_name }}" placeholder="Enter contact name">
                                </div>

                                <div class="mb-3">
                                    <label for="work_order_contact_mobile" class="form-label">Contact Mobile</label>
                                    <input type="text" class="form-control" id="work_order_contact_mobile" name="contact_mobile" 
                                           value="{{ $blockIssue->contact_mobile }}" placeholder="Enter contact mobile">
                                </div>

                                <div class="mb-3">
                                    <label for="work_order_contact_email" class="form-label">Contact Email</label>
                                    <input type="email" class="form-control" id="work_order_contact_email" name="contact_email" 
                                           value="{{ $blockIssue->contact_email }}" placeholder="Enter contact email">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="work_order_preferred_start" class="form-label">Preferred Start Date/Time</label>
                                            <input type="datetime-local" class="form-control" id="work_order_preferred_start" 
                                                   name="preferred_start_date_time" 
                                                   value="{{ $blockIssue->preferred_start_date_time ? $blockIssue->preferred_start_date_time->format('Y-m-d\TH:i') : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="work_order_preferred_end" class="form-label">Preferred End Date/Time</label>
                                            <input type="datetime-local" class="form-control" id="work_order_preferred_end" 
                                                   name="preferred_end_date_time" 
                                                   value="{{ $blockIssue->preferred_end_date_time ? $blockIssue->preferred_end_date_time->format('Y-m-d\TH:i') : '' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="work_order_deadline" class="form-label">Deadline Date</label>
                                    <input type="date" class="form-control" id="work_order_deadline" name="deadline_date" 
                                           value="{{ now()->addDays(7)->format('Y-m-d') }}">
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="work-order-section">
                                    <h6>Additional Information</h6>
                                
                                <div class="mb-3">
                                    <label for="work_order_note_access" class="form-label">Note for Access</label>
                                    <textarea class="form-control" id="work_order_note_access" name="note_for_access" rows="3" 
                                              placeholder="Enter access notes...">{{ $blockIssue->note_for_access }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="work_order_comment" class="form-label">Additional Comments</label>
                                    <textarea class="form-control" id="work_order_comment" name="comment" rows="3" 
                                              placeholder="Enter additional comments..."></textarea>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ph-plus-circle me-1"></i> Create Work Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Handle work order form submission
        document.getElementById('createWorkOrderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ph-spinner ph-spin me-1"></i> Creating...';
            submitBtn.disabled = true;
            
            // Submit form via AJAX
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    Toastify({
                        text: data.message || 'Work order has been created successfully!',
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                        stopOnFocus: true
                    }).showToast();
                    
                    // Close modal and redirect to work order
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createWorkOrderModal'));
                    modal.hide();
                    
                    // Redirect to the created work order
                    if (data.work_order_id) {
                        window.location.href = `{{ route('block-work-orders.index') }}?highlight=${data.work_order_id}`;
                    }
                } else {
                    // Show error message
                    Toastify({
                        text: data.message || 'Failed to create work order. Please try again.',
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                        stopOnFocus: true
                    }).showToast();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Toastify({
                    text: 'An unexpected error occurred. Please try again.',
                    duration: 5000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                    stopOnFocus: true
                }).showToast();
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // Auto-generate reference number when modal opens
        document.getElementById('createWorkOrderModal').addEventListener('show.bs.modal', function() {
            const refNoField = document.getElementById('work_order_ref_no');
            const currentDate = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            const issueId = '{{ $blockIssue->id }}';
            refNoField.value = `WO-${currentDate}-${issueId.padStart(4, '0')}`;
        });

        // Copy issue details to work order form
        document.getElementById('createWorkOrderModal').addEventListener('show.bs.modal', function() {
            // Copy issue details
            const issueDetails = '{{ $blockIssue->issue_details }}';
            if (issueDetails) {
                document.getElementById('work_order_issue').value = issueDetails;
            }
        });
    </script>
@endsection
