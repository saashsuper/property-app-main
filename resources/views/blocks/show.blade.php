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

        <!-- Main Content Layout -->
        <div class="row">
            <!-- Left Column - Statistics Cards -->
            <div class="col-lg-8">
                <!-- Quick Statistics Cards -->
                <div class="row">
                    <!-- Block Information -->
                    <div class="col-lg-6 col-md-6">
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
                    <div class="col-lg-6 col-md-6">
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
                </div>

                <!-- Block Address Details -->
                <div class="row mt-3">
                    <div class="col-lg-6 col-md-6">
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
                    <div class="col-lg-6 col-md-6">
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
            </div>

            <!-- Right Column - Image Gallery -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="ph-images me-2 text-primary"></i>Block Images
                            </h5>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadImagesModal">
                                <i class="ph-plus me-1"></i>Upload Images
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <div id="imageGallery" class="row g-2">
                            @if($block->images && $block->images->count() > 0)
                                @foreach($block->images as $image)
                                    <div class="col-6" data-image-id="{{ $image->id }}">
                                        <div class="position-relative">
                                            <img src="{{ $image->url }}" 
                                                 class="img-fluid rounded" 
                                                 style="height: 120px; width: 100%; object-fit: cover; cursor: pointer;"
                                                 alt="{{ $image->original_name }}"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imagePreviewModal"
                                                 data-image-url="{{ $image->url }}"
                                                 data-image-name="{{ $image->original_name }}"
                                                 data-image-id="{{ $image->id }}">
                                            
                                            @if($image->is_primary)
                                                <span class="badge bg-primary position-absolute top-0 start-0 m-1" style="font-size: 0.7rem;">
                                                    Primary
                                                </span>
                                            @endif
                                            
                                            <div class="position-absolute top-0 end-0 m-1">
                                                <button type="button" class="btn btn-danger btn-sm delete-image-btn"
                                                        data-image-id="{{ $image->id }}" 
                                                        title="Delete Image"
                                                        style="padding: 0.25rem 0.4rem; font-size: 0.7rem;">
                                                    <i class="ph-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12 text-center py-4">
                                    <i class="ph-image text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">No images uploaded yet</p>
                                    <small class="text-muted">Click "Upload Images" to add photos</small>
                                </div>
                            @endif
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
                                    @include('blocks.tabs.edit.issues')
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

/* Section spacing */
.row + .row {
    margin-top: 2rem !important;
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

<!-- Upload Images Modal -->
<div class="modal fade" id="uploadImagesModal" tabindex="-1" aria-labelledby="uploadImagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadImagesModalLabel">
                    <i class="ph-upload me-2"></i>Upload Block Images
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Dropzone Container -->
                <div class="mb-3">
                    <label class="form-label">Upload Images <span class="text-danger">*</span></label>
                    <div id="imageDropzone" class="dropzone">
                        <div class="dz-message">
                            <div class="mb-3">
                                <i class="ph-cloud-upload display-4 text-muted"></i>
                            </div>
                            <h4>Drop images here or click to upload</h4>
                            <p class="text-muted font-size-16">
                                <strong>Requirements:</strong><br>
                                • Maximum 10 images<br>
                                • Each image max 5MB<br>
                                • Total size max 15MB<br>
                                • Formats: JPEG, PNG, JPG, GIF
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div id="uploadStatus" class="mt-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary" id="clearBtn">
                    <i class="ph-x me-1"></i>Clear All
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewModalLabel">
                    <span id="currentImageInfo">Image Preview</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Image Carousel -->
                <div id="imageCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner" id="carouselInner">
                        <!-- Images will be dynamically added here -->
                    </div>
                    
                    <!-- Navigation Arrows -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                
                <!-- Image Info -->
                <div class="p-3 text-center bg-light">
                    <h6 id="previewImageName" class="mb-1"></h6>
                    <small class="text-muted" id="imageCounter"></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteImageModal" tabindex="-1" aria-labelledby="deleteImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteImageModalLabel">
                    <i class="ph-warning text-warning me-2"></i>Delete Image
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this image?</p>
                <p class="text-muted mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="ph-trash me-1"></i>Yes, Delete Image
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let dropzone;
    
    // Initialize Dropzone when modal is shown
    $('#uploadImagesModal').on('shown.bs.modal', function() {
        if (!dropzone) {
            initializeDropzone();
        }
    });
    
    // Initialize Dropzone
    function initializeDropzone() {
        // Disable auto discover to prevent conflicts
        Dropzone.autoDiscover = false;
        
        dropzone = new Dropzone("#imageDropzone", {
            url: '{{ route("blocks.images.upload", $block->id) }}',
            paramName: "images",
            uploadMultiple: true,
            parallelUploads: 10,
            maxFiles: 10,
            maxFilesize: 5, // 5MB per file
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            dictDefaultMessage: "Drop images here or click to upload",
            dictRemoveFile: "Remove",
            dictCancelUpload: "Cancel",
            dictUploadCanceled: "Upload canceled",
            dictInvalidFileType: "You can't upload files of this type.",
            dictFileTooBig: "File is too big. Max filesize: 5MB.",
            dictMaxFilesExceeded: "You can not upload more than 10 files.",
            dictResponseError: "Server responded with an error.",
            dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
            dictRemoveFileConfirmation: "Are you sure you want to remove this file?",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            init: function() {
                const dz = this;
                
                // Custom styling
                this.on("addedfile", function(file) {
                    // Add custom styling to file preview
                    const preview = file.previewElement;
                    $(preview).addClass('dz-image-preview-custom');
                    
                    // Add file size info
                    const sizeInfo = $(preview).find('.dz-size');
                    if (sizeInfo.length === 0) {
                        $(preview).find('.dz-details').append('<div class="dz-size"><span data-dz-size></span></div>');
                    }
                });
                
                // Handle successful upload
                this.on("successmultiple", function(files, response) {
                    showAlert('success', response.message);
                    
                    // Close modal after successful upload
                    setTimeout(() => {
                        $('#uploadImagesModal').modal('hide');
                        location.reload();
                    }, 1500);
                });
                
                // Handle upload errors
                this.on("errormultiple", function(files, response) {
                    let errorMessage = 'Upload failed. Please try again.';
                    
                    if (response && response.message) {
                        errorMessage = response.message;
                    } else if (response && response.errors) {
                        const errors = [];
                        Object.values(response.errors).forEach(errorArray => {
                            errors.push(...errorArray);
                        });
                        errorMessage = errors.join('<br>');
                    }
                    
                    showAlert('error', errorMessage);
                });
                
                // Handle individual file errors
                this.on("error", function(file, errorMessage) {
                    showAlert('error', errorMessage);
                });
                
                // Custom validation for total file size
                this.on("addedfiles", function(files) {
                    let totalSize = 0;
                    const maxTotalSize = 15 * 1024 * 1024; // 15MB
                    
                    files.forEach(file => {
                        totalSize += file.size;
                    });
                    
                    if (totalSize > maxTotalSize) {
                        showAlert('error', `Total size exceeds 15MB (${(totalSize / 1024 / 1024).toFixed(1)}MB)`);
                        files.forEach(file => {
                            this.removeFile(file);
                        });
                    }
                });
            }
        });
    }
    
    // Clear all files
    $('#clearBtn').on('click', function() {
        if (dropzone) {
            dropzone.removeAllFiles(true);
        }
    });
    
    // Image preview modal
    $('#imagePreviewModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const clickedImageId = button.data('image-id');
        
        // Get all images
        const allImages = [];
        $('#imageGallery .col-6').each(function() {
            const img = $(this).find('img');
            allImages.push({
                id: $(this).data('image-id'),
                url: img.attr('src'),
                name: img.attr('alt')
            });
        });
        
        if (allImages.length === 0) return;
        
        // Build carousel items
        const carouselInner = $('#carouselInner');
        carouselInner.empty();
        
        allImages.forEach((image, index) => {
            const isActive = image.id == clickedImageId ? 'active' : '';
            const carouselItem = $(`
                <div class="carousel-item ${isActive}" data-image-id="${image.id}">
                    <img src="${image.url}" class="d-block w-100" style="max-height: 70vh; object-fit: contain;" alt="${image.name}">
                </div>
            `);
            carouselInner.append(carouselItem);
        });
        
        // Update image info
        const currentImage = allImages.find(img => img.id == clickedImageId);
        if (currentImage) {
            $('#previewImageName').text(currentImage.name);
            const currentIndex = allImages.findIndex(img => img.id == clickedImageId) + 1;
            $('#imageCounter').text(`${currentIndex} of ${allImages.length}`);
        }
        
        // Initialize carousel
        const carousel = new bootstrap.Carousel('#imageCarousel', {
            interval: false, // Disable auto-slide
            wrap: true // Enable infinite loop
        });
        
        // Update info when slide changes
        $('#imageCarousel').on('slid.bs.carousel', function (event) {
            const activeItem = $(event.target).find('.carousel-item.active');
            const imageId = activeItem.data('image-id');
            const currentImage = allImages.find(img => img.id == imageId);
            
            if (currentImage) {
                $('#previewImageName').text(currentImage.name);
                const currentIndex = allImages.findIndex(img => img.id == imageId) + 1;
                $('#imageCounter').text(`${currentIndex} of ${allImages.length}`);
            }
        });
        
        // Add keyboard navigation
        $(document).on('keydown', function(e) {
            if ($('#imagePreviewModal').hasClass('show')) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    $('#imageCarousel').carousel('prev');
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    $('#imageCarousel').carousel('next');
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    $('#imagePreviewModal').modal('hide');
                }
            }
        });
    });
    
    // Delete image
    $(document).on('click', '.delete-image-btn', function() {
        const imageId = $(this).data('image-id');
        const imageCard = $(this).closest('.col-6');
        
        // Store the data for the confirmation modal
        $('#confirmDeleteBtn').data('image-id', imageId);
        $('#confirmDeleteBtn').data('image-card', imageCard);
        
        // Show the confirmation modal
        $('#deleteImageModal').modal('show');
    });
    
    // Handle confirmation button click
    $('#confirmDeleteBtn').on('click', function() {
        const imageId = $(this).data('image-id');
        const imageCard = $(this).data('image-card');
        
        // Close the modal
        $('#deleteImageModal').modal('hide');
        
        // Delete the image
        deleteImage(imageId, imageCard);
    });
    
    // Delete image function
    function deleteImage(imageId, imageCard) {
        $.ajax({
            url: '{{ route("blocks.images.delete", $block->id) }}',
            type: 'DELETE',
            data: {
                image_id: imageId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                showAlert('success', response.message);
                imageCard.fadeOut(300, function() {
                    $(this).remove();
                    
                    // Check if no images left
                    if ($('#imageGallery .col-6').length === 0) {
                        $('#imageGallery').html(`
                            <div class="col-12 text-center py-4">
                                <i class="ph-image text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">No images uploaded yet</p>
                                <small class="text-muted">Click "Upload Images" to add photos</small>
                            </div>
                        `);
                    }
                });
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                const errorMessage = response && response.message ? response.message : 'Delete failed. Please try again.';
                showAlert('error', errorMessage);
            }
        });
    }
    
    // Show alert function
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('.container-fluid').prepend(alert);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alert.alert('close');
        }, 5000);
    }
});
</script>

<style>
/* Custom Dropzone Styling */
.dropzone {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    background: #f8f9fa;
    min-height: 200px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
}

.dropzone:hover {
    border-color: #667eea;
    background: #f0f2ff;
}

.dropzone.dz-drag-hover {
    border-color: #667eea;
    background: #e8f0fe;
}

.dropzone .dz-message {
    margin: 0;
    color: #6c757d;
}

.dropzone .dz-message h4 {
    color: #495057;
    margin-bottom: 10px;
}

.dropzone .dz-message p {
    margin-bottom: 0;
    font-size: 14px;
}

/* File preview styling */
.dz-image-preview-custom {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin: 5px;
    padding: 10px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dz-image-preview-custom .dz-image {
    border-radius: 0.25rem;
    overflow: hidden;
}

.dz-image-preview-custom .dz-details {
    padding: 5px 0;
    font-size: 12px;
}

.dz-image-preview-custom .dz-filename {
    font-weight: 500;
    color: #495057;
}

.dz-image-preview-custom .dz-size {
    color: #6c757d;
}

.dz-image-preview-custom .dz-progress {
    margin-top: 5px;
}

.dz-image-preview-custom .dz-remove {
    color: #dc3545;
    font-weight: bold;
    text-decoration: none;
}

.dz-image-preview-custom .dz-remove:hover {
    color: #c82333;
    text-decoration: underline;
}

/* Progress bar styling */
.dz-image-preview-custom .dz-progress .dz-upload {
    background: #667eea;
    border-radius: 2px;
}

/* Image Carousel Styling */
#imageCarousel {
    position: relative;
}

#imageCarousel .carousel-item img {
    background: #f8f9fa;
    border-radius: 0.375rem;
}

#imageCarousel .carousel-control-prev,
#imageCarousel .carousel-control-next {
    width: 50px;
    height: 50px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

#imageCarousel .carousel-control-prev:hover,
#imageCarousel .carousel-control-next:hover {
    opacity: 1;
}

#imageCarousel .carousel-control-prev {
    left: 20px;
}

#imageCarousel .carousel-control-next {
    right: 20px;
}

#imageCarousel .carousel-control-prev-icon,
#imageCarousel .carousel-control-next-icon {
    width: 20px;
    height: 20px;
}

/* Modal styling */
#imagePreviewModal .modal-dialog {
    max-width: 90vw;
    max-height: 90vh;
}

#imagePreviewModal .modal-content {
    border-radius: 0.5rem;
    overflow: hidden;
}

#imagePreviewModal .modal-body {
    padding: 0;
}
</style>
@endsection 