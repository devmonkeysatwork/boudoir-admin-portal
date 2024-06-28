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
      <button class="create-btn" data-open-modal="createStatusModal">+ Create New Status</button>
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
              <button class="edit-btn" data-open-modal="editStatusModal">
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
              <button class="edit-btn" data-open-modal="editStatusModal">
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
              <button class="edit-btn" data-open-modal="editStatusModal">
                <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
              </button>
              <button class="delete-btn">
                <img src="{{ asset('icons/delete.png') }}" alt="Delete Icon">
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <x-modal id="createStatusModal" title="Create New Status">
        <form>
          <div class="form-group name">
            <label for="status-name">Status Name</label>
            <input type="text" id="status-name" name="status-name">
            <div class="form-group">
              <label for="preview">Preview</label>
              <span class="status processing">Processing</span>
            </div>
          </div>
          <div class="form-group color">
            <label for="status-color">Pick a Color</label>
            <input type="color" id="status-color" name="status-color">
          </div>
        </form>
        <x-slot name="footer">
          <div class="form-group">
            <button type="submit" class="btn">Save Status</button>
            <button type="button" class="btn cancel-btn" onclick="document.getElementById('createStatusModal').style.display='none'">Cancel</button>
          </div>
        </x-slot>
      </x-modal>

      <x-modal id="editStatusModal" title="Edit Status">
        <form>
          <div class="form-group name">
            <label for="edit-status-name">Edit Name</label>
            <input type="text" id="edit-status-name" name="edit-status-name" value="Processing">
            <div class="form-group">
              <label for="preview">Preview</label>
              <span class="status processing">Processing</span>
            </div>
          </div>
          <div class="form-group color">
            <label for="edit-status-color">Pick a Color</label>
            <input type="color" id="edit-status-color" name="edit-status-color" value="#007BFF">
          </div>
        </form>
        <x-slot name="footer">
          <div class="form-group">
            <button type="submit" class="btn">Save Status</button>
            <button type="button" class="btn cancel-btn" onclick="document.getElementById('createStatusModal').style.display='none'">Cancel</button>
          </div>
        </x-slot>
      </x-modal>

    </div>

    @include('partials.footer')
  </div>
</div>
@endsection