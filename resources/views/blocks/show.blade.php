@extends('layouts.master')

@section('title')
    Block Details - PROMAN
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Block Details</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blocks.index') }}">Blocks</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">{{ $block->name }}</h4>
                            <div>
                                <a href="{{ route('blocks.edit', $block) }}" class="btn btn-primary btn-sm">
                                    <i class="ri-edit-line align-bottom me-1"></i> Edit
                                </a>
                                <a href="{{ route('blocks.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ri-arrow-left-line align-bottom me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                @if($block->image_url)
                                    <img src="{{ $block->image_url }}" alt="{{ $block->name }}" 
                                         class="img-fluid rounded mb-3" style="max-width: 100%;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" 
                                         style="height: 200px;">
                                        <i class="ri-building-line text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Block Type</label>
                                            <p class="mb-0">
                                                <span class="badge bg-primary">{{ $block->blockType->name ?? 'N/A' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Management Company</label>
                                            <p class="mb-0">{{ $block->management_company }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Car Spaces</label>
                                            <p class="mb-0">
                                                <span class="badge bg-warning">{{ $block->car_spaces }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Number of Units</label>
                                            <p class="mb-0">
                                                <span class="badge bg-info">{{ $block->no_of_units ?? 0 }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Inspection Count</label>
                                            <p class="mb-0">{{ $block->inspection_count ?? 0 }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Created By</label>
                                            <p class="mb-0">{{ $block->creator->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Full Address</label>
                                    <p class="mb-0">{{ $block->full_address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Data -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Buildings ({{ $block->buildings->count() }})</h5>
                            </div>
                            <div class="card-body">
                                @if($block->buildings->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($block->buildings->take(5) as $building)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $building->name }}</h6>
                                                    <small class="text-muted">{{ $building->floor_no }} floors</small>
                                                </div>
                                                <span class="badge bg-primary rounded-pill">{{ $building->no_lift }} lifts</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($block->buildings->count() > 5)
                                        <div class="text-center mt-2">
                                            <small class="text-muted">And {{ $block->buildings->count() - 5 }} more...</small>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">No buildings found</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Units ({{ $block->units->count() }})</h5>
                            </div>
                            <div class="card-body">
                                @if($block->units->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($block->units->take(5) as $unit)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $unit->unit_name }}</h6>
                                                    <small class="text-muted">{{ $unit->owners_name }}</small>
                                                </div>
                                                <span class="badge bg-success rounded-pill">{{ $unit->unit_code }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($block->units->count() > 5)
                                        <div class="text-center mt-2">
                                            <small class="text-muted">And {{ $block->units->count() - 5 }} more...</small>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">No units found</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-primary">{{ $block->buildings->count() }}</h4>
                                    <small class="text-muted">Buildings</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-success">{{ $block->units->count() }}</h4>
                                    <small class="text-muted">Units</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-warning">{{ $block->contractors->count() }}</h4>
                                    <small class="text-muted">Contractors</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-danger">{{ $block->issues->count() }}</h4>
                                    <small class="text-muted">Issues</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Issues -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Issues</h5>
                    </div>
                    <div class="card-body">
                        @if($block->issues->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($block->issues->take(3) as $issue)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($issue->issue, 30) }}</h6>
                                                <small class="text-muted">{{ $issue->ref_no }}</small>
                                            </div>
                                            <small class="text-muted">{{ $issue->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No issues found</p>
                        @endif
                    </div>
                </div>

                <!-- Block Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Block Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Country ID</label>
                            <p class="mb-0">{{ $block->country_id }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">State ID</label>
                            <p class="mb-0">{{ $block->state_id }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Created Date</label>
                            <p class="mb-0">{{ $block->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Last Updated</label>
                            <p class="mb-0">{{ $block->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 