@extends('layouts.app')

@section('content')
    <div class="team">
        <h1 class="d-flex justify-content-between">Production Team
            <button type="button" class="btn btn-primary create-btn" data-bs-toggle="modal" data-bs-target="#createUserModal">
                Add User
            </button>
        </h1>
        <table id="teamTable" class="tablesorter">
            <thead>
            <tr>
                <th>Team</th>
                <th># of Orders</th>
                <th>Time Spent</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($teamMembers as $teamMember)
                <tr>
                    <td>{{ $teamMember->name }}</td>
                    <td>{{ $teamMember->order_count }}</td>
                    <td>{{ $teamMember->total_time }}</td>
                    <td>
                        <button onclick="openUserWorkingStatusModal('{{$teamMember->id}}','{{$teamMember->name}}','{{$teamMember->product_status_id}}')">Edit</button>
                        <button onclick="loadTeamDetails({{ $teamMember->id }})">Details</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <x-modal id="workstationModal" title="Team">
        <div class="modal-body">
            <!-- Search Bar -->
            <div class="search-bar orders-search mb-3">
                <input type="text" id="searchOrders" placeholder="Search" class="form-control" />
                <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
            </div>

            <!-- Scrollable Workstation Orders Table -->
            <div>
                <table class="table" id="workers_area_modal">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Order #</th>
                        <th>Time in Production</th>
                    </tr>
                    </thead>
                    <tbody id="workstationOrders">
                    <!-- Orders will be dynamically loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
        <x-slot name="footer">
            <button class="btn btn-secondary" onclick="document.getElementById('workstationModal').style.display='none'">Cancel</button>
        </x-slot>
    </x-modal>

    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createUserForm">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" placeholder="Name" name="name" required>
                                    <div class="invalid-feedback" id="name-error"></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="role_id" class="form-label">Role</label>
                                    <select class="form-select" id="role_id" name="role_id" required>
                                        <option value="" disabled selected>Select a role</option>
                                        @foreach($roles??[] as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="role_id-error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="email" placeholder="Name" name="email" required>
                                    <div class="invalid-feedback" id="email-error"></div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary create-btn btn_loader">Create User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>




    <div class="modal fade" id="userWorkingStatusModal" tabindex="-1" aria-labelledby="userNameLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userNameLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userAssignedStatusForm">
                        <input type="hidden" name="active_user_id" id="active_user_id">
                        <div id="statusFieldsContainer">
                            <div class="row statusField">

                            </div>
                        </div>
                        <div class="form-group btn-group text-end">
                            <button type="button" id="addStatusField" class="btn btn-light border-1 border-dark cancel-btn">Add Another Status</button>
                            <button type="submit" class="btn btn-primary create-btn btn_loader">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    <script>
        function initDatatable(){
            let table = new DataTable('#workers_area_modal', {
                autoWidth: false,
                columns: [
                    {title: "#"},
                    {title: "Order #"},
                    {title: "Total time(hours)"}
                ],
                columnDefs: [
                    {
                        targets: -1,         // -1 targets the last column
                        orderable: false     // Disable sorting on it
                    }
                ]
            });
        }
        function loadTeamDetails(teamMemberId) {
            // Update the modal title
            $('#workstationModal .modal-title').text('Team #' + teamMemberId);

            // Make an AJAX request to fetch team details
            $.ajax({
                url: '/team/' + teamMemberId,
                method: 'GET',
                success: function(response) {
                    if ($.fn.DataTable.isDataTable('#workers_area_modal')) {
                        $('#workers_area_modal').DataTable().clear().destroy();
                    }
                    // Populate the modal with the response
                    $('#workstationOrders').html(response.ordersHtml);
                    $('#orderCount').text('Showing 1-' + response.orderCount + ' of ' + response.orderCount);
                    initDatatable();
                    // Show the modal
                    $('#workstationModal').show();
                },
                error: function() {
                    $('#workstationOrders').html('<tr><td colspan="3">Failed to load team details.</td></tr>');
                }
            });
        }

        $('#searchOrders').on('keyup', function() {
            var searchValue = $(this).val().toLowerCase();
            $('#workstationOrders tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
            });
        });


        function statusAssignModal(id,user,status){
            $('#userWorkingStatusModal .modal-title').text(user);
            $('#userWorkingStatusModal #active_user_id').val(id);
            $('#userWorkingStatusModal').modal('show');
        }


        $(document).ready(function() {
            $("#teamTable").tablesorter();
            $('#createUserForm').on('submit', function(event) {
                event.preventDefault();
                $('#createUserForm').find('button').addClass('clicked');

                $('.invalid-feedback').empty();
                $('#createUserForm').removeClass('was-validated');
                let data  = new FormData($('#createUserForm')[0]);
                data.append('_token','{{@csrf_token()}}');
                $.ajax({
                    type: 'post',
                    processData: false,
                    contentType: false,
                    cache: false,
                    url: '{{route('admin.add_worker')}}',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            $('#createUserModal').modal('hide');
                            show_toast(response.message,'success');
                            window.location.reload();
                        }else{
                            show_toast(response.message,'error');
                            $('#createUserForm').find('button').removeClass('clicked');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        if (errors) {
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').text(value[0]);
                                $('#' + key).addClass('is-invalid');
                            });
                        }else{
                            show_toast(xhr.responseJSON.message,'error');
                            $('#createUserForm').find('button').removeClass('clicked');
                        }
                    }
                });
            });


            $('#userAssignedStatusForm').on('submit', function(event) {
                event.preventDefault();
                $('#userAssignedStatusForm').find('button').addClass('clicked');

                $('.invalid-feedback').empty();
                $('#userAssignedStatusForm').removeClass('was-validated');
                let data  = new FormData($('#userAssignedStatusForm')[0]);
                data.append('_token','{{@csrf_token()}}');
                $.ajax({
                    type: 'post',
                    processData: false,
                    contentType: false,
                    cache: false,
                    url: '{{route('worker.update_station')}}',
                    data: data,
                    success: function(response) {
                        if (response.status == 200) {
                            $('#userWorkingStatusModal').modal('hide');
                            show_toast(response.message,'success');
                            window.location.reload();
                        }else{
                            show_toast(response.message,'error');
                            $('#userAssignedStatusForm').find('button').removeClass('clicked');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                    }
                });
            });
        });
        function openUserWorkingStatusModal(id,user,status) {
            event.preventDefault();
            $('#statusFieldsContainer .row').empty();
            $('#userWorkingStatusModal #active_user_id').val(id);
            $.ajax({
                url: `/get/${id}/workstations`, // Make sure this URL returns the user’s workstations
                method: 'GET',
                success: function(response) {
                    if (response.status === 200) {
                        const workstations = response.data; // Assuming `data` contains an array of status_ids
                        workstations.forEach(workstation => {
                            addStatusField(workstation.status_id);
                        });
                        statusAssignModal(id,user,status);
                    } else {
                        show_toast(response.message, 'error');
                    }
                },
                error: function(xhr) {
                    show_toast("Error fetching workstations data.", 'error');
                }
            });
        }
        function addStatusField(statusId = '') {
            let statusFieldHtml = `
                <div class="col-12 col-md-6 position-relative">
                    <div class="mb-3">
                        <label for="status_id" class="form-label">Assigned Status</label>
                        <select class="form-select" name="status_ids[]" required>
                            <option value="" disabled ${statusId ? '' : 'selected'}>Select a status</option>
                            @foreach($statuses ?? [] as $status)
                        <option value="{{$status->id}}" ${statusId == {{$status->id}} ? 'selected' : ''}>{{$status->status_name}}</option>
                                    @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <button type="button" class="position-absolute remove_status_btn">X</button>
                </div>`;
            $('#statusFieldsContainer .row').append(statusFieldHtml);
        }
        $('#addStatusField').on('click', function() {
            addStatusField();
        });
        $(document).on('click', '.remove_status_btn', function(){
            $(this).parent().remove();
        })
    </script>
@endsection


