<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Block Information</h6>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBlockInformationModal">
                <i class="ri-add-line align-bottom me-1"></i> Add Block Information
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border">
            <div class="card-header bg-light">
                <h6 class="mb-0">Block Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="fw-semibold">Block ID:</td>
                        <td>#{{ $block->id }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Created:</td>
                        <td>{{ $block->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Last Updated:</td>
                        <td>{{ $block->updated_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Status:</td>
                        <td><span class="badge bg-success">Active</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border">
            <div class="card-header bg-light">
                <h6 class="mb-0">Management Info</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="fw-semibold">Owner:</td>
                        <td>{{ $block->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Block Manager:</td>
                        <td>{{ $block->blockManager->name ?? 'Not Assigned' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Block Type:</td>
                        <td>{{ $block->blockType->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Country:</td>
                        <td>{{ $block->country->country_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">State:</td>
                        <td>{{ $block->state->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Block Address:</td>
                        <td>{{ $block->block_address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Company Address:</td>
                        <td>{{ $block->management_company_address ?? 'Not Provided' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Block Information List -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border">
            <div class="card-header bg-light">
                <h6 class="mb-0">Block Information Details</h6>
            </div>
            <div class="card-body">
                @if(isset($blockInformation) && $blockInformation->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Information Type</th>
                                    <th>Description</th>
                                    <th>Added Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($blockInformation as $info)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $info->informationType->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $info->description ?? 'No description provided' }}</td>
                                        <td>{{ $info->created_at ? $info->created_at->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editBlockInformation({{ $info->id }})">
                                                <i class="ri-edit-line"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBlockInformation({{ $info->id }})">
                                                <i class="ri-delete-bin-line"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ri-information-line text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No block information added yet.</p>
                        <p class="text-muted">Click "Add Block Information" to get started.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Block Information Modal -->
<div class="modal fade" id="addBlockInformationModal" tabindex="-1" aria-labelledby="addBlockInformationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBlockInformationModalLabel">Add Block Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBlockInformationForm" action="{{ route('block-information.store') }}" method="POST">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="information_type_id" class="form-label">Information Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('information_type_id') is-invalid @enderror" 
                                        id="information_type_id" name="information_type_id" required>
                                    <option value="">Select Information Type</option>
                                    @foreach($blockInformationTypes ?? [] as $infoType)
                                        <option value="{{ $infoType->id }}">{{ $infoType->name }}</option>
                                    @endforeach
                                </select>
                                @error('information_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select the type of information you want to add</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="Enter detailed description of the information..." required></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Provide a detailed description of the information</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line align-bottom me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line align-bottom me-1"></i> Save Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Block Information Modal -->
<div class="modal fade" id="editBlockInformationModal" tabindex="-1" aria-labelledby="editBlockInformationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBlockInformationModalLabel">Edit Block Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBlockInformationForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_information_type_id" class="form-label">Information Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_information_type_id" name="information_type_id" required>
                                    <option value="">Select Information Type</option>
                                    @foreach($blockInformationTypes ?? [] as $infoType)
                                        <option value="{{ $infoType->id }}">{{ $infoType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line align-bottom me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line align-bottom me-1"></i> Update Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Function to edit block information
function editBlockInformation(id) {
    // Fetch the block information data
    fetch(`/block-information/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const info = data.data;
                document.getElementById('edit_information_type_id').value = info.information_type_id;
                document.getElementById('edit_description').value = info.description;
                document.getElementById('editBlockInformationForm').action = `/block-information/${id}`;
                
                // Show the edit modal
                const editModal = new bootstrap.Modal(document.getElementById('editBlockInformationModal'));
                editModal.show();
            } else {
                alert('Error loading information: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading information');
        });
}

// Function to delete block information
function deleteBlockInformation(id) {
    if (confirm('Are you sure you want to delete this information? This action cannot be undone.')) {
        fetch(`/block-information/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated data
                location.reload();
            } else {
                alert('Error deleting information: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting information');
        });
    }
}

// Handle form submission for add block information
document.getElementById('addBlockInformationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reload page
            const modal = bootstrap.Modal.getInstance(document.getElementById('addBlockInformationModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error saving information: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving information');
    });
});

// Handle form submission for edit block information
document.getElementById('editBlockInformationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reload page
            const modal = bootstrap.Modal.getInstance(document.getElementById('editBlockInformationModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error updating information: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating information');
    });
});
</script>
@endpush
