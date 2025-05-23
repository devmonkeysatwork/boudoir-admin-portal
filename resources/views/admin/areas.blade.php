@extends('layouts.app')

@section('content')
<div class="areas">
  <h1>Areas</h1>
  <table id="areasTable" class="tablesorter">
    <thead>
      <tr>
        <th>Stations</th>
        <th># of Orders</th>
        <th>Total time(hours)</th>
      </tr>
    </thead>
    <tbody>
      @foreach($workstations as $workstation)
        <tr onclick="loadWorkstationDetails({{ $workstation->id }},'{{ $workstation->status_name }}')">
            <td>{{ $workstation->status_name }}</td>
            <td>{{ $workstation->orders_count() }}</td>
{{--            <td>{{ round($workstation->time_spent,2) }}</td>--}}
            <td>
                <?php
                $time_spent = $workstation->time_spent; // Assuming this is in hours as a decimal
                $hours = floor($time_spent);
                $minutes = floor(($time_spent - $hours) * 60);
                $seconds = round((($time_spent - $hours) * 60 - $minutes) * 60);

                echo "{$hours}h {$minutes}m {$seconds}s";
                ?>
            </td>
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
        <div class="orders">
            <table class="table tablesorter" id="workers_area_modal">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order #</th>
                        <th>Total time(hours)</th>
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


@section('footer_scripts')
  <script>
      function initDatatable(){
          let table = new DataTable('#workers_area_modal', {
              autoWidth: false,
              pageLength: 10,          // Default to 10 entries per page
              lengthChange: false,
              columns: [
                  {title: "#"},
                  {title: "Order #"},
                  {title: "Total time(hours)"}
              ]
          });
          document.querySelector('#searchOrders').addEventListener('keyup', function () {
              table.search(this.value).draw();
          });
      }
    function loadWorkstationDetails(workstationId, name) {
      // Update the modal title
      $('#workstationModal .modal-title').text('Workstation #' + workstationId);

      // Make an AJAX request to fetch workstation details
      $.ajax({
          url: '/workstations/' + workstationId,
          method: 'GET',
          success: function(response) {
              if ($.fn.DataTable.isDataTable('#workers_area_modal')) {
                  $('#workers_area_modal').DataTable().clear().destroy();
              }
              // Populate the modal with the response
              $('#workstationOrders').html(response.ordersHtml); // Assuming response has ordersHtml as HTML structure
              $('#orderCount').text('Showing 1-' + response.orderCount + ' of ' + response.orderCount); // Update count dynamically as needed

              initDatatable();
              // Show the modal
              $('#workstationModal .modal-content > h2').text(name);
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

@push('scripts')
<script>
    $(document).ready(function() {
        $("#areasTable").tablesorter();
    });
</script>
@endpush
