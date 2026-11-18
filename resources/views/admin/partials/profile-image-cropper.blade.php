
@php
    $initialPhotoUrl = $initialPhotoUrl ?? null;
    $initialPhotoAlt = $initialPhotoAlt ?? 'Profile Photo';
@endphp

@push('styles')
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">

<style>
    .pf-avatar-md {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }

    .crop-stage {
        width: 100%;
        height: 380px;
        background: #1e293b;
        overflow: hidden;
    }

    .crop-stage img {
        display: block;
        max-width: 100%;
    }

    .crop-stage .cropper-view-box,
    .crop-stage .cropper-face {
        border-radius: 50%;
        outline: 2px solid #fff;
    }

    .crop-tools {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 15px;
    }

    .crop-angle {
        width: 100%;
    }

    #cropAngleValue {
        min-width: 45px;
        text-align: right;
    }

    .crop-preview-area {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 15px;
    }

    @media (max-width: 576px) {
        .crop-stage {
            height: 300px;
        }
    }
</style>
@endpush

<div class="mt-3">
    <label class="form-label">Photo Preview</label>

    <div class="crop-preview-area">
        <img
            id="avatarPreviewImage"
            class="pf-avatar-md"
            src="{{ $initialPhotoUrl ?? '' }}"
            alt="{{ $initialPhotoAlt }}"
            style="{{ $initialPhotoUrl ? '' : 'display:none;' }}"
        >

        <span
            id="avatarPreviewEmpty"
            class="text-muted"
            style="{{ $initialPhotoUrl ? 'display:none;' : '' }}"
        >
            No photo selected
        </span>
    </div>

    <small class="text-muted d-block mt-2">
        Select a photo above. You can crop and adjust it before saving.
    </small>
</div>

<div class="modal fade"
     id="cropModal"
     tabindex="-1"
     aria-labelledby="cropModalLabel"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="cropModalLabel">
                    Adjust Profile Photo
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>
            </div>

            <div class="modal-body">

                <div class="crop-stage">
                    <img id="cropImage" src="" alt="Image to crop">
                </div>

                <div class="crop-tools">

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            id="cropRotateLeft">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Rotate Left
                    </button>

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            id="cropRotateRight">
                        <i class="bi bi-arrow-clockwise"></i>
                        Rotate Right
                    </button>

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            id="cropFlipH">
                        Flip H
                    </button>

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            id="cropFlipV">
                        Flip V
                    </button>

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            id="cropZoomIn">
                        <i class="bi bi-zoom-in"></i>
                        Zoom In
                    </button>

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            id="cropZoomOut">
                        <i class="bi bi-zoom-out"></i>
                        Zoom Out
                    </button>

                    <button type="button"
                            class="btn btn-outline-danger btn-sm"
                            id="cropReset">
                        <i class="bi bi-arrow-repeat"></i>
                        Reset
                    </button>

                </div>

                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="cropAngle" class="form-label mb-0">
                            Fine Rotation
                        </label>

                        <span id="cropAngleValue">0°</span>
                    </div>

                    <input type="range"
                           class="form-range crop-angle"
                           id="cropAngle"
                           min="-45"
                           max="45"
                           step="1"
                           value="0">
                </div>

                <small class="text-muted d-block mt-2">
                    Move the image inside the circular frame to position it.
                </small>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        class="btn btn-primary"
                        id="cropApply">
                    <i class="bi bi-check-circle"></i>
                    Use This Photo
                </button>

            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('profile_image');
    const modalElement = document.getElementById('cropModal');
    const cropImage = document.getElementById('cropImage');
    const previewImage = document.getElementById('avatarPreviewImage');
    const previewEmpty = document.getElementById('avatarPreviewEmpty');

    if (!input || !modalElement || !cropImage || !previewImage) {
        return;
    }

    if (typeof bootstrap === 'undefined' || typeof Cropper === 'undefined') {
        console.error('Bootstrap or Cropper.js could not be loaded.');
        return;
    }

    const cropModal = bootstrap.Modal.getOrCreateInstance(modalElement);

    let cropper = null;
    let imageUrl = null;
    let previewUrl = null;
    let modalReady = false;
    let imageReady = false;
    let baseAngle = 0;
    let fineAngle = 0;
    let scaleX = 1;
    let scaleY = 1;
    let applied = false;

    function destroyCropper() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    function initializeCropper() {
        if (!modalReady || !imageReady || cropper) {
            return;
        }

        destroyCropper();

        cropper = new Cropper(cropImage, {
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
    }

    function updateRotation() {
        if (cropper) {
            cropper.rotateTo(baseAngle + fineAngle);
        }

        document.getElementById('cropAngleValue').textContent =
            fineAngle + '°';
    }

    input.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            return;
        }

        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file.');
            this.value = '';
            return;
        }

        if (imageUrl) {
            URL.revokeObjectURL(imageUrl);
        }

        imageUrl = URL.createObjectURL(file);

        applied = false;
        modalReady = false;
        imageReady = false;

        baseAngle = 0;
        fineAngle = 0;
        scaleX = 1;
        scaleY = 1;

        destroyCropper();

        document.getElementById('cropAngle').value = 0;
        document.getElementById('cropAngleValue').textContent = '0°';

        cropImage.onload = function () {
            imageReady = true;
            initializeCropper();
        };

        cropImage.onerror = function () {
            alert('Unable to load this image. Please choose another photo.');
            input.value = '';
        };

        cropImage.src = imageUrl;

        cropModal.show();
    });

    modalElement.addEventListener('shown.bs.modal', function () {
        modalReady = true;

        if (cropImage.complete && cropImage.naturalWidth > 0) {
            imageReady = true;
        }

        initializeCropper();
    });

    document.getElementById('cropRotateLeft').addEventListener('click', function () {
        if (!cropper) return;

        baseAngle -= 90;
        updateRotation();
    });

    document.getElementById('cropRotateRight').addEventListener('click', function () {
        if (!cropper) return;

        baseAngle += 90;
        updateRotation();
    });

    document.getElementById('cropFlipH').addEventListener('click', function () {
        if (!cropper) return;

        scaleX *= -1;
        cropper.scaleX(scaleX);
    });

    document.getElementById('cropFlipV').addEventListener('click', function () {
        if (!cropper) return;

        scaleY *= -1;
        cropper.scaleY(scaleY);
    });

    document.getElementById('cropZoomIn').addEventListener('click', function () {
        if (cropper) cropper.zoom(0.1);
    });

    document.getElementById('cropZoomOut').addEventListener('click', function () {
        if (cropper) cropper.zoom(-0.1);
    });

    document.getElementById('cropAngle').addEventListener('input', function () {
        fineAngle = Number(this.value);
        updateRotation();
    });

    document.getElementById('cropReset').addEventListener('click', function () {
        if (!cropper) return;

        cropper.reset();

        baseAngle = 0;
        fineAngle = 0;
        scaleX = 1;
        scaleY = 1;

        document.getElementById('cropAngle').value = 0;
        updateRotation();
    });

    document.getElementById('cropApply').addEventListener('click', function () {
        if (!cropper) {
            alert('Please wait for the image editor to load.');
            return;
        }

        const canvas = cropper.getCroppedCanvas({
            width: 600,
            height: 600,
            fillColor: '#ffffff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!canvas) {
            alert('Unable to crop this image. Please try again.');
            return;
        }

        canvas.toBlob(function (blob) {
            if (!blob) {
                alert('Unable to process the cropped image.');
                return;
            }

            const croppedFile = new File(
                [blob],
                'profile-' + Date.now() + '.jpg',
                {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                }
            );

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            input.files = dataTransfer.files;

            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }

            previewUrl = URL.createObjectURL(blob);
            previewImage.src = previewUrl;
            previewImage.style.display = 'block';
            previewEmpty.style.display = 'none';

            applied = true;
            cropModal.hide();

        }, 'image/jpeg', 0.9);
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        destroyCropper();

        modalReady = false;
        imageReady = false;

        if (imageUrl) {
            URL.revokeObjectURL(imageUrl);
            imageUrl = null;
        }

        if (!applied) {
            input.value = '';
        }

        cropImage.removeAttribute('src');
    });

});
</script>
@endpush
