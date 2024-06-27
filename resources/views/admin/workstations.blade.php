@extends('layouts.app')

@section('content')
<div class="workstations">
  <h1>Workstations</h1>
  <table>
    <thead>
      <tr>
        <th>Workstation #</th>
        <th># of Orders</th>
        <th>Time in Production</th>
        <th>Assigned To</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>5</td>
        <td>13h39m</td>
        <td>Jason Price</td>
      </tr>
      <tr>
        <td>2</td>
        <td>7</td>
        <td>20h27m</td>
        <td>Duane Dean</td>
      </tr>
      <tr>
        <td>3</td>
        <td>4</td>
        <td>11h15m</td>
        <td>Jonathan Barker</td>
      </tr>
      <tr>
        <td>4</td>
        <td>8</td>
        <td>24h51m</td>
        <td>Raphael Margeritti</td>
      </tr>
      <tr>
        <td>5</td>
        <td>12</td>
        <td>32h16m</td>
        <td>Leonardo Dicaprio</td>
      </tr>
      <tr>
        <td>6</td>
        <td>31</td>
        <td>72h05m</td>
        <td>Michaelangelo Smith</td>
      </tr>
      <tr>
        <td>7</td>
        <td>15</td>
        <td>40h30m</td>
        <td>Donatello Johnson</td>
      </tr>
    </tbody>
  </table>
  <!-- @include('partials.footer', ['from' => 1, 'to' => 7, 'total' => 7]) -->
</div>
@endsection