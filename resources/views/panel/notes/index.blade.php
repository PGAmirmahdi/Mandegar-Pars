@extends('panel.layouts.master')
@section('title', 'یادداشت ها')

@section('styles')
    <link rel="stylesheet" href="/assets/css/notes-styles.css">
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>یادداشت ها</h6>
                @can('notes-create')
                    <button class="btn btn-primary" id="btn_add">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد یادداشت
                    </button>
                @endcan
            </div>
            <div class="row" id="list">
                @foreach($notes as $note)
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mt-3">
                        <div class="paper">
                            <span class="btn-remove">&times;</span>
                            <div class="lines">
                                <input type="text" name="note-title" class="title" value="{{ $note->title }}" data-id="{{ $note->id }}" maxlength="30" placeholder="عنوان یادداشت">
                                <textarea class="text" name="note-text" spellcheck="false" placeholder="متن یادداشت...">{{ $note->text }}</textarea>
                                <div class="loading d-none">
                                    درحال ذخیره سازی ...
                                </div>
                            </div>
                            <div class="holes hole-top"></div>
                            <div class="holes hole-middle"></div>
                            <div class="holes hole-bottom"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-5">{{ $notes->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            // add note card
            $(document).on('click', '#btn_add', function () {
                $('#list').prepend(`<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mt-3">
                        <div class="paper">
                            <span class="btn-remove">&times;</span>
                            <div class="lines">
                                <input type="text" name="note-title" class="title" data-id="" maxlength="30" placeholder="عنوان یادداشت">
                                <textarea class="text" name="note-text" spellcheck="false" placeholder="متن یادداشت..."></textarea>
                                <div class="loading d-none">
                                    درحال ذخیره سازی ...
                                </div>
                            </div>
                            <div class="holes hole-top"></div>
                            <div class="holes hole-middle"></div>
                            <div class="holes hole-bottom"></div>
                        </div>
                    </div>`)

                $(this).attr('disabled', 'disabled')
            })
            // end add note card

            let timeout;
            // save title and text
            $(document).on('keyup', 'input[name="note-title"]', function () {
                let item = $(this);
                item.siblings('.loading').removeClass('d-none')

                clearTimeout(timeout)

                timeout = setTimeout(function () {
                    let title = item.val()
                    let text = item.siblings(':first').val()
                    let note_id = item.data('id')

                    $.ajax({
                        url: "{{ route('notes.store') }}",
                        type: 'post',
                        data: {
                            title,
                            text,
                            note_id
                        },
                        success: function (res) {
                            item.data('id', res.id)
                            item.siblings('.loading').addClass('d-none')
                            $('#btn_add').removeAttr('disabled')
                        }
                    })
                }, 1500)
            })

            $(document).on('keyup', 'textarea[name="note-text"]', function () {
                let item = $(this);
                item.siblings('.loading').removeClass('d-none')

                clearTimeout(timeout)

                timeout = setTimeout(function () {
                    let title = item.siblings(':first').val()
                    let text = item.val()
                    let note_id = item.siblings(':first').data('id')

                    $.ajax({
                        url: "{{ route('notes.store') }}",
                        type: 'post',
                        data: {
                            title,
                            text,
                            note_id
                        },
                        success: function (res) {
                            item.siblings(':first').data('id', res.id)
                            item.siblings('.loading').addClass('d-none')
                            $('#btn_add').removeAttr('disabled')
                        }
                    })
                }, 1500)
            })
            // end title and text

            // btn remove
            $(document).on('click', '.btn-remove', function () {
                let self = $(this)
                self.addClass('confirm-delete')
                self.css('width','100%').css('border-radius','1px').css('transition','0.5s').text('حذف')

                setTimeout(function () {
                    if(!self.hasClass('deleting')){
                        self.removeClass('confirm-delete')
                        self.css('width','30px').css('border-radius','15px 0 0 15px').css('transition','0.5s').text('×')
                    }
                },3000)
            })

            $(document).on('click', '.confirm-delete', function () {
                let self = $(this)
                let note_id = self.siblings('.lines').children('.title').data('id')

                self.addClass('deleting')
                self.css('width','100%').css('border-radius','1px').css('transition','0.5s').css('pointer-events','none').text('درحال حذف...')

                if (note_id){
                    $.ajax({
                        url: "{{ route('notes.destroy') }}",
                        type: 'post',
                        data: {
                            note_id
                        },
                        success: function (res) {
                            self.parent().parent().remove()
                        }
                    })
                }else {
                    self.parent().parent().remove()
                    $('#btn_add').removeAttr('disabled')
                }
            })
            // end btn remove
        })
    </script>
@endsection
