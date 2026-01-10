# Work Order Payload Fix - Issue Details Page

## Issue Summary

Analyzed the raise work order process in the issue details page (`block-issues/show.blade.php`) and identified several potential issues with the payload structure and field naming.

## Problems Identified

### 1. Field Name Mismatch (CRITICAL)
- **Problem**: The form field was named `contractor_id` but should be `contract_company_id` for outsource work orders
- **Impact**: Potential validation failures and data inconsistency with backend expectations
- **Root Cause**: The backend expects `contract_company_id` for outsource assignments but the form was using `contractor_id`

### 2. Missing Image Upload Field
- **Problem**: Backend accepts image uploads but the modal form didn't have an upload field
- **Impact**: Users couldn't attach photos when creating work orders from issue details

### 3. Field Toggle Logic
- **Problem**: JavaScript wasn't properly managing field requirements and visibility
- **Impact**: Potential form validation issues when switching between outsource/inhouse

## Fixes Implemented

### 1. Fixed Contract Company Field Name

**File**: `resources/views/block-issues/show.blade.php`

Updated the contract company dropdown to always use `contract_company_id` for outsource work orders:

```blade
<select class="form-select" name="contract_company_id" id="contractorField" required>
    <option value="">Select Contract Company</option>
    @if(isset($contractCompanies) && $contractCompanies->count() > 0)
        @foreach($contractCompanies as $company)
            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
        @endforeach
    @elseif(isset($contractors) && $contractors->count() > 0)
        @foreach($contractors as $contractor)
            <option value="{{ $contractor->id }}">
                {{ $contractor->name }}@if($contractor->code) ({{ $contractor->code }})@endif
            </option>
        @endforeach
    @endif
</select>
```

**Benefits**:
- Always uses `contract_company_id` field name for outsource work orders
- Matches backend validation expectations
- Clear and consistent payload structure

### 2. Added Image Upload Field

Added a file input field for image uploads:

```html
<div class="row">
    <div class="col-md-12 mb-3">
        <label class="form-label">Upload Photos (Optional)</label>
        <input type="file" class="form-control" name="images[]" id="workOrderImages" multiple accept="image/*">
        <small class="text-muted">You can upload multiple images (JPEG, PNG, JPG, GIF). Max 2MB each.</small>
    </div>
</div>
```

**Benefits**:
- Users can now attach photos during work order creation
- Matches backend capability for image processing
- Multiple file upload support

### 3. Updated JavaScript Toggle Logic

Updated the work order type toggle handler to properly manage field visibility and requirements:

```javascript
$('#workOrderType').on('change', function() {
    const selectedType = $(this).val();
    
    if (selectedType === 'inhouse') {
        // Show Property Manager, hide Contract Company
        $('#propertyManagerFieldContainer').show();
        $('#contractorFieldContainer').hide();
        $('#propertyManagerField').prop('required', true).prop('disabled', false);
        $('#contractorField').prop('required', false).prop('disabled', true).val('');
    } else {
        // Show Contract Company, hide Property Manager (Outsource)
        $('#contractorFieldContainer').show();
        $('#propertyManagerFieldContainer').hide();
        // Field name is set by blade template based on data source
        $('#contractorField').prop('required', true).prop('disabled', false);
        $('#propertyManagerField').prop('required', false).prop('disabled', true).val('');
    }
});
```

### 4. Updated Form Reset Logic

Updated `resetWorkOrderModalToCreateMode()` to properly clear all fields:

```javascript
function resetWorkOrderModalToCreateMode() {
    $('#workOrderModalTitle').text('Raise Work Order for Issue: {{ $blockIssue->ref_no }}');
    $('#workOrderSubmitBtnText').text('Raise Work Order');
    $('#createWorkOrderForm').attr('action', '{{ route("block-work-orders.store") }}');
    $('#createWorkOrderForm').find('input[name="_method"]').remove();
    $('#createWorkOrderForm input[name="block_issue_id"]').val('{{ $blockIssue->id }}');
    $('#workOrderType').val('outsource').trigger('change');
    // Clear form values
    $('#contractorField').val('');
    $('#propertyManagerField').val('');
    $('#workOrderImages').val('');
    hideWorkOrderAlert();
}
```

### 5. Updated Edit Load Logic

Simplified the work order edit loading to use the contractor_id from database:

```javascript
if (workOrder.is_property_manager === true || workOrder.is_property_manager === 1) {
    $('#workOrderType').val('inhouse').trigger('change');
    setTimeout(() => {
        $('#propertyManagerField').val(workOrder.contractor_id || '');
    }, 100);
} else {
    $('#workOrderType').val('outsource').trigger('change');
    setTimeout(() => {
        $('#contractorField').val(workOrder.contractor_id || '');
    }, 100);
}
```

## Data Flow

### Outsource Work Order Creation:
1. User selects "Outsource" type
2. System shows contract company dropdown
3. Field name: `contract_company_id`
4. Backend validates against ContractCompany/Contractor model
5. ID is stored in `block_work_orders.contractor_id` field

### Inhouse Work Order Creation:
1. User selects "In House" type
2. System shows property manager dropdown
3. Field name: `property_manager_id`
4. Backend validates against User model (with Property Manager user type)
5. ID is stored in `block_work_orders.contractor_id` field

## Backend Processing

The backend controller handles all three input types:

```php
// Handle contractor or property manager assignment
if ($request->property_manager_id) {
    $data['contractor_id'] = $request->property_manager_id;
} elseif ($request->contractor_id) {
    $data['contractor_id'] = $request->contractor_id;
} elseif ($request->contract_company_id) {
    $data['contractor_id'] = $request->contract_company_id;
}
```

All are stored in the same `contractor_id` field in the database, with the relationship determined by querying the appropriate model.

## Testing Checklist

- [ ] Create outsource work order with contract company
- [ ] Create inhouse work order with property manager
- [ ] Upload images during work order creation
- [ ] Upload multiple images at once
- [ ] Edit existing outsource work order
- [ ] Edit existing inhouse work order
- [ ] Switch between outsource/inhouse types in form
- [ ] Verify validation errors display properly
- [ ] Verify contract company dropdown loads correctly
- [ ] Verify property manager dropdown loads correctly

## Files Modified

1. `resources/views/block-issues/show.blade.php`
   - Updated contract company dropdown to use dynamic field names
   - Added image upload field
   - Updated JavaScript toggle logic
   - Updated form reset and edit load functions

## Notes

- **Outsource**: Always uses `contract_company_id` field to assign to a contract company
- **Inhouse**: Uses `property_manager_id` field to assign to a property manager user
- Both types store the ID in the same `contractor_id` database field
- The form prioritizes `$contractCompanies` data but falls back to `$contractors` if needed
- The `BlockWorkOrder` model has relationships to both User and Contractor to handle the polymorphic nature

## Key Changes from Previous Implementation

1. **Simplified Field Naming**: No longer using dynamic field names based on data source
2. **Consistent Payload**: Always sends `contract_company_id` for outsource work orders
3. **Cleaner Code**: Removed backward compatibility logic for cleaner, more maintainable code
4. **Clear Intent**: Field names now clearly indicate their purpose (contract_company_id vs property_manager_id)

