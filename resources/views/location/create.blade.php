@extends('layouts.app')

@section('title', 'Location Group')

@section('content')
    <style>

    </style>
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header " style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title"> Create Location </h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <!--begin::Label-->
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
                    <div class="col-md-3">
                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required">Enter Grouping Name</span>

                            </label>
                            <input type="text" id="groupingName" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                            <span class="required">Select Country</span>
                        </label>
                        <div class=" border rounded mb-5">
                            <select class="form-select form-select-transparent form-select-sm" id="countries">
                                <option></option>
                                <option value="AF"
                                    data-kt-select2-country="{{ asset('assets/media/flags/afghanistan.svg') }}">Afghanistan
                                </option>
                                <option value="AX"
                                    data-kt-select2-country="{{ asset('assets/media/flags/aland-islands.svg') }}">Aland
                                    Islands</option>
                                <option value="AL" data-kt-select2-country="{{ asset('assets/media/flags/albania.svg') }}">
                                    Albania</option>
                                <option value="DZ" data-kt-select2-country="{{ asset('assets/media/flags/algeria.svg') }}">
                                    Algeria</option>
                                <option value="AS"
                                    data-kt-select2-country="{{ asset('assets/media/flags/american-samoa.svg') }}">American
                                    Samoa</option>
                                <option value="AD" data-kt-select2-country="{{ asset('assets/media/flags/andorra.svg') }}">
                                    Andorra</option>
                                <option value="AO" data-kt-select2-country="{{ asset('assets/media/flags/angola.svg') }}">
                                    Angola</option>
                                <option value="AI" data-kt-select2-country="{{ asset('assets/media/flags/anguilla.svg') }}">
                                    Anguilla</option>
                                <option value="AG"
                                    data-kt-select2-country="{{ asset('assets/media/flags/antigua-and-barbuda.svg') }}">
                                    Antigua and Barbuda</option>
                                <option value="AR"
                                    data-kt-select2-country="{{ asset('assets/media/flags/argentina.svg') }}">Argentina
                                </option>
                                <option value="AM" data-kt-select2-country="{{ asset('assets/media/flags/armenia.svg') }}">
                                    Armenia</option>
                                <option value="AW" data-kt-select2-country="{{ asset('assets/media/flags/aruba.svg') }}">
                                    Aruba</option>
                                <option value="AU"
                                    data-kt-select2-country="{{ asset('assets/media/flags/australia.svg') }}">Australia
                                </option>
                                <option value="AT" data-kt-select2-country="{{ asset('assets/media/flags/austria.svg') }}">
                                    Austria</option>
                                <option value="AZ"
                                    data-kt-select2-country="{{ asset('assets/media/flags/azerbaijan.svg') }}">Azerbaijan
                                </option>
                                <option value="BS" data-kt-select2-country="{{ asset('assets/media/flags/bahamas.svg') }}">
                                    Bahamas</option>
                                <option value="BH" data-kt-select2-country="{{ asset('assets/media/flags/bahrain.svg') }}">
                                    Bahrain</option>
                                <option value="BD"
                                    data-kt-select2-country="{{ asset('assets/media/flags/bangladesh.svg') }}">Bangladesh
                                </option>
                                <option value="BB" data-kt-select2-country="{{ asset('assets/media/flags/barbados.svg') }}">
                                    Barbados</option>
                                <option value="BY" data-kt-select2-country="{{ asset('assets/media/flags/belarus.svg') }}">
                                    Belarus</option>
                                <option value="BE" data-kt-select2-country="{{ asset('assets/media/flags/belgium.svg') }}">
                                    Belgium</option>
                                <option value="BZ" data-kt-select2-country="{{ asset('assets/media/flags/belize.svg') }}">
                                    Belize</option>
                                <option value="BJ" data-kt-select2-country="{{ asset('assets/media/flags/benin.svg') }}">
                                    Benin</option>
                                <option value="BM" data-kt-select2-country="{{ asset('assets/media/flags/bermuda.svg') }}">
                                    Bermuda</option>
                                <option value="BT" data-kt-select2-country="{{ asset('assets/media/flags/bhutan.svg') }}">
                                    Bhutan</option>
                                <option value="BO" data-kt-select2-country="{{ asset('assets/media/flags/bolivia.svg') }}">
                                    Bolivia</option>
                                <option value="BA"
                                    data-kt-select2-country="{{ asset('assets/media/flags/bosnia-and-herzegovina.svg') }}">
                                    Bosnia and Herzegovina</option>
                                <option value="BW" data-kt-select2-country="{{ asset('assets/media/flags/botswana.svg') }}">
                                    Botswana</option>
                                <option value="BR" data-kt-select2-country="{{ asset('assets/media/flags/brazil.svg') }}">
                                    Brazil</option>
                                <option value="GB"
                                    data-kt-select2-country="{{ asset('assets/media/flags/united-kingdom.svg') }}">United
                                    Kingdom</option>
                                <option value="US"
                                    data-kt-select2-country="{{ asset('assets/media/flags/united-states.svg') }}">United
                                    States</option>
                                <option value="CA" data-kt-select2-country="{{ asset('assets/media/flags/canada.svg') }}">
                                    Canada</option>
                                <option value="CN" data-kt-select2-country="{{ asset('assets/media/flags/china.svg') }}">
                                    China</option>
                                <option value="JP" data-kt-select2-country="{{ asset('assets/media/flags/japan.svg') }}">
                                    Japan</option>
                                <option value="FR" data-kt-select2-country="{{ asset('assets/media/flags/france.svg') }}">
                                    France</option>
                                <option value="DE" data-kt-select2-country="{{ asset('assets/media/flags/germany.svg') }}">
                                    Germany</option>
                                <option value="IT" data-kt-select2-country="{{ asset('assets/media/flags/italy.svg') }}">
                                    Italy</option>
                                <option value="RU" data-kt-select2-country="{{ asset('assets/media/flags/russia.svg') }}">
                                    Russia</option>
                                <option value="ES" data-kt-select2-country="{{ asset('assets/media/flags/spain.svg') }}">
                                    Spain</option>
                                <option value="IN" data-kt-select2-country="{{ asset('assets/media/flags/india.svg') }}">
                                    India</option>
                                <option value="AE"
                                    data-kt-select2-country="{{ asset('assets/media/flags/united-arab-emirates.svg') }}">
                                    United Arab Emirates</option>
                                <option value="SA"
                                    data-kt-select2-country="{{ asset('assets/media/flags/saudi-arabia.svg') }}">Saudi
                                    Arabia</option>
                                <option value="QA" data-kt-select2-country="{{ asset('assets/media/flags/qatar.svg') }}">
                                    Qatar</option>
                                <option value="KW" data-kt-select2-country="{{ asset('assets/media/flags/kuwait.svg') }}">
                                    Kuwait</option>
                                <option value="OM" data-kt-select2-country="{{ asset('assets/media/flags/oman.svg') }}">Oman
                                </option>
                                <option value="BH" data-kt-select2-country="{{ asset('assets/media/flags/bahrain.svg') }}">
                                    Bahrain</option>
                                <option value="PK" data-kt-select2-country="{{ asset('assets/media/flags/pakistan.svg') }}">
                                    Pakistan</option>
                                <option value="ZA"
                                    data-kt-select2-country="{{ asset('assets/media/flags/south-africa.svg') }}">South
                                    Africa</option>
                                <option value="EG" data-kt-select2-country="{{ asset('assets/media/flags/egypt.svg') }}">
                                    Egypt</option>
                                <option value="NG" data-kt-select2-country="{{ asset('assets/media/flags/nigeria.svg') }}">
                                    Nigeria</option>
                                <option value="KE" data-kt-select2-country="{{ asset('assets/media/flags/kenya.svg') }}">
                                    Kenya</option>
                                <option value="TR" data-kt-select2-country="{{ asset('assets/media/flags/turkey.svg') }}">
                                    Turkey</option>
                                <option value="IL" data-kt-select2-country="{{ asset('assets/media/flags/israel.svg') }}">
                                    Israel</option>
                                <option value="MY" data-kt-select2-country="{{ asset('assets/media/flags/malaysia.svg') }}">
                                    Malaysia</option>
                                <option value="SG"
                                    data-kt-select2-country="{{ asset('assets/media/flags/singapore.svg') }}">Singapore
                                </option>
                                <option value="ID"
                                    data-kt-select2-country="{{ asset('assets/media/flags/indonesia.svg') }}">Indonesia
                                </option>
                                <option value="TH" data-kt-select2-country="{{ asset('assets/media/flags/thailand.svg') }}">
                                    Thailand</option>
                                <option value="VN" data-kt-select2-country="{{ asset('assets/media/flags/vietnam.svg') }}">
                                    Vietnam</option>
                                <option value="PH"
                                    data-kt-select2-country="{{ asset('assets/media/flags/philippines.svg') }}">Philippines
                                </option>
                                <option value="MX" data-kt-select2-country="{{ asset('assets/media/flags/mexico.svg') }}">
                                    Mexico</option>
                                <option value="BR" data-kt-select2-country="{{ asset('assets/media/flags/brazil.svg') }}">
                                    Brazil</option>
                                <option value="AR"
                                    data-kt-select2-country="{{ asset('assets/media/flags/argentina.svg') }}">Argentina
                                </option>
                                <option value="CO" data-kt-select2-country="{{ asset('assets/media/flags/colombia.svg') }}">
                                    Colombia</option>
                                <option value="CL" data-kt-select2-country="{{ asset('assets/media/flags/chile.svg') }}">
                                    Chile</option>
                                <option value="PE" data-kt-select2-country="{{ asset('assets/media/flags/peru.svg') }}">Peru
                                </option>
                                <option value="VE"
                                    data-kt-select2-country="{{ asset('assets/media/flags/venezuela.svg') }}">Venezuela
                                </option>
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
                        <button type="button" class="btn btn-sm btn-primary" id="saveLocation">
                            <span class="indicator-label">Save</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
@endsection

    @section('scripts')
        <script>
            $(document).ready(function () {
                // Format options
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

                // Init Select2 --- more info: https://select2.org/
                $('#countries').select2({
                    templateSelection: optionFormat,
                    templateResult: optionFormat,
                    placeholder: "Select a country",
                    containerCssClass: "form-select-sm",
                    dropdownCssClass: "select2-dropdown-sm"
                });

                var input = document.querySelector("#cities");
                var cities_tags = new Tagify(input, {
                    whitelist: [],
                    maxTags: 100,
                    dropdown: {
                        maxItems: 100,
                        classname: "",
                        enabled: 0,
                        closeOnSelect: false
                    },
                    transformTag: function (tagData) {
                        // Keep the original city data including ID
                        tagData.id = tagData.id;
                    },
                    callbacks: {
                        // Prevent adding tags if no country is selected
                        beforeAddTag: function (e) {
                            var selectedCountry = $('#countries').val();
                            if (!selectedCountry) {
                                e.preventDefault();
                                toastr.warning('Please select a country first');
                                return false;
                            }
                            return true;
                        },
                        // Prevent manual input if no country is selected
                        invalid: function (e) {
                            var selectedCountry = $('#countries').val();
                            if (!selectedCountry) {
                                toastr.warning('Please select a country first');
                                return true; // This prevents the input
                            }
                            return false;
                        }
                    }
                });

                // Handle country selection change
                $('#countries').on('change', function () {
                    var countryCode = $(this).val();
                    if (countryCode) {
                        // Clear existing tags
                        cities_tags.removeAllTags();

                        $.ajax({
                            url: '{{ route("location.get-cities") }}',
                            type: 'POST',
                            data: {
                                country_code: countryCode,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.success) {
                                    // Update the cities input with the new cities
                                    console.log('Cities data:', response.cities);
                                    cities_tags.whitelist = response.cities;
                                    toastr.success(response.message || 'All Cities Loaded Successfully');
                                } else {
                                    console.error('Error:', response.message);
                                    toastr.error(response.message || 'Error fetching cities');
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('Error:', error);
                                toastr.error('Error fetching cities. Please try again.');
                            }
                        });
                    } else {
                        // Clear cities if no country is selected
                        cities_tags.removeAllTags();
                        cities_tags.whitelist = [];
                    }
                });

                // Disable city input when no country is selected
                $('#cities').on('focus', function () {
                    if (!$('#countries').val()) {
                        toastr.warning('Please select a country first');
                        $(this).blur(); // Remove focus from the input
                    }
                });

                // Handle save button click
                $('#saveLocation').on('click', function () {
                    var countryCode = $('#countries').val();
                    // Get the city IDs from the selected tags
                    var selectedCities = cities_tags.value.map(tag => tag.id);
                    var groupingName = $('#groupingName').val();

                    if (!countryCode) {
                        toastr.warning('Please select a country first');
                        return;
                    }

                    if (selectedCities.length === 0) {
                        toastr.warning('Please select at least one city');
                        return;
                    }

                    if (!groupingName) {
                        toastr.warning('Please enter a grouping name');
                        return;
                    }

                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to save this location with selected cities?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, save it!',
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
                                url: '{{ route("location.store") }}',
                                type: 'POST',
                                data: {
                                    country_code: countryCode,
                                    cities: selectedCities, // Sending array of city IDs
                                    grouping_name: groupingName,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (response) {
                                    if (response.success) {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: response.message || 'Locations Created Successfully',
                                            icon: 'success',
                                            buttonsStyling: false,
                                            customClass: {
                                                confirmButton: 'btn btn-sm btn-primary'
                                            }
                                        }).then(() => {
                                            window.location.href = '{{ route("location.index") }}';
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: response.message || 'Error saving location',
                                            icon: 'error',
                                            buttonsStyling: false,
                                            customClass: {
                                                confirmButton: 'btn btn-sm btn-primary'
                                            }
                                        });
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Error saving location. Please try again.',
                                        icon: 'error',
                                        buttonsStyling: false,
                                        customClass: {
                                            confirmButton: 'btn btn-sm btn-primary'
                                        }
                                    });
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