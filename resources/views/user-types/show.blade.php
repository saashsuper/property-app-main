@extends('layouts.master')

@section('title') @lang('translation.user-type-details') @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.user-management') @endslot
@slot('li_2') @lang('translation.user-types') @endslot
@slot('title') @lang('translation.user-type-details') @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">@lang('translation.user-type-details')</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('user-types.edit', $userType->id) }}" class="btn btn-primary">
                            <i class="ph-pencil"></i> @lang('translation.edit')
                        </a>
                        <a href="{{ route('user-types.index') }}" class="btn btn-secondary">
                            <i class="ph-arrow-left"></i> @lang('translation.back')
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">@lang('translation.id')</label>
                            <p class="mb-0">{{ $userType->id }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">@lang('translation.name')</label>
                            <p class="mb-0">{{ $userType->name }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">@lang('translation.description')</label>
                            <p class="mb-0">{{ $userType->description ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">@lang('translation.users-count')</label>
                            <p class="mb-0">
                                <span class="badge bg-primary">{{ $userType->users_count }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">@lang('translation.created-at')</label>
                            <p class="mb-0">{{ $userType->created_at->format('M d, Y H:i:s') }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">@lang('translation.updated-at')</label>
                            <p class="mb-0">{{ $userType->updated_at->format('M d, Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                @if($userType->users->count() > 0)
                    <hr>
                    <h5 class="mb-3">@lang('translation.associated-users')</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('translation.id')</th>
                                    <th>@lang('translation.name')</th>
                                    <th>@lang('translation.email')</th>
                                    <th>@lang('translation.created-at')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userType->users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
