@extends('layouts.master')

@section('title')
    Block Overview - PROMAN
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ph-buildings me-2 text-primary"></i>
                        BLOCK OVERVIEW
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blocks.index') }}">Blocks</a></li>
                            <li class="breadcrumb-item active">Block Overview</li>
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

        <!-- Block Overview Header -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body bg-gradient-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="mb-1 text-white">{{ $block->name ?? 'Unnamed Block' }}</h2>
                                <p class="mb-0 text-white-50">Block Number: #{{ str_pad($block->id ?? 0, 6, '0', STR_PAD_LEFT) }}</p>
                                <div class="mt-2">
                                    <span class="badge bg-white bg-opacity-25 text-white me-2">
                                        <i class="ph-buildings me-1"></i>{{ $block->blockType->name ?? 'N/A' }}
                                    </span>
                                    <span class="badge bg-white bg-opacity-25 text-white">
                                        <i class="ph-users me-1"></i>{{ $block->no_of_units ?? 0 }} Units
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="d-flex flex-column align-items-md-end">
                                    <div class="mb-2">
                                        <i class="ph-calendar text-white-50 me-2"></i>
                                        <span class="text-white-50">Created: {{ $block->created_at ? $block->created_at->format('d M, Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <i class="ph-clock text-white-50 me-2"></i>
                                        <span class="text-white-50">{{ $block->created_at ? $block->created_at->format('h:i A') : 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('blocks.edit', $block) }}" class="btn btn-light btn-sm">
                                            <i class="ph-pencil me-2"></i>Edit Block
                                        </a>
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

        <!-- Quick Statistics Cards -->
        <div class="row">
            <!-- Block Information -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Block Info</h6>
                            <div class="bg-primary bg-opacity-10 p-2 rounded">
                                <i class="ph-buildings text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="text-start">
                            <p class="mb-1"><strong>Type:</strong> {{ $block->blockType->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Units:</strong> {{ $block->no_of_units ?? 0 }}</p>
                            <p class="mb-1"><strong>Car Spaces:</strong> {{ $block->car_spaces ?? 0 }}</p>
                            <p class="mb-0"><strong>Inspections/Year:</strong> {{ $block->inspection_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Details -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Management</h6>
                            <div class="bg-success bg-opacity-10 p-2 rounded">
                                <i class="ph-truck text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="text-start">
                            <p class="mb-1"><strong>Company:</strong> {{ $block->management_company ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Manager:</strong> {{ $block->blockManager->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Owner:</strong> {{ $block->user->name ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Created By:</strong> {{ $block->creator->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Block Address Details -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Block Address</h6>
                            <div class="bg-warning bg-opacity-10 p-2 rounded">
                                <i class="ph-file-text text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="text-start">
                            <p class="mb-1"><strong>Address:</strong> {{ $block->address1 ?? 'N/A' }}</p>
                            @if($block->address2)
                                <p class="mb-1"><strong>Company Add:</strong> {{ $block->address2 }}</p>
                            @endif
                            <p class="mb-1"><strong>Country:</strong> {{ $block->country->country_name ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>State:</strong> {{ $block->state->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Block Statistics -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Statistics</h6>
                            <div class="bg-info bg-opacity-10 p-2 rounded">
                                <i class="ph-currency-dollar text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="text-start">
                            <p class="mb-1"><strong>Buildings:</strong> {{ $block->buildings ? $block->buildings->count() : 0 }}</p>
                            <p class="mb-1"><strong>Contractors:</strong> {{ $block->contractors ? $block->contractors->count() : 0 }}</p>
                            <p class="mb-1"><strong>Total Issues:</strong> {{ $totalIssues ?? 0 }}</p>
                            <p class="mb-0"><strong>Site Visits:</strong> {{ $totalInspections ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Tabbed Content -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-0">
                    <div class="card-body p-0">
                        <!-- Modern Nav Pills Tabs -->
                        <div class="border-bottom">
                            <ul class="nav nav-pills arrow-navtabs nav-secondary gap-2 flex-grow-1 order-2 order-lg-1 px-3 pt-3" id="blockShowTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="block-info-tab" data-bs-toggle="tab" href="#block-info" role="tab" aria-controls="block-info" aria-selected="true">
                                        <i class="ph-info me-1"></i>Block Information
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="building-core-tab" data-bs-toggle="tab" href="#building-core" role="tab" aria-controls="building-core" aria-selected="false" tabindex="-1">
                                        <i class="ph-buildings me-1"></i>Building Core
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="contractors-tab" data-bs-toggle="tab" href="#contractors" role="tab" aria-controls="contractors" aria-selected="false" tabindex="-1">
                                        <i class="ph-users me-1"></i>Contractors
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="units-tab" data-bs-toggle="tab" href="#units" role="tab" aria-controls="units" aria-selected="false" tabindex="-1">
                                        <i class="ph-house me-1"></i>Units
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="site-visit-tab" data-bs-toggle="tab" href="#site-visit" role="tab" aria-controls="site-visit" aria-selected="false" tabindex="-1">
                                        <i class="ph-map-pin me-1"></i>Site Visit
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="inspections-tab" data-bs-toggle="tab" href="#inspections" role="tab" aria-controls="inspections" aria-selected="false" tabindex="-1">
                                        <i class="ph-clipboard-text me-1"></i>Inspections
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="issues-tab" data-bs-toggle="tab" href="#issues" role="tab" aria-controls="issues" aria-selected="false" tabindex="-1">
                                        <i class="ph-warning me-1"></i>Issues
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="work-orders-tab" data-bs-toggle="tab" href="#work-orders" role="tab" aria-controls="work-orders" aria-selected="false" tabindex="-1">
                                        <i class="ph-wrench me-1"></i>Work Orders
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content" id="blockShowTabContent">
                            <!-- Block Information Tab -->
                            <div class="tab-pane fade show active" id="block-info" role="tabpanel" aria-labelledby="block-info-tab">
                                <div class="p-4">
                                    @include('blocks.tabs.block-info')
                                </div>
                            </div>

                            <!-- Building/Core Tab -->
                            <div class="tab-pane fade" id="building-core" role="tabpanel" aria-labelledby="building-core-tab">
                                <div class="p-4">
                                    @include('blocks.tabs.building-core')
                                </div>
                            </div>

                            <!-- Contractors Tab -->
                            <div class="tab-pane fade" id="contractors" role="tabpanel" aria-labelledby="contractors-tab">
                                <div class="p-4">
                                    @include('blocks.tabs.contractors')
                                </div>
                            </div>

                            <!-- Units Tab -->
                            <div class="tab-pane fade" id="units" role="tabpanel" aria-labelledby="units-tab">
                                <div class="p-4">
                                    @include('blocks.tabs.units')
                                </div>
                            </div>

                            <!-- Site Visit Tab -->
                            <div class="tab-pane fade" id="site-visit" role="tabpanel" aria-labelledby="site-visit-tab">
                                <div class="p-4">
                                    @include('blocks.tabs.site-visit')
                                </div>
                            </div>

                            <!-- Inspections Tab -->
                            <div class="tab-pane fade" id="inspections" role="tabpanel" aria-labelledby="inspections-tab">
                                <div class="p-4">
                                    @include('blocks.tabs.inspections')
                                </div>
                            </div>

                            <!-- Issues Tab -->
                            <div class="tab-pane fade" id="issues" role="tabpanel" aria-labelledby="issues-tab">
                                <div class="p-4">
                                    @include('blocks.tabs.issues')
                                </div>
                            </div>

                            <!-- Work Orders Tab -->
                            <div class="tab-pane fade" id="work-orders" role="tabpanel" aria-labelledby="work-orders-tab">
                                <div class="p-4">
                                    @include('blocks.tabs.work-orders')
                                </div>
                            </div>
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

/* Enhanced arrow-navtabs styling for block tabs */
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

/* Section spacing */
.row + .row {
    margin-top: 2rem !important;
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
        min-width: auto;
        border-radius: 6px 6px 0 0;
    }
    
    .arrow-navtabs .nav-link.active::after {
        border-left-width: 6px;
        border-right-width: 6px;
        border-top-width: 6px;
        bottom: -6px;
    }
    
    .arrow-navtabs .nav-link.active {
        transform: translateY(-1px);
    }
    
    .arrow-navtabs .nav-link:hover:not(.active) {
        transform: none;
    }
}

@media (max-width: 1200px) {
    .arrow-navtabs .nav-link {
        padding: 10px 16px;
        font-size: 0.9rem;
        border-radius: 6px 6px 0 0;
    }
}

/* Large screen enhancements */
@media (min-width: 1400px) {
    .arrow-navtabs .nav-link {
        padding: 14px 24px;
        font-size: 1rem;
    }
}

/* Tab container overflow prevention */
.card-body {
    overflow-x: hidden;
    max-width: 100%;
}

/* Enhanced tab spacing and shadows */
.arrow-navtabs .nav-link {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.arrow-navtabs .nav-link.active {
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Tab content animations */
.tab-pane {
    transition: opacity 0.3s ease-in-out;
}

.tab-pane.fade {
    opacity: 0;
}

.tab-pane.fade.show {
    opacity: 1;
}

/* Smooth transitions for tab links */
.arrow-navtabs .nav-link::before {
    transition: all 0.3s ease;
}

/* Tab content styling */
.tab-content {
    border-top: 1px solid #e9ecef;
    margin-top: 0;
}

/* Modern tab enhancements */
.arrow-navtabs .nav-link {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.arrow-navtabs .nav-link.active {
    background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
    transform: translateY(-2px);
}

.arrow-navtabs .nav-link:hover:not(.active) {
    transform: translateY(-1px);
    background-color: rgba(108, 117, 125, 0.15);
}

/* Enhanced focus states */
.arrow-navtabs .nav-link:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(73, 80, 87, 0.25);
}

/* Improved active arrow */
.arrow-navtabs .nav-link.active::after {
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

/* Ensure tabs don't interfere with sidebar */
.tab-content {
    position: relative;
    z-index: 1;
}

.tab-pane {
    position: relative;
    z-index: 1;
}

/* Prevent any tab content from affecting sidebar */
#blockShowTabContent {
    overflow: visible;
}

#blockShowTabContent .tab-pane {
    overflow: visible;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#blockShowTabs a'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
    
    // Add active class to current tab and handle tabindex
    var tabs = document.querySelectorAll('#blockShowTabs .nav-link');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            // Remove active class and set tabindex="-1" for all tabs
            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('tabindex', '-1');
                t.setAttribute('aria-selected', 'false');
            });
            
            // Add active class and remove tabindex for current tab
            this.classList.add('active');
            this.removeAttribute('tabindex');
            this.setAttribute('aria-selected', 'true');
        });
    });
    
    // Handle tab events for proper accessibility
    tabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function() {
            // Update tabindex for all tabs when a tab is shown
            tabs.forEach(t => {
                if (t === this) {
                    t.removeAttribute('tabindex');
                    t.setAttribute('aria-selected', 'true');
                } else {
                    t.setAttribute('tabindex', '-1');
                    t.setAttribute('aria-selected', 'false');
                }
            });
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
});
</script>
@endpush
@endsection 