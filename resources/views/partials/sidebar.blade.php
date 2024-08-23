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
    <li>
      <a href="{{ route('admin.notification') }}" class="{{ Request::is('notification*') ? 'active' : '' }}">
        <img src="{{ asset('icons/notification.png') }}" alt="Settings">Notifications
      </a>
    </li>
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
      <a href="{{ route('admin.workstations') }}" class="{{ Request::routeIs('admin.workstations') ? 'active' : '' }}">
        <img src="{{ asset('icons/teams.png') }}" alt="Workstations">Production Team
      </a>
    </li>
    <hr>
    <li>
    <li>
      <a href="{{ route('admin.settings') }}" class="{{ Request::is('settings*') ? 'active' : '' }}">
        <img src="{{ asset('icons/settings.png') }}" alt="Settings">Settings
      </a>
    </li>
    <li>
      <a href="{{ route('logout') }}" class="{{ Request::routeIs('logout') ? 'active' : '' }}" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
        <img src="{{ asset('icons/logout.png') }}" alt="Logout">Logout
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </li>
  </ul>
</div>