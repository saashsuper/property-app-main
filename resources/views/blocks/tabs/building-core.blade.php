        <div class="table-responsive w-100">
            <table class="table table-bordered table-hover w-100" id="building-info-table">
                <thead class="table-light">
                    <tr>
                        <th>Building Type</th>
                        <th>Building Name</th>
                        <th>No of Floors</th>
                        <th>Roof Type</th>
                        <th>No of Lifts</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTable will populate this -->
                </tbody>
            </table>
        </div>

<!-- Edit Building Modal -->
<div class="modal fade" id="editBuildingModal" tabindex="-1" aria-labelledby="editBuildingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBuildingModalLabel">Edit Building</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBuildingForm" method="POST">
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
                        <input type="number" class="form-control" id="edit_no_of_floors" name="no_of_floors" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="edit_roof_type" class="form-label">Roof Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_roof_type" name="roof_type" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_no_lift" class="form-label">No of Lifts <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_no_lift" name="no_lift" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Building</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- JavaScript moved to @push('scripts') section below -->
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
        language: {
            search: "Search buildings:",
            lengthMenu: "Show _MENU_ buildings per page",
            info: "Showing _START_ to _END_ of _TOTAL_ buildings",
            infoEmpty: "Showing 0 to 0 of 0 buildings",
            infoFiltered: "(filtered from _MAX_ total buildings)",
            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
        }
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.className = 'alert alert-success';
                messageDiv.textContent = 'Building updated successfully!';
                messageDiv.classList.remove('d-none');
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editBuildingModal'));
                    if (modal) modal.hide();
                    // DataTable will refresh automatically when modal closes
                }, 800);
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = data.message || 'Error updating building.';
                messageDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error updating building';
            messageDiv.classList.remove('d-none');
        });
    });

    // Add event listeners for modal close events
    document.getElementById('editBuildingModal').addEventListener('hidden.bs.modal', function() {
        // Refresh the DataTable when modal is closed (with small delay to ensure modal is fully closed)
        setTimeout(() => {
            refreshBuildingsTable();
        }, 100);
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
                        if (data.data && data.data.length > 0) {
                            data.data.forEach(function(building) {
                                buildingDataTable.row.add([
                                    building.building_type_name || 'N/A',
                                    building.name || '',
                                    building.floor_no || '',
                                    building.roof_type || '',
                                    building.no_lift || '',
                                    building.created_at ? new Date(building.created_at).toLocaleDateString('en-US', { 
                                        year: 'numeric', 
                                        month: 'short', 
                                        day: '2-digit' 
                                    }) : 'N/A',
                                    '<button class="btn btn-sm btn-outline-primary" onclick="editBuilding(' + building.id + ')">' +
                                        '<i class="ph-pencil"></i> Edit' +
                                    '</button> ' +
                                    '<button class="btn btn-sm btn-outline-danger" onclick="deleteBuilding(' + building.id + ')">' +
                                        '<i class="ph-trash"></i> Delete' +
                                    '</button>'
                                ]);
                            });
                        }
                        
                        // Redraw the table
                        buildingDataTable.draw();
                    } else {
                        console.error('API Error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error refreshing buildings table:', error);
                });
        }
    };

    // Initial data load after function is defined
    refreshBuildingsTable();

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
                console.error('Error:', error);
                // Add a message area if not present
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
                // Hide message after 5 seconds
                setTimeout(() => {
                    messageDiv.classList.add('d-none');
                }, 5000);
            });
        }
    };

    // Function to edit a building
    window.editBuilding = function(id) {
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
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading building details');
            });
    };
});
</script>
@endpush

<style>
#building-info-table {
    width: 100% !important;
}
</style>
