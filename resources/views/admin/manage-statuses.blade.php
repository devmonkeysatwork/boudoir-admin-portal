@extends('layouts.app')

@section('content')
<div class="settings">
  <h1>Settings</h1>
  <div class="settings-menu">
    <ul>
      <li>
        <a href="{{ route('admin.manage-statuses') }}" class="active">
          <img src="{{ asset('icons/statuses.png') }}" alt="Status Icon">Manage Statuses
        </a>
      </li>
      <li>
        <a href="{{ route('admin.manage-emails') }}">
          <img src="{{ asset('icons/email.png') }}" alt="Email Icon">Manage Emails
        </a>
      </li>
    </ul>
  </div>
  <div class="settings-content">
    <div class="manage-top">
      <h2>Manage Statuses</h2>
      <button class="create-btn">+ Create New Status</button>
    </div>
    <div class="manage-btm">
      <table>
        <thead>
          <tr>
            <th>Preview</th>
            <th>Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="status not-started">Not Started</span></td>
            <td>Not Started</td>
            <td class="actions">
              <button class="edit-btn">
                <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
              </button>
              <button class="delete-btn">
                <img src="{{ asset('icons/delete.png') }}" alt="Delete Icon">
              </button>
            </td>
          </tr>
          <tr>
            <td><span class="status processing">Processing</span></td>
            <td>Processing</td>
            <td class="actions">
              <button class="edit-btn">
                <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
              </button>
              <button class="delete-btn">
                <img src="{{ asset('icons/delete.png') }}" alt="Delete Icon">
              </button>
            </td>
          </tr>
          <tr>
            <td><span class="status in-production">In Production</span></td>
            <td>In Production</td>
            <td class="actions">
              <button class="edit-btn">
                <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
              </button>
              <button class="delete-btn">
                <img src="{{ asset('icons/delete.png') }}" alt="Delete Icon">
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    @include('partials.footer', ['from' => 1, 'to' => 2, 'total' => 16])
  </div>
</div>
@endsection