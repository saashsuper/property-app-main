        @if($block->units && $block->units->count() > 0)
            <div class="table-responsive">
                <table id="blockUnitsTable" class="table table-bordered table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Unit Code</th>
                            <th>Unit Name</th>
                            <th>Type</th>
                            <th>Owner's Name</th>
                            <th>Salutation</th>
                            <th>Email</th>
                            <th>Resident</th>
                            <th>Mobile</th>
                            <th>Phone</th>
                            <th>Letting Agent</th>
                            <th>Misc Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($block->units as $unit)
                            <tr>
                                <td>{{ $unit->unit_code ?? 'N/A' }}</td>
                                <td>{{ $unit->unit_name ?? 'N/A' }}</td>
                                <td>{{ $unit->unitType->name ?? 'N/A' }}</td>
                                <td>{{ $unit->owners_name ?? 'N/A' }}</td>
                                <td>{{ $unit->salutation ?? 'N/A' }}</td>
                                <td>{{ $unit->email ?? 'N/A' }}</td>
                                <td>{{ $unit->resident ? 'Yes' : 'No' }}</td>
                                <td>{{ $unit->mobile_no ?? 'N/A' }}</td>
                                <td>{{ $unit->phone_number ?? 'N/A' }}</td>
                                <td>{{ $unit->letting_agent ?? 'N/A' }}</td>
                                <td>{{ $unit->misc_info ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                                                    <i class="ph-house text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No units found for this block.</p>
            </div>
        @endif

<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUnitModalLabel">Add Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUnitForm" method="POST" action="{{ route('block-units.store') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="block_building_id" class="form-label">Building/Core <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_building_id" name="block_building_id" required>
                                <option value="">Select Building/Core</option>
                                @foreach($block->buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="block_unit_type_id" class="form-label">Unit Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_unit_type_id" name="block_unit_type_id" required>
                                <option value="">Select Unit Type</option>
                                @foreach(\App\Models\BlockUnitType::orderBy('name')->get() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit_code" class="form-label">Unit Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit_code" name="unit_code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit_name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit_name" name="unit_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="owners_name" class="form-label">Owner's Name</label>
                            <input type="text" class="form-control" id="owners_name" name="owners_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salutation" class="form-label">Salutation</label>
                            <select class="form-select" id="salutation" name="salutation">
                                <option value="">Select Salutation</option>
                                @foreach(\App\Models\Salutation::orderBy('name')->get() as $salutation)
                                    <option value="{{ $salutation->name }}">{{ $salutation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="resident" class="form-label">Resident</label>
                            <select class="form-select" id="resident" name="resident">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mobile_no" class="form-label">Mobile Number</label>
                            <input type="number" class="form-control" id="mobile_no" name="mobile_no" min="0" max="99999999999999999999">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="number" class="form-control" id="phone_number" name="phone_number" min="0" max="99999999999999999999">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="letting_agent" class="form-label">Letting Agent</label>
                            <input type="text" class="form-control" id="letting_agent" name="letting_agent">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="misc_info" class="form-label">Miscellaneous Info</label>
                            <textarea class="form-control" id="misc_info" name="misc_info" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Unit Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUnitModalLabel">Edit Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUnitForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_block_building_id" class="form-label">Building/Core <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_block_building_id" name="block_building_id" required>
                                <option value="">Select Building/Core</option>
                                @foreach($block->buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_block_unit_type_id" class="form-label">Unit Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_block_unit_type_id" name="block_unit_type_id" required>
                                <option value="">Select Unit Type</option>
                                @foreach($blockUnitTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_unit_code" class="form-label">Unit Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_unit_code" name="unit_code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_unit_name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_unit_name" name="unit_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_owners_name" class="form-label">Owner's Name</label>
                            <input type="text" class="form-control" id="edit_owners_name" name="owners_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_salutation" class="form-label">Salutation</label>
                            <select class="form-select" id="edit_salutation" name="salutation">
                                <option value="">Select Salutation</option>
                                @foreach(\App\Models\Salutation::orderBy('name')->get() as $salutation)
                                    <option value="{{ $salutation->name }}">{{ $salutation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_resident" class="form-label">Resident</label>
                            <select class="form-select" id="edit_resident" name="resident">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_mobile_no" class="form-label">Mobile Number</label>
                            <input type="number" class="form-control" id="edit_mobile_no" name="mobile_no" min="0" max="99999999999999999999">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_phone_number" class="form-label">Phone Number</label>
                            <input type="number" class="form-control" id="edit_phone_number" name="phone_number" min="0" max="99999999999999999999">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_letting_agent" class="form-label">Letting Agent</label>
                            <input type="text" class="form-control" id="edit_letting_agent" name="letting_agent">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_misc_info" class="form-label">Miscellaneous Info</label>
                            <textarea class="form-control" id="edit_misc_info" name="misc_info" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
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
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add Unit AJAX submission
    document.getElementById('addUnitForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        let messageDiv = document.getElementById('addUnitMessage');
        if (!messageDiv) {
            messageDiv = document.createElement('div');
            messageDiv.id = 'addUnitMessage';
            messageDiv.className = 'alert d-none';
            form.prepend(messageDiv);
        }
        messageDiv.classList.add('d-none');
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json().catch(() => null) || response)
        .then(data => {
            if (data && data.success) {
                messageDiv.className = 'alert alert-success';
                messageDiv.textContent = 'Unit added successfully!';
                messageDiv.classList.remove('d-none');
                form.reset();
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addUnitModal'));
                    if (modal) modal.hide();
                    // DataTable will refresh automatically when modal closes
                }, 800);
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = (data && data.message) || 'Error saving unit.';
                messageDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error saving unit';
            messageDiv.classList.remove('d-none');
        });
    });

    // Edit Unit AJAX submission
    document.getElementById('editUnitForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        let messageDiv = document.getElementById('editUnitMessage');
        if (!messageDiv) {
            messageDiv = document.createElement('div');
            messageDiv.id = 'editUnitMessage';
            messageDiv.className = 'alert d-none';
            form.prepend(messageDiv);
        }
        messageDiv.classList.add('d-none');
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json().catch(() => null) || response)
        .then(data => {
            if (data && data.success) {
                messageDiv.className = 'alert alert-success';
                messageDiv.textContent = 'Unit updated successfully!';
                messageDiv.classList.remove('d-none');
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editUnitModal'));
                    if (modal) modal.hide();
                    // DataTable will refresh automatically when modal closes
                }, 800);
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = (data && data.message) || 'Error updating unit.';
                messageDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error updating unit';
            messageDiv.classList.remove('d-none');
        });
    });

    // Add event listeners for modal close events
    document.getElementById('addUnitModal').addEventListener('hidden.bs.modal', function() {
        setTimeout(() => {
            refreshBlockUnitsTable();
        }, 100);
    });

    document.getElementById('editUnitModal').addEventListener('hidden.bs.modal', function() {
        setTimeout(() => {
            refreshBlockUnitsTable();
        }, 100);
    });

    // Initialize DataTable
    let blockUnitsDataTable;
    if (window.jQuery && $('#blockUnitsTable').length) {
        blockUnitsDataTable = $('#blockUnitsTable').DataTable({
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

    // Function to refresh the Units DataTable
    window.refreshBlockUnitsTable = function() {
        if (blockUnitsDataTable) {
            // Get the current block ID from the form
            const blockId = document.querySelector('input[name="block_id"]').value;
            
            // Fetch fresh data
            fetch(`/block-units/block/${blockId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear existing data
                        blockUnitsDataTable.clear();
                        
                        // Add new data
                        data.data.forEach(function(unit) {
                            blockUnitsDataTable.row.add([
                                unit.unit_code || '',
                                unit.unit_name || '',
                                unit.owners_name || '',
                                unit.salutation || '',
                                unit.email || '',
                                unit.resident ? 'Yes' : 'No',
                                unit.mobile_no || '',
                                unit.phone_number || '',
                                unit.letting_agent || '',
                                unit.misc_info || '',
                                unit.created_at ? new Date(unit.created_at).toLocaleDateString('en-US', { 
                                    year: 'numeric', 
                                    month: 'short', 
                                    day: '2-digit' 
                                }) : 'N/A',
                                '<button class="btn btn-sm btn-outline-primary" onclick="editUnit(' + unit.id + ')">' +
                                    '<i class="ph-pencil"></i> Edit' +
                                '</button> ' +
                                '<button class="btn btn-sm btn-outline-danger" onclick="deleteUnit(' + unit.id + ')">' +
                                    '<i class="ph-trash"></i> Delete' +
                                '</button>'
                            ]);
                        });
                        
                        // Redraw the table
                        blockUnitsDataTable.draw();
                    }
                })
                .catch(error => {
                    console.error('Error refreshing units table:', error);
                });
        }
    };

    // Initial data load
    refreshBlockUnitsTable();
});

function editUnit(id) {
    fetch(`/block-units/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const u = data.data;
                document.getElementById('edit_block_building_id').value = u.block_building_id;
                document.getElementById('edit_block_unit_type_id').value = u.block_unit_type_id;
                document.getElementById('edit_unit_code').value = u.unit_code;
                document.getElementById('edit_unit_name').value = u.unit_name;
                document.getElementById('edit_owners_name').value = u.owners_name;
                document.getElementById('edit_salutation').value = u.salutation;
                document.getElementById('edit_email').value = u.email;
                document.getElementById('edit_resident').value = u.resident ? '1' : '0';
                document.getElementById('edit_mobile_no').value = u.mobile_no;
                document.getElementById('edit_phone_number').value = u.phone_number;
                document.getElementById('edit_letting_agent').value = u.letting_agent;
                document.getElementById('edit_misc_info').value = u.misc_info;
                document.getElementById('editUnitForm').action = `/block-units/${id}`;
                const modal = new bootstrap.Modal(document.getElementById('editUnitModal'));
                modal.show();
            } else {
                alert('Error loading unit details.');
            }
        });
}

// DataTables for Units
let blockUnitsDataTable;

$(document).ready(function() {
    blockUnitsDataTable = $('#blockUnitsTable').DataTable({
        responsive: true,
        dom: 'rtip', // Removed 'f' (filter/search) to remove the search box on the left
        order: [[0, 'asc']], // default sort by Unit Code
        columnDefs: [
            { targets: [11], orderable: false } // Actions (last column)
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            lengthMenu: "Show _MENU_ units per page",
            info: "Showing _START_ to _END_ of _TOTAL_ units",
            infoEmpty: "Showing 0 to 0 of 0 units",
            infoFiltered: "(filtered from _MAX_ total units)",
            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
        }
    });
});

// Duplicate function removed - now defined inside DOMContentLoaded
</script>
