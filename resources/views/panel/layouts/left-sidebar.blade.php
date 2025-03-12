<!-- begin::sidebar user profile -->
<div class="sidebar" id="userProfile">
    <div class="text-center p-4">
        <figure class="avatar avatar-state-success avatar-lg mb-4">
            @if(auth()->user()->profile)
                <img
                    src="{{ auth()->user()->profile }}"
                    style="max-width: 76.79px"
                    data-toggle="tooltip" data-placement="bottom"
                    title="{{ auth()->user()->fullName() }}"
                    class="rounded-circle" alt="image">
            @elseif(auth()->user()->gender == 'female')
                <img src="{{ asset('assets/media/image/Female.png') }}"
                     data-toggle="tooltip" data-placement="bottom"
                     title="{{ auth()->user()->fullName() }}"
                     class="rounded-circle" alt="image">
            @elseif(auth()->user()->gender == 'male')
                <img src="{{ asset('assets/media/image/Male.png') }}"
                     data-toggle="tooltip" data-placement="bottom"
                     title="{{ auth()->user()->fullName() }}"
                     class="rounded-circle" alt="image">
            @else
                <img src="{{ asset('assets/media/image/inquery.png') }}"
                     data-toggle="tooltip" data-placement="bottom"
                     title="{{ auth()->user()->fullName() }}"
                     class="rounded-circle" alt="image">
            @endif
        </figure>
        <h4 class="text-primary m-b-10">{{auth()->user()->fullName()}}</h4>
        <p class="text-muted d-flex align-items-center justify-content-center line-height-0 mb-0">
            {{auth()->user()->role->label}}
        </p>
    </div>
    <hr class="m-0">
    <div class="p-4">
        @php
            $totalSales = \App\Models\Invoice::where('user_id', auth()->user()->id)
                            ->get()
                            ->sum('total_price');
        @endphp
        <div class="card bg-youtube">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fa fa-bar-chart font-size-30 opacity-7"></i>
                    <div class="m-l-20">
                        @if(in_array(auth()->user()->role->name,['accountant']))
                            <h5 class="mb-0 font-weight-bold primary-font">{{\App\Models\Invoice::where('req_for','pre-invoice')->count()}}</h5>
                            <small>پیش فاکتور های دریافت شده</small>
                            @elseif(in_array(auth()->user()->role->name,['inventory-manager','warehouse-keeper','exit-door']))
                            <h5 class="mb-0 font-weight-bold primary-font">{{\App\Models\Inventory::all()->sum('current_count')}}</h5>
                            <small>تعداد کالا های در انبار</small>
                        @elseif(in_array(auth()->user()->role->name,['ceo','office-manager']))
                            <h5 class="mb-0 font-weight-bold primary-font">{{number_format(\App\Models\Invoice::all()->sum('total_price'))}}</h5>
                            <small>فروش پرسنل</small>
                        @else
                            <h5 class="mb-0 font-weight-bold primary-font">{{ number_format($totalSales) }} (ریال)</h5>
                            <small>فروش شما</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-facebook">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fa fa-envelope-open font-size-30 opacity-7"></i>
                    <div class="m-l-20">
                        @if(in_array(auth()->user()->role->name,['accountant','inventory-manager','warehouse-keeper','exit-door']))
                            <h5 class="mb-0 font-weight-bold primary-font">{{ \App\Models\Ticket::where('receiver_id',auth()->user()->id)->count() }}</h5>
                            <small>تیکت های دریافتی</small>
                        @elseif(in_array(auth()->user()->role->name,['ceo','office-manager']))
                            <h5 class="mb-0 font-weight-bold primary-font">{{ \App\Models\Ticket::all()->count() }}</h5>
                            <small>پیامک های ارسال شده همکاران</small>
                        @else
                            <h5 class="mb-0 font-weight-bold primary-font">{{ \App\Models\Sms::where('user_id',auth()->user()->id)->count() }}</h5>
                            <small>پیامک های ارسالی شما</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-whatsapp">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fa fa-users font-size-30 opacity-7"></i>
                    <div class="m-l-20">
                        @if(in_array(auth()->user()->role->name,['accountant']))
                            <h5 class="mb-0 font-weight-bold primary-font">{{\App\Models\Invoice::where('status','invoiced')->count()}}</h5>
                            <small>فاکتور های دریافت شده</small>
                            @elseif(in_array(auth()->user()->role->name,['inventory-manager','warehouse-keeper','exit-door']))
                            <h5 class="mb-0 font-weight-bold primary-font">{{\App\Models\InventoryReport::where('type','output')->count()}} خروج و {{\App\Models\InventoryReport::where('type','input')->count()}} ورود </h5>
                            <small>خروج و ورود های انبار</small>
                            @elseif(in_array(auth()->user()->role->name,['ceo','office-manager']))
                            <h5 class="mb-0 font-weight-bold primary-font">{{\App\Models\Customer::all()->count()}}</h5>
                            <small>مشتریان شرکت</small>
                        @else
                            <h5 class="mb-0 font-weight-bold primary-font">{{ \App\Models\Customer::where('user_id',auth()->user()->id)->count() }}</h5>
                            <small>مشتریان شما</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{--        <div class="mb-4">--}}
        {{--            <h6 class="font-size-13 mb-3 pt-2">درباره</h6>--}}
        {{--            <p class="text-muted">لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان--}}
        {{--                گرافیک</p>--}}
        {{--        </div>--}}
        <div class="mb-4">
            <h6 class="font-size-13 mb-3">شماره تلفن:</h6>
            <p class="text-muted">{{auth()->user()->phone}}</p>
        </div>
        <div class="mb-4">
            <h6 class="font-size-13 mb-3">شبکه های اجتماعی</h6>
            <ul class="list-inline mb-4">
                <li class="list-inline-item">
                    <a href="https://www.artintoner.com" class="btn btn-sm btn-floating btn-facebook">
                        <i class="fa-brands fa-square-steam"></i>
                    </a>
                </li>
                <li class="list-inline-item">
                    <a href="https://www.app.mpsystem.ir/pwa" class="btn btn-sm btn-floating btn-primary">
                        <i class="fa-brands fa-app-store"></i>
                    </a>
                </li>
                <li class="list-inline-item">
                    <a href="https://web.whatsapp.com" class="btn btn-sm btn-floating btn-whatsapp">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="mb-4">
            <h6 class="font-size-13 mb-3">تنظیمات</h6>
            <div class="mb-4">
                <h6 class="font-size-13 mb-3">رنگ تم:</h6>
                <button class="theme-btn btn btn-floating" style="background-color: #3498db" value="#3498db">
                </button>
                <button class="theme-btn btn btn-floating" value="#e74c3c" style="background-color: #e74c3c">
                </button>
                <button class="theme-btn btn btn-floating" value="#27ae60" style="background-color: #27ae60">
                </button>
                <button class="theme-btn btn btn-floating" value="#f1c40f" style="background-color: #f1c40f">
                </button>
                <button class="theme-btn btn btn-floating" value="#9b59b6" style="background-color: #9b59b6">
                </button>
            </div>
            <div class="mb-4">
                <select id="fontSelect" class="form-control">
                    <option value="" disabled>فونت پنل</option>
                    <option value="secondary-font">یکان(قالب پیش فرض)</option>
                    <option value="secondary-font-vazir">وزیر</option>
                    <option value="secondary-font-dana">دانا</option>
                    <option value="secondary-font-azarmehr">آذرمهر</option>
                    <option value="secondary-font-aviny">آوینی</option>
                    <option value="secondary-font-vanda">واندا</option>
                    <option value="secondary-font-shabnam">شبنم</option>
                    <option value="secondary-font-sahel">ساحل</option>
                    <option value="secondary-font-pinar">پینار</option>
                    <option value="secondary-font-palatino-sans">پالاتینو</option>
                    <option value="secondary-font-myriad">مای ریاد</option>
                    <option value="secondary-font-mikhak">میخک</option>
                    <option value="secondary-font-lalezar">لاله زار</option>
                    <option value="secondary-font-helvetica-nue">هلوتیکا</option>
                    <option value="secondary-font-estedad">استعداد</option>
                    <option value="secondary-font-dubai">دبی</option>
                    <option value="secondary-font-dastnevis"> دست نویس</option>
                    <option value="secondary-font-damavand">دماوند</option>
                </select>
            </div>
{{--            <div class="form-group">--}}
{{--                <div class="form-item custom-control custom-switch">--}}
{{--                    <input type="checkbox" class="custom-control-input" id="customSwitch11">--}}
{{--                    <label class="custom-control-label" for="customSwitch11">بی صدا کردن</label>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="form-group">--}}
{{--                <div class="form-item custom-control custom-switch">--}}
{{--                    <input type="checkbox" class="custom-control-input" id="customSwitch12">--}}
{{--                    <label class="custom-control-label" for="customSwitch12">مسدود کردن</label>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
    </div>
</div>
<script>
    document.getElementById('fontSelect').addEventListener('change', function() {
        var selectedFont = this.value;
        document.body.style.fontFamily = selectedFont;
        localStorage.setItem('selectedFont', selectedFont);
    });

    // اعمال فونت ذخیره شده در بارگذاری صفحه
    document.addEventListener('DOMContentLoaded', function() {
        var savedFont = localStorage.getItem('selectedFont');
        if (savedFont) {
            document.body.style.fontFamily = savedFont;
            document.getElementById('fontSelect').value = savedFont;
        }
    });
    // ثبت رویداد کلیک روی هر دکمه برای تغییر رنگ تم
    document.querySelectorAll('.theme-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var selectedColor = this.value;
            // تغییر متغیر CSS مربوط به رنگ اصلی
            document.documentElement.style.setProperty('--primary-color', selectedColor);
            // ذخیره رنگ انتخاب شده در localStorage
            localStorage.setItem('primaryColor', selectedColor);
        });
    });

    // اعمال رنگ ذخیره‌شده هنگام بارگذاری صفحه
    document.addEventListener('DOMContentLoaded', function() {
        var savedColor = localStorage.getItem('primaryColor');
        if (savedColor) {
            document.documentElement.style.setProperty('--primary-color', savedColor);
        }
    });
</script>
<!-- end::sidebar user profile -->
