@extends('layouts.guest.app')
@section('content')

    <body data-kt-name="metronic" id="kt_body" class="app-blank app-blank bg-dark">
        <!--begin::Theme mode setup on page load-->
        <script>
            if (document.documentElement) {
                const defaultThemeMode = "system";
                const name = document.body.getAttribute("data-kt-name");
                let themeMode = localStorage.getItem("kt_" + (name !== null ? name + "_" : "") + "theme_mode_value");
                if (themeMode === null) {
                    if (defaultThemeMode === "system") {
                        themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
                    } else {
                        themeMode = defaultThemeMode;
                    }
                }
                document.documentElement.setAttribute("data-theme", themeMode);
            }
        </script>
        <!--end::Theme mode setup on page load-->
        <!--begin::Root-->
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Body-->
                <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                    <!--begin::Form-->
                    <div class="d-flex flex-center flex-column flex-lg-row-fluid ">
                        <!--begin::Wrapper-->
                        <div class="w-lg-500px p-10 bg-white rounded-3 p-10">
                            <!--begin::Form-->
                            <form class="form w-100" method="POST" action="{{ route('auth.login') }}">
                                @csrf
                                <!--begin::Heading-->
                                <div class="text-center mb-11">
                                    <a href="/" class="mb-12">
                                    </a>
                                    <!--begin::Title-->
                                    <h1 class="text-dark fw-bolder mb-3">Login to Your Account
                                    </h1>
                                    <!--end::Title-->
                                    <!--begin::Subtitle-->
                                    <!-- <div class="text-gray-500 fw-semibold fs-6">Your Social Campaigns</div> -->
                                    <!--end::Subtitle=-->
                                </div>
                                <!--begin::Heading-->

                                <!--end::Login options-->

                                <!--begin::Input group=-->
                                <div class="fv-row mb-8">
                                    <!--begin::Email-->
                                    <input type="email" placeholder="Email" name="email" id="email"
                                        autocomplete="off" :value="old('email')" required autofocus autocomplete="username"
                                        class="form-control bg-transparent" />

                                    <!--end::Email-->
                                </div>
                                <!--end::Input group=-->
                                <div class="fv-row mb-3">
                                    <!--begin::Password-->
                                    <input type="password" id="password" placeholder="Password" name="password" required
                                        autocomplete="current-password" autocomplete="off"
                                        class="form-control bg-transparent" />
                                    <!--end::Password-->
                                </div>
                                <!--end::Input group=-->

                                <!--begin::Submit button-->
                                <div class="d-grid mb-10">
                                    <button type="submit" id="kt_sign_in_submit" class="btn  btn-golden">
                                        <!--begin::Indicator label-->
                                        <span class="indicator-label">{{ __('Log in') }}
                                        </span>
                                        <!--end::Indicator label-->
                                        <!--begin::Indicator progress-->
                                        <span class="indicator-progress">Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        <!--end::Indicator progress-->
                                    </button>
                                </div>
                                <!--end::Submit button-->

                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Form-->
                    <!--begin::Footer-->
                    <!-- <div class="d-flex flex-center flex-wrap px-5">
                                                                                                <div class="d-flex fw-semibold text-primary fs-base">
                                                                                                    <a href="../../demo1/dist/pages/team.html" class="px-5" target="_blank">Terms</a>
                                                                                                    <a href="../../demo1/dist/pages/pricing/column.html" class="px-5" target="_blank">Plans</a>
                                                                                                    <a href="../../demo1/dist/pages/contact.html" class="px-5" target="_blank">Contact Us</a>
                                                                                                </div>
                                                                                            </div> -->
                </div>
                <!--end::Body-->
                <!--begin::Aside-->
                <div class="d-none d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2"
                    style="background-image: url(assets/media/misc/auth-bg.png)">
                    <!--begin::Content-->
                    <div class="d-flex flex-column flex-center py-15 px-5 px-md-15 w-100">
                        <!--begin::Logo-->
                        <a href="../../demo1/dist/index.html" class="mb-12">
                            <img alt="Logo" src="assets/media/logos/custom-1.png" class="h-75px" />
                        </a>
                        <!--end::Logo-->
                        <!--begin::Image-->
                        <img class="mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20"
                            src="assets/media/misc/auth-screens.png" alt="" />
                        <!--end::Image-->
                        <!--begin::Title-->
                        <h1 class="text-white fs-2qx fw-bolder text-center mb-7">Fast, Efficient and Productive</h1>
                        <!--end::Title-->
                        <!--begin::Text-->
                        <div class="text-white fs-base text-center">In this kind of post,
                            <a href="#" class="opacity-75-hover text-warning fw-bold me-1">the blogger</a>introduces a
                            person they’ve interviewed
                            <br />and provides some background information about
                            <a href="#" class="opacity-75-hover text-warning fw-bold me-1">the interviewee</a>and
                            their
                            <br />work following this is a transcript of the interview.
                        </div>
                        <!--end::Text-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Aside-->
            </div>
            <!--end::Authentication - Sign-in-->
        </div>
    @endsection
