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
      <tr data-open-modal="orderModal">
        <td>00001</td>
        <td><span class="status completed">Completed</span></td>
        <td>Jason Price</td>
        <td>12/09/2019</td>
        <td>20h05m</td>
      </tr>
      <tr data-open-modal="orderModal">
        <td>00002</td>
        <td><span class="status on-hold">On Hold</span></td>
        <td>Duane Dean</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr data-open-modal="orderModal">
        <td>00003</td>
        <td><span class="status rejected">Rejected</span></td>
        <td>Duane Dean</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr data-open-modal="orderModal">
        <td>00004</td>
        <td><span class="status in-production">In Production</span></td>
        <td>Jonathan Barker</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr data-open-modal="orderModal">
        <td>00005</td>
        <td><span class="status processing">Processing</span></td>
        <td>Jason Price</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr data-open-modal="orderModal">
        <td>00006</td>
        <td><span class="status completed">Completed</span></td>
        <td>Jason Price</td>
        <td>12/09/2019</td>
        <td>20h05m</td>
      </tr>
      <tr data-open-modal="orderModal">
        <td>00007</td>
        <td><span class="status processing">Processing</span></td>
        <td>Jonathan Barker</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr data-open-modal="orderModal">
        <td>00008</td>
        <td><span class="status in-production">In Production</span></td>
        <td>Jason Price</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
      <tr data-open-modal="orderModal">
        <td>00009</td>
        <td><span class="status in-production">In Production</span></td>
        <td>Duane Dean</td>
        <td>12/09/2019</td>
        <td>2h05m</td>
      </tr>
    </tbody>
  </table>

  <x-modal id="orderModal" title="Order #00001">
    <span class="status completed">Completed</span>
    <div class="orderModal-flex">
      <div class="activity-log">
        <h2>Activity Log</h2>
        <ul>
          <li class="log-entry">
            <span class="log-desc">Danielle scanned Order #00002 to Workstation #1</span>
            <span class="log-date">Apr 16 at 3:28 pm</span>
          </li>
          <li class="log-entry">
            <span class="log-desc">Dora changed status of Order #00002 to Issue with Print</span>
            <span class="log-date">Apr 16 at 3:28 pm</span>
          </li>
          <li class="log-entry">
            <span class="log-desc">Sarah added new print layout to Order #00002</span>
            <span class="log-date">Apr 17 at 9:45 am</span>
          </li>
          <li class="log-entry">
            <span class="log-desc">Emily moved Order #00002 to Quality Check</span>
            <span class="log-date">Apr 17 at 1:10 pm</span>
          </li>
          <li class="log-entry">
            <span class="log-desc">Danielle reassigned Order #00002 to Workstation #3 for reprinting</span>
            <span class="log-date">Apr 18 at 11:30 am</span>
          </li>
          <li class="log-entry">
            <span class="log-desc">Danielle reassigned Order #00002 to Workstation #3 for reprinting</span>
            <span class="log-date">Apr 18 at 11:30 am</span>
          </li>
        </ul>
      </div>
      <div class="comments">
        <h2>Comments</h2>
        <div class="comment">
          <div class="comment-body">
            <span class="comment-user" data-initial="D">Danielle</span>
            <span class="comment-date">Apr 16 at 3:29 pm</span>
            <p class="comment-text">I noticed a slight color mismatch in the print. I'll recheck the printer settings.</p>
          </div>
          <div class="comment-footer">
            <button class="btn">Reply
          </div>
        </div>
        <div class="comment">
          <div class="comment-body">
            <span class="comment-user" data-initial="D">Dora</span>
            <span class="comment-date">Apr 16 at 3:29 pm</span>
            <p class="comment-text">The album cover material seems off. Need to confirm with the client.</p>
          </div>
          <div class="comment-footer">
            <button class="btn">Reply
          </div>
        </div>
        <div class="comment">
          <div class="comment-body">
            <span class="comment-user" data-initial="S">Sarah</span>
            <span class="comment-date">Apr 17 at 9:45 am</span>
            <p class="comment-text">Added new layout design based on client's feedback. Looks good!</p>
          </div>
          <div class="comment-footer">
            <button class="btn">Reply
          </div>
        </div>

      </div>
      <x-slot name="footer">
        <button class="btn pdf-btn">
          <img src="{{ asset('icons/pdf.png') }}" alt="PDF">View Order
        </button>
        <div class="new-comment">
          <textarea placeholder="Write a message..."></textarea>
          <button class="msg-btn">
            <img src="{{ asset('icons/attach.png') }}" alt="Attach file">
          </button>
          <button class="msg-btn">
            <img src="{{ asset('icons/media.png') }}" alt="Media">
          </button>
          <button class="send-btn">
            Send
            <img src="{{ asset('icons/send.png') }}" alt="Send">
          </button>
        </div>
      </x-slot>
    </div>
  </x-modal>
  <!-- @include('partials.footer', ['from' => 1, 'to' => 9, 'total' => 78]) -->
</div>
@endsection