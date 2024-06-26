<div class="header">
  <div class="header-search">
    <input type="text" placeholder="Search">
    <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
  </div>
  <div class="header-user">
    <img src="{{ asset('icons/notification.png') }}" alt="Notifications">
    <span>{{ Auth::user()->name }}</span>
    <span>{{ Auth::user()->role }}</span>
  </div>
</div>