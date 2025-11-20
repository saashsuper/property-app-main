<!-- Unit Modal (Add/Edit) -->
<div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="unitModalLabel" style="color: white !important; padding-bottom: 15px;">Add Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="unitForm" method="POST" action="{{ route('block-units.store') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <div class="modal-body">
                    <!-- Message container -->
                    <div id="unitMessage" class="alert d-none" role="alert">
                        <i class="ph-check-circle me-2"></i>
                        <span class="message-text"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="block_building_id" class="form-label">Building/Core <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_building_id" name="block_building_id" required>
                                <option value="">Select Building/Core</option>
                                @foreach($block->buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="block_unit_type_id" class="form-label">Unit Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_unit_type_id" name="block_unit_type_id" required>
                                <option value="">Select Unit Type</option>
                                @foreach(\App\Models\BlockUnitType::orderBy('name')->get() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unit_code" class="form-label">Unit Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit_code" name="unit_code" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unit_name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit_name" name="unit_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="owners_name" class="form-label">Owner's Name</label>
                            <input type="text" class="form-control" id="owners_name" name="owners_name">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="salutation" class="form-label">Salutation <span class="text-danger">*</span></label>
                            <select class="form-select" id="salutation" name="salutation" required>
                                <option value="">Select Salutation</option>
                                @foreach(\App\Models\Salutation::orderBy('name')->get() as $salutation)
                                    <option value="{{ $salutation->name }}">{{ $salutation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="resident" class="form-label">Resident <span class="text-danger">*</span></label>
                            <select class="form-select" id="resident" name="resident" required>
                                <option value="">Select Option</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="mobile_no" class="form-label">Mobile Number</label>
                            <input type="number" class="form-control" id="mobile_no" name="mobile_no" min="0" max="99999999999999999999">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="number" class="form-control" id="phone_number" name="phone_number" min="0" max="99999999999999999999">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="letting_agent" class="form-label">Letting Agent</label>
                            <input type="text" class="form-control" id="letting_agent" name="letting_agent">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="misc_info" class="form-label">Miscellaneous Info</label>
                            <textarea class="form-control" id="misc_info" name="misc_info" rows="2"></textarea>
                        </div>
                        
                        <!-- Address Fields - Shown when Resident = No -->
                        <div class="col-md-4 mb-3" id="address1_field" style="display: none;">
                            <label for="address1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address1" name="address1">
                        </div>
                        <div class="col-md-4 mb-3" id="address2_field" style="display: none;">
                            <label for="address2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="address2" name="address2">
                        </div>
                        <div class="col-md-4 mb-3" id="address3_field" style="display: none;">
                            <label for="address3" class="form-label">Address Line 3</label>
                            <input type="text" class="form-control" id="address3" name="address3">
                        </div>
                        <div class="col-md-4 mb-3" id="country_field" style="display: none;">
                            <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-select" id="country_id" name="country_id" onchange="handleCountryChange(this.value)">
                                <option value="">Select Country</option>
                                @foreach(\App\Models\Country::orderBy('country_name')->get() as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3" id="state_field" style="display: none;">
                            <label for="state_id" class="form-label">County / State <span class="text-danger">*</span></label>
                            <select class="form-select" id="state_id" name="state_id">
                                <option value="">Select County / State</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3" id="zip_field" style="display: none;">
                            <label for="zip" class="form-label">Zip / Eircode</label>
                            <input type="text" class="form-control" id="zip" name="zip">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="unitSubmitBtn">
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

<!-- View Unit Modal -->
<div class="modal fade" id="viewUnitModal" tabindex="-1" aria-labelledby="viewUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="viewUnitModalLabel" style="color: white !important; padding-bottom: 15px;">Unit Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold mb-3">Basic</h6>
                            <div class="mb-2"><strong>Unit Code:</strong> <span id="v_unit_code">-</span></div>
                            <div class="mb-2"><strong>Unit Name:</strong> <span id="v_unit_name">-</span></div>
                            <div class="mb-2"><strong>Type:</strong> <span id="v_unit_type">-</span></div>
                            <div class="mb-2"><strong>Building/Core:</strong> <span id="v_building">-</span></div>
                            <div class="mb-2"><strong>Resident:</strong> <span id="v_resident">-</span></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold mb-3">Owner & Contact</h6>
                            <div class="mb-2"><strong>Owner's Name:</strong> <span id="v_owners_name">-</span></div>
                            <div class="mb-2"><strong>Salutation:</strong> <span id="v_salutation">-</span></div>
                            <div class="mb-2"><strong>Email:</strong> <span id="v_email">-</span></div>
                            <div class="mb-2"><strong>Mobile:</strong> <span id="v_mobile_no">-</span></div>
                            <div class="mb-2"><strong>Phone:</strong> <span id="v_phone_number">-</span></div>
                            <div class="mb-2"><strong>Letting Agent:</strong> <span id="v_letting_agent">-</span></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold mb-3">Address (if Non-Resident)</h6>
                            <div class="mb-2"><strong>Address 1:</strong> <span id="v_address1">-</span></div>
                            <div class="mb-2"><strong>Address 2:</strong> <span id="v_address2">-</span></div>
                            <div class="mb-2"><strong>Address 3:</strong> <span id="v_address3">-</span></div>
                            <div class="mb-2"><strong>Country:</strong> <span id="v_country">-</span></div>
                            <div class="mb-2"><strong>County / State:</strong> <span id="v_state">-</span></div>
                            <div class="mb-2"><strong>Zip / Eircode:</strong> <span id="v_zip">-</span></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3">
                            <h6 class="fw-bold mb-2">Miscellaneous Info</h6>
                            <div id="v_misc_info" class="text-muted">-</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
    </div>

<!-- Upload Unit Modal -->
<div class="modal fade" id="uploadUnitModal" tabindex="-1" aria-labelledby="uploadUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                                <strong>Required columns:</strong> Unit Code, Unit Name, Building/Core, Unit Type, Owner's Name, Salutation, Email, Resident, Mobile Number, Phone Number, Letting Agent, Miscellaneous Info, Address Line 1, Address Line 2, Address Line 3, Zip/EirCode<br>
                                <strong>Note:</strong> Building/Core and Unit Type columns have dropdown lists for easy selection
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="ph-info me-2"></i>Upload Instructions</h6>
                                <ul class="mb-0">
                                    <li>Download the template file to see the required format</li>
                                    <li>Ensure all required fields are filled</li>
                                    <li><strong>Building/Core and Unit Type:</strong> Use the dropdown lists in the Excel template to select valid values</li>
                                    <li>Unit codes must be unique within the block</li>
                                    <li>Resident field should be "Yes" or "No"</li>
                                    <li><strong>Address Logic:</strong> Address fields are only saved when Resident = "No"</li>
                                    <li>For Resident = "Yes": Leave address fields empty (they won't be saved)</li>
                                    <li>For Resident = "No": Fill address fields as needed</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <a href="{{ route('block-units.template', $block->id) }}?v={{ time() }}" 
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-labelledby="deleteUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUnitModalLabel">
                    <i class="ph-warning me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="ph-warning-circle text-danger" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Are you sure you want to delete this unit?</h6>
                        <p class="text-muted mb-0">This action cannot be undone. All unit data will be permanently removed.</p>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <strong>Unit Details:</strong>
                    <div id="deleteUnitDetails" class="mt-2">
                        <!-- Unit details will be populated here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteUnitBtn">
                    <i class="ph-trash me-1"></i> Delete Unit
                </button>
            </div>
        </div>
    </div>
</div>

