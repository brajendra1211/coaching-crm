<?php
    $isEdit = isset($isEdit) ? (bool) $isEdit : false;

    $currentRobots = old(
        'robots',
        $seoMeta->robots ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'
    );

    $currentStatus = old('status', $seoMeta->status ?? 'active');

    $ogImageSrc = ($isEdit && !empty($seoMeta->og_image))
        ? asset('storage/' . $seoMeta->og_image)
        : '';

    $twitterImageSrc = ($isEdit && !empty($seoMeta->twitter_image))
        ? asset('storage/' . $seoMeta->twitter_image)
        : '';

    $baseUrl = url('/');
    $viewUrl = url($seoMeta->path ?? '/');
?>

<style>
    .seo-form-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 370px;
        gap: 22px;
        align-items: start;
    }

    .seo-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .seo-card-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
    }

    .seo-card-head h3 {
        margin: 0;
        color: #111827;
        font-size: 19px;
        font-weight: 900;
    }

    .seo-card-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .seo-card-body {
        padding: 22px;
    }

    .seo-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .seo-group {
        margin-bottom: 18px;
    }

    .seo-group label {
        display: block;
        font-weight: 900;
        color: #334155;
        margin-bottom: 8px;
    }

    .seo-group input,
    .seo-group select,
    .seo-group textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 13px 14px;
        font-size: 15px;
        outline: none;
        font-family: inherit;
        background: #ffffff;
        color: #111827;
        box-sizing: border-box;
    }

    .seo-group textarea {
        min-height: 110px;
        resize: vertical;
        line-height: 1.7;
    }

    .seo-group input:focus,
    .seo-group select:focus,
    .seo-group textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
    }

    .seo-help {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .seo-schema-box {
        min-height: 240px !important;
        font-family: Consolas, monospace !important;
        font-size: 13px !important;
    }

    .seo-image-preview {
        width: 100%;
        max-height: 190px;
        object-fit: cover;
        object-position: center top;
        border-radius: 16px;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        margin-bottom: 12px;
    }

    .seo-check-card {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 13px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        margin-top: 12px;
        cursor: pointer;
    }

    .seo-check-card input {
        width: auto;
        margin-top: 3px;
    }

    .seo-check-card strong {
        color: #111827;
        font-size: 14px;
    }

    .seo-check-card small {
        color: #64748b;
        line-height: 1.5;
    }

    .seo-error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 14px;
        border-radius: 14px;
        margin-bottom: 18px;
    }

    .seo-error-box ul {
        margin: 8px 0 0;
        padding-left: 18px;
    }

    .seo-preview-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .seo-google-title {
        color: #1a0dab;
        font-size: 17px;
        line-height: 1.4;
        margin-bottom: 5px;
        word-break: break-word;
    }

    .seo-google-url {
        color: #0f9d58;
        font-size: 13px;
        margin-bottom: 6px;
        word-break: break-all;
    }

    .seo-google-desc {
        color: #4b5563;
        font-size: 13px;
        line-height: 1.5;
        word-break: break-word;
    }

    .seo-counter {
        font-size: 12px;
        color: #64748b;
        font-weight: 800;
        margin-top: 6px;
    }

    .seo-counter.good {
        color: #16a34a;
    }

    .seo-counter.warn {
        color: #d97706;
    }

    .seo-counter.bad {
        color: #dc2626;
    }

    .seo-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .seo-quick-grid {
        display: grid;
        gap: 10px;
    }

    @media (max-width: 1050px) {
        .seo-form-wrap,
        .seo-grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php if ($errors->any()): ?>
    <div class="seo-error-box">
        <strong>Please fix errors:</strong>
        <ul>
            <?php foreach ($errors->all() as $error): ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="seo-form-wrap">
    <div>
        <div class="seo-card">
            <div class="seo-card-head">
                <h3>Page SEO Details</h3>
                <p>URL path ke according meta title, description, keywords and canonical URL manage karein.</p>
            </div>

            <div class="seo-card-body">
                <div class="seo-grid-2">
                    <div class="seo-group">
                        <label>Page Name</label>
                        <input
                            type="text"
                            name="page_name"
                            id="pageNameInput"
                            value="<?php echo e(old('page_name', $seoMeta->page_name ?? '')); ?>"
                            placeholder="Home Page / About Page"
                        >
                    </div>

                    <div class="seo-group">
                        <label>Page Path *</label>
                        <input
                            type="text"
                            name="path"
                            id="pathInput"
                            value="<?php echo e(old('path', $seoMeta->path ?? '/')); ?>"
                            required
                            placeholder="/about"
                        >
                        <span class="seo-help">Example: /, /about, /courses, /blogs, /gallery</span>
                    </div>
                </div>

                <div class="seo-group">
                    <label>Meta Title</label>
                    <input
                        type="text"
                        name="meta_title"
                        id="metaTitleInput"
                        value="<?php echo e(old('meta_title', $seoMeta->meta_title ?? '')); ?>"
                        maxlength="255"
                        placeholder="Best Coaching Institute in Noida | Admission Open"
                    >
                    <div class="seo-counter" id="titleCounter">
                        <span id="titleCount">0</span> characters. Recommended 50-60.
                    </div>
                </div>

                <div class="seo-group">
                    <label>Meta Description</label>
                    <textarea
                        name="meta_description"
                        id="metaDescInput"
                        placeholder="Write SEO description within 150-160 characters..."
                    ><?php echo e(old('meta_description', $seoMeta->meta_description ?? '')); ?></textarea>

                    <div class="seo-counter" id="descCounter">
                        <span id="descCount">0</span> characters. Recommended 150-160.
                    </div>
                </div>

                <div class="seo-group">
                    <label>Meta Keywords</label>
                    <textarea
                        name="meta_keywords"
                        placeholder="coaching institute, best coaching classes, admission open"
                    ><?php echo e(old('meta_keywords', $seoMeta->meta_keywords ?? '')); ?></textarea>
                </div>

                <div class="seo-group">
                    <label>Canonical URL</label>
                    <input
                        type="text"
                        name="canonical_url"
                        id="canonicalInput"
                        value="<?php echo e(old('canonical_url', $seoMeta->canonical_url ?? '')); ?>"
                        placeholder="https://example.com/about"
                    >
                    <span class="seo-help">Blank rakhenge to current page URL auto use hoga.</span>
                </div>
            </div>
        </div>

        <div class="seo-card">
            <div class="seo-card-head">
                <h3>Open Graph / Social Share</h3>
                <p>WhatsApp, Facebook, LinkedIn and Twitter preview ke liye content and image.</p>
            </div>

            <div class="seo-card-body">
                <div class="seo-group">
                    <label>OG Title</label>
                    <input
                        type="text"
                        name="og_title"
                        id="ogTitleInput"
                        value="<?php echo e(old('og_title', $seoMeta->og_title ?? '')); ?>"
                        placeholder="Social share title"
                    >
                </div>

                <div class="seo-group">
                    <label>OG Description</label>
                    <textarea
                        name="og_description"
                        id="ogDescInput"
                        placeholder="Social share description"
                    ><?php echo e(old('og_description', $seoMeta->og_description ?? '')); ?></textarea>
                </div>

                <div class="seo-group">
                    <label>OG Image</label>

                    <img
                        src="<?php echo e($ogImageSrc); ?>"
                        class="seo-image-preview"
                        alt="OG Image"
                        style="<?php echo $ogImageSrc ? '' : 'display:none;'; ?>"
                    >

                    <input type="file" name="og_image" accept="image/*">
                    <span class="seo-help">Recommended: 1200x630px. JPG/PNG/WebP, max 4MB.</span>

                    <label class="seo-check-card" style="<?php echo $ogImageSrc ? '' : 'display:none;'; ?>">
                        <input type="checkbox" name="remove_og_image" value="1">
                        <div>
                            <strong>Remove OG Image</strong>
                            <br>
                            <small>Current OG image remove ho jayegi.</small>
                        </div>
                    </label>
                </div>

                <hr style="border:0;border-top:1px solid #e5e7eb;margin:22px 0;">

                <div class="seo-group">
                    <label>Twitter Title</label>
                    <input
                        type="text"
                        name="twitter_title"
                        value="<?php echo e(old('twitter_title', $seoMeta->twitter_title ?? '')); ?>"
                        placeholder="Twitter/X title"
                    >
                </div>

                <div class="seo-group">
                    <label>Twitter Description</label>
                    <textarea
                        name="twitter_description"
                        placeholder="Twitter/X description"
                    ><?php echo e(old('twitter_description', $seoMeta->twitter_description ?? '')); ?></textarea>
                </div>

                <div class="seo-group">
                    <label>Twitter Image</label>

                    <img
                        src="<?php echo e($twitterImageSrc); ?>"
                        class="seo-image-preview"
                        alt="Twitter Image"
                        style="<?php echo $twitterImageSrc ? '' : 'display:none;'; ?>"
                    >

                    <input type="file" name="twitter_image" accept="image/*">
                    <span class="seo-help">Blank rakhenge to OG image fallback use kar sakte hain.</span>

                    <label class="seo-check-card" style="<?php echo $twitterImageSrc ? '' : 'display:none;'; ?>">
                        <input type="checkbox" name="remove_twitter_image" value="1">
                        <div>
                            <strong>Remove Twitter Image</strong>
                            <br>
                            <small>Current Twitter image remove ho jayegi.</small>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="seo-card">
            <div class="seo-card-head">
                <h3>Advanced SEO</h3>
                <p>Robots tag and custom schema JSON-LD add karein.</p>
            </div>

            <div class="seo-card-body">
                <div class="seo-group">
                    <label>Robots</label>
                    <select name="robots">
                        <option value="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" <?php echo $currentRobots === 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' ? 'selected' : ''; ?>>
                            Index, Follow
                        </option>

                        <option value="noindex, nofollow" <?php echo $currentRobots === 'noindex, nofollow' ? 'selected' : ''; ?>>
                            Noindex, Nofollow
                        </option>

                        <option value="noindex, follow" <?php echo $currentRobots === 'noindex, follow' ? 'selected' : ''; ?>>
                            Noindex, Follow
                        </option>

                        <option value="index, nofollow" <?php echo $currentRobots === 'index, nofollow' ? 'selected' : ''; ?>>
                            Index, Nofollow
                        </option>
                    </select>
                </div>

                <div class="seo-group">
                    <label>Schema JSON</label>
                    <textarea
                        name="schema_json"
                        class="seo-schema-box"
                        placeholder="Paste JSON-LD schema here"
                    ><?php echo e(old('schema_json', $seoMeta->schema_json ?? '')); ?></textarea>
                    <span class="seo-help">Valid JSON-LD schema paste karein. Blank bhi chhod sakte hain.</span>
                </div>
            </div>
        </div>

        <div class="seo-actions">
            <button type="submit" class="btn btn-primary"><?php echo e($buttonText ?? 'Save SEO Meta'); ?></button>
            <a href="<?php echo e(route('admin.seo.index')); ?>" class="btn btn-light">Cancel</a>
        </div>
    </div>

    <aside>
        <div class="seo-card">
            <div class="seo-card-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;">
                <h3 style="color:#fff;">Publish Settings</h3>
                <p style="color:rgba(255,255,255,.9);">Status, order and live SEO preview.</p>
            </div>

            <div class="seo-card-body">
                <div class="seo-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active" <?php echo $currentStatus === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $currentStatus === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>

                <div class="seo-group">
                    <label>Sort Order</label>
                    <input
                        type="number"
                        name="sort_order"
                        value="<?php echo e(old('sort_order', $seoMeta->sort_order ?? 0)); ?>"
                        min="0"
                    >
                </div>

                <div class="seo-preview-box">
                    <strong style="display:block;color:#111827;margin-bottom:10px;">Google Preview</strong>

                    <div class="seo-google-title" id="googleTitle">SEO title will show here</div>
                    <div class="seo-google-url" id="googleUrl"><?php echo e($baseUrl); ?></div>
                    <div class="seo-google-desc" id="googleDesc">SEO description will show here.</div>
                </div>

                <div class="seo-preview-box">
                    <strong style="display:block;color:#111827;margin-bottom:10px;">Social Preview Tips</strong>

                    <ul style="margin:0;padding-left:18px;color:#64748b;line-height:1.7;font-size:13px;">
                        <li>OG image 1200x630px upload karein.</li>
                        <li>Meta title 50-60 characters rakhein.</li>
                        <li>Description 150-160 characters rakhein.</li>
                        <li>Har page ka path unique hona chahiye.</li>
                    </ul>
                </div>

                <div class="seo-preview-box">
                    <strong style="display:block;color:#111827;margin-bottom:10px;">Quick Links</strong>

                    <div class="seo-quick-grid">
                        <a href="<?php echo e(url('/sitemap.xml')); ?>" target="_blank" class="btn btn-light">View Sitemap</a>
                        <a href="<?php echo e(url('/robots.txt')); ?>" target="_blank" class="btn btn-light">View Robots.txt</a>
                        <a href="<?php echo e($viewUrl); ?>" target="_blank" class="btn btn-dark">View Page</a>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const baseUrl = <?php echo json_encode($baseUrl, JSON_UNESCAPED_SLASHES); ?>;

        const metaTitleInput = document.getElementById('metaTitleInput');
        const metaDescInput = document.getElementById('metaDescInput');
        const pathInput = document.getElementById('pathInput');

        const titleCount = document.getElementById('titleCount');
        const descCount = document.getElementById('descCount');
        const titleCounter = document.getElementById('titleCounter');
        const descCounter = document.getElementById('descCounter');

        const googleTitle = document.getElementById('googleTitle');
        const googleUrl = document.getElementById('googleUrl');
        const googleDesc = document.getElementById('googleDesc');

        function normalizePath(path) {
            path = (path || '/').trim();

            if (path === '') {
                return '/';
            }

            if (path !== '/' && !path.startsWith('/')) {
                path = '/' + path;
            }

            return path;
        }

        function updateCounterClass(element, count, min, max) {
            if (!element) {
                return;
            }

            element.classList.remove('good', 'warn', 'bad');

            if (count >= min && count <= max) {
                element.classList.add('good');
                return;
            }

            if (count === 0) {
                return;
            }

            if (count < min || count > max) {
                element.classList.add('warn');
            }

            if (count > max + 30) {
                element.classList.add('bad');
            }
        }

        function updateSeoPreview() {
            if (!metaTitleInput || !metaDescInput || !pathInput) {
                return;
            }

            const title = metaTitleInput.value || 'SEO title will show here';
            const desc = metaDescInput.value || 'SEO description will show here.';
            const path = normalizePath(pathInput.value);

            const titleLength = metaTitleInput.value.length;
            const descLength = metaDescInput.value.length;

            if (titleCount) {
                titleCount.textContent = titleLength;
            }

            if (descCount) {
                descCount.textContent = descLength;
            }

            updateCounterClass(titleCounter, titleLength, 50, 60);
            updateCounterClass(descCounter, descLength, 150, 160);

            if (googleTitle) {
                googleTitle.textContent = title;
            }

            if (googleUrl) {
                googleUrl.textContent = baseUrl + (path === '/' ? '' : path);
            }

            if (googleDesc) {
                googleDesc.textContent = desc;
            }
        }

        [metaTitleInput, metaDescInput, pathInput].forEach(function (input) {
            if (input) {
                input.addEventListener('input', updateSeoPreview);
            }
        });

        updateSeoPreview();
    });
</script>