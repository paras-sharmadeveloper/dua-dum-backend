@extends('layouts.app')

@section('title', 'Create Venue')

@section('content')
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">Create Venue</h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <a href="{{ route('venue.index') }}" class="btn btn-sm btn-primary">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <form id="venueForm">
                    @csrf

                    <!-- Venue Name -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Venue Name</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-geo-alt-fill fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input type="text" name="venue_name"
                                        class="form-control form-control-sm rounded-start-0"
                                        placeholder="Enter venue name (e.g., Liaquat Ground)" required>
                                </div>
                            </div>
                            <div class="form-text">Venue code will be auto-generated (e.g., V1, V2, V3...)</div>
                        </div>
                    </div>

                    <!-- Field Admin and Date Selection -->
                    <div class="row mb-5">
                        <div class="col-md-3">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Select Field Admin</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-bookmarks-fill fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <select name="user_id" class="form-select form-select-sm rounded-start-0"
                                        data-control="select2" data-placeholder="Select Field Admin" required>
                                        <option></option>
                                        @foreach ($fieldAdmins as $fieldAdmin)
                                            <option value="{{ $fieldAdmin->id }}">{{ $fieldAdmin->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Start Date and Time For Booking</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-calendar2-check"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input name="start_date" class="form-control form-control-sm"
                                        placeholder="Pick date & time" id="startDate" required />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">End Date and Time For Booking</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-calendar2-check"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input name="end_date" class="form-control form-control-sm"
                                        placeholder="Pick date & time" id="endDate" required />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Select Location</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-geo-alt-fill"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <select name="location_group_id" class="form-select form-select-sm rounded-start-0"
                                        data-control="select2" data-placeholder="Select Location Group" required>
                                        <option></option>
                                        @foreach ($venueTypes as $venueType)
                                            <option value="{{ $venueType->id }}">{{ $venueType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address and Notes -->
                    <div class="row mb-5">
                        <div class="col-md-3">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Venue Addresses (English)</span>
                            </label>
                            <div class="input-group">
                                <textarea name="venue_address_eng" class="form-control form-control-sm" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Venue Addresses (Urdu)</span>
                            </label>
                            <div class="input-group">
                                <textarea name="venue_address_urdu" class="form-control form-control-sm"
                                    required></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Status Page Note (English)</span>
                            </label>
                            <div class="input-group">
                                <textarea name="status_page_note_eng" class="form-control form-control-sm"
                                    required></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Status Page Note (Urdu)</span>
                            </label>
                            <div class="input-group">
                                <textarea name="status_page_note_urdu" class="form-control form-control-sm"
                                    required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tokens Section -->
                    <div class="row mb-5">
                        <div class="col-md-2"></div>
                        <!-- General Tokens Card -->
                        <div class="col-md-4">
                            <div class="card card-xl-stretch mb-xl-8">
                                <div class="card-body p-0">
                                    <div class="px-9 pt-7 card-rounded h-175px w-100 bg-success">
                                        <div class="d-flex flex-stack">
                                            <h3 class="m-0 text-white fw-bold fs-3">General Tokens</h3>
                                        </div>
                                    </div>
                                    <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
                                        style="margin-top: -100px">
                                        <!-- Dua Tokens -->
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="symbol symbol-45px w-40px me-5">
                                                <span class="symbol-label bg-lighten">
                                                    <i class="bi bi-bookmarks-fill fs-5"></i>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <div class="mb-1 pe-3 flex-grow-1">
                                                    <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">Dua
                                                        Tokens</a>
                                                    <div class="text-gray-400 fw-semibold fs-7">(From 1 - 800)</div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="input-group input-group-xs mb-3" style="width: 80px;">
                                                        <input type="text" name="general_dua_token"
                                                            class="form-control form-control-sm" placeholder="0"
                                                            id="generalDuaTokens" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dua Checkbox -->
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <div class="form-check form-check-custom form-check-sm">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="duaCheck" />
                                                    <label class="form-check-label" for="duaCheck">
                                                        Dua will not happen
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dua Reason -->
                                        <div class="d-flex align-items-center mb-6 d-none" id="duaCheckDiv">
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <div class="input-group input-group-sm flex-nowrap">
                                                    <div class="overflow-hidden flex-grow-1">
                                                        <select name="dua_reason" class="form-select form-select-sm rounded"
                                                            data-control="select2" data-placeholder="Select Reason">
                                                            <option></option>
                                                            <option value="1">Option 1</option>
                                                            <option value="2">Option 2</option>
                                                            <option value="3">Option 3</option>
                                                            <option value="4">Option 4</option>
                                                            <option value="5">Option 5</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dum Tokens -->
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="symbol symbol-45px w-40px me-5">
                                                <span class="symbol-label bg-lighten">
                                                    <i class="bi bi-bookmarks-fill fs-5"></i>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <div class="mb-1 pe-3 flex-grow-1">
                                                    <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">Dum
                                                        Tokens</a>
                                                    <div class="text-gray-400 fw-semibold fs-7">(From 1001 - 1800)</div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="input-group input-group-xs mb-3" style="width: 80px;">
                                                        <input type="text" name="general_dum_token"
                                                            class="form-control form-control-sm" placeholder="0"
                                                            id="generalDumTokens" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dum Checkbox -->
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <div class="form-check form-check-custom form-check-sm">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="dumCheck" />
                                                    <label class="form-check-label" for="dumCheck">
                                                        Dum will not happen
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dum Reason -->
                                        <div class="d-flex align-items-center mb-6 d-none" id="dumCheckDiv">
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <div class="input-group input-group-sm flex-nowrap">
                                                    <div class="overflow-hidden flex-grow-1">
                                                        <select name="dum_reason" class="form-select form-select-sm rounded"
                                                            data-control="select2" data-placeholder="Select Reason">
                                                            <option></option>
                                                            <option value="1">Option 1</option>
                                                            <option value="2">Option 2</option>
                                                            <option value="3">Option 3</option>
                                                            <option value="4">Option 4</option>
                                                            <option value="5">Option 5</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Working Lady Tokens Card -->
                        <div class="col-md-4">
                            <div class="card card-xl-stretch mb-xl-8">
                                <div class="card-body p-0">
                                    <div class="px-9 pt-7 card-rounded h-175px w-100 bg-primary">
                                        <div class="d-flex flex-stack">
                                            <h3 class="m-0 text-white fw-bold fs-3">Working Lady Tokens</h3>
                                        </div>
                                    </div>
                                    <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
                                        style="margin-top: -100px">
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="symbol symbol-45px w-40px me-5">
                                                <span class="symbol-label bg-lighten">
                                                    <i class="bi bi-bookmarks-fill fs-5"></i>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <div class="mb-1 pe-3 flex-grow-1">
                                                    <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">Dua
                                                        Tokens</a>
                                                    <div class="text-gray-400 fw-semibold fs-7">(From 801 - 1000)</div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="input-group input-group-xs mb-3" style="width: 80px;">
                                                        <input type="text" name="working_lady_dua_token"
                                                            class="form-control form-control-sm" placeholder="0"
                                                            id="workingLadyDuaTokens" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Create Venue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize date pickers
            $("#startDate").flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i",
            });
            $("#endDate").flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i",
            });

            // Initialize Select2
            $('[data-control="select2"]').select2();

            // Handle form submission
            $('#venueForm').on('submit', function (e) {
                e.preventDefault();

                // Show loading state
                showLoader();
                // Submit form via AJAX
                $.ajax({
                    url: '{{ route("venue.store") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                text: response.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function () {
                                window.location.href = '{{ route("venue.index") }}';
                            });
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'An error occurred while creating the venue.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errorHtml = '<ul>';
                            Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                                errorHtml += '<li>' + xhr.responseJSON.errors[key][0] + '</li>';
                            });
                            errorHtml += '</ul>';
                            errorMessage = errorHtml;
                        }

                        Swal.fire({
                            text: errorMessage,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    },
                    complete: function () {
                        hideLoader();
                    }
                });
            });

            // Handle checkbox changes
            $('#duaCheck').on('change', function () {
                if (this.checked) {
                    $('#duaCheckDiv').removeClass('d-none');
                    $('#generalDuaTokens').prop('disabled', true);
                    $('#workingLadyDuaTokens').prop('disabled', true);
                } else {
                    $('#duaCheckDiv').addClass('d-none');
                    $('#generalDuaTokens').prop('disabled', false);
                    $('#workingLadyDuaTokens').prop('disabled', false);
                    $('#duaCheckDiv select').val(null).trigger('change');
                }
            });

            $('#dumCheck').on('change', function () {
                if (this.checked) {
                    $('#dumCheckDiv').removeClass('d-none');
                    $('#generalDumTokens').prop('disabled', true);
                } else {
                    $('#dumCheckDiv').addClass('d-none');
                    $('#generalDumTokens').prop('disabled', false);
                    $('#dumCheckDiv select').val(null).trigger('change');
                }
            });
        });
    </script>
@endsection