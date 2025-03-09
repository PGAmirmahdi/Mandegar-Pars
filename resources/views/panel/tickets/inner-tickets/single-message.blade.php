@if($message->user_id == auth()->id())
    <div id="message-{{ $message->id }}" class="message-item {{ $message->file ? 'message-item-media' : '' }}">
        <div class="message-content">
            @if($message->text)
                <div class="message-text">{{ $message->text }}</div>
            @endif
            @includeWhen($message->file, 'panel.partials.file-message')
            <div class="message-meta">
                            <span class="message-time">
                                {{ verta($message->created_at)->format('H:i - Y/m/d') }}
                            </span>
                @if($message->read_at)
                    <i class="status-read fa fa-check-double"></i>
                @else
                    <i class="status-sent fa fa-check"></i>
                @endif
            </div>
        </div>
    </div>
@else
    <div id="message-{{ $message->id }}" class="message-item outgoing-message {{ $message->file ? 'message-item-media' : '' }}">
        @if($message->text)
            <div class="message-text @if($message->file) p-2 @endif">{{ $message->text }}</div>
        @endif
        @includeWhen($message->file, 'panel.partials.file-message')
        <div class="message-meta row @if($message->file) justify-content-center m-2 @else justify-content-between @endif px-2">
                            <span class="message-time">
                                {{ verta($message->created_at)->format('H:i - Y/m/d') }}
                            </span>
        </div>
    </div>
@endif
