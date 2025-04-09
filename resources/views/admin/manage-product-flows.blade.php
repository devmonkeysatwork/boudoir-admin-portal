@extends('layouts.app')

@section('content')
    <div class="settings">
        <h1>Settings</h1>
        <div class="settings-menu">
            <ul>
                <li>
                    <a href="{{ route('admin.manage-statuses') }}">
                        <img src="{{ asset('icons/statuses.png') }}" alt="Status Icon">Manage Statuses
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.manage-emails') }}">
                        <img src="{{ asset('icons/email.png') }}" alt="Email Icon">Manage Emails
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.product-flows') }}" class="active">
                        <img src="{{ asset('icons/email.png') }}" alt="Email Icon">Product Flows
                    </a>
                </li>
            </ul>
        </div>
        <div class="settings-content">
            <div class="manage-top">
                <h2>Product Flows</h2>
{{--                <button class="create-btn" data-open-modal="createStatusModal">+ Create New Status</button>--}}
            </div>
            <div class="manage-btm">
                @if(isset($flows))
                    <table>
                        <tbody>
                        @foreach($flows as $flow)
                            <tr>
                                <td>
                                    <p class="text-start">
                                        <strong>{{$flow->name}}</strong>
                                        <ul class="product_flows">
                                        @if (count($flow->orderStatuses))
                                            @foreach ($flow->orderStatuses as $orderStatus)
                                                <li>
                                                    {{--                                                Step {{ $orderStatus->pivot->step_no }}: --}}
                                                    {{ $orderStatus->status_name }}</li>
                                            @endforeach
                                        @else
                                            <p>No steps assigned yet.
                                                <button type="button" onclick="showModal({{$flow->id}})" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-steps-modal">
                                                    Add steps
                                                </button></p>
                                        @endif

                                        </ul>
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="modal fade" id="add-steps-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addStepsModalLabel">Add Steps to Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="javascript:void(0)" id="product_flow_form" method="POST">
                            <input type="hidden" name="product_id" id="product_id">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="steps">Select Steps</label>
                                    <select name="steps[]" id="steps" class="form-control h-50" multiple>
                                        @foreach ($availableSteps as $step)
                                            <option value="{{ $step->id }}">{{ $step->status_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" onclick="addProductFlow()">Add Steps</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @include('partials.footer')
        </div>
    </div>

@endsection
@section('footer_scripts')
    <script>
        function addProductFlow(){
            let data  = new FormData($('#product_flow_form')[0]);
            data.append('_token','{{@csrf_token()}}');
            $.ajax({
                type: 'post',
                processData: false,
                contentType: false,
                cache: false,
                url: '{{route('add.product-flow')}}',
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
                        window.location.reload();

                    }else{
                        show_toast(response.message,'error');
                    }

                },
                error: function (response) {
                }
            })
        }
        function showModal(id){
            $('#product_id').val(id);
        }

        function deleteStatus(statusId) {
            if (confirm("Are you sure you want to delete this status?")) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.delete_status') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: statusId
                    },
                    beforeSend: function() {
                        show_loader();
                    },
                    success: function(response) {
                        hide_loader();
                        if (response.status == 200) {
                            show_toast(response.message, 'success');
                            window.location.reload(); // Example: Reload the page after successful deletion
                        } else {
                            show_toast(response.message, 'error');
                        }
                    },
                    error: function(response) {
                        hide_loader();
                        show_toast('Error deleting status.', 'error');
                    }
                });
            }
        }
        function editStatus(me){
            $('#edit_id').val($(me).data('id'));
            $('#edit-status-name').val($(me).data('name'));
            $('#edit-create-preview').html($(me).data('name'));
            $('#edit-status-color').val($(me).data('color'));
            editPickr.setColor($(me).data('color'));
            $('#editStatusModal').show();
        }

    </script>
@endsection


