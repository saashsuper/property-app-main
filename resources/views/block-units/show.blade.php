@extends('layouts.master')

@section('title')
    Unit Details - PROMAN
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ph-house me-2 text-primary"></i>
                        UNIT DETAILS
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blocks.index') }}">Blocks</a></li>
                            @if($blockUnit->block)
                                <li class="breadcrumb-item"><a href="{{ route('blocks.show', $blockUnit->block) }}">{{ $blockUnit->block->name }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('blocks.edit', $blockUnit->block) }}">Edit Block</a></li>
                            @endif
                            <li class="breadcrumb-item active">Unit Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @if(session('error'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ph-warning me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
        @endif

        <!-- Unit Overview Header -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body bg-gradient-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="mb-1 text-white">{{ $blockUnit->unit_name ?? 'Unnamed Unit' }}</h2>
                                <p class="mb-0 text-white-50">Unit Code: {{ $blockUnit->unit_code ?? 'N/A' }}</p>
                                <div class="mt-2">
                                    @if($blockUnit->unitType)
                                        <span class="badge bg-white bg-opacity-25 text-white me-2">
                                            <i class="ph-house me-1"></i>{{ $blockUnit->unitType->name }}
                                        </span>
                                    @endif
                                    <span class="badge bg-white bg-opacity-25 text-white">
                                        <i class="ph-{{ $blockUnit->resident ? 'check-circle' : 'x-circle' }} me-1"></i>{{ $blockUnit->resident ? 'Resident' : 'Non-Resident' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="d-flex flex-column align-items-md-end">
                                    <div class="mb-2">
                                        <i class="ph-calendar text-white-50 me-2"></i>
                                        <span class="text-white-50">Created: {{ $blockUnit->created_at ? $blockUnit->created_at->format('d M, Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <i class="ph-clock text-white-50 me-2"></i>
                                        <span class="text-white-50">{{ $blockUnit->created_at ? $blockUnit->created_at->format('h:i A') : 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if($blockUnit->block)
                                            <a href="{{ route('blocks.edit', $blockUnit->block) }}" class="btn btn-light btn-sm">
                                                <i class="ph-arrow-left me-2"></i>Back to Block
                                            </a>
                                        @endif
                                        <a href="{{ route('blocks.index') }}" class="btn btn-outline-light btn-sm">
                                            <i class="ph-list me-2"></i>Block List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Layout -->
        <div class="row">
            <!-- Left Column - Unit Information Cards -->
            <div class="col-lg-8">
                <!-- Quick Statistics Cards -->
                <div class="row">
                    <!-- Unit Information -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Unit Info</h6>
                                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                                        <i class="ph-house text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="mb-1"><strong>Unit Code:</strong> {{ $blockUnit->unit_code ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Unit Name:</strong> {{ $blockUnit->unit_name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Type:</strong> {{ $blockUnit->unitType->name ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Resident:</strong> {{ $blockUnit->resident ? 'Yes' : 'No' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Owner Details -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Owner Details</h6>
                                    <div class="bg-success bg-opacity-10 p-2 rounded">
                                        <i class="ph-user text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="mb-1"><strong>Salutation:</strong> {{ $blockUnit->salutation ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Owner's Name:</strong> {{ $blockUnit->owners_name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $blockUnit->email ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Letting Agent:</strong> {{ $blockUnit->letting_agent ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="row mt-3">
                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Contact Info</h6>
                                    <div class="bg-info bg-opacity-10 p-2 rounded">
                                        <i class="ph-phone text-info fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="mb-1"><strong>Mobile:</strong> {{ $blockUnit->mobile_no ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Phone:</strong> {{ $blockUnit->phone_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Details -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Address</h6>
                                    <div class="bg-warning bg-opacity-10 p-2 rounded">
                                        <i class="ph-map-pin text-warning fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    @if($blockUnit->address1 || $blockUnit->address2 || $blockUnit->address3)
                                        <p class="mb-1"><strong>Address:</strong> {{ $blockUnit->address1 ?? 'N/A' }}</p>
                                        @if($blockUnit->address2)
                                            <p class="mb-1"><strong>Address 2:</strong> {{ $blockUnit->address2 }}</p>
                                        @endif
                                        @if($blockUnit->address3)
                                            <p class="mb-1"><strong>Address 3:</strong> {{ $blockUnit->address3 }}</p>
                                        @endif
                                        <p class="mb-1"><strong>Country:</strong> {{ $blockUnit->country->country_name ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>State:</strong> {{ $blockUnit->state->name ?? 'N/A' }}</p>
                                        <p class="mb-0"><strong>Zip:</strong> {{ $blockUnit->zip ?? 'N/A' }}</p>
                                    @else
                                        <p class="mb-0 text-muted">Resident - No address required</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Miscellaneous Information -->
                @if($blockUnit->misc_info)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Miscellaneous Information</h6>
                                    <div class="bg-secondary bg-opacity-10 p-2 rounded">
                                        <i class="ph-note-pencil text-secondary fs-4"></i>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="mb-0">{{ $blockUnit->misc_info }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Block & Building Information -->
            <div class="col-lg-4">
                <!-- Block Information Card -->
                @if($blockUnit->block)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Block Information</h6>
                            <div class="bg-primary bg-opacity-10 p-2 rounded">
                                <i class="ph-buildings text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                    <i class="ph-buildings"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $blockUnit->block->name }}</h6>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1"><strong>Block Type:</strong><br>
                                @if($blockUnit->block->blockType)
                                    <span class="badge bg-primary">{{ $blockUnit->block->blockType->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong>Address:</strong><br>
                            <small class="text-muted">{{ $blockUnit->block->address1 ?? 'N/A' }}</small>
                        </div>
                        <div class="mb-3">
                            <strong>Units:</strong> {{ $blockUnit->block->no_of_units ?? 0 }}<br>
                            <strong>Car Spaces:</strong> {{ $blockUnit->block->car_spaces ?? 0 }}
                        </div>
                        <a href="{{ route('blocks.show', $blockUnit->block) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="ph-eye me-1"></i>View Block Details
                        </a>
                    </div>
                </div>
                @endif

                <!-- Building Information Card -->
                @if($blockUnit->building)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Building Information</h6>
                            <div class="bg-success bg-opacity-10 p-2 rounded">
                                <i class="ph-buildings text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                    <i class="ph-buildings"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $blockUnit->building->name }}</h6>
                            </div>
                        </div>
                        @if($blockUnit->building->description)
                        <div class="mb-3">
                            <strong>Description:</strong><br>
                            <small class="text-muted">{{ $blockUnit->building->description }}</small>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Unit Metadata -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Metadata</h6>
                            <div class="bg-secondary bg-opacity-10 p-2 rounded">
                                <i class="ph-info text-secondary fs-4"></i>
                            </div>
                        </div>
                        <div class="text-start">
                            <p class="mb-1"><strong>Created:</strong> {{ $blockUnit->created_at ? $blockUnit->created_at->format('d M, Y h:i A') : 'N/A' }}</p>
                            <p class="mb-1"><strong>Updated:</strong> {{ $blockUnit->updated_at ? $blockUnit->updated_at->format('d M, Y h:i A') : 'N/A' }}</p>
                            @if($blockUnit->deleted_at)
                                <p class="mb-0 text-danger"><strong>Deleted:</strong> {{ $blockUnit->deleted_at->format('d M, Y h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
}
.card {
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-2px);
}
.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

/* Section spacing */
.row + .row {
    margin-top: 2rem !important;
}
</style>
@endsection

