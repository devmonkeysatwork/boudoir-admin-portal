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
        @foreach($templates??[] as $template)
          <tr>
            <td>{{$template->name}}</td>
            <td>{{$template->for_status?->status_name}}</td>
            <td>
              <div class="actions">
                <label class="switch">
                  <input type="checkbox" id="status_{{$template->id}}" value="1" {{$template->status==1?'checked':''}} onchange="updateStatus({{$template->id}},this)">
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
        @endforeach
        </tbody>
      </table>

      <x-modal id="createEmailModal" title="Create New Template">
        <form id="emailTemplateForm">
          <div class="second-top">
            <div class="top">
              <div class="form-group name">
                <label for="template-name">Template Name:</label>
                <input type="text" id="template-name" name="name">
              </div>
              <div class="form-group status">
                <label for="associated-status">Associated Status:</label>
                <select id="associated-status" name="status_id">
                  <option value="" disabled selected>Select an option</option>
                    @foreach($statuses??[] as $status)
                        <option value="{{$status->id}}">{{$status->status_name}}</option>
                    @endforeach
                </select>
              </div>
            </div>
            <div class="form-group subject">
              <label for="subject">Subject:</label>
              <input type="text" id="subject" name="subject">
            </div>
          </div>
          <div class="form-group text">
            <textarea id="email-content" name="email_content"></textarea>
          </div>
          <div class="form-group btn-group">
            <button type="button" class="btn save-btn" id="save_template">Save Template</button>
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
                    @foreach($statuses??[] as $status)
                        <option value="{{$status->id}}">{{$status->status_name}}</option>
                    @endforeach
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
        </form>
      </x-modal>

    </div>
    @include('partials.footer')
  </div>
</div>
@endsection
@section('footer_scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $('#save_template').click(function(){
                let data = new FormData($('#emailTemplateForm')[0]);
                data.append('_token', '{{@csrf_token()}}');
                data.append('content', CKEDITOR.instances['email-content'].getData());
                $.ajax({
                    type: 'post',
                    processData: false,
                    contentType: false,
                    cache: false,
                    url: '{{route('email.add')}}',
                    data: data,
                    beforeSend() {
                        show_loader();
                    },
                    complete: function (response) {
                        hide_loader();

                    },
                    success: function (response) {
                        if (response.status == 200) {
                            show_toast(response.message, 'success');
                            window.location.reload();

                        } else {
                            show_toast(response.message, 'error');
                        }

                    },
                    error: function (response) {
                    }
                })
            });
        });

        function updateStatus(id,input){
            let data = new FormData();
            data.append('_token', '{{@csrf_token()}}');
            data.append('id', id);
            data.append('status', $(input).is(":checked"));
            $.ajax({
                type: 'post',
                processData: false,
                contentType: false,
                cache: false,
                url: '{{route('email.update_status')}}',
                data: data,
                beforeSend() {
                    show_loader();
                },
                complete: function (response) {
                    hide_loader();

                },
                success: function (response) {
                    if (response.status == 200) {
                        show_toast(response.message, 'success');

                    } else {
                        show_toast(response.message, 'error');
                    }

                },
                error: function (response) {
                }
            })
        }


    </script>
@endsection
