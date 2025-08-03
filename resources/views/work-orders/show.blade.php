@extends('layouts.master')
@section('title')
    Work Order Details - PROMAN
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
                    <h4 class="mb-sm-0">Work Order Details</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">Work Orders</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Work Order: {{ $workOrder->code }}</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('work-orders.edit', $workOrder) }}" class="btn btn-primary btn-sm">
                                    <i class="ri-edit-line me-1"></i> Edit
                                </a>
                                <a href="{{ route('work-orders.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ri-arrow-left-line me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Code:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->code }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Property ID:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->property_id }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Contractor ID:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->contractor_id }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Priority:</strong></div>
                                            <div class="col-sm-8">
                                                @php
                                                    $priorityColors = [
                                                        1 => 'success',
                                                        2 => 'info',
                                                        3 => 'warning',
                                                        4 => 'danger',
                                                        5 => 'dark'
                                                    ];
                                                    $color = $priorityColors[$workOrder->priority] ?? 'info';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">{{ $workOrder->priority_label }}</span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Status:</strong></div>
                                            <div class="col-sm-8">
                                                <span class="badge bg-{{ $workOrder->common_status_id == 1 ? 'success' : 'secondary' }}">
                                                    {{ $workOrder->status_text }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Issue Details -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Issue Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Category:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->issue_category }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Type:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->issue_type }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Work Type:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->type }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Type ID:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->type_id }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fault Description -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Fault Description</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $workOrder->fault_description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Schedule Information -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Schedule</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Issued Date:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->issued_date }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Deadline:</strong></div>
                                            <div class="col-sm-8">
                                                <span class="badge bg-info">{{ $workOrder->deadline }}</span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Pricing:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->pricing }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Contact Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Name:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->contact_name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Phone:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->contact_number ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Email:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->contact_email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Preferred Schedule -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Preferred Schedule</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Day:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->preferred_day ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Time From:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->time_from ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Time To:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->time_to ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Additional Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Note:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->note ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Report:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->report ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">User Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>User:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->user->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Created By:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->creator->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Created At:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->created_at->format('M d, Y H:i') }}</div>
                                        </div>
                                        @if($workOrder->updated_by)
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Updated By:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->updater->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Updated At:</strong></div>
                                            <div class="col-sm-8">{{ $workOrder->updated_at->format('M d, Y H:i') }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Images -->
                        @if($workOrder->images->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Images ({{ $workOrder->images->count() }})</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($workOrder->images as $image)
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <img src="{{ $image->image_url }}" class="card-img-top" alt="Work Order Image" style="height: 200px; object-fit: cover;">
                                                    <div class="card-body p-2">
                                                        <small class="text-muted">{{ $image->image }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <!-- add your js here -->
@endsection 