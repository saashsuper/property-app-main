<!-- Contractors Header -->
<div class="d-flex align-items-center mb-3 gap-3">
    <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Contractors</h6>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="ph-user-gear text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No contractors assigned to this block.</p>
            </div>
        @endif

<!-- Add Contractor Modal -->
<div class="modal fade" id="addContractorModal" tabindex="-1" aria-labelledby="addContractorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="addContractorModalLabel">Assign Contractor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addContractorForm" method="POST" action="{{ route('block-contractors.store') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div id="addContractorMessage" class="alert d-none" role="alert"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contractor_type_id" class="form-label">Contract Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="contractor_type_id" name="contractor_type_id" required>
                                    <option value="">Select Contract Type</option>
                                    @foreach($contractTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contractor_id" class="form-label">Contractor <span class="text-danger">*</span></label>
                                <select class="form-select" id="contractor_id" name="contractor_id" required>
                                    <option value="">Select Contractor</option>
                                    @foreach($contractors as $contractor)
                                        <option value="{{ $contractor->id }}">{{ $contractor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="default_contractor" name="default_contractor" value="1">
                        <label class="form-check-label" for="default_contractor">
                            Set as Default Contractor
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x align-bottom me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check align-bottom me-1"></i> Assign Contractor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Contractor Modal -->
<div class="modal fade" id="editContractorModal" tabindex="-1" aria-labelledby="editContractorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-gradient-primary text-white">
        <h5 class="modal-title" id="editContractorModalLabel">Edit Contractor</h5>
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
                var modal = bootstrap.Modal.getInstance(document.getElementById('addContractorModal'));
                modal.hide();
                // DataTable will refresh automatically when modal closes
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
                setTimeout(() => {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('editContractorModal'));
                    modal.hide();
                    // Refresh the DataTable instead of reloading the page
                    refreshContractorsTable();
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
                        alert('Contractor deleted successfully!');
                        // Refresh the DataTable instead of reloading the page
                        refreshContractorsTable();
                    } else {
                        alert(data.message || 'Error deleting contractor.');
                    }
                })
                .catch(() => alert('Error deleting contractor.'));
            }
        });
    });

    // Add event listeners for modal close events
    document.getElementById('addContractorModal').addEventListener('hidden.bs.modal', function() {
        setTimeout(() => {
            refreshContractorsTable();
        }, 100);
    });

    document.getElementById('editContractorModal').addEventListener('hidden.bs.modal', function() {
        setTimeout(() => {
            refreshContractorsTable();
        }, 100);
    });

    // Initialize DataTable
    let contractorDataTable;
    if (window.jQuery && $('#contractorTable').length) {
        contractorDataTable = $('#contractorTable').DataTable({
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
    }

    // Function to refresh the Contractors DataTable
    window.refreshContractorsTable = function() {
        if (contractorDataTable) {
            // Get the current block ID from the form
            const blockId = document.querySelector('input[name="block_id"]').value;
            
            // Fetch fresh data
            fetch(`/block-contractors/block/${blockId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear existing data
                        contractorDataTable.clear();
                        
                        // Add new data
                        data.data.forEach(function(contractor) {
                            contractorDataTable.row.add([
                                contractor.contractor_type_name || 'N/A',
                                contractor.contractor_name || 'N/A',
                                contractor.status ? 'Yes' : 'No',
                                contractor.created_at ? new Date(contractor.created_at).toLocaleDateString('en-US', { 
                                    year: 'numeric', 
                                    month: 'short', 
                                    day: '2-digit' 
                                }) : 'N/A',
                                '<button class="btn btn-sm btn-outline-primary edit-contractor-btn" data-id="' + contractor.id + '">' +
                                    '<i class="ph-pencil"></i> Edit' +
                                '</button> ' +
                                '<button class="btn btn-sm btn-outline-danger delete-contractor-btn" data-id="' + contractor.id + '">' +
                                    '<i class="ph-trash"></i> Delete' +
                                '</button>'
                            ]);
                        });
                        
                        // Redraw the table
                        contractorDataTable.draw();
                    }
                })
                .catch(error => {
                    console.error('Error refreshing contractors table:', error);
                });
        }
    };

    // Initial data load
    refreshContractorsTable();
});

// Tab switching code removed
</script>
@endpush
