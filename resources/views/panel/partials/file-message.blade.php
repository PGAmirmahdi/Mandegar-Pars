@php
    $file = json_decode($message->file);
@endphp
@if(in_array($file->type, ['jpg','jpeg','png','webp','svg','gif']))
    <ul class="w-100">
        <li class="flex-column justify-content-center align-items-center w-100">
            <a href="{{ $file->path }}">
                <img src="{{ $file->path }}" alt="image" class="w-100 p-2">
                <span>{{ $file->name }}</span>
            </a>
        </li>
    </ul>
@else
    <div class="m-b-0 text-muted text-left media-file">
        <a href="{{ $file->path }}" class="btn btn-outline-light row text-left align-items-center justify-content-center h-50 w-100" download="{{ $file->path }}">
            <i class="fa fa-download font-size-18 m-r-10"></i>
            <div class="small">
                <div class="mb-2">{{ $file->name }}</div>
                <div class="font-size-13" dir="ltr">{{ formatBytes($file->size) }}</div>
            </div>
        </a>
    </div>
@endif
