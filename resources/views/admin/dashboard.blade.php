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

    .setup-flow-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .setup-flow-head {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
        gap: 18px;
        align-items: center;
        padding: 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
    }

    .setup-flow-head h3 {
        margin: 0;
        color: #111827;
        font-size: 22px;
        font-weight: 900;
    }

    .setup-flow-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.5;
    }

    .setup-progress-wrap {
        min-width: 230px;
    }

    .setup-close-btn {
        width: 38px;
        height: 38px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        color: #64748b;
        cursor: pointer;
        font-size: 16px;
        font-weight: 900;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: .2s ease;
    }

    .setup-close-btn:hover {
        background: #f8fafc;
        color: #111827;
        transform: translateY(-1px);
    }

    .setup-progress-meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        color: #334155;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 9px;
    }

    .setup-progress-bar {
        width: 100%;
        height: 10px;
        border-radius: 999px;
        background: #e5e7eb;
        overflow: hidden;
    }

    .setup-progress-bar span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(135deg, #16a34a, #06b6d4);
    }

    .setup-alert {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        align-items: center;
        margin: 18px 22px 0;
        padding: 14px 16px;
        border-radius: 16px;
        border: 1px solid #fed7aa;
        background: #fff7ed;
        color: #9a3412;
    }

    .setup-alert strong {
        display: block;
        color: #7c2d12;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .setup-alert span {
        display: block;
        font-size: 13px;
        line-height: 1.5;
    }

    .setup-alert.done {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .setup-alert.done strong {
        color: #14532d;
    }

    .setup-flow-body {
        padding: 22px;
    }

    .setup-step-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .setup-step {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 15px;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .setup-step.pending {
        border-color: #fed7aa;
        background: #fffaf4;
    }

    .setup-step.done {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .setup-step.blocked {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .setup-step-no {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 900;
        background: #e0f2fe;
        color: #0369a1;
        flex-shrink: 0;
    }

    .setup-step.done .setup-step-no {
        background: #dcfce7;
        color: #166534;
    }

    .setup-step.pending .setup-step-no {
        background: #ffedd5;
        color: #c2410c;
    }

    .setup-step-title {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 5px;
    }

    .setup-step-title strong {
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .setup-step-title em {
        display: inline-flex;
        align-items: center;
        min-height: 23px;
        padding: 4px 8px;
        border-radius: 999px;
        font-style: normal;
        font-size: 11px;
        font-weight: 900;
        background: #e5e7eb;
        color: #475569;
    }

    .setup-step.done .setup-step-title em {
        background: #dcfce7;
        color: #166534;
    }

    .setup-step.pending .setup-step-title em {
        background: #ffedd5;
        color: #c2410c;
    }

    .setup-step p {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .setup-step small {
        display: block;
        margin-top: 5px;
        color: #b45309;
        font-size: 12px;
        font-weight: 800;
    }

    .setup-step-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .setup-step .btn {
        min-height: 35px;
        padding: 8px 11px;
        font-size: 12px;
        border-radius: 11px;
    }

    .setup-step .btn-muted {
        background: #e5e7eb;
        color: #64748b;
        cursor: not-allowed;
        box-shadow: none;
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

        .setup-alert,
        .setup-step {
            grid-template-columns: 1fr;
        }

        .setup-flow-head {
            grid-template-columns: 1fr auto;
        }

        .setup-progress-wrap {
            grid-column: 1 / -1;
            min-width: 0;
        }

        .setup-step-grid {
            grid-template-columns: 1fr;
        }

        .setup-step-actions {
            justify-content: flex-start;
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

    <div class="setup-flow-card" id="setupFlowCard">
        <div class="setup-flow-head">
            <div>
                <h3>Admin Setup Flow</h3>
                <p>
                    This checklist shows what is complete, what needs attention, and the next action for first-time setup.
                </p>
            </div>

            <div class="setup-progress-wrap">
                <div class="setup-progress-meta">
                    <span>Required Progress</span>
                    <span>{{ $setupFlow['progress'] ?? 0 }}%</span>
                </div>

                <div class="setup-progress-bar">
                    <span style="width: {{ $setupFlow['progress'] ?? 0 }}%;"></span>
                </div>

                <div style="margin-top:8px;color:#64748b;font-size:12px;font-weight:800;">
                    {{ $setupFlow['completed_required'] ?? 0 }}/{{ $setupFlow['total_required'] ?? 0 }} required steps complete
                </div>
            </div>

            <button type="button" class="setup-close-btn" id="closeSetupFlow" aria-label="Close setup flow" title="Close">
                X
            </button>
        </div>

        @php
            $nextStep = $setupFlow['next_step'] ?? null;
            $hasRequiredPending = $setupFlow['has_required_pending'] ?? false;
        @endphp

        @if($nextStep)
            <div class="setup-alert {{ $hasRequiredPending ? '' : 'done' }}">
                <div>
                    <strong>{{ $hasRequiredPending ? 'Mandatory setup pending' : 'Optional next step' }}</strong>
                    <span>
                        Next: {{ $nextStep['title'] }}
                        @if(!empty($nextStep['blocked']) && !empty($nextStep['blocked_text']))
                            - {{ $nextStep['blocked_text'] }}
                        @endif
                    </span>
                </div>

                @if(empty($nextStep['blocked']))
                    <a href="{{ $nextStep['action_url'] }}" class="btn {{ $hasRequiredPending ? 'btn-primary' : 'btn-success' }}">
                        {{ $nextStep['action_label'] }}
                    </a>
                @else
                    <span class="btn btn-light">Waiting</span>
                @endif
            </div>
        @else
            <div class="setup-alert done">
                <div>
                    <strong>Setup complete</strong>
                    <span>The required panel flow is ready. Admins can now run daily operations smoothly.</span>
                </div>

                <a href="{{ route('admin.fees.index') }}" class="btn btn-success">Open Fees</a>
            </div>
        @endif

        <div class="setup-flow-body">
            <div class="setup-step-grid">
                @foreach(($setupFlow['steps'] ?? []) as $step)
                    @php
                        $stepClass = $step['done'] ? 'done' : (!empty($step['blocked']) ? 'blocked' : 'pending');
                        $statusText = $step['done'] ? 'Done' : (!empty($step['blocked']) ? 'Waiting' : 'Pending');
                    @endphp

                    <div class="setup-step {{ $stepClass }}">
                        <span class="setup-step-no">{{ str_pad($step['level'], 2, '0', STR_PAD_LEFT) }}</span>

                        <div>
                            <div class="setup-step-title">
                                <strong>{{ $step['title'] }}</strong>
                                <em>{{ $statusText }}</em>
                            </div>

                            <p>{{ $step['description'] }}</p>

                            @if(!empty($step['blocked']) && !empty($step['blocked_text']))
                                <small>{{ $step['blocked_text'] }}</small>
                            @endif
                        </div>

                        <div class="setup-step-actions">
                            @if($step['done'])
                                <a href="{{ $step['action_url'] }}" class="btn btn-light">Review</a>
                            @elseif(!empty($step['blocked']))
                                <span class="btn btn-muted">{{ $step['count'] }}</span>
                            @else
                                <a href="{{ $step['action_url'] }}" class="btn btn-primary">{{ $step['action_label'] }}</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
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
                        <h3>Flow Snapshot</h3>
                        <p>Key records needed for daily admin work.</p>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="setup-list">
                        <div class="setup-item">
                            <strong>Required Setup</strong>
                            <span class="{{ ($setupFlow['has_required_pending'] ?? true) ? 'setup-pending' : 'setup-ok' }}">
                                {{ $setupFlow['completed_required'] ?? 0 }}/{{ $setupFlow['total_required'] ?? 0 }}
                            </span>
                        </div>

                        <div class="setup-item">
                            <strong>Courses / Teachers</strong>
                            <span class="{{ (($stats['courses'] ?? 0) > 0 && ($stats['teachers'] ?? 0) > 0) ? 'setup-ok' : 'setup-pending' }}">
                                {{ $stats['courses'] ?? 0 }} / {{ $stats['teachers'] ?? 0 }}
                            </span>
                        </div>

                        <div class="setup-item">
                            <strong>Students / Batches</strong>
                            <span class="{{ (($stats['students'] ?? 0) > 0 && ($stats['batches'] ?? 0) > 0) ? 'setup-ok' : 'setup-pending' }}">
                                {{ $stats['students'] ?? 0 }} / {{ $stats['batches'] ?? 0 }}
                            </span>
                        </div>

                        <div class="setup-item">
                            <strong>Fee Plans / Assignments</strong>
                            <span class="{{ (($stats['batch_fee_plans'] ?? 0) > 0 && ($stats['fee_assignments'] ?? 0) > 0) ? 'setup-ok' : 'setup-pending' }}">
                                {{ $stats['batch_fee_plans'] ?? 0 }} / {{ $stats['fee_assignments'] ?? 0 }}
                            </span>
                        </div>

                        <div class="setup-item">
                            <strong>Fee Payments</strong>
                            <span class="{{ ($stats['fee_payments'] ?? 0) > 0 ? 'setup-ok' : 'setup-pending' }}">
                                {{ $stats['fee_payments'] ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const setupFlowCard = document.getElementById('setupFlowCard');
        const closeSetupFlow = document.getElementById('closeSetupFlow');

        closeSetupFlow?.addEventListener('click', function () {
            if (setupFlowCard) {
                setupFlowCard.style.display = 'none';
            }
        });
    });
</script>
@endpush
