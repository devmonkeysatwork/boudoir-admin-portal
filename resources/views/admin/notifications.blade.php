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
            <button id="load_more" class="create-btn mt-3 mx-auto d-inline-block" data-page="2">Load More</button>
        @endif
    </div>
@endsection
@section('footer_scripts')
    <script>
        $(document).ready(function() {
            $('#load_more').on('click', function() {
                let btn = $(this);
                let page = btn.data('page');

                btn.prop('disabled', true).text('Loading...');

                $.ajax({
                    url: "{{route('admin.notification')}}?page=" + page,
                    type: 'GET',
                    success: function(response) {
                        $('#notification_tbody').append(response);
                        btn.data('page', page + 1);
                        btn.prop('disabled', false).text('Load More');
                        if($(response).filter('tr').length < 10) {
                            btn.hide();
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).text('Load More');
                        alert('Error loading notifications');
                    }
                });
            });
        });
    </script>
@endsection
