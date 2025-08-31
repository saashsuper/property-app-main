<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Contractor Information</h6>
            <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#addContractorModal">
                <i class="ph-plus align-bottom me-1"></i> Add Contractor
            </button>
        </div>
        
        @if($block->contractors && $block->contractors->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="contractorTable">
                    <thead class="table-light">
                        <tr>
                            <th>Contractor Name</th>
                            <th>Contractor Email</th>
                            <th>Contract Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($block->contractors as $contractor)
                            <tr>
                                <td>{{ $contractor->contractor ? $contractor->contractor->name : 'N/A' }}</td>
                                <td>{{ $contractor->contractor ? $contractor->contractor->email : 'N/A' }}</td>
                                <td>{{ $contractor->contractorType ? $contractor->contractorType->name : 'N/A' }}</td>
                                <td>
                                    @if($contractor->status == 1)
                                        <span class="badge bg-success">Default</span>
                                    @else
                                        <span class="badge bg-info">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary me-1 edit-contractor-btn" data-id="{{ $contractor->id }}">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger delete-contractor-btn" data-id="{{ $contractor->id }}">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                                                    <i class="ph-user-gear text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No contractors assigned to this block.</p>
                <button class="btn btn-primary">Assign Contractor</button>
            </div>
        @endif
    </div>
</div>

<!-- Add Contractor Modal -->
<div class="modal fade" id="addContractorModal" tabindex="-1" aria-labelledby="addContractorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: #0a497a;">
        <h5 class="modal-title text-white" id="addContractorModalLabel">Add Contractor</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addContractorForm">
        <div class="modal-body">
          <input type="hidden" name="block_id" value="{{ $block->id }}">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="contract_type_id" class="form-label fw-bold">Contract Type<span class="text-danger">*</span></label>
              <select class="form-select" id="contract_type_id" name="contractor_type_id" required>
                <option value="">Select Service</option>
                @foreach($contractTypes as $type)
                  <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label for="contractor_id" class="form-label fw-bold">Contractor<span class="text-danger">*</span></label>
              <select class="form-select" id="contractor_id" name="contractor_id" required>
                <option value="">Select Contractor</option>
                @foreach($contractors as $contractor)
                  <option value="{{ $contractor->id }}">{{ $contractor->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="default_contractor" name="default_contractor" value="1">
            <label class="form-check-label fw-bold" for="default_contractor">
              Default Contractor
            </label>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg"></i> Submit</button>
          <button type="reset" class="btn btn-danger px-4"><i class="bi bi-x-lg"></i> Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Contractor Modal -->
<div class="modal fade" id="editContractorModal" tabindex="-1" aria-labelledby="editContractorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: #0a497a;">
        <h5 class="modal-title text-white" id="editContractorModalLabel">Edit Contractor</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editContractorForm">
        <input type="hidden" name="contractor_id" id="edit_contractor_row_id">
        <div id="editContractorMessage" class="alert d-none mb-2"></div>
        <div class="modal-body">
          <input type="hidden" name="block_id" value="{{ $block->id }}">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="edit_contract_type_id" class="form-label fw-bold">Contract Type<span class="text-danger">*</span></label>
              <select class="form-select" id="edit_contract_type_id" name="contractor_type_id" required>
                <option value="">Select Service</option>
                @foreach($contractTypes as $type)
                  <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label for="edit_contractor_id" class="form-label fw-bold">Contractor<span class="text-danger">*</span></label>
              <select class="form-select" id="edit_contractor_id" name="contractor_id" required>
                <option value="">Select Contractor</option>
                @foreach($contractors as $contractor)
                  <option value="{{ $contractor->id }}">{{ $contractor->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="edit_default_contractor" name="default_contractor" value="1">
            <label class="form-check-label fw-bold" for="edit_default_contractor">
              Default Contractor
            </label>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg"></i> Update</button>
          <button type="reset" class="btn btn-danger px-4"><i class="bi bi-x-lg"></i> Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addContractorForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        fetch("{{ route('block-contractors.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Save the active tab to localStorage before reload
                var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                if (activeTab) {
                    localStorage.setItem('activeBlockTab', activeTab.getAttribute('href'));
                }
                var modal = bootstrap.Modal.getInstance(document.getElementById('addContractorModal'));
                modal.hide();
                setTimeout(() => { location.reload(); }, 400);
            } else {
                alert(data.message || 'Error adding contractor.');
            }
        })
        .catch(() => {
            alert('Error adding contractor.');
        });
    });

    // Edit Contractor logic
    document.querySelectorAll('.edit-contractor-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var rowId = this.getAttribute('data-id');
            fetch(`/block-contractors/${rowId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.contractor) {
                        document.getElementById('edit_contractor_row_id').value = rowId;
                        document.getElementById('edit_contract_type_id').value = data.contractor.contractor_type_id;
                        document.getElementById('edit_contractor_id').value = data.contractor.contractor_id;
                        document.getElementById('edit_default_contractor').checked = data.contractor.status == 1;
                        var modal = new bootstrap.Modal(document.getElementById('editContractorModal'));
                        modal.show();
                    } else {
                        alert('Could not fetch contractor details.');
                    }
                })
                .catch(() => alert('Could not fetch contractor details.'));
        });
    });

    // Edit Contractor form submit
    document.getElementById('editContractorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var rowId = document.getElementById('edit_contractor_row_id').value;
        var formData = new FormData(this);
        formData.append('_method', 'PUT'); // method spoofing for Laravel
        fetch(`/block-contractors/${rowId}`, {
            method: 'POST', // must use POST when spoofing PUT with FormData
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            var messageDiv = document.getElementById('editContractorMessage');
            if (data.success) {
                messageDiv.className = 'alert alert-success mb-2';
                messageDiv.textContent = 'Contractor updated successfully!';
                messageDiv.classList.remove('d-none');
                // Save the active tab to localStorage before reload
                var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                if (activeTab) {
                    localStorage.setItem('activeBlockTab', activeTab.getAttribute('href'));
                }
                setTimeout(() => {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('editContractorModal'));
                    modal.hide();
                    setTimeout(() => { location.reload(); }, 400);
                }, 800);
            } else {
                messageDiv.className = 'alert alert-danger mb-2';
                messageDiv.textContent = data.message || 'Error updating contractor.';
                messageDiv.classList.remove('d-none');
            }
        })
        .catch(() => alert('Error updating contractor.'));
    });

    // Delete Contractor logic
    document.querySelectorAll('.delete-contractor-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var rowId = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this contractor?')) {
                fetch(`/block-contractors/${rowId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Save the active tab to localStorage before reload
                        var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                        if (activeTab) {
                            localStorage.setItem('activeBlockTab', activeTab.getAttribute('href'));
                        }
                        alert('Contractor deleted successfully!');
                        setTimeout(() => { location.reload(); }, 400);
                    } else {
                        alert(data.message || 'Error deleting contractor.');
                    }
                })
                .catch(() => alert('Error deleting contractor.'));
            }
        });
    });
});

$(document).ready(function() {
    $('#contractorTable').DataTable({
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
            zeroRecords: "No matching records found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});

// Restore the active tab from localStorage on page load
$(document).ready(function() {
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
