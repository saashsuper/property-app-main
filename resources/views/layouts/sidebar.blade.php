    <!-- ========== App Menu ========== -->
    <div class="app-menu navbar-menu">
        @php
            // Helper function to check if a route is active
            function isActiveRoute($routeName) {
                return request()->routeIs($routeName);
            }
            
            // Helper function to check if any child routes are active
            function hasActiveChild($routeNames) {
                foreach ($routeNames as $routeName) {
                    if (request()->routeIs($routeName)) {
                        return true;
                    }
                }
                return false;
            }
            
            // Helper function to get menu classes
            function getMenuClasses($routeName = null, $childRoutes = []) {
                $classes = 'nav-link menu-link';
                
                if ($routeName && isActiveRoute($routeName)) {
                    $classes .= ' active';
                } elseif (!empty($childRoutes) && hasActiveChild($childRoutes)) {
                    $classes .= ' active';
                }
                
                if (!empty($childRoutes)) {
                    $classes .= hasActiveChild($childRoutes) ? '' : ' collapsed';
                }
                
                return $classes;
            }
            
            // Helper function to get dropdown classes
            function getDropdownClasses($childRoutes) {
                $classes = 'collapse menu-dropdown';
                return hasActiveChild($childRoutes) ? $classes . ' show' : $classes;
            }
            
            // Helper function to get submenu link classes
            function getSubmenuClasses($routeName) {
                $classes = 'nav-link';
                return isActiveRoute($routeName) ? $classes . ' active' : $classes;
            }
        @endphp
        
        <!-- LOGO -->
        <div class="navbar-brand-box">
            @php
                $logoFiles = [
                    public_path('images/logos/absolute-icon-only.svg'),
                    public_path('images/logos/absolute-sidebar-logo.svg'),
                    public_path('images/logos/absolute-sidebar-logo-light.svg')
                ];
                $logoVersion = time();
                foreach ($logoFiles as $file) {
                    if (file_exists($file)) {
                        $logoVersion = max($logoVersion, filemtime($file));
                    }
                }
            @endphp
            <a href="{{ route('root') }}" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ URL::asset('images/logos/absolute-icon-only.svg') }}?v={{ $logoVersion }}" alt="Absolute Property Group" height="44">
                </span>
                <span class="logo-lg">
                    <img src="{{ URL::asset('images/logos/absolute-sidebar-logo.svg') }}?v={{ $logoVersion }}" alt="Absolute Property Group" height="64">
                </span>
            </a>
            <a href="{{ route('root') }}" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ URL::asset('images/logos/absolute-icon-only.svg') }}?v={{ $logoVersion }}" alt="Absolute Property Group" height="44">
                </span>
                <span class="logo-lg">
                    <img src="{{ URL::asset('images/logos/absolute-sidebar-logo-light.svg') }}?v={{ $logoVersion }}" alt="Absolute Property Group" height="64">
                </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover"
                id="vertical-hover">
                <i class="ph-record"></i>
            </button>
        </div>
        <div id="scrollbar">
            <div class="container-fluid">

                <div id="two-column-menu">
                </div>
                <ul class="navbar-nav" id="navbar-nav">

                    <li class="menu-title"><span>@lang('translation.menu')</span></li>
                    <li class="nav-item">
                        <a class="{{ getMenuClasses('root') }}" href="{{ route('root') }}">
                            <i class="ph-gauge"></i> <span>@lang('translation.dashboard')</span>
                        </a>
                    </li>

                    @if(!auth()->user()->hasType('Contractor Admin'))
                    <li class="nav-item">
                        <a class="{{ getMenuClasses(null, ['blocks.*', 'block-types.*']) }}" href="#sidebarBlocks" data-bs-toggle="collapse"
                            role="button" aria-expanded="{{ hasActiveChild(['blocks.*', 'block-types.*']) ? 'true' : 'false' }}" aria-controls="sidebarBlocks">
                            <i class="ph-buildings"></i> <span>@lang('translation.blocks')</span>
                        </a>
                        <div class="{{ getDropdownClasses(['blocks.*', 'block-types.*']) }}" id="sidebarBlocks">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('blocks.index') }}" class="{{ getSubmenuClasses('blocks.index') }}">@lang('translation.list-blocks')</a>
                                </li>
                                @hasAnyRole('Admin|Super Admin|Property manager|Office Administrator')
                                <li class="nav-item">
                                    <a href="{{ route('blocks.create') }}" class="{{ getSubmenuClasses('blocks.create') }}">@lang('translation.create-block')</a>
                                </li>
                                @endhasAnyRole
                                @admin
                                <li class="nav-item">
                                    <a href="{{ route('block-types.index') }}" class="{{ getSubmenuClasses('block-types.*') }}">@lang('translation.block-types')</a>
                                </li>
                                @endadmin
                            </ul>
                        </div>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="{{ getMenuClasses(null, ['block-work-orders.*']) }}" href="#sidebarWorkOrders" data-bs-toggle="collapse"
                            role="button" aria-expanded="{{ hasActiveChild(['block-work-orders.*']) ? 'true' : 'false' }}" aria-controls="sidebarWorkOrders">
                            <i class="ph-list-dashes"></i> <span>@lang('translation.work-orders')</span>
                        </a>
                        <div class="{{ getDropdownClasses(['block-work-orders.*']) }}" id="sidebarWorkOrders">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('block-work-orders.index') }}" class="{{ getSubmenuClasses('block-work-orders.index') }}">@lang('translation.block-work-orders')</a>
                                </li>
                                @if(!auth()->user()->hasType('Contractor Admin'))
                                <li class="nav-item">
                                    <a href="{{ route('block-work-orders.create') }}" class="{{ getSubmenuClasses('block-work-orders.create') }}">@lang('translation.create-block-work-order')</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>

                    @if(!auth()->user()->hasType('Contractor Admin'))
                    <li class="nav-item">
                        <a class="{{ getMenuClasses('block-visits.*') }}" href="{{ route('block-visits.index') }}">
                            <i class="ph-map-pin"></i> <span>@lang('translation.site-visits')</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="{{ getMenuClasses('block-inspections.*') }}" href="{{ route('block-inspections.index') }}">
                            <i class="ph-clipboard-text"></i> <span>@lang('translation.block-inspections')</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="{{ getMenuClasses(null, ['block-issues.*', 'issues.*']) }}" href="#sidebarIssues" data-bs-toggle="collapse"
                            role="button" aria-expanded="{{ hasActiveChild(['block-issues.*', 'issues.*']) ? 'true' : 'false' }}" aria-controls="sidebarIssues">
                            <i class="ph-warning"></i> <span>@lang('translation.issues')</span>
                        </a>
                        <div class="{{ getDropdownClasses(['block-issues.*', 'issues.*']) }}" id="sidebarIssues">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('block-issues.index') }}" class="{{ getSubmenuClasses('block-issues.index') }}">@lang('translation.block-issues')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('block-issues.create') }}" class="{{ getSubmenuClasses('block-issues.create') }}">@lang('translation.create-block-issue')</a>
                                </li>
                                {{-- Hidden General Issues and Create Issue submenu items --}}
                            </ul>
                        </div>
                    </li>
                    @endif

                    @superAdmin
                    <li class="nav-item">
                        <a class="{{ getMenuClasses(null, ['users.*', 'user-types.*', 'contract-companies.*']) }}" href="#sidebarUsers" data-bs-toggle="collapse"
                            role="button" aria-expanded="{{ hasActiveChild(['users.*', 'user-types.*', 'contract-companies.*']) ? 'true' : 'false' }}" aria-controls="sidebarUsers">
                            <i class="ph-users"></i> <span>@lang('translation.users')</span>
                        </a>
                        <div class="{{ getDropdownClasses(['users.*', 'user-types.*', 'contract-companies.*']) }}" id="sidebarUsers">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('users.index') }}" class="{{ getSubmenuClasses('users.index') }}">@lang('translation.list-users')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('users.create') }}" class="{{ getSubmenuClasses('users.create') }}">@lang('translation.create-user')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('user-types.index') }}" class="{{ getSubmenuClasses('user-types.index') }}">@lang('translation.user-types')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('user-types.create') }}" class="{{ getSubmenuClasses('user-types.create') }}">@lang('translation.create-user-type')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('contract-companies.index') }}" class="{{ getSubmenuClasses('contract-companies.*') }}">Contract Companies</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endsuperAdmin


                </ul>
            </div>
            <!-- Sidebar -->
        </div>

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
