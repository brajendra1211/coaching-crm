@extends('admin.layouts.app')

@section('title', 'Edit Website Page')
@section('page_title', 'Edit Website Page')

@section('content')

<style>
    .page-builder-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 22px;
        align-items: flex-start;
    }

    .builder-card,
    .side-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
        overflow: hidden;
        margin-bottom: 22px;
    }

    .builder-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
    }

    .builder-head h3,
    .side-head h3 {
        margin: 0;
        font-size: 19px;
        font-weight: 900;
    }

    .builder-head h3 {
        color: #111827;
    }

    .builder-head p,
    .side-head p {
        margin: 6px 0 0;
        font-size: 13px;
        line-height: 1.5;
    }

    .builder-head p {
        color: #64748b;
    }

    .builder-body,
    .side-body {
        padding: 22px;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        color: #334155;
        font-weight: 900;
        margin-bottom: 8px;
        font-size: 14px;
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
        background: #fff;
        color: #111827;
        font-family: inherit;
    }

    .form-group textarea {
        min-height: 110px;
        resize: vertical;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
    }

    .input-help {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .required {
        color: #dc2626;
    }

    .error-text {
        display: block;
        color: #dc2626;
        font-size: 12px;
        font-weight: 800;
        margin-top: 6px;
    }

    .error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        border-radius: 16px;
        padding: 14px 16px;
        margin-bottom: 18px;
    }

    .error-box ul {
        margin: 8px 0 0;
        padding-left: 18px;
    }

    .image-upload-box {
        border: 1px dashed #93c5fd;
        background: #eff6ff;
        border-radius: 18px;
        padding: 16px;
    }

    .current-image {
        width: 100%;
        max-height: 240px;
        object-fit: cover;
        border-radius: 16px;
        margin-bottom: 14px;
        border: 1px solid #bfdbfe;
        background: #fff;
    }

    .image-preview {
        display: none;
        width: 100%;
        max-height: 230px;
        object-fit: cover;
        border-radius: 16px;
        margin-top: 14px;
        border: 1px solid #bfdbfe;
        background: #fff;
    }

    .check-card {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
        cursor: pointer;
        margin-bottom: 12px;
    }

    .check-card input {
        width: auto;
        margin-top: 3px;
        transform: scale(1.1);
    }

    .check-card strong {
        display: block;
        color: #111827;
        font-size: 14px;
    }

    .check-card span {
        display: block;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
        margin-top: 3px;
    }

    .side-card {
        position: sticky;
        top: 95px;
    }

    .side-head {
        padding: 18px 20px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
    }

    .side-head h3 {
        color: #fff;
    }

    .side-head p {
        color: rgba(255, 255, 255, .9);
    }

    .preview-box {
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        border-radius: 18px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .preview-box small {
        display: block;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .preview-box strong {
        color: #111827;
        font-size: 15px;
        line-height: 1.4;
        word-break: break-word;
    }

    .seo-preview {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 16px;
        margin-top: 14px;
    }

    .seo-preview-title {
        color: #1a0dab;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
        line-height: 1.4;
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
        color: #475569;
        line-height: 1.8;
        font-size: 13px;
    }

    .action-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .admin-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 14px;
        padding: 13px 18px;
        font-weight: 900;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
    }

    .admin-btn-primary {
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
    }

    .admin-btn-light {
        background: #f1f5f9;
        color: #0f172a;
        border: 1px solid #e2e8f0;
    }

    .ck.ck-editor {
        width: 100%;
    }

    .ck.ck-toolbar {
        border-radius: 14px 14px 0 0 !important;
        border-color: #cbd5e1 !important;
    }

    .ck.ck-editor__main > .ck-editor__editable {
        min-height: 380px;
        border-radius: 0 0 14px 14px !important;
        border-color: #cbd5e1 !important;
        line-height: 1.8;
        font-size: 16px;
    }

    @media(max-width: 1100px) {
        .page-builder-wrap {
            grid-template-columns: 1fr;
        }

        .side-card {
            position: static;
        }
    }

    @media(max-width: 768px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }

        .builder-body,
        .side-body {
            padding: 18px;
        }
    }
</style>

@if($errors->any())
    <div class="error-box">
        <strong>Please fix the following errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.pages.update', $page->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="page-builder-wrap">
        <div>
            <div class="builder-card">
                <div class="builder-head">
                    <h3>Page Basic Details</h3>
                    <p>Website page title, URL, page type and order update karein.</p>
                </div>

                <div class="builder-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Page Title <span class="required">*</span></label>
                            <input type="text" name="title" id="titleInput" value="{{ old('title', $page->title) }}" required>
                            @error('title') <span class="error-text">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>URL Slug</label>
                            <input type="text" name="slug" id="slugInput" value="{{ old('slug', $page->slug) }}">
                            <span class="input-help">Example: about-us, admission, contact-us</span>
                            @error('slug') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Page Type <span class="required">*</span></label>
                            @php $selectedType = old('page_type', $page->page_type ?: 'default'); @endphp

                            <select name="page_type" id="pageTypeSelect" required>
                                <option value="default" {{ $selectedType == 'default' ? 'selected' : '' }}>Default Page</option>
                                <option value="about" {{ $selectedType == 'about' ? 'selected' : '' }}>About Page</option>
                                <option value="courses" {{ $selectedType == 'courses' ? 'selected' : '' }}>Courses Page</option>
                                <option value="admission" {{ $selectedType == 'admission' ? 'selected' : '' }}>Admission Page</option>
                                <option value="results" {{ $selectedType == 'results' ? 'selected' : '' }}>Results Page</option>
                                <option value="gallery" {{ $selectedType == 'gallery' ? 'selected' : '' }}>Gallery Page</option>
                                <option value="contact" {{ $selectedType == 'contact' ? 'selected' : '' }}>Contact Page</option>
                                <option value="landing" {{ $selectedType == 'landing' ? 'selected' : '' }}>Landing Page</option>
                                <option value="policy" {{ $selectedType == 'policy' ? 'selected' : '' }}>Policy Page</option>
                            </select>

                            <span class="input-help">
                                Contact Page + slug <strong>contact</strong> public /contact manage karta hai.
                                Results Page + slug <strong>results</strong> public /results manage karta hai.
                                Courses Page + slug <strong>courses</strong> public /courses manage karta hai.
                            </span>

                            @error('page_type') <span class="error-text">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $page->sort_order ?? 0) }}" min="0">
                            @error('sort_order') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="builder-card">
                <div class="builder-head">
                    <h3>Hero Section</h3>
                    <p>Hero title, subtitle aur optional image update karein.</p>
                </div>

                <div class="builder-body">
                    <div class="form-group">
                        <label>Hero Title</label>
                        <input type="text" name="hero_title" value="{{ old('hero_title', $page->hero_title) }}">
                        @error('hero_title') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Hero Subtitle</label>
                        <textarea name="hero_subtitle">{{ old('hero_subtitle', $page->hero_subtitle) }}</textarea>
                        @error('hero_subtitle') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Hero Image</label>

                        <div class="image-upload-box">
                            @if($page->hero_image)
                                <img src="{{ asset('storage/' . $page->hero_image) }}" class="current-image" alt="{{ $page->title }}">
                            @endif

                            <input type="file" name="hero_image" id="heroImageInput" accept="image/*">

                            <span class="input-help">
                                Optional. New image upload karne par old image replace ho jayegi. Recommended 900x650px. Max 4MB.
                            </span>

                            <img id="heroImagePreview" class="image-preview" alt="New hero image preview">

                            @if($page->hero_image)
                                <label class="check-card" style="margin-top:14px;margin-bottom:0;background:#fff;">
                                    <input type="checkbox" name="remove_hero_image" value="1">
                                    <div>
                                        <strong>Remove Current Hero Image</strong>
                                        <span>Check karne par current image remove ho jayegi.</span>
                                    </div>
                                </label>
                            @endif
                        </div>

                        @error('hero_image') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="builder-card">
                <div class="builder-head">
                    <h3>Page Content</h3>
                    <p>SEO content, headings, paragraphs, lists and page details update karein.</p>
                </div>

                <div class="builder-body">
                    <div class="form-group">
                        <label>Main Content</label>
                        <textarea name="content" id="contentEditor">{{ old('content', $page->content) }}</textarea>
                        <span class="input-help">
                            Editor me H2/H3 headings, bullet points, table, internal links aur SEO content add kar sakte hain.
                        </span>
                        @error('content') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="builder-card">
                <div class="builder-head">
                    <h3>CTA Section</h3>
                    <p>Call-to-action content update karein.</p>
                </div>

                <div class="builder-body">
                    <div class="form-group">
                        <label>CTA Title</label>
                        <input type="text" name="cta_title" value="{{ old('cta_title', $page->cta_title) }}">
                        @error('cta_title') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>CTA Description</label>
                        <textarea name="cta_description">{{ old('cta_description', $page->cta_description) }}</textarea>
                        @error('cta_description') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="builder-card">
                <div class="builder-head">
                    <h3>SEO Settings</h3>
                    <p>Google search result ke liye SEO title, description aur keywords update karein.</p>
                </div>

                <div class="builder-body">
                    <div class="form-group">
                        <label>SEO Title</label>
                        <input type="text" name="seo_title" id="seoTitleInput" value="{{ old('seo_title', $page->seo_title) }}" maxlength="255">
                        <span class="input-help">Recommended: 50-60 characters.</span>
                        @error('seo_title') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>SEO Description</label>
                        <textarea name="seo_description" id="seoDescInput" maxlength="300">{{ old('seo_description', $page->seo_description) }}</textarea>
                        <span class="input-help">Recommended: 150-160 characters.</span>
                        @error('seo_description') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>SEO Keywords</label>
                        <textarea name="seo_keywords">{{ old('seo_keywords', $page->seo_keywords) }}</textarea>
                        @error('seo_keywords') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="action-row">
                <button type="submit" class="admin-btn admin-btn-primary">Update Website Page</button>
                <a href="{{ route('admin.pages.index') }}" class="admin-btn admin-btn-light">Cancel</a>
                <a href="{{ url('/' . $page->slug) }}" target="_blank" class="admin-btn admin-btn-light">View Page</a>
            </div>
        </div>

        <aside class="side-card">
            <div class="side-head">
                <h3>Publish Settings</h3>
                <p>Status, enquiry form and SEO preview.</p>
            </div>

            <div class="side-body">
                <label class="check-card">
                    <input type="checkbox" name="status" value="1" {{ old('status', $page->status === 'active') ? 'checked' : '' }}>
                    <div>
                        <strong>Active Page</strong>
                        <span>Active hoga to frontend par page open hoga.</span>
                    </div>
                </label>

                <label class="check-card">
                    <input type="checkbox" name="show_enquiry_form" value="1" {{ old('show_enquiry_form', $page->show_enquiry_form) ? 'checked' : '' }}>
                    <div>
                        <strong>Show Enquiry Form</strong>
                        <span>Page sidebar me lead form show hoga.</span>
                    </div>
                </label>

                <div class="preview-box">
                    <small>Frontend URL</small>
                    <strong id="urlPreview">{{ url('/' . $page->slug) }}</strong>
                </div>

                <div class="seo-preview">
                    <div class="seo-preview-title" id="seoTitlePreview">
                        {{ $page->seo_title ?: $page->title }}
                    </div>

                    <div class="seo-preview-url" id="seoUrlPreview">
                        {{ url('/' . $page->slug) }}
                    </div>

                    <div class="seo-preview-desc" id="seoDescPreview">
                        {{ $page->seo_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content ?: $page->hero_subtitle), 155) }}
                    </div>
                </div>

                <div style="margin-top:18px;">
                    <strong style="display:block;color:#111827;margin-bottom:8px;">SEO Tips</strong>
                    <ul class="tips-list">
                        <li>Slug short and keyword based rakhein.</li>
                        <li>Meta description unique rakhein.</li>
                        <li>Hero image relevant aur compressed ho.</li>
                        <li>Page content me internal links add karein.</li>
                    </ul>
                </div>
            </div>
        </aside>
    </div>
</form>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
    let contentEditorInstance = null;

    ClassicEditor
        .create(document.querySelector('#contentEditor'), {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'link',
                    '|',
                    'bulletedList',
                    'numberedList',
                    'blockQuote',
                    '|',
                    'insertTable',
                    'undo',
                    'redo'
                ]
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                ]
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            }
        })
        .then(editor => {
            contentEditorInstance = editor;
        })
        .catch(error => {
            console.error(error);
        });

    document.querySelector('form').addEventListener('submit', function () {
        if (contentEditorInstance) {
            document.querySelector('#contentEditor').value = contentEditorInstance.getData();
        }
    });

    function makeSlug(value) {
        return value
            .toString()
            .toLowerCase()
            .trim()
            .replace(/&/g, 'and')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    const titleInput = document.getElementById('titleInput');
    const slugInput = document.getElementById('slugInput');
    const seoTitleInput = document.getElementById('seoTitleInput');
    const seoDescInput = document.getElementById('seoDescInput');

    const urlPreview = document.getElementById('urlPreview');
    const seoUrlPreview = document.getElementById('seoUrlPreview');
    const seoTitlePreview = document.getElementById('seoTitlePreview');
    const seoDescPreview = document.getElementById('seoDescPreview');

    titleInput.addEventListener('input', updatePreview);
    slugInput.addEventListener('input', updatePreview);
    seoTitleInput.addEventListener('input', updatePreview);
    seoDescInput.addEventListener('input', updatePreview);

    function updatePreview() {
        const slug = makeSlug(slugInput.value || titleInput.value || 'page-url') || 'page-url';
        const fullUrl = '{{ url('/') }}/' + slug;

        urlPreview.textContent = fullUrl;
        seoUrlPreview.textContent = fullUrl;
        seoTitlePreview.textContent = seoTitleInput.value || titleInput.value || 'SEO title will show here';
        seoDescPreview.textContent = seoDescInput.value || 'SEO description will show here.';
    }

    updatePreview();

    const heroImageInput = document.getElementById('heroImageInput');
    const heroImagePreview = document.getElementById('heroImagePreview');

    heroImageInput.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            heroImagePreview.style.display = 'none';
            heroImagePreview.removeAttribute('src');
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            heroImagePreview.src = event.target.result;
            heroImagePreview.style.display = 'block';
        };

        reader.readAsDataURL(file);
    });
</script>

@endsection
