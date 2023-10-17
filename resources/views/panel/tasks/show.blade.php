@extends('panel.layouts.master')
@section('title', 'مشاهده وظیفه')
@section('content')
    @php
        $isCreator = $task->creator_id == auth()->id();
        if (!$isCreator){
            $task_done = \Illuminate\Support\Facades\DB::table('task_user')->where(['task_id' => $task->id, 'user_id' => auth()->id()])->first()->status == 'done' ? true : false;
        }
    @endphp
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>مشاهده وظیفه "{{ $task->title }}"</h6>
                @if(!$isCreator)
                    <div class="custom-control custom-switch custom-control-inline {{ $isCreator ? 'd-none' : '' }}">
                        <input type="checkbox" class="custom-control-input" id="btn_task" {{ $task_done ? 'checked' : '' }}>
                        <label class="custom-control-label" for="btn_task" id="btn_task_lbl">{{ $task_done ? 'انجام شده' : 'انجام نشده' }}</label>
                    </div>
                @endif
            </div>
            @if(!$isCreator)
                <div class="row">
                    <div class="col">
                        <strong>توضیحات</strong>
                        <p>{{ $task->description }}</p>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col">
                        sad
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var task_id = "{{ $task->id }}";

        $(document).ready(function () {
            // btn task status
            $(document).on('change', '#btn_task', function () {
                $(this).attr('disabled','disabled')

                $.ajax({
                    url: `/panel/task/change-status`,
                    type: 'post',
                    data: {
                        task_id
                    },
                    success: function (res) {
                        $('#btn_task_lbl').text(res.message)
                        $('#btn_task').removeAttr('disabled')
                    }
                })
            })
            // end btn task status
        })
    </script>
@endsection


