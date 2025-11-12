@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css">
@section('content')
    <div class="dashboard">
        <h1>Completed Order List</h1>

{{--        <div class="filters">--}}
{{--            <form class="filter-bar" action="">--}}
{{--                <div class="filter-item">--}}
{{--                    <button class="filter-btn">--}}
{{--                        <img src="{{ asset('icons/filter.png') }}" alt="Filter Icon">--}}
{{--                        <span>Filter By</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="filter-item">--}}
{{--                    <select class="sort-select" id="filter-date" name="filter_date">--}}
{{--                        <option value="" disabled selected>Date</option>--}}
{{--                        <option value="oldest" {{$filter_date && $filter_date == 'oldest'?'Selected':''}}>Oldest</option>--}}
{{--                        <option value="newest" {{$filter_date && $filter_date == 'newest'?'Selected':''}}>Newest</option>--}}
{{--                    </select>--}}
{{--                </div>--}}
{{--                <div class="filter-item">--}}
{{--                    <select class="sort-select" id="filter-product" name="filter_product">--}}
{{--                        <option value="" disabled selected>Product</option>--}}
{{--                        @foreach($products as $product)--}}
{{--                            <option value="{{ $product->product_name }}" {{$filter_product && $filter_product == $product->product_name?'Selected':''}}>{{ $product->product_name }}</option>--}}
{{--                        @endforeach--}}
{{--                    </select>--}}
{{--                </div>--}}
{{--                <div class="filter-item">--}}
{{--                    <select class="sort-select" id="filter-priority" name="filter_priority">--}}
{{--                        <option value="" disabled selected>Priority</option>--}}
{{--                        <option value="2" {{$filter_priority && $filter_priority == '2'?'Selected':''}}>Normal</option>--}}
{{--                        <option value="1" {{$filter_priority && $filter_priority == '1'?'Selected':''}}>Rush</option>--}}
{{--                    </select>--}}
{{--                </div>--}}
{{--                <div class="filter-item">--}}
{{--                    <button class="reset-btn" type="button">--}}
{{--                        <img src="{{ asset('icons/reset.png') }}" alt="Reset">Reset Filter--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </form>--}}
{{--        </div>--}}
        <div class="orders">
            <form class="w-100" action="">
                <div class="row">
                    <div class="col-12 col-lg-4">
                            <p class="m-0">Per page </p>
                            <select class="form-select w-50" id="per_page" name="per_page" onchange="this.form.submit()">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                    </div>
                    <div class="col-12 col-lg-8 d-flex justify-content-lg-end align-items-center">
                        <a href="{{ route('admin.completed_orders.export', request()->query()) }}" class="btn btn-outline-dark pdf-btn me-3">
                            Export as CSV
                        </a>
                        <div class="orders-search">
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search">
                            <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon" onclick="this.closest('form').submit()">
                        </div>
                    </div>
                </div>
            </form>
            <table id="ordersTable" class="tablesorter">
                <thead>
                <tr>
                    <th>Order #</th>
                    <th>Phase</th>
                    <th>Date Started</th>
                    <th>Time in Production</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="ordersBody">
                @foreach($orders as $order)
                    <tr>
                        <td>
                            @if($order->is_rush)
                                <img src="{{asset('icons/rush.svg')}}" alt="Rush">
                            @endif
                            <button class="edit-btn" onclick="viewDetails('{{$order->id}}','{{$order->order_id}}')">
                                {{ $order->order_id }}
                            </button>
                        </td>
                        <td><span class="status" style="background-color: {{$order->status?->status_color ?? 'transparent'}}">
                                {{$order->status?->status_name ?? null}}
                            </span>
                        </td>
                        <td>{{$order->date_started}}</td>
                        <td>
                            @php
                                $dateStarted = \Carbon\Carbon::parse($order->created_at);
                                $now = \Carbon\Carbon::now();
                                $workingTime = calculateWorkingTime($dateStarted, $now);
                            @endphp

                            {{ $workingTime['months'] > 0 ? $workingTime['months'] . 'm ' : '' }}
                            {{ $workingTime['days'] > 0 ? $workingTime['days'] . 'd ' : '' }}
                            {{ $workingTime['hours'] > 0 ? $workingTime['hours'] . 'h ' : '' }}
                            {{ $workingTime['minutes'] > 0 ? $workingTime['minutes'] . 'm' : '' }}
                        </td>
                        <td>
                            @if(isset($order->children) && count($order->children))
                                <button class="edit-btn" data-order-id="{{ $order->id }}" onclick="openChildrenModal($(this))">
                                    <img src="{{ asset('icons/submenu.svg') }}" alt="View Children">
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
            <div class="row justify-content-end" id="order_paginations">
                <div class="col-6 text-start">
                    @if($orders->count())
                        <p class="py-4 mb-0">
                            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }}
                        </p>
                    @endif
                </div>
                <div class="col-6 text-end">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>



        <x-modal id="orderModal" title="Order #00001">
            <span class="status" id="modal_status_text">Completed</span>
            <div class="orderModal-flex">
                <div class="activity-logs">
                    <div class="activity-log">
                        <h3>Activity Log</h3>
                        <ul id="order_logs">
                        </ul>
                    </div>
                    <div class="sub-orders">
                        <div id="child_order">
                            <h3>Sub-Orders</h3>
                            <table class="border-1 border-dark">
                                <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Status</th>
                                    <th>Completion</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="comments">
                    <h3>Comments</h3>
                    <div id="comments_container">

                    </div>
                </div>

                <x-slot name="footer">
                    <button class="btn pdf-btn" id="download-pdf">
                        <img src="{{ asset('icons/pdf.png') }}" alt="PDF">View Order
                    </button>
                    <div class="new-comment">
                        <textarea placeholder="Write a message..." id="comment_input"></textarea>
                        {{-- <button class="msg-btn">
                          <img src="{{ asset('icons/attach.png') }}" alt="Attach file">
                        </button>
                        <button class="msg-btn">
                          <img src="{{ asset('icons/media.png') }}" alt="Media">
                        </button> --}}
                        <button class="send-btn" onclick="addComment()">
                            Send
                            <img src="{{ asset('icons/send.png') }}" alt="Send">
                        </button>
                    </div>
                </x-slot>
            </div>
        </x-modal>


        <div class="modal fade" id="childOrdersModal" tabindex="-1" aria-labelledby="childOrdersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Child Orders</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="childOrdersModalBody">

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('footer_scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.1/b-3.2.3/b-html5-3.2.3/datatables.min.js" integrity="sha384-sBMuVy0VF5pqJYivpXiLZt97dLWA994xMheWIalaGQfodFfVVhibucErmPqM0kYc" crossorigin="anonymous"></script>
    <script>
        const order_id = '{{$order_id??null}}';
        $(document).ready(function() {
            // let table = new DataTable('#ordersTable', {
            //     autoWidth: false,
            //     columns: [
            //         { title: "Order #" },
            //         { title: "Phase" },
            //         { title: "Date Started" },
            //         { title: "Time in Production" },
            //         { title: "Actions", defaultContent: "" }
            //     ],
            //     columnDefs: [
            //         {
            //             targets: -1,         // -1 targets the last column
            //             orderable: false     // Disable sorting on it
            //         }
            //     ],
            //     layout: {
            //         topStart: {
            //             buttons: ['excelHtml5', 'csvHtml5', 'pdfHtml5']
            //         }
            //     }
            // });



            var url = window.location.href;
            var urlObj = new URL(url);
            var orderId = urlObj.searchParams.get('order_id');
            var tab = urlObj.searchParams.get('tab');
            if (orderId && tab === 'open') {
                viewDetails(order_id,orderId);
            }


            $('#per_page,#searchInput').on('change',function () {
                $('form').submit();
            })

            $('.reset-btn').on('click', function(){
                window.location.href = '{{route('orders.completed')}}';
            });

        });


        function viewChildren(row_id){
            $('#'+row_id).toggle();
        }

        function openChildrenModal(button) {
            var orderId = button.data('order-id');

            $.ajax({
                url: `/get_child_order/${orderId}`,
                type: 'GET',
                success: function (response) {
                    $('#childOrdersModalBody').html(response);
                    $('#childOrdersModal').modal('show');
                },
                error: function () {
                    show_toast('Failed to load child orders. Please try again.','error');
                }
            });
        }



        let activeOrder = 0;

        function viewDetails(orderId, title) {
            $('#child_order > h3').text('Sub-Orders');
            activeOrder = orderId;
            show_loader();

            let data  = new FormData();
            data.append('_token','{{@csrf_token()}}');
            data.append('id',orderId);
            $.ajax({
                type: 'post',
                processData: false,
                contentType: false,
                cache: false,
                url: '{{route('admin.get_order_details')}}',
                data: data,
                beforeSend() {
                    show_loader();
                },
                complete: function (response) {
                    hide_loader();
                },
                success: function (response) {
                    console.log(response);
                    if(response.status == 200){
                        let status = response.status_log;
                        let logs = response.order.logs;
                        let commentsHtml = response.comments_vew; // This is an HTML string now
                        let child_orders = response.order.children;
                        let parent_orders = response.order.parent;
                        if(child_orders && child_orders.length == 0 && parent_orders){
                            child_orders = [parent_orders];
                            $('#child_order > h3').text('Main order');
                        }

                        // Populate the Activity Log
                        $('#order_logs').empty();
                        $.each(logs, function(index, value) {
                            if(value.status){
                                let html = `<li class="log-entry">
                                <span class="log-desc">${value?.user?.name ?? 'Admin'} updated the status to <span class="fw-bold">${value.status.status_name}</span>`;
                                if(value.sub_status){
                                    html += ` because of ${value.sub_status.name}`;
                                }
                                if(value.notes){
                                    html += `<br><b>Notes: </b><i>${value.notes}</i>`;
                                }
                                html += `</span>
                                <span class="log-date">${value.time_started}</span>
                            </li>`;
                                $('#order_logs').append(html);
                            }
                        });

                        // Populate the Sub-orders Table
                        if(child_orders && child_orders.length > 0){
                            $('#child_order').show();
                            $('#child_order table tbody').empty();
                            $.each(child_orders, function(index, value) {
                                let html = `<tr>
                                    <td>${value.order_id}</td>
                                    <td>${value.status.status_name}</td>`;
                                if(value.status.status_name === 'Completed'){
                                    html += `<td><img src="{{ asset('icons/green-checkmark.png') }}" alt="Completed" class="status-icon"></td>`;
                                } else {
                                    html += `<td><img src="{{ asset('icons/grey-checkmark.png') }}" alt="Incomplete" class="status-icon"></td>`;
                                }
                                html += `</tr>`;
                                $('#child_order table tbody').append(html);
                            });
                        } else {
                            $('#child_order').hide();
                        }
                        // Directly append the HTML string to the comments container
                        $('#comments_container').empty().html(commentsHtml);
                        if(status){
                            if(status.sub_status){
                                $('#modal_status_text').empty().html(status.sub_status.name).css('background-color',status.status.status_color);
                            }else{
                                $('#modal_status_text').empty().html(status.status.status_name).css('background-color',status.status.status_color);
                            }
                        }else{
                            $('#modal_status_text').empty().html(response.order.status.status_name).css('background-color',response.order.status.status_color);
                        }

                        // Show the modal
                        $('#orderModal').show();
                        $('#orderModal > div > h2').text('Order #' + title);
                    } else {
                        show_toast(response.message,'error');
                    }
                },
                error: function (response) {
                    console.error('Error:', response);
                }
            });
        }



        function addComment(){
            let data  = new FormData($('#editStatusForm')[0]);
            data.append('_token','{{@csrf_token()}}');
            data.append('comment',$('#comment_input').val());
            data.append('order_id',activeOrder);
            $.ajax({
                type: 'post',
                processData: false,
                contentType: false,
                cache: false,
                url: '{{route('order.add_comment')}}',
                data: data,
                beforeSend() {
                    show_loader();
                },
                complete: function (response) {
                    hide_loader();
                },
                success: function (response) {
                    if(response.status == 200){
                        show_toast(response.message,'success');
                        $('#comments_container').append(response.comment_view);
                        $('#comment_input').val(''); // Clear the input
                    }else{
                        show_toast(response.message,'error');
                    }
                },
                error: function (response) {
                    console.error('Error:', response);
                }
            });
        }

        let commentId=0;
        $(document).on('click', '.reply-btn', function(){
            var $replyForm = $(this).closest('.comment').find('.reply-form');
            $replyForm.toggle();
            commentId = $(this).data('comment');
        });
        $(document).on('click', '.submit-reply', function(){
            var $comment = $(this).closest('.comment');
            var replyText = $(this).closest('.reply-form').find('input').val();
            let data  = new FormData($('#editStatusForm')[0]);
            data.append('_token','{{@csrf_token()}}');
            data.append('comment',replyText);
            data.append('order_id',activeOrder);
            data.append('reply_to',commentId);

            if (replyText !== '') {
                $.ajax({
                    url: '{{route('order.add_reply')}}',
                    type: 'post',
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: data,
                    success: function(reply) {
                        if(reply.status == 200){
                            $comment.replaceWith(reply.comment_view);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        });

        function toggleReplies(id){
            $('.'+id).toggleClass('open');
        }

        $('#download-pdf').on('click', function() {
            window.location.href = '/orders/' + activeOrder + '/download-pdf';
        });

        document.addEventListener('DOMContentLoaded', function () {
            var link = document.getElementById('pdf-export');
            var queryString = window.location.search; // Get the query string from the URL

            // If there's a query string, append it to the link's href
            if (queryString) {
                link.href += queryString; // Append query string to the link
            }
        });
    </script>
@endsection
