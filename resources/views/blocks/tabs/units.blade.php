<!-- Units Header -->
<div class="d-flex align-items-center mb-3 gap-3">
    <h6 class="mb-0 fw-bold text-white px-3 py-2 rounded flex-grow-1 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 38px;">Units</h6>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
        <i class="ph-plus align-bottom me-1"></i> Add Unit
    </button>
</div>

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
                        
                        <!-- Address Fields - Shown by default, hidden when Resident = Yes -->
                        <div id="addressFields" class="row" style="display: block !important; visibility: visible !important; background-color: #f8f9fa; border: 2px solid #007bff; padding: 15px; margin: 10px 0;">
                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-3">Address Information</h6>
                                <p class="text-muted">This section should be visible when the modal opens</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address1" name="address1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address2" class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" id="address2" name="address2">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address3" class="form-label">Address Line 3</label>
                                <input type="text" class="form-control" id="address3" name="address3">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                                <select class="form-select" id="country_id" name="country_id">
                                    <option value="">Select Country</option>
                                    @foreach(\App\Models\Country::orderBy('country_name')->get() as $country)
                                        <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="state_id" class="form-label">County / State <span class="text-danger">*</span></label>
                                <select class="form-select" id="state_id" name="state_id">
                                    <option value="">Select County / State</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="zip" class="form-label">Zip / Eircode</label>
                                <input type="text" class="form-control" id="zip" name="zip">
                            </div>
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
                        
                        <!-- Address Fields - Shown by default, hidden when Resident = Yes -->
                        <div id="editAddressFields" class="row" style="display: block !important; visibility: visible !important;">
                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-3">Address Information</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_address1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_address1" name="address1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_address2" class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" id="edit_address2" name="address2">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_address3" class="form-label">Address Line 3</label>
                                <input type="text" class="form-control" id="edit_address3" name="address3">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_country_id" class="form-label">Country <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_country_id" name="country_id">
                                    <option value="">Select Country</option>
                                    @foreach(\App\Models\Country::orderBy('country_name')->get() as $country)
                                        <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_state_id" class="form-label">County / State <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_state_id" name="state_id">
                                    <option value="">Select County / State</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_zip" class="form-label">Zip / Eircode</label>
                                <input type="text" class="form-control" id="edit_zip" name="zip">
                            </div>
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
    // Function to toggle address fields based on resident selection
    function toggleAddressFields(residentSelectId, addressFieldsId) {
        const residentSelect = document.getElementById(residentSelectId);
        const addressFields = document.getElementById(addressFieldsId);
        
        if (residentSelect && addressFields) {
            console.log('Address fields toggle initialized for:', residentSelectId, addressFieldsId);
            residentSelect.addEventListener('change', function() {
                console.log('Resident changed to:', this.value);
                if (this.value === '0') { // No
                    console.log('Showing address fields');
                    addressFields.style.display = 'block';
                    // Make required fields required
                    const address1Field = addressFields.querySelector('#address1, #edit_address1');
                    const countryField = addressFields.querySelector('#country_id, #edit_country_id');
                    const stateField = addressFields.querySelector('#state_id, #edit_state_id');
                    
                    if (address1Field) address1Field.setAttribute('required', 'required');
                    if (countryField) countryField.setAttribute('required', 'required');
                    if (stateField) stateField.setAttribute('required', 'required');
                } else { // Yes
                    console.log('Hiding address fields');
                    addressFields.style.display = 'none';
                    // Clear address fields and remove required attribute
                    const addressInputs = addressFields.querySelectorAll('input, select');
                    addressInputs.forEach(field => {
                        field.value = '';
                        field.removeAttribute('required');
                    });
                }
            });
        } else {
            console.error('Could not find elements:', { residentSelectId, addressFieldsId, residentSelect, addressFields });
        }
    }

    // Function to load states based on country selection
    function loadStates(countrySelectId, stateSelectId) {
        const countrySelect = document.getElementById(countrySelectId);
        const stateSelect = document.getElementById(stateSelectId);
        
        if (countrySelect && stateSelect) {
            countrySelect.addEventListener('change', function() {
                const countryId = this.value;
                console.log('Country changed to:', countryId);
                
                // Reset state selection
                stateSelect.innerHTML = '<option value="">Select County / State</option>';
                stateSelect.value = '';
                
                if (countryId) {
                    console.log('Loading states for country:', countryId);
                    fetch(`/api/states/${countryId}`)
                        .then(response => {
                            console.log('States API response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('States API response data:', data);
                            if (Array.isArray(data) && data.length > 0) {
                                data.forEach(state => {
                                    const option = document.createElement('option');
                                    option.value = state.id;
                                    option.textContent = state.name;
                                    stateSelect.appendChild(option);
                                });
                                console.log('Loaded', data.length, 'states');
                            } else {
                                console.error('No states found or API error:', data);
                            }
                        })
                        .catch(error => {
                            console.error('Error loading states:', error);
                        });
                } else {
                    console.log('No country selected, states cleared');
                }
            });
        } else {
            console.error('Could not find country or state select elements:', { countrySelectId, stateSelectId });
        }
    }

    // Initialize address field toggles
    console.log('Initializing address field toggles...');
    console.log('Looking for resident element:', document.getElementById('resident'));
    console.log('Looking for addressFields element:', document.getElementById('addressFields'));
    console.log('Looking for edit_resident element:', document.getElementById('edit_resident'));
    console.log('Looking for editAddressFields element:', document.getElementById('editAddressFields'));
    
    toggleAddressFields('resident', 'addressFields');
    toggleAddressFields('edit_resident', 'editAddressFields');
    
    // Initialize state loading
    loadStates('country_id', 'state_id');
    loadStates('edit_country_id', 'edit_state_id');

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
                    // Reload the page to show the new unit
                    location.reload();
                }, 1500);
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
                    // Reload the page to show the updated unit
                    location.reload();
                }, 1500);
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
    const addUnitModal = document.getElementById('addUnitModal');
    if (addUnitModal) {
        addUnitModal.addEventListener('hidden.bs.modal', function() {
            // Clear any error messages and reset form
            const messageDiv = document.getElementById('addUnitMessage');
            if (messageDiv) {
                messageDiv.classList.add('d-none');
            }
            const form = document.getElementById('addUnitForm');
            if (form) {
                form.reset();
            }
        });
    }
    
    // Add event listener for modal show events
    if (addUnitModal) {
        addUnitModal.addEventListener('shown.bs.modal', function() {
            console.log('Add Unit modal shown');
            
            // Initialize address fields
            const addressFields = document.getElementById('addressFields');
            if (addressFields) {
                addressFields.style.display = 'block';
            }
            
            // Reinitialize address field toggle
            toggleAddressFields('resident', 'addressFields');
            
            // Reinitialize state loading
            loadStates('country_id', 'state_id');
        });
    }

    const editUnitModal = document.getElementById('editUnitModal');
    if (editUnitModal) {
        editUnitModal.addEventListener('hidden.bs.modal', function() {
            // Clear any error messages and reset form
            const messageDiv = document.getElementById('editUnitMessage');
            if (messageDiv) {
                messageDiv.classList.add('d-none');
            }
        });
    }
    
    // Add event listener for edit modal show events
    document.getElementById('editUnitModal').addEventListener('shown.bs.modal', function() {
        console.log('Edit Unit modal shown, reinitializing address fields...');
        
        // Show address fields by default when modal opens
        const editAddressFields = document.getElementById('editAddressFields');
        if (editAddressFields) {
            editAddressFields.style.display = 'block';
            console.log('Edit address fields shown by default');
        }
        
        // Reinitialize address field toggle for the modal
        toggleAddressFields('edit_resident', 'editAddressFields');
        
        // Check current resident value and adjust accordingly
        const editResidentSelect = document.getElementById('edit_resident');
        if (editResidentSelect) {
            console.log('Edit resident select value in modal:', editResidentSelect.value);
            if (editResidentSelect.value === '1') {
                console.log('Edit resident is Yes, hiding address fields');
                if (editAddressFields) {
                    editAddressFields.style.display = 'none';
                }
            }
        }
    });

    // Initialize DataTable
    let blockUnitsDataTable;
    if (window.jQuery && $('#blockUnitsTable').length) {
        blockUnitsDataTable = $('#blockUnitsTable').DataTable({
            responsive: true,
            dom: 'rtip', // Removed 'f' (filter/search) to remove the search box on the left
            order: [[0, 'asc']], // default sort by Unit Code
            columnDefs: [
                { targets: [10], orderable: false } // Actions (last column)
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
                        
                        // Add new data with correct column structure
                        data.data.forEach(function(unit) {
                            blockUnitsDataTable.row.add([
                                unit.unit_code || 'N/A',
                                unit.unit_name || 'N/A',
                                unit.block_unit_type ? unit.block_unit_type.name : 'N/A',
                                unit.owners_name || 'N/A',
                                unit.salutation || 'N/A',
                                unit.email || 'N/A',
                                unit.resident ? 'Yes' : 'No',
                                unit.mobile_no || 'N/A',
                                unit.phone_number || 'N/A',
                                unit.letting_agent || 'N/A',
                                unit.misc_info || 'N/A'
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
    
    // Test address fields toggle on page load
    setTimeout(() => {
        console.log('Testing address fields toggle...');
        const residentSelect = document.getElementById('resident');
        if (residentSelect) {
            console.log('Found resident select, current value:', residentSelect.value);
            // Trigger change event to test
            residentSelect.dispatchEvent(new Event('change'));
        }
    }, 1000);
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
                
                // Handle address fields
                if (u.resident === 0) { // No
                    document.getElementById('editAddressFields').style.display = 'block';
                    document.getElementById('edit_address1').value = u.address1 || '';
                    document.getElementById('edit_address2').value = u.address2 || '';
                    document.getElementById('edit_address3').value = u.address3 || '';
                    document.getElementById('edit_country_id').value = u.country_id || '';
                    document.getElementById('edit_zip').value = u.zip || '';
                    
                    // Load states for the selected country
                    if (u.country_id) {
                        loadStatesForEdit(u.country_id, u.state_id);
                    }
                } else {
                    document.getElementById('editAddressFields').style.display = 'none';
                }
                
                document.getElementById('editUnitForm').action = `/block-units/${id}`;
                const modal = new bootstrap.Modal(document.getElementById('editUnitModal'));
                modal.show();
            } else {
                alert('Error loading unit details.');
            }
        });
}

// Function to load states for edit form
function loadStatesForEdit(countryId, selectedStateId) {
    console.log('Loading states for edit form:', { countryId, selectedStateId });
    const stateSelect = document.getElementById('edit_state_id');
    stateSelect.innerHTML = '<option value="">Select County / State</option>';
    stateSelect.value = '';
    
    if (countryId) {
        fetch(`/api/states/${countryId}`)
            .then(response => {
                console.log('Edit states API response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Edit states API response data:', data);
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(state => {
                        const option = document.createElement('option');
                        option.value = state.id;
                        option.textContent = state.name;
                        if (selectedStateId && state.id == selectedStateId) {
                            option.selected = true;
                            stateSelect.value = state.id;
                        }
                        stateSelect.appendChild(option);
                    });
                    console.log('Loaded', data.length, 'states for edit');
                } else {
                    console.error('No states found for edit or API error:', data);
                }
            })
            .catch(error => {
                console.error('Error loading states for edit:', error);
            });
    }
}

</script>
