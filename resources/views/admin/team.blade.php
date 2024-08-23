@extends('layouts.app')

@section('content')
<div class="team">
  <h1>Production Team</h1>
  <table>
    <thead>
      <tr>
        <th>Team</th>
        <th># of Orders</th>
        <th>Time Spent</th>
      </tr>
    </thead>
    <tbody>
      @foreach($teamMembers as $teamMember)
        <tr>
          <td>{{ $teamMember->name }}</td>
          <td>{{ $teamMember->num_orders }}</td>
          <td>{{ $teamMember->time_spent }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  
  <!-- Pagination -->
  <div class="row justify-content-end d-flex">
      <div class="col-12 col-sm-3">
          {{ $teamMembers->links() }}
      </div>
  </div>
</div>
@endsection
