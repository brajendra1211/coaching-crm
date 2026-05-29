@extends('admin.layouts.app')

@section('title', 'Website Settings')
@section('page_title', 'Website Settings')

@push('styles')
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 22px;
        align-items: flex-start;
    }

    .settings-section {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .settings-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
    }

    .settings-head h3 {
        margin: 0;
        color: #111827;
        font-size: 19px;
        font-weight: 900;
    }

    .settings-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .settings-body {
        padding: 22px;
    }

    .preview-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
        overflow: hidden;
        position: sticky;
        top: 95px;
    }

    .preview-head {
        padding: 18px 20px;
        background: linear-gradient(135deg, {{ old('primary_color', $setting->primary_color ?: '#2563eb') }}, {{ old('secondary_color', $setting->secondary_color ?: '#7c3aed') }});
        color: #fff;
    }

    .preview-head h3 {
        margin: 0 0 5px;
        color: #fff;
    }

    .preview-head p {
        margin: 0;
        opacity: .9;
        font-size: 13px;
        line-height: 1.5;
    }

    .preview-body {
        padding: 20px;
    }

    .logo-preview {
        height: 150px;
        border-radius: 18px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 16px;
        text-align: center;
    }

    .logo-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 15px;
    }

    .preview-info {
        display: grid;
        gap: 10px;
    }

    .preview-info div {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 12px;
    }

    .preview-info small {
        display: block;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 4px;
    }

    .preview-info strong {
        color: #111827;
        font-size: 14px;
    }

    .color-field {
        display: grid;
        grid-template-columns: 58px minmax(0, 1fr);
        gap: 10px;
        align-items: center;
    }

    .color-field input[type="color"] {
        height: 48px;
        padding: 4px;
    }

    .form-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    @media(max-width: 1050px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }

        .form-grid-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .preview-card {
            position: static;
        }
    }

    @media(max-width: 640px) {
        .form-grid-4 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

@if(session('success'))
    <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
        <strong>Please fix the following:</strong>
        <ul style="margin:8px 0 0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="settings-grid">
        <div>
            <div class="settings-section">
                <div class="settings-head">
                    <h3>Institute Information</h3>
                    <p>Manage the basic identity of the coaching website.</p>
                </div>

                <div class="settings-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Institute Name *</label>
                            <input type="text" name="institute_name" value="{{ old('institute_name', $setting->institute_name) }}" placeholder="Edu Institute" required>
                        </div>

                        <div class="form-group">
                            <label>Tagline</label>
                            <input type="text" name="tagline" value="{{ old('tagline', $setting->tagline) }}" placeholder="Coaching | Exams | Results">
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Logo</label>
                            <input type="file" name="logo" accept="image/*">
                            <small style="color:#64748b;">Recommended: transparent PNG/WebP, 300 x 120px.</small>
                        </div>

                        <div class="form-group">
                            <label>Favicon</label>
                            <input type="file" name="favicon" accept="image/*,.ico">
                            <small style="color:#64748b;">Recommended: 64 x 64px.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <div class="settings-head">
                    <h3>Admin Account</h3>
                    <p>Update your login name, email address and password.</p>
                </div>

                <div class="settings-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Admin Name *</label>
                            <input type="text" name="admin_name" value="{{ old('admin_name', $adminUser?->name) }}" placeholder="Admin Name" autocomplete="name" required>
                        </div>

                        <div class="form-group">
                            <label>Admin Login Email *</label>
                            <input type="email" name="admin_email" value="{{ old('admin_email', $adminUser?->email) }}" placeholder="admin@example.com" autocomplete="email" required>
                            <small style="color:#64748b;">This email will be used for admin login.</small>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="admin_current_password" value="" placeholder="Required only for password change" autocomplete="current-password">
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="admin_password" value="" placeholder="Minimum 8 characters" autocomplete="new-password">
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="admin_password_confirmation" value="" placeholder="Repeat new password" autocomplete="new-password">
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <div class="settings-head">
                    <h3>Website Theme</h3>
                    <p>Control public website colors and font from the CRM.</p>
                </div>

                <div class="settings-body">
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label>Primary Color</label>
                            <div class="color-field">
                                <input type="color" name="primary_color" value="{{ old('primary_color', $setting->primary_color ?: '#2563eb') }}">
                                <input type="text" value="{{ old('primary_color', $setting->primary_color ?: '#2563eb') }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Secondary Color</label>
                            <div class="color-field">
                                <input type="color" name="secondary_color" value="{{ old('secondary_color', $setting->secondary_color ?: '#7c3aed') }}">
                                <input type="text" value="{{ old('secondary_color', $setting->secondary_color ?: '#7c3aed') }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Accent Color</label>
                            <div class="color-field">
                                <input type="color" name="accent_color" value="{{ old('accent_color', $setting->accent_color ?: '#16a34a') }}">
                                <input type="text" value="{{ old('accent_color', $setting->accent_color ?: '#16a34a') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Website Font</label>
                        <select name="font_family">
                            @foreach(['Arial', 'Inter', 'Poppins', 'Roboto', 'Nunito', 'Lato', 'Merriweather'] as $font)
                                <option value="{{ $font }}" @selected(old('font_family', $setting->font_family ?: 'Arial') === $font)>
                                    {{ $font }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <div class="settings-head">
                    <h3>Homepage Hero & SEO</h3>
                    <p>Control homepage first section, stats, image and SEO from one place.</p>
                </div>

                <div class="settings-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Hero Title</label>
                            <input type="text" name="home_hero_title" value="{{ old('home_hero_title', $setting->home_hero_title) }}" placeholder="Build Strong Concepts for NEET, IIT JEE & Board Exams">
                        </div>

                        <div class="form-group">
                            <label>Hero Highlight</label>
                            <input type="text" name="home_hero_highlight" value="{{ old('home_hero_highlight', $setting->home_hero_highlight) }}" placeholder="Result-Focused Coaching">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hero Subtitle</label>
                        <textarea name="home_hero_subtitle" placeholder="Short homepage intro">{{ old('home_hero_subtitle', $setting->home_hero_subtitle) }}</textarea>
                    </div>

                    <div class="form-grid-4">
                        <div class="form-group">
                            <label>Students Count</label>
                            <input type="text" name="students_count" value="{{ old('students_count', $setting->students_count) }}" placeholder="500+">
                        </div>

                        <div class="form-group">
                            <label>Courses Count</label>
                            <input type="text" name="courses_count" value="{{ old('courses_count', $setting->courses_count) }}" placeholder="20+">
                        </div>

                        <div class="form-group">
                            <label>Tests Count</label>
                            <input type="text" name="tests_count" value="{{ old('tests_count', $setting->tests_count) }}" placeholder="50+">
                        </div>

                        <div class="form-group">
                            <label>Rating</label>
                            <input type="text" name="rating" value="{{ old('rating', $setting->rating) }}" placeholder="4.8/5">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hero Image</label>
                        <input type="file" name="home_hero_image" accept="image/*">
                        <small style="color:#64748b;">Recommended: classroom/student photo, 900 x 650px WebP/JPG.</small>

                        @if($setting->home_hero_image)
                            <div style="margin-top:12px;">
                                <img src="{{ asset('storage/' . $setting->home_hero_image) }}" alt="Homepage hero" style="width:180px;height:110px;object-fit:cover;border-radius:14px;border:1px solid #e5e7eb;">
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Home SEO Title</label>
                        <input type="text" name="home_seo_title" value="{{ old('home_seo_title', $setting->home_seo_title) }}" placeholder="Best Coaching Institute for NEET, IIT JEE & Board Exams">
                    </div>

                    <div class="form-group">
                        <label>Home SEO Description</label>
                        <textarea name="home_seo_description" placeholder="Homepage meta description">{{ old('home_seo_description', $setting->home_seo_description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Home SEO Keywords</label>
                        <textarea name="home_seo_keywords" placeholder="coaching institute, NEET coaching, IIT JEE coaching">{{ old('home_seo_keywords', $setting->home_seo_keywords) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <div class="settings-head">
                    <h3>Contact Details</h3>
                    <p>These details will appear in header, footer and enquiry sections.</p>
                </div>

                <div class="settings-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" placeholder="9999999999">
                        </div>

                        <div class="form-group">
                            <label>WhatsApp Number</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $setting->whatsapp) }}" placeholder="919999999999">
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $setting->email) }}" placeholder="info@example.com">
                        </div>

                        <div class="form-group">
                            <label>Enquiry Email</label>
                            <input type="email" name="enquiry_email" value="{{ old('enquiry_email', $setting->enquiry_email) }}" placeholder="admissions@example.com">
                            <small style="color:#64748b;">Website contact enquiries will be sent to this email.</small>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Mail From Name</label>
                            <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $setting->mail_from_name ?: $setting->institute_name) }}" placeholder="Institute Name">
                        </div>

                        <div class="form-group">
                            <label>Mail From Email</label>
                            <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $setting->mail_from_address ?: $setting->email) }}" placeholder="info@example.com">
                        </div>

                        <div class="form-group">
                            <label>Gmail Address</label>
                            <input type="email" name="gmail_address" value="{{ old('gmail_address', $setting->gmail_address) }}" placeholder="yourgmail@gmail.com">
                            <small style="color:#64748b;">Use the Gmail account that will send enquiry emails.</small>
                        </div>

                        <div class="form-group">
                            <label>Gmail App Password</label>
                            <input type="password" name="gmail_app_password" value="" placeholder="{{ $setting->gmail_app_password ? 'Saved - enter only to change' : '16 character app password' }}">
                            <small style="color:#64748b;">Use Google App Password, not normal Gmail password.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" placeholder="Full institute address">{{ old('address', $setting->address) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Google Map URL</label>
                        <textarea name="google_map_url" placeholder="Google Map embed/share link">{{ old('google_map_url', $setting->google_map_url) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <div class="settings-head">
                    <h3>Footer & Social Links</h3>
                    <p>Manage footer description and social media links.</p>
                </div>

                <div class="settings-body">
                    <div class="form-group">
                        <label>Footer Description</label>
                        <textarea name="footer_description" placeholder="Short professional institute description">{{ old('footer_description', $setting->footer_description) }}</textarea>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Facebook URL</label>
                            <input type="text" name="facebook_url" value="{{ old('facebook_url', $setting->facebook_url) }}">
                        </div>

                        <div class="form-group">
                            <label>Instagram URL</label>
                            <input type="text" name="instagram_url" value="{{ old('instagram_url', $setting->instagram_url) }}">
                        </div>

                        <div class="form-group">
                            <label>YouTube URL</label>
                            <input type="text" name="youtube_url" value="{{ old('youtube_url', $setting->youtube_url) }}">
                        </div>

                        <div class="form-group">
                            <label>LinkedIn URL</label>
                            <input type="text" name="linkedin_url" value="{{ old('linkedin_url', $setting->linkedin_url) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <div class="settings-head">
                    <h3>Default SEO Settings</h3>
                    <p>Used when a page does not have custom SEO information.</p>
                </div>

                <div class="settings-body">
                    <div class="form-group">
                        <label>Default SEO Title</label>
                        <input type="text" name="default_seo_title" value="{{ old('default_seo_title', $setting->default_seo_title) }}" placeholder="Best Coaching Institute">
                    </div>

                    <div class="form-group">
                        <label>Default SEO Description</label>
                        <textarea name="default_seo_description" placeholder="Default website meta description">{{ old('default_seo_description', $setting->default_seo_description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Default SEO Keywords</label>
                        <textarea name="default_seo_keywords" placeholder="coaching institute, NEET coaching, IIT JEE coaching">{{ old('default_seo_keywords', $setting->default_seo_keywords) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <aside>
            <div class="preview-card">
                <div class="preview-head">
                    <h3>Website Preview</h3>
                    <p>Current public website identity.</p>
                </div>

                <div class="preview-body">
                    <div class="logo-preview">
                        @if($setting->logo)
                            <img src="{{ asset('storage/' . $setting->logo) }}" alt="{{ $setting->institute_name }}">
                        @else
                            Logo Preview
                        @endif
                    </div>

                    <div class="preview-info">
                        <div>
                            <small>Admin Login</small>
                            <strong>{{ $adminUser?->email ?: 'Not available' }}</strong>
                        </div>

                        <div>
                            <small>Institute</small>
                            <strong>{{ $setting->institute_name }}</strong>
                        </div>

                        <div>
                            <small>Phone</small>
                            <strong>{{ $setting->phone ?: 'Not added' }}</strong>
                        </div>

                        <div>
                            <small>WhatsApp</small>
                            <strong>{{ $setting->whatsapp ?: 'Not added' }}</strong>
                        </div>

                        <div>
                            <small>Email</small>
                            <strong>{{ $setting->email ?: 'Not added' }}</strong>
                        </div>

                        <div>
                            <small>Enquiry Email</small>
                            <strong>{{ $setting->enquiry_email ?: $setting->email ?: 'Not added' }}</strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:18px;">
                        Save Settings
                    </button>
                </div>
            </div>
        </aside>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.color-field input[type="color"]').forEach(function (picker) {
            const textInput = picker.parentElement.querySelector('input[type="text"]');

            if (!textInput) {
                return;
            }

            picker.addEventListener('input', function () {
                textInput.value = picker.value;
            });
        });
    });
</script>
@endpush
