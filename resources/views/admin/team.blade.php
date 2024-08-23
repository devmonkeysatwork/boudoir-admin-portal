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
          <td>{{ $teamMember->total_orders }}</td>
          <td>{{ $teamMember->time_spent }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  
</div>
@endsection
