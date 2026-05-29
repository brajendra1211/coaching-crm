<style>
    .gallery-form-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 22px;
        align-items: start;
    }

    .form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .form-card-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #fff, #f8fafc);
    }

    .form-card-head h3 {
        margin: 0;
        color: #111827;
        font-size: 19px;
    }

    .form-card-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .form-card-body {
        padding: 22px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-weight: 900;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 13px 14px;
        font-size: 15px;
        outline: none;
    }

    .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }

    .help {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .image-box {
        border: 1px dashed #93c5fd;
        background: #eff6ff;
        border-radius: 18px;
        padding: 16px;
    }

    .current-image,
    .preview-image,
    .youtube-preview {
        width: 100%;
        max-height: 260px;
        object-fit: cover;
        border-radius: 16px;
        border: 1px solid #bfdbfe;
        background: #fff;
        margin-bottom: 14px;
    }

    .preview-image,
    .youtube-preview {
        display: none;
        margin-top: 14px;
        margin-bottom: 0;
    }

    .check-card {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 13px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        margin-bottom: 12px;
        cursor: pointer;
    }

    .check-card input {
        width: auto;
        margin-top: 3px;
    }

    .error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 14px;
        border-radius: 14px;
        margin-bottom: 18px;
    }

    .error-box ul {
        margin: 8px 0 0;
        padding-left: 18px;
    }

    .media-field {
        display: none;
    }

    .media-field.active {
        display: block;
    }

    @media(max-width: 1050px) {
        .gallery-form-wrap,
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

@if($errors->any())
    <div class="error-box">
        <strong>Please fix errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $selectedType = old('type', $galleryItem->type ?: 'image');
@endphp

<div class="gallery-form-wrap">
    <div>
        <div class="form-card">
            <div class="form-card-head">
                <h3>Gallery Details</h3>
                <p>Add image or YouTube video for website gallery page.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Media Type *</label>
                        <select name="type" id="typeSelect" required>
                            <option value="image" {{ $selectedType === 'image' ? 'selected' : '' }}>Image</option>
                            <option value="youtube" {{ $selectedType === 'youtube' ? 'selected' : '' }}>YouTube Video</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" value="{{ old('title', $galleryItem->title) }}" required placeholder="Annual Function 2026">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" value="{{ old('category', $galleryItem->category) }}" placeholder="Events, Classroom, Results">
                        <span class="help">Same category name use karne par frontend me filter ban jayega.</span>
                    </div>

                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $galleryItem->sort_order ?? 0) }}" min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Short description about this gallery item...">{{ old('description', $galleryItem->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Media Upload / Link</h3>
                <p>Image upload karein ya YouTube video ka link paste karein.</p>
            </div>

            <div class="form-card-body">
                <div id="imageField" class="media-field">
                    <div class="form-group">
                        <label>Gallery Image</label>

                        <div class="image-box">
                            @if($isEdit && $galleryItem->image)
                                <img src="{{ asset('storage/' . $galleryItem->image) }}" class="current-image" alt="{{ $galleryItem->title }}">
                            @endif

                            <input type="file" name="image" id="imageInput" accept="image/*">

                            <span class="help">
                                Recommended: 1200x800px or 1200x630px. JPG/PNG/WebP, max 4MB.
                            </span>

                            <img id="imagePreview" class="preview-image" alt="Preview">

                            @if($isEdit && $galleryItem->image)
                                <label class="check-card" style="margin-top:14px;background:#fff;">
                                    <input type="checkbox" name="remove_image" value="1">
                                    <div>
                                        <strong>Remove Current Image</strong>
                                        <br>
                                        <small>Current image remove ho jayegi.</small>
                                    </div>
                                </label>
                            @endif
                        </div>
                    </div>
                </div>

                <div id="youtubeField" class="media-field">
                    <div class="form-group">
                        <label>YouTube Link</label>
                        <input
                            type="text"
                            name="youtube_url"
                            id="youtubeInput"
                            value="{{ old('youtube_url', $galleryItem->youtube_url) }}"
                            placeholder="https://www.youtube.com/watch?v=VIDEO_ID"
                        >
                        <span class="help">
                            YouTube normal URL, shorts URL, youtu.be URL supported hai.
                        </span>

                        <img id="youtubePreview" class="youtube-preview" alt="YouTube Preview">
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
            <a href="{{ route('admin.gallery.index') }}" class="btn btn-light">Cancel</a>
            <a href="{{ route('gallery.index') }}" target="_blank" class="btn btn-dark">View Gallery</a>
        </div>
    </div>

    <aside>
        <div class="form-card">
            <div class="form-card-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;">
                <h3 style="color:#fff;">Publish Settings</h3>
                <p style="color:rgba(255,255,255,.9);">Status and featured settings.</p>
            </div>

            <div class="form-card-body">
                <div class="form-group">
                    <label>Status</label>
                    @php $status = old('status', $galleryItem->status ?: 'active'); @endphp

                    <select name="status">
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <label class="check-card">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $galleryItem->is_featured) ? 'checked' : '' }}>
                    <div>
                        <strong>Featured Item</strong>
                        <br>
                        <small>Website gallery hero/featured section me highlight karna ho to enable karein.</small>
                    </div>
                </label>

                <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px;padding:14px;margin-top:14px;">
                    <strong style="display:block;color:#111827;margin-bottom:8px;">Best Practice</strong>
                    <ul style="margin:0;padding-left:18px;color:#64748b;line-height:1.7;font-size:13px;">
                        <li>Image size compressed rakhein.</li>
                        <li>Category same spelling me add karein.</li>
                        <li>YouTube video public hona chahiye.</li>
                        <li>Featured item sirf best media par lagayein.</li>
                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
    const typeSelect = document.getElementById('typeSelect');
    const imageField = document.getElementById('imageField');
    const youtubeField = document.getElementById('youtubeField');

    function toggleMediaFields() {
        const type = typeSelect.value;

        imageField.classList.toggle('active', type === 'image');
        youtubeField.classList.toggle('active', type === 'youtube');
    }

    typeSelect.addEventListener('change', toggleMediaFields);
    toggleMediaFields();

    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    imageInput?.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            imagePreview.style.display = 'none';
            imagePreview.removeAttribute('src');
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            imagePreview.src = event.target.result;
            imagePreview.style.display = 'block';
        };

        reader.readAsDataURL(file);
    });

    const youtubeInput = document.getElementById('youtubeInput');
    const youtubePreview = document.getElementById('youtubePreview');

    function extractYoutubeId(url) {
        if (!url) return null;

        url = url.trim();

        if (/^[a-zA-Z0-9_-]{11}$/.test(url)) {
            return url;
        }

        const patterns = [
            /youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/,
            /youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
            /youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/,
            /youtu\.be\/([a-zA-Z0-9_-]{11})/,
        ];

        for (const pattern of patterns) {
            const match = url.match(pattern);

            if (match) {
                return match[1];
            }
        }

        return null;
    }

    function updateYoutubePreview() {
        const id = extractYoutubeId(youtubeInput.value);

        if (!id) {
            youtubePreview.style.display = 'none';
            youtubePreview.removeAttribute('src');
            return;
        }

        youtubePreview.src = `https://img.youtube.com/vi/${id}/hqdefault.jpg`;
        youtubePreview.style.display = 'block';
    }

    youtubeInput?.addEventListener('input', updateYoutubePreview);
    updateYoutubePreview();
</script>