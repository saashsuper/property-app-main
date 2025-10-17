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

                        <!-- Modern Nav Pills Tabs -->
                        <ul class="nav nav-pills arrow-navtabs nav-secondary gap-2 flex-grow-1 order-2 order-lg-1" id="blockEditTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="basic-details-tab" data-bs-toggle="tab" href="#basic-details" role="tab" aria-controls="basic-details" aria-selected="true">
                                    Basic Details
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="block-info-tab" data-bs-toggle="tab" href="#block-info" role="tab" aria-controls="block-info" aria-selected="false">
                                    Block Information
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="building-core-tab" data-bs-toggle="tab" href="#building-core" role="tab" aria-controls="building-core" aria-selected="false" dusk="tab-building-core">
                                    Building/Core
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="units-tab" data-bs-toggle="tab" href="#units" role="tab" aria-controls="units" aria-selected="false" dusk="tab-units">
                                    Units
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="contractors-tab" data-bs-toggle="tab" href="#contractors" role="tab" aria-controls="contractors" aria-selected="false">
                                    Contractors
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="site-visit-tab" data-bs-toggle="tab" href="#site-visit" role="tab" aria-controls="site-visit" aria-selected="false">
                                    Site Visit
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="inspections-tab" data-bs-toggle="tab" href="#inspections" role="tab" aria-controls="inspections" aria-selected="false">
                                    Inspections
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="issues-tab" data-bs-toggle="tab" href="#issues" role="tab" aria-controls="issues" aria-selected="false">
                                    Issues
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="work-orders-tab" data-bs-toggle="tab" href="#work-orders" role="tab" aria-controls="work-orders" aria-selected="false">
                                    Work Orders
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
                                @include('blocks.tabs.edit.block-information.index')
                            </div>

                            <!-- Building/Core Tab -->
                            <div class="tab-pane fade" id="building-core" role="tabpanel" aria-labelledby="building-core-tab">
                                @include('blocks.tabs.edit.buildings.index')
                            </div>

                            <!-- Units Tab -->
                            <div class="tab-pane fade" id="units" role="tabpanel" aria-labelledby="units-tab">
                                @include('blocks.tabs.edit.units.index')
                            </div>

                            <!-- Contractors Tab -->
                            <div class="tab-pane fade" id="contractors" role="tabpanel" aria-labelledby="contractors-tab">
                                @include('blocks.tabs.edit.contractors.index')
                            </div>

                            <!-- Site Visit Tab -->
                            <div class="tab-pane fade" id="site-visit" role="tabpanel" aria-labelledby="site-visit-tab">
                                @include('blocks.tabs.edit.site-visits.index')
                            </div>

                            <!-- Inspections Tab -->
                            <div class="tab-pane fade" id="inspections" role="tabpanel" aria-labelledby="inspections-tab">
                                @include('blocks.tabs.edit.inspections.index')
                            </div>

                            <!-- Issues Tab -->
                            <div class="tab-pane fade" id="issues" role="tabpanel" aria-labelledby="issues-tab">
                                @include('blocks.tabs.edit.issues.index')
                            </div>

                            <!-- Work Orders Tab -->
                            <div class="tab-pane fade" id="work-orders" role="tabpanel" aria-labelledby="work-orders-tab">
                                @include('blocks.tabs.edit.work-orders.index')
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
<!-- Dropzone CSS -->
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<style>
/* Modern Nav Pills Tabs Styling */
.arrow-navtabs {
    border: none;
    margin-bottom: 0;
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
    max-width: 100%;
    flex-wrap: nowrap;
    background: transparent;
}

/* Hide scrollbar for Chrome, Safari and Opera */
.arrow-navtabs::-webkit-scrollbar {
    display: none;
}

.arrow-navtabs .nav-item {
    margin-bottom: 0;
    flex-shrink: 0;
    white-space: nowrap;
}

.arrow-navtabs .nav-link {
    border: none;
    border-radius: 8px 8px 0 0;
    padding: 12px 20px;
    font-weight: 500;
    color: #6c757d;
    background: transparent;
    position: relative;
    transition: all 0.3s ease;
    margin-right: 4px;
}

.arrow-navtabs .nav-link:hover {
    color: #495057;
    background-color: rgba(108, 117, 125, 0.1);
    border-color: transparent;
}

.arrow-navtabs .nav-link.active {
    color: #fff;
    background-color: #495057;
    border-color: transparent;
    position: relative;
}

.arrow-navtabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-top: 8px solid #495057;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .arrow-navtabs .nav-link {
        padding: 8px 12px;
        font-size: 0.875rem;
        border-radius: 6px 6px 0 0;
    }
    
    .arrow-navtabs .nav-link.active::after {
        border-left-width: 6px;
        border-right-width: 6px;
        border-top-width: 6px;
        bottom: -6px;
    }
}

@media (max-width: 1200px) {
    .arrow-navtabs .nav-link {
        padding: 10px 16px;
        font-size: 0.9rem;
        border-radius: 6px 6px 0 0;
    }
}

/* Enhanced tab spacing and shadows */
.arrow-navtabs .nav-link {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.arrow-navtabs .nav-link.active {
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Tab container overflow prevention */
.card-body {
    overflow-x: hidden;
    max-width: 100%;
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

    // Initialize Bootstrap tabs
    const triggerTabList = document.querySelectorAll('#blockEditTabs a[data-bs-toggle="tab"]');
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', event => {
            event.preventDefault();
            tabTrigger.show();
        });
    });
    
    // Handle horizontal scroll for tabs on mobile
    const tabContainer = document.querySelector('.arrow-navtabs');
    if (tabContainer) {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        tabContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - tabContainer.offsetLeft;
            scrollLeft = tabContainer.scrollLeft;
        });
        
        tabContainer.addEventListener('mouseleave', () => {
            isDown = false;
        });
        
        tabContainer.addEventListener('mouseup', () => {
            isDown = false;
        });
        
        tabContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - tabContainer.offsetLeft;
            const walk = (x - startX) * 2;
            tabContainer.scrollLeft = scrollLeft - walk;
        });
    }
    
    console.log('Tabs initialized');
});
</script>

<!-- Dropzone JS -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
@endpush 