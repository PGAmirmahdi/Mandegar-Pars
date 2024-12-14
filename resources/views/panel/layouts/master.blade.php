@include('panel.layouts.head')

{{--@include('panel.layouts.loader')--}}
@includeWhen(!isset($sidebar), 'panel.layouts.sidebar')
@includeWhen(!isset($header), 'panel.layouts.header')
@includeWhen(!isset($loader), 'panel.layouts.loader')
@includeWhen(!isset($loader), 'panel.layouts.internal-tels')

<!-- begin::main content -->
<main class="main-content">
    @include('sweet::alert')
    @yield('content')
</main>
<!-- end::main content -->

@include('panel.layouts.scripts')
@include('panel.layouts.footer')
