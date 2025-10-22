@extends('layouts.master')

@section('title') Permissions Management @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Settings @endslot
@slot('li_2') Permissions @endslot
@slot('title') Permissions Management @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ph-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ph-x-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">All Permissions</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="ph-info me-2"></i> 
                    <strong>Total Permissions:</strong> {{ collect($permissions)->flatten()->count() }} grouped into {{ count($permissions) }} modules
                </div>

                <div class="row">
                    @foreach($permissions as $module => $modulePermissions)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="ph-shield-check me-2 text-primary"></i>
                                    {{ str_replace('-', ' ', ucwords($module)) }}
                                </h6>
                                <small class="text-muted">{{ $modulePermissions->count() }} permissions</small>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    @foreach($modulePermissions as $permission)
                                    <li class="mb-2">
                                        <i class="ph-dot text-primary me-1"></i>
                                        <code class="small">{{ $permission->name }}</code>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

