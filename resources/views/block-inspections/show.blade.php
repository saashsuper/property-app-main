@extends('layouts.master')

@section('title') Inspection Details - {{ $blockInspection->ref_no }} @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.blocks') @endslot
        @slot('li_2') @lang('translation.block-inspections') @endslot
        @slot('title') {{ $blockInspection->ref_no }} @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Inspection Details</h4>
                        <div class="d-flex gap-2">
                            @if($blockInspection->job_status_id == 1)
                                <form action="{{ route('block-inspections.start', $blockInspection->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="ph-play me-2"></i>Start Inspection
                                    </button>
                                </form>
                            @endif
                            
                            @if($blockInspection->job_status_id == 2)
                                <form action="{{ route('block-inspections.complete', $blockInspection->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="ph-check me-2"></i>Complete Inspection
                                    </button>
                                </form>
                            @endif
                            
                            @admin
                            <a href="{{ route('block-inspections.edit', $blockInspection->id) }}" class="btn btn-warning btn-sm">
                                <i class="ph-pencil me-2"></i>Edit
                            </a>
                            @endadmin
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Reference Number</label>
                                <p class="form-control-plaintext">{{ $blockInspection->ref_no }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $blockInspection->status_color }}-subtle text-{{ $blockInspection->status_color }}">
                                        {{ $blockInspection->status_text }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Block</label>
                                <p class="form-control-plaintext">
                                    @if($blockInspection->block)
                                        <a href="{{ route('blocks.show', $blockInspection->block->id) }}" class="text-decoration-none">
                                            {{ $blockInspection->block->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Block not found</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Scheduled Date & Time</label>
                                <p class="form-control-plaintext">{{ $blockInspection->scheduled_date_time->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($blockInspection->start_date_time)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Start Date & Time</label>
                                <p class="form-control-plaintext">{{ $blockInspection->start_date_time->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @if($blockInspection->end_date_time)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">End Date & Time</label>
                                <p class="form-control-plaintext">{{ $blockInspection->end_date_time->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($blockInspection->notes)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Notes</label>
                                <p class="form-control-plaintext">{{ $blockInspection->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created By</label>
                                <p class="form-control-plaintext">{{ $blockInspection->creator->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created Date</label>
                                <p class="form-control-plaintext">{{ $blockInspection->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspection Team -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Inspection Team</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Lead</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blockInspection->inspectionTeams as $teamMember)
                                    <tr>
                                        <td>{{ $teamMember->user ? $teamMember->user->name : 'N/A' }}</td>
                                        <td>{{ $teamMember->user ? $teamMember->user->email : 'N/A' }}</td>
                                        <td>{{ $teamMember->role }}</td>
                                        <td>
                                            @if($teamMember->is_lead)
                                                <span class="badge bg-success">Lead</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No team members assigned</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Inspection Assets -->
            @if($blockInspection->inspectionAssets->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Inspection Assets</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Building</th>
                                    <th>Asset</th>
                                    <th>Status</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($blockInspection->inspectionAssets as $asset)
                                    <tr>
                                        <td>{{ $asset->blockBuilding->name ?? 'N/A' }}</td>
                                        <td>{{ $asset->buildingAsset->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $asset->inspectionValue->valueType->name == 'Good' ? 'success' : ($asset->inspectionValue->valueType->name == 'Fair' ? 'warning' : 'danger') }}-subtle">
                                                {{ $asset->inspectionValue->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $asset->comments }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Block Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Block Information</h5>
                </div>
                <div class="card-body">
                    @if($blockInspection->block)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Block Name</label>
                            <p class="form-control-plaintext">{{ $blockInspection->block->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <p class="form-control-plaintext">
                                {{ $blockInspection->block->address1 }}<br>
                                @if($blockInspection->block->address2){{ $blockInspection->block->address2 }}<br>@endif
                                @if($blockInspection->block->address3){{ $blockInspection->block->address3 }}@endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Management Company</label>
                            <p class="form-control-plaintext">{{ $blockInspection->block->management_company }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Block Type</label>
                            <p class="form-control-plaintext">{{ $blockInspection->block->blockType->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Number of Units</label>
                            <p class="form-control-plaintext">{{ $blockInspection->block->no_of_units ?? 'N/A' }}</p>
                        </div>
                    @else
                        <p class="text-muted">Block information not available</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($blockInspection->block)
                            <a href="{{ route('blocks.show', $blockInspection->block->id) }}" class="btn btn-outline-primary">
                                <i class="ph-buildings me-2"></i>View Block Details
                            </a>
                            <a href="{{ route('block-issues.index') }}?block_id={{ $blockInspection->block->id }}" class="btn btn-outline-warning">
                                <i class="ph-warning me-2"></i>View Block Issues
                            </a>
                            <a href="{{ route('block-visits.index') }}?block_id={{ $blockInspection->block->id }}" class="btn btn-outline-info">
                                <i class="ph-map-pin me-2"></i>View Site Visits
                            </a>
                        @endif
                        @if($blockInspection->pdf_path)
                        <a href="{{ asset('storage/' . $blockInspection->pdf_path) }}" class="btn btn-outline-danger" target="_blank">
                            <i class="ph-file-pdf me-2"></i>Download Report
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
