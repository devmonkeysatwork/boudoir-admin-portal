@extends('layouts.app')

@section('content')
<div class="team">
  <h1>Production Team</h1>
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
          <td>{{ $teamMember->total_orders }}</td>
          <td>{{ $teamMember->time_spent }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection

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

        <!-- Pagination or footer for showing number of results -->
        <div class="pagination">
            <p id="orderCount">Showing 1-08 of 08</p>
        </div>
    </div>
    <x-slot name="footer">
        <button class="btn btn-secondary" onclick="document.getElementById('workstationModal').style.display='none'">Cancel</button>
    </x-slot>
</x-modal>

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
  </script>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $("#teamTable").tablesorter();
    });
</script>
@endpush

