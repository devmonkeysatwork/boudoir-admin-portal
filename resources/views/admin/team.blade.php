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
      </tr>
    </thead>
    <tbody>
      @foreach($teamMembers as $teamMember)
        <tr onclick="loadTeamDetails({{ $teamMember->id }})">
          <td>{{ $teamMember->name }}</td>
          <td>{{ $teamMember->order_count }}</td>
          <td>{{ $teamMember->total_time }}</td>
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
        <div class="table-responsive">
            <table class="table">
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
                    </div>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
  <script>
    function loadTeamDetails(teamMemberId) {
      // Update the modal title
      $('#workstationModal .modal-title').text('Team #' + teamMemberId);

      // Make an AJAX request to fetch team details
      $.ajax({
          url: '/team/' + teamMemberId,
          method: 'GET',
          success: function(response) {
              // Populate the modal with the response
              $('#workstationOrders').html(response.ordersHtml);
              $('#orderCount').text('Showing 1-' + response.orderCount + ' of ' + response.orderCount);

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


    $(document).ready(function() {
        $("#teamTable").tablesorter();
        $('#createUserForm').on('submit', function(event) {
            event.preventDefault();

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
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                            $('#' + key).addClass('is-invalid');
                        });
                    }
                }
            });
        });
    });
  </script>
@endsection


