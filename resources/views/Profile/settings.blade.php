@extends('layouts.app')

@section('title', 'Account Settings')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<style>
    .pf-avatar-md {
        width: 88px; height: 88px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: var(--primary); color: #fff; font-size: 1.6rem; font-weight: 700;
        box-shadow: 0 0 0 2px var(--border);
    }
    .pf-avatar-md img { width: 100%; height: 100%; object-fit: cover; }
    .pw-wrap { position: relative; }
    .pw-wrap .form-control { padding-right: 2.75rem; }
    .pw-eye {
        position: absolute; top: 50%; right: 0.5rem; transform: translateY(-50%);
        width: 34px; height: 34px; border: 0; background: transparent; border-radius: 8px;
        color: var(--text-secondary);
    }
    .pw-eye:hover { background: var(--page-bg); color: var(--text-primary); }

    /* Photo editor */
    .crop-stage { width: 100%; height: 380px; background: #0B1220; border-radius: 10px; overflow: hidden; }
    .crop-stage img { display: block; max-width: 100%; }
    .crop-stage .cropper-view-box,
    .crop-stage .cropper-face { border-radius: 50%; }
    .crop-stage .cropper-view-box { outline: 2px solid #fff; outline-color: rgba(255,255,255,.9); }
    .crop-tools { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1rem; }
    .crop-tools .btn { display: inline-flex; align-items: center; gap: .35rem; }
    .crop-angle { margin-top: 1rem; }
    @media (max-width: 575.98px) { .crop-stage { height: 300px; } }
</style>
@endpush

@section('content')
<div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
        <div class="eyebrow">Account</div>
        <h1>Account Settings</h1>
    </div>
    <a href="{{ route('profile.show') }}" class="btn btn-outline-primary">
        <i class="bi bi-person me-1"></i> View profile
    </a>
</div>

<div class="row g-4">

    {{-- PROFILE --}}
    <div class="col-lg-7">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h5 class="chart-title">Profile information</h5>
                    <div class="chart-subtitle">Update your name, phone number and photo.</div>
                </div>
            </div>

            @if (session('profile_success'))
                <div class="alert alert-success py-2">{{ session('profile_success') }}</div>
            @endif

            <form action="{{ route('account.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="pf-avatar-md" id="avatarPreview">
                        @if ($user->photoUrl())
                            <img src="{{ $user->photoUrl() }}" alt="{{ $user->name }}">
                        @else
                            {{ $user->initials() }}
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <label for="profile_image" class="form-label fw-semibold mb-1">Profile photo</label>
                        <input type="file" id="profile_image" name="profile_image"
                               class="form-control @error('profile_image', 'profile') is-invalid @enderror"
                               accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text">JPG, PNG or WEBP. You can crop, rotate and flip before saving.</div>
                        @error('profile_image', 'profile')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Full name</label>
                        <input type="text" id="name" name="name"
                               class="form-control @error('name', 'profile') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name', 'profile')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Phone number</label>
                        <input type="text" id="phone" name="phone" placeholder="98XXXXXXXX"
                               class="form-control @error('phone', 'profile') is-invalid @enderror"
                               value="{{ old('phone', $user->phone_number) }}" required>
                        @error('phone', 'profile')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="email" class="form-label fw-semibold">Email</label>

                        @if ($user->role === 'admin')
                            <input type="email" id="email" name="email"
                                   class="form-control @error('email', 'profile') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email', 'profile')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">This is the email you sign in with.</div>
                        @else
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            <div class="form-text">Email can only be changed by an administrator.</div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- PASSWORD --}}
    <div class="col-lg-5">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h5 class="chart-title">Change password</h5>
                    <div class="chart-subtitle">Use at least 8 characters.</div>
                </div>
            </div>

            @if (session('password_success'))
                <div class="alert alert-success py-2">{{ session('password_success') }}</div>
            @endif

            <form action="{{ route('account.password.update') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="current_password" class="form-label fw-semibold">Current password</label>
                    <div class="pw-wrap">
                        <input type="password" id="current_password" name="current_password"
                               class="form-control @error('current_password', 'password') is-invalid @enderror"
                               autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required>
                        <button type="button" class="pw-eye" data-target="current_password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                    @error('current_password', 'password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label fw-semibold">New password</label>
                    <div class="pw-wrap">
                        <input type="password" id="new_password" name="password"
                               class="form-control @error('password', 'password') is-invalid @enderror"
                               autocomplete="new-password" minlength="8" required>
                        <button type="button" class="pw-eye" data-target="new_password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                    @error('password', 'password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirm new password</label>
                    <div class="pw-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-control" autocomplete="new-password" minlength="8" required>
                        <button type="button" class="pw-eye" data-target="password_confirmation" aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-shield-lock me-1"></i> Update password
                </button>
            </form>
        </div>
    </div>

</div>

{{-- PHOTO EDITOR --}}
<div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropModalLabel">Adjust your photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="crop-stage">
                    <img id="cropImage" alt="Photo to crop">
                </div>

                <div class="crop-tools">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="cropRotateLeft"><i class="bi bi-arrow-counterclockwise"></i> Rotate left</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="cropRotateRight"><i class="bi bi-arrow-clockwise"></i> Rotate right</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="cropFlipH"><i class="bi bi-symmetry-vertical"></i> Flip horizontal</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="cropFlipV"><i class="bi bi-symmetry-horizontal"></i> Flip vertical</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="cropZoomIn"><i class="bi bi-zoom-in"></i> Zoom in</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="cropZoomOut"><i class="bi bi-zoom-out"></i> Zoom out</button>
                    <button type="button" class="btn btn-outline-danger btn-sm ms-auto" id="cropReset"><i class="bi bi-arrow-repeat"></i> Reset</button>
                </div>

                <div class="crop-angle">
                    <label for="cropAngle" class="form-label small fw-semibold mb-1">
                        Fine rotation: <span id="cropAngleValue">0</span>°
                    </label>
                    <input type="range" class="form-range" id="cropAngle" min="-45" max="45" step="1" value="0">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="cropApply">
                    <i class="bi bi-check2-circle me-1"></i> Use this photo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<script>
(function () {
    var input     = document.getElementById('profile_image');
    var preview   = document.getElementById('avatarPreview');
    var modalEl   = document.getElementById('cropModal');
    var cropImg   = document.getElementById('cropImage');
    var angleBar  = document.getElementById('cropAngle');
    var angleText = document.getElementById('cropAngleValue');

    var modal = new bootstrap.Modal(modalEl);
    var cropper = null, objectUrl = null, applied = false;
    var baseAngle = 0, fineAngle = 0, flipX = 1, flipY = 1;

    function resetState() {
        baseAngle = 0; fineAngle = 0; flipX = 1; flipY = 1;
        angleBar.value = 0;
        angleText.textContent = '0';
    }

    function applyRotation() {
        if (cropper) cropper.rotateTo(baseAngle + fineAngle);
    }

    function destroyCropper() {
        if (cropper) { cropper.destroy(); cropper = null; }
        if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
    }

    // Choose a file -> open the editor
    if (input) {
        input.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (!file) return;

            if (!/^image\/(jpeg|png|webp)$/.test(file.type)) {
                alert('Please choose a JPG, PNG or WEBP image.');
                input.value = '';
                return;
            }

            applied = false;
            resetState();
            destroyCropper();
            objectUrl = URL.createObjectURL(file);
            cropImg.src = objectUrl;
            modal.show();
        });
    }

    // Start the cropper once the modal is visible (it needs real dimensions)
    modalEl.addEventListener('shown.bs.modal', function () {
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropImg, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.9,
            background: false,
            guides: false,
            center: false,
            highlight: false,
            cropBoxMovable: false,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false
        });
    });

    // Closed without applying -> discard the chosen file
    modalEl.addEventListener('hidden.bs.modal', function () {
        destroyCropper();
        if (!applied && input) input.value = '';
    });

    // Tools
    document.getElementById('cropRotateLeft').addEventListener('click', function () { baseAngle -= 90; applyRotation(); });
    document.getElementById('cropRotateRight').addEventListener('click', function () { baseAngle += 90; applyRotation(); });
    document.getElementById('cropFlipH').addEventListener('click', function () { flipX *= -1; if (cropper) cropper.scaleX(flipX); });
    document.getElementById('cropFlipV').addEventListener('click', function () { flipY *= -1; if (cropper) cropper.scaleY(flipY); });
    document.getElementById('cropZoomIn').addEventListener('click', function () { if (cropper) cropper.zoom(0.1); });
    document.getElementById('cropZoomOut').addEventListener('click', function () { if (cropper) cropper.zoom(-0.1); });

    angleBar.addEventListener('input', function () {
        fineAngle = parseInt(this.value, 10) || 0;
        angleText.textContent = fineAngle;
        applyRotation();
    });

    document.getElementById('cropReset').addEventListener('click', function () {
        resetState();
        if (cropper) cropper.reset();
    });

    // Apply -> put the edited image into the form's file input
    document.getElementById('cropApply').addEventListener('click', function () {
        if (!cropper) return;

        var canvas = cropper.getCroppedCanvas({
            width: 600,
            height: 600,
            fillColor: '#ffffff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!canvas) return;

        canvas.toBlob(function (blob) {
            if (!blob) { alert('Could not process the image. Please try another photo.'); return; }

            var file = new File([blob], 'profile-' + Date.now() + '.jpg', { type: 'image/jpeg' });
            var dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;

            // Update the avatar preview
            preview.innerHTML = '';
            var img = document.createElement('img');
            img.src = URL.createObjectURL(blob);
            img.alt = 'Preview';
            preview.appendChild(img);

            applied = true;
            modal.hide();
        }, 'image/jpeg', 0.9);
    });

    // Show / hide password
    document.querySelectorAll('.pw-eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var field = document.getElementById(btn.getAttribute('data-target'));
            var icon = btn.querySelector('i');
            var hidden = field.type === 'password';
            field.type = hidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !hidden);
            icon.classList.toggle('bi-eye-slash', hidden);
            btn.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
        });
    });
})();
</script>
@endpush