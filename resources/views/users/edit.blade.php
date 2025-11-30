@extends('layouts.master')

@section('title') @lang('translation.edit-user') @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.user-management') @endslot
@slot('li_2') @lang('translation.users') @endslot
@slot('title') @lang('translation.edit-user') @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">@lang('translation.edit-user')</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">@lang('translation.name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">@lang('translation.email') <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">@lang('translation.new-password')</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{8,}" 
                                       title="Password must be at least 8 characters and include one uppercase and one special character">
                                <div class="form-text">Password must be at least 8 characters and include at least one uppercase letter and one special character. @lang('translation.leave-blank-to-keep-current')</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">@lang('translation.confirm-password')</label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" name="password_confirmation">
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
                                        <option value="{{ $userType->id }}" 
                                                data-name="{{ $userType->name }}"
                                                {{ old('user_type_id', $user->user_type_id) == $userType->id ? 'selected' : '' }}>
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
                                        <option value="{{ $company->id }}" {{ old('contract_company_id', $user->contract_company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('contract_company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="avatar" class="form-label">@lang('translation.avatar')</label>
                                @if($user->avatar)
                                    <div class="mb-2">
                                        <label class="form-label text-muted small d-block">@lang('translation.current-avatar')</label>
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle border" width="80" height="80" style="object-fit: cover;">
                                    </div>
                                @endif
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
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary">
                                    <i class="ph-arrow-left"></i> @lang('translation.back')
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ph-check"></i> @lang('translation.update')
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
    const userTypeSelect = document.getElementById('user_type_id');
    const contractCompanyWrapper = document.getElementById('contract-company-wrapper');

    function toggleContractCompanyField() {
        const selected = userTypeSelect.options[userTypeSelect.selectedIndex];
        const typeName = selected ? selected.getAttribute('data-name') : '';
        
        // Show/hide contract company for Contractor Admin and Contractor User
        if (typeName === 'Contractor Admin' || typeName === 'Contractor User') {
            contractCompanyWrapper.style.display = 'block';
        } else {
            contractCompanyWrapper.style.display = 'none';
            const contractCompanySelect = document.getElementById('contract_company_id');
            if (contractCompanySelect) contractCompanySelect.value = '';
        }
    }

    if (userTypeSelect) {
        userTypeSelect.addEventListener('change', toggleContractCompanyField);
        // Initialize on page load
        toggleContractCompanyField();
    }
});
</script>
@endsection
