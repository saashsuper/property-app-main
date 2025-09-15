<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Building Information</h6>
            <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#addBuildingModal">
                <i class="ph-plus align-bottom me-1"></i> Add Building
            </button>
        </div>
        
        <div class="table-responsive w-100">
            <table class="table table-bordered table-hover w-100" id="building-info-table">
                <thead class="table-light">
                    <tr>
                        <th>Building Name</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Roof Type</th>
                        <th>No of Lifts</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($block->buildings && $block->buildings->count() > 0)
                        @foreach($block->buildings as $building)
                            <tr>
                                <td>{{ $building->name ?? 'N/A' }}</td>
                                <td>{{ $building->buildingType->name ?? 'N/A' }}</td>
                                <td>{{ $building->floor_no ?? 'N/A' }}</td>
                                <td>{{ $building->roof_type ?? 'N/A' }}</td>
                                <td>{{ $building->no_lift ?? 'N/A' }}</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editBuilding({{ $building->id }})">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBuilding({{ $building->id }})">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Building Modal -->
<div class="modal fade" id="addBuildingModal" tabindex="-1" aria-labelledby="addBuildingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="addBuildingModalLabel" style="color: white !important; padding-bottom: 15px;">Add Building</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="addBuildingForm" method="POST" action="{{ route('block-buildings.store') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="building_type_id" class="form-label">Building Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="building_type_id" name="building_type_id" required>
                            <option value="">Select Building Type</option>
                            @foreach($blockBuildingTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="building_name" class="form-label">Building Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="building_name" name="building_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_of_floors" class="form-label">No of Floors <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="no_of_floors" name="no_of_floors" required min="1" max="999" maxlength="3">
                    </div>
                    <div class="mb-3">
                        <label for="roof_type" class="form-label">Roof Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="roof_type" name="roof_type" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_lift" class="form-label">No of Lifts <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="no_lift" name="no_lift" required min="0" max="999" maxlength="3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Save
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Building Modal -->
<div class="modal fade" id="editBuildingModal" tabindex="-1" aria-labelledby="editBuildingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="editBuildingModalLabel" style="color: white !important; padding-bottom: 15px;">Edit Building</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="editBuildingForm" method="POST" action="{{ route('block-buildings.update', 0) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_building_type_id" class="form-label">Building Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_building_type_id" name="building_type_id" required>
                            <option value="">Select Building Type</option>
                            @foreach($blockBuildingTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_building_name" class="form-label">Building Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_building_name" name="building_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_no_of_floors" class="form-label">No of Floors <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_no_of_floors" name="no_of_floors" required min="1" max="999" maxlength="3">
                    </div>
                    <div class="mb-3">
                        <label for="edit_roof_type" class="form-label">Roof Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_roof_type" name="roof_type" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_no_lift" class="form-label">No of Lifts <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_no_lift" name="no_lift" required min="0" max="999" maxlength="3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Update
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add input validation for 3-digit limit
    function validateThreeDigits(input) {
        const value = input.value;
        if (value.length > 3) {
            input.value = value.slice(0, 3);
        }
        if (parseInt(value) > 999) {
            input.value = '999';
        }
    }

    // Add event listeners for input validation
    document.getElementById('no_of_floors').addEventListener('input', function() {
        validateThreeDigits(this);
    });

    document.getElementById('no_lift').addEventListener('input', function() {
        validateThreeDigits(this);
    });

    document.getElementById('edit_no_of_floors').addEventListener('input', function() {
        validateThreeDigits(this);
    });

    document.getElementById('edit_no_lift').addEventListener('input', function() {
        validateThreeDigits(this);
    });

    // Add Building AJAX submission
    document.getElementById('addBuildingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        let messageDiv = document.getElementById('addBuildingMessage');
        if (!messageDiv) {
            messageDiv = document.createElement('div');
            messageDiv.id = 'addBuildingMessage';
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
                messageDiv.textContent = 'Building added successfully!';
                messageDiv.classList.remove('d-none');
                form.reset();
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addBuildingModal'));
                    if (modal) modal.hide();
                    // Refresh the DataTable after modal closes
                    refreshBuildingsTable();
                }, 800);
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = (data && data.message) || 'Error saving building.';
                messageDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error saving building';
            messageDiv.classList.remove('d-none');
        });
    });

    // Edit Building AJAX submission
    document.getElementById('editBuildingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        let messageDiv = document.getElementById('editBuildingMessage');
        if (!messageDiv) {
            messageDiv = document.createElement('div');
            messageDiv.id = 'editBuildingMessage';
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
                messageDiv.textContent = 'Building updated successfully!';
                messageDiv.classList.remove('d-none');
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editBuildingModal'));
                    if (modal) modal.hide();
                    // Refresh the DataTable after modal closes
                    refreshBuildingsTable();
                }, 800);
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = (data && data.message) || 'Error updating building.';
                messageDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error updating building';
            messageDiv.classList.remove('d-none');
        });
    });

});
function editBuilding(id) {
    fetch(`/block-buildings/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const b = data.data;
                document.getElementById('edit_building_type_id').value = b.building_type_id;
                document.getElementById('edit_building_name').value = b.name;
                document.getElementById('edit_no_of_floors').value = b.floor_no;
                document.getElementById('edit_roof_type').value = b.roof_type;
                document.getElementById('edit_no_lift').value = b.no_lift;
                document.getElementById('editBuildingForm').action = `/block-buildings/${id}`;
                const modal = new bootstrap.Modal(document.getElementById('editBuildingModal'));
                modal.show();
            } else {
                alert('Error loading building details.');
            }
        });
}
// Tab switching code removed
</script>
@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
<script>
let buildingDataTable;

$(document).ready(function() {
    buildingDataTable = $('#building-info-table').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No Building Informations found",
            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
        },
        initComplete: function() {
            // Increase search box size
            $('.dataTables_filter input').addClass('form-control').css({
                'width': '300px',
                'height': '38px',
                'font-size': '14px'
            });
        }
    });
});

// Function to refresh the Buildings DataTable
window.refreshBuildingsTable = function() {
    if (buildingDataTable) {
        // Get the current block ID from the form
        const blockId = document.querySelector('input[name="block_id"]').value;
        
        // Fetch fresh data
        fetch(`/block-buildings/block/${blockId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear existing data
                    buildingDataTable.clear();
                    
                    // Add new data
                    data.data.forEach(function(building) {
                        buildingDataTable.row.add([
                            building.name || 'N/A',
                            building.building_type_name || 'N/A',
                            building.floor_no || 'N/A',
                            building.roof_type || 'N/A',
                            building.no_lift || 'N/A',
                            '<span class="badge bg-success">Active</span>',
                            '<button class="btn btn-sm btn-outline-primary" onclick="editBuilding(' + building.id + ')">' +
                                '<i class="ph-pencil"></i> Edit' +
                            '</button> ' +
                            '<button class="btn btn-sm btn-outline-danger" onclick="deleteBuilding(' + building.id + ')">' +
                                '<i class="ph-trash"></i> Delete' +
                            '</button>'
                        ]);
                    });
                    
                    // Redraw the table
                    buildingDataTable.draw();
                }
            })
            .catch(error => {
                console.error('Error refreshing table:', error);
            });
    }
};

// Function to delete a building
window.deleteBuilding = function(buildingId) {
    if (confirm('Are you sure you want to delete this building? This action cannot be undone.')) {
        fetch(`/block-buildings/${buildingId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Add a message area if not present
            let messageDiv = document.getElementById('buildingDeleteMessage');
            if (!messageDiv) {
                messageDiv = document.createElement('div');
                messageDiv.id = 'buildingDeleteMessage';
                messageDiv.className = 'alert d-none';
                document.body.appendChild(messageDiv);
            }
            if (data.success) {
                messageDiv.className = 'alert alert-success';
                messageDiv.textContent = 'Building deleted successfully!';
                messageDiv.classList.remove('d-none');
                // Refresh the DataTable instead of reloading the page
                refreshBuildingsTable();
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = data.message || 'Error deleting building.';
                messageDiv.classList.remove('d-none');
            }
            // Hide message after 5 seconds
            setTimeout(() => {
                messageDiv.classList.add('d-none');
            }, 5000);
        })
        .catch(error => {
            let messageDiv = document.getElementById('buildingDeleteMessage');
            if (!messageDiv) {
                messageDiv = document.createElement('div');
                messageDiv.id = 'buildingDeleteMessage';
                messageDiv.className = 'alert d-none';
                document.body.appendChild(messageDiv);
            }
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error deleting building';
            messageDiv.classList.remove('d-none');
            setTimeout(() => {
                messageDiv.classList.add('d-none');
            }, 5000);
        });
    }
};
</script>
@endpush

<style>
#building-info-table {
    width: 100% !important;
}
</style>
