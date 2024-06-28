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
          <div class="form-group">
            <label for="template-name">Template Name</label>
            <input type="text" id="template-name" name="template-name">
          </div>
          <div class="form-group">
            <label for="associated-status">Associated Status</label>
            <select id="associated-status" name="associated-status">
              <option value="">Select an option</option>
            </select>
          </div>
          <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject">
          </div>
          <div class="form-group">
            <label for="email-content">Content</label>
            <textarea id="email-content" name="email-content"></textarea>
          </div>
          <div class="form-group">
            <button type="submit" class="btn">Save Template</button>
            <button type="button" class="btn cancel-btn" onclick="document.getElementById('createEmailModal').style.display='none'">Cancel</button>
          </div>
        </form>
        <x-slot name="footer"></x-slot>
      </x-modal>

      <x-modal id="editEmailModal" title="Edit Template">
        <form>
          <div class="form-group">
            <label for="edit-template-name">Template Name</label>
            <input type="text" id="edit-template-name" name="edit-template-name" value="">
          </div>
          <div class="form-group">
            <label for="edit-associated-status">Associated Status</label>
            <select id="edit-associated-status" name="edit-associated-status">
              <option value=""></option>
            </select>
          </div>
          <div class="form-group">
            <label for="edit-subject">Subject</label>
            <input type="text" id="edit-subject" name="edit-subject" value="">
          </div>
          <div class="form-group">
            <label for="edit-email-content">Content</label>
            <textarea id="edit-email-content" name="edit-email-content"></textarea>
          </div>
          <div class="form-group">
            <button type="submit" class="btn">Save Template</button>
            <button type="button" class="btn cancel-btn" onclick="document.getElementById('editEmailModal').style.display='none'">Cancel</button>
          </div>
        </form>
        <x-slot name="footer"></x-slot>
      </x-modal>

    </div>
    @include('partials.footer')
  </div>
</div>
@endsection