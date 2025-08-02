@extends('layouts.master')
@section('title')
    Dashboard - PROMAN
@endsection
@section('css')
    <!-- add your css here -->
@endsection
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Total Blocks</span>
                                <h4 class="mb-0">{{ \App\Models\Block::count() }}</h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-primary rounded-3">
                                        <i class="ri-building-line font-size-20 text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Block Types</span>
                                <h4 class="mb-0">{{ \App\Models\BlockType::count() }}</h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-success rounded-3">
                                        <i class="ri-layout-grid-line font-size-20 text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Total Units</span>
                                <h4 class="mb-0">{{ \App\Models\BlockUnit::count() }}</h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-warning rounded-3">
                                        <i class="ri-home-line font-size-20 text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted text-uppercase fw-medium">Active Issues</span>
                                <h4 class="mb-0">{{ \App\Models\BlockIssue::count() }}</h4>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-danger rounded-3">
                                        <i class="ri-error-warning-line font-size-20 text-danger"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-soft-primary rounded-3">
                                                <i class="ri-building-line font-size-24 text-primary"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Manage Blocks</h5>
                                        <p class="card-text text-muted">Create, edit, and manage your property blocks</p>
                                        <a href="{{ route('blocks.index') }}" class="btn btn-primary">View Blocks</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-soft-success rounded-3">
                                                <i class="ri-add-line font-size-24 text-success"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Add New Block</h5>
                                        <p class="card-text text-muted">Create a new property block with all details</p>
                                        <a href="{{ route('blocks.create') }}" class="btn btn-success">Create Block</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-soft-info rounded-3">
                                                <i class="ri-layout-grid-line font-size-24 text-info"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">Block Types</h5>
                                        <p class="card-text text-muted">Manage different types of property blocks</p>
                                        <a href="{{ route('block-types.index') }}" class="btn btn-info">Manage Types</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <span class="avatar-title bg-soft-warning rounded-3">
                                                <i class="ri-settings-line font-size-24 text-warning"></i>
                                            </span>
                                        </div>
                                        <h5 class="card-title">System Settings</h5>
                                        <p class="card-text text-muted">Configure system settings and preferences</p>
                                        <a href="#" class="btn btn-warning">Settings</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Blocks</h4>
                    </div>
                    <div class="card-body">
                        @php
                            $recentBlocks = \App\Models\Block::with('blockType')->latest()->take(5)->get();
                        @endphp
                        
                        @if($recentBlocks->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentBlocks as $block)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $block->name }}</h6>
                                            <small class="text-muted">{{ $block->blockType->name ?? 'N/A' }}</small>
                                        </div>
                                        <a href="{{ route('blocks.show', $block) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ri-building-line fs-2"></i>
                                    <p class="mt-2">No blocks found</p>
                                    <a href="{{ route('blocks.create') }}" class="btn btn-primary btn-sm">
                                        Create Your First Block
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">System Status</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Database</span>
                            <span class="badge bg-success">Connected</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Application</span>
                            <span class="badge bg-success">Running</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Storage</span>
                            <span class="badge bg-info">Available</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Last Updated</span>
                            <small class="text-muted">{{ now()->format('M d, Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <!-- add your js here -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
