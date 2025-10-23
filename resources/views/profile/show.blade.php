@extends('layouts.master')

@section('title') My Profile @endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ph-user-circle me-2 text-primary"></i>
                        MY PROFILE
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">My Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="position-relative d-inline-block mb-3">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle avatar-xl">
                            @else
                                <div class="avatar-xl">
                                    <div class="avatar-title bg-primary-subtle text-primary fs-2 rounded-circle">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </div>
                            @endif
                            <button type="button" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0" data-bs-toggle="modal" data-bs-target="#changeAvatarModal">
                                <i class="ph-camera"></i>
                            </button>
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-2">{{ $user->email }}</p>
                        @if($user->userType)
                            <span class="badge bg-primary-subtle text-primary">{{ $user->userType->name }}</span>
                        @endif
                        @if($user->roles->count() > 0)
                            @foreach($user->roles as $role)
                                <span class="badge bg-success-subtle text-success">{{ $role->name }}</span>
                            @endforeach
                        @endif
                    </div>
                    <div class="card-footer bg-light border-0">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <div class="p-2">
                                    <h5 class="mb-1">{{ $user->created_at->format('M d, Y') }}</h5>
                                    <p class="text-muted mb-0 small">Joined Date</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2">
                                    <h5 class="mb-1">
                                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </h5>
                                    <p class="text-muted mb-0 small">Status</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom d-flex align-items-center">
                        <i class="ph-gear fs-5 me-2 text-primary"></i>
                        <h5 class="mb-0 text-dark">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="ph-user-circle-gear me-2"></i>Edit Profile
                            </button>
                            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="ph-lock-key me-2"></i>Change Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="card border shadow-sm mb-3">
                    <div class="card-header bg-light border-bottom d-flex align-items-center">
                        <i class="ph-user fs-5 me-2 text-primary"></i>
                        <h5 class="mb-0 text-dark">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Full Name</label>
                                <p class="mb-0">{{ $user->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Email Address</label>
                                <p class="mb-0">{{ $user->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Phone Number</label>
                                <p class="mb-0">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">User Type</label>
                                <p class="mb-0">{{ $user->userType->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Address</label>
                                <p class="mb-0">{{ $user->address ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="card border shadow-sm mb-3">
                    <div class="card-header bg-light border-bottom d-flex align-items-center">
                        <i class="ph-shield-check fs-5 me-2 text-success"></i>
                        <h5 class="mb-0 text-dark">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">User ID</label>
                                <p class="mb-0">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Account Created</label>
                                <p class="mb-0">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Last Updated</label>
                                <p class="mb-0">{{ $user->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @if($user->email_verified_at)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Email Verified</label>
                                <p class="mb-0">
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="ph-check-circle me-1"></i>Verified on {{ $user->email_verified_at->format('M d, Y') }}
                                    </span>
                                </p>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Account Status</label>
                                <p class="mb-0">
                                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roles & Permissions -->
                @if($user->roles->count() > 0)
                <div class="card border shadow-sm">
                    <div class="card-header bg-light border-bottom d-flex align-items-center">
                        <i class="ph-key fs-5 me-2 text-warning"></i>
                        <h5 class="mb-0 text-dark">Roles & Permissions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Assigned Roles</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-success-subtle text-success">{{ $role->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @if($user->getAllPermissions()->count() > 0)
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Permissions ({{ $user->getAllPermissions()->count() }})</label>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($user->getAllPermissions()->take(20) as $permission)
                                        <span class="badge bg-primary-subtle text-primary small">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($user->getAllPermissions()->count() > 20)
                                        <span class="badge bg-secondary small">+{{ $user->getAllPermissions()->count() - 20 }} more</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">
                    <i class="ph-user-circle-gear me-2"></i>Edit Profile
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProfileForm">
                @csrf
                <div class="modal-body">
                    <div id="profileAlertContainer" style="display: none;">
                        <div id="profileAlert" class="alert" role="alert">
                            <span id="profileAlertMessage"></span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="{{ $user->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">User Type</label>
                            <input type="text" class="form-control" value="{{ $user->userType->name ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2">{{ $user->address }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-check me-1"></i>Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Avatar Modal -->
<div class="modal fade" id="changeAvatarModal" tabindex="-1" aria-labelledby="changeAvatarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeAvatarModalLabel">
                    <i class="ph-camera me-2"></i>Change Profile Picture
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changeAvatarForm">
                @csrf
                <div class="modal-body">
                    <div id="avatarAlertContainer" style="display: none;">
                        <div id="avatarAlert" class="alert" role="alert">
                            <span id="avatarAlertMessage"></span>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <div class="position-relative d-inline-block">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle" width="150" height="150" id="avatarPreview" style="object-fit: cover;">
                                <div class="d-none" id="avatarPlaceholder" style="width: 150px; height: 150px;">
                                    <div class="avatar-title bg-primary-subtle text-primary fs-1 rounded-circle" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </div>
                            @else
                                <div id="avatarPlaceholder" style="width: 150px; height: 150px;" class="mx-auto">
                                    <div class="avatar-title bg-primary-subtle text-primary fs-1 rounded-circle" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </div>
                                <img src="" alt="{{ $user->name }}" class="rounded-circle d-none" width="150" height="150" id="avatarPreview" style="object-fit: cover;">
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Choose Image</label>
                        <input type="file" class="form-control" name="avatar" id="avatarInput" accept="image/*" required>
                        <small class="text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-upload me-1"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="ph-lock-key me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePasswordForm">
                @csrf
                <div class="modal-body">
                    <div id="passwordAlertContainer" style="display: none;">
                        <div id="passwordAlert" class="alert" role="alert">
                            <span id="passwordAlertMessage"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="new_password" required>
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="new_password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ph-check me-1"></i>Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Edit Profile Form
    $('#editProfileForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="ph-spinner ph-spin me-1"></i> Updating...');
        
        $.ajax({
            url: '{{ route("profile.update") }}',
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('profile', 'success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('profile', 'error', response.message);
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('<br>');
                }
                showAlert('profile', 'error', errorMessage);
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Change Avatar Form
    $('#changeAvatarForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        const formData = new FormData(this);
        
        submitBtn.prop('disabled', true).html('<i class="ph-spinner ph-spin me-1"></i> Uploading...');
        
        $.ajax({
            url: '{{ route("profile.avatar") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('avatar', 'success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('avatar', 'error', response.message);
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('<br>');
                }
                showAlert('avatar', 'error', errorMessage);
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Change Password Form
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="ph-spinner ph-spin me-1"></i> Updating...');
        
        $.ajax({
            url: '{{ route("profile.password") }}',
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('password', 'success', response.message);
                    form[0].reset();
                    setTimeout(() => $('#changePasswordModal').modal('hide'), 1500);
                }
                submitBtn.prop('disabled', false).html(originalBtnText);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('<br>');
                }
                showAlert('password', 'error', errorMessage);
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Avatar Preview
    $('#avatarInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').attr('src', e.target.result).removeClass('d-none');
                $('#avatarPlaceholder').addClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
});

function showAlert(type, level, message) {
    const alertContainer = $(`#${type}AlertContainer`);
    const alert = $(`#${type}Alert`);
    const alertMessage = $(`#${type}AlertMessage`);
    
    alertContainer.show();
    alert.removeClass('alert-success alert-danger').addClass('alert-' + (level === 'success' ? 'success' : 'danger'));
    alertMessage.html(message);
    
    if (level === 'success') {
        setTimeout(() => alertContainer.fadeOut(), 3000);
    }
}
</script>
@endsection

