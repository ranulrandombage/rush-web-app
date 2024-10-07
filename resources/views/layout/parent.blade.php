<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>RS AUTO SPARES - @yield('title')</title>

        <meta content="RUSH web application is a copyright of RUSH Pvt Ltd" name="description">
        <meta content="RUSH" name="keywords">

        <!-- Favicons -->
        <link href="{{ asset('img/favicon.ico') }}" rel="icon">
        <link href="{{ asset('img/apple-touch-icon.png') }}" rel="apple-touch-icon">

        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('img/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('img/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('img/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-icon-180x180.png') }}">

        <!-- Google Fonts -->
        <link href="https://fonts.gstatic.com" rel="preconnect">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

        <!-- Vendor CSS Files -->
        <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/quill/quill.snow.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/quill/quill.bubble.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/remixicon/remixicon.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/simple-datatables/style.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/select2/select2.min.css') }}" rel="stylesheet">

        <!-- Main CSS File -->
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    </head>
    <body>

        @auth
            @include('layout.header')
            @include('layout.sidebar')
        @endauth

        <main @auth id="main" class="main" @endauth>
            @yield('main')
        </main>


        @auth
            @include('layout.footer')
        @endauth

        <!-- Vendor JS Files -->
        <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/chart.js/chart.umd.js') }}"></script>
        <script src="{{ asset('vendor/echarts/echarts.min.js') }}"></script>
        <script src="{{ asset('vendor/quill/quill.js') }}"></script>
        <script src="{{ asset('vendor/simple-datatables/simple-datatables.js') }}"></script>
        <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
        <script src="{{ asset('vendor/php-email-form/validate.js') }}"></script>
        <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
        <!-- jQuery-ui 1.14.0 -->
        <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
        <!-- Select 2 -->
        <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
        <!-- Main JS File -->
        <script src="{{ asset('js/main.js') }}"></script>

        @yield('footer-script')

    </body>
</html>
