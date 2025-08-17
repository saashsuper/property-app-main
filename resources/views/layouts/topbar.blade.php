<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logos/proman-logo-sm.svg') }}" alt="PROMAN" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logos/proman-logo-dark.svg') }}" alt="PROMAN" height="22">
                        </span>
                    </a>

                    <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logos/proman-logo-sm.svg') }}" alt="PROMAN" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logos/proman-logo-light.svg') }}" alt="PROMAN" height="22">
                        </span>
                    </a>
                </div>

                <button type="button"
                    class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <form class="app-search d-none d-md-inline-flex">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search..." autocomplete="off"
                            id="search-options" value="">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none"
                            id="search-close-options"></span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                        <div data-simplebar style="max-height: 320px;">
                            <!-- item-->
                            <div class="dropdown-header">
                                <h6 class="text-overflow text-muted mb-0 text-uppercase">Recent Searches</h6>
                            </div>

                            <div class="dropdown-item bg-transparent text-wrap">
                                <a href="index" class="btn btn-subtle-secondary btn-sm btn-rounded">how to setup <i
                                        class="mdi mdi-magnify ms-1"></i></a>
                                <a href="index" class="btn btn-subtle-secondary btn-sm btn-rounded">buttons <i
                                        class="mdi mdi-magnify ms-1"></i></a>
                            </div>
                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-1 text-uppercase">Pages</h6>
                            </div>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ph-chart-line align-middle fs-18 text-muted me-2"></i>
                                <span>Analytics Dashboard</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ph-lifebuoy align-middle fs-18 text-muted me-2"></i>
                                <span>Help Center</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ph-user-gear align-middle fs-18 text-muted me-2"></i>
                                <span>My account settings</span>
                            </a>

                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-2 text-uppercase">Members</h6>
                            </div>

                            <div class="notification-list">
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="{{ URL::asset('build/images/users/avatar-2.jpg') }}"
                                            class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-1">
                                            <h6 class="m-0">Property Manager</h6>
                                            <span class="fs-2xs mb-0 text-muted">Manager</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="{{ URL::asset('build/images/users/avatar-3.jpg') }}"
                                            class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-1">
                                            <h6 class="m-0">Contract Manager</h6>
                                            <span class="fs-2xs mb-0 text-muted">Contract Management</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="{{ URL::asset('build/images/users/avatar-5.jpg') }}"
                                            class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-1">
                                            <h6 class="m-0">System Admin</h6>
                                            <span class="fs-2xs mb-0 text-muted">Administrator</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="text-center pt-3 pb-1">
                            <a href="#" class="btn btn-primary btn-sm">View All Results <i
                                    class="ph-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center">

                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class='bi bi-grid fs-2xl'></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg p-0 dropdown-menu-end">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fw-semibold fs-base"> Browse by Apps </h6>
                                </div>
                                <div class="col-auto">
                                    <a href="#!" class="btn btn-sm btn-subtle-info"> View All Apps
                                        <i class="ph-arrow-right align-middle"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="p-2">
                            <div class="row g-0">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="{{ URL::asset('build/images/brands/github.png') }}" alt="Github">
                                        <span>GitHub</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="{{ URL::asset('build/images/brands/bitbucket.png') }}"
                                            alt="bitbucket">
                                        <span>Bitbucket</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="{{ URL::asset('build/images/brands/dribbble.png') }}"
                                            alt="dribbble">
                                        <span>Dribbble</span>
                                    </a>
                                </div>
                            </div>

                            <div class="row g-0">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="{{ URL::asset('build/images/brands/dropbox.png') }}"
                                            alt="dropbox">
                                        <span>Dropbox</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="{{ URL::asset('build/images/brands/mail_chimp.png') }}"
                                            alt="mail_chimp">
                                        <span>Mail Chimp</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="{{ URL::asset('build/images/brands/slack.png') }}" alt="slack">
                                        <span>Slack</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @switch(Session::get('lang'))
                            @case('ru')
                                <img src="{{ URL::asset('build/images/flags/russia.svg') }}" class="rounded"
                                    alt="Header Language" height="20">
                            @break

                            @case('it')
                                <img src="{{ URL::asset('build/images/flags/italy.svg') }}" class="rounded"
                                    alt="Header Language" height="20">
                            @break

                            @case('sp')
                                <img src="{{ URL::asset('build/images/flags/spain.svg') }}" class="rounded"
                                    alt="Header Language" height="20">
                            @break

                            @case('ch')
                                <img src="{{ URL::asset('build/images/flags/china.svg') }}" class="rounded"
                                    alt="Header Language" height="20">
                            @break

                            @case('fr')
                                <img src="{{ URL::asset('build/images/flags/french.svg') }}" class="rounded"
                                    alt="Header Language" height="20">
                            @break

                            @case('gr')
                                <img src="{{ URL::asset('build/images/flags/germany.svg') }}" class="rounded"
                                    alt="Header Language" height="20">
                            @break

                            @case('ae')
                                <img src="{{ URL::asset('build/images/flags/ae.svg') }}" class="rounded"
                                    alt="Header Language" height="20">
                            @break

                            @default
                                <img src="{{ URL::asset('build/images/flags/us.svg') }}" class="rounded"
                                    alt="Header Language" height="20">
                        @endswitch
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">

                        <a href="{{ url('index/en') }}" class="dropdown-item notify-item language py-2"
                            data-lang="en" title="English">
                            <img src="{{ URL::asset('build/images/flags/us.svg') }}" alt="user-image"
                                class="me-2 rounded" height="20">
                            <span class="align-middle">English</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/sp') }}" class="dropdown-item notify-item language" data-lang="sp"
                            title="Spanish">
                            <img src="{{ URL::asset('build/images/flags/spain.svg') }}" alt="user-image"
                                class="me-2 rounded" height="20">
                            <span class="align-middle">Española</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/gr') }}" class="dropdown-item notify-item language" data-lang="gr"
                            title="German">
                            <img src="{{ URL::asset('build/images/flags/germany.svg') }}" alt="user-image"
                                class="me-2 rounded" height="20"> <span class="align-middle">Deutsche</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/it') }}" class="dropdown-item notify-item language" data-lang="it"
                            title="Italian">
                            <img src="{{ URL::asset('build/images/flags/italy.svg') }}" alt="user-image"
                                class="me-2 rounded" height="20">
                            <span class="align-middle">Italiana</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/ru') }}" class="dropdown-item notify-item language" data-lang="ru"
                            title="Russian">
                            <img src="{{ URL::asset('build/images/flags/russia.svg') }}" alt="user-image"
                                class="me-2 rounded" height="20">
                            <span class="align-middle">русский</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/ch') }}" class="dropdown-item notify-item language" data-lang="ch"
                            title="Chinese">
                            <img src="{{ URL::asset('build/images/flags/china.svg') }}" alt="user-image"
                                class="me-2 rounded" height="20">
                            <span class="align-middle">中国人</span>
                        </a>

                        <!-- item-->
                        <a href="{{ url('index/fr') }}" class="dropdown-item notify-item language" data-lang="fr"
                            title="French">
                            <img src="{{ URL::asset('build/images/flags/french.svg') }}" alt="user-image"
                                class="me-2 rounded" height="20">
                            <span class="align-middle">français</span>
                        </a>
                        <!-- item-->
                        <a href="{{ url('index/ae') }}" class="dropdown-item notify-item language" data-lang="ae"
                            title="Arabic">
                            <img src="{{ URL::asset('build/images/flags/ae.svg') }}" alt="user-image"
                                class="me-2 rounded" height="18">
                            <span class="align-middle">عربي</span>
                        </a>
                    </div>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle"
                        id="page-header-issues-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-haspopup="true" aria-expanded="false">
                        <i class='ph-warning fs-2xl'></i>
                        <span
                            class="position-absolute topbar-badge fs-3xs translate-middle badge rounded-pill bg-danger">
                            {{ \App\Models\BlockIssue::where('issue_status_id', 1)->count() + \App\Models\Issue::where('status', 1)->count() }}
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end p-0"
                        aria-labelledby="page-header-issues-dropdown">
                        <div class="p-3 border-bottom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-lg fw-semibold"> Active Issues <span
                                            class="badge bg-danger fs-sm ms-1">
                                            {{ \App\Models\BlockIssue::where('issue_status_id', 1)->count() + \App\Models\Issue::where('status', 1)->count() }}
                                        </span></h6>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('block-issues.index') }}">View All</a>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 300px;">
                            <div class="p-3">
                                @php
                                    $recentBlockIssues = \App\Models\BlockIssue::with(['block', 'reportedBy'])
                                        ->where('issue_status_id', 1)
                                        ->latest()
                                        ->take(3)
                                        ->get();
                                    
                                    $recentGeneralIssues = \App\Models\Issue::with(['reportedBy'])
                                        ->where('status', 1)
                                        ->latest()
                                        ->take(3)
                                        ->get();
                                @endphp
                                
                                @if($recentBlockIssues->count() == 0 && $recentGeneralIssues->count() == 0)
                                    <div class="text-center">
                                        <div class="avatar-md mx-auto my-3">
                                            <div class="avatar-title bg-success-subtle text-success fs-2 rounded-circle">
                                                <i class='ph-check-circle'></i>
                                            </div>
                                        </div>
                                        <h5 class="mb-3">No Active Issues!</h5>
                                        <p class="text-muted">All issues have been resolved.</p>
                                    </div>
                                @else
                                    @foreach($recentBlockIssues as $issue)
                                        <div class="d-block dropdown-item p-2 border-bottom">
                                            <div class="d-flex">
                                                <div class="avatar-xs me-3 flex-shrink-0">
                                                    <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-lg">
                                                        <i class="ph-warning"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('block-issues.show', $issue->id) }}" class="text-reset">
                                                        <h6 class="mt-0 mb-1 fs-md lh-base">
                                                            <strong>{{ $issue->ref_no }}</strong> - {{ Str::limit($issue->issue, 50) }}
                                                        </h6>
                                                    </a>
                                                    <p class="mb-0 fs-sm text-muted">
                                                        Block: {{ $issue->block->name ?? 'N/A' }} | 
                                                        Reported by: {{ $issue->reportedBy->name ?? 'N/A' }}
                                                    </p>
                                                    <p class="mb-0 fs-2xs fw-medium text-uppercase text-muted">
                                                        <span><i class="ph-clock"></i> {{ $issue->created_at->diffForHumans() }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @foreach($recentGeneralIssues as $issue)
                                        <div class="d-block dropdown-item p-2 border-bottom">
                                            <div class="d-flex">
                                                <div class="avatar-xs me-3 flex-shrink-0">
                                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-lg">
                                                        <i class="ph-exclamation-triangle"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('issues.show', $issue->id) }}" class="text-reset">
                                                        <h6 class="mt-0 mb-1 fs-md lh-base">
                                                            <strong>{{ $issue->ref_no }}</strong> - {{ Str::limit($issue->title, 50) }}
                                                        </h6>
                                                    </a>
                                                    <p class="mb-0 fs-sm text-muted">
                                                        Category: {{ $issue->category }} | 
                                                        Reported by: {{ $issue->reportedBy->name ?? 'N/A' }}
                                                    </p>
                                                    <p class="mb-0 fs-2xs fw-medium text-uppercase text-muted">
                                                        <span><i class="ph-clock"></i> {{ $issue->created_at->diffForHumans() }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle"
                        data-toggle="fullscreen">
                        <i class='bi bi-arrows-fullscreen fs-lg'></i>
                    </button>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle mode-layout"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-sun align-middle fs-3xl"></i>
                    </button>
                    <div class="dropdown-menu p-2 dropdown-menu-end" id="light-dark-mode">
                        <a href="#!" class="dropdown-item" data-mode="light"><i
                                class="bi bi-sun align-middle me-2"></i> Default (light mode)</a>
                        <a href="#!" class="dropdown-item" data-mode="dark"><i
                                class="bi bi-moon align-middle me-2"></i> Dark</a>
                        <a href="#!" class="dropdown-item" data-mode="auto"><i
                                class="bi bi-moon-stars align-middle me-2"></i> Auto (system default)</a>
                    </div>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='ph-bell fs-2xl'></i>
                        <span
                            class="position-absolute topbar-badge fs-3xs translate-middle badge rounded-pill bg-info">
                            @php
                                $notificationCount = 0;
                                $notificationCount += \App\Models\WorkOrder::where('common_status_id', 1)->count();
                                $notificationCount += \App\Models\BlockVisit::whereNull('end_date_time')->count();
                                $notificationCount += \App\Models\BlockIssue::where('issue_status_id', 1)->count();
                                $notificationCount += \App\Models\Issue::where('status', 1)->count();
                            @endphp
                            {{ $notificationCount }}
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head rounded-top">
                            <div class="p-3 border-bottom border-bottom-dashed">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0 fs-lg fw-semibold"> Notifications <span
                                                class="badge bg-info-subtle text-info fs-sm notification-badge">
                                                {{ $notificationCount }}
                                            </span></h6>
                                        <p class="fs-md text-muted mt-1 mb-0">You have <span
                                                class="fw-semibold notification-unread">{{ $notificationCount }}</span> pending items</p>
                                    </div>
                                    <div class="col-auto dropdown">
                                        <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                            class="link-secondary fs-md"><i class="ph-dots-three-vertical"></i></a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('work-orders.index') }}">View Work Orders</a></li>
                                            <li><a class="dropdown-item" href="{{ route('block-visits.index') }}">View Site Visits</a></li>
                                            <li><a class="dropdown-item" href="{{ route('block-issues.index') }}">View Issues</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="py-2 ps-2" id="notificationItemsTabContent">
                            <div data-simplebar style="max-height: 300px;" class="pe-2">
                                @php
                                    $pendingWorkOrders = \App\Models\WorkOrder::where('common_status_id', 1)
                                        ->latest()
                                        ->take(2)
                                        ->get();
                                    
                                    $activeSiteVisits = \App\Models\BlockVisit::whereNull('end_date_time')
                                        ->whereNotNull('start_date_time')
                                        ->latest()
                                        ->take(2)
                                        ->get();
                                    
                                    $recentIssues = \App\Models\BlockIssue::where('issue_status_id', 1)
                                        ->latest()
                                        ->take(2)
                                        ->get();
                                @endphp

                                @if($pendingWorkOrders->count() > 0)
                                    <h6 class="text-overflow text-muted fs-sm my-2 text-uppercase notification-title">Pending Work Orders</h6>
                                    @foreach($pendingWorkOrders as $workOrder)
                                        <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                            <div class="d-flex">
                                                <div class="avatar-xs me-3 flex-shrink-0">
                                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-lg">
                                                        <i class="ph-list-dashes"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('work-orders.show', $workOrder->id) }}" class="stretched-link">
                                                        <h6 class="mt-0 fs-md mb-2 lh-base">
                                                            <strong>{{ $workOrder->code }}</strong> - {{ Str::limit($workOrder->fault_description, 60) }}
                                                        </h6>
                                                    </a>
                                                    <p class="mb-0 fs-2xs fw-medium text-uppercase text-muted">
                                                        <span><i class="ph-clock"></i> {{ $workOrder->created_at->diffForHumans() }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                @if($activeSiteVisits->count() > 0)
                                    <h6 class="text-overflow text-muted fs-sm my-2 text-uppercase notification-title">Active Site Visits</h6>
                                    @foreach($activeSiteVisits as $visit)
                                        <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                            <div class="d-flex">
                                                <div class="avatar-xs me-3 flex-shrink-0">
                                                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-lg">
                                                        <i class="ph-map-pin"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('block-visits.show', $visit->id) }}" class="stretched-link">
                                                        <h6 class="mt-0 fs-md mb-2 lh-base">
                                                            <strong>{{ $visit->ref_no }}</strong> - Site visit in progress
                                                        </h6>
                                                    </a>
                                                    <p class="mb-0 fs-sm text-muted">
                                                        Block: {{ $visit->block->name ?? 'N/A' }}
                                                    </p>
                                                    <p class="mb-0 fs-2xs fw-medium text-uppercase text-muted">
                                                        <span><i class="ph-clock"></i> Started {{ $visit->start_date_time->diffForHumans() }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                @if($recentIssues->count() > 0)
                                    <h6 class="text-overflow text-muted fs-sm my-2 text-uppercase notification-title">Recent Issues</h6>
                                    @foreach($recentIssues as $issue)
                                        <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                            <div class="d-flex">
                                                <div class="avatar-xs me-3 flex-shrink-0">
                                                    <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-lg">
                                                        <i class="ph-warning"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('block-issues.show', $issue->id) }}" class="stretched-link">
                                                        <h6 class="mt-0 fs-md mb-2 lh-base">
                                                            <strong>{{ $issue->ref_no }}</strong> - {{ Str::limit($issue->issue, 50) }}
                                                        </h6>
                                                    </a>
                                                    <p class="mb-0 fs-sm text-muted">
                                                        Block: {{ $issue->block->name ?? 'N/A' }} | Priority: {{ $issue->priority_text }}
                                                    </p>
                                                    <p class="mb-0 fs-2xs fw-medium text-uppercase text-muted">
                                                        <span><i class="ph-clock"></i> {{ $issue->created_at->diffForHumans() }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                @if($notificationCount == 0)
                                    <div class="text-center py-4">
                                        <div class="avatar-md mx-auto mb-3">
                                            <div class="avatar-title bg-success-subtle text-success fs-2 rounded-circle">
                                                <i class="ph-check-circle"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-1">All Caught Up!</h6>
                                        <p class="text-muted mb-0">No pending notifications at the moment.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn shadow-none" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user"
                                src="{{ URL::asset('build/images/users/32/avatar-1.jpg') }}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</span>
                                <span class="d-none d-xl-block ms-1 fs-sm user-name-sub-text">
                                    @if(Auth::check() && Auth::user()->userType)
                                        {{ Auth::user()->userType->name }}
                                    @else
                                        User
                                    @endif
                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome {{ Auth::check() ? Auth::user()->name : 'Guest' }}!</h6>
                        <a class="dropdown-item" href="javascript:void(0)"><i
                                class="mdi mdi-account-circle text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle"> @lang('translation.profile')</span></a>
                        <a class="dropdown-item" href="javascript:void(0)"><i
                                class="mdi mdi-message-text-outline text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle">Messages</span></a>
                        <a class="dropdown-item" href="javascript:void(0)"><i
                                class="mdi mdi-calendar-check-outline text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle">Taskboard</span></a>
                        <a class="dropdown-item" href="javascript:void(0)"><i
                                class="mdi mdi-lifebuoy text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle">Help</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0)"><i
                                class="mdi mdi-wallet text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle">Role : <b>
                                    @if(Auth::check() && Auth::user()->userType)
                                        {{ Auth::user()->userType->name }}
                                    @else
                                        User
                                    @endif
                                </b></span></a>
                        <a class="dropdown-item" href="javascript:void(0)"><span
                                class="badge bg-success-subtle text-success mt-1 float-end">New</span><i
                                class="mdi mdi-cog-outline text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle">@lang('translation.settings')</span></a>
                        <a class="dropdown-item" href="{{ route('password.confirm') }}"><i
                                class="mdi mdi-lock text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle"> @lang('translation.lock-screen')</span></a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                                class="mdi mdi-logout text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle" data-key="t-logout">@lang('translation.logout')</span></a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
