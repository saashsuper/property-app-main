<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3 gap-3">
            <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Unit Information</h6>
            <div class="d-flex align-items-center gap-2">
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('export.pdf', 'block-units') }}?block_id={{ $block->id }}" 
                       class="btn btn-outline-danger btn-sm" title="Export to PDF">
                        <i class="ph-file-pdf"></i>
                    </a>
                    <a href="{{ route('export.excel', 'block-units') }}?block_id={{ $block->id }}" 
                       class="btn btn-outline-success btn-sm" title="Export to Excel">
                        <i class="ph-file-xls"></i>
                    </a>
                    <a href="{{ route('export.print', 'block-units') }}?block_id={{ $block->id }}" 
                       class="btn btn-outline-secondary btn-sm" title="Print" target="_blank">
                        <i class="ph-printer"></i>
                    </a>
                </div>
                <button class="btn btn-primary custom-toggle active" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                    <i class="ph-plus align-bottom me-1"></i> Add Unit
                </button>
                <button class="btn btn-success custom-toggle" data-bs-toggle="modal" data-bs-target="#uploadUnitModal">
                    <i class="ph-upload align-bottom me-1"></i> Upload Unit
                </button>
            </div>
        </div>
        
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($block->units && $block->units->count() > 0)
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
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editUnit({{ $unit->id }})">Edit</button>
                                    <form action="{{ route('block-units.destroy', $unit->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this unit?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="addUnitModalLabel" style="color: white !important; padding-bottom: 15px;">Add Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
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
                        
                        <!-- Address Fields - Shown when Resident = No -->
                        <div class="col-md-6 mb-3" id="address1_field" style="display: none;">
                            <label for="address1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address1" name="address1">
                        </div>
                        <div class="col-md-6 mb-3" id="address2_field" style="display: none;">
                            <label for="address2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="address2" name="address2">
                        </div>
                        <div class="col-md-6 mb-3" id="address3_field" style="display: none;">
                            <label for="address3" class="form-label">Address Line 3</label>
                            <input type="text" class="form-control" id="address3" name="address3">
                        </div>
                        <div class="col-md-6 mb-3" id="country_field" style="display: none;">
                            <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-select" id="country_id" name="country_id">
                                <option value="">Select Country</option>
                                @foreach(\App\Models\Country::orderBy('country_name')->get() as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="state_field" style="display: none;">
                            <label for="state_id" class="form-label">County / State <span class="text-danger">*</span></label>
                            <select class="form-select" id="state_id" name="state_id">
                                <option value="">Select County / State</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="zip_field" style="display: none;">
                            <label for="zip" class="form-label">Zip / Eircode</label>
                            <input type="text" class="form-control" id="zip" name="zip">
                        </div>
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

<!-- Edit Unit Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="editUnitModalLabel" style="color: white !important; padding-bottom: 15px;">Edit Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
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
                        
                        <!-- Address Fields - Shown when Resident = No -->
                        <div class="col-md-6 mb-3" id="edit_address1_field" style="display: none;">
                            <label for="edit_address1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_address1" name="address1">
                        </div>
                        <div class="col-md-6 mb-3" id="edit_address2_field" style="display: none;">
                            <label for="edit_address2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="edit_address2" name="address2">
                        </div>
                        <div class="col-md-6 mb-3" id="edit_address3_field" style="display: none;">
                            <label for="edit_address3" class="form-label">Address Line 3</label>
                            <input type="text" class="form-control" id="edit_address3" name="address3">
                        </div>
                        <div class="col-md-6 mb-3" id="edit_country_field" style="display: none;">
                            <label for="edit_country_id" class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_country_id" name="country_id">
                                <option value="">Select Country</option>
                                @foreach(\App\Models\Country::orderBy('country_name')->get() as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="edit_state_field" style="display: none;">
                            <label for="edit_state_id" class="form-label">County / State <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_state_id" name="state_id">
                                <option value="">Select County / State</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="edit_zip_field" style="display: none;">
                            <label for="edit_zip" class="form-label">Zip / Eircode</label>
                            <input type="text" class="form-control" id="edit_zip" name="zip">
                        </div>
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

<!-- Upload Unit Modal -->
<div class="modal fade" id="uploadUnitModal" tabindex="-1" aria-labelledby="uploadUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="uploadUnitModalLabel" style="color: white !important; padding-bottom: 15px;">Upload Units</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            
            <!-- Message Container -->
            <div id="uploadMessageContainer" style="display: none;">
                <div id="uploadSuccess" class="alert alert-success mx-3 mt-3" style="display: none;">
                    <h6 class="alert-heading"><i class="ph-check-circle me-2"></i>Upload Successful</h6>
                    <div id="successMessage"></div>
                </div>
            </div>
            
            <form id="uploadUnitForm" method="POST" action="{{ route('block-units.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="unit_file" class="form-label">Upload File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="unit_file" name="unit_file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">
                                <strong>Supported formats:</strong> Excel (.xlsx, .xls) or CSV (.csv)<br>
                                <strong>Maximum file size:</strong> 5MB<br>
                                <strong>Required columns:</strong> Unit Code, Unit Name, Owner's Name, Salutation, Email, Resident, Mobile Number, Phone Number, Letting Agent, Miscellaneous Info
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="ph-info me-2"></i>Upload Instructions</h6>
                                <ul class="mb-0">
                                    <li>Download the template file to see the required format</li>
                                    <li>Ensure all required fields are filled</li>
                                    <li>Unit codes must be unique within the block</li>
                                    <li>Resident field should be "Yes" or "No"</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <a href="{{ asset('storage/templates/unit-upload-template.xlsx') }}" 
                               download="unit_upload_template_{{ str_replace(' ', '_', $block->name) }}_{{ date('Y-m-d') }}.xlsx"
                               class="btn btn-outline-primary">
                                <i class="ph-download me-1"></i> Download Template
                            </a>
                        </div>
                        <div class="col-12 mb-3" id="uploadErrors" style="display: none;">
                            <div class="alert alert-danger">
                                <h6 class="alert-heading"><i class="ph-warning me-2"></i>Import Errors</h6>
                                <div id="errorList"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i> Upload
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
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
<script>
$(document).ready(function() {
    $('#blockUnitsTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        ],
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No Unit Informations found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
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
</script>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to toggle address fields based on resident selection
    function toggleAddressFields(residentSelectId, isEdit = false) {
        const residentSelect = document.getElementById(residentSelectId);
        
        if (residentSelect) {
            const prefix = isEdit ? 'edit_' : '';
            const addressFields = [
                `${prefix}address1_field`,
                `${prefix}address2_field`, 
                `${prefix}address3_field`,
                `${prefix}country_field`,
                `${prefix}state_field`,
                `${prefix}zip_field`
            ];
            
            residentSelect.addEventListener('change', function() {
                console.log('Resident changed to:', this.value);
                if (this.value === '0') {
                    console.log('Showing address fields:', addressFields);
                    // Show all address fields
                    addressFields.forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        console.log('Looking for field:', fieldId, 'Found:', field);
                        if (field) field.style.display = 'block';
                    });
                    
                    // Set required attributes for required fields
                    const address1Field = document.getElementById(`${prefix}address1`);
                    const countryField = document.getElementById(`${prefix}country_id`);
                    const stateField = document.getElementById(`${prefix}state_id`);
                    
                    if (address1Field) address1Field.setAttribute('required', 'required');
                    if (countryField) countryField.setAttribute('required', 'required');
                    if (stateField) stateField.setAttribute('required', 'required');
                } else {
                    // Hide all address fields
                    addressFields.forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        if (field) field.style.display = 'none';
                    });
                    
                    // Remove required attributes
                    addressFields.forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        if (field) {
                            const inputs = field.querySelectorAll('input, select');
                            inputs.forEach(input => input.removeAttribute('required'));
                        }
                    });
                }
            });
        }
    }
    
    // Initialize address fields toggle for Add Unit modal
    toggleAddressFields('resident', false);
    
    // Initialize address fields toggle for Edit Unit modal
    toggleAddressFields('edit_resident', true);
    
    // Function to load states based on country selection
    function loadStates(countryId, stateSelectId) {
        const stateSelect = document.getElementById(stateSelectId);
        if (countryId && stateSelect) {
            fetch(`/api/states/${countryId}`)
                .then(response => response.json())
                .then(data => {
                    stateSelect.innerHTML = '<option value="">Select County / State</option>';
                    if (data.success && data.states) {
                        data.states.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.id;
                            option.textContent = state.name;
                            stateSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading states:', error);
                });
        }
    }
    
    // Add country change listeners for both modals
    document.getElementById('country_id').addEventListener('change', function() {
        loadStates(this.value, 'state_id');
    });
    
    document.getElementById('edit_country_id').addEventListener('change', function() {
        loadStates(this.value, 'edit_state_id');
    });
    
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
                    // Refresh the units table to show the new unit
                    refreshBlockUnitsTable();
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
                    // Refresh the units table to show the updated unit
                    refreshBlockUnitsTable();
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
                
                // Populate address fields
                document.getElementById('edit_address1').value = u.address1 || '';
                document.getElementById('edit_address2').value = u.address2 || '';
                document.getElementById('edit_address3').value = u.address3 || '';
                document.getElementById('edit_country_id').value = u.country_id || '';
                document.getElementById('edit_state_id').value = u.state_id || '';
                document.getElementById('edit_zip').value = u.zip || '';
                
                // Show/hide address fields based on resident status
                const addressFields = [
                    'edit_address1_field',
                    'edit_address2_field', 
                    'edit_address3_field',
                    'edit_country_field',
                    'edit_state_field',
                    'edit_zip_field'
                ];
                
                if (u.resident === 0) {
                    // Show all address fields
                    addressFields.forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        if (field) field.style.display = 'block';
                    });
                    
                    // Set required attributes for required fields
                    const address1Field = document.getElementById('edit_address1');
                    const countryField = document.getElementById('edit_country_id');
                    const stateField = document.getElementById('edit_state_id');
                    
                    if (address1Field) address1Field.setAttribute('required', 'required');
                    if (countryField) countryField.setAttribute('required', 'required');
                    if (stateField) stateField.setAttribute('required', 'required');
                } else {
                    // Hide all address fields
                    addressFields.forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        if (field) field.style.display = 'none';
                    });
                    
                    // Remove required attributes
                    addressFields.forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        if (field) {
                            const inputs = field.querySelectorAll('input, select');
                            inputs.forEach(input => input.removeAttribute('required'));
                        }
                    });
                }
                
                document.getElementById('editUnitForm').action = `/block-units/${id}`;
                const modal = new bootstrap.Modal(document.getElementById('editUnitModal'));
                modal.show();
            } else {
                alert('Error loading unit details.');
            }
        });
}



// Handle upload form submission
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadUnitForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(uploadForm);
            const fileInput = document.getElementById('unit_file');
            
            if (!fileInput.files[0]) {
                // Show error in modal instead of alert
                let errorDiv = document.getElementById('uploadErrors');
                const errorList = document.getElementById('errorList');
                errorList.innerHTML = 'Please select a file to upload.';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Hide any previous messages
            const messageContainer = document.getElementById('uploadMessageContainer');
            const successDiv = document.getElementById('uploadSuccess');
            if (messageContainer) {
                messageContainer.style.display = 'none';
            }
            if (successDiv) {
                successDiv.style.display = 'none';
            }
            document.getElementById('uploadErrors').style.display = 'none';
            
            // Show loading state
            const submitBtn = uploadForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ph-spinner ph-spin me-1"></i> Uploading...';
            submitBtn.disabled = true;
            
            fetch(uploadForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide any previous errors
                    document.getElementById('uploadErrors').style.display = 'none';
                    
                    // Show success message in modal
                    const messageContainer = document.getElementById('uploadMessageContainer');
                    const successDiv = document.getElementById('uploadSuccess');
                    const successMessage = document.getElementById('successMessage');
                    
                    successMessage.textContent = data.message;
                    messageContainer.style.display = 'block';
                    successDiv.style.display = 'block';
                    
                    // Show errors if any
                    if (data.errors && data.errors.length > 0) {
                        const errorList = document.getElementById('errorList');
                        errorList.innerHTML = '<ul class="mb-0">' + data.errors.map(error => '<li>' + error + '</li>').join('') + '</ul>';
                        document.getElementById('uploadErrors').style.display = 'block';
                    }
                    
                    // Reset form
                    uploadForm.reset();
                    
                    // Close modal and redirect to Units tab after 3 seconds
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('uploadUnitModal'));
                        if (modal) modal.hide();
                        
                        // Wait for modal to close, then navigate to Units tab
                        setTimeout(() => {
                            // Find the Units tab using the correct selector
                            let unitsTab = document.querySelector('#units-tab') ||
                                          document.querySelector('a[href="#units"]') || 
                                          document.querySelector('[aria-controls="units"]');
                            
                            if (unitsTab) {
                                console.log('Found units tab:', unitsTab);
                                unitsTab.click();
                                
                                // Refresh the units table to show new units
                                refreshBlockUnitsTable();
                            } else {
                                console.log('Units tab not found, just reloading page');
                                // Refresh the units table
                                refreshBlockUnitsTable();
                            }
                        }, 300);
                    }, 3000);
                } else {
                    // Show error message in modal
                    let errorDiv = document.getElementById('uploadErrors');
                    const errorList = document.getElementById('errorList');
                    errorList.innerHTML = data.message || 'Error uploading units.';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Show error message in modal
                let errorDiv = document.getElementById('uploadErrors');
                const errorList = document.getElementById('errorList');
                errorList.innerHTML = 'An error occurred while uploading. Please try again.';
                errorDiv.style.display = 'block';
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Clear messages when upload modal is opened
    const uploadModal = document.getElementById('uploadUnitModal');
    if (uploadModal) {
        uploadModal.addEventListener('show.bs.modal', function() {
            // Hide any previous messages
            const messageContainer = document.getElementById('uploadMessageContainer');
            const successDiv = document.getElementById('uploadSuccess');
            if (messageContainer) {
                messageContainer.style.display = 'none';
            }
            if (successDiv) {
                successDiv.style.display = 'none';
            }
            const errorDiv = document.getElementById('uploadErrors');
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
        });
    }
});

// Function to refresh the units table
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
                            unit.unit_code || 'N/A',
                            unit.unit_name || 'N/A',
                            unit.block_unit_type?.name || 'N/A',
                            unit.owners_name || 'N/A',
                            unit.salutation || 'N/A',
                            unit.email || 'N/A',
                            unit.resident ? 'Yes' : 'No',
                            unit.mobile_no || 'N/A',
                            unit.phone_number || 'N/A',
                            unit.letting_agent || 'N/A',
                            unit.misc_info || 'N/A',
                            '<button class="btn btn-sm btn-outline-primary" onclick="editUnit(' + unit.id + ')">' +
                                '<i class="ph-pencil"></i> Edit' +
                            '</button> ' +
                            '<form action="/block-units/' + unit.id + '" method="POST" class="d-inline-block" onsubmit="return confirm(\'Are you sure you want to delete this unit?\');">' +
                                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                                '<input type="hidden" name="_method" value="DELETE">' +
                                '<button type="submit" class="btn btn-sm btn-outline-danger">' +
                                    '<i class="ph-trash"></i> Delete' +
                                '</button>' +
                            '</form>'
                        ]);
                    });
                    
                    // Redraw the table
                    blockUnitsDataTable.draw();
                    console.log('Units table refreshed with', data.data.length, 'units');
                } else {
                    console.error('Error refreshing units table:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching units data:', error);
            });
    }
};

// Global variable to store DataTable instance
let blockUnitsDataTable;

// DataTables for Units
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
</script>
