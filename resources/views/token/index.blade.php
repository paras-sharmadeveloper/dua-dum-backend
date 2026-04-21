<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}" dir="{{ ($locale ?? 'en') === 'ur' ? 'rtl' : 'ltr' }}">


<head>
    <base href="../../">
    <title>Token Registration</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="The most advanced Bootstrap Admin Theme on Themeforest trusted by 100,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue, Asp.Net Core, Blazor, Django, Flask &amp; Laravel versions. Grab your copy now and get life-time updates for free." />
    <meta name="keywords"
        content="Metronic, Bootstrap, Bootstrap 5, Angular, VueJs, React, Asp.Net Core, Blazor, Django, Flask &amp; Laravel, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title"
        content="Metronic - Bootstrap 5 HTML, VueJS, React, Angular, Asp.Net Core, Blazor, Django, Flask &amp; Laravel Admin Dashboard Theme" />
    <meta property="og:url" content="https://keenthemes.com/metronic" />
    <meta property="og:site_name" content="Keenthemes | Metronic" />
    <link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
    <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!-- Poppins font for this page -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" />
    <!--end::Global Stylesheets Bundle-->
    <style>
        :root {
            /* Orange palette from attachment */
            --orange-1: #FF6129;
            --orange-2: #FC3B00;
            --orange-3: #FF7145;
            --black: #000000;
            --white: #FFFFFF;
            --text: var(--white);
            --muted: rgba(255,255,255,0.7);
        }
        /* Layered gradient background for depth */
        body {
            background:
                radial-gradient(1200px 800px at 10% 15%, rgba(255, 97, 41, 0.18), transparent 60%),
                radial-gradient(1000px 700px at 85% 25%, rgba(252, 59, 0, 0.22), transparent 65%),
                radial-gradient(900px 600px at 20% 85%, rgba(255, 113, 69, 0.18), transparent 55%),
                linear-gradient(135deg, #0a0a0a 0%, #1a0f0b 20%, #2a130d 40%, #3a1a12 55%, #200b06 70%, #0b0603 100%);
            color: var(--text);
            font-family: 'Poppins', -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            min-height: 100vh;
        }

        /* RTL Support for Urdu */
        [dir="rtl"] .form-label,
        [dir="rtl"] h1,
        [dir="rtl"] h2,
        [dir="rtl"] h3,
        [dir="rtl"] h4,
        [dir="rtl"] h5,
        [dir="rtl"] h6,
        [dir="rtl"] p,
        [dir="rtl"] .text-white,
        [dir="rtl"] .choice-text {
            text-align: right;
            direction: rtl;
        }

        /* Keep input fields LTR even in RTL mode for proper data entry */
        [dir="rtl"] .form-control,
        [dir="rtl"] .glass-input,
        [dir="rtl"] input[type="text"],
        [dir="rtl"] input[type="tel"] {
            text-align: left;
            direction: ltr;
        }

        /* Keep phone input wrapper layout LTR (country code on left, number field on right) */
        [dir="rtl"] .phone-input-wrapper {
            direction: ltr;
        }

        [dir="rtl"] .d-flex.flex-stack {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .me-2 {
            margin-right: 0 !important;
            margin-left: 0.5rem !important;
        }

        [dir="ltr"] .form-label,
        [dir="ltr"] h1,
        [dir="ltr"] h2,
        [dir="ltr"] h3,
        [dir="ltr"] h4,
        [dir="ltr"] h5,
        [dir="ltr"] h6,
        [dir="ltr"] p,
        [dir="ltr"] .text-white,
        [dir="ltr"] .choice-text {
            text-align: left;
            direction: ltr;
        }

        .back-button {
            position: relative;
        }

        .back-button.dropdown-open {
            z-index: -1 !important;
        }

        .app-shell {
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
            gap: 12px;
        }

        .brand-header {
            position: fixed;
            top: 14px;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 14px;
            color: var(--muted);
            z-index: 10;
        }

        .progress-wrap {
            position: fixed;
            top: 48px;
            left: 50%;
            transform: translateX(-50%);
            width: min(640px, 92vw);
            height: 6px;
            background: rgba(255,255,255,0.14);
            border-radius: 999px;
            overflow: hidden;
            z-index: 9;
        }
        .progress-bar {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, var(--orange-1), var(--orange-3), var(--orange-2));
            transition: width 360ms cubic-bezier(.22,.61,.36,1);
        }

        .main-stage {
            display: grid;
            place-items: center;
            perspective: 1200px;
            padding-top: 96px; /* account for fixed header */
            padding-bottom: 32px;
        }

        .card-glass {
            width: min(680px, 94vw);
            background: linear-gradient(180deg, rgba(255,255,255,0.08), rgba(255,255,255,0.05));
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: 0 20px 60px rgba(0,0,0,0.45), inset 0 1px 0 rgba(255,255,255,0.08);
            backdrop-filter: saturate(130%) blur(14px);
            border-radius: 18px;
            transform-style: preserve-3d;
            position: relative; /* create stacking context so dropdown z-index works */
            overflow: visible;
        }

        .stage-panel {
            will-change: transform, opacity;
            transform-origin: center;
            overflow: visible;
        }
        .animate-in {
            animation: zoomFadeIn 520ms cubic-bezier(.22,.61,.36,1) both;
        }
        .animate-out {
            animation: zoomFadeOut 320ms ease both;
        }
        @keyframes zoomFadeIn {
            0% { opacity: 0; transform: translateY(12px) scale(0.96) rotateX(6deg); }
            100% { opacity: 1; transform: translateY(0) scale(1) rotateX(0deg); }
        }
        @keyframes zoomFadeOut {
            0% { opacity: 1; transform: translateY(0) scale(1) rotateX(0deg); }
            100% { opacity: 0; transform: translateY(-8px) scale(0.97) rotateX(-4deg); }
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--orange-1), var(--orange-2));
            background-size: 200% 200%;
            border: none;
            color: var(--white);
            box-shadow: 0 6px 16px rgba(252,59,0,0.35);
            transition: transform 160ms ease, filter 160ms ease, background-position 240ms ease;
        }
        .btn-primary:hover {
            filter: brightness(1.06);
            background-position: 100% 0%;
            transform: translateY(-1px);
        }
        .btn-primary:active { transform: translateY(0); filter: brightness(0.98); }
        .btn-ghost {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.16);
            color: var(--white);
            backdrop-filter: blur(8px);
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.12); }
        .btn-light.btn-active-light-primary { color: var(--white); }

        label, .form-text { color: var(--muted); }
        .form-select, .form-control {
            border-radius: 12px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            color: var(--white);
        }
        .select-wrap { position: relative; }
        .select-wrap::after {
            content: "\25BE"; /* ▼ caret */
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 28px; height: 28px;
            display: grid; place-items: center;
            color: var(--white);
            background: linear-gradient(135deg, var(--orange-1), var(--orange-2));
            border-radius: 8px;
            box-shadow: 0 6px 14px rgba(252,59,0,0.25);
            pointer-events: none;
            font-size: 12px;
        }
        .glass-select { appearance: none; padding: 12px 46px 12px 12px; }
        .glass-select:focus { outline: none; border-color: rgba(255,255,255,0.35); box-shadow: 0 0 0 4px rgba(252,59,0,0.18); }
        .glass-select option {
            background: #4a1c0d;
            color: var(--white);
        }
            .glass-select option:hover { background: transparent; color: var(--white); }
            .glass-select option:checked { background: linear-gradient(90deg, var(--orange-2), var(--orange-1)); color: var(--white); }

            /* Custom dropdown overlay for animated opening */
            .custom-dropdown { position: relative; z-index: 9999; }
            .dropdown-display {
                display: flex; align-items: center; justify-content: space-between;
                padding: 12px; border-radius: 12px;
                background: rgba(255,255,255,0.08);
                border: 1px solid rgba(255,255,255,0.14);
                color: rgba(255,255,255,0.85);
                cursor: pointer;
                position: relative;
                z-index: 1;
            }
            .dropdown-caret {
                width: 28px; height: 28px; border-radius: 8px;
                display: grid; place-items: center; font-size: 12px;
                background: linear-gradient(135deg, var(--orange-1), var(--orange-2));
                box-shadow: 0 6px 14px rgba(252,59,0,0.25); color: var(--white);
                transition: transform 160ms ease;
            }
            .dropdown-caret.rotate { transform: rotate(180deg); }
            .dropdown-menu {
                position: absolute; 
                left: 0; 
                right: 0; 
                top: calc(100% + 6px);
                background: rgba(20,12,8,0.95); /* darker, more opaque */
                border: 1px solid rgba(255,255,255,0.12);
                border-radius: 14px; backdrop-filter: blur(10px);
                backdrop-filter: blur(6px); /* reduce blur per request */
                transform-origin: top center; opacity: 0; transform: translateY(-6px) scaleY(0.98);
                pointer-events: none; transition: opacity 180ms ease, transform 180ms ease;
                z-index: 10000; /* ensure above buttons */
                box-shadow: 0 16px 40px rgba(0,0,0,0.55);
            }
            .dropdown-menu.show { opacity: 1; transform: translateY(0) scaleY(1); pointer-events: auto; }
            .dropdown-item { padding: 12px 14px; margin: 4px 8px; color: var(--white); background: transparent; border-radius: 10px; }
            /* No hover background per request; keep clean listing */
            .dropdown-item.selected { background: linear-gradient(90deg, var(--orange-2), var(--orange-1)); }

            /* Hide native caret decor when using custom overlay while keeping select for value */
            .select-wrap .glass-select.hidden-native { position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px; }

            /* Glass input for phone number */
            .glass-input {
                border-radius: 12px;
                background: rgba(255,255,255,0.08);
                border: 1px solid rgba(255,255,255,0.14);
                color: var(--white);
                padding: 12px;
            }
            .glass-input:focus { outline: none; border-color: rgba(255,255,255,0.35); box-shadow: 0 0 0 4px rgba(252,59,0,0.18); }
            .glass-input.invalid { border-color: #ff4444; background: rgba(255,68,68,0.12); }
            .glass-input.valid { border-color: #44ff88; }
            
            /* Phone number input with country code */
            .phone-input-wrapper {
                display: flex;
                gap: 8px;
                align-items: stretch;
                overflow: visible;
            }
            .country-code-dropdown {
                position: relative;
                min-width: 100px;
                flex-shrink: 0;
                z-index: 9999;
            }
            .country-code-display {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px;
                border-radius: 12px;
                background: rgba(255,255,255,0.08);
                border: 1px solid rgba(255,255,255,0.14);
                cursor: pointer;
                height: 100%;
                transition: all 160ms ease;
                position: relative;
                z-index: 1;
            }
            .country-code-display:hover {
                background: rgba(255,255,255,0.12);
            }
            .country-code-display.open {
                border-color: rgba(255,255,255,0.35);
                box-shadow: 0 0 0 4px rgba(252,59,0,0.18);
            }
            .country-flag {
                width: 24px;
                height: 18px;
                border-radius: 2px;
                font-size: 16px;
                line-height: 1;
            }
            .country-code-text {
                color: var(--white);
                font-weight: 500;
                font-size: 14px;
            }
            .country-dropdown-menu {
                position: absolute;
                left: 0;
                top: calc(100% + 6px);
                width: 280px;
                max-height: 300px;
                overflow-y: auto;
                background: rgba(20,12,8,0.95);
                border: 1px solid rgba(255,255,255,0.12);
                border-radius: 14px;
                backdrop-filter: blur(6px);
                box-shadow: 0 16px 40px rgba(0,0,0,0.55);
                z-index: 10000;
                opacity: 0;
                transform: translateY(-6px) scaleY(0.98);
                pointer-events: none;
                transition: opacity 180ms ease, transform 180ms ease;
            }
            .country-dropdown-menu.show {
                opacity: 1;
                transform: translateY(0) scaleY(1);
                pointer-events: auto;
            }
            .country-dropdown-menu::-webkit-scrollbar {
                width: 6px;
            }
            .country-dropdown-menu::-webkit-scrollbar-track {
                background: rgba(255,255,255,0.05);
                border-radius: 3px;
            }
            .country-dropdown-menu::-webkit-scrollbar-thumb {
                background: rgba(255,255,255,0.2);
                border-radius: 3px;
            }
            .country-dropdown-menu::-webkit-scrollbar-thumb:hover {
                background: rgba(255,255,255,0.3);
            }
            .country-dropdown-search {
                position: sticky;
                top: 0;
                padding: 8px;
                background: rgba(20,12,8,0.95);
                border-bottom: 1px solid rgba(255,255,255,0.08);
                z-index: 1;
            }
            .country-dropdown-search input {
                width: 100%;
                padding: 8px 12px;
                border-radius: 8px;
                background: rgba(255,255,255,0.08);
                border: 1px solid rgba(255,255,255,0.14);
                color: var(--white);
                font-size: 14px;
            }
            .country-dropdown-search input:focus {
                outline: none;
                border-color: rgba(255,255,255,0.35);
            }
            .country-dropdown-search input::placeholder {
                color: rgba(255,255,255,0.5);
            }
            .country-dropdown-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                margin: 4px 8px;
                color: var(--white);
                cursor: pointer;
                border-radius: 8px;
                transition: background 160ms ease;
            }
            .country-dropdown-item:hover {
                background: rgba(255,255,255,0.08);
            }
            .country-dropdown-item.selected {
                background: linear-gradient(90deg, rgba(252,59,0,0.3), rgba(255,97,41,0.3));
            }
            .country-name {
                flex: 1;
                font-size: 14px;
            }
            .country-dial-code {
                color: rgba(255,255,255,0.7);
                font-size: 13px;
            }
            .phone-number-input {
                flex: 1;
            }
            .phone-error-message {
                color: #ff4444;
                font-size: 13px;
                margin-top: 6px;
                display: none;
            }
            .phone-error-message.show {
                display: block;
            }
            
            /* Venue dropdown - same styling as phone number dropdown */
            .venue-dropdown {
                position: relative;
                z-index: 9999;
            }
            .venue-display {
                display: flex;
                align-items: center;
                padding: 12px;
                border-radius: 12px;
                background: rgba(255,255,255,0.08);
                border: 1px solid rgba(255,255,255,0.14);
                cursor: pointer;
                transition: all 160ms ease;
                position: relative;
                z-index: 1;
                min-height: 48px;
            }
            .venue-display:hover {
                background: rgba(255,255,255,0.12);
            }
            .venue-display.open {
                border-color: rgba(255,255,255,0.35);
                box-shadow: 0 0 0 4px rgba(252,59,0,0.18);
            }
            .venue-dropdown-menu {
                position: absolute;
                left: 0;
                top: calc(100% + 6px);
                width: 100%;
                max-height: 300px;
                overflow-y: auto;
                background: rgba(20,12,8,0.95);
                border: 1px solid rgba(255,255,255,0.12);
                border-radius: 14px;
                backdrop-filter: blur(6px);
                box-shadow: 0 16px 40px rgba(0,0,0,0.55);
                z-index: 10000;
                opacity: 0;
                transform: translateY(-6px) scaleY(0.98);
                pointer-events: none;
                transition: opacity 180ms ease, transform 180ms ease;
            }
            .venue-dropdown-menu.show {
                opacity: 1;
                transform: translateY(0) scaleY(1);
                pointer-events: auto;
            }
            .venue-dropdown-menu::-webkit-scrollbar {
                width: 6px;
            }
            .venue-dropdown-menu::-webkit-scrollbar-track {
                background: rgba(255,255,255,0.05);
                border-radius: 3px;
            }
            .venue-dropdown-menu::-webkit-scrollbar-thumb {
                background: rgba(255,255,255,0.2);
                border-radius: 3px;
            }
            .venue-dropdown-menu::-webkit-scrollbar-thumb:hover {
                background: rgba(255,255,255,0.3);
            }
            .venue-dropdown-search {
                position: sticky;
                top: 0;
                padding: 8px;
                background: rgba(20,12,8,0.95);
                border-bottom: 1px solid rgba(255,255,255,0.08);
                z-index: 1;
            }
            .venue-dropdown-search input {
                width: 100%;
                padding: 8px 12px;
                border-radius: 8px;
                background: rgba(255,255,255,0.08);
                border: 1px solid rgba(255,255,255,0.14);
                color: var(--white);
                font-size: 14px;
            }
            .venue-dropdown-search input:focus {
                outline: none;
                border-color: rgba(255,255,255,0.35);
            }
            .venue-dropdown-search input::placeholder {
                color: rgba(255,255,255,0.5);
            }
            .venue-dropdown-item {
                display: flex;
                align-items: center;
                padding: 10px 12px;
                margin: 4px 8px;
                color: var(--white);
                cursor: pointer;
                border-radius: 8px;
                transition: background 160ms ease;
            }
            .venue-dropdown-item:hover {
                background: rgba(255,255,255,0.08);
            }
            .venue-dropdown-item.selected {
                background: linear-gradient(90deg, rgba(252,59,0,0.3), rgba(255,97,41,0.3));
            }
            .venue-name {
                flex: 1;
                font-size: 14px;
            }
        .form-control::placeholder { color: rgba(255,255,255,0.6); }

        /* Hide the stepper navigation (numbers/buttons) while keeping functionality */
        .stepper-nav { display: none !important; }

        /* Choice cards for radios */
        .choices-row { gap: 12px; }
        .choice-card { display: block; cursor: pointer; }
        .choice-input { position: absolute; opacity: 0; pointer-events: none; }
        .choice-body {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.08);
            transition: transform 160ms ease, background 160ms ease, border-color 160ms ease, box-shadow 160ms ease;
        }
        .choice-icon {
            width: 40px; height: 40px; border-radius: 12px;
            display: grid; place-items: center;
            background: linear-gradient(135deg, var(--orange-3), var(--orange-2));
            color: var(--white);
            box-shadow: 0 8px 20px rgba(252,59,0,0.28);
        }
        .choice-text { color: var(--text); font-weight: 600; }
        .choice-card:hover .choice-body { background: rgba(255,255,255,0.1); transform: translateY(-1px); }
        .choice-input:checked ~ .choice-body {
            background: linear-gradient(180deg, rgba(252,59,0,0.22), rgba(255,255,255,0.06));
            border-color: rgba(255, 255, 255, 0.55);
            box-shadow: 0 12px 28px rgba(252,59,0,0.26), inset 0 1px 0 rgba(255,255,255,0.1);
        }
        .choice-input:checked ~ .choice-body .choice-text { color: var(--white); text-shadow: 0 1px 0 rgba(0,0,0,0.35); }
        .choice-input:checked ~ .choice-body .choice-icon { transform: scale(1.05); box-shadow: 0 12px 28px rgba(252,59,0,0.36); }

        /* Visible radio indicator */
        .choice-indicator {
            width: 22px; height: 22px; border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.55);
            position: relative;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.1);
            transition: all 160ms ease;
        }
        .choice-indicator::after {
            content: "";
            position: absolute;
            inset: 4px;
            border-radius: 50%;
            background: transparent;
            transition: background 160ms ease;
        }
        .choice-input:checked ~ .choice-body .choice-indicator {
            border-color: rgba(255, 255, 255, 0.9);
            /* box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.25), inset 0 1px 0 rgba(255,255,255,0.12); */
        }
        .choice-input:checked ~ .choice-body .choice-indicator::after {
            background: white;
        }
        /* .choice-input:focus ~ .choice-body { outline: 2px solid rgba(255, 255, 255, 0.4); outline-offset: 2px; } */
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body data-kt-name="metronic" id="kt_body" class="app-blank app-blank bgi-size-cover bgi-position-center bgi-no-repeat">
    <div class="container app-shell">
        <div class="brand-header">
            <span class="fw-bold">Token Registration</span>
            {{-- <span class="text-muted small">Mobile Friendly</span> --}}
        </div>
        <div class="progress-wrap"><div id="progressBar" class="progress-bar"></div></div>
        @if (!$locale)
            <div class="main-stage">
                <div class="card-glass p-8 w-100" style="max-width: 540px;">
                    <h3 class="mb-6 text-white">{{ __('messages.select_language') }}</h3>
                    <div class="choices-row d-grid mb-6">
                        <label class="choice-card">
                            <input class="choice-input" type="radio" name="locale_choice" value="en" />
                            <div class="choice-body">
                                <div class="choice-icon"><i class="fas fa-language"></i></div>
                                <div class="choice-text">English</div>
                                <div class="choice-indicator" aria-hidden="true"></div>
                            </div>
                        </label>
                        <label class="choice-card">
                            <input class="choice-input" type="radio" name="locale_choice" value="ur" />
                            <div class="choice-body">
                                <div class="choice-icon"><i class="fas fa-language"></i></div>
                                <div class="choice-text">اردو (Urdu)</div>
                                <div class="choice-indicator" aria-hidden="true"></div>
                            </div>
                        </label>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" id="locale-continue-btn">{{ __('messages.next') }}</button>
                    </div>
                </div>
            </div>
        @else
            {{-- Stepper Form --}}

            <!--begin::Stepper-->
            <div class="main-stage">
            <div class="card-glass p-8 stepper stepper-pills" id="kt_stepper_example_basic">
                <!--begin::Nav-->
                <div class="stepper-nav flex-center flex-wrap mb-10">
                    <!--begin::Step 1-->
                    <div class="stepper-item mx-8 my-4 current" data-kt-stepper-element="nav">
                        <!--begin::Wrapper-->
                        <div class="stepper-wrapper d-flex align-items-center">
                            <!--begin::Icon-->
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check"></i>
                                <span class="stepper-number">1</span>
                            </div>
                            <!--end::Icon-->

                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Line-->
                        <div class="stepper-line h-40px"></div>
                        <!--end::Line-->
                    </div>
                    <!--end::Step 1-->

                    <!--begin::Step 2-->
                    <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
                        <!--begin::Wrapper-->
                        <div class="stepper-wrapper d-flex align-items-center">
                            <!--begin::Icon-->
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check"></i>
                                <span class="stepper-number">2</span>
                            </div>
                            <!--begin::Icon-->

                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Line-->
                        <div class="stepper-line h-40px"></div>
                        <!--end::Line-->
                    </div>
                    <!--end::Step 2-->

                    <!--begin::Step 3-->
                    <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
                        <!--begin::Wrapper-->
                        <div class="stepper-wrapper d-flex align-items-center">
                            <!--begin::Icon-->
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check"></i>
                                <span class="stepper-number">3</span>
                            </div>
                            <!--begin::Icon-->

                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Line-->
                        <div class="stepper-line h-40px"></div>
                        <!--end::Line-->
                    </div>
                    <!--end::Step 3-->

                    <!--begin::Step 4-->
                    <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
                        <!--begin::Wrapper-->
                        <div class="stepper-wrapper d-flex align-items-center">
                            <!--begin::Icon-->
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check"></i>
                                <span class="stepper-number">4</span>
                            </div>
                            <!--begin::Icon-->

                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Line-->
                        <div class="stepper-line h-40px"></div>
                        <!--end::Line-->
                    </div>
                    <!--end::Step 4-->

                    <!--begin::Step 5-->
                    <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
                        <!--begin::Wrapper-->
                        <div class="stepper-wrapper d-flex align-items-center">
                            <!--begin::Icon-->
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check"></i>
                                <span class="stepper-number">5</span>
                            </div>
                            <!--begin::Icon-->

                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Line-->
                        <div class="stepper-line h-40px"></div>
                        <!--end::Line-->
                    </div>
                    <!--end::Step 5-->

                    <!--begin::Step 6-->
                    <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
                        <!--begin::Wrapper-->
                        <div class="stepper-wrapper d-flex align-items-center">
                            <!--begin::Icon-->
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check"></i>
                                <span class="stepper-number">6</span>
                            </div>
                            <!--begin::Icon-->

                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Step 6-->
                </div>
                <!--end::Nav-->

                <!--begin::Form-->
                <form class="form w-lg-500px mx-auto" novalidate="novalidate" id="kt_stepper_example_basic_form"
                    method="POST" action="{{ route('token-registration.generate-token') }}" enctype="multipart/form-data">
                    @csrf
                    <!--begin::Group-->
                    <div class="mb-5">
                        <!--begin::Step 1 - Photo Capture-->
                        <div class="flex-column current stage-panel" data-kt-stepper-element="content" id="step1-content">
                            <div id="step1-initial">
                                <p>{{ __('messages.photo_instruction_1') }}</p>
                            </div>
                            <div id="step1-after-capture" style="display:none;">
                                <!-- Removed the photo captured message as per user request -->
                            </div>
                        </div>
                        <!--end::Step 1-->

                        <!--begin::Step 2 - Venue Selection-->
                        <div class="flex-column stage-panel" data-kt-stepper-element="content">
                            <div class="fv-row mb-10">
                                <label class="form-label text-white">{{ __('messages.select_venue') }}</label>
                                <div class="venue-dropdown">
                                    <div class="venue-display" id="venue-display">
                                        <span id="venue-display-text">Loading venues...</span>
                                    </div>
                                    <div class="venue-dropdown-menu" id="venue-menu">
                                        <div class="venue-dropdown-search">
                                            <input type="text" id="venue-search" placeholder="{{ __('messages.search_venue') }}" autocomplete="off">
                                        </div>
                                        <div id="venue-list"></div>
                                    </div>
                                </div>
                                <select class="form-select hidden-native-select" name="venue_id" id="venue-select" required style="position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px;">
                                    <option value="">Loading venues...</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Step 2-->

                        <!--begin::Step 3 - User Type Selection-->
                        <div class="flex-column stage-panel" data-kt-stepper-element="content">
                            <div class="fv-row mb-10">
                                <label class="form-label mb-5 text-white">{{ __('messages.select_user_type') }}</label>

                                <!--begin::Row-->
                                <div class="row mw-500px mb-5 choices-row">
                                    <!--begin::Col-->
                                    <div class="col-12">
                                        <label class="choice-card">
                                            <input class="choice-input" type="radio" value="normal_person" name="user_type" id="normal_person" />
                                            <div class="choice-body">
                                                <div class="choice-icon"><i class="fas fa-user"></i></div>
                                                <div class="choice-text">{{ __('messages.dua_type_normal_person') }}</div>
                                                <div class="choice-indicator" aria-hidden="true"></div>
                                            </div>
                                        </label>
                                    </div>
                                    <!--end::Col-->

                                    <!--begin::Col-->
                                    <div class="col-12">
                                        <label class="choice-card">
                                            <input class="choice-input" type="radio" value="working_lady" name="user_type" id="working_lady" />
                                            <div class="choice-body">
                                                <div class="choice-icon"><i class="fas fa-briefcase"></i></div>
                                                <div class="choice-text">{{ __('messages.dua_type_working_lady') }}</div>
                                                <div class="choice-indicator" aria-hidden="true"></div>
                                            </div>
                                        </label>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Row-->

                                <!--begin::QR Upload for Working Lady-->
                                <div id="qr-upload-section" style="display:none;" class="fv-row mb-10">
                                    <label class="form-label text-white">{{ __('messages.upload_qr_code') }}</label>
                                    <input type="file" class="form-control form-control-solid"
                                        accept="image/jpeg,image/png,image/jpg,image/gif" id="qr-code-input" name="qr_code"
                                        disabled />
                                    <div id="qr-validation-message" class="mt-3" style="display: none;"></div>
                                    <div class="form-text">{{ __('messages.qr_code_instruction') }}</div>
                                </div>
                                <!--end::QR Upload-->
                            </div>
                        </div>
                        <!--end::Step 3-->

                        <!--begin::Step 4 - Service Type (Dua/Dum)-->
                        <div class="flex-column stage-panel" data-kt-stepper-element="content">
                            <div class="fv-row mb-10">
                                <label class="form-label mb-5 text-white">{{ __('messages.select_service_type') }}</label>

                                <!--begin::Row-->
                                <div class="row mw-500px mb-5 choices-row">
                                    <!--begin::Col-->
                                    <div class="col-12">
                                        <label class="choice-card">
                                            <input class="choice-input" type="radio" value="dua" name="service_type" id="dua_option" />
                                            <div class="choice-body">
                                                <div class="choice-icon"><i class="fas fa-hands-praying"></i></div>
                                                <div class="choice-text">{{ __('messages.general_dua') }}</div>
                                                <div class="choice-indicator" aria-hidden="true"></div>
                                            </div>
                                        </label>
                                    </div>
                                    <!--end::Col-->

                                    <!--begin::Col-->
                                    <div class="col-12">
                                        <label class="choice-card">
                                            <input class="choice-input" type="radio" value="dum" name="service_type" id="dum_option" />
                                            <div class="choice-body">
                                                <div class="choice-icon"><i class="fas fa-hand-holding-heart"></i></div>
                                                <div class="choice-text">{{ __('messages.general_dum') }}</div>
                                                <div class="choice-indicator" aria-hidden="true"></div>
                                            </div>
                                        </label>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Row-->
                            </div>
                        </div>
                        <!--end::Step 4-->

                        <!--begin::Step 5 - User Info & Phone Number-->
                        <div class="flex-column stage-panel" data-kt-stepper-element="content">
                            <div class="fv-row mb-6">
                                <label class="form-label d-flex align-items-center">
                                    <span class="required text-white">{{ __('messages.user_name') }}</span>
                                </label>
                                <input type="text" class="form-control form-control-solid glass-input" name="user_name"
                                    placeholder="{{ __('messages.your_name') }}" value="" required />
                            </div>
                            <div class="fv-row mb-6">
                                <label class="form-label d-flex align-items-center">
                                    <span class="required text-white">{{ __('messages.city') }}</span>
                                </label>
                                <input type="text" class="form-control form-control-solid glass-input" name="city"
                                    placeholder="{{ __('messages.your_city') }}" value="" required />
                            </div>
                            <div class="fv-row mb-10">
                                <label class="form-label d-flex align-items-center">
                                    <span class="required text-white">{{ __('messages.phone_number') }}</span>
                                </label>
                                <div class="phone-input-wrapper">
                                    <div class="country-code-dropdown">
                                        <div class="country-code-display" id="country-code-display">
                                            <span class="country-flag" id="selected-country-flag">🇺🇸</span>
                                            <span class="country-code-text" id="selected-country-code">+1</span>
                                        </div>
                                        <div class="country-dropdown-menu" id="country-dropdown-menu">
                                            <div class="country-dropdown-search">
                                                <input type="text" id="country-search" placeholder="{{ __('messages.search_country') }}" autocomplete="off">
                                            </div>
                                            <div id="country-list"></div>
                                        </div>
                                    </div>
                                    <div class="phone-number-input">
                                        <input type="tel" class="form-control form-control-solid glass-input" 
                                            id="phone-input" 
                                            name="phone_number_display" 
                                            placeholder="{{ __('messages.enter_phone_number') }}" 
                                            value="" 
                                            autocomplete="tel" />
                                        <input type="hidden" name="phone_number" id="full-phone-number">
                                        <input type="hidden" name="country_code" id="country-code-value">
                                    </div>
                                </div>
                                <div class="phone-error-message" id="phone-error-message"></div>
                                <div class="form-text">{{ __('messages.select_country') }}</div>
                            </div>
                        </div>
                        <!--end::Step 5-->

                        <!--begin::Step 6 - Success Message-->
                        <div class="flex-column stage-panel" data-kt-stepper-element="content">
                            <div class="text-center">
                                <div class="mb-10">
                                    <i class="fas fa-check-circle fs-5x text-success mb-5"></i>
                                    <h2 class="text-success">{{ __('messages.token_generated') }}</h2>
                                    <p class="text-muted">{{ __('messages.token_instructions') }}</p>
                                </div>
                            </div>
                        </div>
                        <!--end::Step 6-->

                    </div>
                    <!--end::Group-->

                    <!--begin::Actions-->
                    <div class="d-flex flex-stack">
                        <!--begin::Wrapper-->
                        <div class="me-2 back-button">
                            <button type="button" class="btn btn-ghost"
                                data-kt-stepper-action="previous">
                                Back
                            </button>
                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Wrapper-->
                        <div>
                            <button type="submit" class="btn btn-primary" data-kt-stepper-action="submit"
                                style="display:none;">
                                <span class="indicator-label">
                                    Submit
                                </span>
                                <span class="indicator-progress">
                                    Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>

                            <button type="button" class="btn btn-primary" id="stepper-action-btn">
                                Continue
                            </button>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Actions-->

                    <!-- Hidden inputs -->
                    <input type="hidden" name="user_image" id="user-image-input">
                </form>
                <!--end::Form-->
            </div>
            </div>
            <!--end::Stepper-->
        @endif
    </div>

    <video id="user-video" autoplay playsinline style="display:none;"></video>
    <canvas id="user-canvas" style="display:none;"></canvas>
    <input type="hidden" name="user_image" id="user-image-input">

</body>
<!--end::Body-->

<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>
<!-- jsQR library for QR code decoding -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
    // Professional centered alert using SweetAlert
    function showCenterAlert(message, icon = 'warning') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: icon,
                buttonsStyling: false,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
        } else {
            alert(message);
        }
    }

    // Country data with flags, codes, and validation patterns
    const countries = [
        { name: 'United States', code: 'US', dialCode: '+1', flag: '🇺🇸', pattern: /^[2-9]\d{9}$/, placeholder: '(415) 555-2671' },
        { name: 'United Kingdom', code: 'GB', dialCode: '+44', flag: '🇬🇧', pattern: /^[1-9]\d{9,10}$/, placeholder: '7400 123456' },
        { name: 'Pakistan', code: 'PK', dialCode: '+92', flag: '🇵🇰', pattern: /^3\d{9}$/, placeholder: '300 1234567' },
        { name: 'India', code: 'IN', dialCode: '+91', flag: '🇮🇳', pattern: /^[6-9]\d{9}$/, placeholder: '98765 43210' },
        { name: 'Canada', code: 'CA', dialCode: '+1', flag: '🇨🇦', pattern: /^[2-9]\d{9}$/, placeholder: '(416) 555-0123' },
        { name: 'Australia', code: 'AU', dialCode: '+61', flag: '🇦🇺', pattern: /^4\d{8}$/, placeholder: '412 345 678' },
        { name: 'Germany', code: 'DE', dialCode: '+49', flag: '🇩🇪', pattern: /^1[5-7]\d{8,9}$/, placeholder: '151 23456789' },
        { name: 'France', code: 'FR', dialCode: '+33', flag: '🇫🇷', pattern: /^[67]\d{8}$/, placeholder: '6 12 34 56 78' },
        { name: 'United Arab Emirates', code: 'AE', dialCode: '+971', flag: '🇦🇪', pattern: /^5\d{8}$/, placeholder: '50 123 4567' },
        { name: 'Saudi Arabia', code: 'SA', dialCode: '+966', flag: '🇸🇦', pattern: /^5\d{8}$/, placeholder: '50 123 4567' },
        { name: 'Turkey', code: 'TR', dialCode: '+90', flag: '🇹🇷', pattern: /^5\d{9}$/, placeholder: '501 234 5678' },
        { name: 'Malaysia', code: 'MY', dialCode: '+60', flag: '🇲🇾', pattern: /^1\d{8,9}$/, placeholder: '12 345 6789' },
        { name: 'Singapore', code: 'SG', dialCode: '+65', flag: '🇸🇬', pattern: /^[89]\d{7}$/, placeholder: '8123 4567' },
        { name: 'China', code: 'CN', dialCode: '+86', flag: '🇨🇳', pattern: /^1[3-9]\d{9}$/, placeholder: '131 2345 6789' },
        { name: 'Japan', code: 'JP', dialCode: '+81', flag: '🇯🇵', pattern: /^[7-9]0\d{8}$/, placeholder: '90 1234 5678' },
        { name: 'South Korea', code: 'KR', dialCode: '+82', flag: '🇰🇷', pattern: /^1[016-9]\d{7,8}$/, placeholder: '10 1234 5678' },
        { name: 'Indonesia', code: 'ID', dialCode: '+62', flag: '🇮🇩', pattern: /^8\d{9,11}$/, placeholder: '812 3456 7890' },
        { name: 'Philippines', code: 'PH', dialCode: '+63', flag: '🇵🇭', pattern: /^9\d{9}$/, placeholder: '905 123 4567' },
        { name: 'Bangladesh', code: 'BD', dialCode: '+880', flag: '🇧🇩', pattern: /^1[3-9]\d{8}$/, placeholder: '1812 345678' },
        { name: 'Afghanistan', code: 'AF', dialCode: '+93', flag: '🇦🇫', pattern: /^7\d{8}$/, placeholder: '70 123 4567' },
        { name: 'Iran', code: 'IR', dialCode: '+98', flag: '🇮🇷', pattern: /^9\d{9}$/, placeholder: '912 345 6789' },
        { name: 'Egypt', code: 'EG', dialCode: '+20', flag: '🇪🇬', pattern: /^1[0125]\d{8}$/, placeholder: '100 123 4567' },
        { name: 'South Africa', code: 'ZA', dialCode: '+27', flag: '🇿🇦', pattern: /^[6-8]\d{8}$/, placeholder: '71 123 4567' },
        { name: 'Nigeria', code: 'NG', dialCode: '+234', flag: '🇳🇬', pattern: /^[7-9]0\d{8}$/, placeholder: '802 123 4567' },
        { name: 'Kenya', code: 'KE', dialCode: '+254', flag: '🇰🇪', pattern: /^[17]\d{8}$/, placeholder: '712 123456' },
        { name: 'Spain', code: 'ES', dialCode: '+34', flag: '🇪🇸', pattern: /^[6-7]\d{8}$/, placeholder: '612 34 56 78' },
        { name: 'Italy', code: 'IT', dialCode: '+39', flag: '🇮🇹', pattern: /^3\d{8,9}$/, placeholder: '312 345 6789' },
        { name: 'Netherlands', code: 'NL', dialCode: '+31', flag: '🇳🇱', pattern: /^6\d{8}$/, placeholder: '6 12345678' },
        { name: 'Belgium', code: 'BE', dialCode: '+32', flag: '🇧🇪', pattern: /^4\d{8}$/, placeholder: '470 12 34 56' },
        { name: 'Sweden', code: 'SE', dialCode: '+46', flag: '🇸🇪', pattern: /^7\d{8}$/, placeholder: '70 123 45 67' },
        { name: 'Norway', code: 'NO', dialCode: '+47', flag: '🇳🇴', pattern: /^[49]\d{7}$/, placeholder: '406 12 345' },
        { name: 'Denmark', code: 'DK', dialCode: '+45', flag: '🇩🇰', pattern: /^[2-9]\d{7}$/, placeholder: '32 12 34 56' },
        { name: 'Switzerland', code: 'CH', dialCode: '+41', flag: '🇨🇭', pattern: /^7[5-9]\d{7}$/, placeholder: '78 123 45 67' },
        { name: 'Austria', code: 'AT', dialCode: '+43', flag: '🇦🇹', pattern: /^6\d{8,13}$/, placeholder: '664 123456' },
        { name: 'Poland', code: 'PL', dialCode: '+48', flag: '🇵🇱', pattern: /^[4-8]\d{8}$/, placeholder: '512 345 678' },
        { name: 'Romania', code: 'RO', dialCode: '+40', flag: '🇷🇴', pattern: /^7\d{8}$/, placeholder: '712 034 567' },
        { name: 'Greece', code: 'GR', dialCode: '+30', flag: '🇬🇷', pattern: /^6[89]\d{8}$/, placeholder: '691 234 5678' },
        { name: 'Portugal', code: 'PT', dialCode: '+351', flag: '🇵🇹', pattern: /^9[1236]\d{7}$/, placeholder: '912 345 678' },
        { name: 'Ireland', code: 'IE', dialCode: '+353', flag: '🇮🇪', pattern: /^8[356-9]\d{7}$/, placeholder: '85 012 3456' },
        { name: 'New Zealand', code: 'NZ', dialCode: '+64', flag: '🇳🇿', pattern: /^2\d{7,9}$/, placeholder: '21 123 4567' },
        { name: 'Mexico', code: 'MX', dialCode: '+52', flag: '🇲🇽', pattern: /^[1-9]\d{9}$/, placeholder: '222 123 4567' },
        { name: 'Brazil', code: 'BR', dialCode: '+55', flag: '🇧🇷', pattern: /^[1-9]{2}9?\d{8}$/, placeholder: '11 91234-5678' },
        { name: 'Argentina', code: 'AR', dialCode: '+54', flag: '🇦🇷', pattern: /^9?\d{10}$/, placeholder: '11 2345-6789' },
        { name: 'Chile', code: 'CL', dialCode: '+56', flag: '🇨🇱', pattern: /^9\d{8}$/, placeholder: '9 1234 5678' },
        { name: 'Colombia', code: 'CO', dialCode: '+57', flag: '🇨🇴', pattern: /^3\d{9}$/, placeholder: '321 1234567' },
        { name: 'Russia', code: 'RU', dialCode: '+7', flag: '🇷🇺', pattern: /^9\d{9}$/, placeholder: '912 345-67-89' },
        { name: 'Ukraine', code: 'UA', dialCode: '+380', flag: '🇺🇦', pattern: /^[3-9]\d{8}$/, placeholder: '50 123 4567' },
        { name: 'Thailand', code: 'TH', dialCode: '+66', flag: '🇹🇭', pattern: /^[689]\d{8}$/, placeholder: '81 234 5678' },
        { name: 'Vietnam', code: 'VN', dialCode: '+84', flag: '🇻🇳', pattern: /^[3-9]\d{8}$/, placeholder: '91 234 5678' },
        { name: 'Qatar', code: 'QA', dialCode: '+974', flag: '🇶🇦', pattern: /^[3-7]\d{7}$/, placeholder: '3312 3456' },
        { name: 'Kuwait', code: 'KW', dialCode: '+965', flag: '🇰🇼', pattern: /^[569]\d{7}$/, placeholder: '500 12345' },
        { name: 'Oman', code: 'OM', dialCode: '+968', flag: '🇴🇲', pattern: /^9\d{7}$/, placeholder: '9212 3456' },
        { name: 'Bahrain', code: 'BH', dialCode: '+973', flag: '🇧🇭', pattern: /^[36]\d{7}$/, placeholder: '3600 1234' },
    ];

    let selectedCountry = countries[0]; // Default to US

    document.addEventListener('DOMContentLoaded', function () {
        var element = document.querySelector("#kt_stepper_example_basic");
        var actionBtn = document.getElementById('stepper-action-btn');
        var video = document.getElementById('user-video');
        var canvas = document.getElementById('user-canvas');
        var imageInput = document.getElementById('user-image-input');
        var step1Initial = document.getElementById('step1-initial');
        var step1AfterCapture = document.getElementById('step1-after-capture');
        var qrUploadSection = document.getElementById('qr-upload-section');
        var venueSelect = document.getElementById('venue-select');
        var venueDisplayText = document.getElementById('venue-display-text');
        var venueMenu = document.getElementById('venue-menu');
        var venueDisplay = document.getElementById('venue-display');
        var onAfterCapture = false;
        var prevBtn = document.querySelector('button[data-kt-stepper-action="previous"]');
        var progressBar = document.getElementById('progressBar');
        var panels = Array.from(document.querySelectorAll('[data-kt-stepper-element="content"]'));
        
        // Phone number elements
        var phoneInput = document.getElementById('phone-input');
        var fullPhoneNumber = document.getElementById('full-phone-number');
        var countryCodeValue = document.getElementById('country-code-value');
        var countryCodeDisplay = document.getElementById('country-code-display');
        var countryDropdownMenu = document.getElementById('country-dropdown-menu');
        var countryList = document.getElementById('country-list');
        var countrySearch = document.getElementById('country-search');
        var selectedCountryFlag = document.getElementById('selected-country-flag');
        var selectedCountryCode = document.getElementById('selected-country-code');
        var phoneErrorMessage = document.getElementById('phone-error-message');

        // Initialize country dropdown
        function initCountryDropdown() {
            if (!countryList) return;
            renderCountryList(countries);
            updateSelectedCountry(selectedCountry);
        }

        function renderCountryList(countryData) {
            if (!countryList) return;
            countryList.innerHTML = '';
            countryData.forEach(function(country) {
                var item = document.createElement('div');
                item.className = 'country-dropdown-item';
                if (country.code === selectedCountry.code) {
                    item.classList.add('selected');
                }
                item.innerHTML = '<span class="country-flag">' + country.flag + '</span>' +
                                '<span class="country-name">' + country.name + '</span>' +
                                '<span class="country-dial-code">' + country.dialCode + '</span>';
                item.addEventListener('click', function() {
                    var backButton = document.querySelector('.back-button');
                    selectedCountry = country;
                    updateSelectedCountry(country);
                    if (countryDropdownMenu) countryDropdownMenu.classList.remove('show');
                    if (countryCodeDisplay) countryCodeDisplay.classList.remove('open');
                    if (backButton) backButton.classList.remove('dropdown-open');
                    if (phoneInput && phoneInput.value) {
                        validatePhoneNumber();
                    }
                });
                countryList.appendChild(item);
            });
        }

        function updateSelectedCountry(country) {
            if (selectedCountryFlag) selectedCountryFlag.textContent = country.flag;
            if (selectedCountryCode) selectedCountryCode.textContent = country.dialCode;
            if (phoneInput) phoneInput.placeholder = country.placeholder;
            if (countryCodeValue) countryCodeValue.value = country.dialCode;
            
            // Update selected state in dropdown
            if (countryList) {
                var items = countryList.querySelectorAll('.country-dropdown-item');
                items.forEach(function(item) {
                    item.classList.remove('selected');
                });
            }
        }

        // Toggle country dropdown
        if (countryCodeDisplay) {
            countryCodeDisplay.addEventListener('click', function(e) {
                e.stopPropagation();
                
                var isOpening = !countryDropdownMenu.classList.contains('show');
                var backButton = document.querySelector('.back-button');
                
                countryDropdownMenu.classList.toggle('show');
                countryCodeDisplay.classList.toggle('open');
                
                if (isOpening) {
                    if (backButton) backButton.classList.add('dropdown-open');
                    countrySearch.value = '';
                    renderCountryList(countries);
                    countrySearch.focus();
                } else {
                    if (backButton) backButton.classList.remove('dropdown-open');
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            var backButton = document.querySelector('.back-button');
            if (countryCodeDisplay && !countryCodeDisplay.contains(e.target) && 
                !countryDropdownMenu.contains(e.target)) {
                countryDropdownMenu.classList.remove('show');
                countryCodeDisplay.classList.remove('open');
                if (backButton) backButton.classList.remove('dropdown-open');
            }
        });

        // Country search functionality
        if (countrySearch) {
            countrySearch.addEventListener('input', function(e) {
                var searchTerm = e.target.value.toLowerCase();
                var filtered = countries.filter(function(country) {
                    return country.name.toLowerCase().includes(searchTerm) ||
                           country.dialCode.includes(searchTerm) ||
                           country.code.toLowerCase().includes(searchTerm);
                });
                renderCountryList(filtered);
            });

            countrySearch.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Phone number validation
        function validatePhoneNumber() {
            if (!phoneInput) return true;
            var value = phoneInput.value.trim().replace(/\s+/g, '').replace(/[-()]/g, '');
            
            if (!value) {
                phoneInput.classList.remove('invalid', 'valid');
                if (phoneErrorMessage) phoneErrorMessage.classList.remove('show');
                if (fullPhoneNumber) fullPhoneNumber.value = '';
                return true;
            }

            var isValid = selectedCountry.pattern.test(value);
            
            if (isValid) {
                phoneInput.classList.remove('invalid');
                phoneInput.classList.add('valid');
                if (phoneErrorMessage) phoneErrorMessage.classList.remove('show');
                if (fullPhoneNumber) fullPhoneNumber.value = selectedCountry.dialCode + value;
                return true;
            } else {
                phoneInput.classList.add('invalid');
                phoneInput.classList.remove('valid');
                if (phoneErrorMessage) {
                    phoneErrorMessage.textContent = 'Invalid phone number for ' + selectedCountry.name;
                    phoneErrorMessage.classList.add('show');
                }
                if (fullPhoneNumber) fullPhoneNumber.value = '';
                return false;
            }
        }

        // Validate on input
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                validatePhoneNumber();
            });

            phoneInput.addEventListener('blur', function() {
                validatePhoneNumber();
            });
        }
        
        // Language selection redirect (only if first-page card is shown)
        var localeContinueBtn = document.getElementById('locale-continue-btn');
        if (localeContinueBtn) {
            localeContinueBtn.addEventListener('click', function () {
                var selected = document.querySelector('input[name="locale_choice"]:checked');
                if (!selected) { showCenterAlert('{{ __('messages.select_language_msg') }}'); return; }
                window.location.href = '{{ route('token-registration') }}' + '/' + selected.value;
            });
        }

        if (element && actionBtn) {
            var stepper = new KTStepper(element);
            actionBtn.textContent = "{{ __('messages.startbooking') }}";
            
            // Initialize country dropdown after stepper is ready
            if (countryCodeDisplay && countryDropdownMenu && countryList) {
                initCountryDropdown();
            }

            // Add event listener for Previous button
            if (prevBtn) {
                prevBtn.addEventListener('click', function () {
                    var currentStep = stepper.getCurrentStepIndex();
                    
                    // If on step 1, go back to language selection page
                    if (currentStep === 1) {
                        window.location.href = '{{ route('token-registration') }}';
                        return;
                    }
                    
                    // If on step 2 and going back to step 1, reset the photo capture state
                    if (currentStep === 2) {
                        onAfterCapture = false;
                        if (step1Initial && step1AfterCapture) {
                            step1Initial.style.display = 'block';
                            step1AfterCapture.style.display = 'none';
                        }
                        actionBtn.textContent = "{{ __('messages.startbooking') }}";
                    }
                    
                    stepper.goPrevious();
                });
            }

            // Load venues when page loads
            loadVenues();

            // Handle user type selection
            document.querySelectorAll('input[name="user_type"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    var qrInput = document.getElementById('qr-code-input');
                    if (this.value === 'working_lady') {
                        qrUploadSection.style.display = 'block';
                        if (qrInput) {
                            qrInput.disabled = false;
                        }
                    } else {
                        qrUploadSection.style.display = 'none';
                        if (qrInput) {
                            qrInput.disabled = true;
                            qrInput.value = '';
                        }
                    }
                    
                    // Fetch service types based on venue + user type
                    if (venueSelect && venueSelect.value) {
                        fetchVenueAvailability(venueSelect.value, this.value);
                    }
                });
            });

            function captureImage() {
                if (video && canvas && imageInput) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    var dataUrl = canvas.toDataURL('image/png');
                    imageInput.value = dataUrl;
                    if (video.srcObject) {
                        video.srcObject.getTracks().forEach(track => track.stop());
                    }
                }
            }

            function loadVenues() {
                fetch('{{ route("token-registration.get-venues") }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || document.querySelector('input[name="_token"]').value,
                        'Content-Type': 'application/json',
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.venues && data.venues.length > 0) {
                            // Store venues globally for search
                            window.venuesData = data.venues;
                            
                            // Update hidden select
                            venueSelect.innerHTML = '<option value="">Select a venue...</option>';
                            data.venues.forEach(function (venue) {
                                var option = document.createElement('option');
                                option.value = venue.id;
                                option.textContent = venue.venue_name + ' (' + venue.venue_address_eng + ')';
                                venueSelect.appendChild(option);
                            });
                            
                            // Initialize venue dropdown
                            if (venueDisplayText) {
                                venueDisplayText.textContent = 'Select a venue...';
                            }
                            renderVenueList(data.venues);
                        } else {
                            venueSelect.innerHTML = '<option value="">No venues available</option>';
                            if (venueDisplayText) {
                                venueDisplayText.textContent = 'No venues available';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading venues:', error);
                        venueSelect.innerHTML = '<option value="">Error loading venues</option>';
                        if (venueDisplayText) {
                            venueDisplayText.textContent = 'Error loading venues';
                        }
                    });
            }

            function renderVenueList(venues) {
                var venueList = document.getElementById('venue-list');
                if (!venueList) return;
                
                venueList.innerHTML = '';
                venues.forEach(function(venue) {
                    var item = document.createElement('div');
                    item.className = 'venue-dropdown-item';
                    item.innerHTML = '<span class="venue-name">' + venue.venue_name + ' (' + venue.venue_address_eng + ')</span>';
                    item.dataset.venueId = venue.id;
                    item.dataset.venueName = venue.venue_name + ' (' + venue.venue_address_eng + ')';
                    
                    item.addEventListener('click', function() {
                        var backButton = document.querySelector('.back-button');
                        venueSelect.value = this.dataset.venueId;
                        venueDisplayText.textContent = this.dataset.venueName;
                        
                        // Update selected state
                        var allItems = venueList.querySelectorAll('.venue-dropdown-item');
                        allItems.forEach(function(i) { i.classList.remove('selected'); });
                        this.classList.add('selected');
                        
                        // Close dropdown
                        venueMenu.classList.remove('show');
                        venueDisplay.classList.remove('open');
                        if (backButton) backButton.classList.remove('dropdown-open');
                        
                        // Fetch availability for this venue (user types only)
                        fetchVenueAvailability(this.dataset.venueId, null);
                    });
                    
                    venueList.appendChild(item);
                });
            }

            // Fetch venue availability and update user type / service type options
            function fetchVenueAvailability(venueId, userType) {
                console.log('Fetching availability for venue:', venueId, 'user type:', userType);
                
                var requestBody = { venue_id: venueId };
                if (userType) {
                    requestBody.user_type = userType;
                }
                
                fetch('{{ route("token-registration.venue-availability") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestBody)
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Venue availability response:', data);
                    if (data.success) {
                        // If no user type specified, update user type cards
                        if (!userType) {
                            var normalPersonCard = document.getElementById('normal_person').closest('.col-12');
                            var workingLadyCard = document.getElementById('working_lady').closest('.col-12');
                            
                            console.log('Updating user types. Available:', data.user_types);
                            
                            if (normalPersonCard) {
                                normalPersonCard.style.display = data.user_types.normal_person ? 'block' : 'none';
                            }
                            if (workingLadyCard) {
                                workingLadyCard.style.display = data.user_types.working_lady ? 'block' : 'none';
                            }
                            
                            // Clear user type selection
                            document.querySelectorAll('input[name="user_type"]').forEach(function(input) {
                                input.checked = false;
                            });
                            
                            // Hide all service type cards until user type is selected
                            document.querySelectorAll('input[name="service_type"]').forEach(function(input) {
                                var parent = input.closest('.col-12');
                                if (parent) parent.style.display = 'none';
                                input.checked = false;
                            });
                        } else {
                            // If user type specified, update service type cards
                            var duaCard = document.querySelector('input[name="service_type"][value="dua"]');
                            var dumCard = document.querySelector('input[name="service_type"][value="dum"]');
                            
                            console.log('Updating service types. Available:', data.service_types);
                            
                            if (duaCard) {
                                var duaParent = duaCard.closest('.col-12');
                                if (duaParent) {
                                    duaParent.style.display = data.service_types.dua ? 'block' : 'none';
                                }
                            }
                            if (dumCard) {
                                var dumParent = dumCard.closest('.col-12');
                                if (dumParent) {
                                    dumParent.style.display = data.service_types.dum ? 'block' : 'none';
                                }
                            }
                            
                            // Clear service type selection
                            document.querySelectorAll('input[name="service_type"]').forEach(function(input) {
                                input.checked = false;
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching venue availability:', error);
                });
            }

            // Toggle venue dropdown
            if (venueDisplay && venueMenu) {
                var venueSearch = document.getElementById('venue-search');
                
                venueDisplay.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    var isOpening = !venueMenu.classList.contains('show');
                    var backButton = document.querySelector('.back-button');
                    
                    venueMenu.classList.toggle('show');
                    venueDisplay.classList.toggle('open');
                    
                    if (isOpening) {
                        if (backButton) backButton.classList.add('dropdown-open');
                        if (venueSearch) {
                            venueSearch.value = '';
                            if (window.venuesData) renderVenueList(window.venuesData);
                            venueSearch.focus();
                        }
                    } else {
                        if (backButton) backButton.classList.remove('dropdown-open');
                    }
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    var backButton = document.querySelector('.back-button');
                    if (venueDisplay && !venueDisplay.contains(e.target) && 
                        !venueMenu.contains(e.target)) {
                        venueMenu.classList.remove('show');
                        venueDisplay.classList.remove('open');
                        if (backButton) backButton.classList.remove('dropdown-open');
                    }
                });
                
                // Venue search functionality
                if (venueSearch) {
                    venueSearch.addEventListener('input', function(e) {
                        var searchTerm = e.target.value.toLowerCase();
                        if (window.venuesData) {
                            var filtered = window.venuesData.filter(function(venue) {
                                return venue.venue_name.toLowerCase().includes(searchTerm) ||
                                       venue.venue_address_eng.toLowerCase().includes(searchTerm);
                            });
                            renderVenueList(filtered);
                        }
                    });
                    
                    venueSearch.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }
            }

            // QR Code validation for working lady
            var qrCodeInput = document.getElementById('qr-code-input');
            var qrValidationMessage = document.getElementById('qr-validation-message');
            var isQRApproved = false;
            
            if (qrCodeInput) {
                qrCodeInput.addEventListener('change', function(e) {
                    var file = e.target.files[0];
                    if (!file) return;
                    
                    // Show loading message
                    qrValidationMessage.style.display = 'block';
                    qrValidationMessage.style.color = '#ffa500';
                    qrValidationMessage.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validating QR code...';
                    isQRApproved = false;
                    
                    // Read the QR code image
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        var img = new Image();
                        img.onload = function() {
                            // Create canvas to decode QR
                            var canvas = document.createElement('canvas');
                            var context = canvas.getContext('2d');
                            canvas.width = img.width;
                            canvas.height = img.height;
                            context.drawImage(img, 0, 0);
                            
                            var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                            
                            // Try to decode QR code using jsQR library if available
                            if (typeof jsQR !== 'undefined') {
                                var code = jsQR(imageData.data, imageData.width, imageData.height);
                                if (code) {
                                    validateWorkingLady(code.data);
                                } else {
                                    qrValidationMessage.style.color = '#ff4444';
                                    qrValidationMessage.innerHTML = '<i class="fas fa-times-circle"></i> Could not read QR code. Please upload a valid QR code image.';
                                }
                            } else {
                                // Fallback: try to extract ID from filename or send image to server
                                validateQRWithServer(file);
                            }
                        };
                        img.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }
            
            function validateWorkingLady(qrData) {
                // Extract ID from QR data (assuming QR contains the ID)
                var workingLadyId = qrData;
                
                fetch('{{ route("token-registration.validate-working-lady") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: workingLadyId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        if (data.status === 'Approved') {
                            qrValidationMessage.style.color = '#44ff88';
                            qrValidationMessage.innerHTML = '<i class="fas fa-check-circle"></i> You can proceed. Your working lady status is approved.';
                            isQRApproved = true;
                            
                            // Auto-fill name and phone number fields
                            if (data.data) {
                                if (data.data.name) {
                                    document.querySelector('input[name="user_name"]').value = data.data.name;
                                }
                                if (data.data.phone_number) {
                                    // Parse phone number and set country code
                                    var phoneNum = data.data.phone_number;
                                    // Check if phone starts with +
                                    if (phoneNum.startsWith('+')) {
                                        // Extract country code and number
                                        var matched = false;
                                        for (var i = 0; i < countries.length; i++) {
                                            if (phoneNum.startsWith(countries[i].code)) {
                                                // Set country
                                                selectedCountry = countries[i];
                                                document.getElementById('selected-country-flag').textContent = countries[i].flag;
                                                document.getElementById('selected-country-code').textContent = countries[i].code;
                                                document.getElementById('country-code-value').value = countries[i].code;
                                                
                                                // Set phone number without country code
                                                var numberWithoutCode = phoneNum.substring(countries[i].code.length);
                                                document.getElementById('phone-input').value = numberWithoutCode;
                                                document.getElementById('full-phone-number').value = phoneNum;
                                                matched = true;
                                                break;
                                            }
                                        }
                                        if (!matched) {
                                            // Just set the full number if country code not matched
                                            document.getElementById('phone-input').value = phoneNum;
                                            document.getElementById('full-phone-number').value = phoneNum;
                                        }
                                    } else {
                                        // No country code, just set the number
                                        document.getElementById('phone-input').value = phoneNum;
                                        document.getElementById('full-phone-number').value = selectedCountry.code + phoneNum;
                                    }
                                }
                            }
                        } else {
                            qrValidationMessage.style.color = '#ff4444';
                            qrValidationMessage.innerHTML = '<i class="fas fa-times-circle"></i> You cannot proceed because you are not approved. Current status: ' + data.status;
                            isQRApproved = false;
                            qrCodeInput.value = '';
                        }
                    } else {
                        qrValidationMessage.style.color = '#ff4444';
                        qrValidationMessage.innerHTML = '<i class="fas fa-times-circle"></i> Working lady not found in our system.';
                        isQRApproved = false;
                        qrCodeInput.value = '';
                    }
                })
                .catch(error => {
                    qrValidationMessage.style.color = '#ff4444';
                    qrValidationMessage.innerHTML = '<i class="fas fa-times-circle"></i> Error validating QR code. Please try again.';
                    isQRApproved = false;
                    console.error('QR validation error:', error);
                });
            }
            
            function validateQRWithServer(file) {
                var formData = new FormData();
                formData.append('qr_code', file);
                
                fetch('{{ route("token-registration.decode-qr") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.id) {
                        validateWorkingLady(data.id);
                    } else {
                        qrValidationMessage.style.color = '#ff4444';
                        qrValidationMessage.innerHTML = '<i class="fas fa-times-circle"></i> Could not decode QR code. Please upload a valid QR code.';
                        isQRApproved = false;
                    }
                })
                .catch(error => {
                    qrValidationMessage.style.color = '#ff4444';
                    qrValidationMessage.innerHTML = '<i class="fas fa-times-circle"></i> Error processing QR code. Please try again.';
                    isQRApproved = false;
                    console.error('QR decode error:', error);
                });
            }

            function validateStep(stepIndex) {
                switch (stepIndex) {
                    case 2:
                        // Validate venue selection
                        if (!venueSelect.value) {
                            showCenterAlert('{{ __('messages.select_venue_msg') }}');
                            return false;
                        }
                        return true;
                    case 3:
                        // Validate user type selection
                        var userType = document.querySelector('input[name="user_type"]:checked');
                        if (!userType) {
                            showCenterAlert('{{ __('messages.select_user_type_msg') }}');
                            return false;
                        }
                        // Validate QR upload for working lady
                        if (userType.value === 'working_lady') {
                            var qrFile = document.getElementById('qr-code-input');
                            if (!qrFile || qrFile.disabled || !qrFile.files.length) {
                                showCenterAlert('{{ __('messages.upload_qr_msg') }}');
                                return false;
                            }
                            // Check if QR is approved
                            if (!isQRApproved) {
                                showCenterAlert('{{ __('messages.qr_validation_msg') }}');
                                return false;
                            }
                        }
                        return true;
                    case 4:
                        // Validate service type selection
                        var serviceType = document.querySelector('input[name="service_type"]:checked');
                        if (!serviceType) {
                            showCenterAlert('{{ __('messages.select_service_type_msg') }}');
                            return false;
                        }
                        return true;
                    case 5:
                        // Validate user info and phone number
                        var nameEl = document.querySelector('input[name="user_name"]');
                        var cityEl = document.querySelector('input[name="city"]');
                        if (!nameEl || !nameEl.value.trim()) { showCenterAlert('{{ __('messages.enter_name_msg') }}'); return false; }
                        if (!cityEl || !cityEl.value.trim()) { showCenterAlert('{{ __('messages.enter_city_msg') }}'); return false; }
                        if (!phoneInput || !phoneInput.value.trim()) { showCenterAlert('{{ __('messages.enter_phone_msg') }}'); return false; }
                        if (!validatePhoneNumber()) { showCenterAlert('{{ __('messages.valid_phone_msg') }} ' + selectedCountry.name); return false; }
                        return true;
                    default:
                        return true;
                }
            }

            actionBtn.addEventListener('click', function () {
                var stepCount = stepper.getCurrentStepIndex();

                if (stepCount === 1 && !onAfterCapture) {
                    // Step 1: capture image, change content, change button text
                    navigator.mediaDevices.getUserMedia({
                        video: { facingMode: "user" },
                        audio: false
                    }).then(function (stream) {
                        video.srcObject = stream;
                        video.onloadedmetadata = function () {
                            setTimeout(function () {
                                captureImage();
                                // Change content and button text
                                if (step1Initial && step1AfterCapture) {
                                    step1Initial.style.display = 'none';
                                    step1AfterCapture.style.display = 'block';
                                }
                                actionBtn.textContent = "{{ __('messages.next') }}";
                                onAfterCapture = true;
                                // Automatically go to next step after capture
                                stepper.goNext();
                            }, 500);
                        };
                    }).catch(function (err) {
                        showCenterAlert("{{ __('messages.camera_denied_msg') }} " + err, 'error');
                    });
                } else if (stepCount === 1 && onAfterCapture) {
                    // Step 1 after capture: go to step 2
                    stepper.goNext();
                } else if (stepCount >= 2 && stepCount <= 4) {
                    // Validate current step before proceeding
                    if (validateStep(stepCount)) {
                        stepper.goNext();
                    }
                } else if (stepCount === 5) {
                    // Step 5: Validate and submit form
                    if (window.formSubmitted) {
                        return; // Prevent double submission
                    }
                    
                    if (validateStep(stepCount)) {
                        // Check duplicate phone for current date
                        fetch('{{ route('token-registration.check-phone') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ phone_number: fullPhoneNumber.value })
                        }).then(function(res){ return res.json(); }).then(function(data){
                            if (data.exists) {
                                showCenterAlert('{{ __('messages.already_booked_msg') }}', 'info');
                            } else {
                                // Show loading state
                                actionBtn.disabled = true;
                                actionBtn.innerHTML = '<span class="spinner-border spinner-border-sm align-middle ms-2"></span> {{ __('messages.saving') }}';
                                
                                // Submit the form
                                var formData = new FormData(document.getElementById('kt_stepper_example_basic_form'));
                                
                                fetch('{{ route('token-registration.generate-token') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: formData
                                }).then(function(response) {
                                    // Always parse JSON, even for errors
                                    return response.json().then(function(data) {
                                        return { ok: response.ok, status: response.status, data: data };
                                    });
                                }).then(function(result) {
                                    console.log('Server response:', result);
                                    
                                    // Reset button state
                                    actionBtn.disabled = false;
                                    actionBtn.textContent = "{{ __('messages.save') }}";
                                    
                                    if (result.ok && result.data.success) {
                                        // Mark as submitted
                                        window.formSubmitted = true;
                                        // Go to thank you page
                                        stepper.goNext();
                                    } else {
                                        // Handle validation errors (422) or other errors
                                        var errorMessage = 'Error submitting form';
                                        
                                        if (result.status === 422 && result.data.errors) {
                                            // Laravel validation errors
                                            var errors = Object.values(result.data.errors).flat();
                                            errorMessage = errors.join('\n');
                                        } else if (result.data.message) {
                                            errorMessage = result.data.message;
                                        }
                                        
                                        showCenterAlert(errorMessage, 'error');
                                        window.formSubmitted = false; // Allow retry on error
                                    }
                                }).catch(function(error) {
                                    console.error('Submission error:', error);
                                    actionBtn.disabled = false;
                                    actionBtn.textContent = "{{ __('messages.save') }}";
                                    window.formSubmitted = false; // Allow retry on error
                                    showCenterAlert('Error submitting form. Please try again. Details: ' + error.message, 'error');
                                });
                            }
                        }).catch(function(){
                            showCenterAlert('{{ __('messages.verify_phone_error_msg') }}', 'error');
                        });
                    }
                } else if (stepCount === 6) {
                    // Step 6: Redirect or close
                    window.location.href = '{{ route('token-registration') }}';
                }
            });

            function animateContent(stepIndex) {
                // stepIndex is 1-based
                panels.forEach(function (panel, idx) {
                    panel.classList.remove('animate-in');
                    panel.classList.remove('animate-out');
                    if ((idx + 1) === stepIndex) {
                        panel.classList.add('animate-in');
                    }
                });
            }

            function updateProgress(stepIndex) {
                var percent = Math.min(100, Math.max(0, ((stepIndex - 1) / 5) * 100));
                if (progressBar) progressBar.style.width = percent + '%';
            }

            stepper.on("kt.stepper.next", function (stepperObj) {
                var currentStep = stepperObj.getCurrentStepIndex();
                updateButtonText(currentStep);
                updateProgress(currentStep);
                animateContent(currentStep);
            });

            stepper.on("kt.stepper.previous", function (stepperObj) {
                var currentStep = stepperObj.getCurrentStepIndex();
                updateButtonText(currentStep);
                updateProgress(currentStep);
                animateContent(currentStep);
            });

            stepper.on("kt.stepper.changed", function (stepperObj) {
                var currentStep = stepperObj.getCurrentStepIndex();
                updateButtonText(currentStep);
                updateProgress(currentStep);
                animateContent(currentStep);
            });

            function updateButtonText(stepIndex) {
                // Show back button on all steps
                if (prevBtn) {
                    prevBtn.style.display = 'inline-block';
                }
                
                switch (stepIndex) {
                    case 1:
                        actionBtn.textContent = onAfterCapture ? "{{ __('messages.next') }}" : "{{ __('messages.startbooking') }}";
                        break;
                    case 2:
                    case 3:
                    case 4:
                        actionBtn.textContent = "{{ __('messages.next') }}";
                        break;
                    case 5:
                        actionBtn.textContent = "{{ __('messages.save') }}";
                        // Disable button if already submitted
                        if (window.formSubmitted) {
                            actionBtn.disabled = true;
                            actionBtn.style.opacity = '0.5';
                            actionBtn.style.cursor = 'not-allowed';
                        }
                        break;
                    case 6:
                        actionBtn.textContent = "{{ __('messages.finish') }}";
                        break;
                }
            }

            // Initial state
            animateContent(1);
            updateProgress(1);
        }
    });
</script>

</html>