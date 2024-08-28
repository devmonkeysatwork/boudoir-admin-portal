@extends('layouts.app')

@section('content')
<div class="areas">
  <h1>Areas</h1>
  <table>
    <thead>
      <tr>
        <th>Stations</th>
        <th># of Orders</th>
        <th>Time in Production</th>
      </tr>
    </thead>
    <tbody>
      @foreach($workstations as $workstation)
        <tr onclick="loadWorkstationDetails({{ $workstation->id }})">
          <td>{{ $workstation->workstation_name }}</td>
          <td>{{ $workstation->num_orders }}</td> 
          <td>{{ $workstation->time_in_production }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  
  <!-- Pagination -->
  <div class="row justify-content-end d-flex">
      <div class="col-12 col-sm-3">
          {{ $workstations->links() }}
      </div>
  </div>
</div>
@endsection

<x-modal id="workstationModal" title="Workstation">
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
    function loadWorkstationDetails(workstationId) {
      // Update the modal title
      $('#workstationModal .modal-title').text('Workstation #' + workstationId);

      // Make an AJAX request to fetch workstation details
      $.ajax({
          url: '/workstations/' + workstationId,
          method: 'GET',
          success: function(response) {
              // Populate the modal with the response
              $('#workstationOrders').html(response.ordersHtml); // Assuming response has ordersHtml as HTML structure
              $('#orderCount').text('Showing 1-08 of 08'); // Update count dynamically as needed

              // Show the modal
              $('#workstationModal').show();
          },
          error: function() {
              $('#workstationOrders').html('<tr><td colspan="3">Failed to load workstation details.</td></tr>');
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
