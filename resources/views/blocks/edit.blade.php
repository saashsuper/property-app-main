@extends('layouts.master')

@section('title')
    Edit Block - PROMAN
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Block</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blocks.index') }}">Blocks</a></li>
                            <li class="breadcrumb-item active">Edit</li>
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
                        <h4 class="card-title">Edit Block Information</h4>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Animation Nav Tabs -->
                        <ul class="nav nav-tabs nav-tabs-custom arrow-navtabs" id="blockEditTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="basic-details-tab" data-bs-toggle="tab" href="#basic-details" role="tab" aria-controls="basic-details" aria-selected="true">
                                    <span>Basic Details</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="block-info-tab" data-bs-toggle="tab" href="#block-info" role="tab" aria-controls="block-info" aria-selected="false">
                                    <span>Block Information</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="building-core-tab" data-bs-toggle="tab" href="#building-core" role="tab" aria-controls="building-core" aria-selected="false">
                                    <span>Building/Core</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="contractors-tab" data-bs-toggle="tab" href="#contractors" role="tab" aria-controls="contractors" aria-selected="false">
                                    <span>Contractors</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="units-tab" data-bs-toggle="tab" href="#units" role="tab" aria-controls="units" aria-selected="false">
                                    <span>Units</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="site-visit-tab" data-bs-toggle="tab" href="#site-visit" role="tab" aria-controls="site-visit" aria-selected="false">
                                    <span>Site Visit</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="inspections-tab" data-bs-toggle="tab" href="#inspections" role="tab" aria-controls="inspections" aria-selected="false">
                                    <span>Inspections</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="issues-tab" data-bs-toggle="tab" href="#issues" role="tab" aria-controls="issues" aria-selected="false">
                                    <span>Issues</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="work-orders-tab" data-bs-toggle="tab" href="#work-orders" role="tab" aria-controls="work-orders" aria-selected="false">
                                    <span>Work Orders</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-4" id="blockEditTabContent">
                            <!-- Basic Details Tab -->
                            <div class="tab-pane fade show active" id="basic-details" role="tabpanel" aria-labelledby="basic-details-tab">
                                @include('blocks.tabs.basic-details')
                            </div>

                            <!-- Block Information Tab -->
                            <div class="tab-pane fade" id="block-info" role="tabpanel" aria-labelledby="block-info-tab">
                                @include('blocks.tabs.block-info')
                            </div>

                            <!-- Building/Core Tab -->
                            <div class="tab-pane fade" id="building-core" role="tabpanel" aria-labelledby="building-core-tab">
                                @include('blocks.tabs.building-core')
                            </div>

                            <!-- Contractors Tab -->
                            <div class="tab-pane fade" id="contractors" role="tabpanel" aria-labelledby="contractors-tab">
                                @include('blocks.tabs.contractors')
                            </div>

                            <!-- Units Tab -->
                            <div class="tab-pane fade" id="units" role="tabpanel" aria-labelledby="units-tab">
                                @include('blocks.tabs.units')
                            </div>

                            <!-- Site Visit Tab -->
                            <div class="tab-pane fade" id="site-visit" role="tabpanel" aria-labelledby="site-visit-tab">
                                @include('blocks.tabs.site-visit')
                            </div>

                            <!-- Inspections Tab -->
                            <div class="tab-pane fade" id="inspections" role="tabpanel" aria-labelledby="inspections-tab">
                                @include('blocks.tabs.inspections')
                            </div>

                            <!-- Issues Tab -->
                            <div class="tab-pane fade" id="issues" role="tabpanel" aria-labelledby="issues-tab">
                                @include('blocks.tabs.issues')
                            </div>

                            <!-- Work Orders Tab -->
                            <div class="tab-pane fade" id="work-orders" role="tabpanel" aria-labelledby="work-orders-tab">
                                @include('blocks.tabs.work-orders')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Enhanced arrow-navtabs styling for block tabs */
.arrow-navtabs {
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 0;
}

.arrow-navtabs .nav-item {
    margin-bottom: -2px;
}

.arrow-navtabs .nav-link {
    border: none;
    border-radius: 0;
    padding: 12px 20px;
    font-weight: 500;
    color: #6c757d;
    background: transparent;
    position: relative;
    transition: all 0.3s ease;
}

.arrow-navtabs .nav-link:hover {
    color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.05);
    border-color: transparent;
}

.arrow-navtabs .nav-link.active {
    color: var(--bs-primary);
    background-color: #fff;
    border-color: transparent;
    border-bottom: 2px solid var(--bs-primary);
}

.arrow-navtabs .nav-link.active::before {
    border-top-color: var(--bs-primary);
    border-width: 8px;
    bottom: -16px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .arrow-navtabs .nav-link {
        padding: 8px 12px;
        font-size: 0.875rem;
    }
    
    .arrow-navtabs .nav-link.active::before {
        border-width: 6px;
        bottom: -12px;
    }
}

/* Smooth transitions */
.arrow-navtabs .nav-link::before {
    transition: all 0.3s ease;
}

/* Tab content styling */
.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    background-color: #fff;
}

.tab-pane {
    padding: 1.5rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country_id');
    const stateSelect = document.getElementById('state_id');
    const stateOptions = stateSelect.querySelectorAll('option[data-country]');

    function updateStates() {
        const selectedCountryId = countrySelect.value;
        
        // Hide all state options
        stateOptions.forEach(option => {
            option.style.display = 'none';
        });
        
        // Show only states for selected country
        if (selectedCountryId) {
            stateOptions.forEach(option => {
                if (option.dataset.country === selectedCountryId) {
                    option.style.display = '';
                }
            });
        }
        
        // Reset state selection if it doesn't belong to selected country
        const currentStateId = stateSelect.value;
        const currentStateOption = stateSelect.querySelector(`option[value="${currentStateId}"]`);
        if (currentStateOption && currentStateOption.dataset.country !== selectedCountryId) {
            stateSelect.value = '';
        }
    }

    // Initial update
    updateStates();

    // Update states when country changes
    countrySelect.addEventListener('change', updateStates);

    // Initialize Bootstrap tabs
    const triggerTabList = document.querySelectorAll('#blockEditTabs a[data-bs-toggle="tab"]');
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', event => {
            event.preventDefault();
            tabTrigger.show();
        });
    });
});
</script>
@endpush 