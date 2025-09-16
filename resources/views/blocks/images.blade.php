@extends('layouts.master')

@section('title')
    Block Images - {{ $block->name }} - PROMAN
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Block Images - {{ $block->name }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blocks.index') }}">Blocks</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blocks.show', $block) }}">{{ $block->name }}</a></li>
                            <li class="breadcrumb-item active">Images</li>
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
                        <h4 class="card-title">Upload Block Images</h4>
                        <p class="text-muted mb-0">Upload multiple images for this block. Maximum 10 images, 15MB total size.</p>
                    </div>
                    <div class="card-body">
                        <!-- Upload Form -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="images" class="form-label">Select Images <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="images" name="images[]" 
                                           accept="image/*" multiple>
                                    <div class="form-text">
                                        <strong>Requirements:</strong><br>
                                        • Maximum 10 images<br>
                                        • Each image max 5MB<br>
                                        • Total size max 15MB<br>
                                        • Formats: JPEG, PNG, JPG, GIF
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary" id="uploadBtn" disabled>
                                        <i class="ph-upload align-bottom me-1"></i> Upload Images
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="clearBtn" disabled>
                                        <i class="ph-x align-bottom me-1"></i> Clear Selection
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="progress" style="display: none;" id="uploadProgress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <div id="uploadStatus" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Preview Container -->
                        <div id="imagePreviewContainer" class="row" style="display: none;">
                            <div class="col-12">
                                <h5>Selected Images Preview</h5>
                                <div id="imagePreview" class="row"></div>
                            </div>
                        </div>

                        <!-- Uploaded Images -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Uploaded Images ({{ $block->images->count() }}/10)</h5>
                                <div id="uploadedImages" class="row">
                                    @if($block->images && $block->images->count() > 0)
                                        @foreach($block->images as $image)
                                            <div class="col-md-3 mb-3" data-image-id="{{ $image->id }}">
                                                <div class="card {{ $image->is_primary ? 'border-primary' : '' }}">
                                                    <div class="position-relative">
                                                        <img src="{{ $image->url }}" 
                                                             class="card-img-top" style="height: 200px; object-fit: cover;"
                                                             alt="{{ $image->original_name }}">
                                                        
                                                        <!-- Primary Badge -->
                                                        @if($image->is_primary)
                                                            <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                                                Primary
                                                            </span>
                                                        @endif
                                                        
                                                        <!-- Action Buttons -->
                                                        <div class="position-absolute top-0 end-0 m-2 d-flex flex-column gap-1">
                                                            @if(!$image->is_primary)
                                                                <button type="button" class="btn btn-primary btn-sm set-primary-btn"
                                                                        data-image-id="{{ $image->id }}" title="Set as Primary">
                                                                    <i class="ph-star"></i>
                                                                </button>
                                                            @endif
                                                            <button type="button" class="btn btn-danger btn-sm delete-image-btn"
                                                                    data-image-id="{{ $image->id }}" title="Delete Image">
                                                                <i class="ph-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <h6 class="card-title text-truncate" title="{{ $image->original_name }}">
                                                            {{ $image->original_name }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ $image->file_size_human }} • 
                                                            {{ $image->created_at->format('M d, Y') }}
                                                        </small>
                                                        @if($image->uploader)
                                                            <br><small class="text-muted">by {{ $image->uploader->name }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12">
                                            <div class="text-center py-4">
                                                <i class="ph-image text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">No images uploaded yet</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="{{ route('blocks.show', $block) }}" class="btn btn-secondary">
                                    <i class="ph-arrow-left align-bottom me-1"></i> Back to Block Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const maxImages = 10;
    const maxTotalSize = 15 * 1024 * 1024; // 15MB
    const maxFileSize = 5 * 1024 * 1024; // 5MB per file
    let selectedFiles = [];
    let totalSize = 0;

    // File input change handler
    $('#images').on('change', function() {
        const files = Array.from(this.files);
        selectedFiles = [];
        totalSize = 0;
        
        // Validate files
        const validFiles = [];
        const errors = [];
        
        files.forEach((file, index) => {
            // Check file type
            if (!file.type.startsWith('image/')) {
                errors.push(`${file.name}: Not an image file`);
                return;
            }
            
            // Check file size
            if (file.size > maxFileSize) {
                errors.push(`${file.name}: File size exceeds 5MB`);
                return;
            }
            
            validFiles.push(file);
            totalSize += file.size;
        });
        
        // Check total count
        if (validFiles.length > maxImages) {
            errors.push(`Maximum ${maxImages} images allowed`);
        }
        
        // Check total size
        if (totalSize > maxTotalSize) {
            errors.push(`Total size exceeds 15MB (${(totalSize / 1024 / 1024).toFixed(1)}MB)`);
        }
        
        if (errors.length > 0) {
            showAlert('error', errors.join('<br>'));
            $('#images').val('');
            return;
        }
        
        selectedFiles = validFiles;
        updateUI();
        showImagePreview(validFiles);
    });
    
    // Update UI based on selection
    function updateUI() {
        const hasFiles = selectedFiles.length > 0;
        $('#uploadBtn').prop('disabled', !hasFiles);
        $('#clearBtn').prop('disabled', !hasFiles);
        
        if (hasFiles) {
            $('#uploadBtn').html(`<i class="ph-upload align-bottom me-1"></i> Upload ${selectedFiles.length} Image(s)`);
        } else {
            $('#uploadBtn').html('<i class="ph-upload align-bottom me-1"></i> Upload Images');
        }
    }
    
    // Show image preview
    function showImagePreview(files) {
        const container = $('#imagePreview');
        container.empty();
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = $(`
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <h6 class="card-title text-truncate" title="${file.name}">${file.name}</h6>
                                <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                            </div>
                        </div>
                    </div>
                `);
                container.append(col);
            };
            reader.readAsDataURL(file);
        });
        
        $('#imagePreviewContainer').show();
    }
    
    // Clear selection
    $('#clearBtn').on('click', function() {
        $('#images').val('');
        selectedFiles = [];
        totalSize = 0;
        updateUI();
        $('#imagePreviewContainer').hide();
        $('#imagePreview').empty();
    });
    
    // Upload images
    $('#uploadBtn').on('click', function() {
        if (selectedFiles.length === 0) return;
        
        const formData = new FormData();
        selectedFiles.forEach(file => {
            formData.append('images[]', file);
        });
        
        // Show progress
        $('#uploadProgress').show();
        $('#uploadBtn').prop('disabled', true);
        $('#uploadStatus').html('<div class="text-info">Uploading images...</div>');
        
        $.ajax({
            url: '{{ route("blocks.images.upload", $block->id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        const percentComplete = (evt.loaded / evt.total) * 100;
                        $('.progress-bar').css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                showAlert('success', response.message);
                $('#uploadProgress').hide();
                $('#uploadBtn').prop('disabled', false);
                $('#uploadStatus').empty();
                
                // Clear selection
                $('#clearBtn').click();
                
                // Reload page to show new images
                setTimeout(() => {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
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
                $('#uploadProgress').hide();
                $('#uploadBtn').prop('disabled', false);
                $('#uploadStatus').empty();
            }
        });
    });
    
    // Delete image
    $(document).on('click', '.delete-image-btn', function() {
        const imageId = $(this).data('image-id');
        const imageCard = $(this).closest('.col-md-3');
        
        if (confirm('Are you sure you want to delete this image?')) {
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
                        
                        // Update image count
                        const remainingImages = $('#uploadedImages .col-md-3').length;
                        $('h5').text(`Uploaded Images (${remainingImages}/10)`);
                        
                        // Check if no images left
                        if (remainingImages === 0) {
                            $('#uploadedImages').html(`
                                <div class="col-12">
                                    <div class="text-center py-4">
                                        <i class="ph-image text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No images uploaded yet</p>
                                    </div>
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
    });
    
    // Set primary image
    $(document).on('click', '.set-primary-btn', function() {
        const imageId = $(this).data('image-id');
        const button = $(this);
        
        $.ajax({
            url: '{{ route("blocks.images.primary", $block->id) }}',
            type: 'POST',
            data: {
                image_id: imageId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                showAlert('success', response.message);
                
                // Remove primary badges from all images
                $('.badge.bg-primary').remove();
                $('.card.border-primary').removeClass('border-primary');
                
                // Add primary badge and border to selected image
                const imageCard = button.closest('.col-md-3').find('.card');
                imageCard.addClass('border-primary');
                imageCard.find('.position-relative').prepend(`
                    <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                        Primary
                    </span>
                `);
                
                // Hide the set primary button for this image
                button.hide();
                
                // Show set primary buttons for other images
                $('.set-primary-btn').not(button).show();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                const errorMessage = response && response.message ? response.message : 'Failed to set primary image. Please try again.';
                showAlert('error', errorMessage);
            }
        });
    });
    
    // Show alert function
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('.card-body').prepend(alert);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alert.alert('close');
        }, 5000);
    }
});
</script>
@endpush
