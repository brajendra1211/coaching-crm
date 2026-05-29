@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Coaching Admin Dashboard')

@push('styles')
<style>
    .dashboard-wrap {
        display: grid;
        gap: 22px;
    }

    .welcome-card {
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.18), transparent 34%),
            linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        border-radius: 24px;
        padding: 26px;
        box-shadow: 0 18px 45px rgba(37, 99, 235, .22);
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 20px;
        align-items: center;
    }

    .welcome-card h2 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 28px;
        line-height: 1.2;
        font-weight: 900;
    }

    .welcome-card p {
        margin: 0;
        color: rgba(255,255,255,.9);
        line-height: 1.6;
        font-size: 15px;
    }

    .welcome-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 16px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: "";
        position: absolute;
        right: -32px;
        top: -32px;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(37, 99, 235, .08);
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        font-size: 21px;
        margin-bottom: 14px;
        position: relative;
        z-index: 1;
    }

    .stat-card h3 {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .4px;
        position: relative;
        z-index: 1;
    }

    .stat-card strong {
        display: block;
        margin-top: 8px;
        color: #0f172a;
        font-size: 30px;
        line-height: 1;
        font-weight: 900;
        position: relative;
        z-index: 1;
    }

    .dashboard-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 22px;
        align-items: start;
    }

    .panel-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .panel-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        background: #fff;
    }

    .panel-head h3 {
        margin: 0;
        color: #111827;
        font-size: 19px;
        font-weight: 900;
    }

    .panel-head p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .panel-body {
        padding: 20px 22px;
    }

    .quick-actions {
        display: grid;
        gap: 12px;
    }

    .quick-action {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 15px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        transition: .2s ease;
    }

    .quick-action:hover {
        transform: translateY(-2px);
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .quick-action span {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .quick-action strong {
        display: block;
        color: #111827;
        font-size: 15px;
        margin-bottom: 3px;
    }

    .quick-action small {
        display: block;
        color: #64748b;
        font-size: 12px;
        line-height: 1.4;
    }

    .lead-list {
        display: grid;
        gap: 12px;
    }

    .lead-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 15px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .lead-item strong {
        display: block;
        color: #111827;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .lead-item small {
        display: block;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .lead-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        white-space: nowrap;
    }

    .setup-list {
        display: grid;
        gap: 10px;
    }

    .setup-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 13px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 15px;
        background: #f8fafc;
    }

    .setup-item strong {
        color: #111827;
        font-size: 14px;
    }

    .setup-ok {
        color: #16a34a;
        font-size: 12px;
        font-weight: 900;
    }

    .setup-pending {
        color: #f59e0b;
        font-size: 12px;
        font-weight: 900;
    }

    @media(max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dashboard-main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media(max-width: 768px) {
        .welcome-card {
            grid-template-columns: 1fr;
        }

        .welcome-actions {
            justify-content: flex-start;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .lead-item {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="dashboard-wrap">
    <div class="welcome-card">
        <div>
            <h2>
                Welcome{{ $setting?->institute_name ? ', ' . $setting->institute_name : '' }}
            </h2>

            <p>
                Manage courses, enquiries, website pages, SEO locations and institute settings from one coaching admin panel.
            </p>
        </div>

        <div class="welcome-actions">
            <a href="{{ url('/') }}" target="_blank" class="btn btn-light">View Website</a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-dark">Website Settings</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <h3>Total Courses</h3>
            <strong>{{ $stats['courses'] ?? 0 }}</strong>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📩</div>
            <h3>Total Leads</h3>
            <strong>{{ $stats['leads'] ?? 0 }}</strong>
        </div>

        <div class="stat-card">
            <div class="stat-icon">🔥</div>
            <h3>Today Leads</h3>
            <strong>{{ $stats['today_leads'] ?? 0 }}</strong>
        </div>

        <div class="stat-card">
            <div class="stat-icon">Q</div>
            <h3>Contact Enquiries</h3>
            <strong>{{ $stats['contact_enquiries'] ?? 0 }}</strong>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📄</div>
            <h3>Website Pages</h3>
            <strong>{{ $stats['pages'] ?? 0 }}</strong>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📍</div>
            <h3>SEO Locations</h3>
            <strong>{{ $stats['locations'] ?? 0 }}</strong>
        </div>
    </div>

    <div class="dashboard-main-grid">
        <div class="panel-card">
            <div class="panel-head">
                <div>
                    <h3>Recent Enquiries</h3>
                    <p>Latest leads submitted from website pages and course pages.</p>
                </div>

                @if(Route::has('admin.leads.index'))
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-primary">View All</a>
                @endif
            </div>

            <div class="panel-body">
                <div class="lead-list">
                    @forelse($recentLeads as $lead)
                        <div class="lead-item">
                            <div>
                                <strong>{{ $lead->name }}</strong>
                                <small>
                                    {{ $lead->phone }}
                                    @if($lead->email)
                                        • {{ $lead->email }}
                                    @endif
                                </small>
                                <small>
                                    Course: {{ $lead->course->title ?? 'General Enquiry' }}
                                </small>
                                <small>
                                    {{ $lead->created_at?->format('d M Y, h:i A') }}
                                </small>
                            </div>

                            <div>
                                <span class="lead-status">
                                    {{ ucwords(str_replace('_', ' ', $lead->status ?? 'new')) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:28px;color:#64748b;">
                            <h3 style="margin:0 0 8px;color:#111827;">No Leads Yet</h3>
                            <p style="margin:0;">Website enquiries will appear here once students submit forms.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <div class="panel-card">
                <div class="panel-head">
                    <div>
                        <h3>Quick Actions</h3>
                        <p>Common tasks for coaching admin.</p>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="quick-actions">
                        @if(Route::has('admin.courses.create'))
                            <a href="{{ route('admin.courses.create') }}" class="quick-action">
                                <span>📚</span>
                                <div>
                                    <strong>Add Course</strong>
                                    <small>Create course page with fee, SEO and content.</small>
                                </div>
                            </a>
                        @endif

                        @if(Route::has('admin.pages.create'))
                            <a href="{{ route('admin.pages.create') }}" class="quick-action">
                                <span>📄</span>
                                <div>
                                    <strong>Add Website Page</strong>
                                    <small>Create about, contact, admission or custom page.</small>
                                </div>
                            </a>
                        @endif

                        @if(Route::has('admin.locations.create'))
                            <a href="{{ route('admin.locations.create') }}" class="quick-action">
                                <span>📍</span>
                                <div>
                                    <strong>Add SEO Location</strong>
                                    <small>Create local SEO page for city or area keyword.</small>
                                </div>
                            </a>
                        @endif

                        @if(Route::has('admin.settings.index'))
                            <a href="{{ route('admin.settings.index') }}" class="quick-action">
                                <span>⚙️</span>
                                <div>
                                    <strong>Website Settings</strong>
                                    <small>Update logo, phone, WhatsApp and footer details.</small>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="panel-card" style="margin-top:22px;">
                <div class="panel-head">
                    <div>
                        <h3>Website Setup Status</h3>
                        <p>Complete these items before going live.</p>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="setup-list">
                        <div class="setup-item">
                            <strong>Institute Name</strong>
                            <span class="{{ $setting?->institute_name ? 'setup-ok' : 'setup-pending' }}">
                                {{ $setting?->institute_name ? 'Done' : 'Pending' }}
                            </span>
                        </div>

                        <div class="setup-item">
                            <strong>Logo Uploaded</strong>
                            <span class="{{ $setting?->logo ? 'setup-ok' : 'setup-pending' }}">
                                {{ $setting?->logo ? 'Done' : 'Pending' }}
                            </span>
                        </div>

                        <div class="setup-item">
                            <strong>Phone / WhatsApp</strong>
                            <span class="{{ ($setting?->phone && $setting?->whatsapp) ? 'setup-ok' : 'setup-pending' }}">
                                {{ ($setting?->phone && $setting?->whatsapp) ? 'Done' : 'Pending' }}
                            </span>
                        </div>

                        <div class="setup-item">
                            <strong>Courses Added</strong>
                            <span class="{{ ($stats['courses'] ?? 0) > 0 ? 'setup-ok' : 'setup-pending' }}">
                                {{ ($stats['courses'] ?? 0) > 0 ? 'Done' : 'Pending' }}
                            </span>
                        </div>

                        <div class="setup-item">
                            <strong>SEO Pages Added</strong>
                            <span class="{{ ($stats['locations'] ?? 0) > 0 ? 'setup-ok' : 'setup-pending' }}">
                                {{ ($stats['locations'] ?? 0) > 0 ? 'Done' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
