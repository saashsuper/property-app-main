@extends('layouts.master')

@section('title') Roles Management @endsection

@section('css')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Settings @endslot
@slot('li_2') Roles @endslot
@slot('title') Roles Management @endslot
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
                <h4 class="card-title mb-0">Roles List</h4>
                @can('roles.create')
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    <i class="ph-plus me-1"></i> Create New Role
                </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dt-responsive nowrap" id="rolesTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Role Name</th>
                                <th>Permissions Count</th>
                                <th>Users Count</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $role->name }}</strong>
                                    @if($role->name === 'Super Admin')
                                        <span class="badge bg-danger ms-2">System</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $role->permissions->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $role->users->count() }}</span>
                                </td>
                                <td>{{ $role->created_at->format('d M, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('roles.view')
                                        <a href="{{ route('roles.show', $role->id) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="ph-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('roles.edit')
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="ph-pencil"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('roles.delete')
                                        @if($role->name !== 'Super Admin')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="ph-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
    $('#rolesTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        pageLength: 25
    });
});
</script>
@endsection

