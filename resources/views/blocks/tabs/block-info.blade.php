<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Block Information</h6>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBlockInformationModal">
                <i class="ph-plus align-bottom me-1"></i> Add Block Information
            </button>
        </div>
    </div>
</div>

<!-- Block Information List -->
<div class="row mt-4">
    <div class="col-12 p-0">
        <table id="blockInformationTable" class="table table-bordered table-hover w-100">
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
                                        <i class="ph-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBlockInformation({{ $info->id }})">
                                        <i class="ph-trash"></i> Delete
                                    </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
            @php
                $usedTypeIds = isset($blockInformation) ? $blockInformation->pluck('information_type_id')->toArray() : [];
            @endphp
            <form id="addBlockInformationForm" action="{{ route('block-information.store') }}" method="POST">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <!-- Message area for success/failure -->
                <div id="addBlockInformationMessage" class="alert d-none" role="alert"></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="information_type_id" class="form-label">Information Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('information_type_id') is-invalid @enderror" 
                                        id="information_type_id" name="information_type_id" required>
                                    <option value="">Select Information Type</option>
                                    @if(isset($blockInformationTypes) && $blockInformationTypes->count() > 0)
                                        @foreach($blockInformationTypes as $infoType)
                                            <option value="{{ $infoType->id }}" @if(in_array($infoType->id, $usedTypeIds)) disabled @endif>
                                                {{ $infoType->name }}@if(in_array($infoType->id, $usedTypeIds)) (Already added)@endif
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No information types available</option>
                                    @endif
                                </select>
                                @error('information_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Select the type of information you want to add
                                    @if(isset($blockInformationTypes))
                                        ({{ $blockInformationTypes->count() }} types available)
                                    @endif
                                </div>
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
                            <i class="ph-x align-bottom me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-floppy-disk align-bottom me-1"></i> Save Information
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
            @php
                $usedTypeIds = isset($blockInformation) ? $blockInformation->pluck('information_type_id')->toArray() : [];
            @endphp
            <form id="editBlockInformationForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div id="editBlockInformationMessage" class="alert d-none" role="alert"></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_information_type_id" class="form-label">Information Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_information_type_id" name="information_type_id" required>
                                    <option value="">Select Information Type</option>
                                    @if(isset($blockInformationTypes) && $blockInformationTypes->count() > 0)
                                        @foreach($blockInformationTypes as $infoType)
                                            <option value="{{ $infoType->id }}"
                                                @if(in_array($infoType->id, $usedTypeIds)) disabled @endif>
                                                {{ $infoType->name }}@if(in_array($infoType->id, $usedTypeIds)) (Already added)@endif
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No information types available</option>
                                    @endif
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
                            <i class="ph-x align-bottom me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-floppy-disk align-bottom me-1"></i> Update Information
                        </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.jQuery && $('#blockInformationTable').length) {
        $('#blockInformationTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries to show",
                infoFiltered: "(filtered from _MAX_ total entries)",
                zeroRecords: "No matching records found",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }
    // Function to edit block information
    window.editBlockInformation = function(id) {
        // Fetch the block information data
        fetch(`/block-information/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const info = data.data;
                    // Enable all options first
                    const select = document.getElementById('edit_information_type_id');
                    for (let i = 0; i < select.options.length; i++) {
                        select.options[i].disabled = false;
                    }
                    // Disable used types except the current one
                    const usedTypeIds = @json($usedTypeIds);
                    for (let i = 0; i < select.options.length; i++) {
                        const opt = select.options[i];
                        if (usedTypeIds.includes(parseInt(opt.value)) && parseInt(opt.value) !== info.information_type_id) {
                            opt.disabled = true;
                            opt.text = opt.text.replace(' (Already added)', '') + ' (Already added)';
                        } else if (parseInt(opt.value) === info.information_type_id) {
                            opt.disabled = false;
                            opt.text = opt.text.replace(' (Already added)', '');
                        }
                    }
                    select.value = info.information_type_id;
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
    window.deleteBlockInformation = function(id) {
        if (confirm('Are you sure you want to delete this information? This action cannot be undone.')) {
            fetch(`/block-information/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Add a message area if not present
                let messageDiv = document.getElementById('blockInformationDeleteMessage');
                if (!messageDiv) {
                    messageDiv = document.createElement('div');
                    messageDiv.id = 'blockInformationDeleteMessage';
                    messageDiv.className = 'alert d-none';
                    document.body.appendChild(messageDiv);
                }
                if (data.success) {
                    messageDiv.className = 'alert alert-success';
                    messageDiv.textContent = 'Block information deleted successfully!';
                    messageDiv.classList.remove('d-none');
                    // Save the active tab to localStorage before reload
                    var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                    if (activeTab) {
                        localStorage.setItem('activeBlockTab', activeTab.getAttribute('href'));
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 800);
                } else {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.textContent = data.message || 'Error deleting information.';
                    messageDiv.classList.remove('d-none');
                }
            })
            .catch(error => {
                let messageDiv = document.getElementById('blockInformationDeleteMessage');
                if (!messageDiv) {
                    messageDiv = document.createElement('div');
                    messageDiv.id = 'blockInformationDeleteMessage';
                    messageDiv.className = 'alert d-none';
                    document.body.appendChild(messageDiv);
                }
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = 'Error deleting information';
                messageDiv.classList.remove('d-none');
            });
        }
    }

    // Handle form submission for add block information
    document.getElementById('addBlockInformationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const messageDiv = document.getElementById('addBlockInformationMessage');
        messageDiv.classList.add('d-none'); // Hide before new request

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
                messageDiv.className = 'alert alert-success';
                messageDiv.textContent = 'Block information added successfully!';
                messageDiv.classList.remove('d-none');
                this.reset();
                // Close the modal after a short delay, then reload the page
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addBlockInformationModal'));
                    if (modal) modal.hide();
                    // Save the active tab to localStorage before reload
                    var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                    if (activeTab) {
                        localStorage.setItem('activeBlockTab', activeTab.getAttribute('href'));
                    }
                    // Reload the page to refresh the data table
                    setTimeout(() => {
                        location.reload();
                    }, 400);
                }, 800);
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = data.message || 'Error saving information.';
                messageDiv.classList.remove('d-none');
            }
        })
        .catch(error => {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error saving information';
            messageDiv.classList.remove('d-none');
        });
    });

    // Handle form submission for edit block information
    document.getElementById('editBlockInformationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        // Add a message area if not present
        let messageDiv = document.getElementById('editBlockInformationMessage');
        if (!messageDiv) {
            messageDiv = document.createElement('div');
            messageDiv.id = 'editBlockInformationMessage';
            messageDiv.className = 'alert d-none';
            this.prepend(messageDiv);
        }
        messageDiv.classList.add('d-none');

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
                messageDiv.className = 'alert alert-success';
                messageDiv.textContent = 'Block information updated successfully!';
                messageDiv.classList.remove('d-none');
                // Close the modal after a short delay, then reload the page
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editBlockInformationModal'));
                    if (modal) modal.hide();
                    // Save the active tab to localStorage before reload
                    var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                    if (activeTab) {
                        localStorage.setItem('activeBlockTab', activeTab.getAttribute('href'));
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 400);
                }, 800);
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = data.message || 'Error updating information.';
                messageDiv.classList.remove('d-none');
            }
        })
        .catch(error => {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error updating information';
            messageDiv.classList.remove('d-none');
        });
    });

    // Restore the active tab from localStorage
    var lastTab = localStorage.getItem('activeBlockTab');
    if (lastTab) {
        var triggerTab = document.querySelector('.nav-link[data-bs-toggle="tab"][href="' + lastTab + '"]');
        if (triggerTab) {
            var tab = new bootstrap.Tab(triggerTab);
            tab.show();
        }
        localStorage.removeItem('activeBlockTab');
    }
});
</script>
@endpush
