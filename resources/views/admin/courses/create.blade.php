@extends('admin.layouts.app')

@section('title', 'Add Course')
@section('page_title', 'Add New Course')

@push('styles')
<style>
    .course-form-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 22px;
        align-items: flex-start;
    }

    .form-section {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(17, 24, 39, .07);
        overflow: hidden;
        margin-bottom: 22px;
    }

    .form-section-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .form-section-head h3 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .form-section-head p {
        margin: 5px 0 0;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
    }

    .form-section-body {
        padding: 22px;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .input-help {
        display: block;
        margin-top: 6px;
        color: #6b7280;
        font-size: 12px;
        line-height: 1.5;
    }

    .required {
        color: #dc2626;
    }

    .side-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(17, 24, 39, .07);
        overflow: hidden;
    }

    .publish-card {
        position: sticky;
        top: 95px;
        z-index: 5;
    }

    .side-card-head {
        padding: 18px 20px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
    }

    .side-card-head h3 {
        margin: 0 0 5px;
        color: #fff;
        font-size: 18px;
    }

    .side-card-head p {
        margin: 0;
        opacity: .9;
        font-size: 13px;
        line-height: 1.5;
    }

    .side-card-body {
        padding: 20px;
    }

    .image-preview-box {
        width: 100%;
        height: 210px;
        background:
            radial-gradient(circle at top left, rgba(37, 99, 235, .12), transparent 30%),
            linear-gradient(135deg, #f8fafc, #eef2ff);
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #64748b;
        font-weight: 800;
        text-align: center;
        padding: 18px;
        margin-bottom: 16px;
    }

    .image-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .status-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 15px;
        margin-top: 16px;
    }

    .status-check {
        display: flex;
        gap: 10px;
        align-items: center;
        margin: 0;
        cursor: pointer;
    }

    .status-check input {
        width: auto;
        transform: scale(1.1);
    }

    .status-check strong {
        color: #111827;
        font-size: 14px;
    }

    .status-check span {
        display: block;
        color: #6b7280;
        font-size: 12px;
        margin-top: 2px;
        line-height: 1.4;
    }

    .action-area {
        display: grid;
        gap: 10px;
        margin-top: 18px;
    }

    .seo-preview {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        margin-top: 16px;
    }

    .seo-preview-title {
        color: #1a0dab;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .seo-preview-url {
        color: #0f9d58;
        font-size: 13px;
        margin-bottom: 6px;
        word-break: break-all;
    }

    .seo-preview-desc {
        color: #4b5563;
        font-size: 13px;
        line-height: 1.5;
    }

    .tips-list {
        margin: 0;
        padding-left: 18px;
        color: #4b5563;
        line-height: 1.8;
        font-size: 13px;
    }

    .toolbar-note {
        display: inline-flex;
        padding: 8px 12px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 900;
        border: 1px solid #bfdbfe;
    }

    @media(max-width: 1100px) {
        .course-form-wrap {
            grid-template-columns: 1fr;
        }

        .publish-card {
            position: static;
        }
    }

    @media(max-width: 760px) {
        .form-grid-2,
        .form-grid-3 {
            grid-template-columns: 1fr;
        }

        .form-section-body,
        .side-card-body {
            padding: 18px;
        }

        .form-section-head {
            padding: 16px 18px;
        }
    }
</style>
@endpush

@section('content')

<form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="course-form-wrap">
        <div>
            <div class="form-section">
                <div class="form-section-head">
                    <div>
                        <h3>Course Basic Details</h3>
                        <p>Enter the primary course information that will appear across the website and admin panel.</p>
                    </div>

                    <span class="toolbar-note">SEO-Ready Course Page</span>
                </div>

                <div class="form-section-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Course Title <span class="required">*</span></label>
                            <input
                                type="text"
                                name="title"
                                id="courseTitle"
                                value="{{ old('title') }}"
                                placeholder="Example: NEET Foundation Course"
                                required
                            >
                            <small class="input-help">Use a clear and searchable course name. Example: NEET Foundation Course for Class 11.</small>
                        </div>

                        <div class="form-group">
                            <label>URL Slug</label>
                            <input
                                type="text"
                                name="slug"
                                id="courseSlug"
                                value="{{ old('slug') }}"
                                placeholder="neet-foundation-course"
                            >
                            <small class="input-help">This will be used in the course page URL. Leave it blank to generate automatically.</small>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label>Course Category</label>
                            <input
                                type="text"
                                name="course_type"
                                value="{{ old('course_type') }}"
                                placeholder="NEET / IIT JEE / SSC / Tuition"
                            >
                            <small class="input-help">Group the course by exam, stream, or program type.</small>
                        </div>

                        <div class="form-group">
                            <label>Class / Level</label>
                            <input
                                type="text"
                                name="class_level"
                                value="{{ old('class_level') }}"
                                placeholder="Class 11 / Class 12 / Dropper"
                            >
                            <small class="input-help">Mention the student level this course is designed for.</small>
                        </div>

                        <div class="form-group">
                            <label>Duration</label>
                            <input
                                type="text"
                                name="duration"
                                value="{{ old('duration') }}"
                                placeholder="12 Months"
                            >
                            <small class="input-help">Example: 3 Months, 6 Months, 1 Year, Weekend Batch.</small>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Regular Fee</label>
                            <input
                                type="number"
                                step="0.01"
                                name="fee"
                                value="{{ old('fee') }}"
                                placeholder="45000"
                            >
                            <small class="input-help">Enter the standard course fee before any discount.</small>
                        </div>

                        <div class="form-group">
                            <label>Offer Fee</label>
                            <input
                                type="number"
                                step="0.01"
                                name="offer_fee"
                                value="{{ old('offer_fee') }}"
                                placeholder="39999"
                            >
                            <small class="input-help">Enter the discounted fee, if any. This will be highlighted on the website.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-head">
                    <div>
                        <h3>Course Content</h3>
                        <p>Add detailed course information for students, parents, and search engines.</p>
                    </div>
                </div>

                <div class="form-section-body">
                    <div class="form-group">
                        <label>Short Description</label>
                        <textarea
                            name="short_description"
                            placeholder="Write a short summary that explains the main benefit of this course."
                        >{{ old('short_description') }}</textarea>
                        <small class="input-help">Recommended length: 120–160 characters. This is used on cards, listings, and previews.</small>
                    </div>

                    <div class="form-group">
                        <label>Full Description</label>
                        <textarea
                            name="description"
                            style="min-height:170px;"
                            placeholder="Describe the course structure, target students, teaching method, batch details, and learning outcomes."
                        >{{ old('description') }}</textarea>
                        <small class="input-help">Write detailed, useful content. Include course benefits, batch format, exam preparation approach, and student outcomes.</small>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Subjects Covered</label>
                            <textarea
                                name="subjects"
                                placeholder="Physics&#10;Chemistry&#10;Biology"
                            >{{ old('subjects') }}</textarea>
                            <small class="input-help">Add one subject per line for better display on the course page.</small>
                        </div>

                        <div class="form-group">
                            <label>Eligibility Criteria</label>
                            <textarea
                                name="eligibility"
                                placeholder="Class 11 students&#10;Class 12 students&#10;Dropper students"
                            >{{ old('eligibility') }}</textarea>
                            <small class="input-help">Mention who can enroll in this course.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Course Highlights / Features</label>
                        <textarea
                            name="features"
                            placeholder="Daily classroom sessions&#10;Weekly tests and performance analysis&#10;Doubt-clearing sessions&#10;Study material and assignments"
                        >{{ old('features') }}</textarea>
                        <small class="input-help">Add one feature per line. These will be shown as key selling points on the frontend.</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-head">
                    <div>
                        <h3>SEO Settings</h3>
                        <p>Optimize this course page for search engines, local keywords, and organic admissions.</p>
                    </div>
                </div>

                <div class="form-section-body">
                    <div class="form-group">
                        <label>SEO Title</label>
                        <input
                            type="text"
                            name="seo_title"
                            id="seoTitle"
                            value="{{ old('seo_title') }}"
                            placeholder="Best NEET Coaching in Greater Noida"
                        >
                        <small class="input-help">Recommended length: 50–60 characters. Include course name and target location.</small>
                    </div>

                    <div class="form-group">
                        <label>SEO Meta Description</label>
                        <textarea
                            name="seo_description"
                            id="seoDescription"
                            placeholder="Join expert-led NEET coaching in Greater Noida with structured classes, tests, doubt sessions, and result-focused preparation."
                        >{{ old('seo_description') }}</textarea>
                        <small class="input-help">Recommended length: 140–160 characters. Explain the course benefit clearly.</small>
                    </div>

                    <div class="form-group">
                        <label>SEO Keywords</label>
                        <textarea
                            name="seo_keywords"
                            placeholder="neet coaching greater noida, best neet classes, coaching institute, medical entrance preparation"
                        >{{ old('seo_keywords') }}</textarea>
                        <small class="input-help">Add comma-separated keywords. Use course, class, exam, and location-based terms.</small>
                    </div>

                    <div class="seo-preview">
                        <div class="seo-preview-title" id="seoPreviewTitle">
                            Best Coaching Course in Your City
                        </div>

                        <div class="seo-preview-url">
                            {{ url('/courses') }}/<span id="seoPreviewSlug">course-slug</span>
                        </div>

                        <div class="seo-preview-desc" id="seoPreviewDesc">
                            SEO meta description preview will appear here.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="side-card publish-card">
                <div class="side-card-head">
                    <h3>Publish Course</h3>
                    <p>Upload the course image, manage visibility, and publish this course.</p>
                </div>

                <div class="side-card-body">
                    <div class="image-preview-box" id="imagePreviewBox">
                        <span>Course Image Preview</span>
                    </div>

                    <div class="form-group">
                        <label>Course Banner Image</label>
                        <input type="file" name="image" id="courseImage" accept="image/*">
                        <small class="input-help">Recommended size: 900×600px. Supported formats: JPG, PNG, WebP.</small>
                    </div>

                    <div class="status-box">
                        <label class="status-check">
                            <input type="checkbox" name="status" value="active" checked>
                            <div>
                                <strong>Publish as Active</strong>
                                <span>Active courses will be visible on the public website.</span>
                            </div>
                        </label>
                    </div>

                    <div class="action-area">
                        <button type="submit" class="btn btn-primary">
                            Save Course
                        </button>

                        <a href="{{ route('admin.courses.index') }}" class="btn btn-light">
                            Back to Courses
                        </a>
                    </div>
                </div>
            </div>

            <div class="side-card" style="margin-top:18px;">
                <div class="side-card-head">
                    <h3>SEO Best Practices</h3>
                    <p>Use these guidelines to make the course page more search-friendly.</p>
                </div>

                <div class="side-card-body">
                    <ul class="tips-list">
                        <li>Use the course name and target location in the SEO title.</li>
                        <li>Write a benefit-driven short description for students and parents.</li>
                        <li>Add course highlights as one feature per line for better readability.</li>
                        <li>Use location-based keywords such as “Best NEET Coaching in Greater Noida”.</li>
                        <li>Upload a clear, high-quality course banner image.</li>
                        <li>Include subjects, eligibility, duration, and batch benefits in the course content.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const titleInput = document.getElementById('courseTitle');
        const slugInput = document.getElementById('courseSlug');
        const seoTitleInput = document.getElementById('seoTitle');
        const seoDescriptionInput = document.getElementById('seoDescription');

        const seoPreviewTitle = document.getElementById('seoPreviewTitle');
        const seoPreviewSlug = document.getElementById('seoPreviewSlug');
        const seoPreviewDesc = document.getElementById('seoPreviewDesc');

        const imageInput = document.getElementById('courseImage');
        const imagePreviewBox = document.getElementById('imagePreviewBox');

        let slugTouched = false;

        function slugify(text) {
            return text
                .toString()
                .toLowerCase()
                .trim()
                .replace(/[\s_]+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function updateSeoPreview() {
            const title = seoTitleInput.value || titleInput.value || 'Best Coaching Course in Your City';
            const slug = slugInput.value || slugify(titleInput.value) || 'course-slug';
            const desc = seoDescriptionInput.value || 'SEO meta description preview will appear here.';

            seoPreviewTitle.textContent = title;
            seoPreviewSlug.textContent = slug;
            seoPreviewDesc.textContent = desc;
        }

        slugInput.addEventListener('input', function () {
            slugTouched = true;
            slugInput.value = slugify(slugInput.value);
            updateSeoPreview();
        });

        titleInput.addEventListener('input', function () {
            if (!slugTouched) {
                slugInput.value = slugify(titleInput.value);
            }

            updateSeoPreview();
        });

        seoTitleInput.addEventListener('input', updateSeoPreview);
        seoDescriptionInput.addEventListener('input', updateSeoPreview);

        imageInput.addEventListener('change', function () {
            const file = imageInput.files[0];

            if (!file) {
                imagePreviewBox.innerHTML = '<span>Course Image Preview</span>';
                return;
            }

            const reader = new FileReader();

            reader.onload = function (event) {
                imagePreviewBox.innerHTML = '<img src="' + event.target.result + '" alt="Course Preview">';
            };

            reader.readAsDataURL(file);
        });

        updateSeoPreview();
    });
</script>
@endpush