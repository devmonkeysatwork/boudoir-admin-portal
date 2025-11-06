@extends('layouts.app')

@section('content')
    <div class="workstations">
        <h1>Notifications</h1>
        <table id="notification_list">
            <tbody id="notification_tbody">
            @include('admin.partials.notification_rows')
            </tbody>
        </table>

        @if($notifications->hasMorePages())
            <div id="load_more" class="text-center mt-3" data-page="2">
                <span class="loading-text">Loading...</span>
            </div>
        @endif
    </div>
@endsection

@section('footer_scripts')
    <script>
        $(document).ready(function() {
            let isLoading = false;
            let hasMorePages = {{ $notifications->hasMorePages() ? 'true' : 'false' }};
            let currentPage = 2;

            $(window).on('scroll', function() {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                    if (!isLoading && hasMorePages) {
                        loadMoreNotifications();
                    }
                }
            });

            function loadMoreNotifications() {
                isLoading = true;
                $('#load_more .loading-text').show();

                $.ajax({
                    url: "{{route('admin.notification')}}?page=" + currentPage,
                    type: 'GET',
                    success: function(response) {
                        $('#notification_tbody').append(response);
                        currentPage++;
                        isLoading = false;
                        if($(response).filter('tr').length < 10) {
                            hasMorePages = false;
                            $('#load_more').hide();
                        }
                    },
                    error: function() {
                        isLoading = false;
                        $('#load_more .loading-text').text('Error loading. Scroll to retry.');
                        setTimeout(function() {
                            $('#load_more .loading-text').text('Loading...');
                        }, 2000);
                    }
                });
            }
        });
    </script>
@endsection
