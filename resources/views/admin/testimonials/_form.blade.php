<style>
    .testimonial-form-wrap {
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
        min-height: 170px;
        resize: vertical;
        line-height: 1.7;
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
    .preview-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        object-position: center top;
        border-radius: 28px;
        border: 1px solid #bfdbfe;
        background: #fff;
        margin-bottom: 14px;
    }

    .preview-image {
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

    .preview-card {
        background:
            radial-gradient(circle at top right, rgba(37,99,235,.12), transparent 28%),
            #fff;
        border: 1px solid #e5e7eb;
        border-radius: 24px;
        padding: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
    }

    .preview-avatar {
        width: 70px;
        height: 70px;
        border-radius: 22px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        margin-bottom: 14px;
    }

    .preview-stars {
        color: #f59e0b;
        font-weight: 900;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }

    @media(max-width: 1050px) {
        .testimonial-form-wrap,
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

<div class="testimonial-form-wrap">
    <div>
        <div class="form-card">
            <div class="form-card-head">
                <h3>Student / Parent Details</h3>
                <p>Add name, course, location and display information.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" id="nameInput" value="{{ old('name', $testimonial->name) }}" required placeholder="Student Name">
                    </div>

                    <div class="form-group">
                        <label>Designation</label>
                        <input type="text" name="designation" id="designationInput" value="{{ old('designation', $testimonial->designation) }}" placeholder="Student / Parent / Topper">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" name="course_name" id="courseInput" value="{{ old('course_name', $testimonial->course_name) }}" placeholder="Class 10 Foundation">
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" id="locationInput" value="{{ old('location', $testimonial->location) }}" placeholder="Noida / Delhi">
                    </div>
                </div>

                <div class="form-group">
                    <label>Review *</label>
                    <textarea name="review" id="reviewInput" required placeholder="Write testimonial/review here...">{{ old('review', $testimonial->review) }}</textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Rating *</label>
                        @php $selectedRating = old('rating', $testimonial->rating ?: 5); @endphp

                        <select name="rating" id="ratingInput" required>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ (int) $selectedRating === $i ? 'selected' : '' }}>
                                    {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" min="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Photo</h3>
                <p>Optional student/parent image upload karein.</p>
            </div>

            <div class="form-card-body">
                <div class="image-box">
                    @if($isEdit && $testimonial->image)
                        <img src="{{ asset('storage/' . $testimonial->image) }}" class="current-image" alt="{{ $testimonial->name }}">
                    @endif

                    <input type="file" name="image" id="imageInput" accept="image/*">

                    <span class="help">Optional. Recommended: square photo, JPG/PNG/WebP, max 4MB.</span>

                    <img id="imagePreview" class="preview-image" alt="Preview">

                    @if($isEdit && $testimonial->image)
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

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-light">Cancel</a>
            <a href="{{ route('testimonials.index') }}" target="_blank" class="btn btn-dark">View Testimonials</a>
        </div>
    </div>

    <aside>
        <div class="form-card">
            <div class="form-card-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;">
                <h3 style="color:#fff;">Publish Settings</h3>
                <p style="color:rgba(255,255,255,.9);">Status and featured option.</p>
            </div>

            <div class="form-card-body">
                <div class="form-group">
                    <label>Status</label>
                    @php $status = old('status', $testimonial->status ?: 'active'); @endphp

                    <select name="status">
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <label class="check-card">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $testimonial->is_featured) ? 'checked' : '' }}>
                    <div>
                        <strong>Featured Testimonial</strong>
                        <br>
                        <small>Website hero/featured section me highlight karna ho to enable karein.</small>
                    </div>
                </label>

                <div class="preview-card">
                    <div class="preview-avatar" id="previewAvatar">
                        {{ $testimonial->initials ?: 'ST' }}
                    </div>

                    <div class="preview-stars" id="previewStars">
                        {!! str_repeat('★', $testimonial->rating ?: 5) !!}
                    </div>

                    <p id="previewReview" style="color:#475569;line-height:1.7;margin:0 0 16px;">
                        {{ $testimonial->review ?: 'Review preview will show here...' }}
                    </p>

                    <strong id="previewName" style="display:block;color:#0f172a;">
                        {{ $testimonial->name ?: 'Student Name' }}
                    </strong>

                    <small id="previewMeta" style="color:#64748b;">
                        {{ $testimonial->course_name ?: 'Course Name' }}
                    </small>
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
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

    const nameInput = document.getElementById('nameInput');
    const courseInput = document.getElementById('courseInput');
    const locationInput = document.getElementById('locationInput');
    const reviewInput = document.getElementById('reviewInput');
    const ratingInput = document.getElementById('ratingInput');

    const previewAvatar = document.getElementById('previewAvatar');
    const previewStars = document.getElementById('previewStars');
    const previewReview = document.getElementById('previewReview');
    const previewName = document.getElementById('previewName');
    const previewMeta = document.getElementById('previewMeta');

    function initials(value) {
        return (value || 'ST')
            .trim()
            .split(/\s+/)
            .slice(0, 2)
            .map(word => word.charAt(0).toUpperCase())
            .join('');
    }

    function updatePreview() {
        const rating = parseInt(ratingInput.value || 5);
        const course = courseInput.value || 'Course Name';
        const location = locationInput.value || '';

        previewAvatar.textContent = initials(nameInput.value);
        previewStars.textContent = '★'.repeat(rating) + '☆'.repeat(5 - rating);
        previewReview.textContent = reviewInput.value || 'Review preview will show here...';
        previewName.textContent = nameInput.value || 'Student Name';
        previewMeta.textContent = location ? `${course} • ${location}` : course;
    }

    [nameInput, courseInput, locationInput, reviewInput, ratingInput].forEach(input => {
        input?.addEventListener('input', updatePreview);
        input?.addEventListener('change', updatePreview);
    });

    updatePreview();
</script>