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
            <a href="{{ route('root') }}" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ URL::asset('build/images/logos/proman-logo-sm.svg') }}" alt="PROMAN" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ URL::asset('build/images/logos/proman-logo-dark.svg') }}" alt="PROMAN" height="22">
                </span>
            </a>
            <a href="{{ route('root') }}" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ URL::asset('build/images/logos/proman-logo-sm.svg') }}" alt="PROMAN" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ URL::asset('build/images/logos/proman-logo-light.svg') }}" alt="PROMAN" height="22">
                </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover"
                id="vertical-hover">
                <i class="ri-record-circle-line"></i>
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
                                @admin
                                <li class="nav-item">
                                    <a href="{{ route('blocks.create') }}" class="{{ getSubmenuClasses('blocks.create') }}">@lang('translation.create-block')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('block-types.index') }}" class="{{ getSubmenuClasses('block-types.*') }}">@lang('translation.block-types')</a>
                                </li>
                                @endadmin
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="{{ getMenuClasses(null, ['work-orders.*', 'block-work-orders.*']) }}" href="#sidebarWorkOrders" data-bs-toggle="collapse"
                            role="button" aria-expanded="{{ hasActiveChild(['work-orders.*', 'block-work-orders.*']) ? 'true' : 'false' }}" aria-controls="sidebarWorkOrders">
                            <i class="ph-list-dashes"></i> <span>@lang('translation.work-orders')</span>
                        </a>
                        <div class="{{ getDropdownClasses(['work-orders.*', 'block-work-orders.*']) }}" id="sidebarWorkOrders">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('work-orders.index') }}" class="{{ getSubmenuClasses('work-orders.index') }}">@lang('translation.general-work-orders')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('work-orders.create') }}" class="{{ getSubmenuClasses('work-orders.create') }}">@lang('translation.create-work-order')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('block-work-orders.index') }}" class="{{ getSubmenuClasses('block-work-orders.index') }}">@lang('translation.block-work-orders')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('block-work-orders.create') }}" class="{{ getSubmenuClasses('block-work-orders.create') }}">@lang('translation.create-block-work-order')</a>
                                </li>
                            </ul>
                        </div>
                    </li>

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
                                <li class="nav-item">
                                    <a href="{{ route('issues.index') }}" class="{{ getSubmenuClasses('issues.index') }}">@lang('translation.general-issues')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('issues.create') }}" class="{{ getSubmenuClasses('issues.create') }}">@lang('translation.create-issue')</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="{{ getMenuClasses(null, ['users.*', 'user-types.*']) }}" href="#sidebarUsers" data-bs-toggle="collapse"
                            role="button" aria-expanded="{{ hasActiveChild(['users.*', 'user-types.*']) ? 'true' : 'false' }}" aria-controls="sidebarUsers">
                            <i class="ph-users"></i> <span>@lang('translation.users')</span>
                        </a>
                        <div class="{{ getDropdownClasses(['users.*', 'user-types.*']) }}" id="sidebarUsers">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('users.index') }}" class="{{ getSubmenuClasses('users.index') }}">@lang('translation.list-users')</a>
                                </li>
                                @admin
                                <li class="nav-item">
                                    <a href="{{ route('users.create') }}" class="{{ getSubmenuClasses('users.create') }}">@lang('translation.create-user')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('user-types.index') }}" class="{{ getSubmenuClasses('user-types.index') }}">@lang('translation.user-types')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('user-types.create') }}" class="{{ getSubmenuClasses('user-types.create') }}">@lang('translation.create-user-type')</a>
                                </li>
                                @endadmin
                            </ul>
                        </div>
                    </li>


                    <li class="menu-title"><i class="ri-more-fill"></i> <span
                            data-key="t-pages">@lang('translation.pages')</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link collapsed" href="#sidebarAuth" data-bs-toggle="collapse"
                            role="button" aria-expanded="false" aria-controls="sidebarAuth">
                            <i class="ph-user-circle"></i> <span>@lang('translation.authentication')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarAuth">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="auth-signin" class="nav-link" role="button"
                                        data-key="t-signin">@lang('translation.signin') </a>
                                </li>
                                <li class="nav-item">
                                    <a href="auth-signup" class="nav-link" role="button"
                                        data-key="t-signup">@lang('translation.signup')</a>
                                </li>

                                <li class="nav-item">
                                    <a href="auth-pass-reset" class="nav-link" role="button"
                                        data-key="t-password-reset">
                                        @lang('translation.password-reset')
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="auth-pass-change" class="nav-link" role="button"
                                        data-key="t-password-create">
                                        @lang('translation.password-create')
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="auth-lockscreen" class="nav-link" role="button"
                                        data-key="t-lock-screen">
                                        @lang('translation.lock-screen')
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="auth-logout" class="nav-link" role="button" data-key="t-logout">
                                        @lang('translation.logout') </a>
                                </li>
                                <li class="nav-item">
                                    <a href="auth-success-msg" class="nav-link" role="button"
                                        data-key="t-success-message">@lang('translation.success-message') </a>
                                </li>
                                <li class="nav-item">
                                    <a href="auth-twostep" class="nav-link" role="button"
                                        data-key="t-two-step-verification"> @lang('translation.two-step-verification') </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#sidebarErrors" class="nav-link" data-bs-toggle="collapse"
                                        role="button" aria-expanded="false" aria-controls="sidebarErrors"
                                        data-key="t-errors"> @lang('translation.errors')</a>
                                    <div class="collapse menu-dropdown" id="sidebarErrors">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="auth-404" class="nav-link"
                                                    data-key="t-404-error">@lang('translation.404')</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="auth-500" class="nav-link" data-key="t-500">
                                                    @lang('translation.500') </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="auth-503" class="nav-link"
                                                    data-key="t-503">@lang('translation.503')</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="auth-offline" class="nav-link" data-key="t-offline-page">
                                                    @lang('translation.offline-page')</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link collapsed" href="#sidebarPages" data-bs-toggle="collapse"
                            role="button" aria-expanded="false" aria-controls="sidebarPages">
                            <i class="ph-address-book"></i> <span data-key="t-pages">@lang('translation.pages')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarPages">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="pages-starter" class="nav-link" data-key="t-starter">@lang('translation.starter')
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="pages-maintenance" class="nav-link"
                                        data-key="t-maintenance">@lang('translation.maintenance') </a>
                                </li>
                                <li class="nav-item">
                                    <a href="pages-coming-soon" class="nav-link"
                                        data-key="t-coming-soon">@lang('translation.coming-soon') </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarMultilevel" data-bs-toggle="collapse"
                            role="button" aria-expanded="false" aria-controls="sidebarMultilevel">
                            <i class="ri-share-line"></i> <span>@lang('translation.multi-level')</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarMultilevel">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">@lang('translation.level-1.1')</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#sidebarAccount" class="nav-link" data-bs-toggle="collapse"
                                        role="button" aria-expanded="false"
                                        aria-controls="sidebarAccount">@lang('translation.level-1.2')
                                    </a>
                                    <div class="collapse menu-dropdown" id="sidebarAccount">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="#" class="nav-link">@lang('translation.level-2.1')</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#sidebarCrm" class="nav-link" data-bs-toggle="collapse"
                                                    role="button" aria-expanded="false"
                                                    aria-controls="sidebarCrm">@lang('translation.level-2.2')
                                                </a>
                                                <div class="collapse menu-dropdown" id="sidebarCrm">
                                                    <ul class="nav nav-sm flex-column">
                                                        <li class="nav-item">
                                                            <a href="#" class="nav-link">@lang('translation.level-3.1')</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="#" class="nav-link">@lang('translation.level-3.2')</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>

                </ul>

                <!-- Layouts Section - Moved to end -->
                <li class="menu-title"><i class="ri-layout-line"></i> <span>Layouts</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link collapsed" href="#sidebarLayouts" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                        <i class="ph-layout"></i><span>@lang('translation.layouts')</span> <span
                            class="badge badge-pill bg-danger" data-key="t-hot">Hot</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarLayouts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="layouts-horizontal" target="_blank" class="nav-link"
                                    data-key="t-horizontal">@lang('translation.horizontal')</a>
                            </li>
                            <li class="nav-item">
                                <a href="layouts-detached" target="_blank" class="nav-link"
                                    data-key="t-detached">@lang('translation.detached')</a>
                            </li>
                            <li class="nav-item">
                                <a href="layouts-two-column" target="_blank" class="nav-link"
                                    data-key="t-two-column">@lang('translation.two-column')</a>
                            </li>
                            <li class="nav-item">
                                <a href="layouts-vertical-hovered" target="_blank" class="nav-link"
                                    data-key="t-hovered">@lang('translation.hovered')</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            </div>
            <!-- Sidebar -->
        </div>

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
