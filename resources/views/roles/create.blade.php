@extends('layouts.master')

@section('title') Create Role @endsection

@section('css')
<style>
.permission-group {
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
    background-color: #f8f9fa;
}

.permission-group h6 {
    margin-bottom: 0.75rem;
    color: #495057;
    font-weight: 600;
    text-transform: capitalize;
}

.permission-item {
    margin-bottom: 0.5rem;
}

.form-check-label {
    margin-left: 0.5rem;
}
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Settings @endslot
@slot('li_2') Roles @endslot
@slot('title') Create Role @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Create New Role</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required 
                                       placeholder="Enter role name (e.g., Manager, Inspector)">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3">Assign Permissions</h5>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">
                                        <strong>Select All Permissions</strong>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="row">
                                @foreach($permissions as $module => $modulePermissions)
                                <div class="col-md-6 col-lg-4">
                                    <div class="permission-group">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input module-checkbox" type="checkbox" id="module_{{ $module }}" data-module="{{ $module }}">
                                            <label class="form-check-label" for="module_{{ $module }}">
                                                <h6 class="mb-0">{{ str_replace('-', ' ', ucwords($module)) }}</h6>
                                            </label>
                                        </div>
                                        <hr class="my-2">
                                        @foreach($modulePermissions as $permission)
                                        <div class="permission-item">
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="permissions[]" value="{{ $permission->name }}" 
                                                       id="permission_{{ $permission->id }}"
                                                       data-module="{{ $module }}"
                                                       {{ is_array(old('permissions')) && in_array($permission->name, old('permissions')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ ucwords(str_replace(['-', '.'], [' ', ' - '], explode('.', $permission->name)[1])) }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                    <i class="ph-arrow-left"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ph-check"></i> Create Role
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    
    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        permissionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        moduleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Module select all
    moduleCheckboxes.forEach(moduleCheckbox => {
        moduleCheckbox.addEventListener('change', function() {
            const module = this.dataset.module;
            const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
            modulePermissions.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    });
    
    // Update module checkbox when individual permissions change
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const module = this.dataset.module;
            const moduleCheckbox = document.querySelector(`.module-checkbox[data-module="${module}"]`);
            const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
            const allChecked = Array.from(modulePermissions).every(cb => cb.checked);
            const someChecked = Array.from(modulePermissions).some(cb => cb.checked);
            
            moduleCheckbox.checked = allChecked;
            moduleCheckbox.indeterminate = someChecked && !allChecked;
        });
    });
});
</script>
@endsection

