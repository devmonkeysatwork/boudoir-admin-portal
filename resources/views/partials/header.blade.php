<div class="header">
  <!-- <div class="header-search">
    <input type="text" placeholder="Search">
    <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
  </div> -->
  <div class="header-user">
    <div class="notification_container position-relative">
        <button class="btn bg-transparent" onclick="toggleNotifications()"><img src="{{ asset('icons/notification.png') }}" alt="Notifications">
            <span class="notification_counter">0</span>
        </button>
        <div id="notifications_div" class=" bg-white">
            <div class="notification_header">
                <p class="p12 m-0 text-white">Notifications</p>
            </div>
            <div id="notifications">
                <p class="p12 no_notif p-3">No notifications to show</p>
            </div>
            <div class="notification_footer d-flex py-3 px-2 justify-content-center align-items-center bg-white d-none">
                <a href="{{route('admin.notification')}}" class="p12 text-dark">See all notification</a>
            </div>
        </div>
    </div>
    <span>{{ Auth::user()->name }}</span>
    <span>{{ Auth::user()->role }}</span>
  </div>
</div>
