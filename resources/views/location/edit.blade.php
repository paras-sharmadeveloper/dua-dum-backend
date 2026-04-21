@extends('layouts.app')

@section('title', 'Edit Location Group')

@section('content')
    <style>
        /* Add any custom styles here */
    </style>
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">Edit Location</h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <a href="{{ route('location.index') }}" class="btn btn-sm btn-primary">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row">
                    <div class="col-md-4">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required">Group Name</span>
                        </label>
                        <input type="text" class="form-control form-control-sm" id="groupName"
                            value="{{ $locationGroup->name }}" placeholder="Enter Group Name">
                    </div>
                    <div class="col-md-4">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required">Status</span>
                        </label>
                        <select class="form-select form-select-sm" id="status">
                            <option value="Active" {{ $locationGroup->status === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="In Active" {{ $locationGroup->status === 'In Active' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required">Select Country</span>
                        </label>
                        <div class="border rounded mb-5">
                            <select class="form-select form-select-transparent form-select-sm" id="countries">
                                <option></option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->iso }}"
                                        data-kt-select2-country="{{ asset('assets/media/flags/' . strtolower($country->name) . '.svg') }}"
                                        {{ $country->id == $locationGroup->country_id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required">Select Cities</span>
                        </label>
                        <input class="form-control-sm form-control" placeholder="Select Multiple Cities" id="cities" />
                    </div>
                    <div class="col-md-2">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required invisible">Select Cities</span>
                        </label>
                        <button type="button" class="btn btn-sm btn-primary" id="updateLocation">
                            <span class="indicator-label">Update</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Format options for Select2
            var optionFormat = function (item) {
                if (!item.id) {
                    return item.text;
                }

                var span = document.createElement('span');
                var imgUrl = item.element.getAttribute('data-kt-select2-country');
                var template = '';

                template += '<img src="' + imgUrl + '" class="rounded-circle h-20px me-2" alt="image"/>';
                template += item.text;

                span.innerHTML = template;
                return $(span);
            }

            // Initialize Select2
            $('#countries').select2({
                templateSelection: optionFormat,
                templateResult: optionFormat,
                placeholder: "Select a country",
                containerCssClass: "form-select-sm",
                dropdownCssClass: "select2-dropdown-sm"
            });

            // Initialize Tagify with pre-selected cities
            var input = document.querySelector("#cities");
            var cities_tags = new Tagify(input, {
                whitelist: @json($formattedCities),
                maxTags: 100,
                dropdown: {
                    maxItems: 100,
                    classname: "",
                    enabled: 0,
                    closeOnSelect: false
                },
                transformTag: function (tagData) {
                    tagData.id = tagData.id;
                }
            });

            // Set pre-selected cities
            var preSelectedCities = @json($selectedCityIds).map(id => {
                var city = @json($formattedCities).find(c => c.id == id);
                return city ? { id: city.id, value: city.value } : null;
            }).filter(Boolean);

            cities_tags.addTags(preSelectedCities);

            // Handle country change
            $('#countries').on('change', function () {
                var countryCode = $(this).val();
                if (!countryCode) {
                    cities_tags.removeAllTags();
                    return;
                }

                $.ajax({
                    url: '{{ route("location.get-cities") }}',
                    type: 'POST',
                    data: {
                        country_code: countryCode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            cities_tags.whitelist = response.cities;
                            cities_tags.removeAllTags();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching cities:', error);
                        toastr.error('Error fetching cities');
                    }
                });
            });

            // Handle update button click
            $('#updateLocation').on('click', function () {
                var groupName = $('#groupName').val().trim();
                var status = $('#status').val();
                var countryCode = $('#countries').val();
                var selectedCities = cities_tags.value.map(tag => tag.id);

                if (!groupName) {
                    toastr.warning('Please enter a group name');
                    return;
                }

                if (!countryCode) {
                    toastr.warning('Please select a country first');
                    return;
                }

                if (selectedCities.length === 0) {
                    toastr.warning('Please select at least one city');
                    return;
                }

                // Show confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to update this location group?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update it!',
                    cancelButtonText: 'No, cancel!',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-sm btn-primary',
                        cancelButton: 'btn btn-sm btn-light'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        showLoader();
                        $.ajax({
                            url: '{{ route("location.update", $locationGroup->id) }}',
                            type: 'PUT',
                            data: {
                                name: groupName,
                                status: status,
                                country_code: countryCode,
                                cities: selectedCities,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.success) {
                                    toastr.success('Location Group Updated Successfully');

                                } else {
                                    toastr.error('Error on Updating Location Group');
                                }
                            },
                            error: function (xhr, status, error) {
                                toastr.error('Error updating location group. Please try again.');

                            },
                            complete: function () {
                                hideLoader();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection