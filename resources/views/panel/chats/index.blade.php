@extends('panel.layouts.master')
@section('title', 'گفتگو')
@section('styles')
    <!-- lightbox -->
    <link rel="stylesheet" href="/vendors/lightbox/magnific-popup.css" type="text/css">
@endsection
@section('content')
    <div class="card chat-app-wrapper">
            <div class="row chat-app">
                <div class="col-xl-12 col-md-12 chat-body">
                    <div class="chat-body-header">
                        <a href="#" class="btn btn-dark opacity-3 m-r-10 btn-chat-sidebar-open">
                            <i class="ti-menu"></i>
                        </a>
                        <div>
                            <figure class="avatar avatar-sm m-r-10">
                                <img src="/assets/media/image/avatar.png" class="rounded-circle" alt="image">
                            </figure>
                        </div>
                        <div>
                            <h6 class="mb-1 primary-font line-height-18">بری الن</h6>
                            <span class="small text-success">در حال نوشتن ...</span>
                        </div>
                        <div class="ml-auto d-flex">
                            <button type="button" class="ml-2 btn btn-sm btn-success btn-floating">
                                <i class="fa fa-video-camera"></i>
                            </button>
                            <div class="dropdown ml-2">
                                <button type="button" data-toggle="dropdown" class="btn btn-sm  btn-warning btn-floating">
                                    <i class="fa fa-cog"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-menu-body">
                                        <ul>
                                            <li>
                                                <a class="dropdown-item" href="#">پروفایل</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">مسدود کردن</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">حذف</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-body-messages">
                        <div class="message-items">
                            <div class="message-item message-item-media">
                                <ul>
                                    <li>
                                        <a href="assets/media/image/portfolio-four.jpg">
                                            <img src="assets/media/image/portfolio-four.jpg" alt="image">
                                            <span>portfolio-four.jpg</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="assets/media/image/portfolio-two.jpg">
                                            <img src="assets/media/image/portfolio-two.jpg" alt="image">
                                            <span>portfolio-two.jpg</span>
                                        </a>
                                    </li>
                                </ul>
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item">
                                لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item message-item-media">
                                <div class="m-b-0 text-muted text-left">
                                    <a href="#" class="btn btn-outline-light text-left align-items-center justify-content-center">
                                        <i class="fa fa-download font-size-18 m-r-10"></i>
                                        <div class="small">
                                            <div class="mb-2">example test.txt</div>
                                            <div dir="ltr">10 KB</div>
                                        </div>
                                    </a>
                                </div>
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item outgoing-message">
                                لورم ایپسوم متن ساختگی با تولید
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item outgoing-message message-item-media">
                                <ul>
                                    <li>
                                        <a href="assets/media/image/portfolio-four.jpg" class="media-error">
                                            <img src="assets/media/image/portfolio-four.jpg" alt="image">
                                            <span>لورم ایپسوم متن ساختگی با تولید</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="assets/media/image/portfolio-six.jpg">
                                            <img src="assets/media/image/portfolio-six.jpg" alt="image">
                                            <span>portfolio-six.jpg</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="assets/media/image/portfolio-three.jpg" class="media-error">
                                            <img src="assets/media/image/portfolio-three.jpg" alt="image">
                                            <span>لورم ایپسوم متن ساختگی با تولید</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="assets/media/image/portfolio-one.jpg">
                                            <img src="assets/media/image/portfolio-one.jpg" alt="image">
                                            <span>portfolio-one.jpg</span>
                                        </a>
                                    </li>
                                </ul>
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item outgoing-message">
                                لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item">
                                لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item">
                                لورم ایپسوم متن
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item message-item-date-border">
                                <span class="badge">دیروز</span>
                            </div>
                            <div class="message-item">
                                لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item outgoing-message message-item-error">
                                لورم ایپسوم متن ساختگی
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item outgoing-message message-item-media">
                                <div class="m-b-0 text-muted text-left media-file">
                                    <a href="#" class="btn btn-outline-light text-left align-items-center justify-content-center">
                                        <i class="fa fa-download font-size-18 m-r-10"></i>
                                        <div class="small">
                                            <div class="mb-2">example file.txt</div>
                                            <div class="font-size-13" dir="ltr">5 KB</div>
                                        </div>
                                    </a>
                                </div>
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item">
                                لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده
                            </div>
                            <div class="message-item">
                                لورم ایپسوم متن
                            </div>
                            <div class="message-item message-item-date-border">
                                <span class="badge">امروز</span>
                            </div>
                            <div class="message-item">
                                لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت
                            </div>
                            <div class="message-item outgoing-message">
                                لورم ایپسوم متن ساختگی
                                <small class="message-item-date text-muted">22.30</small>
                            </div>
                            <div class="message-item">&bull; &bull; &bull;</div>
                        </div>
                    </div>
                    <div class="chat-body-footer">
                        <form class="d-flex align-items-center">
                            <input type="text" class="form-control" placeholder="پیام ...">
                            <div class="d-flex">
                                <button type="button" class="ml-3 btn btn-primary btn-floating">
                                    <i class="fa fa-send"></i>
                                </button>
                                <div class="dropup">
                                    <button type="button" data-toggle="dropdown" class="ml-3 btn btn-success btn-floating">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <div class="dropdown-menu-body">
                                            <ul>
                                                <li>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="icon fa fa-picture-o"></i> تصویر
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="icon fa fa-video-camera"></i> ویدئو
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
    <!-- begin::lightbox -->
    <script src="/vendors/lightbox/jquery.magnific-popup.min.js"></script>
    <script src="/assets/js/examples/lightbox.js"></script>
@endsection
