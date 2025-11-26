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
                                        <i class="ph-calendar-check text-white-50 me-2"></i>
                                        <span class="text-white-50">Updated: {{ $blockUnit->updated_at ? $blockUnit->updated_at->format('d M, Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        @if($blockUnit->block)
                                            <a href="{{ route('blocks.edit', $blockUnit->block) }}" class="btn btn-light btn-sm">
                                                <i class="ph-arrow-left me-2"></i>Back to Block
                                            </a>
                                        @endif
                                        <a href="{{ route('blocks.index') }}" class="btn btn-outline-light btn-sm">
                                            <i class="ph-list me-2"></i>Block List
                                        </a>
                                        <button type="button" class="btn btn-warning btn-sm text-white" onclick="openUnitModal('edit', {{ $blockUnit->id }})">
                                            <i class="ph-pencil me-2"></i>Edit Unit
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="showDeleteUnitModal({{ $blockUnit->id }}, {
                                            unit_code: '{{ addslashes($blockUnit->unit_code ?? 'N/A') }}',
                                            unit_name: '{{ addslashes($blockUnit->unit_name ?? 'N/A') }}',
                                            owners_name: '{{ addslashes($blockUnit->owners_name ?? 'N/A') }}',
                                            unit_type: { name: '{{ addslashes($blockUnit->unitType->name ?? 'N/A') }}' }
                                        })">
                                            <i class="ph-trash me-2"></i>Delete Unit
                                        </button>
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
            </div>
        </div>

        <!-- Active Issues and Work Orders Section -->
        <div class="row mt-4">
            <!-- Active Issues -->
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">
                                <i class="ph-warning-circle text-warning me-2"></i>Active Issues for This Unit
                            </h6>
                            <span class="badge bg-warning">{{ $activeIssues->count() }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref #</th>
                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Reported Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($activeIssues->count() > 0)
                                        @foreach($activeIssues as $issue)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('block-issues.show', $issue) }}" class="text-decoration-none">
                                                        <b>#{{ $issue->ref_no ?? $issue->id }}</b>
                                                    </a>
                                                </td>
                                                <td>
                                                    <div title="{{ $issue->issue ?? 'N/A' }}">
                                                        {{ $issue->issue ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($issue->priority_id == 1)
                                                        <span class="badge bg-success">Low</span>
                                                    @elseif($issue->priority_id == 2)
                                                        <span class="badge bg-info">Normal</span>
                                                    @elseif($issue->priority_id == 3)
                                                        <span class="badge bg-warning">High</span>
                                                    @elseif($issue->priority_id == 4)
                                                        <span class="badge bg-danger">Urgent</span>
                                                    @elseif($issue->priority_id == 5)
                                                        <span class="badge bg-dark">Critical</span>
                                                    @else
                                                        <span class="badge bg-secondary">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($issue->issue_status_id == 1)
                                                        <span class="badge bg-warning">Open</span>
                                                    @elseif($issue->issue_status_id == 2)
                                                        <span class="badge bg-primary">In Progress</span>
                                                    @else
                                                        <span class="badge bg-secondary">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $issue->created_at ? $issue->created_at->format('d M, Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">
                                                <i class="ph-check-circle me-2"></i>No active issues for this unit
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                            </div>

            <!-- Active Work Orders -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">
                                <i class="ph-wrench text-primary me-2"></i>Active Work Orders for This Unit
                            </h6>
                            <span class="badge bg-primary">{{ $activeWorkOrders->count() }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref #</th>
                                        <th>Issue</th>
                                        <th>Priority</th>
                                        <th>Deadline</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($activeWorkOrders->count() > 0)
                                        @foreach($activeWorkOrders as $workOrder)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('block-work-orders.show', $workOrder) }}" class="text-decoration-none">
                                                        <b>#{{ $workOrder->ref_no ?? $workOrder->id }}</b>
                                                    </a>
                                                </td>
                                                <td>
                                                    <div title="{{ $workOrder->issue ?? 'N/A' }}">
                                                        {{ $workOrder->issue ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $priorityColors = [
                                                            1 => 'success',
                                                            2 => 'info',
                                                            3 => 'warning',
                                                            4 => 'danger',
                                                            5 => 'dark'
                                                        ];
                                                        $color = $priorityColors[$workOrder->priority_id ?? 2] ?? 'info';
                                                        $priorityLabels = [
                                                            1 => 'Low',
                                                            2 => 'Normal',
                                                            3 => 'High',
                                                            4 => 'Urgent',
                                                            5 => 'Critical'
                                                        ];
                                                        $label = $priorityLabels[$workOrder->priority_id ?? 2] ?? 'Normal';
                                                    @endphp
                                                    <span class="badge bg-{{ $color }}">{{ $label }}</span>
                                                </td>
                                                <td>
                                                    @if($workOrder->deadline_date)
                                                        {{ \Carbon\Carbon::parse($workOrder->deadline_date)->format('d M, Y') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $workOrder->created_at ? $workOrder->created_at->format('d M, Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">
                                                <i class="ph-check-circle me-2"></i>No active work orders for this unit
                                            </td>
                                        </tr>
                            @endif
                                </tbody>
                            </table>
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

@if($blockUnit->block)
<!-- Unit Edit Modal -->
<div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border-bottom: none;">
                <h5 class="modal-title" id="unitModalLabel" style="color: white !important; padding-bottom: 15px;">Edit Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(100) !important; margin-bottom: 10px; font-weight: bold;"></button>
            </div>
            <form id="unitForm" method="POST" action="{{ route('block-units.store') }}">
                @csrf
                <input type="hidden" name="block_id" value="{{ $blockUnit->block->id }}">
                <div class="modal-body">
                    <!-- Message container -->
                    <div id="unitMessage" class="alert d-none" role="alert">
                        <i class="ph-check-circle me-2"></i>
                        <span class="message-text"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="block_building_id" class="form-label">Building/Core <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_building_id" name="block_building_id" required>
                                <option value="">Select Building/Core</option>
                                @foreach($blockUnit->block->buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="block_unit_type_id" class="form-label">Unit Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_unit_type_id" name="block_unit_type_id" required>
                                <option value="">Select Unit Type</option>
                                @foreach(\App\Models\BlockUnitType::orderBy('name')->get() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unit_code" class="form-label">Unit Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit_code" name="unit_code" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unit_name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit_name" name="unit_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="owners_name" class="form-label">Owner's Name</label>
                            <input type="text" class="form-control" id="owners_name" name="owners_name">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="salutation" class="form-label">Salutation <span class="text-danger">*</span></label>
                            <select class="form-select" id="salutation" name="salutation" required>
                                <option value="">Select Salutation</option>
                                @foreach(\App\Models\Salutation::orderBy('name')->get() as $salutation)
                                    <option value="{{ $salutation->name }}">{{ $salutation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="resident" class="form-label">Resident <span class="text-danger">*</span></label>
                            <select class="form-select" id="resident" name="resident" required>
                                <option value="">Select Option</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="mobile_no" class="form-label">Mobile Number</label>
                            <input type="number" class="form-control" id="mobile_no" name="mobile_no" min="0" max="99999999999999999999">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="number" class="form-control" id="phone_number" name="phone_number" min="0" max="99999999999999999999">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="letting_agent" class="form-label">Letting Agent</label>
                            <input type="text" class="form-control" id="letting_agent" name="letting_agent">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="misc_info" class="form-label">Miscellaneous Info</label>
                            <textarea class="form-control" id="misc_info" name="misc_info" rows="2"></textarea>
                        </div>
                        
                        <!-- Address Fields - Shown when Resident = No -->
                        <div class="col-md-4 mb-3" id="address1_field" style="display: none;">
                            <label for="address1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address1" name="address1">
                        </div>
                        <div class="col-md-4 mb-3" id="address2_field" style="display: none;">
                            <label for="address2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="address2" name="address2">
                        </div>
                        <div class="col-md-4 mb-3" id="address3_field" style="display: none;">
                            <label for="address3" class="form-label">Address Line 3</label>
                            <input type="text" class="form-control" id="address3" name="address3">
                        </div>
                        <div class="col-md-4 mb-3" id="country_field" style="display: none;">
                            <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-select" id="country_id" name="country_id" onchange="handleCountryChange(this.value)">
                                <option value="">Select Country</option>
                                @foreach(\App\Models\Country::orderBy('country_name')->get() as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3" id="state_field" style="display: none;">
                            <label for="state_id" class="form-label">County / State <span class="text-danger">*</span></label>
                            <select class="form-select" id="state_id" name="state_id">
                                <option value="">Select County / State</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3" id="zip_field" style="display: none;">
                            <label for="zip" class="form-label">Zip / Eircode</label>
                            <input type="text" class="form-control" id="zip" name="zip">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="unitSubmitBtn">
                        <i class="ph-check me-1"></i> Update
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-x me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-labelledby="deleteUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUnitModalLabel">
                    <i class="ph-warning me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="ph-warning-circle text-danger" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Are you sure you want to delete this unit?</h6>
                        <p class="text-muted mb-0">This action cannot be undone. All unit data will be permanently removed.</p>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <strong>Unit Details:</strong>
                    <div id="deleteUnitDetails" class="mt-2">
                        <!-- Unit details will be populated here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteUnitBtn">
                    <i class="ph-trash me-1"></i> Delete Unit
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Global variable to store the unit ID for deletion
    let unitToDelete = null;
    let deleteRedirectUrl = null;

    /**
     * Shows the delete confirmation modal with unit details
     * 
     * @param {number} unitId - The ID of the unit to delete
     * @param {object} unitData - The unit data to display in confirmation
     */
    window.showDeleteUnitModal = function(unitId, unitData) {
        unitToDelete = unitId;
        
        // Set redirect URL to block edit page if available
        @if($blockUnit->block)
            deleteRedirectUrl = '{{ route("blocks.edit", $blockUnit->block) }}';
        @else
            deleteRedirectUrl = '{{ route("blocks.index") }}';
        @endif
        
        // Populate unit details in the modal
        const detailsHtml = `
            <div class="row">
                <div class="col-6"><strong>Unit Code:</strong></div>
                <div class="col-6">${unitData.unit_code || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Unit Name:</strong></div>
                <div class="col-6">${unitData.unit_name || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Owner:</strong></div>
                <div class="col-6">${unitData.owners_name || 'N/A'}</div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Type:</strong></div>
                <div class="col-6">${unitData.unit_type?.name || 'N/A'}</div>
            </div>
        `;
        
        $('#deleteUnitDetails').html(detailsHtml);
        
        // Set up the confirm button to actually delete
        $('#confirmDeleteUnitBtn').off('click').on('click', function() {
            deleteUnit();
        });
        
        // Show the modal
        $('#deleteUnitModal').modal('show');
    };

    /**
     * Deletes a unit via AJAX
     */
    function deleteUnit() {
        if (!unitToDelete) {
            return;
        }
        
        // Show loading state
        const $confirmBtn = $('#confirmDeleteUnitBtn');
        const originalText = $confirmBtn.html();
        $confirmBtn.html('<i class="ph-spinner-gap ph-spin me-1"></i> Deleting...').prop('disabled', true);
        
        $.ajax({
            url: `/block-units/${unitToDelete}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                // Hide the modal
                $('#deleteUnitModal').modal('hide');
                
                // Show success message and redirect
                if (response && response.success) {
                    // Show success alert
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ph-check-circle me-2"></i>
                            Unit deleted successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    $('.page-content').prepend(alertHtml);
                    
                    // Redirect after a short delay
                    setTimeout(function() {
                        if (deleteRedirectUrl) {
                            window.location.href = deleteRedirectUrl;
                        } else {
                            window.location.href = '{{ route("blocks.index") }}';
                        }
                    }, 1500);
                } else {
                    // Show error and reset button
                    const alertHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ph-warning me-2"></i>
                            Error deleting unit. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    $('.page-content').prepend(alertHtml);
                    $confirmBtn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                // Hide the modal
                $('#deleteUnitModal').modal('hide');
                
                // Show error message
                let errorMessage = 'Error deleting unit. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ph-warning me-2"></i>
                        ${errorMessage}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('.page-content').prepend(alertHtml);
                
                // Reset button state
                $confirmBtn.html(originalText).prop('disabled', false);
            }
        });
    }

    @if($blockUnit->block)
    // ========================================
    // UNIT EDIT MODAL FUNCTIONS
    // ========================================
    
    // Global configuration for the unit edit modal
    window.blockId = {{ $blockUnit->block->id }};
    window.csrfToken = '{{ csrf_token() }}';
    window.routes = {
        blockUnits: {
            store: '{{ route("block-units.store") }}',
            showBase: '{{ url("block-units") }}'
        }
    };

    /**
     * Shows a message in the modal form
     */
    window.showMessage = function(containerId, type, message) {
        let $messageDiv = $('#' + containerId);
        
        if (!$messageDiv.length) {
            $messageDiv = $(`
                <div id="${containerId}" class="alert d-none" role="alert">
                    <i class="ph-check-circle me-2"></i>
                    <span class="message-text"></span>
                </div>
            `);
            $('#unitForm .modal-body').prepend($messageDiv);
        }
        
        $messageDiv.removeClass('alert-success alert-danger alert-info alert-warning')
                  .addClass(`alert-${type}`)
                  .removeClass('d-none');
        
        const $icon = $messageDiv.find('i');
        $icon.removeClass('ph-check-circle ph-warning ph-info-circle ph-x-circle');
        
        switch(type) {
            case 'success':
                $icon.addClass('ph-check-circle');
                break;
            case 'danger':
            case 'error':
                $icon.addClass('ph-x-circle');
                break;
            case 'warning':
                $icon.addClass('ph-warning');
                break;
            case 'info':
                $icon.addClass('ph-info-circle');
                break;
            default:
                $icon.addClass('ph-info-circle');
        }
        
        $messageDiv.find('.message-text').text(message);
        
        if (type === 'success') {
            setTimeout(function() {
                $messageDiv.addClass('d-none');
            }, 5000);
        }
    };

    /**
     * Clears/hides message in the modal
     */
    window.clearMessage = function(containerId) {
        const $messageDiv = $('#' + containerId);
        if ($messageDiv.length) {
            $messageDiv.addClass('d-none');
        }
    };

    /**
     * Opens the unit modal in Edit mode
     */
    window.openUnitModal = function(mode, id = null) {
        if (typeof $ === 'undefined') {
            return;
        }
        
        if (mode === 'edit' && id) {
            loadUnitForEdit(id);
        }
    };

    /**
     * Loads unit data and populates the modal for editing
     */
    function loadUnitForEdit(id) {
        if (typeof $ === 'undefined') {
            return;
        }
        
        $.ajax({
            url: `/block-units/${id}`,
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data && data.success) {
                    const unit = data.data;
                    const $modal = $('#unitModal');
                    const $modalLabel = $('#unitModalLabel');
                    const $form = $('#unitForm');
                    const $submitBtn = $('#unitSubmitBtn');
                    
                    // Set modal to edit mode
                    $modalLabel.text('Edit Unit');
                    $submitBtn.html('<i class="ph-check me-1"></i> Update');
                    $form.attr('action', `/block-units/${id}`);
                    
                    // Add PUT method for edit
                    if ($form.find('input[name="_method"]').length === 0) {
                        $form.append('<input type="hidden" name="_method" value="PUT">');
                    }
                    
                    // Initialize modal
                    initializeModal('unitModal', 'resident');
                    
                    // Populate form fields
                    $('#block_building_id').val(unit.block_building_id);
                    $('#block_unit_type_id').val(unit.block_unit_type_id);
                    $('#unit_code').val(unit.unit_code);
                    $('#unit_name').val(unit.unit_name);
                    $('#owners_name').val(unit.owners_name);
                    $('#salutation').val(unit.salutation);
                    $('#email').val(unit.email);
                    $('#mobile_no').val(unit.mobile_no);
                    $('#phone_number').val(unit.phone_number);
                    $('#letting_agent').val(unit.letting_agent);
                    $('#misc_info').val(unit.misc_info);
                    
                    const residentValue = unit.resident ? '1' : '0';
                    $('#resident').val(residentValue);
                    
                    // Handle address fields based on resident status
                    if (!unit.resident) {
                        const addressFields = ['address1_field', 'address2_field', 'address3_field', 'country_field', 'state_field', 'zip_field'];
                        addressFields.forEach(function(fieldId) {
                            $('#' + fieldId).show();
                        });
                        
                        $('#address1').val(unit.address1);
                        $('#address2').val(unit.address2);
                        $('#address3').val(unit.address3);
                        $('#zip').val(unit.zip);
                        
                        if (unit.country_id) {
                            $('#country_id').val(unit.country_id).trigger('change');
                            
                            loadStates(unit.country_id, 'state_id').then(function(states) {
                                if (unit.state_id) {
                                    $('#state_id').val(unit.state_id).trigger('change');
                                }
                                $modal.on('shown.bs.modal', function() {
                                    toggleAddressFields('resident');
                                });
                                $modal.modal('show');
                            }).catch(function(error) {
                                $modal.on('shown.bs.modal', function() {
                                    toggleAddressFields('resident');
                                });
                                $modal.modal('show');
                            });
                        } else {
                            $modal.on('shown.bs.modal', function() {
                                toggleAddressFields('resident');
                            });
                            $modal.modal('show');
                        }
                    } else {
                        $modal.on('shown.bs.modal', function() {
                            toggleAddressFields('resident');
                        });
                        $modal.modal('show');
                    }
                } else {
                    showMessage('unitMessage', 'danger', 'Error loading unit data');
                }
            },
            error: function(xhr, status, error) {
                showMessage('unitMessage', 'danger', 'Error loading unit data');
            }
        });
    }

    /**
     * Handles country dropdown change event
     */
    window.handleCountryChange = function(countryId) {
        if (countryId) {
            loadStates(countryId, 'state_id');
        } else {
            const $stateSelect = $('#state_id');
            if ($stateSelect.length) {
                $stateSelect.html('<option value="">Select County / State</option>');
            }
        }
    };

    /**
     * Loads states/provinces based on selected country
     */
    function loadStates(countryId, stateSelectId) {
        const $stateSelect = $('#' + stateSelectId);

        if (!countryId || !$stateSelect.length) {
            if ($stateSelect.length) {
                $stateSelect.html('<option value="">Select County / State</option>');
            }
            return $.Deferred().resolve([]).promise();
        }

        $stateSelect.html('<option value="">Loading states...</option>').prop('disabled', true);

        return $.ajax({
            url: `/api/states/${countryId}`,
            method: 'GET',
            dataType: 'json'
        }).then(function(response) {
            const states = Array.isArray(response) ? response : (response && Array.isArray(response.data) ? response.data : []);

            let options = '<option value="">Select County / State</option>';
            states.forEach(function(state, index) {
                const id = state && (state.id ?? state.value);
                const name = state && (state.name ?? state.text);
                if (id != null && name != null) {
                    options += `<option value="${id}">${name}</option>`;
                }
            });

            $stateSelect.html(options).prop('disabled', false);
            $('#state_field').show();
            return states;
        }).catch(function(error) {
            $stateSelect.html('<option value="">Error loading states</option>').prop('disabled', false);
            return [];
        });
    }

    /**
     * Toggles address fields visibility based on resident selection
     */
    function toggleAddressFields(residentSelectId) {
        const $residentSelect = $('#' + residentSelectId);
        
        if ($residentSelect.length) {
            $residentSelect.off('change.toggleAddress');
            
            const addressFields = [
                'address1_field',
                'address2_field', 
                'address3_field',
                'country_field',
                'state_field',
                'zip_field'
            ];
            
            $residentSelect.on('change.toggleAddress', function() {
                if (this.value === '0') {
                    addressFields.forEach(function(fieldId) {
                        $('#' + fieldId).show();
                    });
                    $('#address1').attr('required', 'required');
                    $('#country_id').attr('required', 'required');
                    $('#state_id').attr('required', 'required');
                } else {
                    addressFields.forEach(function(fieldId) {
                        $('#' + fieldId).hide();
                    });
                    addressFields.forEach(function(fieldId) {
                        $('#' + fieldId).find('input, select').removeAttr('required');
                    });
                }
            });
            
            const currentValue = $residentSelect.val();
            if (currentValue === '0') {
                addressFields.forEach(function(fieldId) {
                    $('#' + fieldId).show();
                });
                $('#address1').attr('required', 'required');
                $('#country_id').attr('required', 'required');
                $('#state_id').attr('required', 'required');
            } else {
                addressFields.forEach(function(fieldId) {
                    $('#' + fieldId).hide();
                });
                addressFields.forEach(function(fieldId) {
                    $('#' + fieldId).find('input, select').removeAttr('required');
                });
            }
        }
    }

    /**
     * Initialize modal with default state
     */
    function initializeModal(modalId, residentId) {
        const addressFields = [
            'address1_field',
            'address2_field',
            'address3_field',
            'country_field',
            'state_field',
            'zip_field'
        ];
        
        addressFields.forEach(function(fieldId) {
            $('#' + fieldId).hide();
        });
        
        const $residentSelect = $('#' + residentId);
        $residentSelect.val('1');
        
        toggleAddressFields(residentId);
    }

    /**
     * Refreshes building/core dropdown
     */
    function refreshBuildingCoreDropdowns() {
        const blockId = window.blockId || $('input[name="block_id"]').val();
        const $buildingSelect = $('#block_building_id');
        const currentBuildingValue = $buildingSelect.val();
        
        $buildingSelect.html('<option value="">Loading buildings...</option>').prop('disabled', true);
        
        $.ajax({
            url: '/api/blocks/' + blockId + '/buildings',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let buildingOptions = '<option value="">Select Building/Core</option>';
                
                if (response && response.length > 0) {
                    response.forEach(function(building) {
                        const selected = building.id == currentBuildingValue ? 'selected' : '';
                        buildingOptions += `<option value="${building.id}" ${selected}>${building.name}</option>`;
                    });
                }
                
                $buildingSelect.html(buildingOptions).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                $buildingSelect.html('<option value="">Error loading buildings</option>').prop('disabled', false);
            }
        });
    }

    // Handle unit form submission
    $('#unitForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const formData = new FormData(this);
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                if (data && data.success) {
                    showMessage('unitMessage', 'success', 'Unit updated successfully!');
                    
                    setTimeout(function() {
                        $('#unitModal').modal('hide');
                        // Reload the page to show updated data
                        window.location.reload();
                    }, 800);
                } else {
                    showMessage('unitMessage', 'danger', (data && data.message) || 'Error updating unit');
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Error updating unit. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showMessage('unitMessage', 'danger', errorMessage);
            }
        });
    });

    // Clear messages when unit modal is opened
    $('#unitModal').on('show.bs.modal', function() {
        setTimeout(function() {
            clearMessage('unitMessage');
        }, 50);
        setTimeout(function() {
            toggleAddressFields('resident');
        }, 100);
    });
    @endif
});
</script>
@endpush
@endsection

