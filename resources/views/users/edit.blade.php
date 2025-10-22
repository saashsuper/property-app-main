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
                                       id="password" name="password" pattern="[a-zA-Z0-9]{8,}" 
                                       title="Password must be alphanumeric and at least 8 characters">
                                <div class="form-text">Password must be alphanumeric (letters and numbers only) and at least 8 characters long. @lang('translation.leave-blank-to-keep-current')</div>
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

                    @if($user->avatar)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">@lang('translation.current-avatar')</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle" width="100" height="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Assign Roles</label>
                                <div class="border rounded p-3">
                                    @foreach($roles as $role)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                               id="role_{{ $role->id }}" {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            {{ $role->name }}
                                            <small class="text-muted">({{ $role->permissions->count() }} permissions)</small>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="form-text">Select one or more roles for this user</div>
                                @error('roles')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
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
