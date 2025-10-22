@extends('layouts.master')

@section('title') View Role @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Settings @endslot
@slot('li_2') Roles @endslot
@slot('title') View Role @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Role Details: {{ $role->name }}</h4>
                <div class="btn-group">
                    @can('roles.edit')
                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary">
                        <i class="ph-pencil me-1"></i> Edit Role
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Role Name:</th>
                                <td>
                                    <strong>{{ $role->name }}</strong>
                                    @if($role->name === 'Super Admin')
                                        <span class="badge bg-danger ms-2">System Role</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $role->created_at->format('d M, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At:</th>
                                <td>{{ $role->updated_at->format('d M, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Total Permissions:</th>
                                <td><span class="badge bg-primary">{{ $role->permissions->count() }}</span></td>
                            </tr>
                            <tr>
                                <th>Total Users:</th>
                                <td><span class="badge bg-info">{{ $role->users->count() }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Assigned Permissions</h5>
                @if($role->permissions->count() > 0)
                <div class="row">
                    @php
                        $groupedPermissions = $role->permissions->groupBy(function($permission) {
                            return explode('.', $permission->name)[0];
                        });
                    @endphp
                    
                    @foreach($groupedPermissions as $module => $permissions)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card bg-light">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">{{ str_replace('-', ' ', ucwords($module)) }}</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    @foreach($permissions as $permission)
                                    <li class="mb-2">
                                        <i class="ph-check-circle text-success me-1"></i>
                                        {{ ucwords(str_replace(['-', '.'], [' ', ' - '], explode('.', $permission->name)[1])) }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="ph-warning me-2"></i> No permissions assigned to this role yet.
                </div>
                @endif

                <hr>

                <h5 class="mb-3">Users with this Role</h5>
                @if($role->users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>User Type</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->userType->name ?? 'N/A' }}</td>
                                <td>{{ $user->created_at->format('d M, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="ph-info me-2"></i> No users assigned to this role yet.
                </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="ph-arrow-left"></i> Back to Roles
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

