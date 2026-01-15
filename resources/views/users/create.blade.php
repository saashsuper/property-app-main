@extends('layouts.master')

@section('title') @lang('translation.create-user') @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.user-management') @endslot
@slot('li_2') @lang('translation.users') @endslot
@slot('title') @lang('translation.create-user') @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">@lang('translation.create-user')</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">@lang('translation.name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">@lang('translation.email') <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
                                       title="Please enter a valid email address (e.g., user@example.com)" 
                                       required>
                                <div class="form-text">Please enter a valid email address (e.g., user@example.com)</div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">@lang('translation.password') <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{8,}" 
                                       title="Password must be at least 8 characters and include one uppercase and one special character" required>
                                <div class="form-text">Password must be at least 8 characters and include at least one uppercase letter and one special character.</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">@lang('translation.confirm-password') <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" name="password_confirmation" required>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_type_id" class="form-label">@lang('translation.user-type') <span class="text-danger">*</span></label>
                                <select class="form-select @error('user_type_id') is-invalid @enderror" 
                                        id="user_type_id" name="user_type_id" required>
                                    <option value="">@lang('translation.select-user-type')</option>
                                    @foreach($userTypes as $userType)
                                        <option value="{{ $userType->id }}" data-name="{{ $userType->name }}" {{ old('user_type_id') == $userType->id ? 'selected' : '' }}>
                                            {{ $userType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3" id="contract-company-wrapper" style="display: none;">
                                <label for="contract_company_id" class="form-label">Contract Company</label>
                                <select class="form-select @error('contract_company_id') is-invalid @enderror" 
                                        id="contract_company_id" name="contract_company_id">
                                    <option value="">Select Contract Company</option>
                                    @foreach($contractCompanies as $company)
                                        <option value="{{ $company->id }}" {{ old('contract_company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('contract_company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3" id="web-login-wrapper" style="display: none;">
                                <label class="form-label" for="is_web_login_required">Web Login Required</label>
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input" type="checkbox" id="is_web_login_required" name="is_web_login_required" {{ old('is_web_login_required') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_web_login_required"><small class="text-muted">If checked, a default password will be set.</small></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="avatar" class="form-label">@lang('translation.avatar')</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                       id="avatar" name="avatar" accept="image/*">
                                <div class="form-text">@lang('translation.upload-avatar')</div>
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                    <i class="ph-arrow-left"></i> @lang('translation.back')
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ph-check"></i> @lang('translation.create')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.getElementById('user_type_id');
    const webLoginWrapper = document.getElementById('web-login-wrapper');
    const contractCompanyWrapper = document.getElementById('contract-company-wrapper');
    const emailInput = document.getElementById('email');
    const emailFormText = emailInput ? emailInput.nextElementSibling : null;

    // Email validation function
    function validateEmail(email) {
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailRegex.test(email);
    }

    // Real-time email validation
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && !validateEmail(email)) {
                this.setCustomValidity('Please enter a valid email address (e.g., user@example.com)');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });

        emailInput.addEventListener('input', function() {
            const email = this.value.trim();
            if (email && validateEmail(email)) {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });

        // Form submission validation
        const form = emailInput.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const email = emailInput.value.trim();
                if (!validateEmail(email)) {
                    e.preventDefault();
                    emailInput.focus();
                    emailInput.classList.add('is-invalid');
                    emailInput.setCustomValidity('Please enter a valid email address');
                    return false;
                }
            });
        }
    }

    function toggleFields() {
        const selected = userTypeSelect.options[userTypeSelect.selectedIndex];
        const typeName = selected ? selected.getAttribute('data-name') : '';
        
        // Show/hide web login for Contractor Admin
        if (typeName === 'Contractor Admin') {
            webLoginWrapper.style.display = '';
        } else {
            webLoginWrapper.style.display = 'none';
            const cb = document.getElementById('is_web_login_required');
            if (cb) cb.checked = false;
        }
        
        // Show/hide contract company for Contractor Admin and Contractor User
        if (typeName === 'Contractor Admin' || typeName === 'Contractor User') {
            contractCompanyWrapper.style.display = '';
        } else {
            contractCompanyWrapper.style.display = 'none';
            const contractCompanySelect = document.getElementById('contract_company_id');
            if (contractCompanySelect) contractCompanySelect.value = '';
        }
    }

    userTypeSelect.addEventListener('change', toggleFields);
    toggleFields();
});
</script>
@endsection
@endsection
