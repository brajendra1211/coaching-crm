<style>
    .blog-form-wrap {
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
    }

    .help {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
    }

    .image-box {
        border: 1px dashed #93c5fd;
        background: #eff6ff;
        border-radius: 18px;
        padding: 16px;
    }

    .current-image,
    .preview-image {
        width: 100%;
        max-height: 230px;
        object-fit: cover;
        border-radius: 16px;
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

    .ck.ck-editor__main > .ck-editor__editable {
        min-height: 380px;
        line-height: 1.8;
        font-size: 16px;
    }

    @media(max-width: 1050px) {
        .blog-form-wrap,
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

@if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:14px;border-radius:14px;margin-bottom:18px;">
        <strong>Please fix errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="blog-form-wrap">
    <div>
        <div class="form-card">
            <div class="form-card-head">
                <h3>Blog Details</h3>
                <p>Blog title, URL, category and author details.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Blog Title *</label>
                        <input type="text" name="title" id="titleInput" value="{{ old('title', $blog->title) }}" required>
                    </div>

                    <div class="form-group">
                        <label>URL Slug</label>
                        <input type="text" name="slug" id="slugInput" value="{{ old('slug', $blog->slug) }}">
                        <span class="help">Blank chhodne par title se auto slug banega.</span>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" value="{{ old('category', $blog->category) }}" placeholder="Admission, Exam Tips, Result">
                    </div>

                    <div class="form-group">
                        <label>Author Name</label>
                        <input type="text" name="author_name" value="{{ old('author_name', $blog->author_name) }}" placeholder="Admin">
                    </div>
                </div>

                <div class="form-group">
                    <label>Short Excerpt</label>
                    <textarea name="excerpt" id="excerptInput" rows="4">{{ old('excerpt', $blog->excerpt) }}</textarea>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Featured Image</h3>
                <p>This image will show in blog card, detail page and social sharing preview.</p>
            </div>

            <div class="form-card-body">
                <div class="image-box">
                    @if($isEdit && $blog->featured_image)
                        <img src="{{ asset('storage/' . $blog->featured_image) }}" class="current-image" alt="{{ $blog->title }}">
                    @endif

                    <input type="file" name="featured_image" id="featuredImageInput" accept="image/*">

                    <span class="help">Recommended: 1200x630px. JPG/PNG/WebP, max 4MB.</span>

                    <img id="featuredImagePreview" class="preview-image" alt="Preview">

                    @if($isEdit && $blog->featured_image)
                        <label class="check-card" style="margin-top:14px;background:#fff;">
                            <input type="checkbox" name="remove_featured_image" value="1">
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

        <div class="form-card">
            <div class="form-card-head">
                <h3>Blog Content</h3>
                <p>CKEditor me headings, paragraphs, list, links and table add kar sakte hain.</p>
            </div>

            <div class="form-card-body">
                <div class="form-group">
                    <label>Main Content</label>
                    <textarea name="content" id="contentEditor">{{ old('content', $blog->content) }}</textarea>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>SEO Settings</h3>
                <p>Google ranking and social sharing ke liye meta details.</p>
            </div>

            <div class="form-card-body">
                <div class="form-group">
                    <label>SEO Title</label>
                    <input type="text" name="seo_title" id="seoTitleInput" value="{{ old('seo_title', $blog->seo_title) }}">
                    <span class="help">Recommended: 50-60 characters.</span>
                </div>

                <div class="form-group">
                    <label>SEO Description</label>
                    <textarea name="seo_description" id="seoDescInput" rows="4">{{ old('seo_description', $blog->seo_description) }}</textarea>
                    <span class="help">Recommended: 150-160 characters.</span>
                </div>

                <div class="form-group">
                    <label>SEO Keywords</label>
                    <textarea name="seo_keywords" rows="3">{{ old('seo_keywords', $blog->seo_keywords) }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-light">Cancel</a>

            @if($isEdit && $blog->status === 'active')
                <a href="{{ route('blogs.show', $blog->slug) }}" target="_blank" class="btn btn-dark">View Blog</a>
            @endif
        </div>
    </div>

    <aside>
        <div class="form-card">
            <div class="form-card-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;">
                <h3 style="color:#fff;">Publish Settings</h3>
                <p style="color:rgba(255,255,255,.9);">Status, publish date and SEO preview.</p>
            </div>

            <div class="form-card-body">
                <div class="form-group">
                    <label>Status</label>
                    @php $status = old('status', $blog->status ?: 'draft'); @endphp
                    <select name="status">
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Published At</label>
                    <input
                        type="datetime-local"
                        name="published_at"
                        value="{{ old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                    >
                </div>

                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $blog->sort_order ?? 0) }}" min="0">
                </div>

                <div class="seo-preview">
                    <div class="seo-preview-title" id="seoTitlePreview">
                        {{ $blog->seo_title ?: ($blog->title ?: 'SEO title will show here') }}
                    </div>

                    <div class="seo-preview-url" id="seoUrlPreview">
                        {{ $blog->slug ? url('/blogs/' . $blog->slug) : url('/blogs/blog-url') }}
                    </div>

                    <div class="seo-preview-desc" id="seoDescPreview">
                        {{ $blog->seo_description ?: ($blog->excerpt ?: 'SEO description will show here.') }}
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
    let contentEditorInstance = null;

    ClassicEditor
        .create(document.querySelector('#contentEditor'), {
            toolbar: [
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
            ],
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            }
        })
        .then(editor => contentEditorInstance = editor)
        .catch(error => console.error(error));

    document.querySelector('form').addEventListener('submit', function () {
        if (contentEditorInstance) {
            document.querySelector('#contentEditor').value = contentEditorInstance.getData();
        }
    });

    function makeSlug(value) {
        return value.toString().toLowerCase().trim()
            .replace(/&/g, 'and')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    const titleInput = document.getElementById('titleInput');
    const slugInput = document.getElementById('slugInput');
    const seoTitleInput = document.getElementById('seoTitleInput');
    const seoDescInput = document.getElementById('seoDescInput');
    const excerptInput = document.getElementById('excerptInput');

    const seoTitlePreview = document.getElementById('seoTitlePreview');
    const seoUrlPreview = document.getElementById('seoUrlPreview');
    const seoDescPreview = document.getElementById('seoDescPreview');

    let slugManuallyChanged = {{ $isEdit ? 'true' : 'false' }};

    slugInput.addEventListener('input', function () {
        slugManuallyChanged = true;
        updatePreview();
    });

    titleInput.addEventListener('input', function () {
        if (!slugManuallyChanged) {
            slugInput.value = makeSlug(titleInput.value);
        }

        updatePreview();
    });

    seoTitleInput.addEventListener('input', updatePreview);
    seoDescInput.addEventListener('input', updatePreview);
    excerptInput.addEventListener('input', updatePreview);

    function updatePreview() {
        const slug = makeSlug(slugInput.value || titleInput.value || 'blog-url') || 'blog-url';

        seoUrlPreview.textContent = '{{ url('/blogs') }}/' + slug;
        seoTitlePreview.textContent = seoTitleInput.value || titleInput.value || 'SEO title will show here';
        seoDescPreview.textContent = seoDescInput.value || excerptInput.value || 'SEO description will show here.';
    }

    updatePreview();

    const imageInput = document.getElementById('featuredImageInput');
    const preview = document.getElementById('featuredImagePreview');

    imageInput.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            preview.style.display = 'none';
            preview.removeAttribute('src');
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            preview.src = event.target.result;
            preview.style.display = 'block';
        };

        reader.readAsDataURL(file);
    });
</script>