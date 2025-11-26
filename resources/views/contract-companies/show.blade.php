@extends('layouts.master')

@section('title') Contract Company Details @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Dashboard @endslot
@slot('li_2') Contract Companies @endslot
@slot('title') Contract Company Details @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $contractCompany->company_name }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('contract-companies.edit', $contractCompany->id) }}" class="btn btn-warning btn-sm">
                            <i class="ph-pencil me-2"></i>Edit
                        </a>
                        <a href="{{ route('contract-companies.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ph-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Company Name:</label>
                            <p class="form-control-plaintext">{{ $contractCompany->company_name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone Number:</label>
                            <p class="form-control-plaintext">{{ $contractCompany->phone_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Address:</label>
                            <p class="form-control-plaintext">{{ $contractCompany->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Website:</label>
                            <p class="form-control-plaintext">
                                @if($contractCompany->website)
                                    <a href="{{ $contractCompany->website }}" target="_blank" class="text-primary">
                                        {{ $contractCompany->website }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created:</label>
                            <p class="form-control-plaintext">
                                {{ $contractCompany->created_at ? $contractCompany->created_at->format('d M, Y h:i A') : 'N/A' }}
                                @if($contractCompany->creator)
                                    <br><small class="text-muted">by {{ $contractCompany->creator->name }}</small>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Updated:</label>
                            <p class="form-control-plaintext">
                                {{ $contractCompany->updated_at ? $contractCompany->updated_at->format('d M, Y h:i A') : 'N/A' }}
                                @if($contractCompany->updater)
                                    <br><small class="text-muted">by {{ $contractCompany->updater->name }}</small>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

