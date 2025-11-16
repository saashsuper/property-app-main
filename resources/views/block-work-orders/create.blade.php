@extends('layouts.master')
@section('title')
    Create Block Work Order - PROMAN
@endsection
@section('css')
    <style>
    .autoComplete_wrapper {
        position: relative;
    }
    .autoComplete_wrapper > ul {
        z-index: 2050 !important; /* above modals/dropdowns */
        max-height: 240px;
        overflow-y: auto;
    }
    .autoComplete_wrapper > ul > li mark {
        background-color: #fff3cd;
        color: #495057;
        padding: 0;
    }
    </style>
@endsection
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Create Block Work Order</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('block-work-orders.index') }}">Block Work Orders</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create New Block Work Order</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('block-work-orders.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Basic Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="block_id" class="form-label">Block <span class="text-danger">*</span></label>
                                        <select class="form-select @error('block_id') is-invalid @enderror" id="block_id" name="block_id" required>
                                            <option value="">Select Block</option>
                                            @foreach($blocks as $block)
                                                <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                                    {{ $block->name }} - {{ $block->blockType->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('block_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="block_issue_id" class="form-label">Block Issue <span class="text-danger">*</span></label>
                                        <select class="form-select @error('block_issue_id') is-invalid @enderror" id="block_issue_id" name="block_issue_id" required>
                                            <option value="">Select Block Issue</option>
                                            @foreach($blockIssues as $issue)
                                                <option value="{{ $issue->id }}" {{ old('block_issue_id') == $issue->id ? 'selected' : '' }}>
                                                    {{ $issue->ref_no }} - {{ $issue->block->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('block_issue_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="mb-3">
                                        <label for="priority_id" class="form-label">Priority <span class="text-danger">*</span></label>
                                        <select class="form-select @error('priority_id') is-invalid @enderror" id="priority_id" name="priority_id" required>
                                            <option value="">Select Priority</option>
                                            <option value="1" {{ old('priority_id') == '1' ? 'selected' : '' }}>Low</option>
                                            <option value="2" {{ old('priority_id') == '2' ? 'selected' : '' }}>Normal</option>
                                            <option value="3" {{ old('priority_id') == '3' ? 'selected' : '' }}>High</option>
                                            <option value="4" {{ old('priority_id') == '4' ? 'selected' : '' }}>Urgent</option>
                                            <option value="5" {{ old('priority_id') == '5' ? 'selected' : '' }}>Critical</option>
                                        </select>
                                        @error('priority_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                                <!-- Location Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Location Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="block_unit_search" class="form-label">Block Unit <span class="text-danger">*</span></label>
                                        <input type="text" id="block_unit_search" class="form-control" placeholder="Search for units..." autocomplete="off">
                                        <input type="hidden" id="block_unit_id" name="block_unit_id" value="{{ old('block_unit_id') }}" required>
                                        @error('block_unit_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Start typing unit code or name to search within the selected block.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="block_building_id" class="form-label">Block Building</label>
                                        <select class="form-select @error('block_building_id') is-invalid @enderror" id="block_building_id" name="block_building_id">
                                            <option value="">Select Building (Optional)</option>
                                            @foreach($blockBuildings as $building)
                                                <option value="{{ $building->id }}" {{ old('block_building_id') == $building->id ? 'selected' : '' }}>
                                                    {{ $building->name }} - {{ $building->block->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('block_building_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="contractor_id" class="form-label">Contractor ID</label>
                                        <input type="number" class="form-control @error('contractor_id') is-invalid @enderror" 
                                               id="contractor_id" name="contractor_id" value="{{ old('contractor_id') }}">
                                        @error('contractor_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="repair_category_id" class="form-label">Repair Category ID</label>
                                        <input type="number" class="form-control @error('repair_category_id') is-invalid @enderror" 
                                               id="repair_category_id" name="repair_category_id" value="{{ old('repair_category_id') }}">
                                        @error('repair_category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Issue Description -->
                            <div class="mb-3">
                                <label for="issue" class="form-label">Issue Description</label>
                                <textarea class="form-control @error('issue') is-invalid @enderror" 
                                          id="issue" name="issue" rows="3">{{ old('issue') }}</textarea>
                                @error('issue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Schedule Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Schedule</h5>
                                    
                                    <div class="mb-3">
                                        <label for="issued_date_time" class="form-label">Issued Date & Time</label>
                                        <input type="datetime-local" class="form-control @error('issued_date_time') is-invalid @enderror" 
                                               id="issued_date_time" name="issued_date_time" value="{{ old('issued_date_time') }}">
                                        @error('issued_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="preferred_start_date_time" class="form-label">Preferred Start Date & Time</label>
                                        <input type="datetime-local" class="form-control @error('preferred_start_date_time') is-invalid @enderror" 
                                               id="preferred_start_date_time" name="preferred_start_date_time" value="{{ old('preferred_start_date_time') }}">
                                        @error('preferred_start_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="preferred_end_date_time" class="form-label">Preferred End Date & Time</label>
                                        <input type="datetime-local" class="form-control @error('preferred_end_date_time') is-invalid @enderror" 
                                               id="preferred_end_date_time" name="preferred_end_date_time" value="{{ old('preferred_end_date_time') }}">
                                        @error('preferred_end_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="deadline_date" class="form-label">Deadline Date</label>
                                        <input type="date" class="form-control @error('deadline_date') is-invalid @enderror" 
                                               id="deadline_date" name="deadline_date" value="{{ old('deadline_date') }}">
                                        @error('deadline_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Contact Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="contact_name" class="form-label">Contact Name</label>
                                        <input type="text" class="form-control @error('contact_name') is-invalid @enderror" 
                                               id="contact_name" name="contact_name" value="{{ old('contact_name') }}">
                                        @error('contact_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_mobile" class="form-label">Contact Mobile</label>
                                        <input type="text" class="form-control @error('contact_mobile') is-invalid @enderror" 
                                               id="contact_mobile" name="contact_mobile" value="{{ old('contact_mobile') }}">
                                        @error('contact_mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Contact Email</label>
                                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                               id="contact_email" name="contact_email" value="{{ old('contact_email') }}">
                                        @error('contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="note_for_access" class="form-label">Note for Access</label>
                                        <input type="text" class="form-control @error('note_for_access') is-invalid @enderror" 
                                               id="note_for_access" name="note_for_access" value="{{ old('note_for_access') }}">
                                        @error('note_for_access')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comments</label>
                                <textarea class="form-control @error('comment') is-invalid @enderror" 
                                          id="comment" name="comment" rows="4">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File Uploads -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="mb-3">PDF Document</h5>
                                        <label for="pdf" class="form-label">Upload PDF</label>
                                        <input type="file" class="form-control @error('pdf') is-invalid @enderror" 
                                               id="pdf" name="pdf" accept=".pdf">
                                        <div class="form-text">Maximum file size: 10MB</div>
                                        @error('pdf')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="mb-3">Images</h5>
                                        <label for="images" class="form-label">Upload Images</label>
                                        <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                               id="images" name="images[]" multiple accept="image/*">
                                        <div class="form-text">You can select multiple images. Maximum file size: 2MB each.</div>
                                        @error('images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('block-work-orders.index') }}" class="btn btn-secondary">
                            <i class="ph-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-floppy-disk me-1"></i> Create Block Work Order
                        </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let unitAutoCompleteInstance = null;
        const blockSelect = document.getElementById('block_id');
        const unitSearchInput = document.getElementById('block_unit_search');
        const unitHiddenInput = document.getElementById('block_unit_id');

        function destroyAutoComplete() {
            if (unitAutoCompleteInstance && typeof unitAutoCompleteInstance.unInit === 'function') {
                try { unitAutoCompleteInstance.unInit(); } catch (e) {}
            }
            unitAutoCompleteInstance = null;
            document.querySelectorAll('[id^="autoComplete_list"]').forEach(el => el.remove());
        }

        function initAutoComplete(units) {
            if (typeof autoComplete === 'undefined') {
                return;
            }
            destroyAutoComplete();
            unitAutoCompleteInstance = new autoComplete({
                selector: () => document.getElementById("block_unit_search"),
                placeHolder: "Search for units...",
                data: {
                    src: units,
                    keys: ["searchValue", "label"],
                    // Ensure no duplicate suggestions are rendered even if multiple keys match
                    filter: (list) => {
                        const seen = new Set();
                        return list.filter(item => {
                            const id = item?.value?.value ?? item?.value?.id ?? item?.value;
                            if (seen.has(id)) return false;
                            seen.add(id);
                            return true;
                        });
                    }
                },
                resultItem: {
                    highlight: false,
                    element: (item, data) => {
                        item.innerHTML = data.value.label || '';
                    }
                },
                events: {
                    input: {
                        selection: (event) => {
                            const selection = event.detail?.selection?.value;
                            if (selection) {
                                unitSearchInput.value = selection.label;
                                unitHiddenInput.value = selection.value;
                            }
                        }
                    }
                },
                threshold: 1,
                debounce: 250,
                maxResults: 10
            });
        }

        function fetchUnitsForBlock(blockId) {
            if (!blockId) {
                unitSearchInput.value = '';
                unitHiddenInput.value = '';
                destroyAutoComplete();
                return;
            }
            fetch(`/block-units/block/${blockId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                const units = (data?.data || []).map(unit => {
                    const labelParts = [];
                    if (unit.unit_code) labelParts.push(unit.unit_code);
                    if (unit.unit_name && unit.unit_name !== unit.unit_code) labelParts.push(unit.unit_name);
                    const label = labelParts.join(' - ') || `Unit #${unit.id}`;
                    const searchValue = [unit.unit_code, unit.unit_name].filter(Boolean).join(' ').toLowerCase();
                    return { value: unit.id, label, searchValue };
                });
                // Deduplicate by unit id to avoid showing the same unit twice
                const seenIds = new Set();
                const uniqueUnits = [];
                for (const u of units) {
                    if (!seenIds.has(u.value)) {
                        seenIds.add(u.value);
                        uniqueUnits.push(u);
                    }
                }
                initAutoComplete(uniqueUnits);
                // Preselect if hidden has value
                const existing = (unitHiddenInput.value || '').toString();
                if (existing) {
                    const found = uniqueUnits.find(u => String(u.value) === existing);
                    if (found) unitSearchInput.value = found.label;
                }
            })
            .catch(() => {
                destroyAutoComplete();
            });
        }

        if (blockSelect && blockSelect.value) {
            fetchUnitsForBlock(blockSelect.value);
        }

        if (blockSelect) {
            blockSelect.addEventListener('change', function() {
                unitHiddenInput.value = '';
                unitSearchInput.value = '';
                fetchUnitsForBlock(this.value);
            });
        }
    });
    </script>
@endpush