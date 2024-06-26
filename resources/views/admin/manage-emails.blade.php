@extends('layouts.app')

@section('content')
<div class="settings">
  <h1>Settings</h1>
  <div class="settings-menu">
    <ul>
      <li>
        <a href="{{ route('admin.manage-statuses') }}">
          <img src="{{ asset('icons/statuses.png') }}" alt="Status Icon">Manage Statuses
        </a>
      </li>
      <li>
        <a href="{{ route('admin.manage-emails') }}" class="active">
          <img src="{{ asset('icons/email.png') }}" alt="Email Icon">Manage Emails
        </a>
      </li>
    </ul>
  </div>
  <div class="settings-content">
    <div class="manage-top">
      <h2>Manage Emails</h2>
      <button class="create-btn">+ Create New Email Template</button>
    </div>
    <div class="manage-btm">
      <table>
        <thead>
          <tr>
            <th>Template</th>
            <th>Associated Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Order Received</td>
            <td>Not Started</td>
            <td>
              <div class="actions">
                <label class="switch">
                  <input type="checkbox" checked>
                  <span class="slider round"></span>
                </label>
                <button class="edit-btn">
                  <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
                </button>
                <button class="delete-btn">
                  <img src="{{ asset('icons/delete.png') }}" alt="Delete Icon">
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>Order In Production</td>
            <td>In Production</td>
            <td>
              <div class="actions">
                <label class="switch">
                  <input type="checkbox">
                  <span class="slider round"></span>
                </label>
                <button class="edit-btn">
                  <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
                </button>
                <button class="delete-btn">
                  <img src="{{ asset('icons/delete.png') }}" alt="Delete Icon">
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    @include('partials.footer', ['from' => 1, 'to' => 5, 'total' => 5])
  </div>
</div>
@endsection