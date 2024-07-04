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
      <button class="create-btn" data-open-modal="createEmailModal">+ Create New Email Template</button>
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
                <button class="edit-btn" data-open-modal="editEmailModal">
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
                <button class="edit-btn" data-open-modal="editEmailModal">
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

      <x-modal id="createEmailModal" title="Create New Template">
        <form>
          <div class="second-top">
            <div class="top">
              <div class="form-group name">
                <label for="template-name">Template Name:</label>
                <input type="text" id="template-name" name="template-name">
              </div>
              <div class="form-group status">
                <label for="associated-status">Associated Status:</label>
                <select id="associated-status" name="associated-status">
                  <option value="" disabled selected>Select an option</option>
                  <option value="processing">Processing</option>
                  <option value="in-production">In Production</option>
                  <option value="on-hold">On Hold</option>
                  <option value="completed">Completed</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
            </div>
            <div class="form-group subject">
              <label for="subject">Subject:</label>
              <input type="text" id="subject" name="subject">
            </div>
          </div>
          <div class="form-group text">
            <textarea id="email-content" name="email-content"></textarea>
          </div>
          <div class="form-group btn-group">
            <button type="submit" class="btn save-btn">Save Template</button>
            <button type="button" class="btn cancel-btn" onclick="document.getElementById('createEmailModal').style.display='none'">Cancel</button>
          </div>
        </form>
        <x-slot name="footer"></x-slot>
      </x-modal>

      <x-modal id="editEmailModal" title="Edit Template">
        <form>
          <div class="second-top">
            <div class="top">
              <div class="form-group name">
                <label for="template-name">Template Name:</label>
                <input type="text" id="template-name" name="template-name">
              </div>
              <div class="form-group status">
                <label for="associated-status">Associated Status:</label>
                <select id="associated-status" name="associated-status">
                  <option value="" disabled selected>Select an option</option>
                  <option value="processing">Processing</option>
                  <option value="in-production">In Production</option>
                  <option value="on-hold">On Hold</option>
                  <option value="completed">Completed</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
            </div>
            <div class="form-group subject">
              <label for="subject">Subject:</label>
              <input type="text" id="subject" name="subject">
            </div>
          </div>
          <div class="form-group text">
            <textarea id="edit-email-content" name="edit-email-content"></textarea>
          </div>
          <div class="form-group btn-group">
            <button type="submit" class="btn save-btn">Save Template</button>
            <button type="button" class="btn cancel-btn" onclick="document.getElementById('editEmailModal').style.display='none'">Cancel</button>
          </div>
          <x-slot name="footer"></x-slot>
      </x-modal>

    </div>
    @include('partials.footer')
  </div>
</div>
@endsection