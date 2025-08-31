@extends('layouts.master')

@section('title') @lang('translation.user-details') @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.user-management') @endslot
@slot('li_2') @lang('translation.users') @endslot
@slot('title') @lang('translation.user-details') @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">@lang('translation.user-details')</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
                            <i class="ph-pencil"></i> @lang('translation.edit')
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="ph-arrow-left"></i> @lang('translation.back')
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-4">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle img-thumbnail" width="150" height="150">
                        @else
                            <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                <span class="text-white fw-bold" style="font-size: 3rem;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">@lang('translation.id')</label>
                                    <p class="mb-0">{{ $user->id }}</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">@lang('translation.name')</label>
                                    <p class="mb-0">{{ $user->name }}</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">@lang('translation.email')</label>
                                    <p class="mb-0">{{ $user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">@lang('translation.user-type')</label>
                                    <p class="mb-0">
                                        @if($user->userType)
                                            <span class="badge bg-info">{{ $user->userType->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">@lang('translation.email-verified')</label>
                                    <p class="mb-0">
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">@lang('translation.email-verified')</span>
                                        @else
                                            <span class="badge bg-warning">@lang('translation.email-not-verified')</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">@lang('translation.created-at')</label>
                                    <p class="mb-0">{{ $user->created_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">@lang('translation.updated-at')</label>
                                    <p class="mb-0">{{ $user->updated_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
