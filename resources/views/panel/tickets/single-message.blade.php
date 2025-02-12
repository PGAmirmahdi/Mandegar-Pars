@if($message->user_id == auth()->id())
    <div class="message-item {{ $message->file ? 'message-item-media' : '' }}">
        {{ $message->text }}
        @includeWhen($message->file, 'panel.partials.file-message')
        <small class="message-item-date text-muted">
            {{ verta($message->created_at)->format('H:i - Y/m/d') }}
            @if($message->read_at)
                <i class="fa fa-check-double"></i>
            @else
                <i class="fa fa-check"></i>
            @endif
        </small>
    </div>
@else
    <div class="message-item outgoing-message {{ $message->file ? 'message-item-media' : '' }}">
        {{ $message->text }}
        @includeWhen($message->file, 'panel.partials.file-message')
        <small class="message-item-date text-muted">
            {{ verta($message->created_at)->format('H:i - Y/m/d') }}
        </small>
    </div>
@endif
