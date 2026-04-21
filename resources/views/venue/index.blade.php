@extends('layouts.app')

@section('title', 'Venues')

@section('content')
    <style>
        .btn i {
            padding-right: 0rem !important;
        }
    </style>
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">Manage Venues</h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <a href="{{ route('venue.create') }}" class="btn btn-sm btn-primary">
                                Create Venue
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="dataTables_length" id="venue-table_length">
                            <label>
                                <select name="venue-table_length" aria-controls="venue-table"
                                    class="form-select form-select-sm form-select-solid" id="customLengthSelect">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="-1">All</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-4 d-flex justify-content-end align-items-center">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="customSearchInput" class="form-control form-control-sm"
                                placeholder="Search venues...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-left gs-3 no-footer dataTable" id="venue-table">
                        <thead>
                            <tr>
                                <th>Sr no</th>
                                <th>Venue Code</th>
                                <th>Venue Name</th>
                                <th>Location</th>
                                <th>Field Admin</th>
                                <th>General DUA</th>
                                <th>General DUM</th>
                                <th>WL DUA</th>
                                <th>WL DUM</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will come from AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="venueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " style=" max-width: 1200px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Venue Details</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-2 ">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center bg-light-success rounded p-5 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
                                <span class="svg-icon svg-icon-success svg-icon-1 me-5">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3"
                                            d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z"
                                            fill="currentColor" />
                                        <path
                                            d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <!--begin::Title-->
                                <div class="flex-grow-1 me-2">
                                    <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-6">General Dua Tokens</a>
                                </div>
                                <!--end::Title-->
                                <!--begin::Lable-->
                                <span class="fw-bold text-success py-1">0/0</span>
                                <!--end::Lable-->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center bg-light-primary rounded p-5 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
                                <span class="svg-icon svg-icon-primary svg-icon-1 me-5">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3"
                                            d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z"
                                            fill="currentColor" />
                                        <path
                                            d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <!--begin::Title-->
                                <div class="flex-grow-1 me-2">
                                    <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-6">Working Lady Dua
                                        Tokens</a>
                                </div>
                                <!--end::Title-->
                                <!--begin::Lable-->
                                <span class="fw-bold text-primary py-1">0/0</span>
                                <!--end::Lable-->
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center bg-light-info rounded p-5 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
                                <span class="svg-icon svg-icon-info svg-icon-1 me-5">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3"
                                            d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z"
                                            fill="currentColor" />
                                        <path
                                            d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <!--begin::Title-->
                                <div class="flex-grow-1 me-2">
                                    <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-6">General Dum Tokens</a>
                                </div>
                                <!--end::Title-->
                                <!--begin::Lable-->
                                <span class="fw-bold text-info py-1">0/0</span>
                                <!--end::Lable-->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-8">
                                <!--begin::Bullet-->
                                <span class="bullet bullet-vertical h-40px bg-primary mx-5"></span>

                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fw-bold fs-6">Venue Code</a>
                                    <span class="text-muted fw-semibold d-block venue-code">-</span>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-8">
                                <!--begin::Bullet-->
                                <span class="bullet bullet-vertical h-40px bg-primary mx-5"></span>

                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fw-bold fs-6">Venue Name</a>
                                    <span class="text-muted fw-semibold d-block venue-name">-</span>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-8">
                                <!--begin::Bullet-->
                                <span class="bullet bullet-vertical h-40px bg-primary mx-5"></span>

                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fw-bold fs-6">Location Group</a>
                                    <span class="text-muted fw-semibold d-block">Due in 2 Days</span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-8">
                                <!--begin::Bullet-->
                                <span class="bullet bullet-vertical h-40px bg-primary  mx-5"></span>

                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fw-bold fs-6">Site Admin
                                    </a>
                                    <span class="text-muted fw-semibold d-block">Due in 2 Days</span>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-8">
                                <!--begin::Bullet-->
                                <span class="bullet bullet-vertical h-40px bg-primary  mx-5"></span>

                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fw-bold fs-6">Start Date
                                    </a>
                                    <span class="text-muted fw-semibold d-block">Due in 2 Days</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-8">
                                <!--begin::Bullet-->
                                <span class="bullet bullet-vertical h-40px bg-primary mx-5"></span>

                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fw-bold fs-6">End Date</a>
                                    <span class="text-muted fw-semibold d-block">Due in 2 Days</span>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-8">
                                <!--begin::Bullet-->
                                <span class="bullet bullet-vertical h-40px bg-primary  mx-5"></span>

                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fw-bold fs-6">Status</a>
                                    <span class="text-muted fw-semibold d-block">Due in 2 Days</span>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-8">
                                <!--begin::Bullet-->
                                <span class="bullet bullet-vertical h-40px bg-primary  mx-5"></span>

                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fw-bold fs-6">Venue Address</a>
                                    <span class="text-muted fw-semibold d-block">Due in 2 Days</span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-8">
                                <!--begin::Bullet-->
                                <span class="bullet bullet-vertical h-40px bg-primary mx-5"></span>

                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fw-bold fs-6">Venue Address</a>
                                    <span class="text-muted fw-semibold d-block">Due in 2 Days</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Constants
        const VENUE_URL = {
            DATA: "{{ route('venue.get-venues') }}",
            DETAILS: "{{ route('venue.get-venue-details', ['id' => ':id']) }}"
        };

        $(document).ready(function () {
            getVenueData();
            
            // Auto-reload table data every 30 seconds without refreshing the page
            setInterval(function() {
                const table = $('#venue-table').DataTable();
                if (table) {
                    table.ajax.reload(null, false); // false keeps current page position
                }
            }, 30000); // 30000 milliseconds = 30 seconds
        });

        function getVenueData() {
            const columns = [{
                data: null,
                orderable: true,
                searchable: true,
                render: function (data, type, row, meta) {
                    return meta.row + 1 + meta.settings._iDisplayStart;
                }
            },
            {
                data: 'venue_code',
                orderable: true,
                searchable: true
            },
            {
                data: 'venue_name',
                orderable: true,
                searchable: true
            },
            {
                data: 'location_name',
                orderable: true,
                searchable: true
            },
            {
                data: 'user_name',
                orderable: true,
                searchable: true
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const issued = row.general_dua_issued || 0;
                    const total = row.general_dua_token || 0;
                    return `<span class="badge badge-light-success">${issued}/${total}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const issued = row.general_dum_issued || 0;
                    const total = row.general_dum_token || 0;
                    return `<span class="badge badge-light-info">${issued}/${total}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const issued = row.wl_dua_issued || 0;
                    const total = row.working_lady_dua_token || 0;
                    return `<span class="badge badge-light-primary">${issued}/${total}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const issued = row.wl_dum_issued || 0;
                    const total = 0; // No field for WL DUM total in venues table
                    return `<span class="badge badge-light-warning">${issued}/${total}</span>`;
                }
            },
            {
                data: 'formatted_start_date',
                orderable: true,
                searchable: true
            },
            {
                data: 'formatted_end_date',
                orderable: true,
                searchable: true
            },

            // {
            //     data: 'general_dua_token',
            //     orderable: true,
            //     searchable: true
            // },
            // {
            //     data: 'general_dum_token',
            //     orderable: true,
            //     searchable: true
            // },
            // {
            //     data: 'working_lady_dua_token',
            //     orderable: true,
            //     searchable: true
            // },
            {
                data: 'status',
                orderable: true,
                searchable: true,
                render: statusBadge
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return row.id ? ActionPrivilege(row) : '';
                }
            }
            ];

            initializeDataTable(
                '#venue-table',
                VENUE_URL.DATA,
                columns,
                "#customSearchInput",
                "#customLengthSelect"
            );
        }

        function ActionPrivilege(row) {
            const statusClass = row.status === 'Active' ? 'btn-danger' : 'btn-success';
            const newStatus = row.status === 'Active' ? 'In Active' : 'Active';

            var html = `
                                        <div class="d-flex gap-2">
                                            <a href="/venue/${row.id}/edit" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> 
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm ${statusClass} status-toggle" 
                                                    data-id="${row.id}" 
                                                    data-status="${newStatus}">
                                                <i class="fas fa-power-off"></i> 
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary view-venue" 
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#venueModal"
                                                    onclick="getVenueDetails('${row.id}')"
                                                    data-id="${row.id}">
                                                <i class="fas fa-eye"></i> 
                                            </button>
                                        </div>
                                        `;
            return html;
        }

        // Add status toggle functionality
        $(document).on('click', '.status-toggle', function () {
            debugger;
            const button = $(this);
            const venueId = button.data('id');
            const newStatus = button.data('status');

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to change the status to ${newStatus}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'No, cancel!',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    showLoader();

                    // Make AJAX call to update status
                    $.ajax({
                        url: `/venue/${venueId}/status`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'PUT',
                            status: newStatus
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                // Reload the table to reflect changes
                                $('#venue-table').DataTable().ajax.reload();
                                // Show success message
                                successToaster(response.message);
                            }
                        },
                        error: function (xhr) {
                            console.log('Full error response:', xhr);
                            console.log('Response JSON:', xhr.responseJSON);
                            console.log('Status:', xhr.status);
                            console.log('Status Text:', xhr.statusText);
                            
                            let errorMessage = 'Failed to update status.';
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                if (xhr.responseJSON.errors) {
                                    console.log('Validation errors:', xhr.responseJSON.errors);
                                    errorMessage += ' Validation errors: ' + JSON.stringify(xhr.responseJSON.errors);
                                }
                            }
                            errorToaster(errorMessage);
                        },
                        complete: function () {
                            hideLoader();
                        }
                    });
                }
            });
        });

        function getVenueDetails(id) {
            debugger;
            showLoader();
            $.ajax({
                url: VENUE_URL.DETAILS.replace(':id', id),
                method: 'GET',
                success: function (response) {
                    if (response.status === 'success') {
                        // Update modal content with venue details
                        const venue = response.data;

                        // Update token counts
                        $('.bg-light-success .fw-bold.text-success').text(`${venue.used_general_dua_tokens || 0}/${venue.general_dua_token || 0}`);
                        $('.bg-light-primary .fw-bold.text-primary').text(`${venue.used_working_lady_dua_tokens || 0}/${venue.working_lady_dua_token || 0}`);
                        $('.bg-light-info .fw-bold.text-info').text(`${venue.used_general_dum_tokens || 0}/${venue.general_dum_token || 0}`);

                        // Update other venue details
                        $('.text-gray-800:contains("Location Group")').closest('.flex-grow-1').find('.text-muted').text(venue.location_name || 'N/A');
                        $('.text-gray-800:contains("Site Admin")').closest('.flex-grow-1').find('.text-muted').text(venue.user_name || 'N/A');
                        $('.text-gray-800:contains("Start Date")').closest('.flex-grow-1').find('.text-muted').text(venue.formatted_start_date || 'N/A');
                        $('.text-gray-800:contains("End Date")').closest('.flex-grow-1').find('.text-muted').text(venue.formatted_end_date || 'N/A');
                        $('.text-gray-800:contains("Status")').closest('.flex-grow-1').find('.text-muted').text(venue.status || 'N/A');
                        $('.text-gray-800:contains("Venue Address")').closest('.flex-grow-1').find('.text-muted').text(venue.address || 'N/A');

                        // Update venue code and name in the modal
                        $('.venue-code').text(venue.venue_code || 'N/A');
                        $('.venue-name').text(venue.venue_name || 'N/A');
                    } else {
                        errorToaster('Failed to load venue details');
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Failed to load venue details';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    errorToaster(errorMessage);
                },
                complete: function () {
                    hideLoader();
                }
            });
        }
    </script>
@endsection