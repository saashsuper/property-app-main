@extends('layouts.master')

@section('title') Block Type Details @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Dashboard @endslot
@slot('li_2') Block Types @endslot
@slot('title') Block Type Details @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Block Type Details</h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('block-types.edit', $blockType->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">ID:</th>
                                <td>{{ $blockType->id }}</td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td><strong>{{ $blockType->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Total Blocks:</th>
                                <td>
                                    <span class="badge bg-primary">{{ $blockType->blocks->count() }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Created:</th>
                                <td>{{ $blockType->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Updated:</th>
                                <td>{{ $blockType->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($blockType->blocks->count() > 0)
                <hr>
                <h5 class="mb-3">Related Blocks</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Management Company</th>
                                <th>Address</th>
                                <th>Units</th>
                                <th>Car Spaces</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blockType->blocks as $block)
                            <tr>
                                <td>{{ $block->id }}</td>
                                <td>
                                    <strong>{{ $block->name }}</strong>
                                </td>
                                <td>{{ $block->management_company }}</td>
                                <td>
                                    <small class="text-muted">{{ $block->full_address }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $block->no_of_units ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $block->car_spaces }}</span>
                                </td>
                                <td>
                                    <small>{{ $block->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('blocks.show', $block->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <hr>
                <div class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No blocks found for this type.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 