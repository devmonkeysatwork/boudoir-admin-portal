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
        <!-- <th>Assigned To</th> -->
      </tr>
    </thead>
    <tbody>
      @foreach($workstations as $workstation)
        <tr>
          <td>{{ $workstation->status_name }}</td>
          <td>{{ $workstation->orders->count() }}</td> 
          <td>{{ $workstation->time_in_production }}</td> 
          <!-- <td>{{ $workstation->assigned_to }}</td> -->
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
