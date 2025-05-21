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
            <li class="item-with-dropdown">
                <a class="sub-btn {{ Request::routeIs('orders.completed') || Request::routeIs('admin.orders') ? 'active' : '' }}" href="javascript:void(0);">
                    <span>
                        <img src="{{ asset('icons/order_listsings.svg') }}" alt="Reports">Orders
                    </span>
                    <svg width="10px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"/>
                    </svg>
                </a>
                <ul class="sub-menu {{ Request::routeIs('orders.completed') || Request::routeIs('admin.orders') ? 'd-block' : '' }}">
                    <li>
                        <a href="{{ route('admin.orders') }}" class="{{ Request::routeIs('admin.orders') ? 'active' : '' }}">
                            <img src="{{ asset('icons/orders.svg') }}" alt="Order List">Order List
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('orders.completed') }}" class="{{ Request::routeIs('orders.completed') ? 'active' : '' }}">
                            <img src="{{ asset('icons/order-list.png') }}" alt="Order List">Completed Orders
                        </a>
                    </li>
                </ul>
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
        <li>
            <a href="{{ route('worker.my_orders') }}" class="{{ Request::routeIs('worker.my_orders') ? 'active' : '' }}">
                <img src="{{ asset('icons/my-orders.svg') }}" height="40px" alt="Order List" style="object-fit: cover">My Orders
            </a>
        </li>
        <li>
            <a href="{{ route('admin.notification') }}" class="{{ Request::is('notification*') ? 'active' : '' }}">
                <img src="{{ asset('icons/notification.png') }}" alt="Settings">Notifications
            </a>
        </li>
        @if(Auth::user()->role_id == 1)
            <li class="item-with-dropdown">
                <a class="sub-btn {{ Request::routeIs('dashboard.reports') || Request::routeIs('dashboard.compare') ? 'active' : '' }}" href="javascript:void(0);">
                    <span>
                        <img src="{{ asset('icons/reports.png') }}" alt="Reports">Analytics
                    </span>
                    <svg width="10px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"/>
                    </svg>
                </a>
                <ul class="sub-menu {{Request::routeIs('dashboard.reports') || Request::routeIs('dashboard.compare') ? 'd-block' : '' }}">
                    <li>
                        <a href="{{ route('dashboard.reports') }}" class="{{ Request::routeIs('dashboard.reports')|| Request::routeIs('dashboard.compare') ? 'active' : '' }}">
                            <img src="{{ asset('icons/reports.svg') }}" alt="Reports">Reports
                        </a>
                    </li>
{{--                    <li>--}}
{{--                        <a href="{{ route('dashboard.compare') }}" class="{{ Request::routeIs('dashboard.compare') ? 'active' : '' }}">--}}
{{--                            <img src="{{ asset('icons/reports.png') }}" alt="Reports">Compare Members--}}
{{--                        </a>--}}
{{--                    </li>--}}
                </ul>
            </li>

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
                <
            </button>
        </li>
    </ul>
</div>
