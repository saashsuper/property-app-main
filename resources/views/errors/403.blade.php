@extends('layouts.master')

@section('title', 'Access Denied - PROMAN')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="text-danger mb-3">Access Denied</h2>
                        <p class="text-muted mb-4">
                            You don't have permission to access this resource. 
                            Please contact your administrator if you believe this is an error.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('root') }}" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Go to Dashboard
                            </a>
                            <a href="javascript:history.back()" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
