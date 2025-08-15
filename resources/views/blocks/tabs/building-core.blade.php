<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Building Information</h6>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBuildingModal">
                <i class="ri-add-line align-bottom me-1"></i> Add Building
            </button>
        </div>
        
        @if($block->buildings && $block->buildings->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Building Name</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($block->buildings as $building)
                            <tr>
                                <td>{{ $building->name ?? 'N/A' }}</td>
                                <td>{{ $building->buildingType->name ?? 'N/A' }}</td>
                                <td>{{ $building->floor_no ?? 'N/A' }}</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editBuilding({{ $building->id }})">Edit</button>
                                    <form action="{{ route('block-buildings.destroy', $building->id) }}" method="POST" class="d-inline-block" onsubmit="saveActiveTab(); return confirm('Are you sure you want to delete this building?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="ri-building-line text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No buildings found for this block.</p>
                <button class="btn btn-primary">Add First Building</button>
            </div>
        @endif
    </div>
</div>

<!-- Add Building Modal -->
<div class="modal fade" id="addBuildingModal" tabindex="-1" aria-labelledby="addBuildingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBuildingModalLabel">Add Building</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <input type="number" class="form-control" id="no_of_floors" name="no_of_floors" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="roof_type" class="form-label">Roof Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="roof_type" name="roof_type" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_lift" class="form-label">No of Lifts <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="no_lift" name="no_lift" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Building</button>
                </div>
            </form>
        </div>
    </div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
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
                    // Save the active tab to localStorage before reload
                    var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                    if (activeTab) {
                        localStorage.setItem('activeBlockTab', activeTab.getAttribute('href'));
                    }
                    setTimeout(() => { location.reload(); }, 400);
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
                    // Save the active tab to localStorage before reload
                    var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
                    if (activeTab) {
                        localStorage.setItem('activeBlockTab', activeTab.getAttribute('href'));
                    }
                    setTimeout(() => { location.reload(); }, 400);
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
function saveActiveTab() {
    var activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
    if (activeTab) {
        localStorage.setItem('activeBlockTab', activeTab.getAttribute('href'));
    }
}
</script>
