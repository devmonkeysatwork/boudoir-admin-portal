<div class="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('images/logo.png') }}" alt="The Boudoir Album">
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <img src="{{ asset('icons/dashboard.png') }}" alt="Dashboard">Dashboard
            </a>
        </li>
        @if(Auth::user()->role_id == 1)
            <li>
                <a href="{{ route('admin.orders') }}" class="{{ Request::routeIs('admin.orders') ? 'active' : '' }}">
                    <img src="{{ asset('icons/order-list.png') }}" alt="Order List">Order List
                </a>
            </li>
            <li>
                <a href="{{ route('admin.areas') }}" class="{{ Request::routeIs('admin.areas') ? 'active' : '' }}">
                    <img src="{{ asset('icons/workstations.png') }}" alt="Areas">Areas
                </a>
            </li>
            <li>
                <a href="{{ route('admin.team') }}" class="{{ Request::routeIs('admin.team') ? 'active' : '' }}">
                    <img src="{{ asset('icons/teams.png') }}" alt="Team">Production Team
                </a>
            </li>
        @endif
{{--        <li>--}}
{{--            <a href="{{ route('worker.my_orders') }}" class="{{ Request::routeIs('worker.my_orders') ? 'active' : '' }}">--}}
{{--                <img src="{{ asset('icons/order-list.png') }}" alt="Order List">My Orders--}}
{{--            </a>--}}
{{--        </li>--}}
        <li>
            <a href="{{ route('admin.notification') }}" class="{{ Request::is('notification*') ? 'active' : '' }}">
                <img src="{{ asset('icons/notification.png') }}" alt="Settings">Notifications
            </a>
        </li>
        @if(Auth::user()->role_id == 1)
{{--            <li>--}}
{{--                <a href="{{ route('dashboard.reports') }}" class="{{ Request::routeIs('dashboard.reports') ? 'active' : '' }}">--}}
{{--                    <img src="{{ asset('icons/reports.png') }}" alt="Reports">Reports--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{ route('dashboard.compare') }}" class="{{ Request::routeIs('dashboard.compare') ? 'active' : '' }}">--}}
{{--                    <img src="{{ asset('icons/reports.png') }}" alt="Reports">Compare Members--}}
{{--                </a>--}}
{{--            </li>--}}
            <hr>
            <li>
                <a href="{{ route('admin.settings') }}" class="{{ Request::is('settings*') ? 'active' : '' }}">
                    <img src="{{ asset('icons/settings.png') }}" alt="Settings">Settings
                </a>
            </li>
        @else
            <hr>
        @endif
        <li>
            <a href="{{ route('logout') }}" class="{{ Request::routeIs('logout') ? 'active' : '' }}" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                <img src="{{ asset('icons/logout.png') }}" alt="Logout">Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
        <li class="toggleSidebar ps-4">
            <button class="btn border-0 d-flex justify-content-center align-items-center flex-row"  onclick="toggleSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                </svg> hide menu
            </button>
        </li>
    </ul>
</div>
