@php use App\Models\Customer;use App\Models\Product;use App\Models\Province;use App\Models\invoice @endphp
@extends('panel.layouts.master')
@section('title', 'ثبت حمل و نقل')
@section('styles')
    <style>
        #products_table input, #products_table select {
            width: auto;
        }

        #other_products_table input, #other_products_table select {
            width: auto;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="col-12 mb-4 text-center">
                <h4>مشخصات خریدار</h4>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                <label for="buyer_name">نام شخص حقیقی/حقوقی <span class="text-danger">*</span></label>
                <select name="buyer_name" id="buyer_name"
                        class="js-example-basic-single select2-hidden-accessible" data-select2-id="5"
                        tabindex="-2" aria-hidden="true">
                    <option value="" disabled selected>انتخاب کنید</option>
                    @foreach(Invoice::all(['id','name','code']) as $customer)
                        <option
                            value="{{ $customer->id }}" {{ old('buyer_name') == $customer->id ? 'selected' : '' }}>{{ $customer->code.' - '.$customer->name }}</option>
                    @endforeach
                </select>
                @error('buyer_name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                <label for="customer">نام مشتری<span class="text-danger">*</span></label>
                <input name="customer" id="customer" class="form-control" readonly>
                @error('customer')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                <label for="address">نشانی<span class="text-danger">*</span></label>
                <textarea name="address" id="address" class="form-control" readonly>{{ old('address') }}</textarea>
                @error('address')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 mt-2 text-center">
                <h5>حمل و نقل کننده ها</h5>
            </div>
            <div class="col-12 mb-3">
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-outline-success" type="button" id="btn_add"><i
                            class="fa fa-plus mr-2"></i> افزودن حمل و نقل کننده
                    </button>
                </div>
                <div class="overflow-auto">
                    <table class="table table-bordered table-striped text-center" id="products_table">
                        <thead>
                        <tr>
                            <th>حمل و نقل کننده</th>
                            <th>مبلغ</th>
                            <th>نوع پرداختی</th>
                            <th>حذف</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(old('transporters'))
                            @foreach(old('transporters') as $i => $transporterId)
                                <tr>
                                    <td>
                                        <select class="js-example-basic-single" name="transporters[]" required>
                                            <option value="" disabled selected>انتخاب کنید
                                            </option>
                                            @foreach(Product::all(['id','title','code']) as $item)
                                                <option
                                                    value="{{ $item->id }}" {{ $item->id == $transporterId ? 'selected' : '' }}>{{ $item->code.' - '.$item->title }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="prices[]" class="form-control" min="0"
                                               value="{{ old('prices')[$i] }}" readonly>
                                        <div id="formatted-price-{{ $i }}" class="formatted-price"></div>
                                    </td>
                                    <td>
                                        <select class="form-control" name="payment_type[]">
                                            @foreach(App\Models\Transport::Payment_Type as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <button class="btn btn-primary" type="submit" id="btn_form">ثبت فرم</button>
        <div class="card chat-app-wrapper">
            <div class="row chat-app">
                <div class="col-xl-12 col-md-12 chat-body">
                    <div class="chat-body-header">
                        <div>
                            <h6 class="mb-1 primary-font line-height-18">
                                حمل و نقل سفارش {{ $invoice->id }} - {{ $invoice->customer->name }}
                            </h6>
                        </div>
                        <div class="ml-auto d-flex">
                            <div class="mr-4">
                                @if($transport->status == 'payment_incomplete')
                                    <span class="badge badge-warning">درحال گفت و گو</span>
                                @elseif($transport->status == 'payment_complete')
                                    <span class="badge badge-success">پرداخت شده!</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="chat-body-messages">
                        <div class="message-items">
                            @foreach ($comments as $comment)
                                <div class="message-item {{ $comment->user_id == auth()->id() ? '' : 'outgoing-message' }}">
                                    <!-- نمایش تصویر پروفایل -->
                                    <figure class="avatar avatar-sm m-r-10">
                                        <img
                                            src="{{ $comment->user->profile ? asset('storage/'.$comment->user->profile) : asset('assets/media/image/avatar.png') }}"
                                            class="rounded-circle" alt="profile">
                                    </figure>
                                    <strong>{{ $comment->user->name }}:</strong>
                                    <p>{{ $comment->comment }}</p>
                                    <small class="message-item-date text-muted">
                                        {{ verta($comment->created_at)->format('H:i - Y/m/d') }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="chat-body-footer">
                        @if($transport->status == 'payment_incomplete')
                            <form action="{{ route('buy-orders.comments.store', $transport->id) }}" method="post"
                                  class="d-flex align-items-center">
                                @csrf
                                <input type="text" name="comment" class="form-control" placeholder="نظر خود را وارد کنید..."
                                       required>
                                <button type="submit" class="ml-3 btn btn-primary btn-floating">
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            </form>
                        @elseif($transport->status == 'payment_complete')
                            <form action="#" method="post"
                                  class="d-flex align-items-center">
                                @csrf
                                <input type="text" name="comment" class="form-control" placeholder="امکان ارسال نظر هنگامی که وضعیت سفارش خریداری شده باشد وجود ندارد"
                                       required disabled>
                                <button type="submit" class="ml-3 btn btn-primary btn-floating disabled" disabled>
                                    <i class="fa fa-paper-plane disabled" disabled></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.js-example-basic-single').select2(); // تنظیم انتخابگر select2 برای انتخاب محصولات
        })
        ;
        $(document).on('change', 'select[name="buyer_name"]', function () {
            let invoice_id = this.value;

            $.ajax({
                url: '/panel/get-invoice-info/' + invoice_id,
                type: 'post',
                success: function (res) {
                    $('#name').val(res.data.name);
                    $('#address').val(res.data.address1);
                }
            });
        })

    </script>
@endsection


