@extends('panel.layouts.master')

@section('title', 'جزئیات تامین کننده')

@section('styles')
    <style>
        .social_sec {
            font-size: large !important;
        }

        .social_sec a {
            margin: 0 10px !important;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>جزئیات تامین کننده</h6>
            </div>
            <form action="{{ route('suppliers.update', $supplier->id) }}" method="post">
                @csrf
                @method('PATCH')
                <input type="hidden" name="url" value="{{ $url }}">
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">نام سازمان/فروشگاه </label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $supplier->name }}"
                               readonly>
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="supplier_type">نوع</label>
                        <input type="text" name="supplier_type" class="form-control" id="supplier_type"
                               value="@if($supplier->supplier_type  == 'local')داخلی@elseif($supplier->supplier_type  == 'foreign')خارجی@endif"
                               readonly>
                        @error('supplier_type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="economical_number">شماره اقتصادی</label>
                        <input type="text" name="economical_number" class="form-control" id="economical_number"
                               value="{{ $supplier->economical_number }}" readonly>
                        @error('economical_number')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="national_number">شماره ثبت/ملی</label>
                        <input type="text" name="national_number" class="form-control" id="national_number"
                               value="{{ $supplier->national_number }}" readonly>
                        @error('national_number')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="postal_code">کد پستی</label>
                        <input type="text" name="postal_code" class="form-control" id="postal_code"
                               value="{{ $supplier->postal_code }}" readonly>
                        @error('postal_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="province">استان</label>
                        <input type="text" name="postal_code" class="form-control" id="postal_code"
                               value="{{ $supplier->province }}" readonly>
                        @error('province')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="city">شهر</label>
                        <input type="text" name="city" class="form-control" id="city" value="{{ $supplier->city }}"
                               readonly>
                        @error('city')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone1">شماره تماس 1</label>
                        <input type="text" name="phone1" class="form-control" id="phone1"
                               value="{{ $supplier->phone1 }}" readonly>
                        <div class="social_sec">
                            <a href="https://t.me/+98{{ $supplier->phone1 }}" target="_blank">
                                <i class="fa-brands fa-telegram text-info"></i>
                            </a>
                            <a href="https://wa.me/+98{{ $supplier->phone1 }}" target="_blank">
                                <i class="fa-brands fa-whatsapp text-success"></i>
                            </a>
                            <a href="tel:{{ $supplier->phone1 }}">
                                <i class="fa fa-phone-square text-success"></i>
                            </a>
                        </div>
                        @error('phone1')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone2">شماره تماس 2</label>
                        <input type="text" name="phone2" class="form-control" id="phone2"
                               value="{{ $supplier->phone2 }}" readonly>
                        @if($supplier->phone2)
                            <div class="social_sec">
                                <a href="https://t.me/+98{{ $supplier->phone2 }}" target="_blank">
                                    <i class="fa-brands fa-telegram text-info"></i>
                                </a>
                                <a href="https://wa.me/+98{{ $supplier->phone2 }}" target="_blank">
                                    <i class="fa-brands fa-whatsapp text-success"></i>
                                </a>
                                <a href="tel:{{ $supplier->phone2 }}">
                                    <i class="fa fa-phone-square text-success"></i>
                                </a>
                            </div>
                        @endif
                        @error('phone2')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="address1">آدرس 1</label>
                        <textarea readonly name="address1" id="address1"
                                  class="form-control">{{ $supplier->address1 }}</textarea>
                        @error('address1')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="address2">آدرس 2</label>
                        <textarea readonly name="address2" id="address2"
                                  class="form-control">{{ $supplier->address2 }}</textarea>
                        @error('address2')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea readonly name="description" id="description"
                                  class="form-control">{{ $supplier->description }}</textarea>
                        @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="categories">زمینه‌های فعالیت</label>
                        @if($categories->count())
                            <ul>
                                @foreach($categories as $category)
                                   <span class="badge badge-primary p-2 font-size-16"> + {{$category->name}}</span>
                                @endforeach
                            </ul>
                        @else
                            <p>تعیین نشده</p>
                        @endif
                    </div>

                </div>
                <a href="{{url()->previous()}}" class="btn btn-danger">بازگشت</a>
            </form>
        </div>
    </div>
@endsection
