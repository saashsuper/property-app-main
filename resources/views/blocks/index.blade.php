@extends('layouts.master')

@section('title') Blocks @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Dashboard @endslot
@slot('title') Blocks Management @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Blocks List</h4>
                    </div>
                    <div class="col-auto">
                        <form action="{{ route('blocks.index') }}" method="GET" class="d-flex me-3">
                            <div class="input-group" style="min-width: 250px;">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search blocks..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('blocks.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                        <a href="{{ route('blocks.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Block
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(request('search'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-search me-2"></i>
                        Search results for "{{ request('search') }}": {{ $blocks->total() }} block(s) found
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Management Company</th>
                                <th>Address</th>
                                <th>Units</th>
                                <th>Car Spaces</th>
                                <th>Created By</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blocks as $block)
                            <tr>
                                <td>{{ $block->id }}</td>
                                <td>
                                    @if($block->image_url)
                                        <img src="{{ $block->image_url }}" alt="{{ $block->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-building text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $block->name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $block->blockType->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $block->management_company }}</td>
                                <td>
                                    <small class="text-muted">{{ $block->full_address }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $block->no_of_units ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $block->car_spaces }}</span>
                                </td>
                                <td>
                                    <small>{{ $block->creator->name ?? 'System' }}</small>
                                </td>
                                <td>
                                    <small>{{ $block->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('blocks.show', $block->id) }}">
                                                    <i class="fas fa-eye me-2"></i>View
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('blocks.edit', $block->id) }}">
                                                    <i class="fas fa-edit me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('blocks.destroy', $block->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this block?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>No blocks found. <a href="{{ route('blocks.create') }}" class="text-primary">Create your first block</a></p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($blocks->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $blocks->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 