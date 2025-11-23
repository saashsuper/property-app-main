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
                            @if($blockInspection->job_status_id == 3)
                                <a href="{{ route('block-inspections.download-pdf', $blockInspection->id) }}" class="btn btn-danger btn-sm">
                                    <i class="ph-file-pdf me-2"></i>Download PDF Report
                                </a>
                            @endif
                            
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
                                    <th>Type</th>
                                    <th>Building</th>
                                    <th>Asset</th>
                                    <th>Status</th>
                                    <th>Comments</th>
                                    <th>Images</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($blockInspection->inspectionAssets as $asset)
                                    <tr>
                                        <td>
                                            @if($asset->block_general_asset_id)
                                                <span class="badge bg-info-subtle text-info">General Asset</span>
                                            @else
                                                <span class="badge bg-primary-subtle text-primary">Building Asset</span>
                                            @endif
                                        </td>
                                        <td>{{ $asset->blockBuilding->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($asset->block_general_asset_id)
                                                {{ $asset->generalAsset->name ?? 'N/A' }}
                                            @else
                                                {{ $asset->buildingAsset->name ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $valueName = strtolower($asset->inspectionValue->name ?? 'n/a');
                                                if(in_array($valueName, ['good', 'working', 'operational'])) {
                                                    $badgeClass = 'bg-success';
                                                } elseif(in_array($valueName, ['poor', 'not working', 'non-operational'])) {
                                                    $badgeClass = 'bg-danger';
                                                } elseif(in_array($valueName, ['fair', 'needs attention', 'average'])) {
                                                    $badgeClass = 'bg-warning';
                                                } else {
                                                    $badgeClass = 'bg-secondary';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }} text-white">
                                                {{ $asset->inspectionValue->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $asset->comments ?? '-' }}</td>
                                        <td>
                                            @if($asset->images->count() > 0)
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info view-asset-images-btn" 
                                                        data-asset-id="{{ $asset->id }}"
                                                        data-image-id="{{ $asset->images->first()->id }}"
                                                        title="View Images ({{ $asset->images->count() }})">
                                                    <i class="ph-camera"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
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

    <!-- Image Preview Modal -->
    <div class="modal fade" id="assetImagePreviewModal" tabindex="-1" aria-labelledby="assetImagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assetImagePreviewModalLabel">
                        <i class="ph-images me-2"></i>Inspection Asset Images
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Image Carousel -->
                    <div id="assetImageCarousel" class="carousel slide" data-bs-ride="false">
                        <div class="carousel-inner" id="assetCarouselInner">
                            <!-- Images will be dynamically added here -->
                        </div>
                        
                        <!-- Navigation Arrows -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#assetImageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#assetImageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    
                    <!-- Image Info -->
                    <div class="p-3 text-center bg-light border-top">
                        <h6 id="previewAssetImageName" class="mb-1"></h6>
                        <small class="text-muted" id="assetImageCounter"></small>
                    </div>
                    
                    <!-- Thumbnails -->
                    <div class="p-3 bg-light border-top">
                        <div class="d-flex gap-2 flex-wrap justify-content-center" id="assetImageThumbnails">
                            <!-- Thumbnails will be dynamically added here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Collect all images from all inspection assets
            let allAssetImages = [];
            
            @foreach($blockInspection->inspectionAssets as $asset)
                @if($asset->images->count() > 0)
                    @foreach($asset->images as $image)
                        allAssetImages.push({
                            id: {{ $image->id }},
                            url: {!! json_encode($image->image_url) !!},
                            name: {!! json_encode($image->image_name ?? "Asset Image") !!},
                            assetId: {{ $asset->id }},
                            assetName: {!! json_encode($asset->block_general_asset_id ? ($asset->generalAsset->name ?? "N/A") : ($asset->buildingAsset->name ?? "N/A")) !!}
                        });
                    @endforeach
                @endif
            @endforeach
            
            // Handle view images button click
            $('.view-asset-images-btn').on('click', function() {
                const clickedImageId = $(this).data('image-id');
                openAssetImageModal(clickedImageId);
            });
            
            function openAssetImageModal(clickedImageId) {
                if (allAssetImages.length === 0) return;
                
                const modal = $('#assetImagePreviewModal');
                const carouselInner = modal.find('#assetCarouselInner');
                const thumbnailsContainer = modal.find('#assetImageThumbnails');
                
                // Clear previous content
                carouselInner.empty();
                thumbnailsContainer.empty();
                
                // Build carousel items and thumbnails
                allAssetImages.forEach((image, index) => {
                    const isActive = image.id == clickedImageId ? 'active' : '';
                    
                    // Carousel item
                    const carouselItem = $(`
                        <div class="carousel-item ${isActive}" data-image-id="${image.id}">
                            <img src="${image.url}" class="d-block w-100" style="max-height: 60vh; object-fit: contain;" alt="${image.name}">
                        </div>
                    `);
                    carouselInner.append(carouselItem);
                    
                    // Thumbnail
                    const thumbnail = $(`
                        <div class="thumbnail-item ${isActive}" data-image-id="${image.id}" style="cursor: pointer; border: 2px solid ${isActive ? '#0d6efd' : 'transparent'}; border-radius: 4px; padding: 2px;">
                            <img src="${image.url}" 
                                 alt="${image.name}" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 2px;"
                                 class="img-thumbnail">
                        </div>
                    `);
                    thumbnailsContainer.append(thumbnail);
                });
                
                // Update image info
                updateAssetImageInfo(clickedImageId);
                
                // Initialize carousel
                const carousel = new bootstrap.Carousel(modal.find('#assetImageCarousel')[0], {
                    interval: false,
                    wrap: true
                });
                
                // Update info when slide changes
                modal.find('#assetImageCarousel').on('slid.bs.carousel', function (event) {
                    const activeItem = modal.find('.carousel-item.active');
                    const imageId = activeItem.data('image-id');
                    updateAssetImageInfo(imageId);
                    updateThumbnailSelection(imageId);
                });
                
                // Handle thumbnail click
                thumbnailsContainer.on('click', '.thumbnail-item', function() {
                    const imageId = $(this).data('image-id');
                    const carousel = bootstrap.Carousel.getInstance(modal.find('#assetImageCarousel')[0]);
                    const targetIndex = allAssetImages.findIndex(img => img.id == imageId);
                    if (targetIndex !== -1) {
                        carousel.to(targetIndex);
                    }
                });
                
                // Add keyboard navigation
                $(document).off('keydown.assetImageModal');
                $(document).on('keydown.assetImageModal', function(e) {
                    if (modal.hasClass('show')) {
                        if (e.key === 'ArrowLeft') {
                            e.preventDefault();
                            modal.find('#assetImageCarousel').carousel('prev');
                        } else if (e.key === 'ArrowRight') {
                            e.preventDefault();
                            modal.find('#assetImageCarousel').carousel('next');
                        } else if (e.key === 'Escape') {
                            e.preventDefault();
                            modal.modal('hide');
                        }
                    }
                });
                
                // Clean up on modal close
                modal.on('hidden.bs.modal', function() {
                    $(document).off('keydown.assetImageModal');
                });
                
                // Show modal
                modal.modal('show');
            }
            
            function updateAssetImageInfo(imageId) {
                const currentImage = allAssetImages.find(img => img.id == imageId);
                if (currentImage) {
                    $('#previewAssetImageName').text(currentImage.name);
                    const currentIndex = allAssetImages.findIndex(img => img.id == imageId) + 1;
                    $('#assetImageCounter').text(`${currentIndex} of ${allAssetImages.length} - ${currentImage.assetName}`);
                }
            }
            
            function updateThumbnailSelection(imageId) {
                $('#assetImageThumbnails .thumbnail-item').each(function() {
                    const $item = $(this);
                    if ($item.data('image-id') == imageId) {
                        $item.addClass('active').css('border-color', '#0d6efd');
                    } else {
                        $item.removeClass('active').css('border-color', 'transparent');
                    }
                });
            }
        });
    </script>
    
    <style>
        #assetImagePreviewModal .modal-dialog {
            max-width: 90vw;
            max-height: 90vh;
        }
        
        #assetImagePreviewModal .modal-content {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        #assetImagePreviewModal .modal-body {
            padding: 0;
        }
        
        #assetImagePreviewModal .thumbnail-item {
            transition: all 0.2s ease;
        }
        
        #assetImagePreviewModal .thumbnail-item:hover {
            transform: scale(1.05);
            border-color: #0d6efd !important;
        }
        
        #assetImagePreviewModal .thumbnail-item.active {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
        }
        
        #assetImagePreviewModal .carousel-control-prev,
        #assetImagePreviewModal .carousel-control-next {
            background-color: rgba(0, 0, 0, 0.3);
            width: 50px;
        }
        
        #assetImagePreviewModal .carousel-control-prev:hover,
        #assetImagePreviewModal .carousel-control-next:hover {
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>
    @endpush
@endsection
