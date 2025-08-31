@extends('layouts.master')

@section('title') @lang('translation.create-user-type') @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.user-management') @endslot
@slot('li_2') @lang('translation.user-types') @endslot
@slot('title') @lang('translation.create-user-type') @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">@lang('translation.create-user-type')</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('user-types.store') }}" method="POST">
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
                                <label for="description" class="form-label">@lang('translation.description')</label>
                                <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                       id="description" name="description" value="{{ old('description') }}">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('user-types.index') }}" class="btn btn-secondary">
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
@endsection
