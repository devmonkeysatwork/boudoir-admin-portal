<div class="modal" id="{{ $id }}" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('{{ $id }}').style.display='none'">&times;</span>
        <h2>{{ $title }}</h2>
        <div class="modal-body">
            {{ $slot }}
        </div>
        <div class="modal-footer">
            {{ $footer }}
        </div>
    </div>
</div>