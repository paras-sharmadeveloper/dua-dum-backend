<!DOCTYPE html>
<html lang="en">

<head>
    <base href="">
    <title>@yield('title', 'Dashboard')</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Vendor CSS --}}
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/developer.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.0/css/fixedHeader.dataTables.min.css">
    <link href="//cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet" />

    @yield('styles')

    <style>
        body {
            font-family: 'Poppins', sans-serif !important;
        }

        .form-control {
            border-width: 1px;
            border-color: #cfcfcf;
            border-radius: 0.375rem;
        }

        .form-control:focus {
            border-color: #2e2d2d;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.25);
        }

        .form-select.form-select-transparent {
            border-color: #cfcfcf !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0px !important;
        }

        .dataTables_paginate {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .dataTables_paginate .pagination {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
        }

        .dataTables_paginate .pagination li {
            margin: 0 5px;
        }

        .dataTables_paginate .pagination li a {
            padding: 8px 16px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #ddd;
            color: #333;
        }

        .dataTables_paginate .pagination li.active a {
            background-color: #009ef7;
            color: #fff;
            border-color: #009ef7;
        }

        .dataTables_paginate .pagination li.disabled a {
            background-color: #e9ecef;
            color: #6c757d;
            pointer-events: none;
        }

        .dataTables_paginate .pagination li a:hover {
            background-color: #009ef7 !important;
            color: white !important;
            border-color: #009ef7 !important;
            box-shadow: none !important;
        }

        #customSearchInput:focus {
            border: 1px solid #cccfd7 !important;
            box-shadow: none !important;
        }

        #loader-overlay {
            transition: opacity 0.3s ease;
            background: rgba(0, 0, 0, 0.7);
        }

        #loader-overlay img {
            width: 80px;
            height: 80px;
        }
    </style>
</head>

<body data-kt-name="metronic" id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">

    {{-- Theme Mode --}}
    <script>
        if (document.documentElement) {
            const defaultThemeMode = "system";
            const name = document.body.getAttribute("data-kt-name");
            let themeMode = localStorage.getItem("kt_" + (name !== null ? name + "_" : "") + "theme_mode_value");
            if (themeMode === null) {
                themeMode = defaultThemeMode === "system" ?
                    (window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light") :
                    defaultThemeMode;
            }
            document.documentElement.setAttribute("data-theme", themeMode);
        }
    </script>

    {{-- Loader --}}
    <div id="loader-overlay"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; text-align:center;">
        <img src="{{ asset('assets/media/test.gif') }}" alt="Loading..."
            style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);">
    </div>

    {{-- App Root --}}
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

            {{-- Header --}}
            @include('layouts.header')

            {{-- Wrapper --}}
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

                {{-- Sidebar --}}
                @include('layouts.sidebar')

                {{-- Main --}}
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">


                    <div class="d-flex flex-column flex-column-fluid">

                        {{-- Toolbar --}}
                        <div id="kt_app_toolbar" class="app-toolbar py-3"></div>

                        {{-- Content --}}
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div class="content d-flex flex-column flex-column-fluid">
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
                                @yield('content')
                            </div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    @include('layouts.footer')
                </div>

            </div>
        </div>
    </div>

    {{-- Global Scripts --}}
    <script>
        var hostUrl = "{{ asset('assets') }}/";
    </script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="{{ asset('assets/js/custom/main.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
    <script src="{{ asset('assets/js/custom/apps/chat/chat.js') }}"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.0/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    {{-- CSRF for AJAX --}}
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @yield('scripts')

</body>

</html>
