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

                        <form action="{{ route('blocks.update', $block) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Block Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $block->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="management_company" class="form-label">Management Company <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('management_company') is-invalid @enderror" 
                                               id="management_company" name="management_company" value="{{ old('management_company', $block->management_company) }}" required>
                                        @error('management_company')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="block_type_id" class="form-label">Block Type <span class="text-danger">*</span></label>
                                        <select class="form-select @error('block_type_id') is-invalid @enderror" 
                                                id="block_type_id" name="block_type_id" required>
                                            <option value="">Select Block Type</option>
                                            @foreach($blockTypes as $blockType)
                                                <option value="{{ $blockType->id }}" 
                                                    {{ old('block_type_id', $block->block_type_id) == $blockType->id ? 'selected' : '' }}>
                                                    {{ $blockType->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('block_type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Block Image</label>
                                        @if($block->image_url)
                                            <div class="mb-2">
                                                <img src="{{ $block->image_url }}" alt="{{ $block->name }}" 
                                                     class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                                <small class="d-block text-muted">Current image</small>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*">
                                        <div class="form-text">Accepted formats: JPEG, PNG, JPG, GIF (Max: 2MB)</div>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="address1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('address1') is-invalid @enderror" 
                                               id="address1" name="address1" value="{{ old('address1', $block->address1) }}" required>
                                        @error('address1')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address2" class="form-label">Address Line 2</label>
                                        <input type="text" class="form-control @error('address2') is-invalid @enderror" 
                                               id="address2" name="address2" value="{{ old('address2', $block->address2) }}">
                                        @error('address2')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address3" class="form-label">Address Line 3</label>
                                        <input type="text" class="form-control @error('address3') is-invalid @enderror" 
                                               id="address3" name="address3" value="{{ old('address3', $block->address3) }}">
                                        @error('address3')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="country_id" class="form-label">Country ID <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('country_id') is-invalid @enderror" 
                                               id="country_id" name="country_id" value="{{ old('country_id', $block->country_id) }}" required>
                                        @error('country_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="state_id" class="form-label">State ID <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('state_id') is-invalid @enderror" 
                                               id="state_id" name="state_id" value="{{ old('state_id', $block->state_id) }}" required>
                                        @error('state_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="car_spaces" class="form-label">Car Spaces <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('car_spaces') is-invalid @enderror" 
                                               id="car_spaces" name="car_spaces" value="{{ old('car_spaces', $block->car_spaces) }}" min="0" required>
                                        @error('car_spaces')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="no_of_units" class="form-label">Number of Units</label>
                                        <input type="number" class="form-control @error('no_of_units') is-invalid @enderror" 
                                               id="no_of_units" name="no_of_units" value="{{ old('no_of_units', $block->no_of_units) }}" min="0">
                                        @error('no_of_units')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="inspection_count" class="form-label">Inspection Count</label>
                                        <input type="number" class="form-control @error('inspection_count') is-invalid @enderror" 
                                               id="inspection_count" name="inspection_count" value="{{ old('inspection_count', $block->inspection_count) }}" min="0">
                                        @error('inspection_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('blocks.index') }}" class="btn btn-secondary">
                                            <i class="ri-arrow-left-line align-bottom me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line align-bottom me-1"></i> Update Block
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 