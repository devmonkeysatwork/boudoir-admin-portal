@extends('layouts.app')

@section('content')
<div class="orders">
  <h1>Order List</h1>
  <div class="filters">
    <div class="filter-item">
      <button class="filter-btn">
        <img src="{{ asset('icons/filter.png') }}" alt="Filter Icon">
        <span>Filter By</span>
      </button>
    </div>
    <div class="filter-item">
      <select class="filter-select">
        <option value="" disabled selected>Date</option>
        <option value="oldest">Oldest</option>
        <option value="newest">Newest</option>
      </select>
    </div>
    <div class="filter-item">
      <select class="filter-select">
        <option value="" disabled selected>Product</option>
      </select>
    </div>
    <div class="filter-item">
      <select class="filter-select">
        <option value="" disabled selected>Order Status</option>
        <option value="processing">Processing</option>
        <option value="in-production">In Production</option>
        <option value="on-hold">On Hold</option>
        <option value="completed">Completed</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>
    <div class="filter-item">
      <button class="reset-btn">
        <img src="{{ asset('icons/reset.png') }}" alt="Reset">Reset Filter
      </button>
    </div>
  </div>
  <table>
    <thead>
      <tr>
        <th>Order #</th>
        <th>Phase</th>
        <th>Team Member</th>
        <th>Date Started</th>
        <th>Time in Production</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>00001</td>
        <td><span class="status completed">Completed</span></td>
        <td>Jason Price</td>
        <td>12/09/2019</td>
        <td>20h05m</td>
      </tr>
      <tr>
        <td>00002</td>
        <td><span class="status on-hold">On Hold</span></td>
        <td>Duane Dean</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr>
        <td>00003</td>
        <td><span class="status rejected">Rejected</span></td>
        <td>Duane Dean</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr>
        <td>00004</td>
        <td><span class="status in-production">In Production</span></td>
        <td>Jonathan Barker</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr>
        <td>00005</td>
        <td><span class="status processing">Processing</span></td>
        <td>Jason Price</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr>
        <td>00006</td>
        <td><span class="status completed">Completed</span></td>
        <td>Jason Price</td>
        <td>12/09/2019</td>
        <td>20h05m</td>
      </tr>
      <tr>
        <td>00007</td>
        <td><span class="status processing">Processing</span></td>
        <td>Jonathan Barker</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr>
        <td>00008</td>
        <td><span class="status in-production">In Production</span></td>
        <td>Jason Price</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr>
        <td>00009</td>
        <td><span class="status in-production">In Production</span></td>
        <td>Duane Dean</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
    </tbody>
  </table>
  <!-- @include('partials.footer', ['from' => 1, 'to' => 9, 'total' => 78]) -->
</div>
@endsection