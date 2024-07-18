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
        @if(isset($statuses))
            <table>
        <thead>
          <tr>
            <th>Preview</th>
            <th>Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        @foreach($statuses as $status)
          <tr>
            <td><span class="status" style="background-color: {{$status->status_color}};">{{$status->status_name}}</span></td>
            <td>{{$status->status_name}}</td>
            <td class="actions">
              <button class="edit-btn" data-open-modal="editStatusModal">
                <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
              </button>
              <button class="delete-btn" onclick="deleteStatus({{$status->id}})">
                <img src="{{ asset('icons/delete.png') }}" alt="Delete Icon">
              </button>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
        @endif

      <x-modal id="createStatusModal" title="Create New Status">
        <form id="addStatusForm" action="javascript:void(0);" method="post">
          <div class="form-group name">
            <label for="status-name">Status Name</label>
            <input type="text" id="status-name" name="status-name">
            <div class="form-group preview">
              <label for="preview">Preview</label>
              <span class="status processing" id="create-preview">Processing</span>
            </div>
          </div>
          <div class="form-group color">
            <label for="status-color">Pick a Color</label>
            <div id="create-status-color-picker"></div>
            <input type="hidden" id="status-color" name="status-color" value="#007BFF">
          </div>
        </form>
        <x-slot name="footer">
          <div class="form-group buttons">
            <button type="submit" class="btn save-btn" onclick="addStatus()">Save Status</button>
            <button type="button" class="btn cancel-btn" onclick="document.getElementById('createStatusModal').style.display='none'">Cancel</button>
          </div>
        </x-slot>
      </x-modal>

    </div>

    @include('partials.footer')
  </div>
</div>
@endsection
@section('footer_scripts')
<script>
    function addStatus(){
        let data  = new FormData($('#addStatusForm')[0]);
        data.append('_token','{{@csrf_token()}}');
        $.ajax({
            type: 'post',
            processData: false,
            contentType: false,
            cache: false,
            url: '{{route('admin.add_status')}}',
            data: data,
            beforeSend() {
                show_loader();
            },
            complete: function (response) {
                hide_loader();

            },
            success: function (response) {
                if(response.status == 200){
                    show_toast(response.message,'success');
                    window.location.reload();

                }else{
                    show_toast(response.message,'error');
                }

            },
            error: function (response) {
            }
        })
    }

    function deleteStatus(statusId) {
        if (confirm("Are you sure you want to delete this status?")) {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.delete_status') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: statusId
                },
                beforeSend: function() {
                    show_loader();
                },
                success: function(response) {
                    hide_loader();
                    if (response.status == 200) {
                        show_toast(response.message, 'success');
                        window.location.reload(); // Example: Reload the page after successful deletion
                    } else {
                        show_toast(response.message, 'error');
                    }
                },
                error: function(response) {
                    hide_loader();
                    show_toast('Error deleting status.', 'error');
                }
            });
        }
    }

</script>
@endsection

