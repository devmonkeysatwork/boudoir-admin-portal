@extends('layouts.app')

@section('content')
<div class="dashboard">
  <h1>Dashboard</h1>
  <div class="stats">
    <div class="stat">
      <div class="stat-top">
        <img src="{{ asset('icons/orders-pending.png') }}" alt="Pending">
        <div class="stat-info">
          <h3>40,689</h3>
          <p>Pending</p>
        </div>
      </div>
      <div class="stat-btm">
        <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
        <p><span class="stat-up">8.5%</span> Up from yesterday</p>
      </div>
    </div>
    <div class="stat">
      <div class="stat-top">
        <img src="{{ asset('icons/orders-in-production.png') }}" alt="in Production">
        <div class="stat-info">
          <h3>10,293</h3>
          <p>In Production</p>
        </div>
      </div>
      <div class="stat-btm">
        <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
        <p><span class="stat-up">1.3%</span> Up from past week</p>
      </div>
    </div>
    <div class="stat">
      <div class="stat-top">
        <img src="{{ asset('icons/orders-on-hold.png') }}" alt="on Hold">
        <div class="stat-info">
          <h3>89,000</h3>
          <p>On Hold</p>
        </div>
      </div>
      <div class="stat-btm">
        <img src="{{ asset('icons/stat-down.png') }}" alt="Stat Down">
        <p><span class="stat-down">4.3%</span> Down from yesterday</p>
      </div>
    </div>
    <div class="stat">
      <div class="stat-top">
        <img src="{{ asset('icons/orders-ready.png') }}" alt="Ready">
        <div class="stat-info">
          <h3>2,040</h3>
          <p>Ready to Ship</p>
        </div>
      </div>
      <div class="stat-btm">
        <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
        <p><span class="stat-up">1.8%</span> Up from yesterday</p>
      </div>
    </div>
  </div>
  <div class="orders">
    <div class="orders-top">
      <h2>List of Orders</h2>
      <div class="orders-filter">
        <div class="orders-search">
          <input type="text" placeholder="Search">
          <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
        </div>
        <div class="sort-dropdown">
          <select class="sort-select" id="order-sort">
            <option value="" disabled selected>Sort By</option>
            <option value="oldest">Oldest</option>
            <option value="newest">Newest</option>
          </select>
        </div>
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
          <td>Jonathan Barker</td>
          <td>12/09/2019</td>
          <td>2h05m</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="team-workstations">
    <div class="team">
      <div class="team-top">
        <h2>Team</h2>
        <div class="sort-dropdown">
          <select class="sort-select" id="team-sort">
            <option value="week" selected>Week</option>
            <option value="day">Day</option>
            <option value="month">Month</option>
            <option value="year">Year</option>
          </select>
        </div>
      </div>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th># of Orders</th>
            <th>Time Spent</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Jason Price</td>
            <td>5</td>
            <td>3h30m</td>
          </tr>
          <tr>
            <td>Duane Dean</td>
            <td>7</td>
            <td>5h07m</td>
          </tr>
          <tr>
            <td>Jonathan Barker</td>
            <td>4</td>
            <td>2h49m</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="workstations">
      <div class="workstations-top">
        <h2>Workstations</h2>
        <div class="sort-dropdown">
          <select class="sort-select" id="workstation-sort">
            <option value="week" selected>Week</option>
            <option value="day">Day</option>
            <option value="month">Month</option>
            <option value="year">Year</option>
          </select>
        </div>
      </div>
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
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection