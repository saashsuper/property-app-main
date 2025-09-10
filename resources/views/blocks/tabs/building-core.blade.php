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
                    @foreach($block->buildings ?? [] as $building)
                        <tr>
                            <td>{{ $building->buildingType->name ?? 'N/A' }}</td>
                            <td>{{ $building->name ?? 'N/A' }}</td>
                            <td>{{ $building->floor_no ?? 'N/A' }}</td>
                            <td>{{ $building->roof_type ?? 'N/A' }}</td>
                            <td>{{ $building->no_lift ?? 'N/A' }}</td>
                            <td>{{ $building->created_at ? $building->created_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editBuilding({{ $building->id }})">
                                    <i class="ph-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBuilding({{ $building->id }})">
                                    <i class="ph-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

<!-- Add Building Modal -->
<div class="modal fade" id="addBuildingModal" tabindex="-1" aria-labelledby="addBuildingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="addBuildingModalLabel">Add Building</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBuildingForm" method="POST" action="{{ route('block-buildings.store') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div id="addBuildingMessage" class="alert d-none" role="alert"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="building_type_id" class="form-label">Building Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="building_type_id" name="building_type_id" required>
                                    <option value="">Select Building Type</option>
                                    @foreach(\App\Models\BuildingType::all() as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="building_name" class="form-label">Building Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="building_name" name="name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_of_floors" class="form-label">No of Floors</label>
                                <input type="number" class="form-control" id="no_of_floors" name="floor_no" min="1" max="999">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="roof_type" class="form-label">Roof Type</label>
                                <input type="text" class="form-control" id="roof_type" name="roof_type">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_lift" class="form-label">No of Lifts</label>
                                <input type="number" class="form-control" id="no_lift" name="no_lift" min="0" max="999">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Building</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let buildingDataTable;

document.addEventListener('DOMContentLoaded', function() {
    // Only initialize DataTable when building tab is active
    const buildingTab = document.getElementById('building-core-tab');
    if (buildingTab) {
        buildingTab.addEventListener('shown.bs.tab', function() {
            if (window.jQuery && $('#building-info-table').length && !buildingDataTable) {
                buildingDataTable = $('#building-info-table').DataTable({
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
        });
    }
});

// Global functions for building operations
function editBuilding(id) {
    // Edit building functionality
    console.log('Edit building:', id);
}

function deleteBuilding(id) {
    // Delete building functionality
    console.log('Delete building:', id);
}
</script>
@endpush

<style>
#building-info-table {
    width: 100% !important;
}

/* Ensure building tab content doesn't interfere with sidebar */
.tab-pane#building-core {
    position: relative;
    z-index: 1;
}

.tab-pane#building-core .modal {
    z-index: 1055;
}

.tab-pane#building-core .modal-backdrop {
    z-index: 1050;
}
</style>
